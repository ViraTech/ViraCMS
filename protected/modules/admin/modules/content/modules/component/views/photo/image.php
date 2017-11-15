<li data-image-id="<?= $image->imageID ?>">
  <input type="hidden" class="image-remove-flag" name="images[<?= $image->imageID ?>][deleteFlag]" value="<?= (int) $image->deleteFlag ?>">
  <?php if ($image->image): ?>
    <img src="<?= call_user_func_array(array($image->image, 'getUrl'), array(PhotoController::IMAGE_PREVIEW_WIDTH, PhotoController::IMAGE_PREVIEW_HEIGHT, 1)) ?>" />
  <?php endif; ?>
  <div class="image-controls">
    <div class="control-group">
      <label class="control-label"><?= Yii::t('admin.content.titles', 'Photo Title') ?></label>
      <div class="controls">
        <input type="text" class="input-block-level" name="images[<?= $image->imageID ?>][title]" value="<?= CHtml::encode($image->title) ?>">
      </div>
    </div>
    <a href="#image-remove" class="btn btn-small btn-danger control-delete"><i class="icon-trash"></i> <?= Yii::t('admin.content.labels', 'Delete Image') ?></a>
    <a href="#image-restore" class="btn btn-small btn-primary control-restore"><i class="icon-trash"></i> <?= Yii::t('admin.content.labels', 'Restore Image') ?></a>
  </div>
</li>
