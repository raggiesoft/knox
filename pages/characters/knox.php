<?php
/**
 * Page: Subject "KNOX" (Persona)
 *
 * This file details the Axiom's flawed internal profile of the
 * persona they call "Knox".
 */

// --- 1. SETTINGS ---
$strPageTitle = "Subject: KNOX";
$strHeaderMenuFile = 'about.php';
$strSidebarMenuFile = 'characters.php';


// --- 2. CONTENT ---
// Define the unique content function for this page
function render_page_content() {
?>

    <wa-alert open variant="primary" style="margin-bottom: 1rem;">
        <wa-icon slot="icon" name="fa-pro-circle-info"></wa-icon>
        <strong>This is a Persona, Not a Person</strong><br />
        "Knox" is a myth; a persona invented by the Axiom to explain a series of devastating, deniable sabotage incidents. They are hunting a single, phantom veteran, when in reality, all "Knox" activity is the work of two young twins.
    </wa-alert>
    
    <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
        <wa-button variant="brand" href="/about/characters/anya">
            <wa-icon slot="prefix" name="fa-pro-ghost"></wa-icon>
            Anya Rostova (The Reality)
        </wa-button>
        <wa-button variant="brand" href="/about/characters/kael">
            <wa-icon slot="prefix" name="fa-pro-gear"></wa-icon>
            Kael Rostova (The Reality)
        </wa-button>
    </div>

    <wa-card>
        <div slot="header">
            <wa-icon name="fa-pro-file-shield"></wa-icon>
            Axiom Internal Threat Profile: Subject "KNOX"
        </div>
        
        <p>This is the flawed profile the Axiom is actively hunting.</p>
        
        <ul>
            <li><strong>Designation:</strong> Domestic Terrorist / Insurgent Leader</li>
            <li><strong>Threat Level:</strong> Alpha-Prime (Sector-Wide Priority)</li>
            <li><strong>Assessed Sex:</strong> Male (High Confidence)</li>
            <li><strong>Assessed Age:</strong> Estimated Late 40s - Early 50s</li>
            <li><strong>Assessed Build:</strong> Heavy-set, muscular. Likely shows extensive combat scarring</li>
            <li><strong>Assessed Background:</strong> Former Republic Special Forces operative</li>
            <li><strong>Assessed Skills:</strong> Mastery of conventional ordnance, heavy weapons, and electronic countermeasures</li>
        </ul>
        
        <div slot="footer">
            <strong>Status:</strong> Active, Unsubstantiated Identity
        </div>
    </wa-card>

<?php
} // End of render_page_content()
?>