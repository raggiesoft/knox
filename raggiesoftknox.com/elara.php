<?php
// elara.php - Quick & Dirty Router
define('ROOT_PATH', __DIR__);
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Sanitize the request URI:
// Remove trailing slash if it exists and it's not the root path "/"
if (strlen($request_uri) > 1) {
    $request_uri = rtrim($request_uri, '/');
}

// --- SET DEFAULTS ---
$siteName = 'Knox: A Narrative Universe'; // Define base site name
$pageTitle = $siteName; // Default title is just the site name
$currentPageTheme = 'aerie-hold';
$currentHeaderMenu = ROOT_PATH . '/includes/components/headers/header-default.php';
$currentSidebar = ROOT_PATH . '/includes/components/sidebars/sidebar-default.php';
$showSidebar = true;
// --------------------

$view_to_load = null;
$params = [];

// Routing logic...
switch ($request_uri) {
    case '/':
        $view_to_load = 'pages/home';
        // $pageTitle = 'Home - ' . $siteName; // Or keep the default site name
        $showSidebar = false;
        break;

    case '/about':
        $view_to_load = 'pages/about';
        $pageTitle = 'About This Project - ' . $siteName; // <<< SET PAGE TITLE
        break;

    case '/license':
        $view_to_load = 'pages/license';
        $pageTitle = 'License Information - ' . $siteName; // <<< SET PAGE TITLE
        break;

        // Page Routes for Concepts
    case '/concepts/pact':
        $view_to_load = 'pages/concepts/pact';
        $pageTitle = 'The Pact, Anya, Kael, and Pip - ' . $siteName; // <<< SET PAGE TITLE
        break;
    case '/concepts/port':
        $view_to_load = 'pages/concepts/port';
        $pageTitle = 'Port Telsus: The Axiom - ' . $siteName; // <<< SET PAGE TITLE
        break;
}

// Handle dynamic chapter route if no static route was found
if ($view_to_load === null) {
    if (preg_match('#^/chapter/([a-zA-Z0-9-]+)$#', $request_uri, $matches)) {
        $params['slug'] = $matches[1];
        $view_to_load = 'pages/chapter';
        // Example: Set title based on slug or fetched chapter data later
        $pageTitle = 'Chapter: ' . htmlspecialchars(ucwords(str_replace('-', ' ', $params['slug']))) . ' - ' . $siteName; // <<< SET PAGE TITLE
    } else {
        http_response_code(404);
        $view_to_load = 'errors/404';
        $pageTitle = 'Page Not Found - ' . $siteName; // <<< SET PAGE TITLE
        $showSidebar = false;
    }
}

if (!empty($params)) {
    extract($params);
}

// --- RENDER THE PAGE ---
// $currentPageTheme, $currentHeaderMenu, $currentSidebar, $showSidebar are now set

// 1. Include Header (which will use the variables)
require_once ROOT_PATH . '/includes/header.php';

// 2. Conditionally include Sidebar and wrap main content
//    We start the main content container and row structure here
echo '<div class="container-fluid flex-grow-1 d-flex">'; // Use container-fluid for potential full-width sections
echo '  <div class="row flex-grow-1">'; // Make row fill height

if ($showSidebar && file_exists($currentSidebar)) {
    // Sidebar column (adjust col classes for desired width breakpoints)
    // d-none d-md-block hides sidebar on small screens
    // vh-100 makes sidebar full height potentially (might need more styling)
    echo '    <aside class="col-md-3 col-lg-2 d-none d-md-block bg-body-tertiary border-end p-3">';
    require_once $currentSidebar;
    echo '    </aside>';
    // Main content column (takes remaining width)
    echo '    <main id="main-content" class="col-md-9 col-lg-10 p-3">'; // Add main tag here
} else {
    // Main content column (takes full width if no sidebar)
    echo '    <main id="main-content" class="col-12 p-3">'; // Add main tag here
}

// 3. Include the specific page content view
if (file_exists(ROOT_PATH . '/' . $view_to_load . '.php')) {
    require_once ROOT_PATH . '/' . $view_to_load . '.php';
} else {
    http_response_code(404);
    // Ensure 404 file path is correct relative to ROOT_PATH
    require_once ROOT_PATH . '/error/404.php';
}

// Close content divs
echo '    </main>'; // Close main content column (<main>)
echo '  </div>'; // Close row
echo '</div>'; // Close container-fluid

// 4. Include Footer
require_once ROOT_PATH . '/includes/footer.php';