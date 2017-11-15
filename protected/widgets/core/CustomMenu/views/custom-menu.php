<ul class="<?= $this->menuType == VCustomMenuWidget::MENU_TYPE_NAV_LIST ? 'well ' : '' ?><?= $this->menuType ?>">
<?php foreach ($this->getMenuItems() as $item): ?>
  <li<?= $item['active'] ? ' class="active"' : '' ?>>
    <a href="<?= $item['url'] ?><?= $item['anchor'] ? '#' . $item['anchor'] : '' ?>"<?= $item['target'] ? ' target="' . $item['target'] . '"' : '' ?>><?= $item['label'] ?></a>
  </li>
<?php endforeach; ?>
</ul>
