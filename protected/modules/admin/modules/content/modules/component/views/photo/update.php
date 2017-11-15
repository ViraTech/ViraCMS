<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => strtolower(get_class($model)),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "if (submitForm()) { $('button[type=submit]',this).button('loading'); return true; } else { return false; }",
  ),
)); ?>
<?= $form->hiddenField($model, 'id') ?>
<?= $form->errorSummary($model) ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'update', array('model' => $model)) ?></legend>
</fieldset>
<?php $this->renderPartial('tabs'); ?>
<div class="tab-content">
  <div class="tab-pane fade active in" id="common">
    <fieldset>
      <?php if (Yii::app()->user->getAttribute('siteAccess') == 0): ?>
        <?= $form->dropDownListRow($model, 'siteID', CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'), array('class' => 'input-block-level')) ?>
        <?php $this->widget('application.extensions.eselect2.ESelect2', array(
          'selector' => '#' . CHtml::activeId($model, 'siteID'),
        )); ?>
      <?php else: ?>
        <?= $form->dropDownListRow($model, 'siteID', CHtml::listData(VSite::model()->findAll(), 'id', 'title'), array('empty' => '', 'placeholder' => 'На всех сайтах', 'class' => 'input-block-level')) ?>
        <?php $this->widget('application.extensions.eselect2.ESelect2', array(
          'selector' => '#' . CHtml::activeId($model, 'siteID'),
          'options' => array(
            'allowClear' => true,
          ),
        )); ?>
      <?php endif; ?>
      <?php if (count($languages) > 1): ?>
        <?= $form->dropDownListRow($model, 'languageID', CHtml::listData($languages, 'id', 'title'), array('class' => 'input-block-level')) ?>
        <?php $this->widget('application.extensions.eselect2.ESelect2', array(
          'selector' => '#' . CHtml::activeId($model, 'languageID'),
        )); ?>
      <?php else: ?>
        <?= $form->hiddenField($model, 'languageID') ?>
      <?php endif; ?>
      <?= $form->textFieldRow($model, 'title', array('class' => 'input-block-level')) ?>
      <?= $form->checkBoxRow($model, 'public') ?>
    </fieldset>
  </div>
  <div class="tab-pane fade" id="images">
    <div>
      <?php $this->widget('application.extensions.fineuploader.EFineUploader', array(
        'buttonClass' => 'btn',
        'loadingClass' => 'alert alert-info',
        'successClass' => 'alert alert-success',
        'failClass' => 'alert alert-error',
        'spinnerClass' => 'icon-spinner',
        'allowedExtensions' => explode(',', 'png,gif,jpg,jpeg'),
        'acceptFiles' => 'image/*',
        'sizeLimit' => '',
        'minSizeLimit' => 1000,
        'maxConnections' => 1,
        'uploadButton' => '<i class="icon-upload"></i> ' . Yii::t('admin.content.labels', 'Upload Images'),
        'cancelButton' => Yii::t('common', 'Cancel'),
        'retryButton' => Yii::t('common', 'Retry'),
        'failUpload' => Yii::t('common', 'Upload Failed'),
        'dragZone' => Yii::t('common', 'Drop files here to upload'),
        'formatProgress' => Yii::t('common', '{percent}% of {total_size}'),
        'waitingForResponse' => Yii::t('common', 'Processing...'),
        'template' => '<div class="qq-uploader row-fluid">' .
        '<pre class="qq-upload-drop-area span12"><span>{dragZoneText}</span></pre>' .
        '<a href="#" class="btn btn-success">{uploadButtonText}</a>' .
        '<ul class="qq-uploads unstyled" style="margin: 10px 0; text-align: left;"></ul>' .
        '</div>',
        'fileTemplate' => '<li class="alert alert-info">' .
        '<div class="qq-progress-bar progress-bar"></div>' .
        '<span class="icon-spinner icon-spin" style="margin-right: 5px;"></span>' .
        '<span class="qq-upload-finished"></span>' .
        '<span class="qq-upload-file"></span>' .
        '<span class="qq-upload-size"></span>' .
        '<a class="qq-upload-cancel btn btn-mini btn-danger" href="#">{cancelButtonText}</a>' .
        '<a class="qq-upload-retry" href="#">{retryButtonText}</a>' .
        '<span class="qq-upload-status-text">{statusText}</span>' .
        '</li>',
        'listClass' => 'qq-uploads',
        'onCompleteCallback' => "newFileUploaded",
        'endpoint' => $this->createUrl('upload'),
        'inputName' => 'filename',
      )); ?>
    </div>
    <div class="clearfix">
      <ul id="images-list" class="dragsort">
      <?php foreach ($this->getImages($model) as $image): ?>
        <?php $this->renderPartial('image', array(
          'image' => $image,
        )); ?>
      <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="tab-pane fade" id="history">
    <fieldset>
      <?php $this->widget('application.widgets.core.VHistoryWidget', array(
        'model' => $model,
        'form' => $form,
      )); ?>
    </fieldset>
  </div>
</div>
<div class="help-block"><?= Yii::t('common', '{sign} - this fields is required.', array(
  '{sign}' => '<span class="required">*</span>',
)) ?></div>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
<?php
$this->widget('application.modules.admin.modules.content.modules.component.extensions.dragsort.EDragSort', array(
  'itemSelector' => '.dragsort > li',
  'dragSelector' => '.dragsort > li > img',
));
?>
<?php $this->cs->registerCss('DragSort', "
ul.dragsort {
  margin: 0;
}
ul.dragsort > li {
  margin-bottom: 10px;
  outline: solid 2px rgba(255,255,255,0.25);
  background-color: transparent;
  height: 108px;
  overflow: hidden;
  float: none;
  padding: 10px;
  position: relative;
}
.dragsort > li img {
  float: left;
  margin: -10px 20px -10px -10px;
  cursor: move;
}
.dragsort > li.deleted img {
  opacity: 0.2;
}
.dragsort > li > .image-controls {
  position: absolute;
  left: 276px;
  right: 20px;
  bottom: 10px;
}
.dragsort > li .control-delete,
.dragsort > li.deleted .control-restore {
  display: inline-block;
}
.dragsort > li.deleted .control-delete,
.dragsort > li .control-restore {
  display: none;
}
.dragsort > *[data-placeholder] {
  background-color: rgba(127,127,127,0.5);
}
"); ?>
<?php $this->cs->registerScript('ImageDeleteHandler', "
$(document).on('click','a.control-delete',function(e) {
  e.preventDefault();
  var li = $(this).closest('li'),
    flag = li.find('.image-remove-flag');

  flag.val(1);
  li.addClass('deleted');
});
"); ?>
<?php $this->cs->registerScript('ImageRestoreHandler', "
$(document).on('click','a.control-restore',function(e) {
  e.preventDefault();
  var li = $(this).closest('li'),
    flag = li.find('.image-remove-flag');

  flag.val(0);
  li.removeClass('deleted');
});
"); ?>
<?php $this->cs->registerScript('UploadedFileHandler', "
function newFileUploaded(id,name,responseJSON) {
  if (typeof responseJSON.success != 'undefined') {
    $('#images-list').append(responseJSON.html);
  }
  $('.qq-uploader .qq-uploads li.alert-success').fadeOut('slow',function(){
    $(this).remove();
  });
}
", CClientScript::POS_END); ?>
<?php $this->cs->registerScript('FormSubmitHandler', "
function submitForm() {
  var form = $('#" . strtolower(get_class($model)) . "');
  var position = 0;
  $('#images-list > li').each(function()
  {
    var self = $(this);
    var id = self.data('image-id');
    appendHiddenField(form,'images[' + id + '][sort]',position);
    position++;
  });
  return true;
}
function appendHiddenField(form,name,value) {
  form.append($('<input />',{
    type: 'hidden',
    name: name,
    value: value
  }));
}
", CClientScript::POS_END); ?>
<?php $this->cs->registerScriptFile($this->coreScriptUrl('dialogs')); ?>
