<?php $images = $this->getImages(); ?>
<?php if (count($images) > 0): ?>
  <?php Yii::app()->photoViewer->renderImages($this, $images, $this->width, $this->height, array_filter(array(
    'thumbnailCssClass' => $this->rows
  ))); ?>
<?php endif; ?>
