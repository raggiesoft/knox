<?php
// --- Component: card.php ---

// 1. Get data from $props
$imgSrc = $props['imgSrc'] ?? null; // Make null default
$imgAlt = $props['imgAlt'] ?? 'Card image';
$fallbackText = $props['fallbackText'] ?? 'Image';
$title = isset($props['title']) ? htmlspecialchars($props['title']) : 'Card Title';
$description = isset($props['description']) ? htmlspecialchars($props['description']) : 'Card description goes here.';
$buttonProps = $props['buttonProps'] ?? null;
$variant = $buttonProps['variant'] ?? 'secondary'; // Get variant for placeholder theme

// Create placeholder URL (using updated theme colors)
$bgColor = '6c757d'; // secondary/neutral grey
if ($variant === 'pact') $bgColor = '005A5A'; // primary/weave
if ($variant === 'axiom') $bgColor = 'A8491A';// warning/rust
$textColor = 'FFFFFF';
$placeholderUrl = "https://placehold.co/600x400/{$bgColor}/{$textColor}?text=" . urlencode($fallbackText);

?>

<div class="card h-100 shadow-sm">

  <?php if ($imgSrc): ?>
    <img src="<?php echo htmlspecialchars($imgSrc); ?>"
         class="card-img-top"
         alt="<?php echo htmlspecialchars($imgAlt); ?>"
         onerror="this.onerror=null;this.src='<?php echo $placeholderUrl; ?>';">
  <?php else: ?>
     <img src="<?php echo $placeholderUrl; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($imgAlt); ?>">
  <?php endif; ?>

  <div class="card-body d-flex flex-column">
    <h3 class="card-title h5">
      <?php echo $title; ?>
    </h3>
    <p class="card-text text-body-secondary flex-grow-1">
      <?php echo $description; ?>
    </p>

    <div class="mt-auto pt-3">
      <?php
        if ($buttonProps) {
          // Pass the $buttonProps down to the button component
          $props = $buttonProps;
          include __DIR__ . '/button.php';
        }
      ?>
    </div>
  </div>

</div>