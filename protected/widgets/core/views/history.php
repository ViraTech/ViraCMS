<?php if (count($events)): ?>
  <ul class="nav nav-tabs">
  <?php foreach ($events as $i => $event): ?>
    <li<?= $i == 0 ? ' class="active"' : '' ?>><a href="#history-<?= $event['id'] ?>" data-toggle="tab"><?= $event['name'] ?></a></li>
  <?php endforeach; ?>
  </ul>
  <div class="tab-content">
  <?php foreach ($events as $i => $event): ?>
    <div class="tab-pane fade<?= $i == 0 ? ' active in' : '' ?>" id="history-<?= $event['id'] ?>">
      <div class="row-fluid">
        <div class="span6">
          <?= $form->uneditableRow($event['model'], 'userID', array('class' => 'input-block-level', 'value' => $event['model']->user ? $event['model']->user->name : '')) ?>
        </div>
        <div class="span3">
          <?= $form->uneditableRow($event['model'], 'timestamp', array('class' => 'input-block-level', 'value' => Yii::app()->format->formatDatetime($event['model']->timestamp))) ?>
        </div>
        <div class="span3">
          <?= $form->uneditableRow($event['model'], 'ip', array('class' => 'input-block-level')) ?>
        </div>
      </div>
      <?= $form->uneditableRow($event['model'], 'agent', array('class' => 'input-block-level')) ?>
    </div>
  <?php endforeach; ?>
  </div>
<?php else: ?>
  <p class="muted"><?= Yii::t('common', 'No actions recorded.') ?></p>
<?php endif; ?>
