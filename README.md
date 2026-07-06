# HS CAR RENTAL - Premium Car Rental Web Application

A complete, production-ready car rental web application with customer booking, UPI/COD payments, KYC verification, user dashboard, admin panel, CMS and real-time notifications.

## Features

### Frontend
- Responsive, mobile-friendly luxury black-white-gold design
- Home page with hero banner, search, featured/popular/latest cars, reviews, about, contact, FAQs
- Car listing with filters (brand, fuel, transmission, seating, price)
- Car details with image gallery, specifications, features, pricing and availability calendar
- Booking form with rental calculation and mandatory 2-person KYC
- Payment page with dynamic UPI QR code and COD option
- Booking confirmation with receipt download/print
- User registration, login, forgot password, profile management
- User dashboard with booking history, active/completed/cancelled bookings, payment history

### Admin Panel
- Secure admin login with role-based access
- Dashboard with statistics (total cars, bookings, revenue, pending approvals, active rentals, users)
- Car management: add, edit, delete, enable/disable cars
- Booking management: view, approve, reject, cancel, update statuses
- Payment management: manage UPI ID, QR code, COD, verify payments
- Review management: approve, hide, delete customer reviews
- Contact messages management
- CMS: edit website name, hero content, about, contact, footer, policies, colors
- Website settings: address, contact numbers, social links, Google Maps
- Real-time notifications for new bookings, payments and contacts

### Security
- CSRF protection
- Input sanitization and validation
- Password hashing (Bcrypt)
- XSS protection via output escaping
- SQL injection protection via prepared statements
- Role-based access control

## Delivered Source Code

This repository contains two implementations:

1. **React Demo (`src/` + deployed Vercel app)**
   - Fully functional frontend demo using Vite + React + TypeScript + Tailwind CSS
   - Uses browser localStorage for state persistence
   - Demonstrates the complete user journey and admin workflow

2. **PHP/MySQL Backend (`php/` + `database/schema.sql`)**
   - Production-ready PHP backend source code
   - MySQL database schema with all required tables
   - REST API endpoints and Bootstrap-based admin panel
   - Intended for deployment on a PHP/MySQL hosting environment

## Database Setup

1. Create a MySQL database named `hs_car_rental`.
2. Import `database/schema.sql`.
3. Update `php/config.php` with your database credentials.

Default admin credentials:  
**Email:** admin@hscarrental.com  
**Password:** password

## PHP Deployment

1. Upload `php/` folder contents to your web server (e.g., `public_html/`).
2. Ensure PHP 7.4+ and MySQL 5.7+ are available.
3. Set `BASE_URL` in `php/config.php` to your domain.
4. Create `assets/uploads/` directory with write permissions for file uploads.

## React Demo Deployment

The React demo is built with Vite and deploys as a static site.

```bash
npm install
npm run build
```

The `dist/` folder can be deployed to any static hosting service.

## Currency

All prices are displayed in Indian Rupees (₹ INR).

## Tech Stack

- Frontend: Vite, React 19, TypeScript, Tailwind CSS v4, Framer Motion, Lucide React
- Demo State: localStorage
- Backend Source: PHP 7.4+, MySQL, PDO, Bootstrap 5
- Currency: INR (₹)

## Branding

- Website Name: HS CAR RENTAL
- Theme: Premium Luxury Black-White-Gold
- Logo/Favicon: Custom HS car logo (`public/favicon.svg`)

---

For production deployment of the PHP/MySQL version, configure your web server, enable HTTPS, set up mail server for notifications, and update all configuration values in `php/config.php`.
