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

- Home, bag, checkout and account: Pages > edit with Elementor.
- Header, footer, product page, shop/collections, search, text pages, 404 and
  maintenance: Templates > Saved Templates ("Miroir: …"). Which template draws
  each screen: Site Settings > Miroir > Templates.
- Colours and fonts: Site Settings > Global Colors / Global Fonts ("miroir_*").
  Layout, products, linked pages, maintenance and every label: Site Settings > Miroir.

```bash
docker compose run --rm wpcli miroir elementor setup [--force]   # build templates and pages from the current design
docker compose run --rm wpcli miroir elementor reset             # back to the theme's block templates
```

With Elementor inactive, or a screen without a template, the theme's block
templates draw the site as before.
