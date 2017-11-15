<div id="<?= $this->id ?>">
  <?= CHtml::dropDownList($this->pageVar, $this->value, $this->getDropdownData(), $this->htmlOptions) ?>
</div>
