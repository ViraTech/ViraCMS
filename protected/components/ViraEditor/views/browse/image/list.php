<li class="row-fluid" style="line-height: 45px; min-height: 45px; max-height: auto; padding-top: 7px; margin-top: 5px; border-top: 1px solid #cccccc;">
  <div class="span2">
    <a href="<?= $data->getUrl() ?>" class="select-image" data-model-id="<?= $data->id ?>">
      <div style="min-height: 45px; line-height: 45px;">
        <img style="max-height: 45px; width: auto;" src="<?= $data->getUrl(45, 45, 1) ?>" alt="">
      </div>
    </a>
  </div>
  <div class="span5">
    <?= $data->filename ?>
  </div>
  <div class="span3">
    <?= Yii::app()->format->formatSize($data->size) ?>
  </div>
  <div class="span2">
    <?= $data->width ?>&times;<?= $data->height ?>
  </div>
</li>
