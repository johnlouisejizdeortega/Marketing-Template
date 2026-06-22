# Deploying to Laravel Cloud

This repository is a **Laravel 13 app** that is a **private admin control panel**
with a built-in preview of a marketing website. It runs on Laravel Cloud's
managed infrastructure (zero-config web server, HTTPS, CDN, deploys from Git).

> **No database is required.** The dashboard logs in with a single shared
> password (env `DASHBOARD_PASSWORD`) and keeps its session in an encrypted
> cookie. There are no migrations to run and no DB to attach.

The whole app is private: landing on `/` shows the **login** screen, then the
**dashboard**. The marketing website is only viewable, gated, via the
in-dashboard **Preview** (`/dashboard/preview`).

---

## 1. Push the branch

The app lives on branch **`claude/marketing-site-pagespeed-90uzo1`** (also merged
to `main`).

## 2. Create the project in Laravel Cloud

1. Go to <https://cloud.laravel.com> → **Create application**.
2. Connect your GitHub account and pick the
   `johnlouisejizdeortega/marketing-template` repository.
3. Set the deploy branch to `main` (or the feature branch).
4. When asked about a **database**, choose **None** — this app doesn't use one.

## 3. Environment variables

In the application's **Environment** settings, set:

```
APP_NAME="BoilerCo UK"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_KEY=                 # click "Generate" in the Laravel Cloud UI

SESSION_DRIVER=cookie
CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stack

# The dashboard password — paste your own here:
DASHBOARD_PASSWORD=your-strong-password
```

`.env.example` ships these defaults. **Generate the `APP_KEY`** in the dashboard
(it encrypts the session cookie, so it's required). `SESSION_DRIVER` already
defaults to `cookie`, but setting it explicitly is good practice.

## 4. Build & deploy commands

Laravel Cloud auto-detects a Laravel app. Use these:

- **Build command** (default is fine):
  ```
  composer install --no-dev --optimize-autoloader
  ```
  There is **no `package.json`**, so no Node/Vite build runs.

- **Deploy command**:
  ```
  php artisan config:cache
  php artisan route:cache
  ```
  > No `migrate` step — there's no database.

The web root is `public/` — Laravel Cloud handles this automatically.

## 5. Deploy

Click **Deploy**. On success you'll get a `*.laravel.cloud` URL; add your custom
domain under **Domains** and Laravel Cloud provisions HTTPS automatically. Visit
the URL → you'll be sent to `/login`. Enter `DASHBOARD_PASSWORD` to reach the
dashboard.

---

## The dashboard

A private control panel reached at **`/login`** → **`/dashboard`**:

- **Generate** — the Design Copier (paste a page's source, extract its colours &
  type, get CSS variables for `public/css/styles.css`).
- **PageSpeed** — runs Google PageSpeed Insights and shows scores + Core Web Vitals.
- **SEO** — a Lighthouse-based on-page SEO checklist.
- **Preview site** — a framed, private preview of the marketing website
  (`public/site.html`) in desktop / tablet / mobile widths.

PageSpeed and SEO call Google's PSI API **directly from the browser**, so the
server makes no outbound requests and stores no API key. To avoid PSI rate
limits, create a free [PSI API key](https://developers.google.com/speed/docs/insights/v5/get-started)
and paste it into the tool — it's saved in your browser's `localStorage` only.
Restrict the key by HTTP referrer in the Google Cloud console.

### Changing the password

Update `DASHBOARD_PASSWORD` in the Laravel Cloud **Environment** settings and
redeploy (or clear config cache). It is a single shared password — there is no
user database.

## How requests are served

- `public/css/**`, `public/js/**`, `public/tools/**`, `robots.txt`, `sitemap.xml`
  and `*.html` are **served directly by the web server** — fast, cacheable, no PHP.
- `routes/web.php` gates everything: `/` redirects to `/login` or `/dashboard`;
  the dashboard pages and the `/site` preview target require the password session;
  a catch-all maps any remaining `/foo` to `public/foo.html`.
- `/up` is Laravel's built-in health check.

## Run locally

```bash
composer install
cp .env.example .env
php artisan key:generate

# set a password (or edit .env directly):
#   DASHBOARD_PASSWORD=your-strong-password

php artisan serve         # http://127.0.0.1:8000  → redirects to /login
```

No database, no migrations — it just runs.
