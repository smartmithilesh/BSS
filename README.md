# Bharat Book Depot – Management System

A complete PHP MVC web application for managing a book depot business.
Handles purchases from publishers, school sales, payments, stock, and PDF invoices.

---

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- A web server like XAMPP, WAMP, or LAMP

---

## Installation

### Step 1 – Copy Files
Place the entire `bbd` folder inside your web root:
- XAMPP: `C:\xampp\htdocs\bbd\`
- Linux:  `/var/www/html/bbd/`

### Step 2 – Create the Database
1. Open phpMyAdmin or your MySQL client
2. Run the SQL file:
   ```
   bbd/database/schema.sql
   ```
   This will create the `bharat_book_depot` database, all tables, and seed data.

### Step 3 – Configure Database
Edit `bbd/app/config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'bharat_book_depot');
define('DB_USER', 'root');
define('DB_PASS', '');          // ← your MySQL password
```

If your project folder is named differently, also update:
```php
define('PROJECT_FOLDER', 'bbd');  // ← folder name in htdocs
```

### Step 4 – Enable mod_rewrite (Apache)
Make sure `AllowOverride All` is set in your Apache config.
The `.htaccess` file in `public/` handles URL routing.

### Step 5 – Open the Application
Navigate to: `http://localhost/bbd/public/`

**Default Login:**
- Email: `admin@bharatbookdepot.com`
- Password: `admin123`

---

## Features

### Masters
- **Seasons** – Academic/financial year management with active season flag
- **Companies** – Publisher/supplier management
- **Publications** – Publication brand management
- **Classes** – School grade management
- **Schools** – Customer school management
- **Books** – Book catalog with MRP, purchase rate, sale rate, discount

### Purchases
- **New Purchase** – Dynamic book selection by company & class with inline quantity/rate/discount
- **Purchase List** – Filterable list with PDF invoice download
- **PDF Invoice** – Professional purchase invoice with totals
- Stock is automatically **increased** on purchase, **reversed** on delete

### School Sales
- **New Sale** – Dynamic book selection by class with live stock availability check
- **Sales List** – Filter by season, school, invoice no, date range
- **Invoice View** – Web view with payment history sidebar
- **PDF Invoice** – Branded invoice with bill-to, items, totals, payment history, balance due
- Stock is automatically **reduced** on sale creation

### Payments
- **Receive Payment** – Record partial or full payment from school against an invoice
- **Pay Company** – Record payments made to suppliers (cash/cheque/online/UPI)
- **Company Outstanding** – Summary of purchase vs paid vs returned per company

### Returns
- **Company Returns** – Return unsold stock to companies with dynamic book selection
- Stock is automatically **reduced** on return

### Reports
- **Stock Report** – Current stock levels by book, class, company per season with Low/Out indicators
- **Dashboard** – Season summary with purchase, sales, collection, outstanding totals

---

## Project Structure

```
bbd/
├── app/
│   ├── config/
│   │   └── config.php          ← Database & URL configuration
│   ├── core/
│   │   ├── Autoload.php        ← Class autoloader
│   │   ├── Controller.php      ← Base controller
│   │   ├── Database.php        ← PDO singleton
│   │   └── Model.php           ← Base model
│   ├── controllers/            ← 14 controllers
│   ├── models/                 ← 11 models
│   └── views/                  ← All HTML views + layout
├── database/
│   └── schema.sql              ← Complete DB schema + seed data
├── public/
│   ├── assets/                 ← CSS, JS, Font Awesome
│   ├── .htaccess               ← Apache URL routing
│   └── index.php               ← Front controller
└── vendor/
    └── fpdf/
        └── fpdf.php            ← PDF generation library
```

---

## Database Tables

| Table                 | Purpose                              |
|-----------------------|--------------------------------------|
| users                 | Admin/staff login accounts           |
| seasons               | Academic years (one active at a time)|
| companies             | Publishers / suppliers               |
| publications          | Publication brands                   |
| classes               | School grades / standards            |
| books                 | Book catalog                         |
| schools               | Customer schools                     |
| stocks                | Stock qty per season + book          |
| purchases             | Purchase order headers               |
| purchase_items        | Purchase line items                  |
| school_sales          | Sale invoice headers                 |
| school_sale_items     | Sale line items                      |
| school_payments       | Payments received from schools       |
| company_payments      | Payments made to companies           |
| company_returns       | Return note headers                  |
| company_return_items  | Return line items                    |

---

## URL Routing

All URLs follow the pattern:
```
http://localhost/bbd/public/?controller=<name>&action=<action>
```

Examples:
- `?controller=dashboard&action=index`
- `?controller=schoolsale&action=create`
- `?controller=schoolsale&action=pdf&id=5`
- `?controller=purchase&action=pdf&id=3`

---

## PDF Invoices

PDF invoices are generated server-side using the bundled FPDF library.

- **Purchase PDF** – `?controller=purchase&action=pdf&id=<id>`
- **School Sale PDF** – `?controller=schoolsale&action=pdf&id=<id>`

Both PDFs download automatically to the browser.

---

## Customisation

### Change Business Name
In `app/config/config.php`:
```php
define('BASE_NAME', 'Bharat Book Depot');
```

### Change Base URL
If hosted on a different folder or domain:
```php
define('BASE_URL', 'http://yourdomain.com/bbd/public/');
```
