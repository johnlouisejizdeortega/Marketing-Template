# Deploying to Laravel Cloud

This repository is a **Laravel 13 application that serves a static marketing
site** from `public/`. That gives you Laravel Cloud's managed infrastructure
(zero-config web server, HTTPS, CDN, deploys from Git) while keeping the front
end as plain static HTML/CSS/JS for top PageSpeed scores.

> No database is required. Sessions/cache/queue use `cookie`/`file`/`sync`
> drivers, so there are **no migrations to run**.

---

## 1. Push the branch (already done)

The app lives on branch **`claude/marketing-site-pagespeed-90uzo1`**.

## 2. Create the project in Laravel Cloud

1. Go to <https://cloud.laravel.com> → **Create application**.
2. Connect your GitHub account and pick the
   `johnlouisejizdeortega/marketing-template` repository.
3. Set the deploy branch to `claude/marketing-site-pagespeed-90uzo1`
   (or merge it into `main` first and deploy `main`).
4. When asked about a **database**, choose **None** — this site doesn't use one.

## 3. Environment variables

In the application's **Environment** settings, set:

```
APP_NAME="BoilerCo UK"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_KEY=               # click "Generate" in the Laravel Cloud UI

SESSION_DRIVER=cookie
CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stack
```

`.env.example` already ships these defaults, so most fields are pre-filled.
**Generate the `APP_KEY`** in the dashboard (or run `php artisan key:generate`
locally and paste it).

## 4. Build & deploy commands

Laravel Cloud auto-detects a Laravel app. Use these:

- **Build command** (default is fine):
  ```
  composer install --no-dev --optimize-autoloader
  ```
  There is **no `package.json`**, so no Node/Vite build runs — nothing to configure.

- **Deploy command** — use Laravel Cloud's defaults but **remove the migrate
  step** (there's no database):
  ```
  php artisan config:cache
  php artisan route:cache
  ```
  > If you leave `php artisan migrate --force` in the deploy script it will fail
  > because no database is attached. Just delete that line.

The web root is `public/` (Laravel standard) — Laravel Cloud handles this
automatically.

## 5. Deploy

Click **Deploy**. On success you'll get a `*.laravel.cloud` URL; add your custom
domain under **Domains** and Laravel Cloud provisions HTTPS automatically.

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
php artisan serve         # http://127.0.0.1:8000
```

Or serve the static files alone with any static server (the marketing site is
self-contained inside `public/`):

```bash
php -S localhost:8000 -t public
```
