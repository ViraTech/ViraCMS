<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => strtolower(get_class($model)),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return submitForm();",
  ),
)); ?>
<?= CHtml::activeHiddenField($model, 'id') ?>
<?= $form->errorSummary(array_merge(array($model), $model->getL10nModels())) ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'update', array('model' => $model)) ?></legend>
</fieldset>
<?php $this->renderPartial('tabs'); ?>
<div class="tab-content">
  <div class="tab-pane fade active in" id="general">
    <?= $form->dropDownListRow($model, 'siteID', CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'), array('class' => 'input-block-level')) ?>
    <?php $this->widget('ext.eselect2.ESelect2', array(
      'selector' => '#' . CHtml::activeId($model, 'siteID'),
      'events' => array(
        'change' => "js:function(e) {
  var siteID = e.val;
  $.ajax({
    cache: false,
    type: 'get',
    dataType: 'json',
    url: '" . $this->createUrl('ajax') . "',
    data: { ajax: 'site', site: siteID },
    success: function(jdata)
    {
      for (var languageID in jdata.sitemap) {
        var sitemap = jdata.sitemap[languageID], element = $('#page-' + languageID);
        element.find('option[value!=\"\"]').remove();
        for (var i in sitemap) {
          var option = $('<option>',{ value: i, text: sitemap[i] });
          element.append(option);
        }
      }
    }
  });
}",
    ))); ?>
    <?= $form->checkboxRow($model, 'public') ?>
    <ul class="nav nav-tabs" id="languages">
      <?php foreach ($languages as $language): ?>
        <li<?= $language->id == Yii::app()->getLanguage() ? ' class="active"' : '' ?>>
          <a data-toggle="tab" href="#l10n-<?= $language->id ?>"><?= $language->title ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
    <div class="tab-content">
    <?php foreach ($languages as $language): ?>
      <div class="tab-pane fade<?= $language->id == Yii::app()->getLanguage() ? ' active in' : '' ?>" id="l10n-<?= $language->id ?>">
      <?php $l10n = $model->getL10nModel($language->id, false); ?>
      <?= $form->textFieldRow($l10n, 'title', array('class' => 'input-block-level', 'id' => get_class($l10n) . '_' . $language->id . '_title', 'name' => get_class($l10n) . '[' . $language->id . '][title]')) ?>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
  <div class="tab-pane fade" id="carousel">
    <?php $this->widget('application.extensions.fineuploader.EFineUploader', array(
      'buttonClass' => 'btn',
      'successClass' => 'alert alert-success',
      'failClass' => 'alert alert-error',
      'allowedExtensions' => explode(',', Yii::app()->params['allowImageTypes']),
      'acceptFiles' => 'image/*',
      'sizeLimit' => '',
      'minSizeLimit' => 100,
      'maxConnections' => 1,
      'uploadButton' => '<i class="icon-upload"></i> ' . Yii::t('admin.content.labels', 'Upload Images'),
      'cancelButton' => Yii::t('common', 'Cancel'),
      'retryButton' => Yii::t('common', 'Retry'),
      'failUpload' => Yii::t('common', 'Upload Failed'),
      'dragZone' => Yii::t('common', 'Drop files here to upload'),
      'formatProgress' => Yii::t('common', '{percent}% of {total_size}'),
      'waitingForResponse' => Yii::t('common', 'Processing...'),
      'template' => '<div class="qq-uploader">' .
      '<pre class="qq-upload-drop-area span12"><span>{dragZoneText}</span></pre>' .
      '<a href="#" class="btn btn-success">{uploadButtonText}</a>' .
      '<ul class="qq-uploads unstyled" style="margin: 10px 0; text-align: left;"></ul>' .
      '</div>',
      'listClass' => 'qq-uploads',
      'onCompleteCallback' => "newFileUploaded",
      'onErrorCallback' => "errorFileUpload",
      'endpoint' => $this->createUrl('upload'),
      'inputName' => 'filename',
    )); ?>
    <div class="clearfix">
      <ul id="images" class="dragsort row-fluid">
      <?php foreach ($this->getImages($model) as $image): ?>
        <?php $this->renderPartial('image', array(
          'id' => $image->imageID,
          'img' => $image->image->getUrl(CarouselController::IMAGE_PREVIEW_WIDTH, CarouselController::IMAGE_PREVIEW_HEIGHT, true),
          'model' => $image,
          'languages' => $languages,
        )); ?>
      <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="tab-pane fade" id="history">
    <?php $this->widget('application.widgets.core.VHistoryWidget', array(
      'model' => $model,
      'form' => $form,
    )); ?>
  </div>
