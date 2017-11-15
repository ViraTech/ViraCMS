<div class="row-fluid">
  <div class="span3">
    <?= $data->site->shortTitle ?>
  </div>
  <div class="span3">
    <?php if (($author = $data->getRelated('author' . $data->authorType)) != null): ?>
      <span><small>[<?= $data->authorID ?>]</small> <?= $author->name ?></span>
    <?php else: ?>
      <span><?= Yii::t('admin.registry.errors', 'Unknown Account ID {id}', array('{id}' => $data->authorID)) ?></span>
    <?php endif; ?>
    <br>
    <small class="muted"><?= Yii::app()->collection->accountType->itemAt($data->authorType) ?></small>
  </div>
  <div class="span6">
    <span><?= Yii::t($data->translate, $data->event, $data->params) ?></span><br>
    <small class="muted"><?= Yii::app()->format->formatDatetime($data->time) ?>,</small>
    <small class="muted"><?= Yii::t('admin.registry.labels', 'IP {ip}', array('{ip}' => Yii::app()->format->formatIp4Address($data->remote))) ?></small>
  </div>
</div>
