# Hotel Manager

A simple but complete hotel management system: clients, rooms, bookings, invoices, payments, and staff. Built in plain PHP 8 with SQLite — no framework, no build step, runs anywhere PHP is installed.

## Features

- **Auth & roles**: Admin and Staff accounts with role-based access.
- **Dashboard**: Live stats for room availability, active bookings, revenue and outstanding balances.
- **Rooms**: CRUD with type (single/double/suite/deluxe), nightly rate, and status (available / occupied / maintenance). Admin only.
- **Clients**: Profiles with contact + ID, full booking history, totals spent and outstanding.
- **Bookings**: Create, check-in, check-out, cancel. Auto-detects date overlaps and prevents double-booking. Updates room status.
- **Invoices**: Auto-generated per booking with 10% tax. Editable extra charges, discount, tax rate. Real-time balance and status (pending / partial / paid / cancelled).
- **Payments**: Cash / card / transfer / other. Tracks who recorded each payment. Admin can reverse payments.
- **Staff management**: Admin can create users, assign roles, deactivate accounts.
- **Reports** (admin): Monthly revenue, revenue by room type, top clients.
- CSRF protection on every mutating form, prepared statements throughout.

## Quick start

```bash
php -S 127.0.0.1:8000 -t public router.php
```

Open <http://127.0.0.1:8000>.

The SQLite database is created and seeded automatically on first request.

### Demo accounts

| Role  | Email              | Password   |
|-------|--------------------|------------|
| Admin | admin@hotel.test   | admin123   |
| Staff | staff@hotel.test   | staff123   |

To reset everything, delete `database/database.sqlite` and reload.

## Layout

```
app/
  Controllers/    Auth, Dashboard, Room, Client, Booking, Invoice, Staff
  Views/          layouts, auth, dashboard, rooms, clients, bookings, invoices, staff
  Auth.php        Session-based auth helpers
  Db.php          PDO + migrations + seed
  Router.php      Tiny pattern router
  Helpers.php     view(), e(), csrf, flash, etc.
database/
  schema.sql      Table definitions
public/
  index.php       Front controller
router.php        PHP built-in server router
```
