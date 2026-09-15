# Window Trip

**Your Complete Travel Partner** — a travel-agency platform for tourist visa processing, air-ticket requests, and tour packages.

## Stack
- **Laravel 13** (PHP 8.4) · **MySQL** (prod) / SQLite (local)
- **Public site:** Blade + Tailwind (SEO-first)
- **Client portal + Admin:** Inertia.js + Vue 3
- Auth: Laravel Breeze (customers) + a separate `admin` guard (staff) · Spatie permissions

## Features
- Public site: home, per-country visa pages with requirements, air tickets, tour packages, contact, application tracking
- Multi-step visa application wizard with document upload
- Client portal: track applications & status
- Admin: dashboard, visa applications (review, status, docs, edit), leads inbox, visa & package management, branded email compose, SMTP settings, profile
- Manual payments (bank transfer / offline) — no online gateway
- Email notifications (applicant confirmation + staff alerts)

## Local development (Laravel Herd)
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev        # or: npm run build
```
Seeds an admin: `admin@windowtrip.test` / `password` (change before production).

## Deployment (shared cPanel — no Node.js)
1. Build assets locally: `npm run build` (committed under `public/build`).
2. Upload the project; point the domain docroot to `/public`.
3. `composer install --no-dev`, configure `.env` for production:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<domain>`
   - `DB_CONNECTION=mysql` + credentials
   - `SESSION_SECURE_COOKIE=true`
   - `MAIL_MAILER=smtp` (or configure SMTP in Admin → Settings)
4. `php artisan key:generate`, `php artisan migrate --force`, `php artisan storage:link`
5. `php artisan config:cache route:cache view:cache`
6. Cron: `* * * * * php /path/artisan schedule:run`

## Notes
- Uploaded documents are stored **privately** (not web-accessible).
- Visa processing only — outcomes are decided by embassies; not guaranteed.
