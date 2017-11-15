<div class="container">
  <div class="row-fluid">
    <div class="span12">
      <div class="btn-group" id="vira-editor-mode-select" data-toggle="buttons-radio">
        <a href="#" class="btn btn-small btn-primary active disabled" data-feature="default" data-mode="view"><i class="icon-eye-open"></i> <?= Yii::t('vira_editor', 'Preview') ?></a>
        <a href="#" class="btn btn-small btn-primary disabled" data-feature="designer" data-mode="row"><i class="icon-move"></i> <?= Yii::t('vira_editor', 'Rows') ?></a>
        <a href="#" class="btn btn-small btn-primary disabled" data-feature="designer" data-mode="block"><i class="icon-move"></i> <?= Yii::t('vira_editor', 'Blocks') ?></a>
        <a href="#" class="btn btn-small btn-primary disabled" data-feature="editor" data-mode="edit"><i class="icon-pencil"></i> <?= Yii::t('vira_editor', 'Edit') ?></a>
      </div>
      <div class="btn-group" id="vira-editor-width-select" data-toggle="buttons-radio">
        <a href="#" class="btn btn-small btn-info active disabled" data-width="100%">100%</a>
        <a href="#" class="btn btn-small btn-info disabled" data-width="980px">940px</a>
        <a href="#" class="btn btn-small btn-info disabled" data-width="847px">768px</a>
        <a href="#" class="btn btn-small btn-info disabled" data-width="640px">640px</a>
        <a href="#" class="btn btn-small btn-info disabled" data-width="480px">480px</a>
      </div>
      <div class="btn-group" id="vira-editor-language-select">
        <a href="#" class="btn btn-small btn-success disabled dropdown-toggle" data-toggle="dropdown" data-lang-id="<?= Yii::app()->getLanguage() ?>" data-lang="<?= Yii::app()->getLanguage() ?>"><span><?= VLanguageHelper::getLanguageTitle() ?></span> <i class="icon-angle-down"></i></a>
        <ul class="dropdown-menu">
          <?php foreach (VLanguageHelper::getLanguages() as $language): ?>
            <li><a href="#" data-lang-id="<?= $language->id ?>" data-lang="<?= $language->id ?>"><?= $language->title ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="btn-group">
        <a href="<?= $this->createUrl('config', array('id' => is_array($model->primaryKey) ? implode(',', $model->primaryKey) : $model->primaryKey)) ?>" class="btn btn-small btn-warning"><i class="icon-cog"></i> <?= Yii::t('vira_editor', 'Configure') ?></a>
      </div>
      <div class="pull-right">
        <a href="#" class="btn btn-small btn-primary disabled" id="vira-editor-save-contents"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></a>
        <a href="<?= $this->createUrl('index') ?>" class="btn btn-small btn-link" id="vira-editor-cancel-update"><?= Yii::t('common', 'Cancel') ?></a>
      </div>
    </div>
  </div>
</div>

<div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" id="vira-editor-select-row-template">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?= Yii::t('vira_editor', 'Select Row Template') ?></h3>
  </div>
  <div class="modal-body">
    <div class="row-fluid">
      <?php foreach (VContentTemplate::model()->orderByName()->findAll() as $i => $template): ?>
        <?php if ($i != 0 && $i % 3 == 0): ?>
        </div>
        <div class="row-fluid">
        <?php endif; ?>
        <div class="span4 text-center">
          <a href="#" class="btn btn-mini btn-block" style="margin-bottom: 5px;" data-template="<?= CHtml::encode(strtr($template->template, array("\r\n" => '', "\r" => '', "\n" => ''))) ?>">
            <?= $template->title ?>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="modal-footer">
    <a class="btn btn-link" data-dismiss="modal"><?= Yii::t('common', 'Cancel') ?></a>
  </div>
</div>

<div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" id="vira-editor-select-widget">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?= Yii::t('vira_editor', 'Select Widget') ?></h3>
  </div>
  <div class="modal-body">
    <ul class="nav nav-tabs">
      <?php foreach (Yii::app()->widgetFactory->categories as $category): ?>
        <?php if (Yii::app()->widgetFactory->getWidgetCount($category[ 'category' ]) < 1) {
          continue;
        } ?>
        <li<?= $category[ 'category' ] == 'core' ? ' class="active"' : '' ?>>
          <a href="#widgets-<?= $category[ 'category' ] ?>" data-toggle="tab" tabindex="-1">
        <?= Yii::t($category[ 'translate' ], $category[ 'name' ]) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="tab-content">
<?php foreach (Yii::app()->widgetFactory->categories as $category): ?>
            <?php if (Yii::app()->widgetFactory->getWidgetCount($category[ 'category' ]) < 1) {
              continue;
            } ?>
        <div class="tab-pane hide fade<?= $category[ 'category' ] == 'core' ? ' in active' : '' ?>" id="widgets-<?= $category[ 'category' ] ?>">
          <div class="row-fluid">
            <?php foreach (Yii::app()->widgetFactory->getWidgets($category[ 'category' ]) as $i => $widget): ?>
    <?php if ($i != 0 && $i % 3 == 0): ?>
              </div>
              <div class="row-fluid">
              <?php endif; ?>
              <div class="span4">
                <a href="#" style="margin-bottom: 5px;" class="btn btn-mini btn-block" data-widget-id="<?= $widget[ 'id' ] ?>"><?= $widget[ 'title' ] ?></a>
              </div>
  <?php endforeach; ?>
          </div>
        </div>
<?php endforeach; ?>
    </div>
  </div>
  <div class="modal-footer">
    <a class="btn btn-link" data-dismiss="modal"><?= Yii::t('common', 'Cancel') ?></a>
  </div>
</div>

<div class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" id="vira-editor-widget-config">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?= Yii::t('vira_editor', 'Widget Configuration') ?></h3>
  </div>
  <div class="modal-body">

  </div>
  <div class="modal-footer">
    <button class="btn btn-link" data-dismiss="modal" aria-hidden="true"><?= Yii::t('common', 'Cancel') ?></button>
    <button class="btn btn-primary" data-action="submit"><?= Yii::t('common', 'OK') ?></button>
  </div>
</div>

<?php $this->cs->registerScript('EditorVariables', "
var viraEditorApi,
	siteID = '{$model->siteID}';
", CClientScript::POS_HEAD); ?>

<?php $this->cs->registerScript('EditorIframeInit', "
$(window).on('resize',function(e){
  var wrapper = $('#iframe-wrapper');
  wrapper.height($(window).height() - wrapper.offset().top - 5);
}).trigger('resize');
", CClientScript::POS_READY); ?>
