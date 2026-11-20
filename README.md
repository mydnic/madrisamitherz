# Madrisa mit Herz

One-page microsite of Stiftung Madrisa mit Herz (madrisamitherz.ch).
Laravel 12 · Filament 5 · outerweb/filament-settings · Tailwind 4 · Alpine · MySQL.

## Local install

```bash
composer install
yarn install && yarn build
cp .env.example .env && php artisan key:generate
php artisan migrate --seed      # seeds all texts + images (settings:sync)
php artisan storage:link
php artisan make:filament-user
```

Admin: `/admin` → **Landing Page**. Everything on the page is there, pre-filled.

## Production (first deploy)

Server needs PHP 8.4+, MySQL 8, Composer. Node is only needed to build assets
(build locally and upload `public/build` if the server has no Node).

1. Point `madrisamitherz.ch` (+ `www`) to the server, web root = `public/`, HTTPS on (Let's Encrypt).
2. Upload the code (git clone or deploy tool).
3. Create an empty MySQL database + user.
4. `cp .env.example .env`, then set:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://madrisamitherz.ch
   DB_DATABASE=… DB_USERNAME=… DB_PASSWORD=…
   MAIL_MAILER=smtp
   MAIL_HOST=… MAIL_PORT=587 MAIL_USERNAME=… MAIL_PASSWORD=…
   MAIL_FROM_ADDRESS=noreply@madrisamitherz.ch
   MAIL_FROM_NAME="Stiftung Madrisa mit Herz"
   ```
   Mail is only used by the contact form (sent to the address in Admin → Meta, reply-to = visitor).
5. Install and set up:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force --seed
   php artisan storage:link
   php artisan make:filament-user        # account for the foundation
   php artisan optimize
   ```
6. Build assets: `yarn install && yarn build` (or upload `public/build`).
7. Make `storage/` and `bootstrap/cache/` writable by the web server user.
8. Log in on `/admin`, check the content, then hand over the login.

## Later deploys

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan settings:sync --force     # adds new content keys, never overwrites edits
php artisan optimize
yarn build                            # if assets changed
```

## Content & languages

- Every text/image is a setting stored per locale (`de.hero.title`, `fr.hero.title`…). Defaults live in `app/Support/SiteContent.php`; `settings:sync` adds missing keys only, so client edits are safe.
- Images are seeded to `storage/app/public/defaults/` from `public/images/`.
- Languages: Admin → Landing Page → Einstellungen → switch on FR / IT. German is always on. Every language is seeded with the German copy, so a new language tab is complete and only needs translating. The header language switcher appears automatically.

## Video (Stories)

Admin → Geschichten → Video: none, YouTube link, Google Drive link (shared "anyone with the link") or MP4 upload. The player loads only when the visitor clicks play.

## Donations (RaiseNow / TWINT)

The "Hier kannst du spenden!" section embeds the official RaiseNow form (amounts, one-off/monthly, TWINT, card, donation receipt are all handled by RaiseNow).

- Admin → Einstellungen → "RaiseNow Formular-URL": the `url` from RaiseNow Hub's embed code, e.g. `https://donate.raisenow.io/zptqy` (the language `?lng=` is added automatically from the visitor's language).
- While "Spendenformular öffentlich" is off, only logged-in admins see the form (preview); visitors see a short text + contact button. Switch it on to go live.
- Amounts, texts, colours and payment methods of the form itself are configured in RaiseNow Hub, not here.

## Contact form

Submissions are saved in Admin → Kontaktanfragen (so nothing is lost if mail fails) and e-mailed to the contact address. Spam protection: honeypot field + 5 submissions/minute per IP.

## Legal pages

`/impressum` and `/datenschutz` are edited in Admin → Landing Page → Impressum & Datenschutz.

## Font

The design uses **Tonos** (Black[Foundry], commercial). Until a web licence is bought, Fira Sans is used. With the woff2 files: put them in `public/fonts/`, add an `@font-face` named `Tonos` in `resources/css/app.css`, rebuild.

## Tests

```bash
php artisan test
```
