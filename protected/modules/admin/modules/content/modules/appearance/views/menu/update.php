<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => $model->getGridID(),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return beforeSend(this);",
  ),
)); ?>
<?= $form->errorSummary($model) ?>
<?= $form->hiddenField($model, 'id') ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'update', array('model' => $model)) ?></legend>
</fieldset>
<?php $this->renderPartial('tabs'); ?>
<div class="tab-content">
  <div class="tab-pane fade active in" id="general">
  <?php if ($model->isNewRecord && (Yii::app()->user->getAttribute('siteID')) == 0): ?>
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
    url: '" . $this->createUrl('reload') . "',
    data: { site: siteID },
    success: function(jdata)
    {
      pageUrls = jdata.pageUrls;
      sitemap = jdata.sitemap;

      var pages = $('#Entry_pageID');
      pages.find('option').each(function(){
        if ($(this).attr('value')) {
          $(this).remove();
        }
      });
      for (var i in jdata.pages) {
        var option = $('<option />').attr('value',i).text(jdata.pages[i]);
        pages.append(option);
      }
    }
  });
}",
    ))); ?>
  <?php else: ?>
    <?= $form->uneditableRow($model, 'siteID', array('class' => 'input-block-level', 'value' => $model->site ? $model->site->title : $model->siteID)) ?>
  <?php endif; ?>
    <?= $form->textFieldRow($model, 'title', array('class' => 'input-block-level')) ?>
  </div>
  <div class="tab-pane fade" id="menu-items">
    <ul style="display:none;" id="entry-blank-row">
      <?php $this->renderPartial('edit/item', array(
        'id' => '%id%',
        'url' => '%url%',
        'pageID' => '%pageID%',
        'titles' => '%titles%',
        'title' => '%title%',
        'target' => '%target%',
        'anchor' => '%anchor%',
      )); ?>
    </ul>
    <div class="btn-group">
      <a href="#" class="btn btn-success" id="btn-control-create"><i class="icon-plus-sign"></i> <?= Yii::t('admin.content.labels', 'Add Menu Item') ?></a>
      <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><i class="icon-angle-down"></i></a>
      <ul class="dropdown-menu">
        <li><a href="#" id="btn-control-copy"><i class="icon-code-fork"></i> <?= Yii::t('admin.content.labels', 'Make Site Map Copy') ?></a></li>
        <li><a href="#" id="btn-control-clear"><i class="icon-trash"></i> <?= Yii::t('admin.content.labels', 'Clear Menu Items') ?></a></li>
      </ul>
    </div>
    <?php $this->renderPartial('menu', array(
      'model' => $model,
      'view' => false,
    )); ?>
  </div>
  <div class="tab-pane fade" id="history">
    <?php $this->widget('application.widgets.core.VHistoryWidget', array(
      'model' => $model,
      'form' => $form,
    )); ?>
  </div>
