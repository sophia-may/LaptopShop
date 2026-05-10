USE laptopshop;

-- =============================================
-- Seed Settings
-- =============================================
INSERT INTO site_settings (`key`, `value`) VALUES
('company_name',        'LaptopShop VN'),
('phone',               '1900 1234'),
('email',               'contact@laptopshop.vn'),
('address',             '123 Duong Cong Nghe, Quan 1, TP.HCM'),
('homepage_intro_title','Ve LaptopShop'),
('homepage_intro_text', 'Chung toi chuyen cung cap cac dong laptop chinh hang, gia canh tranh voi dich vu hau mai tot nhat thi truong.'),
('social_facebook',     'https://facebook.com/laptopshop'),
('social_youtube',      'https://youtube.com/laptopshop'),
('homepage_banner_1',   '/uploads/settings/banner1.jpg'),
('homepage_banner_2',   '/uploads/settings/banner2.jpg');

-- =============================================
-- Seed Membership Tiers
-- =============================================
INSERT INTO membership_tiers (id, name, min_points, discount_percent) VALUES
(1, 'S-New',     0,    0.00),
(2, 'S-Student', 100,  2.00),
(3, 'S-Mem',     500,  3.00),
(4, 'S-Vip',     2000, 5.00);

-- =============================================
-- Seed Coupons
INSERT INTO coupons (code, discount_percent, is_active, description) VALUES
('LAPTOP10', 10.00, 1, 'Giảm giá 10% cho đơn hàng.'),
('FREESHIP', 0.00, 1, 'Mã dùng thử, không áp dụng giảm giá nhưng có thể dùng để demo.'),
('SUMMER20', 20.00, 1, 'Giảm giá 20% cho mùa hè.'),
('WELCOME5', 5.00, 1, 'Giảm giá 5% cho khách hàng mới.'),
('BLACKFRIDAY', 30.00, 1, 'Giảm giá 30% ngày Black Friday.');

-- =============================================
-- Seed Users, Admins, Members
-- All passwords = Admin@123
-- Hash generated with PHP password_hash('Admin@123', PASSWORD_BCRYPT)
-- =============================================
-- avatar_url and is_active are now part of users table
INSERT INTO users (id, fullname, email, phone, password_hash, avatar_url, is_active) VALUES
(1, 'Super Admin',  'admin@laptop.vn', '0901234567',
    '$2y$10$nGmmkhAr/YzdE25rN6NjGOOX9.SP0VcsdOrHudf3syYuXpmXvS11e', NULL, 1),
(2, 'Nguyen Van A', 'nva@gmail.com',   '0912345678',
    '$2y$10$nGmmkhAr/YzdE25rN6NjGOOX9.SP0VcsdOrHudf3syYuXpmXvS11e', NULL, 1),
(3, 'Tran Thi B',   'ttb@gmail.com',   '0923456789',
    '$2y$10$nGmmkhAr/YzdE25rN6NjGOOX9.SP0VcsdOrHudf3syYuXpmXvS11e', NULL, 1),
-- User 4 is banned (is_active = 0) to demo the ban feature
(4, 'Le Van C',     'lvc@gmail.com',   '0934567890',
    '$2y$10$nGmmkhAr/YzdE25rN6NjGOOX9.SP0VcsdOrHudf3syYuXpmXvS11e', NULL, 0);

INSERT INTO admins (user_id) VALUES (1);

INSERT INTO members (user_id, tier_id, points) VALUES
(2, 1,  10),  -- S-New
(3, 2, 150),  -- S-Student
(4, 3, 620);  -- S-Mem (account is banned via users.is_active = 0)

-- =============================================
-- Seed Categories & Brands
-- =============================================
INSERT INTO categories (id, name, slug, is_featured) VALUES
(1, 'Laptop Gaming',    'laptop-gaming',    1),
(2, 'Laptop Van Phong', 'laptop-van-phong', 1),
(3, 'Laptop Do Hoa',    'laptop-do-hoa',    0),
(4, 'MacBook',          'macbook',          1);

INSERT INTO brands (id, name, slug) VALUES
(1, 'Asus',     'asus'),
(2, 'Dell',     'dell'),
(3, 'HP',       'hp'),
(4, 'Apple',    'apple'),
(5, 'Acer',     'acer'),
(6, 'Lenovo',   'lenovo'),
(7, 'MSI',      'msi'),
(8, 'Gigabyte', 'gigabyte');

