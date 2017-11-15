<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => strtolower(get_class($model)),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<?= $form->errorSummary(array_merge(array($model), $model->getL10nModels())) ?>
<?= $form->hiddenField($model, 'id') ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'update', array('model' => $model)) ?></legend>
</fieldset>
<ul class="nav nav-tabs" id="page">
  <li class="active"><a href="#page-config" data-toggle="tab"><?= Yii::t('admin.content.titles', 'Configuration') ?></a></li>
  <li><a href="#page-l10n" data-toggle="tab"><?= Yii::t('admin.content.titles', 'Localization') ?></a></li>
  <li><a href="#page-history" data-toggle="tab"><?= Yii::t('admin.content.titles', 'History') ?></a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane fade active in" id="page-config">
    <fieldset>
      <?= $form->dropDownListRow($model, 'siteID', CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'), array('empty' => '', 'class' => 'input-block-level')) ?>
      <?php $this->widget('application.extensions.eselect2.ESelect2', array(
        'selector' => '#' . CHtml::activeId($model, 'siteID'),
        'events' => array(
          'change' => "js:function(e) {
  var siteID = e.val;
  $.ajax({
    cache: false,
    type: 'get',
    dataType: 'json',
    data: { site: siteID },
    url: '" . $this->createUrl('ajax') . "',
    data: { site: siteID },
    success: function(jdata)
    {
      var layouts = $('#" . CHtml::activeId($model, 'layoutID') . "');
      layouts.find('option[value!=\"\"]').remove();
      for (var i in jdata.layouts) {
        layouts.append($('<option>',{ value: i}).text(jdata.layouts[i]));
      }
    }
  });
}",
        ),
      )); ?>
      <?= $form->dropDownListRow($model, '_mcv', $this->getMcvList(), array('class' => 'input-block-level')) ?>
      <?php $this->widget('application.extensions.eselect2.ESelect2', array(
        'selector' => '#' . CHtml::activeId($model, '_mcv'),
      )); ?>
      <?= $form->dropDownListRow($model, 'layoutID', CHtml::listData(VSiteLayout::model()->from($model->siteID)->findAll(), 'id', 'title'), array('class' => 'input-block-level')) ?>
    </fieldset>
  </div>
  <div class="tab-pane fade" id="page-l10n">
    <ul class="nav nav-tabs">
    <?php foreach ($languages as $language): ?>
        <li <?= $language->id == Yii::app()->getLanguage() ? 'class="active"' : '' ?>><a href="#l10n_<?= $language->id ?>" data-toggle="tab"><?= $language->title ?></a></li>
    <?php endforeach; ?>
    </ul>
    <div class="tab-content">
    <?php foreach ($languages as $language): ?>
      <div class="tab-pane fade in<?= $language->id == Yii::app()->getLanguage() ? ' active' : '' ?>" id="l10n_<?= $language->id ?>">
        <fieldset>
          <?php $l10n = $model->getL10nModel($language->id, false); ?>
          <?= $form->textFieldRow($l10n, 'name', array('class' => 'input-block-level', 'id' => get_class($l10n) . '_' . $language->id . '_name', 'name' => get_class($l10n) . '[' . $language->id . '][name]')) ?>
          <?= $form->textFieldRow($l10n, 'title', array('class' => 'input-block-level', 'id' => get_class($l10n) . '_' . $language->id . '_title', 'name' => get_class($l10n) . '[' . $language->id . '][title]')) ?>
          <?= $form->textAreaRow($l10n, 'keywords', array('class' => 'input-block-level', 'rows' => 3, 'id' => get_class($l10n) . '_' . $language->id . '_keywords', 'name' => get_class($l10n) . '[' . $language->id . '][keywords]')) ?>
          <?= $form->textAreaRow($l10n, 'description', array('class' => 'input-block-level', 'rows' => 3, 'id' => get_class($l10n) . '_' . $language->id . '_description', 'name' => get_class($l10n) . '[' . $language->id . '][description]')) ?>
        </fieldset>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
  <div class="tab-pane fade" id="page-history">
    <?php $this->widget('application.widgets.core.VHistoryWidget', array(
      'model' => $model,
      'form' => $form,
    )); ?>
  </div>
</div>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
