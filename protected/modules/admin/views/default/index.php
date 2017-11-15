<div class="statistics">
  <div class="container">
    <div class="row">
      <div class="span6">
        <h2><?= Yii::t('admin.titles', 'Statistics') ?></h2>
      </div>
      <div class="span6">
        <div class="btn-group pull-right" data-toggle="buttons-radio" id="range-selector">
          <button type="button" class="btn btn-small active" data-range="week"><?= Yii::t('admin.labels', 'Week') ?></button>
          <button type="button" class="btn btn-small" data-range="month"><?= Yii::t('admin.labels', 'Month') ?></button>
          <button type="button" class="btn btn-small" data-range="halfyear"><?= Yii::t('admin.labels', 'Half Of Year') ?></button>
          <button type="button" class="btn btn-small" data-range="year"><?= Yii::t('admin.labels', 'Year') ?></button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="span12">
        <div id="stats">
          <div class="loading"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php if (Yii::app()->authManager->checkAccessList('registryEventLog', true)): ?>
  <div class="events-components">
    <div class="container">
      <div class="row">
        <div class="span12">
          <h2><?= Yii::t('admin.labels', 'Latest Events') ?></h2>
          <table class="table">
            <thead>
              <tr>
                <th><?= Yii::t('admin.labels', 'Date And Time') ?></th>
                <th><?= Yii::t('admin.labels', 'Administrator, IP Address') ?></th>
                <th><?= Yii::t('admin.labels', 'Event') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($events as $event): ?>
                <tr>
                  <td class="span3">
                    <?= Yii::app()->format->formatDate($event->time) ?>
                    <small><?= Yii::app()->format->formatTime($event->time) ?></small>
                  </td>
                  <td class="span4">
                    <div><a href="<?= $this->createUrl($event->authorType == VAccountTypeCollection::ADMINISTRATOR ? '/admin/registry/admin/update' : '/admin/registry/user/update', array('id' => $event->authorID)) ?>"><?= ($author = $event->getRelated('author' . $event->authorType)) ? ($author->name ? $author->name : $author->email) : Yii::t('admin.labels', 'Erased user with ID {id}', array('{id}' => $event->authorID)) ?></a><small class="muted">, <?= Yii::app()->format->formatIp4Address($event->remote) ?></small></div>
                  </td>
                  <td class="span5">
                    <?= Yii::t($event->translate, $event->event, $event->params) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (count($events) == 0): ?>
                <tr><td colspan="3"><span class="muted"><?= Yii::t('admin.messages', 'No events registered yet.') ?></span></td></tr>
                  <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php $this->cs->registerScriptFile(Yii::app()->theme->getScriptUrl('raphael.min.js'), CClientScript::POS_END); ?>
<?php $this->cs->registerScriptFile(Yii::app()->theme->getScriptUrl('morris.min.js'), CClientScript::POS_END); ?>
<?php $this->cs->registerScript('Dashboard.Plot.Init', "
InitMorris(" . CJavaScript::encode(array_values($stats)) . ");
$('#stats .loading').css('background-color',$('.statistics').css('background-color'));
$(window).on('resize',function(e) {
  setTimeout(function() {
    InitMorris(morrisData);
  },100);
});
"); ?>
<?php $this->cs->registerScript('Dashboard.Plot.Functions', "
var morrisData;
function InitMorris(data) {
  $('svg,.morris-hover','#stats').remove();
  morrisData = data;
  Morris.Line({
    element: 'stats',
    data: data,
    xkey: 'period',
    ykeys: ['requests', 'visitors'],
    labels: ['" . Yii::t('admin.labels', 'Requests') . "', '" . Yii::t('admin.labels', 'Visitors') . "'],
    smooth: false,
    parseTime: false,
    hideHover: true
  });
}
", CClientScript::POS_END); ?>
<?php $this->cs->registerScript('Dashboard.Plot.Reload', "
$('#range-selector > button').click(function(e) {
  $('#stats .loading').show();
  var range = $(this).data('range');
  $.ajax({
    cache: true,
    data: { range: range },
    dataType: 'json',
    success: function(jdata) {
        InitMorris(jdata.stats);
    },
    complete: function() {
        $('#stats .loading').hide();
    }
  });
});
"); ?>
