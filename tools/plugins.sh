#!/usr/bin/env bash
# Keep vendor plugins (the Docker wp_plugins volume) in step with plugins.txt,
# so every machine runs the same plugins at the same versions.
#
#   tools/plugins.sh freeze              write the installed vendor plugins to plugins.txt
#   tools/plugins.sh sync                install, update, activate and deactivate to match plugins.txt
#   tools/plugins.sh sync --prune        ...and delete vendor plugins not listed
#   tools/plugins.sh sync --from <rev>   apply only what changed in plugins.txt since <rev>
#   tools/plugins.sh sync --manifest <file>
#
# --hook (used by .githooks): skip quietly when Docker is down; freeze never drops
# a listed plugin or lowers its version, so an out-of-date machine cannot undo the other's change.
# Own plugins (miroir-*) are tracked in git and skipped here.
set -euo pipefail
cd "$(dirname "$0")/.."
. tools/lib/common.sh

MANIFEST=plugins.txt
HEADER='# Vendor plugins: <slug> <version> <active|inactive>.
# Written by tools/plugins.sh freeze on every commit. Install plugins in wp-admin or WP-CLI, not here.'

# Non-comment manifest lines, sorted.
entries() { tr -d '\r' | { grep -Ev '^[[:space:]]*(#|$)' || true; } | LC_ALL=C sort; }

# Installed vendor plugins as "slug version status", sorted.
installed_plugins() {
	wpcli plugin list --fields=name,version,status --format=csv 2>/dev/null \
		| tr -d '\r' | tail -n +2 \
		| awk -F, '$1 !~ /^miroir-/ && ($3 == "active" || $3 == "inactive") { print $1, $2, $3 }' \
		| LC_ALL=C sort
}

require_stack() {
	stack_running && return 0
	[ "$1" = 1 ] && { echo "plugins: Docker stack is not running, skipped ($2)." >&2; exit 0; }
	die "Docker stack is not running. Start it with: docker compose up -d"
}

freeze() {
	local hook=0
	[ "${1:-}" = --hook ] && hook=1
	require_stack $hook "run $(tool_cmd plugins) freeze later"

	local have want list
	have=$(installed_plugins) || die "could not list plugins (is WordPress installed? try $(tool_cmd snapshot) import)"
	[ -n "$have" ] || die "no vendor plugins installed; refusing to write an empty plugins.txt"
	want=$([ -f "$MANIFEST" ] && entries < "$MANIFEST" || true)

	if [ $hook = 1 ] && [ -n "$want" ]; then
		list=$(printf '%s\n' "$want" | awk '
			function newer(a, b,   x, y, n, m, i) {
				n = split(a, x, "."); m = split(b, y, ".")
				for (i = 1; i <= (n > m ? n : m); i++)
					if (x[i] + 0 != y[i] + 0) return x[i] + 0 > y[i] + 0
				return 0
			}
			FNR == NR { want[$1] = $0; ver[$1] = $2; next }
			{
				have[$1] = 1
				if (($1 in ver) && newer(ver[$1], $2)) {
					print want[$1]
					print "plugins: " $1 " is " $2 " here but plugins.txt has " ver[$1] "; run tools/plugins.sh sync" > "/dev/stderr"
				} else print
			}
			END {
				for (s in want) if (!(s in have)) {
					print want[s]
					print "plugins: " s " is listed but not installed here; sync installs it, freeze drops it" > "/dev/stderr"
				}
			}' /dev/stdin <(printf '%s\n' "$have") | LC_ALL=C sort)
	else
		list=$have
	fi

	printf '%s\n%s\n' "$HEADER" "$list" > "$MANIFEST.tmp"
	mv "$MANIFEST.tmp" "$MANIFEST"
	echo "plugins: plugins.txt lists $(printf '%s\n' "$list" | wc -l | tr -d ' ') vendor plugins"
}

sync() {
	local from="" prune=0 manifest=$MANIFEST hook=0
	while [ $# -gt 0 ]; do
		case $1 in
			--from) from=$2; shift 2 ;;
			--manifest) manifest=$2; shift 2 ;;
			--prune) prune=1; shift ;;
			--hook) hook=1; shift ;;
			*) die "unknown option: $1" ;;
		esac
	done
	[ -f "$manifest" ] || die "$manifest not found"
	require_stack $hook "run $(tool_cmd plugins) sync after docker compose up -d"

	local want apply remove="" full=1
	want=$(entries < "$manifest")
	if [ -n "$from" ]; then
		# Only what the incoming commits changed, so local uncommitted plugin work is left alone.
		local old
		full=0
		old=$(git show "$from:$MANIFEST" 2>/dev/null | entries || true)
		apply=$(LC_ALL=C comm -13 <(printf '%s\n' "$old") <(printf '%s\n' "$want"))
		remove=$(LC_ALL=C comm -23 \
			<(printf '%s\n' "$old" | cut -d' ' -f1 | LC_ALL=C sort) \
			<(printf '%s\n' "$want" | cut -d' ' -f1 | LC_ALL=C sort))
		if [ -z "$apply$remove" ]; then
			echo "plugins: no changes in plugins.txt"
			return 0
		fi
	else
		apply=$want
	fi

	{
		printf 'FULL=%s\nPRUNE=%s\n' $full $prune
		printf 'APPLY=$(cat <<'\''MIROIR_EOF'\''\n%s\nMIROIR_EOF\n)\n' "$apply"
		printf 'REMOVE=$(cat <<'\''MIROIR_EOF'\''\n%s\nMIROIR_EOF\n)\n' "$remove"
		cat tools/lib/plugins-apply.sh
	} | wpcli_root_sh
}

case "${1:-}" in
	freeze) shift; freeze "$@" ;;
	sync) shift; sync "$@" ;;
	*) sed -n '2,13p' "$0" | sed 's/^# \{0,1\}//'; exit 1 ;;
esac