-- =============================================
-- Seed Products & Variants (40 products across 4 categories)
-- =============================================
INSERT INTO products (id, category_id, brand_id, name, slug, short_description, is_featured) VALUES
-- CATEGORY 1: Gaming (IDs 1-10)
(1,  1, 1, 'Asus ROG Strix G15',      'asus-rog-strix-g15',      'Laptop gaming sieu manh voi RTX 4070',           1),
(2,  1, 1, 'Asus ROG Zephyrus G14',   'asus-rog-zephyrus-g14',   'Laptop gaming nhe nhat voi RTX 4060',            1),
(3,  1, 7, 'MSI GE76 Raider',         'msi-ge76-raider',         'Laptop gaming ton dung voi man 4K 120Hz',        0),
(4,  1, 7, 'MSI GS66 Stealth',        'msi-gs66-stealth',        'Laptop gaming sieu mong nhe nhat MSI',           1),
(5,  1, 2, 'Dell Alienware m15',      'dell-alienware-m15',      'Gaming beast voi cau hinh khung',                1),
(6,  1, 2, 'Dell Alienware x15',      'dell-alienware-x15',      'Gaming 4K 240Hz cho e-sports pro',               0),
(7,  1, 8, 'Gigabyte Aorus 15',       'gigabyte-aorus-15',       'Gaming canh tranh voi gia tot',                  0),
(8,  1, 8, 'Gigabyte Aorus 17 XE',    'gigabyte-aorus-17-xe',    'Man hinh cong tron dung cho game immersive',     1),
(9,  1, 6, 'Lenovo Legion 7',         'lenovo-legion-7',         'Chien binh gaming tot nhat nam',                 1),
(10, 1, 5, 'Acer Nitro 5',            'acer-nitro-5',            'Gaming entry level voi gia canh tranh',          1),
-- CATEGORY 2: Office (IDs 11-20)
(11, 2, 3, 'HP Pavilion 15',          'hp-pavilion-15',          'Laptop van phong cam nhan sang tao',             1),
(12, 2, 3, 'HP Pavilion 16',          'hp-pavilion-16',          'Man 4K 120Hz cho cong viec sang tao',            0),
(13, 2, 6, 'Lenovo ThinkPad X1 Carbon','lenovo-thinkpad-x1',      'Laptop doanh nhan hang dau thi truong',          1),
(14, 2, 6, 'Lenovo ThinkPad T14 Gen4', 'lenovo-thinkpad-t14-g4',   'Laptop van phong sieu ben bi',                   1),
(15, 2, 1, 'Asus VivoBook 15',        'asus-vivobook-15',        'Laptop tinh te voi pin 12 gio',                  0),
(16, 2, 1, 'Asus VivoBook 14 OLED',   'asus-vivobook-14-oled',   'Man OLED sac nef cho sinh vien',                 1),
(17, 2, 5, 'Acer Aspire 5',           'acer-aspire-5',           'Laptop vuong dung va ty le gia tot',             1),
(18, 2, 5, 'Acer Aspire 7',           'acer-aspire-7',           'Tam trung gia tot voi CPU Ryzen',                0),
(19, 2, 2, 'Dell Vostro 15',          'dell-vostro-15',          'Laptop van phong doanh nhan',                    1),
(20, 2, 2, 'Dell Vostro 14',          'dell-vostro-14',          'Nhe nha de di lai cho nhan vien linh hoat',      0),
-- CATEGORY 3: Design/Graphics (IDs 21-30)
(21, 3, 2, 'Dell Precision 15',       'dell-precision-15',       'Workstation do hoa 4K HDR tinh xac',             1),
(22, 3, 2, 'Dell Precision 17',       'dell-precision-17',       'Workstation khung cau hinh co nhan',             1),
(23, 3, 3, 'HP ZBook 15',             'hp-zbook-15',             'Workstation suc manh cho do hoa',                0),
(24, 3, 3, 'HP ZBook Create 16',      'hp-zbook-create-16',      'Workstation sac nef voi color accuracy 100%',    1),
(25, 3, 1, 'Asus ProArt StudioBook',  'asus-proart-studiobook',   'Laptop sang tao cao cap cho nha thiet ke',        1),
(26, 3, 1, 'Asus ProArt Colorbook',   'asus-proart-colorbook',   'Man hoa my thuat voi touchscreen cam ung',        0),
(27, 3, 6, 'Lenovo Yoga 7i',          'lenovo-yoga-7i',          '2-in-1 mong nhe cho sinh vien sang tao',         0),
(28, 3, 5, 'Acer ConceptD 3',         'acer-conceptd-3',         'Laptop do hoa gia re tai hop luc dung',           1),
(29, 3, 8, 'Gigabyte Aorus Creator',  'gigabyte-aorus-creator',  'Workstation gaming chuan do hoa',                0),
(30, 3, 7, 'MSI Raider GE66 UHD',     'msi-raider-ge66-uhd',     'Gaming do hoa 4K UHD 120Hz',                     1),
-- CATEGORY 4: MacBook (IDs 31-40 - all Apple brand)
(31, 4, 4, 'MacBook Air M2 13inch',   'macbook-air-m2-13',       'Mong nhe hieu nang cao voi chip M2',             1),
(32, 4, 4, 'MacBook Air M3 13inch',   'macbook-air-m3-13',       'Nang cap voi chip M3 nhanh hon 15%',             1),
(33, 4, 4, 'MacBook Pro 14inch M2',   'macbook-pro-14-m2',       'Man 120Hz Pro Motion cho cong viec chuyenghiep',  1),
(34, 4, 4, 'MacBook Pro 14inch M3',   'macbook-pro-14-m3',       'Chip M3 Pro voi RAM toi 36GB',                   1),
(35, 4, 4, 'MacBook Pro 16inch M2',   'macbook-pro-16-m2',       'Man lon 120Hz cho studio phat sinh content',      1),
(36, 4, 4, 'MacBook Pro 16inch M3',   'macbook-pro-16-m3',       'Nang cap M3 Max voi hieu nang o to M3 Pro',      0),
(37, 4, 4, 'MacBook Air 13 M3 2024',  'macbook-air-13-m3-2024',  'Phien ban 2024 voi man Liquid Retina va M3',     1),
(38, 4, 4, 'MacBook Air 15 M3 2024',  'macbook-air-15-m3-2024',  'Man 15.3 inch sau sac M3 voi GPU 10-core',       1),
(39, 4, 4, 'MacBook Pro 14 Max 2024',  'macbook-pro-14-max-2024', 'Chip M3 Max voi GPU 30-core hieu suat hang nhat', 1),
(40, 4, 4, 'MacBook Pro 16 Max 2024',  'macbook-pro-16-max-2024', 'Monster trinh dang co 64GB RAM va GPU 40-core',   0);

