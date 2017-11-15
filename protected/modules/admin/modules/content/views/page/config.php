<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => 'form-' . strtolower(get_class($model)),
  'type' => 'vertical',
  'htmlOptions' => array(
    'class' => 'form-bordered',
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<?= $form->errorSummary(array_merge(array($model), is_array($model->l10n) ? $model->l10n : array(), $model->getSeoModels())) ?>
<?= $form->hiddenField($model, 'id') ?>
<input type="hidden" name="<?= CHtml::activeName($model, 'redirectRoute') ?>" value="">
<input type="hidden" name="<?= CHtml::activeName($model, 'redirectItem') ?>" value="">
<fieldset>
  <legend><?= $title ?></legend>
</fieldset>
<ul class="nav nav-tabs">
  <li class="active"><a href="#page-config" data-toggle="tab"><?= Yii::t('admin.content.titles', 'Configuration') ?></a></li>
  <li><a href="#page-seo" data-toggle="tab"><?= Yii::t('admin.content.titles', 'SEO') ?></a></li>
  <li><a href="#page-history" data-toggle="tab"><?= Yii::t('admin.content.titles', 'History') ?></a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane fade in active" id="page-config">
    <?php if ((!$model->isNewRecord || $model->homepage) && (Yii::app()->user->getAttribute('siteID')) == 0): ?>
      <?= $form->uneditableRow($model, 'siteID', array('class' => 'input-block-level', 'value' => $model->site ? $model->site->title : $model->siteID)) ?>
    <?php else: ?>
      <?= $form->dropDownListRow($model, 'siteID', CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'), array('class' => 'input-block-level')) ?>
      <?php $this->widget('ext.eselect2.ESelect2', array(
        'selector' => '#' . CHtml::activeId($model, 'siteID'),
        'events' => array(
          'change' => "js:function(e) {
  var siteID = e.val;
  $.ajax({
    cache: false,
    type: 'get',
    dataType: 'json',
    data: { site: siteID },
    url: '" . $this->createUrl('ajax') . "',
    data: { site: siteID },
    success: function(jdata)
    {
      var parents = $('#" . CHtml::activeId($model, 'parentID') . "');
      parents.val('').change();
      parents.find('option[value!=\"\"]').remove();
      for (var i in jdata.parentPages) {
        parents.append($('<option>',{ value: i}).text(jdata.parentPages[i]));
      }

      var layouts = $('#" . CHtml::activeId($model, 'layoutID') . "');
      layouts.find('option').remove();
      for (var i in jdata.layouts) {
        layouts.append($('<option>',{ value: i}).text(jdata.layouts[i]));
      }

      ParentUrl = jdata.parentUrls;
    }
  });
}",
        ),
      ));
      ?>
    <?php endif; ?>
    <div class="control-group">
      <label class="control-label"><?= Yii::t('admin.content.labels', 'Page Name') ?></label>
      <div class="controls">
      <?php if (count($languages) > 1): ?>
        <?php foreach ($languages as $language): ?>
        <?php $l10n = $model->getL10nModel($language->id, false); ?>
        <div class="input-prepend" style="width: 100%; display: table; border-collapse: separate; margin-bottom: 5px;">
          <span class="add-on" style="width: 15%; border-right: 0; display: table-cell;"><?= $language->title ?></span>
          <span style="display: table-cell; width: 85%; margin-bottom: 0;">
            <?= $form->textField($l10n, 'name', array('class' => 'input-block-level', 'id' => get_class($l10n) . '_' . $language->id . '_name', 'name' => get_class($l10n) . '[' . $language->id . '][name]')) ?>
          </span>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php $l10n = $model->getL10nModel($languages[0]->id, false); ?>
        <?= $form->textField($l10n, 'name', array('class' => 'input-block-level', 'id' => get_class($l10n) . '_' . $languages[0]->id . '_name', 'name' => get_class($l10n) . '[' . $languages[0]->id . '][name]')) ?>
      <?php endif; ?>
      </div>
    </div>
    <?php if ($model->homepage): ?>
      <?= $form->uneditableRow($model, 'updateUrl', array('class' => 'input-block-level')) ?>
    <?php else: ?>
      <?= $form->dropDownListRow($model, 'parentID', $parentPages, array('class' => 'input-block-level', 'empty' => Yii::t('admin.content.labels', 'No parent (top level)'))) ?>
      <div class="control-group">
        <?= $form->labelEx($model, 'updateUrl', array('class' => 'control-label')) ?>
        <div class="controls">
          <div class="input-prepend" style="width: 100%; display: table; border-collapse: separate;">
            <span class="add-on" style="width: 1%; display: table-cell; border-right: 0;"><?= $model->homepage ? null : ($parentUrl != '/' ? $parentUrl . '/' : $parentUrl) ?></span>
            <div style="display: table-cell;">
              <?= $form->textField($model, 'updateUrl', array('class' => 'input-block-level', 'readonly' => $model->homepage ? 'readonly' : '')) ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?= $form->dropDownListRow($model, 'class', Yii::app()->collection->pageRenderer->toArray(), array('class' => 'input-block-level')) ?>
    <div class="control-group" id="redirect-options" style="display: <?= Yii::app()->collection->rendererAction->getRendererAction($model->class) == VRendererActionCollection::ACTION_REDIRECT ? 'block' : 'none' ?>;">
      <?= $form->labelEx($model, 'redirectRoute', array('class' => 'control-label')) ?>
      <div class="controls">
        <div class="row-fluid">
          <div class="span6">
            <?= $form->dropDownList($model, 'redirectRoute', $this->getEntryPoints($model), array('class' => 'input-block-level')) ?>
          </div>
          <div class="span6">
            <?= $form->dropDownList($model, 'redirectItem', $this->getItems($model->redirectRoute ? $model->redirectRoute : 'VPage', $model), array('options' => array('id' . VPage::REDIRECT_ITEM_DELIMITER . $model->id => array('disabled' => true)), 'class' => 'input-block-level', 'empty' => null)) ?>
          </div>
        </div>
      </div>
    </div>
    <div class="control-group" id="external-redirect" style="display: <?= Yii::app()->collection->rendererAction->getRendererAction($model->class) == VRendererActionCollection::ACTION_EXTERNAL_REDIRECT ? 'block' : 'none' ?>;">
      <?= $form->labelEx($model, 'redirectUrl', array('class' => 'control-label')) ?>
      <div class="controls">
        <?= $form->textField($model, 'redirectUrl', array('class' => 'input-block-level')) ?>
      </div>
    </div>
    <?= $form->dropDownListRow($model, 'layoutID', CHtml::listData(VSiteLayout::model()->from($site->id)->findAll(), 'id', 'title'), array('class' => 'input-block-level')) ?>
    <?php /* disabled // 20170631 // evc
      <div class="row-fluid">
      <div class="span6">
      <?= $form->dropDownListRow($model,'accessibility',Yii::app()->collection->pageAccessibility->toArray(),array('class' => 'input-block-level')) ?>
      </div>
      <div class="span6">
      <?php if (!$model->homepage): ?>
      <?= $form->dropDownListRow($model,'visibility',Yii::app()->collection->pageVisibility->toArray(),array('class' => 'input-block-level')) ?>
      <?php endif ?>
      </div>
      </div>
     */ ?>
      <?= $form->radioButtonListInlineRow($model, 'cacheable', Yii::app()->format->booleanFormat, array('class' => 'input-block-level')) ?>
  </div>
  <div class="tab-pane fade" id="page-seo">
    <fieldset>
      <?php $this->widget('application.widgets.core.VSeoWidget', array(
        'model' => $model,
        'form' => $form,
      )); ?>
    </fieldset>
  </div>
  <div class="tab-pane fade" id="page-history">
    <?php $this->widget('application.widgets.core.VHistoryWidget', array(
      'model' => $model,
      'form' => $form,
    )); ?>
  </div>
</div>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index', array('site' => $site->id)) ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
<?php $this->cs->registerScript('BootstrapTabs_Page', "$('#page a').click(function(e) { e.preventDefault(); $(this).tab('show'); })", CClientScript::POS_READY); ?>
<?php $this->cs->registerScript('ParentUrlFunction', "$('#" . CHtml::activeId($model, 'parentID') . "').change(function(e) {
  var url = ParentUrl[$(this).val()] || '/';
  if (url[url.length-1] != '/') {
    url += '/';
  }
  $('#" . CHtml::activeId($model, 'updateUrl') . "').closest('.controls').find('span.add-on').text(url);
});", CClientScript::POS_READY); ?>
<?php $this->cs->registerScript('ParentUrlVariable', "var ParentUrl = " . CJavaScript::encode($parentUrls) . ";", CClientScript::POS_END); ?>
<?php $this->cs->registerScript('ReferUrlFunction', "
$('#" . CHtml::activeId($model, 'class') . "').change(function(e) {
  if ($.inArray($(this).val()," . CJavaScript::encode(Yii::app()->collection->pageRenderer->getActionRenderer(VRendererActionCollection::ACTION_REDIRECT)) . ") == -1) {
    $('#redirect-options').slideUp('fast');
  }
  else {
    $('#redirect-options').slideDown('fast');
  }
  if ($.inArray($(this).val()," . CJavaScript::encode(Yii::app()->collection->pageRenderer->getActionRenderer(VRendererActionCollection::ACTION_EXTERNAL_REDIRECT)) . ") == -1) {
    $('#external-redirect').slideUp('fast');
  }
  else {
    $('#external-redirect').slideDown('fast');
  }
});
$('#" . CHtml::activeId($model, 'redirectRoute') . "').change(function(e) {
  var items = $('#" . CHtml::activeId($model, 'redirectItem') . "');
  items.find('optgroup,option').remove();
  var route = $(this).val();
  if (route) {
    $.ajax({
      url: '" . $this->createUrl('ajax') . "',
      cache: true,
      type: 'get',
      dataType: 'json',
      data: { route: route, pageID: '" . $model->id . "', siteID: $('#" . CHtml::activeId($model, 'siteID') . "').val() },
      success: function(jdata)
      {
        if (jdata) {
          for (i in jdata) {
            if (typeof jdata[i] == 'object') {
              var optgroup = $('<optgroup />').attr('label',i);
              var options = jdata[i];
              for (j in options) {
                var option = $('<option />').attr('value',j).text(options[j]);
                optgroup.append(option);
              }
              items.append(optgroup);
            }
            else {
              var option = $('<option />').attr('value',i).text(jdata[i]);
              items.append(option);
            }
          }
        }
      }
    });
  }
});
", CClientScript::POS_READY); ?>
