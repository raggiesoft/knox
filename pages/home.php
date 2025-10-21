<?php
/**
 * Page: Home
 *
 * This file sets the variables and defines the render_page_content()
 * function for the main site homepage.
 */

// --- 1. SETTINGS ---
// Set the page-specific variables
$strPageTitle = "Knox | A World by RaggieSoft";
$strHeaderMenuFile = 'default.php';
$strSidebarMenuFile = 'default.php';


// --- 2. CONTENT ---
// Define the unique content function for this page
function render_page_content() {
?>

    <h1>Welcome to the World of Knox</h1>
    <p>This is the placeholder homepage. The story of Telsus Minor, the Axiom, and the myth of "Knox" will be detailed here.</p>
    
    <wa-card>
        <div slot="header">
            <wa-icon name="fa-pro-planet-moon"></wa-icon> Telsus Minor
        </div>
        [cite_start]<p>A rugged, high-gravity jungle world [cite: 79] [cite_start]that the Axiom corporation views as a "Green Hell" [cite: 78] [cite_start]but natives call "The Weave"[cite: 87].</p>
    </wa-card>

<?php
} // End of render_page_content()
?>