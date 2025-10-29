# Knox: A Narrative Universe

Welcome to the central development hub for the **Knox** narrative universe, created by Michael Ragsdale (raggiesoft). This project encompasses the world-building, future narratives, and the web technologies used to present them.

## The Premise

On the oppressive, high-gravity jungle world of **Telsus Minor**, located in the unaligned Telsan Gap, the monopolistic **Axiom corporation** rules through economic force. Their operations are plagued by a phantom saboteur they call **"Knox"**—a myth they believe to be a single, highly-trained ex-military operative.

But the Axiom hunts a ghost of their own making. The reality is far more dangerous: **Anya and Kael Rostova**, young fraternal twins raised in the hidden canopy villages of "The Weave," wage a secret war using scavenged technology, improvised chemistry, and an intimate knowledge of the lethal environment the Axiom dismisses as the "Green Hell."

## Project Architecture

This narrative universe is presented across a "family" of interconnected websites:

- **`https://raggiesoftknox.com/` (Main Site):** Serves as the primary hub or "welcome mat," presenting the narrative and directing users. Built with custom PHP using a front controller pattern (`elara.php`).
    
- **`https://lore.raggiesoftknox.com/` (The Lore Bible):** Hosts all the detailed world-building, character profiles, and faction backstories. This site is powered by **WordPress** using a custom-built theme (`knox-lore`).
    
- **`https://pact.raggiesoftknox.com/` & `https://port.raggiesoftknox.com/` (Narrative Sites):** Dedicated sites to present the story from different perspectives.
    

## Technology Stack

This project utilizes a custom PHP application and a WordPress installation hosted on DigitalOcean:

- **Frontend:** Custom PHP (using `elara.php` front controller), HTML, CSS, and a WordPress CMS.
    
- **Styling:** Bootstrap 5, Custom SCSS Themes, WP Dark Mode Ultimate.
    
- **UI Components:** Font Awesome Pro (Icons).
    
- **Server:** DigitalOcean Droplet (`glowing-galaxy`) running Ubuntu 25.04.
    
- **Web Server:** Nginx with PHP 8.4.
    
- **Database (Planned/Optional):** MariaDB (Hosted on `elara.raggiesoft.com`).
    
- **Asset Hosting:** DigitalOcean Spaces (CDN via `assets.raggiesoft.com`).
    
- **Security:** Cloudflare (DNS, potentially WAF), UFW, Fail2Ban, Bastion Host (`sentinel-star.raggiesoft.com`), SSH Key Authentication.
    
- **Version Control:** Git, GitHub.
    
- **Deployment:** Custom Bash script (`deploy.sh`) using `git pull`.
    

## Repository Structure & Version Control

This Git repository (`raggiesoft/knox-main`) contains the entire codebase for the `raggiesoftknox.com` website and the custom WordPress theme for `lore.raggiesoftknox.com`.

## Triple-Licensing

This project contains three distinct types of intellectual property, governed by separate licenses:

1. **Narrative Content (CC BY-SA 4.0):**
    
    - **Applies to:** All story text, character profiles, world-building descriptions, lore entries, etc. (e.g., the content of `Knox - Behind the Scenes.docx`).
        
    - **License:** Creative Commons Attribution-ShareAlike 4.0 International License (`CC BY-SA 4.0`). See [CONTENT_LICENSE.md](https://www.google.com/search?q=CONTENT_LICENSE.md).
        
    - **Permissions:** You are free to share and adapt this creative work, even commercially, provided you give appropriate credit to Michael Ragsdale (raggiesoft), indicate if changes were made, and distribute your contributions under the same CC BY-SA 4.0 license.
        
2. **Website Source Code (MIT):**
    
    - **Applies to:** All general PHP, JavaScript, CSS, Nginx configurations, deployment scripts, and other code used to build and display the **non-WordPress** sites.
        
    - **License:** The MIT License. See LICENSE.md.
        
    - **Permissions:** You are free to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the software, subject to the conditions outlined in the MIT License file.
        
3. **WordPress Theme Code (GPL v3):**
    
    - **Applies to:** The source code for the custom WordPress theme (`knox-lore`) developed for the `lore.raggiesoftknox.com` site. As a derivative work of WordPress, this theme is licensed under the GPL.
        
    - **License:** GNU General Public License v3.0.
        
    - **Permissions:** You are free to run, study, share, and modify the software. Any distributed derivative works must also be licensed under the GPL.
        

## Getting Started / Development

_(Placeholder: Add instructions later on how to set up the development environment, run the PHP application locally, etc.)_

## Contributing

_(Placeholder: Add guidelines later if you plan to accept community contributions to the lore or code.)_

_This README reflects the project state and planned architecture as of October 2025._