<?php
/**
 * Universal Header Component (header.php)
 *
 * This file generates the top portion of every HTML page, including the
 * <head> section and the main header navigation.
 *
 * It relies on variables loaded by the front controller (anya.php) from
 * the specific page file being rendered:
 * - $strPageTitle:    Used to set the document's <title>.
 * - $strHeaderMenuFile: The filename of the specific menu to load from
 * /components/menus/headers/.
 * - ASSET_URL:         A constant from oris.php for the CDN path.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php // --- Dynamic Page Title --- ?>
    <title><?php echo htmlspecialchars($strPageTitle ?? 'Knox | A World by RaggieSoft'); ?></title>

    <?php // --- Web Awesome & Font Awesome (Asset Dependencies) --- ?>
    
    <link rel="stylesheet" href="https://early.webawesome.com/webawesome@3.0.0-beta.6/dist/styles/webawesome.css" />

    <script src="https://kit.fontawesome.com/ec060982d4.js" crossorigin="anonymous"></script> 
    
    <script type="module">
      import { setKitCode } from 'https://early.webawesome.com/webawesome@3.0.0-beta.6/dist/webawesome.loader.js';
      setKitCode('ec060982d4');
    </script>
    
    <?php // --- Custom Site Stylesheet (from DigitalOcean Spaces) --- ?>
    <link rel="stylesheet" href="<?php echo ASSET_URL; ?>/css/theme.css">

</head>
<body>

    <?php // This is the main site header component from Web Awesome ?>
    <wa-header>
        <nav class="header-nav">
            <?php
            // --- Dynamic Menu Inclusion ---
            // This block checks which menu the page requested and includes it.

            if (!empty($strHeaderMenuFile)) {

                // Construct the full, secure path to the menu file
                $strMenuPath = __DIR__ . '/menus/headers/' . $strHeaderMenuFile;

                // Include the menu file only if it actually exists
                if (file_exists($strMenuPath)) {
                    include_once($strMenuPath);
                } else {
                    // Log an error or show a simple message if the menu is missing
                    echo '';
                }
            }
            ?>
        </nav>
    </wa-header>

    <?php // The main content wrapper will start after the sidebar is loaded ?>
    <div class="site-container">

<?php // The opening <body> and <div class="site-container"> tags are left unclosed. ?>
<?php // They will be closed by the footer.php component. ?>