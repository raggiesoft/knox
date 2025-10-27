<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
    <h1 class="text-5xl font-extrabold text-heading">404</h1>
    <p class="mt-4 text-2xl font-semibold text-body">
      Page Not Found
    </p>
    <p class="mt-2 text-lg text-body-muted">
      The page you were looking for could not be found.
    </p>
    <div class="mt-8">
      <?php
        // --- Use the button component ---
        $props = [
          'href' => '/',
          'text' => 'Return Home',
          'variant' => 'pact',
          'icon' => 'fa-solid fa-home',
          'iconPosition' => 'before',
          'size' => 'large'
        ];
        include __DIR__ . '/../includes/components/button.php';
      ?>
    </div>
</div>

