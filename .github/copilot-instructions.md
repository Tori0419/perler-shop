# Copilot Instructions — Perler Shop

A Laravel 12 e-commerce demo for a perler bead shop. No database — all data is persisted to JSON files. Custom session-based authentication (not Laravel Auth).

## Commands

```bash
# Start all services concurrently (PHP server + Vite + queue + pail)
composer run dev

# Run all tests
composer run test

# Run a single test class or method
php artisan test --filter=ExampleTest

# Lint with Laravel Pint
./vendor/bin/pint

# Build frontend assets
npm run build
```

## Architecture

### Data Layer (No Database)
All persistence goes through `FileStorageService` → JSON files in `storage/app/data/`:
- `products.json` — managed by `ProductService`
- `orders.json` — managed by `OrderService`

`FileStorageService::readJson` / `writeJson` use `LOCK_EX` for writes. The data directory is configurable via `SHOP_DATA_DIR` env var (defaults to `storage/app/data`).

### Authentication
Two completely separate auth systems, both custom (no Laravel Guard/Auth):

| Session key | Middleware | Routes |
|---|---|---|
| `session('customer')` → array | `CustomerAuthenticate` | `/`, `/cart`, `/orders/history`, `/checkout` |
| `session('is_admin')` → bool | `AdminAuthenticate` | `/admin/*` |

Customer accounts are hardcoded in `config/customers.php`. Admin credentials are in `config/admin.php` (env: `ADMIN_USERNAME`, `ADMIN_PASSWORD`).

The middlewares enforce mutual exclusion: an admin session is redirected away from customer routes and vice versa.

### Service Layer
Controllers use constructor-injected services (Laravel's container resolves them):
- `ProductService` — CRUD, stock deduction, active/search filtering
- `OrderService` — create order, status transitions (`pending` → `shipped`/`cancelled`), dashboard stats
- `CustomerAuthService` — reads from `config('customers', [])`, returns public profile (no password)
- `FileStorageService` — low-level JSON read/write (injected into the other services)

### AJAX Pattern
Cart operations use vanilla `fetch`. Controllers detect AJAX with `$request->expectsJson()` / `$request->ajax()` and return JSON (`{ ok: true, ... }`) instead of a redirect. The shop page intercepts form submits, posts with `X-Requested-With: XMLHttpRequest` + `Accept: application/json` headers, and updates the nav cart count without a page reload.

### Frontend
- All global CSS lives inline in `resources/views/layouts/app.blade.php` using CSS custom properties (`--primary`, `--danger`, etc.). No separate per-component CSS files.
- Per-page JavaScript is written inline inside `@section('scripts')` blocks in each Blade view. No separate JS modules per feature.
- Tailwind CSS 4 is configured via Vite (`@tailwindcss/vite` plugin) but the primary UI uses the custom CSS in the layout.

## Key Conventions

### Cart Structure
The cart session value is a PHP array keyed by **integer product ID**:
```php
$cart[$productId] = ['product_id' => int, 'name' => string, 'price' => float, 'qty' => int, 'image' => string];
```

### Product / Order Fields
Products: `id` (int), `name`, `price` (float), `description`, `image` (URL path), `stock` (int), `status` (`'active'` | `'inactive'`).

Orders: `id` (string, format `ORD{YmdHis}{100-999}`), `user_id`, `user_name`, `address`, `contact`, `items[]`, `total` (float), `status` (`'pending'` | `'shipped'` | `'cancelled'`), `created_at`.

Only `pending` orders can transition to `shipped` or `cancelled` (`OrderService::updateStatus` enforces this).

### Stock Deduction
`ProductService::consumeStockByItems` validates and atomically deducts stock at checkout. It checks all items before writing, so a partial-stock failure rolls back the entire write.

### Image Uploads
Admin-uploaded images are stored in `public/images/uploads/products/` but served through `UploadController` at `/uploads/products/{filename}` to prevent path traversal. The route has a strict allowlist regex: `[A-Za-z0-9\-\._]+`.

### Config Files
App-specific config is split into three files:
- `config/shop.php` — data/upload directories and public URL prefix
- `config/admin.php` — admin credentials
- `config/customers.php` — hardcoded customer accounts array

### Validation Errors
Controllers use `throw ValidationException::withMessages([...])` for domain errors (stock, cart empty, etc.) in addition to standard `$request->validate()`.
