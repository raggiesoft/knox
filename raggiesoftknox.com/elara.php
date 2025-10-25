<?php
// elara.php - Quick & Dirty Router

// Define the root directory for includes
define('ROOT_PATH', __DIR__);

// Get the request URI (e.g., "/about", "/")
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Simple routing
switch ($request_uri) {
    case '/': // Let's also make the root URL work
    case '/pages/':
        $view_to_load = 'pages/home'; // Loads /pages/home.php
        break;
    
    case '/pages/about':
        $view_to_load = 'pages/about'; // Loads /pages/about.php
        break;

    case '/pages/license':
        $view_to_load = 'pages/license'; // Loads /pages/license.php
        break;
    
    case '/pages/about/copyright':
        $view_to_load = 'pages/about/copyright'; // Loads /pages/about/copyright.php
        break;
        
    default:
        // Handle 404
        http_response_code(404);
        $view_to_load = 'error/404'; // Loads /error/404.php
        break;
}

// Render the page
require_once ROOT_PATH . '/includes/header.php';
// This line now correctly builds the path you want
require_once ROOT_PATH . '/' . $view_to_load . '.php';
require_once ROOT_PATH . '/includes/footer.php';
