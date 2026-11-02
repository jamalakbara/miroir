#!/usr/bin/env bash
# Move the site's content between machines: database (Elementor pages, settings,
# products, orders), uploads and licensed fonts. Code goes through git; this does the rest.
#
#   tools/snapshot.sh export [--force]   write this machine's content to the snapshot folder
#   tools/snapshot.sh import [--force]   replace this machine's content with the snapshot
#   tools/snapshot.sh status             show the snapshot and what this machine last synced
#
# The snapshot folder is MIROIR_SNAPSHOT_DIR in .env (default .snapshots/). Point it at a
# private folder both machines sync, e.g. Google Drive or iCloud. It holds customer data and
# password hashes: never put it in git or anywhere public.
#
# Work on one machine at a time: export before you leave it, import when you arrive.
# Databases cannot be merged; an import replaces everything (a local backup is kept in .snapshots-backup/).
set -euo pipefail
cd "$(dirname "$0")/.."
. tools/lib/common.sh
load_env

SNAP_DIR=$(snapshot_dir)
META=$SNAP_DIR/snapshot.env
STATE=$(git rev-parse --git-common-dir)/miroir-snapshot
BACKUP_DIR=.snapshots-backup
HOST=$(hostname)
# Gitignored files every machine needs (licensed fonts).
PRIVATE_GLOBS=(
	'wp-content/themes/miroir-theme/assets/fonts/Avenir-*.ttf'
	'docs/design/fonts/*'
)
UPLOAD_EXCLUDES=(--exclude=uploads/cache --exclude=uploads/wc-logs --exclude=uploads/elementor/css)

get() { [ -f "$2" ] && sed -n "s/^$1=//p" "$2" | tr -d '\r' || true; }
sha() { if command -v sha256sum >/dev/null; then sha256sum "$1"; else shasum -a 256 "$1"; fi | cut -d' ' -f1; }
upload_count() { find wp-content/uploads -type f ! -path '*/uploads/cache/*' ! -path '*/uploads/wc-logs/*' ! -path '*/uploads/elementor/css/*' | wc -l | tr -d ' '; }

start_stack() {
	compose up -d --wait db wordpress >/dev/null
	local i=0
	until compose exec -T wordpress test -f /var/www/html/wp-includes/version.php 2>/dev/null; do
		i=$((i + 1)); [ $i -lt 60 ] || die "WordPress core did not appear in the wp_core volume"
		sleep 1
	done
}

dump_db() {
	db_exec mariadb-dump -uroot --single-transaction --quick --skip-dump-date \
		--default-character-set=utf8mb4 "$DB_NAME" | gzip -c > "$1"
}

