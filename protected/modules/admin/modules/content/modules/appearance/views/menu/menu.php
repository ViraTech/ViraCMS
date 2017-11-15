<div id="menu" class="sitemap">
  <?php $this->renderPartial(($view ? 'view/' : 'edit/') . 'items', array('items' => $model->menu)); ?>
</div>
<?php if (!$view): ?>
  <?php $this->widget('application.extensions.nestable.EjQueryNestable', array(
    'selector' => '#menu',
    'scriptPosition' => CClientScript::POS_END,
  )); ?>
  <?php $this->cs->registerCss('CustomMenuCss', "
#menu { margin-top: 15px; }
.sitemap#menu li > .btn-group { margin-right: 154px; }
"); ?>
<?php endif; ?>
