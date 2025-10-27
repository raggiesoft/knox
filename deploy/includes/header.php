<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Knox: A Narrative Universe'); ?></title>

    <meta name="description" content="Explore Telsus Minor, a sci-fi universe where the reality of a phantom saboteur named Knox is a family fighting a corrupt corporation.">

    <script src="https://kit.fontawesome.com/ec060982d4.js" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <?php
        $themeName = preg_replace('/[^a-zA-Z0-9_-]/', '', $currentPageTheme ?? 'aerie-hold');
        $themeFilePath = "/assets/css/theme-" . $themeName . ".css";
    ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($themeFilePath); ?>" />

    <link rel="stylesheet" href="/assets/css/bootstrap-overrides.css" />

</head>
<body class="d-flex flex-column min-vh-100">

    <a href="#main-content" class="visually-hidden-focusable">Skip to Main Content</a>

    <header class="sticky-top shadow-sm">
      <nav class="navbar navbar-expand-md bg-body-tertiary border-bottom">
        <div class="container-lg">

          <a class="navbar-brand fw-bold text-uppercase" href="/" aria-label="Knox Universe Home">Knox</a>

          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="mainNavbar">
            <?php
              // $currentHeaderMenu path is set by elara.php
              if (isset($currentHeaderMenu) && file_exists($currentHeaderMenu)) {
                  include $currentHeaderMenu; // This file should contain the <ul>...</ul> structure
              } else {
                  // Fallback or error message if file is missing
                  echo '<ul class="navbar-nav ms-auto mb-2 mb-md-0"><li class="nav-item"><span class="nav-link text-danger">Error: Menu definition not found.</span></li></ul>';
              }
            ?>
          </div>

        </div>
      </nav>
    </header>