<li class="row-fluid" style="line-height: 45px; min-height: 45px; max-height: auto; padding-bottom: 7px; margin-bottom: 5px; border-bottom: 1px solid #cccccc;">
  <div class="span3">
    <a href="#" class="select-video">
      <div style="min-height: 45px; line-height: 45px;">
        <img style="max-height: 45px; width: auto;" src="<?= Yii::app()->editor->getImageUrl('flash.png') ?>" alt=""  data-src="<?= Yii::app()->storage->getFileUrl($data->path) ?>" />
      </div>
    </a>
  </div>
  <div class="span5">
    <?= $data->filename ?>
  </div>
  <div class="span4">
    <?= Yii::app()->format->formatSize($data->size) ?>
  </div>
</li>
