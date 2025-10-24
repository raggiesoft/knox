# Knox: A Narrative Universe

Welcome to the central development hub for the **Knox** narrative universe, created by Michael Ragsdale (raggiesoft). This project encompasses the world-building, future narratives, and the web technologies used to present them.

## The Premise

On the oppressive, high-gravity jungle world of **Telsus Minor**, located in the unaligned Telsan Gap, the monopolistic **Axiom corporation** rules through economic force. Their operations are plagued by a phantom saboteur they call **"Knox"**—a myth they believe to be a single, highly-trained ex-military operative.

But the Axiom hunts a ghost of their own making. The reality is far more dangerous: **Anya and Kael Rostova**, young fraternal twins raised in the hidden canopy villages of "The Weave," wage a secret war using scavenged technology, improvised chemistry, and an intimate knowledge of the lethal environment the Axiom dismisses as the "Green Hell."

## Project Architecture

This narrative universe is presented across multiple, interconnected websites, each with a distinct purpose:

- **`https://raggiesoftknox.com/` (Main Site):** The primary welcome page and central hub, introducing the world and linking to the other sites. Built with custom PHP.
    
- **`https://lore.raggiesoftknox.com/` (Lore Bible):** Hosts the detailed world-building information (characters, locations, factions, technology). Powered by WordPress with a custom theme.
    
- **`https://pact.raggiesoftknox.com/` (Narrative - Twins' POV):** Intended to host the story told from the perspective of Anya and Kael. (Technology TBD - likely custom PHP).
    
- **`https://port.raggiesoftknox.com/` (Narrative - Axiom POV):** Intended to host the story told from the perspective of Axiom personnel (like Auditors or Whispers). (Technology TBD - likely custom PHP).
    

## Technology Stack

This project utilizes a polyglot microservices architecture hosted primarily on DigitalOcean:

- **Frontend (Main Site):** Custom PHP (using `elara.php` front controller), HTML, CSS.
    
- **Frontend (Lore Site):** WordPress, Custom Theme, PHP.
    
- **UI Components:** Web Awesome Pro 3.0 (Web Components), Font Awesome Pro (Icons).
    
- **Backend Services:**
    
    - **Music API:** Java (Spring Boot) - Scans file system, serves album/track data as JSON.
        
    - **Content API (Planned):** Python (FastAPI with SQLAlchemy) - Serves lore data from a database.
        
- **Database:** MariaDB (Hosted on a dedicated DigitalOcean Droplet - `elara.raggiesoft.com`).
    
- **Asset Hosting:** DigitalOcean Spaces (CDN via `assets.raggiesoft.com`).
    
- **Web Server:** Nginx (Self-managed on Ubuntu Droplet - `glowing-galaxy.raggiesoft.com`).
    
- **Security:** Cloudflare (DNS, potentially WAF), UFW, Fail2Ban, Bastion Host (`sentinel-star.raggiesoft.com`), SSH Key Authentication.
    
- **Version Control:** Git, GitHub.
    
- **Deployment:** Custom Bash script (`deploy.sh`) using `git pull`.
    

## Repository Structure & Version Control

**(When Completed)** This project employs **separate Git repositories** for each distinct website component to maintain modularity and ease deployment, hosted under the `raggiesoft` GitHub account:

- `raggiesoft/knox-main`: For `raggiesoftknox.com`
    
- `raggiesoft/knox-lore`: For `lore.raggiesoftknox.com` (WordPress theme, etc.)
    
- `raggiesoft/knox-pact`: For `pact.raggiesoftknox.com`
    
- `raggiesoft/knox-port`: For `port.raggiesoftknox.com`
    
- _(Repositories for Java API, Python API, etc. will also be separate)_
    

A separate, consolidated repository exists for course requirements (`ITD-210/course-project-raggiesoft`) which may utilize Git Submodules to reference these individual project repositories.

## Dual-Licensing

This project contains two distinct types of intellectual property, governed by separate licenses found in this repository:

1. **Narrative Content (CC BY-SA 4.0):**
    
    - **Applies to:** All story text, character profiles, world-building descriptions, lore entries, etc.
        
    - **License:** [Creative Commons Attribution-ShareAlike 4.0 International License](https://www.google.com/search?q=CONTENT_LICENSE.md "null") (`CC BY-SA 4.0`).
        
    - **Permissions:** You are free to share and adapt this creative work, even commercially, provided you give appropriate credit to Michael Ragsdale (raggiesoft), indicate if changes were made, and distribute your contributions under the same CC BY-SA 4.0 license.
        
2. **Source Code (MIT):**
    
    - **Applies to:** All PHP, JavaScript, CSS, Java, Python, Nginx configurations, deployment scripts, and other code used to build and display the websites and APIs.
        
    - **License:** [The MIT License](https://gemini.google.com/app/LICENSE.md "null").
        
    - **Permissions:** You are free to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the software, subject to the conditions outlined in the MIT License file.
        

## Getting Started / Development

_(Placeholder: Add instructions later on how to set up the development environment, run the different components, etc.)_

## Contributing

_(Placeholder: Add guidelines later if you plan to accept community contributions to the lore or code.)_

_This README reflects the project state and planned architecture as of October 2025._