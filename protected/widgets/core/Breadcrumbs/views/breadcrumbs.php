<?php if ($this->showPageTitle && $this->pageTitlePosition == VBreadcrumbsWidget::PAGE_TITLE_POSITION_ABOVE): ?>
<?php $this->render('page-title') ?>
<?php endif; ?>
<ul class="breadcrumb">
<?php foreach ($this->getController()->getBreadcrumbs() as $url => $title): ?>
  <li><?= CHtml::link($title, $url ? $url : '.') ?></li>
<?php endforeach; ?>
</ul>
<?php if ($this->showPageTitle && $this->pageTitlePosition == VBreadcrumbsWidget::PAGE_TITLE_POSITION_BELOW): ?>
<?php $this->render('page-title') ?>
<?php endif; ?>
