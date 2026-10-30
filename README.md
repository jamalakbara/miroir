# Miroir

Local WordPress + WooCommerce rebuild of miroir.id.

## Layout

- `docker-compose.yml` — WordPress (PHP 8.3, Apache), MariaDB 11.4, WP-CLI runner
- `wp-content/` — themes, plugins, uploads (bind-mounted into the container)
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
