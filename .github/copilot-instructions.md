# Bharat Book Depot – AI Coding Instructions

## Project Overview
**Bharat Book Depot** is a PHP MVC web application for managing a book distribution business. It handles publisher purchases, school sales, inventory management, and payment tracking with automatic PDF invoice generation.

**Tech Stack:** PHP 7.4+, MySQL 5.7+, FPDF, jQuery, Bootstrap  
**Architecture:** MVC with PDO database layer, object-oriented models, transaction support

---

## Critical Architecture Patterns

### MVC Framework
- **Entry Point:** [public/index.php](public/index.php) – URL router mapping slug → Controller class
- **Controllers:** [app/controllers/](app/controllers/) – Extend `Controller` base class with `$this->view()` and `$this->redirect()`
- **Models:** [app/models/](app/models/) – Extend `Model` base class with automatic `$this->db` PDO connection
- **Views:** [app/views/](app/views/) – PHP templates with auto-injected variables via `extract($data)`
- **Core:** [app/core/](app/core/) – `Database` (PDO singleton), `Controller` (routing/templating), `Model` (base ORM methods)

### Routing Convention
URL slugs in [public/index.php](public/index.php) map to controller class names:
- `?controller=school&action=create` → `SchoolController->create()`
- Multi-word slugs use camelCase: `schoolsale` → `SchoolsaleController`
- Always sanitize controller/action names with `preg_replace('/[^a-z]/i', '')`

### Database Layer
- **Connection:** PDO singleton via `Database::connect()` in [app/core/Database.php](app/core/Database.php)
- **Transactions:** Models use `$this->beginTransaction()`, `$this->commit()`, `$this->rollBack()` for multi-step operations
- **Prepared Statements:** Always use parameterized queries `$this->db->prepare("... ?...")` to prevent SQL injection
- **Configuration:** [app/config/config.php](app/config/config.php) defines `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `PROJECT_FOLDER`, `BASE_URL`

---

## Data Flow & Entity Relationships

### Core Business Entities
1. **Masters** (reference data):
   - `seasons` – Academic year with `is_active` flag (only one active per time)
   - `companies` – Publishers/suppliers
   - `publications` – Publication brands
   - `classes` – School grades with `sort_order` for ordering
   - `schools` – Customer schools
   - `books` – Catalog with MRP, purchase_rate, sale_rate, discount_pct

2. **Transactions**:
   - `purchases` + `purchase_items` – Supplier invoices with item-level details
   - `school_sales` + `school_sale_items` – Customer invoices
   - `payments` – Payment records (school payments, company payments)
   - `company_returns` + `company_return_items` – Returns to suppliers

3. **Inventory**:
   - `stocks` – Ledger of `qty` per season/book (composite key: `season_id`, `book_id`)

### Stock Movement Logic
**Critical:** Stock is automatically managed via Model methods:
- **Purchase created** → `Stock::add()` increases qty ([app/models/Stock.php](app/models/Stock.php))
- **Purchase deleted** → `Stock::reduce()` decreases qty (via transaction rollback in Purchase model)
- **School sale created** → `Stock::reduce()` decreases qty ([app/models/SchoolSale.php](app/models/SchoolSale.php))
- **Company return created** → `Stock::reduce()` decreases qty ([app/models/CompanyReturn.php](app/models/CompanyReturn.php))

**Always wrap stock operations in transactions** to maintain consistency. Example pattern:
```php
public function create($data) {
    $this->beginTransaction();
    try {
        // 1. Validate & create record
        // 2. Update stock
        $stock = new Stock();
        $stock->add($data['season_id'], $book_id, $qty);
        $this->commit();
    } catch(Exception $e) {
        $this->rollBack();
        throw $e;
    }
}
```

---

## Project-Specific Conventions

### Data Flow Pattern (Controllers)
Controllers follow this standard pattern:
1. **index()** – List view with pagination & filters
2. **create()** – Form rendering with dropdown data
3. **store()** – Form processing (POST), validation, redirect to show/list
4. **show()/view()** – Single record display
5. **pdf()** – PDF generation (uses FPDF)

Example: [app/controllers/PurchaseController.php](app/controllers/PurchaseController.php)

### Filter Convention
List queries accept `$filters` array with optional keys (filters are null-safe):
- `season_id`, `company_id`, `school_id`, `invoice_no`, `from_date`, `to_date`
- SQL uses `WHERE 1=1` pattern for dynamic condition building
- See [app/models/Purchase.php](app/models/Purchase.php) `getAll()` method

### PDF Generation
- **Library:** FPDF (`vendor/fpdf/fpdf.php`)
- **Pattern:** Controllers pass data to private `generatePdf()` method
- **Example:** [app/controllers/PurchaseController.php](app/controllers/PurchaseController.php) lines 59-96
- Uses cell-based layout: `$pdf->Cell(width, height, text, border, newline, align)`

### View Data Injection
- Controller passes associative array to `$this->view('path/to/view', $data)`
- Variables auto-extracted: `extract($data)` makes array keys available as variables
- Template files: [app/views/layout/header.php](app/views/layout/header.php) & [footer.php](app/views/layout/footer.php) auto-included

### Session & Authentication
- `$_SESSION['user']` must exist after login; checked by `$this->authCheck()`
- Flash messages: `$this->flash('type', 'msg')` sets `$_SESSION['flash']`
- Helper file: [app/views/layout/helpers.php](app/views/layout/helpers.php) provides display utilities

---

## Common Workflows & Commands

### Local Development Setup
```bash
# 1. Copy to web root
cp -r bbd /opt/lampp/htdocs/