INSERT INTO product_variants (id, product_id, sku_code, ram, color, storage, quantity, base_price, img_url) VALUES
(1,  1,  'ROG-G15-001',    '16GB', 'Black',      '512GB SSD',  8, 35000000.00, 'assets/img/products/product_1.webp'),
(2,  2,  'ROG-ZEP-001',    '16GB', 'Black',      '1TB SSD',   10, 38500000.00, 'assets/img/products/product_1.webp'),
(3,  3,  'MSI-GE76-001',   '16GB', 'Black',      '1TB SSD',    6, 42000000.00, 'assets/img/products/product_1.webp'),
(4,  4,  'MSI-GS66-001',   '16GB', 'Black',      '512GB SSD', 12, 36500000.00, 'assets/img/products/product_1.webp'),
(5,  5,  'DELL-AWX15-001', '32GB', 'Silver',     '1TB SSD',    7, 50000000.00, 'assets/img/products/product_1.webp'),
(6,  6,  'DELL-AWX15X-01', '32GB', 'Silver',     '1TB SSD',    5, 52000000.00, 'assets/img/products/product_1.webp'),
(7,  7,  'GBYTE-ARS15-01', '16GB', 'Black',      '512GB SSD',  9, 32000000.00, 'assets/img/products/product_1.webp'),
(8,  8,  'GBYTE-ARS17-01', '16GB', 'Black',      '1TB SSD',    8, 35500000.00, 'assets/img/products/product_1.webp'),
(9,  9,  'LENO-LEG7-001',  '16GB', 'Gray',       '1TB SSD',   11, 39000000.00, 'assets/img/products/product_1.webp'),
(10, 10, 'ACER-NIT5-001',  '8GB',  'Black',      '512GB SSD', 15, 18500000.00, 'assets/img/products/product_1.webp'),
(11, 11, 'HP-PAV15-001',   '8GB',  'Silver',     '256GB SSD', 14, 13990000.00, 'assets/img/products/product_1.webp'),
(12, 12, 'HP-PAV16-001',   '16GB', 'Silver',     '512GB SSD', 10, 19990000.00, 'assets/img/products/product_1.webp'),
(13, 13, 'LENO-TPX1-001',  '16GB', 'Black',      '512GB SSD',  9, 28000000.00, 'assets/img/products/product_1.webp'),
(14, 14, 'LENO-TPT14-001', '16GB', 'Black',      '512GB SSD', 13, 24500000.00, 'assets/img/products/product_1.webp'),
(15, 15, 'ASUS-VIV15-001', '8GB',  'Silver',     '512GB SSD', 12, 12990000.00, 'assets/img/products/product_1.webp'),
(16, 16, 'ASUS-VIV14-001', '16GB', 'Silver',     '512GB SSD', 11, 21990000.00, 'assets/img/products/product_1.webp'),
(17, 17, 'ACER-ASP5-001',  '8GB',  'Silver',     '256GB SSD', 16, 11990000.00, 'assets/img/products/product_1.webp'),
(18, 18, 'ACER-ASP7-001',  '16GB', 'Silver',     '512GB SSD', 13, 17990000.00, 'assets/img/products/product_1.webp'),
(19, 19, 'DELL-VOS15-001', '8GB',  'Silver',     '256GB SSD', 14, 12990000.00, 'assets/img/products/product_1.webp'),
(20, 20, 'DELL-VOS14-001', '8GB',  'Silver',     '256GB SSD', 16, 11990000.00, 'assets/img/products/product_1.webp'),
(21, 21, 'DELL-PRE15-001', '32GB', 'Black',      '1TB SSD',    6, 55000000.00, 'assets/img/products/product_1.webp'),
(22, 22, 'DELL-PRE17-001', '32GB', 'Black',      '1TB SSD',    5, 57990000.00, 'assets/img/products/product_1.webp'),
(23, 23, 'HP-ZBOOK15-001', '32GB', 'Black',      '1TB SSD',    7, 48000000.00, 'assets/img/products/product_1.webp'),
(24, 24, 'HP-ZBCR16-001',  '32GB', 'Silver',     '1TB SSD',    8, 51000000.00, 'assets/img/products/product_1.webp'),
(25, 25, 'ASUS-PAST-001',  '32GB', 'Black',      '1TB SSD',    6, 45000000.00, 'assets/img/products/product_1.webp'),
(26, 26, 'ASUS-ACB-001',   '32GB', 'Silver',     '1TB SSD',    7, 46000000.00, 'assets/img/products/product_1.webp'),
(27, 27, 'LENO-YOG7-001',  '16GB', 'Gray',       '512GB SSD',  9, 22000000.00, 'assets/img/products/product_1.webp'),
(28, 28, 'ACER-CON3-001',  '16GB', 'Silver',     '512GB SSD', 10, 25000000.00, 'assets/img/products/product_1.webp'),
(29, 29, 'GBYTE-ACS-001',  '32GB', 'Black',      '1TB SSD',    6, 44000000.00, 'assets/img/products/product_1.webp'),
(30, 30, 'MSI-RAD-UHD',    '32GB', 'Black',      '1TB SSD',    7, 49000000.00, 'assets/img/products/product_1.webp'),
(31, 31, 'MAC-AIR13-M2',   '8GB',  'Midnight',   '256GB SSD', 10, 28000000.00, 'assets/img/products/product_1.webp'),
(32, 32, 'MAC-AIR13-M3',   '8GB',  'Midnight',   '256GB SSD', 12, 30500000.00, 'assets/img/products/product_1.webp'),
(33, 33, 'MAC-PRO14-M2',   '16GB', 'Space Gray', '512GB SSD',  9, 35000000.00, 'assets/img/products/product_1.webp'),
(34, 34, 'MAC-PRO14-M3',   '16GB', 'Space Gray', '512GB SSD', 11, 38000000.00, 'assets/img/products/product_1.webp'),
(35, 35, 'MAC-PRO16-M2',   '16GB', 'Space Gray', '512GB SSD',  8, 42000000.00, 'assets/img/products/product_1.webp'),
(36, 36, 'MAC-PRO16-M3',   '16GB', 'Space Gray', '1TB SSD',    7, 48000000.00, 'assets/img/products/product_1.webp'),
(37, 37, 'MAC-AIR13-M3-24','8GB',  'Starlight',  '256GB SSD', 13, 31000000.00, 'assets/img/products/product_1.webp'),
(38, 38, 'MAC-AIR15-M3-24','8GB',  'Starlight',  '256GB SSD', 11, 35500000.00, 'assets/img/products/product_1.webp'),
(39, 39, 'MAC-MAX14-24',   '18GB', 'Space Gray', '1TB SSD',    9, 52000000.00, 'assets/img/products/product_1.webp'),
(40, 40, 'MAC-MAX16-24',   '24GB', 'Space Gray', '1TB SSD',    8, 57990000.00, 'assets/img/products/product_1.webp');

