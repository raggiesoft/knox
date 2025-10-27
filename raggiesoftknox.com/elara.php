<?php
// elara.php - Quick & Dirty Router

// Define the root directory for includes
define('ROOT_PATH', __DIR__);

// Get the request URI (e.g., "/about", "/")
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Initialize view and params
$view_to_load = null;
$params = [];

// Simple routing for static pages
switch ($request_uri) {
    case '/':
        $view_to_load = 'pages/home';
        break;
    
    case '/about':
        $view_to_load = 'pages/about';
        break;

    case '/license':
        $view_to_load = 'pages/license';
        break;
}

// Handle dynamic chapter route if no static route was found
if ($view_to_load === null) {
    // This regex matches /chapter/ followed by letters, numbers, and hyphens
    if (preg_match('#^/chapter/([a-zA-Z0-9-]+)$#', $request_uri, $matches)) {
        $params['slug'] = $matches[1]; // Capture the slug
        $view_to_load = 'pages/chapter'; // Load the chapter template
    } else {
        // Handle 404
        http_response_code(404);
        $view_to_load = 'error/404';
    }
}

// Pass params (like $slug) into the view's scope
if (!empty($params)) {
    extract($params);
}

// Render the page
require_once ROOT_PATH . '/includes/header.php';
// Check if the view file exists before trying to load it
if (file_exists(ROOT_PATH . '/' . $view_to_load . '.php')) {
    require_once ROOT_PATH . '/' . $view_to_load . '.php';
} else {
    // Fallback to 404 if the view file is missing
    http_response_code(404);
    require_once ROOT_PATH . '/error/404.php';
}
require_once ROOT_PATH . '/includes/footer.php';

