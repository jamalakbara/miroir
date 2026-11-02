# Shared helpers for tools/*.sh. Source from the repo root:  . tools/lib/common.sh
# Needs bash: Git Bash on Windows, the system bash (3.2+) on macOS.

# Git Bash rewrites /var/www/... arguments into Windows paths; Docker needs them as-is.
export MSYS_NO_PATHCONV=1
export MSYS2_ARG_CONV_EXCL='*'

REPO_ROOT=$(pwd)

die() { echo "error: $*" >&2; exit 1; }
note() { echo "==> $*"; }

# Load .env without overriding variables already set in the environment.
load_env() {
	[ -f .env ] || die ".env missing. Copy .env.example to .env and set passwords."
	while IFS= read -r line || [ -n "$line" ]; do
		line=${line%$'\r'}
		case "$line" in '' | '#'*) continue ;; esac
		key=${line%%=*}
		val=${line#*=}
		val=${val%\"}; val=${val#\"}; val=${val%\'}; val=${val#\'}
		[ -z "${!key+x}" ] && export "$key=$val"
	done < .env
	: "${WP_PORT:=8080}"
	: "${WP_URL:=http://localhost:$WP_PORT}"
}

# MIROIR_SNAPSHOT_DIR as a bash path: ~ expanded, and on Windows "D:\x" becomes "/d/x"
# (GNU tar reads "D:" as a remote host).
snapshot_dir() {
	local dir=${MIROIR_SNAPSHOT_DIR:-.snapshots}
	dir=${dir/#\~/$HOME}
	if command -v cygpath >/dev/null 2>&1; then dir=$(cygpath -u "$dir"); fi
	printf '%s' "$dir"
}

# How to call a tools script on this OS (Windows wrappers avoid WSL's bash).
tool_cmd() {
	if command -v cygpath >/dev/null 2>&1; then printf 'tools\\%s.cmd' "$1"; else printf 'tools/%s.sh' "$1"; fi
}

compose() { docker compose --progress quiet "$@"; }

# True when Docker answers and both db and wordpress containers are running.
stack_running() {
	local running
	running=$(compose ps --status running --services 2>/dev/null) || return 1
	printf '%s\n' "$running" | grep -qx db && printf '%s\n' "$running" | grep -qx wordpress
}

# WP-CLI as www-data (uid 33).
wpcli() { compose run --rm -T wpcli "$@"; }

# Run a POSIX sh script (from stdin) inside the WP-CLI container as root.
# Needed for plugin installs: the wp_plugins volume root belongs to root.
wpcli_root_sh() { compose run --rm -T --user 0:0 --entrypoint sh wpcli -s; }

# mariadb client inside the db container, authenticated as root.
db_exec() { compose exec -T -e MYSQL_PWD="$DB_ROOT_PASSWORD" db "$@"; }