</div>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', $model->isNewRecord ? 'Create' : 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>

<span id="image-delete" class="fade">
  <a href="#" class="btn btn-danger"><i class="icon-trash"></i></a>
</span>

<ul class="hide" id="removed-images">
</ul>

<?php if (($removed = Yii::app()->request->getParam('removed', array())) !== array()): ?>
  <?php foreach ($removed as $id): ?>
    <input type="hidden" name="removed[]" value="<?= $id ?>" />
  <?php endforeach; ?>
<?php endif; ?>

<?php $this->endWidget(); ?>

<div class="modal hide fade" id="image-description">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?= Yii::t('admin.content.titles', 'Image Caption') ?></h3>
  </div>
  <div class="modal-body">
    <ul class="nav nav-tabs" id="languages">
    <?php foreach ($languages as $language): ?>
      <li<?= $language->id == Yii::app()->getLanguage() ? ' class="active"' : '' ?>>
        <a data-toggle="tab" href="#description-<?= $language->id ?>"><?= $language->title ?></a>
      </li>
    <?php endforeach; ?>
    </ul>
    <div class="tab-content">
    <?php foreach ($languages as $language): ?>
      <div class="tab-pane fade<?= $language->id == Yii::app()->getLanguage() ? ' active in' : '' ?>" id="description-<?= $language->id ?>">
        <div class="form-vertical" id="image-l10n-form-<?= $language->id ?>">
          <label for="title-<?= $language->id ?>"><?= Yii::t('admin.content.labels', 'Caption Title') ?></label>
          <?= CHtml::textField('title-' . $language->id, '', array('class' => 'input-block-level', 'data-attribute' => 'title')) ?>
          <label for="caption-<?= $language->id ?>"><?= Yii::t('admin.content.labels', 'Caption Text') ?></label>
          <?= CHtml::textField('caption-' . $language->id, '', array('class' => 'input-block-level', 'data-attribute' => 'caption')) ?>
          <label for="page-<?= $language->id ?>"><?= Yii::t('admin.content.labels', 'Link To The Page') ?></label>
          <?= CHtml::dropDownList('page-' . $language->id, '', Yii::app()->siteMap->getMapItems($model->siteID), array('class' => 'input-block-level', 'empty' => '', 'data-attribute' => 'pageID')) ?>
          <label for="url-<?= $language->id ?>"><?= Yii::t('admin.content.labels', 'Link To The URL') ?></label>
          <?= CHtml::textField('url-' . $language->id, '', array('class' => 'input-block-level', 'data-attribute' => 'url')) ?>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-link" data-dismiss="modal"><?= Yii::t('common', 'Cancel') ?></a>
    <a href="#" class="btn btn-primary" id="update-description"><?= Yii::t('common', 'OK') ?></a>
  </div>
</div>

<ul class="hide" id="new-image">
<?php $this->renderPartial('image', array(
  'id' => '',
  'img' => '',
  'model' => null,
  'languages' => $languages,
)); ?>
</ul>

<?php $this->renderPartial('description', array(
  'id' => 'image-description-block',
  'l10n' => false,
)); ?>

<?php $this->widget('application.modules.admin.modules.content.modules.component.extensions.dragsort.EDragSort'); ?>

<?php $this->cs->registerCss('DragSort', "
#image-delete {
  position: absolute;
  left: -20px;
  top: -20px;
}
#image-delete a {
  display: block;
  width: 20px;
  height: 20px;
  padding: 4px;
  text-align: center;
}
.legend-regular,
.legend-primary {
  float: left;
  display: block;
  line-height: 30px;
  font-weight: bold;
  padding: 0 10px;
  margin-left: 10px;
}
.legend-regular {
  outline: solid 2px #cccccc;
}
.legend-primary {
  outline: solid 2px green;
}
.dragsort li {
  min-height: 320px;
  max-height: 320px;
  min-width: 250px;
  text-align: center;
  margin-bottom: 15px;
  line-height: 250px;
}
.dragsort li > .img-holder {
  padding: 5px;
}
.dragsort li img {
  max-height: 250px;
  padding: 0;
  margin: 0;
}
.dragsort li span {
  display: block;
  width: auto;
  text-align: left;
  padding: 5px 10px;
  line-height: 18px;
}
.dragsort li span > a {
  margin: 0 15px 0 0;
  border-bottom: 1px dashed;
}
.dragsort li span > a:hover {
  text-decoration: none;
  border-bottom: 1px solid;
}
.dragsort li span > label {
  padding: 0;
  margin: 0;
}
.dragsort li span > label > input {
  margin: 0;
  padding: 0;
}
.dragsort > *[data-placeholder] {
  margin-left: 20px;
}
"); ?>

