<p align="center">
  <img src="public/RSMS%20(1).png" alt="RSMS Logo" width="220" />
</p>

<h1 align="center">RSMS • Results & Schools Management System</h1>

<p align="center">
  <a href="#features">Features</a> ·
  <a href="#tech-stack">Tech Stack</a> ·
  <a href="#quickstart">Quickstart</a> ·
  <a href="#api">API</a> ·
  <a href="#deployment">Deployment</a> ·
  <a href="#license">License</a>
</p>

---

## Overview

RSMS is a modern, fast, and mobile-friendly platform for managing and publishing examination results, school directories, calendar events, publications, and blogs/news. It provides a clean admin experience and a polished public UI, with a fully documented JSON API for web/mobile clients.

### Brand Palette

- Primary Green: `#0B6B3A`
- Accent Green: `#0AA74A`
- Lime Highlight: `#E6FF8A`
- Text Dark: `#1B1B18`
- Subtext: `#6B6A67`
- Border: `#ECEBEA`

Use these colors for consistent theming across UI and documentation.

---

## Features

- Results Center with filters: Exam → Year → Region → Council → School
- Grouped results by type (e.g., Mid Term, Annual) with normalized URLs
- Full-screen web PDF viewer (`/view/pdf?src=<url>`) with print/download controls
- Admin panel: results upload (single/bulk), years/types management, settings
- Calendar with month grid + events; recent events list
- Publications with folders and documents
- Blogs/News with categories, latest posts, and details
- Schools and councils management (CRUD + CSV bulk)
- Public JSON API for all core data

---

## Tech Stack

- Laravel 11 (PHP 8+)
- Blade + Vue 3 (Vite bundling)
- TailwindCSS for styling
- MySQL/PostgreSQL compatible schema via migrations

---

## Quickstart

1) Clone and install
```bash
git clone https://github.com/your-org/rsms.git
cd rsms
cp .env.example .env
composer install
npm install
```

2) Configure `.env`
- Database connection
- `APP_URL=http://localhost:8000`
- `FILESYSTEM_DISK=public`

3) Migrate & seed (optional seeds you add)
```bash
php artisan migrate
php artisan db:seed
```

4) Storage link
```bash
php artisan storage:link
```

5) Run the app
```bash
php artisan serve
npm run dev
```

Now open `http://localhost:8000`.

## Login Details

- **URL**: `/login`
- **Default admin credentials** are created by the database seeder.
  - **Email**: `ADMIN_EMAIL` in `.env` (defaults to `admin@rsms.local`)
  - **Password**: `ADMIN_PASSWORD` in `.env` (defaults to `ChangeThis123!`)
- To generate the admin user, run: `php artisan db:seed`
- After first login, navigate to Admin Users to add users or change the password.
- For security, change the default password immediately in production.

---

## API

The RSMS API is documented in detail in `API_README.md`.

- Status/Hero/About
- Exams & Results full flow (exams, regions, districts, schools, documents)
- Calendar & Events
- Blogs & News
- FAQ & Contacts

Open the PDF viewer with:
```
/view/pdf?src=<encoded-url>
```

See: [API_README.md](./API_README.md)

---

## Development Notes

- Ensure `/public/storage` symlink is present for local files to render under `/storage/...`.
- Results documents can be uploaded as files (stored under `storage/app/public`) or referenced by external URLs; the API normalizes all URLs for clients.
- Frontend assets are built with Vite; use `npm run dev` while developing or `npm run build` for production.

---

## Deployment

- Build assets: `npm run build`
- Configure your web server root to `public/`
- Ensure storage is writable and `php artisan storage:link` has been run on the target host.
- Cache routes/config/views in production:
```bash
php artisan route:cache
php artisan config:cache
php artisan view:cache
```

---

## Security

If you discover a vulnerability, please open a private issue or contact the maintainers. Do not disclose publicly until a fix is available.

---

## Contributing

Issues and PRs are welcome. Please follow PSR-12 coding standards and include concise descriptions. For large changes, open an issue to discuss the approach first.

---

## License

This project is licensed under the **MIT License**. See [LICENSE](./LICENSE) for details.

© 2025 RSMS
