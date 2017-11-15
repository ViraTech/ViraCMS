<li class="span2" style="min-height: 160px; max-height: 160px;">
  <a href="<?= $data->getUrl() ?>" class="thumbnail select-image" data-model-id="<?= $data->id ?>">
    <div style="min-height: 120px; text-align: center; line-height: 120px;">
      <img style="max-height: 120px; width: auto;" src="<?= $data->getUrl(120, 120, 1) ?>" alt="">
    </div>
  </a>
  <div class="caption" style="padding-top: 5px; max-height: 40px; overflow: hidden;">
    <small>
      <?= $data->filename ?>,
      <?= Yii::app()->format->formatSize($data->size) ?>,
      <?= $data->width ?>&times;<?= $data->height ?>
    </small>
  </div>
</li>