<?php $this->cs->registerScript('ImageDeleteHandler', "
var trashTimer, currentImage;
function trashTimerRun() {
  trashTimer = setTimeout(function() {
    $('#image-delete').
      removeClass('in').
      css({
        left: -20,
        top: -20
      });
  },50);
}
$(document).on('mouseenter','.dragsort > li',function(e) {
  clearTimeout(trashTimer);
  var self = $(this);
  $('#image-delete').
    css({
      left: self.offset().left + self.outerWidth() - 25,
      top: self.offset().top - 5
    }).
    addClass('in');
  currentImage = self;
});
$(document).on('mouseleave','.dragsort > li',function(e) {
  trashTimerRun();
});
$(document).on('mouseenter','#image-delete',function(e) {
  clearTimeout(trashTimer);
});
$(document).on('mouseleave','#image-delete',function(e) {
  trashTimerRun();
});
$('#image-delete').click(function(e) {
	e.preventDefault();
	var confirm = viraCoreConfirm('" . Yii::t('admin.content.titles', 'Image Delete Confirmation') . "','" . Yii::t('admin.content.messages', 'Are you sure to delete image?') . "',function() {
      currentImage.detach().appendTo($('#removed-images'));
      currentImage = null;
      confirm.modal('hide');
	},function() {
      currentImage = null;
	},{
      ok: '" . Yii::t('common', 'OK') . "',
      cancel: '" . Yii::t('common', 'Cancel') . "'
	});
});
"); ?>

<?php $this->cs->registerScript('DescriptionHandler', "
var currentBlock;
var currentLanguageID = '" . Yii::app()->getLanguage() . "';
var emptyDescription = '" . Yii::t('admin.content.messages', 'No caption given yet') . "';
$(document).on('click','#images a.descr',function(e) {
  var li = $(this).closest('li');
  currentBlock = li;
  li.find('ul[data-language-id]').each(function()
  {
    var self = $(this),
      languageID = self.data('language-id'),
      l10n = $('#image-l10n-form-' + languageID);

    self.find('li').each(function()
    {
      var attribute = $(this).data('attribute'),
        value = $(this).text();
      l10n.find('[data-attribute=\"' + attribute + '\"]').val(value);
    });
  });
});
$('#update-description').click(function(e) {
  e.preventDefault();
  var modal = $('#image-description');
  if (currentBlock) {
    var description = '';
    $('ul[data-language-id]',currentBlock).remove();
    modal.find('.tab-pane').each(function() {
      var self = $(this);
      var languageID = self.attr('id').split('-')[1];
      var block = $('#image-description-block').clone();
      block.removeAttr('id');
      block.attr('data-language-id',languageID);
      self.find('input,select,textarea').each(function() {
        var attribute = $(this).data('attribute'),
          value = $(this).val();
        console.log(attribute,value);
        block.find('[data-attribute=\"' + attribute + '\"]').text(value);
        if (languageID == currentLanguageID && attribute == 'title') {
          description = value;
        }
      });
      currentBlock.append(block);
    });
    $('span > a',currentBlock).text(description || emptyDescription).toggleClass('muted',!description);
  }
  modal.modal('hide');
});
$('#image-description').on('hidden',function() {
  var self = $(this);
  $('input',self).val('');
});
"); ?>

<?php $this->cs->registerScript('UploadedFileHandler', "
function newFileUploaded(id,name,responseJSON) {
  if (typeof responseJSON.success != 'undefined') {
    var li = $('#new-image li:eq(0)').clone();
    li.find('img').attr({
      src: responseJSON.url,
      'data-image-id': responseJSON.id
    });
    $('#images').append(li);
  }
}
function errorFileUpload(id,fileName,reason) {
  viraCoreAlert('error',reason,'center');
}
", CClientScript::POS_END); ?>

<?php $this->cs->registerScript('FormSubmitHandler', "
function submitForm() {
  var form = $('#" . strtolower(get_class($model)) . "');
  $('#removed-images > li img').each(function() {
    appendHiddenField(form,'removed[]',$(this).data('image-id'));
  });
  var position = 0;
  $('#images > li').each(function() {
    var self = $(this);
    var id = self.find('img').eq(0).data('image-id');
    appendHiddenField(form,'image[' + id + '][position]',position);
    self.find('ul').each(function() {
      var languageID = $(this).data('language-id');
      if (languageID) {
        $('li',this).each(function() {
          var self = $(this);
          var attr = self.data('attribute');
          appendHiddenField(form,'image[' + id + '][caption][' + languageID + '][' + attr + ']',self.text());
        });
      }
    });
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
