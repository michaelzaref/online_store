# BegoStore — Luxury Watch E-Commerce Store

A full-featured Laravel + MySQL ecommerce platform for luxury watches, built with Filament admin panel.

## Tech Stack

- **Backend:** Laravel 13, PHP 8.3+
- **Database:** MySQL 8
- **Admin Panel:** Filament v3
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Auth:** Laravel Breeze
- **Payments:** Cash on Delivery only
- **SEO:** Spatie Sitemap, meta tags, robots.txt

## Features

### Customer (Frontend)
- Home page with hero, featured, new arrivals, best sellers, limited edition, brands, testimonials
- Product catalog with grid/list view, sorting, pagination
- Advanced filters (brand, price, gender, watch type, materials, color, stock)
- Product details with image gallery, zoom, specs, reviews, related products
- Autocomplete search (brand, model, SKU)
- Shopping cart with coupon support
- Guest & user checkout (cash on delivery)
- Wishlist, watch comparison, recently viewed
- User account (orders, tracking, addresses, loyalty points)
- Newsletter subscription, contact form

### Admin Panel (`/admin`)
- Product CRUD with watch-specific fields & image management
- Order management with manual delivery & status updates
- Brand, category, coupon, shipping method management
- Review moderation (approve/reject)
- Customer profiles with purchase history
- Analytics dashboard (revenue, orders, top products)
- Banner & testimonial management

### Security
- CSRF protection, SQL injection protection (Eloquent), XSS protection (Blade escaping)
- Rate limiting on auth routes
- Admin-only panel access
- Password hashing (bcrypt)

## Quick Start

### 1. Install Dependencies

```bash
composer install
npm install && npm run build
```

### 2. Start Local MySQL + App

This project runs its own MySQL server (no Docker, no sudo) on port **3309**:

```bash
bash scripts/start.sh
```

Or step by step:

```bash
bash scripts/mysql-start.sh   # start MySQL
php artisan migrate --seed    # first time only
php artisan serve --port=8002
```

### 3. Database Credentials (`.env`)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3309
DB_DATABASE=watch_store
DB_USERNAME=watchstore
DB_PASSWORD=watchstore
```

### Default Accounts

| Role     | Email                  | Password  |
|----------|------------------------|-----------|
| Admin    | admin@begostore.com    | password  |
| Customer | customer@example.com   | password  |

## Managing Orders & Delivery

All orders use **Cash on Delivery**. There are no online payment gateways.

### Admin workflow (`/admin` → Orders)

1. **Pending** — New order received
2. **Processing** — Preparing the order for delivery
3. **Shipped** — Out for delivery (add delivery notes or reference if needed)
4. **Delivered** — Order delivered; payment is automatically marked as **paid**

Use **Update Status** on any order to change status and add delivery notes (courier name, driver, pickup details, etc.).

Delivery fees are set under **Settings → Shipping Methods** in the admin panel.

## Project Structure

```
app/
├── Filament/          # Admin panel resources & widgets
├── Http/Controllers/  # Storefront controllers
├── Models/            # Eloquent models
└── Services/          # Cart, Checkout, Payment, Product services
resources/views/
├── layouts/store.blade.php
├── store/             # Storefront pages
└── components/        # Reusable Blade components
database/
├── migrations/        # Full ecommerce schema
└── seeders/           # Sample watches, brands, coupons
```

## Roadmap (Future Features)

- [ ] Abandoned cart emails
- [ ] AI product recommendations
- [ ] Multi-vendor marketplace
- [ ] Live chat / ticket system
- [ ] JWT API for mobile app

## License

MIT
