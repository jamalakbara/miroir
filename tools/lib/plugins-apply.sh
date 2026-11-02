# Runs inside the WP-CLI container as root, piped in by tools/plugins.sh sync.
# Expects APPLY ("slug version status" lines), REMOVE (slugs), FULL and PRUNE (0/1).
set -u
cd /var/www/html
wp() { command wp --allow-root "$@"; }

if ! wp core is-installed 2>/dev/null; then
	echo "plugins: WordPress has no database yet. Run tools/snapshot.sh import first." >&2
	exit 3
fi

installed() {
	wp plugin list --fields=name,version,status --format=csv 2>/dev/null \
		| tail -n +2 | tr ',' ' ' | grep -v '^miroir-'
}
failed=""
have=$(installed)

# Install or change version.
while read -r slug ver status; do
	[ -n "$slug" ] || continue
	cur=$(printf '%s\n' "$have" | awk -v s="$slug" '$1 == s { print $2 }')
	[ "$cur" = "$ver" ] && continue
	if [ -z "$cur" ]; then echo "plugins: install $slug $ver"; else echo "plugins: $slug $cur -> $ver"; fi
	wp plugin install "$slug" --version="$ver" --force --quiet 2>&1 | grep -v 'Warning:' || true
	if [ -d "wp-content/plugins/$slug" ] && [ "$(wp plugin get "$slug" --field=version 2>/dev/null)" = "$ver" ]; then
		chown -R 33:33 "wp-content/plugins/$slug"
	else
		echo "plugins: could not install $slug $ver (not on wordpress.org? install it by hand)" >&2
		failed="$failed $slug"
	fi
done <<EOF
$APPLY
EOF

# Activate and deactivate.
have=$(installed)
pending=""
while read -r slug ver status; do
	[ -n "$slug" ] || continue
	cur=$(printf '%s\n' "$have" | awk -v s="$slug" '$1 == s { print $3 }')
	[ -n "$cur" ] || continue
	if [ "$status" = active ] && [ "$cur" != active ]; then
		pending="$pending $slug"
	elif [ "$status" = inactive ] && [ "$cur" = active ]; then
		echo "plugins: deactivate $slug"
		wp plugin deactivate "$slug" --quiet 2>/dev/null || failed="$failed $slug"
	fi
done <<EOF
$APPLY
EOF
# A second pass lets add-ons activate after the plugin they extend (e.g. WooCommerce).
for pass in 1 2; do
	retry=""
	for slug in $pending; do
		[ $pass = 1 ] && echo "plugins: activate $slug"
		if [ $pass = 1 ]; then
			wp plugin activate "$slug" --quiet >/dev/null 2>&1 || retry="$retry $slug"
		else
			wp plugin activate "$slug" --quiet || retry="$retry $slug"
		fi
	done
	pending=$retry
	[ -n "$pending" ] || break
done
failed="$failed$pending"

# Plugins not in the manifest: removed on the other machine (--from), or extras (full sync).
if [ "$FULL" = 1 ]; then
	extras=$(printf '%s\n' "$have" | while read -r slug rest; do
		[ -n "$slug" ] && ! printf '%s\n' "$APPLY" | grep -q "^$slug " && echo "$slug"
	done)
	if [ "$PRUNE" = 1 ]; then
		REMOVE=$extras
	elif [ -n "$extras" ]; then
		echo "plugins: installed here but not in plugins.txt:" $extras
		echo "         commit to keep them (the pre-commit hook lists them), or sync --prune to delete them."
	fi
fi
for slug in $REMOVE; do
	printf '%s\n' "$have" | grep -q "^$slug " || continue
	echo "plugins: remove $slug"
	wp plugin deactivate "$slug" --quiet 2>/dev/null
	wp plugin delete "$slug" --quiet 2>/dev/null || failed="$failed $slug"
done

if [ -n "${failed# }" ]; then
	echo "plugins: failed:$failed" >&2
	exit 1
fi
echo "plugins: in sync"
