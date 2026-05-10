<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'laptopshop');
define('DB_USER', 'root');
define('DB_PASS', '');

define('JWT_SECRET', 'laptopshop-secret-key-change-in-production');
define('JWT_EXPIRE', 86400 * 7); // 7 days
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_MIMES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
