<ul class="<?= $this->menuType == VSectionMenuWidget::MENU_TYPE_NAV_LIST ? 'well ' : '' ?><?= $this->menuType ?>">
<?php foreach ($this->getMenuItems() as $item): ?>
  <li<?= $item['active'] ? ' class="active"' : '' ?>>
    <a href="<?= $item['url'] ?>"><?= $item['label'] ?></a>
  </li>
<?php endforeach; ?>
</ul>