export_snapshot() {
	local force=${1:-}
	local shared last
	shared=$(get ID "$META")
	last=$(get ID "$STATE")
	if [ -n "$shared" ] && [ "$shared" != "$last" ] && [ "$force" != --force ]; then
		die "the snapshot from $(get HOST "$META") ($(get CREATED "$META")) was never imported here.
Exporting would overwrite that machine's content. Run $(tool_cmd snapshot) import first,
or $(tool_cmd snapshot) export --force to overwrite it anyway."
	fi

	mkdir -p "$SNAP_DIR"
	start_stack
	local id stage
	id=$(date -u +%Y%m%dT%H%M%SZ)-$HOST
	stage=$SNAP_DIR/.incoming-$id
	mkdir -p "$stage"
	trap "rm -rf '$stage'" EXIT

	note "plugins"
	bash tools/plugins.sh freeze
	cp plugins.txt "$stage/plugins.txt"
	if [ -n "$(git status --porcelain -- plugins.txt)" ]; then
		echo "    plugins.txt changed: commit and push it so the other machine's git hooks pick it up."
	fi

	note "database"
	dump_db "$stage/db.sql.gz"

	note "uploads"
	local count uploads_sha
	count=$(upload_count)
	uploads_sha=$(get UPLOADS_SHA "$META")
	if [ -f "$SNAP_DIR/uploads.tar" ] && [ "$count" = "$(get UPLOADS_FILES "$META")" ] \
		&& [ -z "$(find wp-content/uploads -type f -newer "$SNAP_DIR/uploads.tar" ! -path '*/uploads/cache/*' ! -path '*/uploads/wc-logs/*' ! -path '*/uploads/elementor/css/*' -print -quit)" ]; then
		echo "    unchanged ($count files), reusing uploads.tar"
	else
		tar -cf "$stage/uploads.tar" -C wp-content "${UPLOAD_EXCLUDES[@]}" uploads
		uploads_sha=$(sha "$stage/uploads.tar")
		echo "    $count files"
	fi

	note "private files"
	local files=() g f
	shopt -s nullglob
	for g in "${PRIVATE_GLOBS[@]}"; do for f in $g; do [ "${f##*/}" = README.md ] || files+=("$f"); done; done
	shopt -u nullglob
	if [ ${#files[@]} -gt 0 ]; then
		tar -cf "$stage/private.tar" "${files[@]}"
		echo "    ${#files[@]} files"
	else
		echo "    none found"
	fi

	local site_url
	site_url=$(wpcli option get siteurl 2>/dev/null | tr -d '\r')
	{
		echo "ID=$id"
		echo "CREATED=$(date -u '+%Y-%m-%d %H:%M UTC')"
		echo "HOST=$HOST"
		echo "GIT_COMMIT=$(git rev-parse --short HEAD)"
		echo "SITE_URL=$site_url"
		echo "UPLOADS_FILES=$count"
		echo "UPLOADS_SHA=$uploads_sha"
		echo "DB_SHA=$(sha "$stage/db.sql.gz")"
		[ -f "$stage/private.tar" ] && echo "PRIVATE_SHA=$(sha "$stage/private.tar")"
	} > "$stage/snapshot.env"

	# Data files first, snapshot.env last: an import only trusts files whose checksum matches it.
	[ -f "$stage/private.tar" ] || rm -f "$SNAP_DIR/private.tar"
	for f in db.sql.gz uploads.tar private.tar plugins.txt; do
		if [ -f "$stage/$f" ]; then mv -f "$stage/$f" "$SNAP_DIR/$f"; fi
	done
	mv -f "$stage/snapshot.env" "$META"
	printf 'ID=%s\nUPLOADS_SHA=%s\n' "$id" "$uploads_sha" > "$STATE"
	note "exported $id to $SNAP_DIR"
}

import_snapshot() {
	local force=${1:-}
	[ -f "$META" ] || die "no snapshot in $SNAP_DIR. Set MIROIR_SNAPSHOT_DIR in .env to the shared folder."
	local id f key
	id=$(get ID "$META")
	if [ "$id" = "$(get ID "$STATE")" ] && [ "$force" != --force ]; then
		echo "Already up to date with $id."
		return 0
	fi
	for f in db.sql.gz:DB_SHA uploads.tar:UPLOADS_SHA private.tar:PRIVATE_SHA; do
		key=${f#*:}; f=${f%%:*}
		[ -n "$(get "$key" "$META")" ] || continue
		[ -f "$SNAP_DIR/$f" ] && [ "$(sha "$SNAP_DIR/$f")" = "$(get "$key" "$META")" ] \
			|| die "$f does not match snapshot.env yet (still syncing?). Try again in a minute."
	done
	note "importing $id (from $(get HOST "$META"), $(get CREATED "$META"))"

	start_stack
	if [ -n "$(db_exec mariadb -uroot -N -e "SHOW TABLES" "$DB_NAME" 2>/dev/null)" ]; then
		mkdir -p "$BACKUP_DIR"
		local backup
		backup=$BACKUP_DIR/$(date -u +%Y%m%dT%H%M%SZ)-before-import.sql.gz
		note "backing up current database to $backup"
		dump_db "$backup"
		ls -1t "$BACKUP_DIR"/*.sql.gz | tail -n +4 | while read -r old; do rm -f "$old"; done
	fi

	note "database"
	db_exec mariadb -uroot -e "DROP DATABASE IF EXISTS \`$DB_NAME\`;
		CREATE DATABASE \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
		GRANT ALL ON \`$DB_NAME\`.* TO '$DB_USER'@'%';"
	gunzip -c "$SNAP_DIR/db.sql.gz" | db_exec mariadb -uroot "$DB_NAME"

	note "plugins"
	# --prune: the imported database only knows the snapshot's plugins, so mirror that set exactly.
	bash tools/plugins.sh sync --prune --manifest "$SNAP_DIR/plugins.txt" || echo "    some plugins need attention (see above)"
	if ! diff -q <(tr -d '\r' < "$SNAP_DIR/plugins.txt") <(tr -d '\r' < plugins.txt) >/dev/null 2>&1; then
		echo "    plugins.txt in git differs from the snapshot's: git pull (or commit on the other machine)."
	fi

	local from=$(get SITE_URL "$META")
	if [ -n "$from" ] && [ "$from" != "$WP_URL" ]; then
		note "site URL $from -> $WP_URL"
		wpcli search-replace "$from" "$WP_URL" --all-tables-with-prefix --skip-columns=guid --report-changed-only 2>/dev/null
		# WCML caches switcher asset URLs as serialized objects search-replace cannot rewrite; it rebuilds them.
		wpcli option delete wcml_currency_switcher_template_objects >/dev/null 2>&1 || true
		wpcli elementor replace_urls "$from" "$WP_URL" 2>/dev/null || true
	fi

	note "uploads"
	local uploads_sha=$(get UPLOADS_SHA "$META")
	if [ "$uploads_sha" = "$(get UPLOADS_SHA "$STATE")" ] && [ "$force" != --force ]; then
		echo "    unchanged since last sync"
	else
		tar -xf "$SNAP_DIR/uploads.tar" -C wp-content
		echo "    $(get UPLOADS_FILES "$META") files"
	fi
	if [ -f "$SNAP_DIR/private.tar" ]; then
		note "private files"
		tar -xf "$SNAP_DIR/private.tar"
	fi

	note "caches"
	wpcli elementor flush_css 2>/dev/null || true
	wpcli cache flush 2>/dev/null || true
	wpcli rewrite flush 2>/dev/null || true

	printf 'ID=%s\nUPLOADS_SHA=%s\n' "$id" "$uploads_sha" > "$STATE"
	note "imported $id. Open $WP_URL"
}

status() {
	if [ "${1:-}" = --hint ]; then
		# One line for git hooks, only when there is something to import.
		[ -f "$META" ] && [ "$(get ID "$META")" != "$(get ID "$STATE")" ] \
			&& echo "snapshot: newer content from $(get HOST "$META") ($(get CREATED "$META")): run $(tool_cmd snapshot) import"
		return 0
	fi
	echo "Snapshot folder: $SNAP_DIR"
	if [ -f "$META" ]; then
		echo "Shared snapshot: $(get ID "$META")  from $(get HOST "$META"), $(get CREATED "$META"), git $(get GIT_COMMIT "$META")"
	else
		echo "Shared snapshot: none"
	fi
	echo "Last synced here: $(get ID "$STATE")"
	if [ -f "$META" ] && [ "$(get ID "$META")" != "$(get ID "$STATE")" ]; then
		echo "-> The shared snapshot is not on this machine yet: $(tool_cmd snapshot) import"
	fi
}

case "${1:-}" in
	export) shift; export_snapshot "$@" ;;
	import) shift; import_snapshot "$@" ;;
	status) shift; status "$@" ;;
	*) sed -n '2,14p' "$0" | sed 's/^# \{0,1\}//'; exit 1 ;;
esac
