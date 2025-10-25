<?php
// elara.php - Quick & Dirty Router for raggiesoftknox.com

// Define the root directory for includes and views
define('ROOT_PATH', __DIR__);

// Get the request URI (e.g., "/pages/about", "/")
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Simple routing based on the URL path
switch ($request_uri) {
    case '/': // Handle the root URL
    case '/pages/': // Keep this if you want /pages/ to also show home
        $view_to_load = 'pages/home'; // Loads /pages/home.php
        break;

    case '/pages/about':
        $view_to_load = 'pages/about'; // Loads /pages/about.php
        break;

    case '/pages/license':
        $view_to_load = 'pages/license'; // Loads /pages/license.php
        break;

    // Add other simple pages here if needed

    default:
        // Handle 404 Not Found
        http_response_code(404);
        $view_to_load = 'error/404'; // Loads /error/404.php
        break;
}

// Render the page by including header, view, and footer
// Note the path construction uses the $view_to_load variable directly
require_once ROOT_PATH . '/includes/header.php';
require_once ROOT_PATH . '/' . $view_to_load . '.php'; // Dynamic view loading
require_once ROOT_PATH . '/includes/footer.php';