-- =============================================
-- Seed FAQs (required by spec task #2)
-- =============================================
INSERT INTO faqs (question, answer, sort_order) VALUES
('Chinh sach bao hanh cua LaptopShop la gi?',
 'Tat ca san pham duoc bao hanh chinh hang toi thieu 12 thang. Mot so dong may Apple duoc bao hanh len den 24 thang.',
 1),
('Toi co the doi tra san pham khong?',
 'Ban co the doi tra trong vong 7 ngay neu san pham co loi tu nha san xuat, con nguyen seal va day du phu kien.',
 2),
('LaptopShop co ho tro tra gop khong?',
 'Co, chung toi ho tro tra gop 0% qua the tin dung cua cac ngan hang doi tac: VIB, Techcombank, Sacombank.',
 3),
('Thoi gian giao hang la bao lau?',
 'Noi thanh TP.HCM: 2-4 gio. Cac tinh thanh khac: 1-3 ngay lam viec.',
 4);

-- =============================================
-- Seed Articles (posted by admin user_id = 1)
-- =============================================
INSERT INTO articles (id, admin_id, title, slug, content, meta_title, meta_description, meta_keywords, published_at) VALUES
(1, 1,
 'Top 5 Laptop Gaming Tot Nhat 2025',
 'top-5-laptop-gaming-tot-nhat-2025',
 '<p>Noi dung chi tiet ve top 5 laptop gaming 2025...</p>',
 'Top 5 Laptop Gaming 2025 | LaptopShop VN',
 'Danh sach top 5 laptop gaming tot nhat nam 2025 voi hieu nang cao, tan nhiet tot.',
 'laptop gaming, laptop gaming 2025, laptop choi game',
 NOW()),
(2, 1,
 'MacBook Air M2 - Co Dang Mua Khong?',
 'macbook-air-m2-co-dang-mua-khong',
 '<p>Danh gia chi tiet MacBook Air M2...</p>',
 'Review MacBook Air M2 | LaptopShop VN',
 'Danh gia chi tiet MacBook Air M2 - hieu nang, thiet ke, pin va gia ban.',
 'macbook air m2, apple m2, review macbook',
 NOW());

-- =============================================
-- Seed Reviews (user_id must exist in members; product_id from products)
-- =============================================
INSERT INTO reviews (user_id, product_id, rating, comment, status) VALUES
(2, 1, 5, 'May choi game muot lam, tan nhiet tot.', 'approved'),
(3, 2, 4, 'Phu hop voi cong viec van phong, pin on.', 'approved'),
(2, 3, 5, 'Thiet ke dep, mong nhe, dang tien.', 'pending');

-- =============================================
-- Seed Contacts
-- =============================================
INSERT INTO contacts (customer_name, customer_email, subject, message, status) VALUES
('Khach Hang X', 'khachhangx@gmail.com',
 'Hoi ve thoi gian giao hang',
 'Cho minh hoi don hang giao noi thanh mat bao lau?',
 'unread'),
('Nguyen Thi Y', 'nty@gmail.com',
 'Yeu cau xuat hoa don VAT',
 'Cho toi xin hoa don VAT cho don hang DH240001.',
 'replied');
