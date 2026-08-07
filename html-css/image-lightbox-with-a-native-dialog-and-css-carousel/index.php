<?php

declare(strict_types=1);

// Single source of truth. Each entry maps to a picsum.photos image id, so
// thumbnails, full-size slides and their indexes stay in sync.
$images = [
    ['id' => 1015, 'alt' => 'Person overlooking a mountain lake'],
    ['id' => 1025, 'alt' => 'Pug wrapped in a blanket'],
    ['id' => 1039, 'alt' => 'Waterfall through a forest'],
    ['id' => 1043, 'alt' => 'City skyline at dusk'],
    ['id' => 1050, 'alt' => 'Skyscraper seen from below'],
    ['id' => 1062, 'alt' => 'Rocks on a beach shoreline'],
    ['id' => 1074, 'alt' => 'Lion resting in the grass'],
    ['id' => 1084, 'alt' => 'Aerial view of a coastline'],
];

$thumbUrl = fn (int $id): string => "https://picsum.photos/id/{$id}/400/300";
$fullUrl = fn (int $id): string => "https://picsum.photos/id/{$id}/1600/1000";
$e = fn (string $value): string => htmlspecialchars($value, ENT_QUOTES);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pure HTML Image Gallery (PHP)</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="page-header">
    <h1>Image Gallery</h1>
    <p>Click a thumbnail to open the lightbox. Page through with the arrows or dots.</p>
  </header>

  <main class="gallery" aria-label="Photo thumbnails">
    <?php foreach ($images as $index => $image): ?>
      <button class="thumb" type="button" command="show-modal" commandfor="lightbox" data-index="<?= $index ?>">
        <img src="<?= $e($thumbUrl($image['id'])) ?>" alt="<?= $e($image['alt']) ?>" loading="lazy">
      </button>
    <?php endforeach; ?>
  </main>

  <dialog id="lightbox" class="lightbox" closedby="any" aria-label="Image viewer">
    <form method="dialog">
      <button class="lightbox__close" aria-label="Close">&times;</button>
    </form>
    <ul class="carousel">
      <?php foreach ($images as $index => $image): ?>
        <li class="carousel__item" id="slide-<?= $index ?>">
          <img src="<?= $e($fullUrl($image['id'])) ?>" alt="<?= $e($image['alt']) ?>">
        </li>
      <?php endforeach; ?>
    </ul>
  </dialog>

  <script>
    // Opening and closing are native: the thumbnails are command="show-modal"
    // invokers and the close button uses <form method="dialog">. The only JS left
    // records which thumbnail was clicked, then jumps the carousel to that slide
    // once the dialog has actually opened (its toggle event).
    const dialog = document.querySelector('#lightbox');
    let pendingIndex = 0;

    document.querySelector('.gallery').addEventListener('click', (event) => {
      const thumb = event.target.closest('.thumb');
      if (thumb) {
        pendingIndex = thumb.dataset.index;
      }
    });

    dialog.addEventListener('toggle', (event) => {
      if (event.newState !== 'open') {
        return;
      }

      document.querySelector(`#slide-${pendingIndex}`)
        ?.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'instant' });
    });
  </script>
</body>
</html>
