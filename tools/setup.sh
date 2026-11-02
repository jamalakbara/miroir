#!/usr/bin/env bash
# One-time setup for each machine (Windows: run in Git Bash). Safe to re-run.
set -euo pipefail
cd "$(dirname "$0")/.."

git config core.hooksPath .githooks
chmod +x .githooks/* tools/*.sh 2>/dev/null || true
echo "git hooks: on (plugins.txt is refreshed on commit and applied on pull)"

if [ ! -f .env ]; then
	cp .env.example .env
	echo "Created .env. Set the passwords and MIROIR_SNAPSHOT_DIR, then run tools/setup.sh again."
	exit 1
fi

. tools/lib/common.sh
load_env
dir=$(snapshot_dir)
if [ -f "$dir/snapshot.env" ]; then
	echo "Shared snapshot found in $dir."
	echo "Next: $(tool_cmd snapshot) import   (installs the plugins and loads the content)"
else
	echo "No snapshot in $dir yet."
	echo "On the machine that has the content: $(tool_cmd snapshot) export"
	if [ -z "${MIROIR_SNAPSHOT_DIR:-}" ]; then
		echo "Tip: set MIROIR_SNAPSHOT_DIR in .env to a folder both machines sync (Google Drive, iCloud)."
	fi
fi
