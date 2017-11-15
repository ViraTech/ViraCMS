<ul class="thumbnails">
<?php foreach ($images as $image): ?>
  <li class="<?= $this->thumbnailCssClass ?>">
    <?php $this->render($this->imageView,array(
      'image' => $image,
      'owner' => $owner,
    )) ?>
  </li>
<?php endforeach; ?>
</ul>
