<ul class="dropdown-menu">
<?php if (!empty($header)): ?>
  <li class="nav-header"><?= $header ?></li>
<?php endif; ?>
<?php foreach ($items as $item): ?>
<li class="<?= $item['active'] ? 'active ' : '' ?><?= !empty($item['items']) ? 'dropdown-submenu' : '' ?>">
  <a tabindex="-1" href="<?= $item['url'] ?>"<?= $item['target'] ? ' target="' . $item['target'] . '"' : '' ?>><?= $item['label'] ?></a>
  <?php if ($item['items']): ?>
    <?php
    $this->render('dropdown-menu', array(
      'items' => $item['items'],
      'level' => $level + 1,
    ))
    ?>
<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
