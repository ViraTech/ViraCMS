<?= CHtml::openTag('a',array_filter(array(
  'href' => $image['url'],
  'class' => 'thumbnail cb-grp-' . $this->id,
  'title' => $image['title'],
  'data-full-image' => isset($image['image']) ? $image['image'] : '',
))) ?>
  <?= CHtml::image($image['thumbnail'],$image['title'],array('class' => 'img-responsive')) ?>
</a>
