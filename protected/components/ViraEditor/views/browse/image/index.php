<?php $logo = Yii::app()->theme->getLogoImage(); ?>

<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand" href="javascript:void(0)">
        <?= $logo ? CHtml::image(Yii::app()->theme->getImageUrl($logo, Yii::app()->site->name)) : 'ViraCMS' ?>
        <small><?= Yii::t('vira_editor', 'Image Browser') ?></small>
      </a>
      <ul class="pull-right nav">
        <li class="divider-vertical"></li>
        <li><a href="javascript:window.close()"><i class="icon-remove"></i> <?= Yii::t('common', 'Cancel') ?></a></li>
      </ul>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="span4">
      <h3><?= Yii::t('common', 'Filter') ?></h3>
      <div class="well">
        <form id="activate-filter">
          <input type="hidden" name="className" value="<?= CHtml::encode(isset($params[ 'className' ]) ? $params[ 'className' ] : 'Internal') ?>">
          <input type="hidden" name="primaryKey" value="<?= isset($params[ 'primaryKey' ]) ? $params[ 'primaryKey' ] : Yii::app()->user->id ?>">
          <div class="nav-header"><?= Yii::t('vira_editor', 'Scope') ?></div>
          <div class="filter-select">
            <?= CHtml::dropDownList('group', 'object', array(
              'object' => Yii::t('vira_editor', 'Current Object Images'),
              'model'  => Yii::t('vira_editor', 'This Section Images'),
              'all'    => Yii::t('vira_editor', 'All Images'),
              ), array('class' => 'input-block-level')) ?>
          </div>
          <div class="nav-header"><?= Yii::t('vira_editor', 'File Name') ?></div>
          <div class="filter-inputs">
            <input type="text" class="input-block-level" value="" name="name" />
          </div>
          <div class="filter-apply">
            <button type="submit" class="btn btn-info btn-small"><i class="icon-filter"></i> <?= Yii::t('common', 'Apply') ?></button>
            <a href="#" class="btn btn-small" id="clear-filter"><i class="icon-remove"></i> <?= Yii::t('common', 'Clear') ?></a>
          </div>
        </form>
      </div>
      <h3><?= Yii::t('vira_editor', 'Upload') ?></h3>
      <div class="row-fluid">
        <?php $this->widget('application.extensions.fineuploader.EFineUploader', array(
          'buttonClass'        => 'btn',
          'successClass'       => 'alert alert-success',
          'failClass'          => 'alert alert-error',
          'allowedExtensions'  => explode(',', Yii::app()->params[ 'allowImageTypes' ]),
          'acceptFiles'        => 'image/*',
          'sizeLimit'          => '',
          'minSizeLimit'       => 100,
          'uploadButton'       => '<i class="icon-upload"></i> ' . Yii::t('common', 'Select File'),
          'cancelButton'       => Yii::t('common', 'Cancel'),
          'retryButton'        => Yii::t('common', 'Retry'),
          'failUpload'         => Yii::t('common', 'Upload Failed'),
          'dragZone'           => Yii::t('common', 'Drop files here to upload'),
          'formatProgress'     => Yii::t('common', '{percent}% of {total_size}'),
          'waitingForResponse' => Yii::t('common', 'Processing...'),
          'template'           => '<div class="qq-uploader">' .
          '<pre class="qq-upload-drop-area span12"><span>{dragZoneText}</span></pre>' .
          '<a href="#" class="btn btn-success">{uploadButtonText}</a>' .
          '<ul class="qq-upload-list" style="margin-top: 10px; text-align: center;"></ul>' .
          '</div>',
          'onCompleteCallback' => "js:function(id, fileName, responseJSON) { $.fn.yiiListView.update('select-image',{ data: $('#activate-filter').serialize() }); }",
          'endpoint'           => $this->createUrl('/admin/content/editor/upload'),
          'inputName'          => 'filename',
          'params'             => array(
            'type'       => 'image',
            'siteID'     => isset($params[ 'siteID' ]) ? $params[ 'siteID' ] : Yii::app()->site->id,
            'className'  => isset($params[ 'className' ]) ? $params[ 'className' ] : 'Internal',
            'primaryKey' => isset($params[ 'primaryKey' ]) ? $params[ 'primaryKey' ] : Yii::app()->user->id,
          ),
        )); ?>
      </div>
    </div>
    <div class="span8">
      <div class="row-fluid">
        <div class="span8">
          <h3><?= Yii::t('vira_editor', 'Select Image') ?></h3>
        </div>
        <div class="span4">
          <div class="pull-right" style="margin-top: 15px;">
            <div class="btn-group" data-toggle="buttons-radio">
              <button id="select-block-view" type="button" class="btn<?= $view == 'block' ? ' active' : '' ?>"><i class="icon-th-large" style="font-size: 14px;"></i></button>
              <button id="select-list-view" type="button" class="btn<?= $view == 'list' ? ' active' : '' ?>"><i class="icon-th-list" style="font-size: 14px;"></i></button>
            </div>
          </div>
        </div>
      </div>
      <?php $this->renderPartial(
        ($viewFile = $this->getViewFile('thumbnails')) != false ? $viewFile :
          'application.components.ViraEditor.views.browse.image.thumbnails', array(
        'model'  => $model,
        'view'   => $view,
        'params' => $params,
        )
      ); ?>
    </div>
  </div>
</div>
