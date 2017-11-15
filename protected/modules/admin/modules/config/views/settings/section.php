<?php $model->section = $section; ?>
<?php foreach ($model->getSectionCategories() as $id => $category): ?>
<div class="well">
  <div class="nav-header" style="padding: 3px 0 10px 0;"><?= $category['label'] ? Yii::t((isset($category['translate']) ? $category['translate'] : 'admin.settings.titles'), $category['label']) : $model->currentMenuLabel() ?></div>
  <p><strong class="text-warning"><?= empty($category['note']) ? '' : Yii::t('admin.titles', $category['note']) ?></strong></p>
  <?php foreach ($model->getItems($category) as $item): ?>
    <div class="control-group">
      <label class="control-label"><?= Yii::t((isset($item['translate']) ? $item['translate'] : 'admin.settings.labels'), $item['label']) ?><br /><small class="muted"><?= CHtml::encode(Yii::t('admin.labels', $item['type'])) ?></small></label>
      <div class="controls">
        <?php $this->renderPartial('update/' . $item['type'], $model->getItemParams($item)); ?>
        <?php if (isset($item['hint']) && $item['hint']): ?>
          <p class="help-block"><?= Yii::t((isset($item['translate']) ? $item['translate'] : 'admin.settings.hints'), $item['hint']) ?></p>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php endforeach; ?>
