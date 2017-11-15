<h3 class="page-header"><?= Yii::t('admin.titles', 'Cache Configuration') ?></h3>
<div class="alert alert-info">
  <strong><?= Yii::t('admin.messages', 'Please note!') ?></strong>
  <?= Yii::t('admin.messages', 'New cache configuration will be tested and applied immediately.') ?>
</div>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'horizontal',
  'id' => 'cacheConfig',
)); ?>
<?php if ($engine && isset($engines[$engine])): ?>
  <?= $form->errorSummary($engines[$engine]['form']) ?>
<?php endif; ?>
<div class="control-group">
  <label class="control-label"><?= Yii::t('admin.titles', 'Cache Engine') ?></label>
  <div class="controls">
    <div class="radio-group">
    <?php foreach ($engines as $class => $config): ?>
          <?php if (!empty($config['skip'])) {
            continue;
          } ?>
        <label class="radio inline<?= $config['available'] ? '' : ' disabled' ?>">
          <input type="radio" name="engine" value="<?= $class ?>"<?= $config['available'] ? '' : ' disabled' ?><?= ($engine ? $class == $engine : $config['active']) ? ' checked' : '' ?> />
          <?= $config['title'] ?>
        </label>
    <?php endforeach; ?>
    </div>
  </div>
</div>
<div class="tab-content">
<?php foreach ($engines as $class => $config): ?>
  <?php if (!empty($config['skip'])) continue; ?>
  <div class="tab-pane fade <?= ($engine ? $class == $engine : $config['active']) ? ' active in' : '' ?>" id="cache_<?= $class ?>">
    <fieldset>
      <legend><?= $config['title'] ?></legend>
      <?php if (!empty($config['form']) && ($attrs = $config['form']->formAttributes) != array()): ?>
        <?php $f = $config['form']; ?>
        <?php $hints = $f->attributeHints(); ?>
        <?php foreach ($attrs as $name => $options): ?>
          <?php if ($options['type'] == 'text'): ?>
            <?= $form->textFieldRow($f, $name, array('class' => !empty($options['width']) ? $options['width'] : 'span7', 'hint' => isset($hints[$name]) ? $hints[$name] : null)) ?>
          <?php elseif ($options['type'] == 'boolean'): ?>
            <?= $form->checkBoxRow($f, $name, array('hint' => isset($hint[$name]) ? $hint[$name] : null)) ?>
          <?php endif ?>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="muted"><?= Yii::t('admin.messages', 'No configuration parameters available.') ?></div>
      <?php endif; ?>
    </fieldset>
  </div>
<?php endforeach; ?>
</div>
<div class="form-actions">
  <button type="submit" class="btn btn-success" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>">
    <i class="icon-save"></i> <?= Yii::t('common', 'Apply') ?>
  </button>
</div>
<?php $this->endWidget(); ?>
<?php $this->cs->registerCss('RadioGroupCss', ".radio-group{margin-left:-10px;}.radio-group .radio.inline{margin-left:10px;margin-bottom:10px;}"); ?>
<?php $this->cs->registerScript('RadioGroupHandler', "
$('#cacheConfig .radio-group input').on('click',function(e) {
  if ($(this).attr('disabled')) {
    e.preventDefault();
    return;
  }
  var form = $('#cacheConfig');
  form.find('.tab-pane.active').removeClass('active in');
  form.find('#cache_' + $(this).val()).addClass('active in');
});
$('#cacheConfig').on('submit',function(e) {
  $(this).find('button[type=submit]').button('loading');
  return true;
});
"); ?>
