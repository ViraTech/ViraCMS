<h2><?= $title ?></h2>
<div class="row-fluid">
  <div class="span6">
    <?php $this->widget('application.widgets.core.SiteSearch.VSiteSearchWidget', array(
      'size' => 'xxlarge',
    )); ?>
  </div>
</div>
  <?php if (!empty($results)): ?>
  <h5><?= Yii::t('common', 'Found {n} entry:|Found {n} entries:', array(count($results))) ?></h5>
  <ol class="search" start="1">
  <?php foreach ($results as $result): ?>
    <li>
      <h4><strong><a href="<?= $result['url'] ?>"><?= $result['title'] ?></a></strong></h4>
    <?php foreach ($result['snippets'] as $snippet): ?>
      <div class="snippet"><?= $snippet ?></div>
    <?php endforeach; ?>
    </li>
  <?php endforeach; ?>
  </ol>
<?php endif; ?>
<?php if (!empty($q) && empty($results)): ?>
  <h5><?= Yii::t('common', 'Nothing found') ?></h5>
<?php endif; ?>
