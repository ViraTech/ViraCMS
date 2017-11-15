<?php $this->beginContent('layouts/html'); ?>
<div class="page">
  <div class="wrapper">
    <div class="content no-footer">
      <?php if (!Yii::app()->user->isGuest && Yii::app()->user->getType() == VAccountTypeCollection::ADMINISTRATOR) {
        $this->widget('bootstrap.widgets.TbNavbar', Yii::app()->getModule('admin')->getNavBarParams($this));
      } ?>
      <?= $content ?>
    </div>
  </div>
</div>
<?php $this->endContent(); ?>
