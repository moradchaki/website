# AGENTS.md — Laptops e‑commerce (Symfony 8.0)

## Stack

- **PHP 8.4+**, Symfony 8.0, Doctrine ORM 3.6, MariaDB 10.11
- **Frontend**: Tailwind CSS (CDN in `base.html.twig`, config in a `<script>` block), Stimulus 3, Turbo 8, AssetMapper (no Webpack Encore)
- **Icons**: Material Symbols (Google Fonts) via `<span class="material-symbols-outlined">`
- **Fonts**: Geist (headings) + Inter (body) via Google Fonts
- **Twig UX Components** in `src/Twig/Components/` + `templates/components/`

## Quick start

```bash
docker compose up -d                         # php, cron, nginx :8000, MariaDB :3307, Adminer :8082, Mailpit :8025
symfony console d:m:m --no-interaction       # run pending migrations
symfony console doctrine:fixtures:load       # seed data
symfony console asset-map:compile            # build frontend assets
symfony serve                                 # dev server (or nginx on :8000)
```

## Key commands

| What | Command |
|---|---|
| Run migrations | `symfony console d:m:m --no-interaction` |
| Load fixtures | `symfony console doctrine:fixtures:load` |
| Generate migration | `symfony console d:m:diff` |
| List routes | `symfony console debug:router` |
| Debug assets | `symfony console debug:asset-map` |
| Compile assets | `symfony console asset-map:compile` |
| Run tests | `symfony console phpunit` (no tests exist yet; `tests/` has only `bootstrap.php`) |
| Create entity/controller | `symfony console make:entity` / `make:controller` |

## Automation commands (cron‑driven)

```bash
symfony console app:scrape-ebay             # daily 06:00 — creates ≤15 products from eBay listings
symfony console app:auto-generate-laptops   # daily 06:00 — creates 15 random laptops
symfony console app:auto-generate-orders    # daily 07:00 — creates 15–20 fake orders
```
Logs written to `var/log/scrape-ebay.log`, `var/log/auto-laptops.log`, `var/log/auto-orders.log`.

Cron schedules are in `docker/cron/crontab`. The `cron` service in `compose.yaml` runs the PHP image with dcron and mounts the app volume, so all three commands fire automatically at their scheduled times via `docker compose up -d`.

## Database

- MariaDB on port **3307** (not the default 3306)
- Test env appends `_test` suffix to DB name (`doctrine.yaml` under `when@test`)
- Messenger uses Doctrine transport (`MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0`)

## Architecture notes

- **No Symfony Form component used** — admin CRUD reads raw `$request->request->get()` instead of form classes
- **No formal tests exist** — `tests/bootstrap.php` is the only file
- Controllers use `#[Route]` attributes with route names prefixed by controller domain: `product_`, `auth_`, `admin_`, `cart_`, `comparison_`
- Security: `ROLE_USER` required for `/admin` and `/profile` (access_control in `security.yaml`); form_login with csrf
- Entities use `#[ORM\HasLifecycleCallbacks]` with `PrePersist`/`PreUpdate` for timestamps
- `OrderStatus` is a PHP 8.1 backed enum (`src/Entity/OrderStatus.php`)

## Admin routes (all require login)

| Route | Name |
|---|---|
| `GET  /admin` | `admin_dashboard` |
| `GET  /admin/products` | `admin_product_index` |
| `GET/POST /admin/products/new` | `admin_product_new` |
| `GET/POST /admin/products/{id}/edit` | `admin_product_edit` |
| `POST /admin/products/{id}/delete` | `admin_product_delete` |
| `GET  /admin/orders` | `admin_order_index` |
| `GET  /admin/orders/{id}` | `admin_order_show` |

## File ownership

- `src/Entity/` — 14 Doctrine entities
- `src/Repository/` — 11 Doctrine repositories
- `src/Controller/` — 10 controllers
- `src/Command/` — 3 console commands
- `src/DataFixtures/` — 1 fixtures loader
- `src/Twig/Components/` — 4 Twig UX components
- `templates/` — Twig templates organized by domain
- `assets/` — JS (Stimulus) + CSS (Tailwind via CDN, app.css for extras)
