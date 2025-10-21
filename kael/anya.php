<?php
/**
 * Knox Front Controller (anya.php)
 *
 * This file acts as the single entry point for all non-static requests.
 * It is responsible for:
 * 1. Loading configuration.
 * 2. Parsing the incoming URL.
 * 3. Routing the request to the correct page-content file.
 * 4. Assembling the final HTML response from modular components
 * (header, sidebar, page content, footer).
 *
 * This file is located in the project root for security, one level
 * above the public-facing 'public' directory.
 */

// --- 1. Load Configuration ---
// Load the central config file which defines constants like ASSET_URL.
// We use require_once as this file is essential for the site to run.
require_once __DIR__ . '/oris.php';


// --- 2. Parse the Request URL ---

// Get the full request URI from the server (e.g., "/about/characters/rook/?ref=123")
$strRequestUri = $_SERVER['REQUEST_URI'];

// Isolate the path component, stripping any query parameters.
$strRequestPath = parse_url($strRequestUri, PHP_URL_PATH);

// Trim leading/trailing slashes for clean exploding
// (e.g., "/about/characters/" becomes "about/characters")
$strCleanedPath = trim($strRequestPath, '/');

// Explode the path into an array of parts
// (e.g., "about/characters/rook" becomes ['about', 'characters', 'rook'])
$arrPathParts = explode('/', $strCleanedPath);


// --- 3. Define File Paths & Default Variables ---

// Define the absolute path to the 'pages' directory for security.
$strBaseContentDir = __DIR__ . '/pages/';

// Set the default page to load. This will be updated by the routing logic.
// If no route matches, the 404 page will be loaded.
$strPageToLoad = $strBaseContentDir . '404.php';

// Set default page-level variables. These will be overridden by the
// settings inside the specific page file that we load in Step 5.
$strPageTitle = 'Knox';
$strHeaderMenuFile = 'default.php';
$strSidebarMenuFile = 'default.php';


// --- 4. Routing Logic ---

// Get the first part of the URL, which determines the main section.
// Default to 'home' if the path is empty.
$strMainPath = $arrPathParts[0] ?? 'home';

// Use a switch statement to route to the correct content file.
switch ($strMainPath) {

    case 'home':
    case '': // Handles the root URL (e.g., knox.raggiesoft.com/)
        $strPageToLoad = $strBaseContentDir . 'home.php';
        break;

    case 'about':
        // This section handles all URLs for the world-building document.
        if (isset($arrPathParts[1])) {
            
            // Check the sub-section (e.g., 'characters', 'world', etc.)
            switch ($arrPathParts[1]) {
                
                case 'characters':
                    if (isset($arrPathParts[2])) {
                        // A specific character is requested (e.g., /about/characters/rook)
                        $strPageToLoad = $strBaseContentDir . 'characters/' . $arrPathParts[2] . '.php';
                    } else {
                        // The main character index (e.g., /about/characters)
                        $strPageToLoad = $strBaseContentDir . 'characters/index.php';
                    }
                    break;
                
                case 'world':
                    if (isset($arrPathParts[2])) {
                        // A specific location is requested (e.g., /about/world/telsus-minor)
                        $strPageToLoad = $strBaseContentDir . 'world/' . $arrPathParts[2] . '.php';
                    } else {
                        // The main world index (e.g., /about/world)
                        $strPageToLoad = $strBaseContentDir . 'world/index.php';
                    }
                    break;
                
                // --- PLACEHOLDER FOR FUTURE SECTION ---
                case 'factions':
                    if (isset($arrPathParts[2])) {
                        // e.g., /about/factions/axiom
                        $strPageToLoad = $strBaseContentDir . 'factions/' . $arrPathParts[2] . '.php';
                    } else {
                        // e.g., /about/factions
                        $strPageToLoad = $strBaseContentDir . 'factions/index.php';
                    }
                    break;
                
                // --- PLACEHOLDER FOR FUTURE SECTION ---
                case 'technology':
                    if (isset($arrPathParts[2])) {
                        // e.g., /about/technology/glimmer-moss
                        $strPageToLoad = $strBaseContentDir . 'technology/' . $arrPathParts[2] . '.php';
                    } else {
                        // e.g., /about/technology
                        $strPageToLoad = $strBaseContentDir . 'technology/index.php';
                    }
                    break;
            }
        } else {
            // Default /about page (if one exists, e.g. /about.php)
            // If not, it will default to the 404 set in Step 3.
            if (file_exists($strBaseContentDir . 'about.php')) {
                 $strPageToLoad = $strBaseContentDir . 'about.php';
            }
        }
        break;

    // --- PLACEHOLDER FOR FUTURE SECTION ---
    case 'narrative':
        // This section will handle the story itself.
        if (isset($arrPathParts[1])) {
            // e.g., /narrative/chapter-1
            $strPageToLoad = $strBaseContentDir . 'narrative/' . $arrPathParts[1] . '.php';
        } else {
            // e.g., /narrative (main story index)
            $strPageToLoad = $strBaseContentDir . 'narrative/index.php';
        }
        break;

    // Add other top-level routes here (e.g., 'contact', 'gallery')
}


// --- 5. Load Page-Specific Variables ---

// We must check if the routed file actually exists on the server.
$bFileExists = file_exists($strPageToLoad);

if (!$bFileExists) {
    // The route was valid but the file is missing (e.g., /about/characters/invalid-name).
    // Set a 404 Not Found HTTP response code.
    http_response_code(404);
    
    // Force the page to load the 404 content.
    $strPageToLoad = $strBaseContentDir . '404.php';
}

// Include the page file. This does NOT output HTML.
// This critical step loads the page-specific variables
// (e.g., $strPageTitle, $strHeaderMenuFile, $strSidebarMenuFile)
// and makes the render_page_content() function available in memory.
include_once($strPageToLoad);


// --- 6. Render the Full Page Assembly ---

// The page-specific variables (like $strPageTitle) are now available
// for the component wrappers to use.

// Load the header wrapper (builds the <head> and top nav)
include_once(__DIR__ . '/components/header.php');

// Load the sidebar wrapper (builds the side nav)
include_once(__DIR__ . '/components/sidebar.php');

// Start the main content area
echo '<main class="content-wrapper">';

// Call the page's specific content-rendering function
if (function_exists('render_page_content')) {
    // This function was defined in the file loaded in Step 5.
    render_page_content();
} else {
    // Fallback in case the page file is malformed (e.g., function is missing)
    echo '<h1>Error: Page content render function not found.</h1>';
    // You might want to log this error for debugging.
}

echo '</main>';

// Load the footer wrapper (closes </body>, </html>, etc.)
include_once(__DIR__ . '/components/footer.php');

// The script ends here, and the full HTML page is sent to the user.