</div>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
<?php $this->cs->registerScriptFile($this->coreScriptUrl('dialogs'), CClientScript::POS_END); ?>
<?php $this->cs->registerScript('InitVariables', "
var pageUrls = " . CJavaScript::encode($pageUrls) . ", idCounter = 0;
var currentItem, currentLanguageID = '{$currentLanguageID}';
var sitemap = " . CJavaScript::encode($sitemap) . ";", CClientScript::POS_HEAD); ?>
<?php $this->cs->registerScript('FormHandler', "
function beforeSend(form) {
  var addMenuItems = function(ul,parentPrefix) {
      var position = 0;
      ul.each(function() {
        var li = $(this);
        var hidden = $('<input />').attr('type','hidden');
        var prefix = (typeof parentPrefix === 'undefined' ? 'items' : parentPrefix + '[items]') + '[' + li.data('entry-id') + ']';
        $(form).append(hidden.clone().attr('name',prefix + '[id]').attr('value',li.data('entry-id')));
        $(form).append(hidden.clone().attr('name',prefix + '[url]').attr('value',li.data('entry-url')));
        $(form).append(hidden.clone().attr('name',prefix + '[pageID]').attr('value',li.data('page-id')));
        $(form).append(hidden.clone().attr('name',prefix + '[target]').attr('value',li.data('target')));
        $(form).append(hidden.clone().attr('name',prefix + '[anchor]').attr('value',li.data('anchor')));
        var titles = li.data('entry-titles');
        for (i in titles) {
          $(form).append(hidden.clone().attr('name',prefix + '[titles][' + i + ']').attr('value',titles[i]));
        }
        $(form).append(hidden.clone().attr('name',prefix + '[title]').attr('value',titles[currentLanguageID]));
        $(form).append(hidden.clone().attr('name',prefix + '[position]').attr('value',position));
        if (li.find('>ul').length) {
          addMenuItems(li.find('>ul>li'),prefix);
        }
        position++;
    });
  };

  addMenuItems($('#menu > ul > li'));
  return true;
}
", CClientScript::POS_END); ?>
<?php $this->cs->registerScript('CustomMenuHandler', "
var getItems = function(ul,level,disabled) {
  var items = $('<select></select>');
  items.attr('name','Entry_parent').attr('id','Entry_parent').addClass('input-block-level');

  if (typeof level === 'undefined') {
    var level = 0;
    items.append($('<option></option>').attr('value',''));
  }

  if (typeof ul === 'undefined') {
    var ul = $('#menu > ul');
  }

  if (typeof disabled === 'undefined') {
    var disabled = false;
  }

  if (currentItem) {
    var parent = currentItem.closest('ul').closest('li');
  }

  ul.find(' > li').each(function()
  {
    var option = $('<option></option'), itemDisabled = disabled;
    option.attr('value',$(this).data('entry-id'));
    if (typeof parent === 'object' && parent.length && parent.data('entry-id') === $(this).data('entry-id')) {
      option.attr('selected','selected');
    }
    if (itemDisabled || (currentItem && currentItem.data('entry-id') === $(this).data('entry-id'))) {
      option.attr('disabled','disabled');
      itemDisabled = true;
    }
    var label = $(this).find('a:first span').text();
    var p = '';
    for (var j = 0; j < level; j++) {
      p += '\xc2\xa0\xc2\xa0';
    }
    option.text(p + label);
    option.data('li',this);
    items.append(option);
    var sub = $(this).find('>ul');
    if (sub.length) {
      var subitems = getItems(sub,level + 1,itemDisabled);
      items.append(subitems.find('option'));
    }
  });

  return items;
};
$(document).on('click','#btn-control-create,#menu .btn-control.control-add-page',function(e) {
  e.preventDefault();
  currentItem = null;
  $('#modal-entry-options-header-update').hide();
  $('#modal-entry-options-header-create').show();
  $('#modal-entry-options-form').find('select,textarea,input').val('');
  $('#modal-entry-options').modal('show');
  $('#Entry_parent').replaceWith(getItems());
  if ($(this).closest('li').length) {
    $('#Entry_parent').val($(this).closest('li').data('entry-id'));
  }
});
$('#btn-control-copy').off('click').on('click',function(e) {
  e.preventDefault();

  var buildMap = function(items) {
    var content = '';
    for (i in items) {
      var titles = [];
      var t = items[i].titles;
      for (j in t) {
        titles.push('&quot;' + j + '&quot;:&quot;' + t[j] + '&quot;');
      }
      var li = $('#entry-blank-row').html().
        replace('%id%','new-' + idCounter++).
        replace(/%url%/gm,items[i].url).
        replace('%pageID%',items[i].id).
        replace('%titles%','{' + titles.join(',') + '}').
        replace('%title%',items[i].titles[currentLanguageID]).
        replace('%target%','').
          replace('%anchor%','');

      if (typeof items[i].items === 'object' && items[i].items.length) {
        var  m = buildMap(items[i].items);
        li = li.replace('<!-- subitems -->',m);
      }
      content += li;
    }

    return '<ul>' + content + '</ul>';
  };

  var modal = viraCoreConfirm('" . Yii::t('admin.content.titles', 'Confirm Site Map Copy') . "',
    '" . Yii::t('admin.content.messages', 'Current menu will be lost. Are you sure?') . "',
    function(e) {
      $('#menu > ul').replaceWith(buildMap(sitemap));
      modal.modal('hide');
    },null,{ok:'" . Yii::t('common', 'Yes') . "',cancel: '" . Yii::t('common', 'No') . "'});
  });

$('#btn-control-clear').off('click').on('click',function(e) {
  e.preventDefault();
  var modal = viraCoreConfirm('" . Yii::t('admin.content.titles', 'Confirm Menu Clearing') . "',
    '" . Yii::t('admin.content.messages', 'Are you sure to clear all of menu contents?') . "',
    function() {
      $('#menu > ul').empty();
      modal.modal('hide');
    },null,{ok:'" . Yii::t('common', 'Yes') . "',cancel: '" . Yii::t('common', 'No') . "'});
});

$('#menu').on('click','.btn-control.control-edit',function(e) {
  e.preventDefault();
  currentItem = $(this).closest('li');
  $('#modal-entry-options-header-update').show();
  $('#modal-entry-options-header-create').hide();
  $('#modal-entry-options').modal('show');
  $('#modal-entry-options-form').find('select,textarea,input').val('');
  var titles = $(currentItem).data('entry-titles');
  for (i in titles) {
    $('#Entry_title-' + i).val(titles[i]);
  }
  $('#Entry_pageID').val(currentItem.data('page-id'));
  $('#Entry_url').val(currentItem.data('entry-url'));
  $('#Entry_parent').replaceWith(getItems());
  $('#Entry_target').val(currentItem.data('target'));
  $('#Entry_anchor').val(currentItem.data('anchor'));
  $('#Entry_parent').val('');
  if (currentItem.parent().closest('li').length) {
    $('#Entry_parent').val(currentItem.parent().closest('li').data('entry-id'));
  }
});
$('#menu').on('click','.btn-control.control-delete-page',function(e) {
  e.preventDefault();
  var item = $(this).closest('li');
  var modal = viraCoreConfirm('" . Yii::t('admin.content.titles', 'Confirm Item Delete') . "',
    '" . Yii::t('admin.content.messages', 'Item will be removed as well as it children. Are you sure?') . "',
    function(e) {
      item.remove();
      modal.modal('hide');
    },null,{ok:'" . Yii::t('common', 'Yes') . "',cancel: '" . Yii::t('common', 'No') . "'});
});
$('#Entry_url').on('keyup',function(e) {
  $('#Entry_pageID').val('');
});
$('#Entry_pageID').on('change',function(e) {
  var val = $(this).val(),
    url = typeof pageUrls[val] !== 'undefined' ? pageUrls[val] : '',
    findPage = function(url,map) {
      for (var i in map) {
        if (map[i].url == url) {
          return map[i];
        }
        if (map[i].items.length > 0) {
          item = findPage(url,map[i].items);
          if (item != null) {
            return item;
          }
        }
      }
    };

  if (url) {
    item = findPage(url,sitemap);
    if (item != null) {
      var titles = item.titles;
      for (var i in titles) {
        var title = $('#Entry_title-' + i);
        if (title.val() == '') {
          title.val(titles[i]);
        }
      }
    }
  }

  $('#Entry_url').val(url);
});
$('#modal-entry-options [data-action=submit]').on('click',function(e) {
  e.preventDefault();
  var titles = {};
  var title = '';
  $('#modal-entry-options-form input[id^=Entry_title]').each(function() {
    var id = $(this).attr('id').split('-')[1];
    var label = $(this).val().replace(/\"/gm,'');
    titles[id] = label;
    if (id == currentLanguageID) {
        title = label;
    }
  });
var url = $('#Entry_url').val();
  var pageID = $('#Entry_pageID').val();
  var parentID = $('#Entry_parent').val();
  var target = $('#Entry_target').val();
  var anchor = $('#Entry_anchor').val();
  var parent = [];
  var selector = null;
  if (parentID) {
    parent = $('#menu li[data-entry-id=\"' + parentID + '\"]');
  }
  if (parent.length) {
    selector = parent.find('>ul');
    if (!selector.length) {
      selector = $('<ul></ul>').appendTo(parent);
    }
  }
  if (currentItem) {
    currentItem.
      data('entry-url',url).
      data('page-id',pageID).
      data('entry-titles',titles).
      data('target',target).
      data('anchor',anchor);
    currentItem.find('a:first span').text(title);
    currentItem.find('a:first small').text(url + (anchor ? '#' + anchor : ''));
    if (parent.length && parent.data('entry-id') !== currentItem.closest('ul').closest('li').data('entry-id')) {
      currentItem.detach().appendTo(selector);
    }
    else if (!parent.length && currentItem.closest('ul').closest('li').length) {
      currentItem.detach().appendTo('#menu > ul');
    }
  }
  else {
    var t = [];
    for (var i in titles) {
      t.push('&quot;' + i + '&quot;:&quot;' + titles[i].replace(/\"/gm,'&quot;') + '&quot;');
    }
    var blank = $('#entry-blank-row').
      html().
      replace('%id%','new-' + idCounter++).
      replace(/%url%/gm,url).
      replace('%pageID%',pageID).
      replace('%titles%','{' + t.join(',') + '}').
      replace('%title%',title).
      replace('%target%',target).
      replace('%anchor%',anchor);
    $(selector || '#menu > ul').append($(blank));
  }
  $('#modal-entry-options').modal('hide');
});
", CClientScript::POS_READY); ?>
<div class="modal hide fade" id="modal-entry-options">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="modal-entry-options-header-create" style="display:none;"><?= Yii::t('admin.content.titles', 'New Menu Item') ?></h3>
    <h3 id="modal-entry-options-header-update" style="display:none;"><?= Yii::t('admin.content.titles', 'Edit Menu Item') ?></h3>
  </div>
  <div class="modal-body">
    <form onsubmit="return false;" class="form-horizontal" id="modal-entry-options-form">
      <div class="control-group">
        <label class="control-label" for="Entry_parent"><?= Yii::t('admin.content.labels', 'Parent Item') ?></label>
        <div class="controls">
          <?= CHtml::dropDownList('Entry_parent', '', array(), array('empty' => '', 'class' => 'input-block-level')) ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"><?= Yii::t('admin.content.labels', 'Menu Item Title') ?></label>
        <div class="controls">
        <?php foreach ($languages as $language): ?>
          <div class="input-prepend" style="width:100%;display:table;border-collapse:separate;margin-bottom:10px;">
            <span class="add-on" style="width:70px;border-right:0;display:table-cell;"><?= $language->title ?></span>
            <?= CHtml::textField('Entry_title-' . $language->id, '', array('style' => 'display:table-cell;margin-bottom:0;')) ?>
          </div>
        <?php endforeach; ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Entry_pageID"><?= Yii::t('admin.content.labels', 'Link Item To Site Page') ?></label>
        <div class="controls">
          <?= CHtml::dropDownList('Entry_pageID', '', $pages, array('empty' => '', 'class' => 'input-block-level')) ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Entry_url"><?= Yii::t('admin.content.labels', 'Or Specify URL') ?></label>
        <div class="controls">
          <?= CHtml::textField('Entry_url', '', array('class' => 'input-block-level')) ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Entry_anchor"><?= Yii::t('admin.content.labels', 'Anchor') ?></label>
        <div class="controls">
          <?= CHtml::textField('Entry_anchor', '', array('class' => 'input-block-level')) ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Entry_target"><?= Yii::t('admin.content.labels', 'Item Link Target') ?></label>
        <div class="controls">
          <?= CHtml::dropDownList('Entry_target', '', $targets, array('class' => 'input-block-level')) ?>
        </div>
      </div>
    </form>
  </div>
  <div class="modal-footer">
    <button class="btn btn-link" data-dismiss="modal" aria-hidden="true"><?= Yii::t('common', 'Cancel') ?></button>
    <button class="btn btn-primary" data-action="submit"><?= Yii::t('common', 'OK') ?></button>
  </div>
</div>
