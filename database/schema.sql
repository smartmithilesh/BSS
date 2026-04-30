-- =============================================================
--  BHARAT BOOK DEPOT – Complete Database Schema
--  Database: bharat_book_depot
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS bharat_book_depot;
CREATE DATABASE bharat_book_depot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bharat_book_depot;

-- ─────────────────────────────────────────────
-- 1. USERS
-- ─────────────────────────────────────────────
CREATE TABLE departments (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL UNIQUE,
    description  TEXT,
    is_active    TINYINT(1) DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100)  NOT NULL,
    email        VARCHAR(100)  NOT NULL UNIQUE,
    password     VARCHAR(255)  NOT NULL,
    department_id INT NULL,
    role_id      INT NULL,
    role         VARCHAR(100) DEFAULT 'staff',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

CREATE TABLE site_settings (
    setting_key   VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NULL,
    name          VARCHAR(100) NOT NULL,
    slug          VARCHAR(100) NOT NULL UNIQUE,
    description   TEXT,
    is_active     TINYINT(1) DEFAULT 1,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

CREATE TABLE system_migrations (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    migration     VARCHAR(190) NOT NULL UNIQUE,
    batch         INT NOT NULL DEFAULT 1,
    executed_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- 2. SEASONS
-- ─────────────────────────────────────────────
CREATE TABLE seasons (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(50)   NOT NULL,
    start_year   INT           NOT NULL,
    end_year     INT           NOT NULL,
    is_active    TINYINT(1)    DEFAULT 0,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- 3. COMPANIES  (publishers / suppliers)
-- ─────────────────────────────────────────────
CREATE TABLE companies (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(150) NOT NULL,
    contact_person   VARCHAR(100),
    phone            VARCHAR(20),
    email            VARCHAR(100),
    address          TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- 4. PUBLICATIONS
-- ─────────────────────────────────────────────
CREATE TABLE publications (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(150) NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- 5. CLASSES
-- ─────────────────────────────────────────────
CREATE TABLE classes (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(50) NOT NULL,
    sort_order   INT DEFAULT 0,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- 6. BOOKS
-- ─────────────────────────────────────────────
CREATE TABLE books (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(200)    NOT NULL,
    class_id         INT             NOT NULL,
    publication_id   INT             NOT NULL,
    company_id       INT             NOT NULL,
    mrp              DECIMAL(10,2)   DEFAULT 0.00,
    purchase_rate    DECIMAL(10,2)   DEFAULT 0.00,
    sale_rate        DECIMAL(10,2)   DEFAULT 0.00,
    discount_pct     DECIMAL(5,2)    DEFAULT 0.00,
    is_active        TINYINT(1)      DEFAULT 1,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id)       REFERENCES classes(id),
    FOREIGN KEY (publication_id) REFERENCES publications(id),
    FOREIGN KEY (company_id)     REFERENCES companies(id)
);

-- ─────────────────────────────────────────────
-- 7. SCHOOLS
-- ─────────────────────────────────────────────
CREATE TABLE schools (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(200) NOT NULL,
    contact_person   VARCHAR(100),
    phone            VARCHAR(20),
    email            VARCHAR(100),
    address          TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- 8. STOCKS  (per season + book)
-- ─────────────────────────────────────────────
CREATE TABLE stocks (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    season_id    INT NOT NULL,
    book_id      INT NOT NULL,
    qty          INT DEFAULT 0,
    UNIQUE KEY uq_stock (season_id, book_id),
    FOREIGN KEY (season_id) REFERENCES seasons(id),
    FOREIGN KEY (book_id)   REFERENCES books(id)
);

-- ─────────────────────────────────────────────
-- 9. PURCHASES  (from company)
-- ─────────────────────────────────────────────
CREATE TABLE purchases (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    season_id        INT           NOT NULL,
    company_id       INT           NOT NULL,
    invoice_no       VARCHAR(60)   NOT NULL,
    purchase_date    DATE          NOT NULL,
    gross_amount     DECIMAL(12,2) DEFAULT 0.00,
    discount_amount  DECIMAL(12,2) DEFAULT 0.00,
    net_amount       DECIMAL(12,2) DEFAULT 0.00,
    notes            TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (season_id)  REFERENCES seasons(id),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- ─────────────────────────────────────────────
-- 10. PURCHASE ITEMS
-- ─────────────────────────────────────────────
CREATE TABLE purchase_items (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id      INT           NOT NULL,
    book_id          INT           NOT NULL,
    qty              INT           NOT NULL DEFAULT 0,
    rate             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_pct     DECIMAL(5,2)           DEFAULT 0.00,
    amount           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id)     REFERENCES books(id)
);

-- ─────────────────────────────────────────────
-- 11. SCHOOL SALES
-- ─────────────────────────────────────────────
CREATE TABLE school_sales (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    season_id        INT           NOT NULL,
    school_id        INT           NOT NULL,
    invoice_no       VARCHAR(60)   NOT NULL UNIQUE,
    sale_date        DATE          NOT NULL,
    gross_amount     DECIMAL(12,2) DEFAULT 0.00,
    discount_amount  DECIMAL(12,2) DEFAULT 0.00,
    net_amount       DECIMAL(12,2) DEFAULT 0.00,
    paid_amount      DECIMAL(12,2) DEFAULT 0.00,
    notes            TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (season_id) REFERENCES seasons(id),
    FOREIGN KEY (school_id) REFERENCES schools(id)
);

-- ─────────────────────────────────────────────
-- 12. SCHOOL SALE ITEMS
-- ─────────────────────────────────────────────
CREATE TABLE school_sale_items (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    sale_id      INT           NOT NULL,
    book_id      INT           NOT NULL,
    qty          INT           NOT NULL DEFAULT 0,
    rate         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_pct DECIMAL(5,2)           DEFAULT 0.00,
    amount       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (sale_id)  REFERENCES school_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id)  REFERENCES books(id)
);

-- ─────────────────────────────────────────────
-- 13. SCHOOL PAYMENTS
-- ─────────────────────────────────────────────
CREATE TABLE school_payments (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    sale_id          INT           NOT NULL,
    school_id        INT           NOT NULL,
    season_id        INT           NOT NULL,
    payment_date     DATE          NOT NULL,
    amount           DECIMAL(12,2) NOT NULL,
    payment_mode     ENUM('cash','cheque','online','upi') DEFAULT 'cash',
    reference_no     VARCHAR(100),
    notes            TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id)   REFERENCES school_sales(id),
    FOREIGN KEY (school_id) REFERENCES schools(id),
    FOREIGN KEY (season_id) REFERENCES seasons(id)
);

-- ─────────────────────────────────────────────
-- 14. COMPANY PAYMENTS  (paid to supplier)
-- ─────────────────────────────────────────────
CREATE TABLE company_payments (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    season_id        INT           NOT NULL,
    company_id       INT           NOT NULL,
    payment_date     DATE          NOT NULL,
    amount           DECIMAL(12,2) NOT NULL,
    payment_mode     ENUM('cash','cheque','online','upi') DEFAULT 'cash',
    reference_no     VARCHAR(100),
    notes            TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (season_id)  REFERENCES seasons(id),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- ─────────────────────────────────────────────
-- 15. COMPANY RETURNS  (unsold stock returned)
-- ─────────────────────────────────────────────
CREATE TABLE company_returns (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    season_id        INT           NOT NULL,
    company_id       INT           NOT NULL,
    return_date      DATE          NOT NULL,
    reference_no     VARCHAR(60),
    total_amount     DECIMAL(12,2) DEFAULT 0.00,
    notes            TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (season_id)  REFERENCES seasons(id),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

-- ─────────────────────────────────────────────
-- 16. COMPANY RETURN ITEMS
-- ─────────────────────────────────────────────
CREATE TABLE company_return_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    return_id   INT           NOT NULL,
    book_id     INT           NOT NULL,
    qty         INT           NOT NULL DEFAULT 0,
    rate        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    amount      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (return_id) REFERENCES company_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id)   REFERENCES books(id)
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
-- SEED DATA
-- =============================================================

-- Default admin user  (password: admin123)
INSERT INTO departments (name, description, is_active) VALUES
('Super Admin', 'Full system ownership and configuration', 1),
('Administration', 'System administration and management', 1),
('Sales', 'School sales and payment follow-up', 1),
('Purchase', 'Supplier purchase and payment work', 1),
('IT', 'Information technology and system maintenance', 1);

INSERT INTO roles (department_id, name, slug, description, is_active) VALUES
(1, 'Super Admin', 'superadmin', 'Full system access', 1),
(2, 'Admin', 'admin', 'Administration access', 1),
(2, 'Staff', 'staff', 'Standard staff access', 1),
(5, 'IT', 'it', 'IT department access', 1);

INSERT INTO users (name, email, password, department_id, role_id, role) VALUES
('Admin', 'admin@bharatbookdepot.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, 'superadmin');

INSERT INTO site_settings (setting_key, setting_value) VALUES
('shop_name', 'Bharat Book Depot'),
('tagline', 'Book Depot Management System'),
('brand_logo', ''),
('phone', ''),
('email', ''),
('address', ''),
('currency_symbol', '₹'),
('purchase_prefix', 'PUR'),
('sale_prefix', 'INV'),
('invoice_footer', 'Thank you for your business!'),
('timezone', 'Asia/Kolkata');

-- Seasons
INSERT INTO seasons (name, start_year, end_year, is_active) VALUES
('2024-25', 2024, 2025, 0),
('2025-26', 2025, 2026, 1);

-- Classes
INSERT INTO classes (name, sort_order) VALUES
('Nursery', 1), ('LKG', 2), ('UKG', 3),
('Class 1', 4), ('Class 2', 5), ('Class 3', 6),
('Class 4', 7), ('Class 5', 8), ('Class 6', 9),
('Class 7', 10), ('Class 8', 11), ('Class 9', 12), ('Class 10', 13);

-- Publications
INSERT INTO publications (name) VALUES
('Oxford University Press'), ('S. Chand Publishing'),
('NCERT'), ('Ratna Sagar'), ('Evergreen Publications');

-- Companies
INSERT INTO companies (name, contact_person, phone, email, address) VALUES
('Oxford Press Ltd', 'Ramesh Kumar', '9876543210', 'ramesh@oxford.com', 'Delhi, India'),
('S. Chand & Co.', 'Suresh Sharma', '9812345678', 'suresh@schand.com', 'New Delhi, India');

-- Schools
INSERT INTO schools (name, contact_person, phone, email, address) VALUES
('Delhi Public School', 'Mrs. Anita Singh', '9911223344', 'dps@gmail.com', 'Sector 12, Delhi'),
('St. Mary Convent', 'Fr. Thomas', '9922334455', 'stmary@gmail.com', 'Church Road, Agra'),
('Kendriya Vidyalaya', 'Mr. Rakesh Gupta', '9933445566', 'kv@gov.in', 'Gandhi Nagar, Jaipur');
