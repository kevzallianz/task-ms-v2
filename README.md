# Task MS v2

Project documentation for **Task Management System v2** (Laravel 12).

---

## 1) Project Overview

Task MS v2 is a web-based task and project management platform with campaign-centric planning.
It supports:

- Authentication and password reset
- Role-based access (`user`, `superadmin`)
- Campaign management and campaign membership
- Project and project task management
- Remarks, activity tracking, and contributor assignment
- CSV-style import workflow for campaign tasks

Primary objective: provide centralized execution tracking across campaigns and projects with clear ownership and status visibility.

---

## 2) Technology Stack

### Backend
- PHP `^8.2`
- Laravel `^12.0`
- Eloquent ORM + Migrations
- Pest + PHPUnit for testing

### Frontend
- Vite `^7`
- Tailwind CSS `^4`
- Axios

### Infrastructure
- MySQL (default local DB)
- Optional Docker Compose stack (`app`, `nginx`, `db`)

---

## 3) Core Modules

### Authentication
- Login, register, logout
- Forgot/reset password flow
- Session-based auth

### Campaigns
- Create/manage campaigns
- Add campaign members
- Access-level updates for campaign members
- Campaign project lifecycle and campaign task lifecycle

### Projects
- Project create/update/delete
- Project status management
- Project task create/update/delete
- Task remarks and contributor assignment

### Super Admin
- Campaign administration
- User management
- Role update and campaign assignments (single/bulk)

---

## 4) Domain Model (High-Level)

Main entities in `app/Models`:

- `User`
- `Campaign`, `CampaignMember`
- `CampaignProject`, `CampaignProjectActivity`
- `CampaignTask`, `CampaignTaskMember`, `CampaignTaskRemark`
- `Project`, `ProjectActivity`
- `ProjectTask`, `ProjectRemarks`, `ProjectContributor`

Conceptually:

- A campaign has members, projects, and tasks.
- Projects have contributors, tasks, remarks, and activity history.
- Tasks can have remarks and assignees/members.

---

## 5) Request Flow & Access Control

### Routing
- Public routes: home, register, login, password reset endpoints
- Auth routes (`auth` middleware): overview, tasks, projects, campaign endpoints
- Admin routes (`role:superadmin` middleware): prefixed under `/~/...`

### Middleware
- `auth` middleware protects private pages
- `role` middleware validates role-based access to superadmin operations

---

## 6) Key Route Groups

### Public
- `GET /` → welcome/home
- `POST /login`, `POST /register`, `POST /logout`
- Password reset sequence:
	- `GET /password/reset`
	- `POST /password/email`
	- `GET /password/reset/{token}`
	- `POST /password/reset`

### Authenticated User
- `GET /overview`
- `GET /tasks`
- Project routes under `/projects...`
- Campaign routes under `/campaign` and `/campaigns/...`

### Super Admin
- `GET /~/campaigns`
- `POST /~/campaigns`
- `GET /~/campaigns/{campaign}/members`
- `GET /~/users`
- Bulk/single campaign assignment and role updates

---

## 7) Local Development Setup

### Prerequisites
- PHP `8.2+`
- Composer `2+`
- Node.js `18+` (recommended `20+`)
- npm
- MySQL `8+` (or compatible)

### Installation
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

### Run in development
Option A (single command):
```bash
composer run dev
```

Option B (separate processes):
```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

### Build frontend assets
```bash
npm run build
```

---

## 8) Docker Deployment (Optional)

Docker Compose services:

- `app` (PHP/Laravel)
- `nginx` (web server, mapped to host port `3000`)
- `db` (MySQL)

Typical flow:

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --force
```

Then open: `http://localhost:3000`

> Security note: use environment-managed secrets for production credentials.

---

## 9) Configuration Reference

Important `.env` keys:

- App: `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL`
- DB: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Queue: `QUEUE_CONNECTION`
- Session: `SESSION_DRIVER`
- Cache: `CACHE_STORE`
- Mail: `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_FROM_ADDRESS`

For production:

- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Use strong DB credentials
- Configure real mail transport
- Configure queue worker process manager (Supervisor/systemd/container)

---

## 10) Testing & Quality

### Run tests
```bash
composer test
```

or:

```bash
php artisan test
```

### Test stack
- Pest (`pestphp/pest`)
- PHPUnit configuration in `phpunit.xml`
- In-memory SQLite for tests by default

### Optional formatting/linting
```bash
./vendor/bin/pint
```

---

## 11) Suggested Production Checklist

- [ ] Set production `.env` safely (no default passwords)
- [ ] Enable HTTPS at reverse proxy/load balancer
- [ ] Run migrations in deployment pipeline
- [ ] Configure queue workers and restart policy
- [ ] Set log aggregation and monitoring
- [ ] Set backup/restore procedure for database
- [ ] Verify role access paths (`user` vs `superadmin`)

---

## 12) Project Structure Snapshot

```text
app/
	Http/
		Controllers/
		Middleware/
	Mail/
	Models/
database/
	migrations/
	seeders/
resources/
	css/
	js/
	views/
routes/
	web.php
docker/
tests/
```

---

## 13) Common Operations

### Clear/rebuild caches
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Re-run migrations from scratch (development only)
```bash
php artisan migrate:fresh --seed
```

---

## 14) Troubleshooting Guide

### `php artisan serve` fails
- Verify `.env` exists and `APP_KEY` is generated
- Check PHP version (`php -v`)
- Confirm DB connection values

### Frontend not updating
- Ensure `npm run dev` is running
- Verify Vite host/HMR config if using remote host

### 419 / CSRF issues
- Ensure session/cookie domain settings are correct
- Avoid mixing multiple app URLs during development

---

## 15) Reusable Documentation Template

A reusable standard template is included at:

- `docs/PROJECT_DOCUMENTATION_TEMPLATE.md`

Use this template for future projects by replacing placeholders and module-specific sections.

---

## 16) Ownership & Maintenance

Recommended:

- Keep this document updated whenever routes, environments, or deployment process changes.
- Update the template first when organizational documentation standards evolve.