# 2. Import database schema
mysql -u root -p < database/schema.sql

# 3. Update config (if needed)
# Edit app/config/config.php - DB_PASS, PROJECT_FOLDER, BASE_URL

# 4. Start Apache/MySQL
# (XAMPP: use control panel; or systemctl start apache2 mysql)

# 5. Access application
# http://localhost/bbd/public/
# Login: admin@bharatbookdepot.com / admin123
```

### Adding a New Feature (e.g., New Master Entity)
1. **Create model** in [app/models/YourModel.php](app/models/)
   - Extend `Model`
   - Implement `getAll()`, `find()`, `create()`, `update()`, `delete()`
2. **Create controller** in [app/controllers/YourController.php](app/controllers/) with index/create/store/edit/update/delete actions
3. **Add route mapping** in [public/index.php](public/index.php)
4. **Create views** in [app/views/yourcontroller/](app/views/)
5. **Add navigation link** in [app/views/layout/leftnav.php](app/views/layout/leftnav.php)
6. **Create table schema** in [database/schema.sql](database/schema.sql)

### Modifying Stock Logic
**CRITICAL:** Always update transaction-based tests when modifying Stock operations:
- Locate the trigger point (e.g., `create()` method in model)
- Use `Stock` model methods only: `add()`, `reduce()`, `getQty()`
- Wrap in transaction: `beginTransaction()` → operate → `commit()` or rollback
- Test with: create → delete → verify stock returns to original

---

## Key File Reference

| File | Purpose |
|------|---------|
| [public/index.php](public/index.php) | Router & controller dispatcher |
| [app/config/config.php](app/config/config.php) | Database & site configuration |
| [app/core/Database.php](app/core/Database.php) | PDO singleton connection |
| [app/core/Controller.php](app/core/Controller.php) | Base controller with view/redirect/auth |
| [app/core/Model.php](app/core/Model.php) | Base model with transaction methods |
| [app/models/Stock.php](app/models/Stock.php) | Inventory ledger (critical for accuracy) |
| [app/views/layout/header.php](app/views/layout/header.php) | Page header template |
| [app/views/layout/leftnav.php](app/views/layout/leftnav.php) | Navigation menu |
| [database/schema.sql](database/schema.sql) | Database structure & sample data |

---

## Known Limitations & Gotchas

- **Session state:** No session middleware; relies on `$_SESSION` global
- **No validation layer:** Validation happens in models; no dedicated validators
- **Active season:** Only one season can be `is_active=1` at a time (enforced in UI, not DB)
- **Stock negative:** No constraint preventing negative stock; validate qty before reduce
- **Timezone:** Application uses server timezone; no timezone handling for dates
- **PDF paths:** FPDF requires absolute paths; use `__DIR__` for relative references

---

## Testing & Debugging Tips

- **Break in controller:** Set `var_dump()` before `$this->view()` to inspect data
- **Check SQL:** Enable PDO error mode to catch query issues (already set in Database.php)
- **Stock audit:** Query `stocks` table directly to verify qty after transactions
- **Route issues:** Verify slug→class mapping in [public/index.php](public/index.php)
- **View variables:** Check `extract($data)` by adding `var_dump(get_defined_vars())` in view template
