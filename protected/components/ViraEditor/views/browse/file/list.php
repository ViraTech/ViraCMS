<div class="row-fluid" style="line-height: 30px; min-height: 30px; max-height: auto; margin-bottom: 0; padding-top: 5px; margin-top: 5px; border-top: 1px solid #cccccc;">
  <div class="span8">
    <a href="#" class="select-file" data-src="<?= Yii::app()->storage->getFileUrl($data->path) ?>">
      <?= $data->filename ?>
    </a>
  </div>
  <div class="span2">
    <span style="float: left; font-size: 11px; line-height: 13px; background-color: rgba(19, 146, 233, 0.48); color: #f0f0f0; padding: 3px 5px; margin: 7px 0; max-width: 99%; word-wrap: break-word;"><?= $data->mime ?></span>
  </div>
  <div class="span2">
    <?= Yii::app()->format->formatSize($data->size) ?>
  </div>
</div>
