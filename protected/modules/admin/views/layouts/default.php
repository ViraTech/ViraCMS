<?php $this->beginContent('layouts/html'); ?>
<div class="page">
  <div class="wrapper">
    <div class="content">
      <?php if (!Yii::app()->user->isGuest && Yii::app()->user->getType() == VAccountTypeCollection::ADMINISTRATOR) {
        $this->widget('bootstrap.widgets.TbNavbar', Yii::app()->getModule('admin')->getNavBarParams($this));
      } ?>
      <?= $content ?>
      <footer>
        <div class="container">
          <div class="row">
            <div class="span6">&copy; <a href="http://viracms.ru/" target="_blank">ViraCMS</a>, 2015</div>
            <div class="span6 text-right">
              <?= Yii::t('common', 'Production of {viratech}', array(
                '{viratech}' => CHtml::link(
                  Yii::t('common', 'Vira Technologies'), 'http://viratechnologies.ru/', array('target' => '_blank')
                )
              )) ?>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</div>
<?php $this->endContent(); ?>
