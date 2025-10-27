<div class="container py-5" style="max-width: 50rem;"> <h1 class="display-5 fw-bold border-bottom pb-2 mb-4"> About This Project
    </h1>

    <div class="fs-5"> <p class="lead mb-4"> The <strong>Knox</strong> narrative universe is a multi-layered story presented across several interconnected websites, all built on a custom, polyglot technology stack. This "welcome mat" site (<code>raggiesoftknox.com</code>) serves as the central hub, introducing the world and directing visitors to the different facets of the story.
      </p>

      <h2 class="h3 fw-bold pt-4 mb-3"> Narrative Architecture
      </h2>
      <p class="mb-4 text-body-secondary">The story is split into distinct parts, each with its own dedicated subdomain:</p> <ul class="list-unstyled mb-4"> <li class="mb-3"> <strong class="h5 d-block mb-1">https://raggiesoftknox.com</strong> <p class="text-body-secondary ps-3"> The main "front door" and central hub you are on now. Built with a custom, lightweight PHP router (<code>elara.php</code>) and styled with Bootstrap for layout and a custom theme for colors.
          </p>
        </li>
        <li class="mb-3">
          <strong class="h5 d-block mb-1">https://lore.raggiesoftknox.com</strong>
          <p class="text-body-secondary ps-3">
            The "Lore Bible," which hosts all the detailed world-building, character profiles, and faction backstories. This site is powered by WordPress with a custom-built theme to match the brand.
          </p>
        </li>
        <li class="mb-3">
          <strong class="h5 d-block mb-1">https://pact.raggiesoftknox.com</strong>
          <p class="text-body-secondary ps-3">
            The narrative told from the perspective of the twins, Anya and Kael Rostova, and their companion Pippa.
          </p>
        </li>
        <li class="mb-3">
          <strong class="h5 d-block mb-1">https://port.raggiesoftknox.com</strong>
          <p class="text-body-secondary ps-3">
            The narrative told from the point of view of the Axiom, the oppressive corporate entity ruling Telsus Minor.
          </p>
        </li>
      </ul>

      <h2 class="h3 fw-bold pt-4 mb-3">
        Technology Stack
      </h2>
      <p class="mb-4 text-body-secondary">This project is built on a custom microservices architecture. The core components are:</p>

      <ul class="list-unstyled mb-4">
        <li class="mb-3">
          <strong class="h5 d-block mb-1">Frontend:</strong>
          <p class="text-body-secondary ps-3">
            The static and narrative sites are custom PHP applications. Styling is managed by <a href="https://getbootstrap.com/" target="_blank" rel="noopener noreferrer" class="link-primary text-decoration-none">Bootstrap</a>, with icons potentially from <a href="https://webawesome.com/" target="_blank" rel="noopener noreferrer" class="link-primary text-decoration-none">Web Awesome</a> or Font Awesome.
          </p>
        </li>
        <li class="mb-3">
          <strong class="h5 d-block mb-1">Backend Services:</strong>
          <p class="text-body-secondary ps-3">
            The stack includes a <a href="https://spring.io/projects/spring-boot" target="_blank" rel="noopener noreferrer" class="link-primary text-decoration-none">Java (Spring Boot)</a> API for serving music and a planned <a href="https://fastapi.tiangolo.com/" target="_blank" rel="noopener noreferrer" class="link-primary text-decoration-none">Python (FastAPI)</a> API for dynamic content.
          </p>
        </li>
        <li class="mb-3">
          <strong class="h5 d-block mb-1">Database:</strong>
          <p class="text-body-secondary ps-3">
            A dedicated <a href="https://mariadb.org/" target="_blank" rel="noopener noreferrer" class="link-primary text-decoration-none">MariaDB</a> droplet hosts the data.
          </p>
        </li>
        <li class="mb-3">
          <strong class="h5 d-block mb-1">Hosting & Deployment:</strong>
          <p class="text-body-secondary ps-3">
            All sites are self-managed on a <a href="https://www.digitalocean.com/" target="_blank" rel="noopener noreferrer" class="link-primary text-decoration-none">DigitalOcean</a> Ubuntu droplet (<code>glowing-galaxy</code>) running an <a href="https://www.nginx.com/" target="_blank" rel="noopener noreferrer" class="link-primary text-decoration-none">Nginx</a> web server.
          </p>
        </li>
        <li class="mb-3">
          <strong class="h5 d-block mb-1">CDN & DNS:</strong>
          <p class="text-body-secondary ps-3">
            Static assets (CSS, images) are served from <a href="https://www.digitalocean.com/products/spaces" target="_blank" rel="noopener noreferrer" class="link-primary text-decoration-none">DigitalOcean Spaces</a> (<code>assets.raggiesoft.com</code>), with DNS and security managed by <a href="https://www.cloudflare.com/" target="_blank" rel="noopener noreferrer" class="link-primary text-decoration-none">Cloudflare</a>.
          </p>
        </li>
        <li class="mb-3">
          <strong class="h5 d-block mb-1">Version Control:</strong>
          <p class="text-body-secondary ps-3">
            All code is version-controlled with <a href="https://git-scm.com/" target="_blank" rel="noopener noreferrer" class="link-primary text-decoration-none">Git</a> and hosted on GitHub, deployed via custom Bash scripts.
          </p>
        </li>
      </ul>
    </div>
</div>