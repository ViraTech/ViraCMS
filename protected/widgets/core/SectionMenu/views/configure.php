<?= $form->errorSummary($model) ?>
<?= $form->dropDownListRow($model, 'parentPageID', Yii::app()->siteMap->getMapItems(Yii::app()->site->id), array('class' => 'input-block-level', 'empty' => '')) ?>
<?= $form->checkboxRow($model, 'addParentPage') ?>
<?= $form->dropDownListRow($model, 'menuType', $model->getMenuTypes(), array('class' => 'input-block-level')) ?>
