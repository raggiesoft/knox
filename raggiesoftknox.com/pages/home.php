<section
  class="position-relative d-flex align-items-center justify-content-center text-center text-white p-4"
  style="background-image: url('/assets/images/aerie-hold-atmospheric.jpg'); background-size: cover; background-position: center center; min-height: 60vh;"
  aria-labelledby="hero-title">

  <div class="position-absolute top-0 start-0 end-0 bottom-0" style="background-color: rgba(0, 0, 0, 0.6);"></div>

  <div class="position-relative z-1 d-flex flex-column align-items-center">
    <h1 id="hero-title" class="display-3 fw-bold text-uppercase" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.4);">
      Knox
    </h1>
    <h2 class="mt-4 fs-4 fw-light" style="max-width: 40rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.7);">
      On a planet of crushing gravity, they hunt a phantom. The reality is a family.
    </h2>

    <div class="mt-5"> <?php
        $props = [
          'href' => '#core-conflict',
          'text' => 'Explore the Universe',
          'variant' => 'pact', // Will map to btn-primary in button.php
          'icon' => 'fa-solid fa-arrow-down-to-line',
          'iconPosition' => 'before',
          'size' => 'large'
        ];
        include __DIR__ . '/../includes/components/button.php';
      ?>
    </div>
  </div>
</section>

<section id="core-conflict" class="py-5" aria-labelledby="core-conflict-title">
  <div class="container-lg">

    <h2 id="core-conflict-title" class="display-5 fw-bold text-center mb-5">
      One World. Three Truths.
    </h2>

    <div class="row g-4 row-cols-1 row-cols-md-3">

      <div class="col">
        <?php
          $props = [
            'imgSrc' => '/assets/images/pip-fantasy.jpg',
            'imgAlt' => 'Anya, Kael, and Pip cuddled together in their pod.',
            'fallbackText' => 'The Pact',
            'title' => 'The Pact',
            'description' => 'Follow the story of Anya, Kael, and Pip. From their hidden village, they wage a secret war of sabotage against the corporation that hunts them.',
            'buttonProps' => [
              'href' => '/concepts/pact/',
              'text' => 'Read The Pact',
              'variant' => 'pact', // -> maps to btn-primary
              'icon' => 'fa-solid fa-leaf',
              'fullWidth' => true
            ]
          ];
          include __DIR__ . '/../includes/components/card.php';
        ?>
      </div>

      <div class="col">
        <?php
          $props = [
            'imgSrc' => '/assets/images/port-telsus-atmospheric.jpg',
            'imgAlt' => 'The massive, industrial Axiom Spire of Port Telsus.',
            'fallbackText' => 'The Port',
            'title' => 'The Port',
            'description' => 'Experience the narrative from inside the Axiom. Follow the Auditors, Agents, and Whispers trying to enforce corporate law in a Green Hell.',
            'buttonProps' => [
              'href' => '/concepts/port/',
              'text' => 'Enter The Port',
              'variant' => 'axiom', // -> maps to btn-warning
              'icon' => 'fa-solid fa-industry',
              'fullWidth' => true
            ]
          ];
          include __DIR__ . '/../includes/components/card.php';
        ?>
      </div>

      <div class="col">
        <?php
          $props = [
            'imgSrc' => '/assets/images/aerie-hold-atmospheric.jpg',
            'imgAlt' => 'The vertical village of Aerie-Hold, built into miles-high trees.',
            'fallbackText' => 'The Lore',
            'title' => 'The Lore',
            'description' => 'Explore the Lore Bible. The official codex for the factions, locations, and technology of Telsus Minor. Powered by WordPress.',
            'buttonProps' => [
              'href' => 'https://lore.raggiesoftknox.com/',
              'text' => 'Open The Lore Bible',
              'variant' => 'neutral', // -> maps to btn-secondary
              'icon' => 'fa-solid fa-book-journal-whills',
              'fullWidth' => true
            ]
          ];
          include __DIR__ . '/../includes/components/card.php';
        ?>
      </div>

    </div>
  </div>
</section>

<section class="py-5 bg-about-section" aria-labelledby="premise-title">
  <div class="container" style="max-width: 45rem;">
    <div class="text-center">
        <h2 id="premise-title" class="display-5 fw-bold mb-4">
        About the Knox Universe
        </h2>
        <p class="fs-5 mb-3 text-body-secondary">
        On the oppressive, high-gravity jungle world of <strong>Telsus Minor</strong>, the monopolistic <strong>Axiom corporation</strong> rules through economic force. Their operations are plagued by a phantom saboteur they call <strong>"Knox"</strong>—a myth they believe to be a single, highly-trained ex-military operative.
        </p>
        <p class="fs-5 text-body-secondary">
        But the Axiom hunts a ghost of their own making. The reality is far more dangerous: <strong>Anya and Kael Rostova</strong>, young fraternal twins raised in the hidden canopy villages of "The Weave," wage a secret war using scavenged technology, improvised chemistry, and an intimate knowledge of the lethal environment the Axiom dismisses as the "Green Hell."
        </p>
    </div>
  </div>
</section>