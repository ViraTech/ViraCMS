<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => 'form-' . strtolower(get_class($model)),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<?= $form->errorSummary($model) ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'config', array('model' => $model)) ?></legend>
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab-layout" data-toggle="tab"><?= Yii::t('admin.content.labels', 'Layout') ?></a></li>
    <li><a href="#tab-body" data-toggle="tab"><?= Yii::t('admin.content.labels', 'Body') ?></a></li>
    <li><a href="#tab-link" data-toggle="tab"><?= Yii::t('admin.content.labels', 'Links') ?></a></li>
    <li><a href="#tab-css" data-toggle="tab"><?= Yii::t('admin.content.labels', 'CSS') ?></a></li>
    <li><a href="#tab-meta" data-toggle="tab"><?= Yii::t('admin.content.labels', 'Meta Tags') ?></a></li>
    <li><a href="#tab-history" data-toggle="tab"><?= Yii::t('admin.content.titles', 'History') ?></a></li>
  </ul>
  <div class="tab-content" style="overflow: visible;">
    <div class="tab-pane hide fade in active" id="tab-layout" style="padding: 20px;">
      <?php if ($model->isNewRecord): ?>
        <?= $form->dropDownListRow($model, 'siteID', CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'), array('class' => 'input-block-level')) ?>
        <?php $this->widget('ext.eselect2.ESelect2', array(
          'selector' => '#' . CHtml::activeId($model, 'siteID'),
        )); ?>
          <?php else: ?>
            <?= $form->uneditableRow($model, 'siteID', array('class' => 'input-block-level', 'value' => $model->site ? $model->site->title : $model->siteID)) ?>
      <?php endif; ?>
      <div class="row-fluid">
        <div class="span3">
          <?= $form->textFieldRow($model, 'id', array_merge(array('class' => 'input-block-level'), $model->isNewRecord ? array() : array('readonly' => 'readonly'))) ?>
        </div>
        <div class="span9">
          <div class="control-group">
            <label class="control-label"><?= Yii::t('admin.content.titles', 'Page Areas') ?></label>
            <div class="controls">
              <span class="uneditable-textarea input-block-level"><?= implode(', ', $areas) ?></span>
            </div>
          </div>
        </div>
      </div>
      <?= $form->textFieldRow($model, 'title', array('class' => 'input-block-level')) ?>
      <?= $form->checkBoxRow($model, 'default') ?>
    </div>
    <div class="tab-pane hide fade" id="tab-body" style="padding: 20px;">
      <div class="row-fluid">
        <div class="span4">
          <?php $this->widget('application.extensions.minicolors.EMiniColors', array(
            'model' => $model,
            'attribute' => 'bodyTextColor',
            'scriptPosition' => CClientScript::POS_END,
          )); ?>
        </div>
        <div class="span4">
          <?php $this->widget('application.extensions.minicolors.EMiniColors', array(
            'model' => $model,
            'attribute' => 'bodyBackgroundColor',
            'scriptPosition' => CClientScript::POS_END,
            'settings' => array(
              'opacity' => true,
            ),
          )); ?>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span6">
          <div class="control-group">
            <?= $form->labelEx($model, 'bodyBackgroundImage') ?>
            <?= $form->hiddenField($model, 'bodyBackgroundImage') ?>
            <div class="controls">
              <div style="margin-bottom: 10px;">
                <a href="#" onclick="return selectBackgroundImage()" class="btn btn-small btn-success"><i class="icon-picture"></i> <?= Yii::t('admin.content.labels', 'Select Image') ?></a>
                <a href="#" class="btn btn-small btn-danger fade<?= $model->backgroundImage ? ' in' : '' ?>" id="clear-background-image" onclick="clearBackgroundImage()"><i class="icon-trash"></i> <?= Yii::t('admin.content.labels', 'Clear') ?></a>
              </div>
              <div style="max-height: 300px; overflow: auto;">
              <?php if ($model->backgroundImage): ?>
                <?= CHtml::image(Yii::app()->storage->getFileUrl($model->backgroundImage->path), '', array('id' => 'background-image-preview')) ?>
              <?php else: ?>
                <span class="muted" id="background-image-preview"><em><?= Yii::t('admin.content.messages', 'Background image is not set.') ?></em></span>
              <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="span6">
          <div class="control-group">
            <?= $form->labelEx($model, 'favIconImage') ?>
            <?= $form->hiddenField($model, 'favIconImage') ?>
            <div class="controls">
              <div style="margin-bottom: 10px;">
                <a href="#" onclick="return selectFavIconImage()" class="btn btn-small btn-success"><i class="icon-picture"></i> <?= Yii::t('admin.content.labels', 'Select Image') ?></a>
                <a href="#" class="btn btn-small btn-danger fade<?= $model->iconImage ? ' in' : '' ?>" id="clear-favicon-image" onclick="clearFavIconImage()"><i class="icon-trash"></i> <?= Yii::t('admin.content.labels', 'Clear') ?></a>
              </div>
              <div style="max-height: 300px; overflow: auto;">
              <?php if ($model->iconImage): ?>
                <?= CHtml::image(Yii::app()->storage->getFileUrl($model->iconImage->path), '', array('id' => 'favicon-image-preview')) ?>
              <?php else: ?>
                <span class="muted" id="favicon-image-preview"><em><?= Yii::t('admin.content.messages', 'Favourite icon image is not set.') ?></em></span>
              <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane hide fade" id="tab-link" style="padding: 20px;">
      <div class="row-fluid">
        <div class="span4">
          <?php $this->widget('application.extensions.minicolors.EMiniColors', array(
            'model' => $model,
            'attribute' => 'linkColor',
            'scriptPosition' => CClientScript::POS_END,
          )); ?>
        </div>
        <div class="span4">
          <?php $this->widget('application.extensions.minicolors.EMiniColors', array(
            'model' => $model,
            'attribute' => 'linkHoverColor',
            'scriptPosition' => CClientScript::POS_END,
          )); ?>
        </div>
        <div class="span4">
          <?php $this->widget('application.extensions.minicolors.EMiniColors', array(
            'model' => $model,
            'attribute' => 'linkVisitedColor',
            'scriptPosition' => CClientScript::POS_END,
          )); ?>
        </div>
      </div>
    </div>
    <div class="tab-pane hide fade" id="tab-css" style="padding: 20px;">
      <div class="control-group ">
        <?= $form->labelEx($model, 'styleOverride') ?>
        <div class="controls">
          <?php $this->widget('application.extensions.ace.EAce', array(
            'model' => $model,
            'attribute' => 'styleOverride',
            'mode' => 'css',
            'htmlOptions' => array(
              'style' => 'width: 100%; height: 300px;',
            ),
          )); ?>
        </div>
      </div>
    </div>
    <div class="tab-pane hide fade" id="tab-meta" style="padding: 20px;">
      <div class="control-group ">
        <?= $form->labelEx($model, 'metaTags') ?>
        <div class="controls">
          <?php $this->widget('application.extensions.ace.EAce', array(
            'model' => $model,
            'attribute' => 'metaTags',
            'mode' => 'html',
            'htmlOptions' => array(
              'style' => 'width: 100%; height: 300px;',
            ),
          )); ?>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="tab-history">
      <?php $this->widget('application.widgets.core.VHistoryWidget', array(
        'model' => $model,
        'form' => $form,
      )); ?>
    </div>
  </div>
</fieldset>
<div class="form-actions">
  <div class="btn-group">
    <button class="btn btn-primary" type="submit" name="update" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= $model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save') ?></button>
    <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i class="icon-angle-down"></i></a>
    <ul class="dropdown-menu">
      <li><a href="#" onclick="return submitFormAndEdit();"><?= Yii::t('admin.content.labels', 'Save & Update Contents') ?></a></li>
    </ul>
  </div>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
<?php $this->cs->registerScript('SelectImageHandler', "
var backgroundImageSelector, favIconImageSelector;
function selectBackgroundImage(model,source) {
  if (typeof model === 'undefined') {
    var width = window.screen.availWidth;
    var height = window.screen.availHeight;
    var wWidth = width > 980 ? 980 : width;
    var wHeight = height > 500 ? 500 : height;
    var left = ((width - wWidth)/2).toFixed(0);
    var top = ((height - wHeight)/2).toFixed(0);
    backgroundImageSelector = window.open('" . $this->createUrl(Yii::app()->editor->imageBrowserAction, array('mode' => 'layout', 'func' => 'selectBackgroundImage')) . "','','width=' + wWidth + ',height=' + wHeight + ',left=' + left + ',top=' + top + ',menubar=no,status=no');
  }
  else {
    $('#" . CHtml::activeId($model, 'bodyBackgroundImage') . "').val(model);
    $('#background-image-preview').replaceWith($('<img />',{ src: source, id: 'background-image-preview' }));
    $('#clear-background-image').addClass('in');
  }
  return false;
}
function clearBackgroundImage() {
  $('#clear-background-image').removeClass('in');
  $('#" . CHtml::activeId($model, 'bodyBackgroundImage') . "').val('');
  $('#background-image-preview').replaceWith($('<span />',{ 'class': 'muted', id: 'background-image-preview' }).html('<em>" . CHtml::encode(Yii::t('admin.content.messages', 'Background image is not set.')) . "</em>'));
}
function selectFavIconImage(model,source) {
  if (typeof model === 'undefined') {
    var width = window.screen.availWidth;
    var height = window.screen.availHeight;
    var wWidth = width > 980 ? 980 : width;
    var wHeight = height > 500 ? 500 : height;
    var left = ((width - wWidth)/2).toFixed(0);
    var top = ((height - wHeight)/2).toFixed(0);
    favIconImageSelector = window.open('" . $this->createUrl(Yii::app()->editor->imageBrowserAction, array('mode' => 'layout', 'func' => 'selectFavIconImage')) . "','','width=' + wWidth + ',height=' + wHeight + ',left=' + left + ',top=' + top + ',menubar=no,status=no');
  }
  else {
    $('#" . CHtml::activeId($model, 'favIconImage') . "').val(model);
    $('#favicon-image-preview').replaceWith($('<img />',{ src: source, id: 'favicon-image-preview' }));
    $('#clear-favicon-image').addClass('in');
  }
  return false;
}
function clearFavIconImage() {
  $('#clear-favicon-image').removeClass('in');
  $('#" . CHtml::activeId($model, 'favIconImage') . "').val('');
  $('#favicon-image-preview').replaceWith($('<span />',{ 'class': 'muted', id: 'favicon-image-preview' }).html('<em>" . CHtml::encode(Yii::t('admin.content.messages', 'Favourite icon image is not set.')) . "</em>'));
}
", CClientScript::POS_END); ?>
<?php $this->cs->registerScript('ClearColorHandler', "
$('.minicolors').each(function() {
  var self = $(this);
  var clear = $('<a></a>',{ href: '#', style: 'position: relative; left: -20px; top: -3px; color: #ccc;' }).html($('<i></i>').addClass('icon-remove-sign'));
  clear.on('mouseenter',function(e){ $(this).css('color','#fff'); });
  clear.on('mouseleave',function(e){ $(this).css('color','#ccc'); });
  clear.on('click',function(e) { e.preventDefault(); var input = $(this).prev('input:first'); input.val(''); input.trigger('keyup'); });
  self.find('input').css('padding-right','20px').after(clear);
});
"); ?>
<?php $this->cs->registerScript('FormSubmitFunctions', "
function submitFormAndEdit() {
  var form = $('#form-" . strtolower(get_class($model)) . "');
  form.append($('<input />',{ type: 'hidden', name: 'edit', value: '1' }));
  form.submit();
  return false;
}
", CClientScript::POS_END); ?>
<?php $this->cs->registerScript('FormSubmitHandler', "
$('#form-" . strtolower(get_class($model)) . "').on('submit',function(e) {
  var bgColor = $('#" . CHtml::activeId($model, 'bodyBackgroundColor') . "');
  if (bgColor.val()) {
    bgColor.val(bgColor.minicolors('rgbaString'));
  }
});
"); ?>
