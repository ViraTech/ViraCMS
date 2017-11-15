<?php $this->cs->registerScriptFile($this->coreScriptUrl('dialogs')); ?>
<h2><?= Yii::t('admin.content.titles', 'Site "{title}" Map', array('{title}' => $site->title)) ?></h2>
<?php $this->widget('bootstrap.widgets.TbAlert') ?>
<?php if (file_exists(Yii::getPathOfAlias($alias) . '.php')): ?>
  <?php if (count($sites) > 1): ?>
    <?= CHtml::dropDownList('site', $site->id, $sites, array('id' => 'site-selector', 'class' => 'input-block-level')) ?>
    <?php $this->widget('ext.eselect2.ESelect2', array(
      'selector' => '#site-selector',
    )); ?>
  <?php endif; ?>
  <?php $this->renderPartial('sitemap', array(
    'model' => $model,
    'site' => $site,
    'alias' => $alias,
  )); ?>
<?php else: ?>
  <?= Yii::t('admin.content.errors', 'Sorry, site map widget "{widget}" either not installed or not found.', array('{widget}' => $widget)) ?>
<?php endif; ?>
<?php $this->cs->registerScript('SiteSelectorFunctions', "
$('#site-selector').on('change',function(e){ document.location = ('" . $this->createUrl('map', array('site' => '999999999')) . "').replace('999999999',$(this).val()); });
"); ?>
