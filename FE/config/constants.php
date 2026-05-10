<?php
// FE/config/constants.php — Shared constants (inherited from backend)

if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_PORT')) define('DB_PORT', '3306');
if (!defined('DB_NAME')) define('DB_NAME', 'laptopshop');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');

if (!defined('JWT_SECRET')) define('JWT_SECRET', 'laptopshop-secret-key-change-in-production');
if (!defined('JWT_EXPIRE')) define('JWT_EXPIRE', 86400 * 7); // 7 days
if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', __DIR__ . '/../uploads/');
if (!defined('UPLOAD_MAX_SIZE')) define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
if (!defined('ALLOWED_IMAGE_MIMES')) define('ALLOWED_IMAGE_MIMES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
