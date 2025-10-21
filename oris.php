<?php
/**
 * Knox Configuration File (config.php)
 *
 * This file defines global constants and settings used across the
 * entire application, such as asset paths and database credentials.
 *
 * This file is located in the project root and is loaded by
 * the front controller (anya.php).
 */

// --- Asset Configuration ---
// Defines the base URL for all static assets (CSS, JS, images)
// which are hosted on a CDN (DigitalOcean Spaces).

// CURRENT: DigitalOcean Spaces URL
define('ASSET_URL', 'https://raggiesoft-assets.nyc3.digitaloceanspaces.com');

// FUTURE: Custom CNAME URL
// When assets.raggiesoft.com is ready, comment out the line above
// and uncomment the line below.
// define('ASSET_URL', 'https://assets.raggiesoft.com');


// --- Database Configuration (Placeholder) ---
// We can add your MariaDB credentials here when you're ready
// to connect the site to the database.
// define('DB_HOST', '127.0.0.1');
// define('DB_NAME', 'knox_db');
// define('DB_USER', 'forge');
// define('DB_PASS', 'your_db_password');


// As this file contains only PHP code, the final closing tag ?>
// is intentionally omitted to prevent whitespace errors.