<div class="row-fluid">
  <div class="span3">
    <?php if ($data->site): ?>
      <?= $data->site->shortTitle ?>
    <?php else: ?>
      <span><?= Yii::t('admin.registry.errors', 'Unknown Site ID {id}', array('{id}' => $data->siteID)) ?></span>
    <?php endif; ?>
  </div>
  <div class="span3">
    <?php if ($data->authorType && ($author = $data->getRelated('author' . $data->authorType)) != null): ?>
      <span><small>[<?= $data->authorID ?>]</small> <?= $author->name ?></span>
    <?php else: ?>
      <span><?= Yii::t('admin.registry.errors', 'Unknown Account ID {id}', array('{id}' => $data->authorID)) ?></span>
    <?php endif; ?>
    <br>
    <small class="muted"><?= Yii::app()->collection->accountType->itemAt($data->authorType) ?></small>
  </div>
  <div class="span2">
    <?= Yii::app()->collection->authLogType->itemAt($data->type) ?>
  </div>
  <div class="span1">
    <span class="label label-<?= $data->result ? 'success' : 'important' ?>"><?= Yii::app()->format->formatBoolean($data->result) ?></span>
  </div>
  <div class="span3">
    <?= Yii::app()->format->formatDatetime($data->time) ?>
    <br><small class="muted"><?= Yii::t('admin.registry.labels', 'IP {ip}', array('{ip}' => Yii::app()->format->formatIp4Address($data->remote))) ?></small>
  </div>
</div>
