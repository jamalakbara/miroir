# Miroir

Local WordPress + WooCommerce rebuild of miroir.id.

## Layout

- `docker-compose.yml` — WordPress (PHP 8.3, Apache), MariaDB 11.4, WP-CLI runner
- `wp-content/` — editable custom plugins/themes and uploads (bind-mounted)
- Docker `wp_plugins` volume — installed third-party plugins, shared by WordPress and WP-CLI
- `docs/migration/` — legacy URL inventory from the old site (`legacy-urls.csv`, `.xlsx`)
- `.aimem/` — shared project notebook (`aimem context` to get briefed)

## Run

```bash
cp .env.example .env   # then set passwords
docker compose up -d
```

Site: http://localhost:8080 — admin: http://localhost:8080/wp-admin (credentials in `.env`).

WP-CLI:

```bash
docker compose run --rm wpcli plugin list
```

Stop: `docker compose down` (add `-v` to wipe the database and WordPress core volumes).

## Working on two machines

Git carries the code. Everything else is synced by two scripts. On macOS run
`tools/*.sh` in Terminal. On Windows run the `tools\*.cmd` wrappers from
PowerShell (`tools\snapshot.cmd export`): they use Git Bash, because a plain
`bash` in PowerShell is WSL's and fails. The commands below use the macOS form.

| What | Where it lives | Synced by |
|---|---|---|
| Theme, `miroir-*` plugins, tools, design | git | `git push` / `git pull` |
| Vendor plugins (Elementor, WooCommerce …) | Docker `wp_plugins` volume | `plugins.txt` + git hooks |
| Pages, Elementor layouts, settings, products, orders | Docker `db_data` volume | `tools/snapshot.sh` |
| Uploads, licensed Avenir fonts | gitignored files | `tools/snapshot.sh` |

Once per machine: set `MIROIR_SNAPSHOT_DIR` in `.env` to a private folder both
machines sync (Google Drive, iCloud), then:

```bash
tools/setup.sh
```

```bash
tools/snapshot.sh import
```

Every switch between machines:

```bash
tools/snapshot.sh export   # on the machine you leave, plus git push
```

```bash
tools/snapshot.sh import   # on the machine you arrive at, after git pull
```

- **Plugins sync themselves.** Install or update one in wp-admin and commit: the
  pre-commit hook writes `plugins.txt`. On the other machine `git pull` runs the
  post-merge hook, which installs, updates, activates or removes exactly what
  changed. Plugin settings live in the database and travel with the snapshot.
  `tools/plugins.sh sync` applies the whole list by hand. A plugin that is not on
  wordpress.org cannot be installed this way.
- **Content does not merge.** Work on one machine at a time. Import replaces the
  database; the previous one is kept in `.snapshots-backup/` (last three). Export
  refuses to overwrite a snapshot this machine never imported (`--force` overrides).
- Unchanged uploads are not re-packed or re-extracted. `tools/snapshot.sh status`
  shows what this machine last synced; `git pull` also says when the snapshot is newer.
- The snapshot holds customer data and password hashes: keep it private, never in git.
- Hooks need Docker running; otherwise they skip with a message. `MIROIR_SKIP_HOOKS=1` turns them off.

## Plugin storage on Windows

Third-party plugins run from the Linux `wp_plugins` volume to avoid slow Windows
file access. The four `miroir-*` plugin folders have individual bind mounts, so
editing their files still takes effect immediately. Themes and uploads remain
in the Windows `wp-content` folder.

To migrate plugins from an older checkout that used a full Windows plugin mount:

```powershell
docker compose stop wordpress
./tools/Initialize-PluginVolume.ps1
docker compose up -d wordpress
```

The migration copies only missing vendor plugins and preserves the original
Windows files. Re-running it preserves plugin versions already in the volume.
Install/update vendor plugins through wp-admin or WP-CLI; the old Windows copies
are no longer used. For a fresh store, install required plugins through WP-CLI
as usual. If adding another custom plugin, add its individual mount to both
services in `docker-compose.yml`.

The plugin volume survives ordinary `down`/`up` commands. `docker compose down -v`
also deletes installed vendor plugins, along with database and core volumes.

The volume root belongs to root, so WP-CLI (uid 33) cannot create a new plugin
folder. Install a new vendor plugin as root, then hand it to uid 33:

```bash
docker compose run --rm --user 0:0 --entrypoint sh wpcli -c 'wp --allow-root plugin install <slug> && chown -R 33:33 /var/www/html/wp-content/plugins/<slug>'
```

## Editing the storefront (Elementor)

The site is edited in Elementor (free). The `miroir-*` widgets print the theme's
own sections, so the storefront stays 1:1 with the design in `docs/design/miroir.pen`.

- Every page: Pages > Edit with Elementor. Home, bag, checkout and account use
  their Miroir widgets; text pages (FAQ, size guide, policies, contact …) use the
  "Text page" widget (page title + text in the page's column). The shop page is
  the product listing: its layout is the "Shop and collections" template.
- Header, footer, product page, shop/collections, search, 404, maintenance and
  pages not built with Elementor: Templates > Saved Templates ("Miroir: …"). Which template draws
  each screen: Site Settings > Miroir > Templates.
- Colours and fonts: Site Settings > Global Colors / Global Fonts ("miroir_*").
  Layout, products, linked pages, maintenance and every label: Site Settings > Miroir.

```bash
docker compose run --rm wpcli miroir elementor setup [--force]   # build templates and pages from the current design
docker compose run --rm wpcli miroir elementor reset             # back to the theme's block templates
```

With Elementor inactive, or a screen without a template, the theme's block
templates draw the site as before.

## Automatic popular searches

The search modal ranks the top three successful product searches over the last
30 UTC days. Terms need at least three counts; the section stays hidden until
some qualify. There is no manually configured list or fallback. Existing saved
manual settings are ignored and their editor control has been removed.

Counts are normalized for case and whitespace. Empty results, pagination,
editor previews, staff searches and known bots are excluded. A daily cookie
deduplicates each term per browser and limits contributions to 20 terms per day;
clearing cookies can bypass this, so these are popularity hints, not audited
unique-visitor statistics. Contact details, URLs, numbers and markup are rejected.
The database stores only terms and daily aggregate counts, without visitor IDs
or IP addresses. WP-Cron removes expired counts daily, and ranking caches for
five minutes (invalidated when a search is counted). Full-page caching of search
results must be disabled for server-side counting to run.

The theme creates its aggregate table automatically. Regression tests run in a
database transaction and roll back their test data:

```powershell
docker compose run --rm -T -v "${PWD}/tools:/tmp/miroir-tests:ro" wpcli eval-file /tmp/miroir-tests/test-search-popularity.php
```
