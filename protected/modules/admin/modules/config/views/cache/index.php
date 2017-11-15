<h2 class="page-header"><?= Yii::t('admin.content.titles', 'Site Cache') ?></h2>
<?php $this->widget('bootstrap.widgets.TbAlert'); ?>
<form method="POST" class="form-horizontal" onsubmit="$('button[type=submit]', this).button('loading'); return true;">
  <fieldset>
    <div class="control-group">
      <div class="controls">
        <label class="checkbox">
          <input type="checkbox" name="config"> <?= Yii::t('admin.content.cache', 'Flush configuration cache') ?>
        </label>
      </div>
    </div>
    <div class="control-group">
      <div class="controls">
        <label class="checkbox">
          <input type="checkbox" name="app"> <?= Yii::t('admin.content.cache', 'Flush application cache') ?>
        </label>
      </div>
    </div>
    <?php if (function_exists('apc_clear_cache')): ?>
      <div class="control-group">
        <div class="controls">
          <label class="checkbox">
            <input type="checkbox" name="opcode"> <?= Yii::t('admin.content.cache', 'Flush opcode cache') ?>
          </label>
        </div>
      </div>
    <?php endif; ?>
    <div class="control-group">
      <div class="controls">
        <label class="checkbox">
          <input type="checkbox" name="image"> <?= Yii::t('admin.content.cache', 'Flush images cache') ?>
        </label>
      </div>
    </div>
    <div class="control-group">
      <div class="controls">
        <label class="checkbox">
          <input type="checkbox" name="assets"> <?= Yii::t('admin.content.cache', 'Flush assets cache') ?>
        </label>
      </div>
    </div>
  </fieldset>

  <div class="form-actions">
    <button type="submit" class="btn btn-danger" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('admin.content.cache', 'Flushing...')) ?>"><i class="icon-trash"></i> <?= Yii::t('admin.content.cache', 'Flush') ?></button>
    <a href="<?= $this->createUrl('/admin/default/index') ?>" class="btn btn-link"><?= Yii::t('common', 'Cancel') ?></a>
  </div>
</form>
