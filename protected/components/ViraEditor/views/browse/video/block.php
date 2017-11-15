<li class="span2" style="min-height: 160px; max-height: 160px;">
  <a href="#" class="thumbnail select-video">
    <div style="min-height: 120px; text-align: center; line-height: 120px;">
      <img style="max-height: 120px; width: auto;" src="<?= Yii::app()->editor->getImageUrl('flash.png') ?>" alt="" data-src="<?= Yii::app()->storage->getFileUrl($data->path) ?>" />
    </div>
  </a>
  <div class="caption" style="padding-top: 5px; max-height: 40px; overflow: hidden;">
    <small><?= $data->filename ?>, <?= Yii::app()->format->formatSize($data->size) ?></small>
  </div>
</li>
