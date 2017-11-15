<?= $form->errorSummary($model) ?>
<?= $form->checkboxRow($model, 'plain') ?>
<?= $form->dropDownListRow($model, 'menuID', CHtml::listData(VCustomMenu::model()->forSite(Yii::app()->site->id)->findAll(), 'id', 'title'), array(
  'class' => 'input-block-level',
  'empty' => '',
  'onchange' => "$('#" . CHtml::activeId($model, 'parentID') . "').val('');",
)) ?>
<?= $form->dropDownListRow($model, 'parentID', Yii::app()->siteMap->getMapItems(Yii::app()->site->id), array(
  'class' => 'input-block-level',
  'empty' => '',
  'onchange' => "$('#" . CHtml::activeId($model, 'menuID') . "').val('');",
)) ?>
<?= $form->checkboxRow($model, 'children') ?>
<?= $form->textFieldRow($model, 'cssClass') ?>
