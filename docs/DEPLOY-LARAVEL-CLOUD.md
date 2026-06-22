# Deploying to Laravel Cloud

This repository is a **Laravel 13 application that serves a static marketing
site** from `public/`. That gives you Laravel Cloud's managed infrastructure
(zero-config web server, HTTPS, CDN, deploys from Git) while keeping the front
end as plain static HTML/CSS/JS for top PageSpeed scores.

> The public marketing pages are static and need no database. The private
> **admin dashboard** (`/login` → `/dashboard`) does: it uses real
> database-backed Laravel auth, so you'll attach a database and run migrations.
> If you don't need the dashboard, skip section 6 and leave the DB as **None**.

---

## 1. Push the branch (already done)

The app lives on branch **`claude/marketing-site-pagespeed-90uzo1`**.

## 2. Create the project in Laravel Cloud

1. Go to <https://cloud.laravel.com> → **Create application**.
2. Connect your GitHub account and pick the
   `johnlouisejizdeortega/marketing-template` repository.
3. Set the deploy branch to `claude/marketing-site-pagespeed-90uzo1`
   (or merge it into `main` first and deploy `main`).
4. When asked about a **database**: choose **PostgreSQL** if you want the admin
   dashboard (recommended). Choose **None** only if you're deploying the public
   marketing pages alone — then skip section 6.

## 3. Environment variables

In the application's **Environment** settings, set:

```
APP_NAME="BoilerCo UK"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_KEY=               # click "Generate" in the Laravel Cloud UI

CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stack

# Admin dashboard (omit this block if you chose DB "None"):
SESSION_DRIVER=database     # use "cookie" if you have no database
DB_CONNECTION=pgsql         # Laravel Cloud injects the DB_* host/credentials
ADMIN_NAME="Site Admin"
ADMIN_EMAIL=you@your-domain.com
ADMIN_PASSWORD=             # a strong password; change it after first login
```

`.env.example` already ships these defaults, so most fields are pre-filled.
**Generate the `APP_KEY`** in the dashboard (or run `php artisan key:generate`
locally and paste it). Laravel Cloud auto-injects the `DB_*` connection
variables when a database is attached.

## 4. Build & deploy commands

Laravel Cloud auto-detects a Laravel app. Use these:

- **Build command** (default is fine):
  ```
  composer install --no-dev --optimize-autoloader
  ```
  There is **no `package.json`**, so no Node/Vite build runs — nothing to configure.

- **Deploy command** — with the dashboard's database attached:
  ```
  php artisan migrate --force
  php artisan config:cache
  php artisan route:cache
  ```
  > **No database (marketing pages only)?** Drop the `migrate` line — it will
  > fail with no DB attached — and set `SESSION_DRIVER=cookie`.

The web root is `public/` (Laravel standard) — Laravel Cloud handles this
automatically.

## 5. Deploy

Click **Deploy**. On success you'll get a `*.laravel.cloud` URL; add your custom
domain under **Domains** and Laravel Cloud provisions HTTPS automatically.

## 6. Admin dashboard (optional)

The dashboard is a private control panel for the site owner, reached at
**`/login`** → **`/dashboard`**. It has three tools:

- **Generate** — the Design Copier (paste a page's source, extract its colours
  & type, get CSS variables for `public/css/styles.css`).
- **PageSpeed** — runs Google PageSpeed Insights and shows scores + Core Web Vitals.
- **SEO** — a Lighthouse-based on-page SEO checklist.

PageSpeed and SEO call Google's PSI API **directly from the browser**, so the
server makes no outbound requests and stores no API key. To avoid PSI rate
limits, create a free [PSI API key](https://developers.google.com/speed/docs/insights/v5/get-started)
and paste it into the tool — it's saved in your browser's `localStorage` only.
Restrict the key by HTTP referrer in the Google Cloud console.

**Seed the admin account** once after the first deploy (Laravel Cloud → command
runner / SSH), using the `ADMIN_*` env values:

```
php artisan db:seed --class=Database\\Seeders\\AdminUserSeeder --force
```

The seeder is idempotent (`updateOrCreate` on the email), so re-running it just
updates the existing admin. **Log in and change the password** immediately.

---

## How requests are served

- `public/css/**`, `public/js/**`, `public/tools/**`, `robots.txt`, `sitemap.xml`
  and every `*.html` are **served directly by the web server** — fast, cacheable,
  no PHP. This is what keeps the Lighthouse/PageSpeed score high.
- `routes/web.php` only handles "directory" URLs that have no physical file:
  `/` → `public/index.html`, `/thank-you`, `/tools/design-copier`, plus a
  catch-all that maps `/foo` to `public/foo.html`.
- `/up` is Laravel's built-in health check (useful for Laravel Cloud monitoring).

## Run locally

```bash
composer install
cp .env.example .env
php artisan key:generate

# For the admin dashboard, create a local SQLite DB + the admin user:
touch database/database.sqlite     # .env already sets DB_CONNECTION=sqlite
php artisan migrate
php artisan db:seed                 # creates the ADMIN_* account

php artisan serve         # http://127.0.0.1:8000  (dashboard at /login)
```

Or serve the static files alone with any static server (the marketing site is
self-contained inside `public/`):

```bash
php -S localhost:8000 -t public
```
