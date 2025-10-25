<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knox: A Narrative Universe</title>
    
    <meta name="description" content="Explore Telsus Minor, a sci-fi universe where the reality of a phantom saboteur named Knox is a family fighting a corrupt corporation.">

    <!-- Web Awesome & Font Awesome (Asset Dependencies) -->
    <link rel="stylesheet" href="https://early.webawesome.com/webawesome@3.0.0-beta.6/dist/styles/webawesome.css" />
    <script src="https://kit.fontawesome.com/ec060982d4.js" crossorigin="anonymous"></script> 
    <script type="module">
      import { setKitCode } from 'https://early.webawesome.com/webawesome@3.0.0-beta.6/dist/webawesome.loader.js';
      setKitCode('ec060982d4');
    </script>
    
    <!-- 
      Main Stylesheet - This links directly to your CDN.
      Upload the 'main.css' file to 'cdn/css/main.css'
    -->
    <link rel="stylesheet" href="https://assets.raggiesoft.com/css/main.css">
</head>
<body>
    <wa-page>
        <span slot="skip-to-content">Skip to Main Content</span>

        <header slot="header" class="site-header page-container">
            <a href="/" class="brand-logo" aria-label="Knox Universe Home">KNOX</a>
            
            <nav class="desktop-nav wa-desktop-only" aria-label="Main Navigation">
                <wa-layout-cluster gap="2">
                    <a href="https://pact.raggiesoftknox.com/">The Pact</a>
                    <a href="https://port.raggiesoftknox.com/">The Port</a>
                    <a href="https://lore.raggiesoftknox.com/">The Lore Bible</a>
                </wa-layout-cluster>
            </nav>

            <wa-button data-toggle-nav class="wa-mobile-only" appearance="plain" aria-label="Toggle Menu">
                <wa-icon name="fa-pro-solid fa-bars"></wa-icon>
            </wa-button>
        </header>

        <nav slot="navigation" class="mobile-drawer-nav" aria-label="Mobile Navigation">
            <a href="https://pact.raggiesoftknox.com/">The Pact</a>
            <a href="https://port.raggiesoftknox.com/">The Port</a>
            <a href="https://lore.raggiesoftknox.com/">The Lore Bible</a>
            <hr>
            <a href="/about">About This Project</a>
            <a href="/license">License Information</a>
        </nav>

        <main id="main-content">
