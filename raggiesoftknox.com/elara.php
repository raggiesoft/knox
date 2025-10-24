<?php
$server_name = htmlspecialchars($_SERVER['SERVER_NAME']);
$router_name = htmlspecialchars(basename(__FILE__));
$request_uri = htmlspecialchars($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to <?php echo htmlspecialchars("$server_name"); ?></title>
    <style>
        :root {
            --bg-color-light: #f4f4f4;
            --text-color-light: #333;
            --bg-color-dark: #222;
            --text-color-dark: #eee;
            --link-color: #007bff;
            --accent-color: #007bff;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: var(--bg-color-light);
            color: var(--text-color-light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        
        main {
            background: var(--bg-color-light);
            padding: 2rem;
            border-radius: 8px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #ddd;
        }

        h1 {
            color: var(--accent-color);
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 10px;
            font-size: 2.5rem;
        }
        
        p {
            font-size: 1.1rem;
        }

        ul {
            list-style: none;
            padding: 0;
        }
        
        li {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        
        a {
            color: var(--link-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        a:hover {
            text-decoration: underline;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: var(--bg-color-dark);
                color: var(--text-color-dark);
            }
            main {
                background: #333;
                border-color: #444;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }
            :root {
                --link-color: #58a6ff;
            }
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            main {
                padding: 1.5rem;
            }
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <main>
        <h1>Welcome to <?php echo htmlspecialchars("$server_name"); ?></h1>
        <p><?php echo htmlspecialchars("$router_name"); ?> (the knowledge keeper) has successfully routed your request for: <strong><?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?></strong></p>
        <p>This is the main project hub. The full doormat site will be built tomorrow.</p>
        
        <h2>Project Links:</h2>
        <ul>
            <li><a href="https://lore.raggiesoftknox.com">The Lore Codex (WordPress)</a></li>
            <li><a href="https://pact.raggiesoftknox.com">The Glimmer Moss Pact (Twins)</a></li>
            <li><a href="https://port.raggiesoftknox.com">Port Telsus (Axiom)</a></li>
        </ul>
    </main>
</body>
</html>
