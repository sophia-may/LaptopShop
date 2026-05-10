CREATE DATABASE IF NOT EXISTS laptopshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE laptopshop;

-- =============================================
-- SYSTEM & SETTINGS
-- =============================================
CREATE TABLE IF NOT EXISTS site_settings (
    `key`      VARCHAR(100) PRIMARY KEY,
    `value`    TEXT         NOT NULL,
    updated_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- USERS & MEMBERSHIPS (Class Table Inheritance)
-- Role is determined by which child table (admins/members) the user belongs to.
-- PHP login logic: after finding users row, do:
--   SELECT 1 FROM admins WHERE user_id = ? -> admin
--   SELECT 1 FROM members WHERE user_id = ? -> member
-- =============================================
CREATE TABLE IF NOT EXISTS membership_tiers (
    id               INT           AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(50)   NOT NULL,           -- S-New, S-Student, S-Mem, S-Vip
    min_points       INT           NOT NULL DEFAULT 0, -- Diem toi thieu de dat hang
    discount_percent DECIMAL(5,2)  NOT NULL DEFAULT 0.00 -- Giam gia dac quyen
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id            INT          AUTO_INCREMENT PRIMARY KEY,
    fullname      VARCHAR(100) NOT NULL,
    email         VARCHAR(100) NOT NULL UNIQUE,
    phone         VARCHAR(20),
    -- ERD typo "password_harsh" is corrected here
    password_hash VARCHAR(255) NOT NULL,
    -- [ADDED] spec: "Thay doi hinh anh dai dien"
    avatar_url    VARCHAR(255) DEFAULT NULL,
    -- [ADDED] spec: admin "Cam/khoa thanh vien". Kept on users (not members)
    -- so the login query WHERE is_active = 1 stays a single fast lookup.
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Specialisation: admin accounts
CREATE TABLE IF NOT EXISTS admins (
    user_id INT PRIMARY KEY,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Specialisation: member accounts
-- ERD attributes: points (Diem tich luy hien tai), FK tier
CREATE TABLE IF NOT EXISTS members (
    user_id INT PRIMARY KEY,
    tier_id INT DEFAULT NULL,
    points  INT NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tier_id) REFERENCES membership_tiers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- PRODUCTS CATALOG
-- (categories & brands are not in ERD but required by business logic)
-- =============================================
CREATE TABLE IF NOT EXISTS categories (
    id          INT          AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    is_featured TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS brands (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    slug       VARCHAR(100) NOT NULL UNIQUE,
    logo_url   VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ERD PRODUCTS attributes: id, name, slug, short_description, detail_description
CREATE TABLE IF NOT EXISTS products (
    id                 INT          AUTO_INCREMENT PRIMARY KEY,
    category_id        INT          DEFAULT NULL,
    brand_id           INT          DEFAULT NULL,
    name               VARCHAR(255) NOT NULL,
    slug               VARCHAR(255) NOT NULL UNIQUE,
    short_description  TEXT,
    detail_description LONGTEXT,
    is_featured        TINYINT(1)   NOT NULL DEFAULT 0,
    created_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id)    REFERENCES brands(id)     ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ERD: PRODUCT_VARIANTS is a weak entity (double-border rectangle in ERD)
-- Attributes: id, sku_code, ram, color, storage, quantity, base_price, img_url
CREATE TABLE IF NOT EXISTS product_variants (
    id         INT            AUTO_INCREMENT PRIMARY KEY,
    product_id INT            NOT NULL,
    sku_code   VARCHAR(50)    NOT NULL UNIQUE,
    ram        VARCHAR(50),
    color      VARCHAR(50),
    storage    VARCHAR(50),
    quantity   INT            NOT NULL DEFAULT 0,
    base_price DECIMAL(15,2)  NOT NULL,
    img_url    VARCHAR(255)   DEFAULT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SHOPPING & ORDERS
-- Only members can have carts/orders -> FK to members(user_id)
-- =============================================
CREATE TABLE IF NOT EXISTS carts (
    id         INT       AUTO_INCREMENT PRIMARY KEY,
    user_id    INT       NOT NULL UNIQUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES members(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cart_items (
    cart_id    INT           NOT NULL,
    variant_id INT           NOT NULL,
    quantity   INT           NOT NULL DEFAULT 1,
    unit_price DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (cart_id, variant_id),
    FOREIGN KEY (cart_id)    REFERENCES carts(id)            ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ERD: MEMBERS "Implements" ORDERS (1:N)
-- Attributes: id, order_code, total_amount, discount_amount, final_amount,
--             payment_method, payment_status, status
CREATE TABLE IF NOT EXISTS orders (
    id               INT           AUTO_INCREMENT PRIMARY KEY,
    user_id          INT           DEFAULT NULL,
    order_code       VARCHAR(20)   NOT NULL UNIQUE,
    -- [ADDED] spec: checkout flow requires a delivery address
    shipping_address TEXT          NOT NULL,
    total_amount     DECIMAL(15,2) NOT NULL,
    discount_amount  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    final_amount     DECIMAL(15,2) NOT NULL,
    -- ERD note: 'cod', 'credit_card'
    payment_method   ENUM('cod','credit_card')                                     NOT NULL DEFAULT 'cod',
    -- ERD note: 'unpaid', 'paid', 'refunded'
    payment_status   ENUM('unpaid','paid','refunded')                              NOT NULL DEFAULT 'unpaid',
    -- ERD note: 'pending', 'confirmed', 'shipping', 'completed', 'canceled'
    status           ENUM('pending','confirmed','shipping','completed','canceled')  NOT NULL DEFAULT 'pending',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES members(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ERD: "Has ORDER ITEMS" relationship
-- Attributes: quantity, unit_price (Gia luc mua), total_price (= unit_price * quantity)
-- FIX: Original schema had variant_id INT NULL as part of composite PK which MySQL
-- rejects because PK columns cannot be NULL. Solution: surrogate PK + UNIQUE KEY.
CREATE TABLE IF NOT EXISTS order_items (
    id          INT            AUTO_INCREMENT PRIMARY KEY,
    order_id    INT            NOT NULL,
    variant_id  INT            DEFAULT NULL,
    quantity    INT            NOT NULL,
    unit_price  DECIMAL(15,2)  NOT NULL,
    total_price DECIMAL(15,2)  NOT NULL,
    UNIQUE KEY uq_order_variant (order_id, variant_id),
    FOREIGN KEY (order_id)   REFERENCES orders(id)           ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS coupons (
    id               INT           AUTO_INCREMENT PRIMARY KEY,
    code             VARCHAR(50)   NOT NULL UNIQUE,
    discount_percent DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
    is_active        TINYINT(1)    NOT NULL DEFAULT 1,
    description      VARCHAR(255)  DEFAULT NULL,
    created_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- CONTENT
-- =============================================
-- ERD: ADMINS "Post" ARTICLES (1:N)
-- Attributes: id, title, slug, content, published_at
CREATE TABLE IF NOT EXISTS articles (
    id               INT          AUTO_INCREMENT PRIMARY KEY,
    admin_id         INT          DEFAULT NULL,
    title            VARCHAR(255) NOT NULL,
    slug             VARCHAR(255) NOT NULL UNIQUE,
    content          LONGTEXT     NOT NULL,
    -- [ADDED] spec: "Quan ly tu khoa, mo ta, tieu de bai viet" (SEO)
    meta_title       VARCHAR(255) DEFAULT NULL,
    meta_description VARCHAR(500) DEFAULT NULL,
    meta_keywords    VARCHAR(255) DEFAULT NULL,
    thumbnail_url    VARCHAR(255) DEFAULT NULL,
    created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    published_at     TIMESTAMP    NULL DEFAULT NULL,
    FOREIGN KEY (admin_id) REFERENCES admins(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ERD: COMMENT relationship - MEMBERS write N comments on ARTICLES
-- Attributes from ERD: content, created_at
-- [ADDED] status: spec "Quan ly binh luan danh gia cua thanh vien"
CREATE TABLE IF NOT EXISTS article_comments (
    id         INT       AUTO_INCREMENT PRIMARY KEY,
    article_id INT       NOT NULL,
    user_id    INT       NOT NULL,
    content    TEXT      NOT NULL,
    status     ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id)     ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES members(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- [ADDED] spec task #2: Trang Hoi/Dap - admin CRUD
-- Not in ERD but explicitly required by specification section #2.
CREATE TABLE IF NOT EXISTS faqs (
    id         INT        AUTO_INCREMENT PRIMARY KEY,
    question   TEXT       NOT NULL,
    answer     TEXT       NOT NULL,
    sort_order INT        NOT NULL DEFAULT 0,
    is_active  TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- REVIEWS
-- ERD: MEMBERS "Post" -> PRODUCTS (the "Post" diamond in ERD connects MEMBERS to PRODUCTS)
-- Attributes: id, rating, comment, status ('pending', 'approved', 'reject')
-- [ADDED] UNIQUE(user_id, product_id): one review per user per product
-- =============================================
CREATE TABLE IF NOT EXISTS reviews (
    id         INT       AUTO_INCREMENT PRIMARY KEY,
    user_id    INT       NOT NULL,
    product_id INT       NOT NULL,
    rating     TINYINT   NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment    TEXT,
    status     ENUM('pending','approved','reject') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_product (user_id, product_id),
    FOREIGN KEY (user_id)    REFERENCES members(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- CONTACTS
-- Not in ERD - added per spec: "Quan ly cac lien he cua khach hang"
-- =============================================
CREATE TABLE IF NOT EXISTS contacts (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    customer_name  VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    subject        VARCHAR(255) NOT NULL,
    message        TEXT         NOT NULL,
    status         ENUM('unread','read','replied') NOT NULL DEFAULT 'unread',
    created_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
