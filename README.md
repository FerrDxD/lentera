# LENTERA Score

A Laravel + Inertia.js application for managing cultural competition scoring.

## Features
- Admin panel to create, edit, and toggle competition (Lomba) status.
- Juri scoring interface with real‑time leaderboard.
- Responsive UI built with Tailwind CSS, Lucide icons, and a generated design system.
- SQLite for local development, Supabase PostgreSQL for production.
- Deployable to Railway, Render, or Vercel (see deployment notes).

## Getting Started

```bash
# Clone the repository
git clone <repo-url>
cd Lentera

# Install PHP dependencies (requires a full PHP build, not Herd Lite)
composer install

# Install Node dependencies
npm ci

# Set up environment
cp .env.example .env
# Edit .env – set DB_CONNECTION=sqlite for local dev or pgsql for Supabase

# Generate app key
php artisan key:generate

# Run migrations & seeders
php artisan migrate --seed

# Start the development server
php artisan serve
npm run dev   # or npm run build for production assets
```

## Testing
```bash
php artisan test
```
All feature tests pass.

## Deployment
- **Railway / Render** – recommended for PHP apps.
- **Vercel** – possible with custom server‑less functions; see `vercel.json`.

## Contributing
See `CONTRIBUTING.md`.

## License
MIT – see `LICENSE`.
