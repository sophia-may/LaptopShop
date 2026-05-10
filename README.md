# CellphoneS E-Commerce & Admin Dashboard

This is a modern, full-stack E-Commerce and Admin Dashboard application built using native PHP, HTML5, CSS3, JavaScript, and MySQL without external frameworks. It features a responsive split-screen client UI, an interactive Q&A system, and a comprehensive Srtdash-powered admin backend.

## Prerequisites
- **XAMPP, WAMP, or MAMP** (Apache and MySQL)
- **PHP 8.x** or higher
- **MySQL / MariaDB**

## Installation & Setup Guide

### 1. Project Placement
If you are using XAMPP, place the entire `LaptopWeb` project folder into your `htdocs` directory:
```
C:\xampp\htdocs\LaptopWeb
```

### 2. Start Servers
Open the XAMPP Control Panel and start **Apache** and **MySQL**.

### 3. Database Setup
The application requires a database to function. The database schema and seed data are located in the `database/` folder.

1. Open **phpMyAdmin** in your browser: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. The provided SQL scripts create a database named `laptopshop`.
3. Go to the **Import** tab.
4. Choose the file `database/schema.sql` and click **Import**.
5. Once the schema is imported, choose the file `database/seed.sql` and click **Import** to populate the site settings and dummy data.

*Note: Ensure the database connection settings in `FE/config/db.php` match your local environment. Currently, the code expects the database name. If `schema.sql` creates `laptopshop`, ensure `$db_name = 'laptopshop';` is set in `FE/config/db.php` (if it was previously set to `phone_shop`, please update it).*

### 4. Running the Application
Once the database is set up, you can access the application through your web browser:

- **Main Website / Client Interface**: 
  [http://localhost/LaptopWeb/FE/index.php](http://localhost/LaptopWeb/FE/index.php)

- **Admin Dashboard**:
  Log in via the client interface using an administrator account. Once authenticated, you will be redirected to the admin dashboard, or you can access it via:
  [http://localhost/LaptopWeb/FE/index.php?page=admin_dashboard](http://localhost/LaptopWeb/FE/index.php?page=admin_dashboard)

## Key Features
- **No-Framework Architecture**: Built purely with native web technologies.
- **Srtdash Admin Dashboard**: A sleek, fully responsive admin panel to manage products, orders, Q&A, and site settings.
- **Dynamic Site Settings**: The "About" page and other site configurations are driven completely by the database (`site_settings` table), meaning admins can change the site content without touching code.
- **ElectroPulse Theme**: A custom design system emphasizing the `#e11b22` primary red color for high-energy retail aesthetics.
- **Modern Login Flow**: Split-screen authentication page.
