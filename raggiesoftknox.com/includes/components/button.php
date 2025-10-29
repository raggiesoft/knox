<?php
// --- Component: button.php ---

// 1. Get data from $props
$href = $props['href'] ?? '#';
$text = isset($props['text']) ? htmlspecialchars($props['text']) : 'Button';
$variant = $props['variant'] ?? 'secondary'; // Map pact->primary, axiom->warning, neutral->secondary
$icon = $props['icon'] ?? null;
$iconPosition = $props['iconPosition'] ?? 'after';
$fullWidth = $props['fullWidth'] ?? false;
$size = $props['size'] ?? 'medium';

// 2. Map your variants to Bootstrap theme colors
$btnClass = 'btn-secondary'; // Default to neutral/secondary
if ($variant === 'pact') $btnClass = 'btn-primary';
if ($variant === 'axiom') $btnClass = 'btn-warning';

// 3. Define Size classes
$sizeClass = ''; // Bootstrap default size
if ($size === 'large') $sizeClass = 'btn-lg';
if ($size === 'small') $sizeClass = 'btn-sm';

// 4. Define other classes
$widthClass = $fullWidth ? 'd-block w-100' : ''; // Use d-block for full width anchor buttons

// 5. Build Icon HTML
$iconHtml = '';
if ($icon) {
    // Bootstrap uses margin utilities (e.g., me-2 for margin-end)
    $iconMargin = ($text && $iconPosition === 'before') ? 'me-2' : 'ms-2';
    $iconHtml = "<i class='" . htmlspecialchars($icon) . " " . $iconMargin . "'></i>";
}

?>

<a href="<?php echo htmlspecialchars($href); ?>"
   class="btn <?php echo "$btnClass $sizeClass $widthClass"; ?> d-inline-flex align-items-center justify-content-center">

    <?php if ($iconPosition === 'before' && $icon) echo $iconHtml; ?>
    <?php if ($text): ?>
      <span><?php echo $text; ?></span>
    <?php endif; ?>
    <?php if ($iconPosition === 'after' && $icon) echo $iconHtml; ?>

</a>