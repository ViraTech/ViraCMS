<?php
/**
 * ViraCMS Draggable (items) Site Map Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
Yii::import('zii.widgets.CMenu');
class DraggableSiteMapWidget extends CMenu
{
  /**
   * @var array HTML attributes for link label wrapper
   */
  public $linkLabelWrapperOptions = array();

  /**
   * @var array Common HTML attributes for link
   */
  public $linkOptions = array(
    'class' => 'btn',
  );

  /**
   * @var integer Buttons show delay
   */
  public $delay = 500;

  /**
   * @var boolean Encode labels or not
   */
  public $encodeLabel = false;

  /**
   *
   * @var array HTML attributes for top container
   */
  public $htmlOptions = array(
    'class' => 'sitemap',
  );

  /**
   * Initialize widget
   */
  public function init()
  {
    $cs = Yii::app()->getClientScript();
    $cs->registerCoreScript('jquery');
    $cs->registerScriptFile(Yii::app()->getController()->coreScriptUrl('dialogs'));
    $this->registerScripts($cs);
    $this->items = $this->prepareItems($this->items);
    parent::init();
  }

  /**
   * Calls {@link renderMenu} to render the menu.
   */
  public function run()
  {
    $this->renderMenu($this->items);
  }

  /**
   * Renders the menu items.
   * @param array $items menu items. Each menu item will be an array with at least two elements: 'label' and 'active'.
   * It may have three other optional elements: 'items', 'linkOptions' and 'itemOptions'.
   */
  protected function renderMenu($items)
  {
    if (count($items)) {
      echo CHtml::openTag('div', $this->htmlOptions) . "\n";
      echo CHtml::openTag('ul') . "\n";
      $this->renderMenuRecursive($items);
      echo CHtml::closeTag('ul');
      echo CHtml::closeTag('div');
    }
  }

  /**
   * Renders the content of a menu item.
   * Note that the container and the sub-menus are not rendered here.
   * @param array $item the menu item to be rendered. Please see {@link items} on what data might be in the item.
   * @return string
   */
  protected function renderMenuItem($item)
  {
    if (!isset($item['linkOptions']) || !is_array($item['linkOptions'])) {
      $item['linkOptions'] = array();
    }
    $item['linkOptions'] = CMap::mergeArray($this->linkOptions, isset($item['linkOptions']) ? $item['linkOptions'] : array());
    $linkLabelWrapperOptions = CMap::mergeArray($this->linkLabelWrapperOptions, isset($item['linkLabelWrapperOptions']) ? $item['linkLabelWrapperOptions'] : array());
    if (isset($item['url'])) {
      $label = '';
      if (isset($item['url-long']) && $item['url-long']) {
        $label .= CHtml::tag('span', array('class' => 'muted pull-right', 'title' => $item['url-long']), CHtml::encode($item['url-long']));
      }
      $label .= empty($item['label']) ? CHtml::tag('span', array('class' => 'text-warning'), Yii::t('admin.content.titles', 'No Title Given')) : CHtml::tag('span', array('title' => $item['label']), CHtml::encode($item['label']));
      if (isset($item['icon']) && $item['icon']) {
        $label = CHtml::tag('i', array('class' => 'icon-' . $item['icon']), '') . ' ' . $label;
      }
//			if (isset($item['layout']) && $item['layout']) {
//				$label .= CHtml::tag('small',array('class' => 'muted pull-right'),'[' . $item['layout'] . ']');
//			}
      if ($this->linkLabelWrapper !== null) {
        $label = CHtml::tag($this->linkLabelWrapper, $linkLabelWrapperOptions, $label);
      }
      $inner = CHtml::openTag('div', array('class' => 'btn-group'));
      $inner .= CHtml::link($label, '#', $item['linkOptions']);
      $inner .= empty($item['itemOptions']['data-edit-url']) ? '' : CHtml::link('<i class="icon-pencil"></i>', $item['itemOptions']['data-edit-url'], array('class' => 'btn btn-primary btn-control control-edit', 'title' => Yii::t('admin.content.labels', 'Edit Page Contents')));
      $inner .= empty($item['itemOptions']['data-create-url']) ? '' : CHtml::link('<i class="icon-plus"></i>', $item['itemOptions']['data-create-url'], array('class' => 'btn btn-success btn-control control-add-page', 'title' => Yii::t('admin.content.labels', 'Add Children Page')));
      $inner .= CHtml::link('<i class="icon-cog"></i>', $item['itemOptions']['data-config-url'], array('class' => 'btn btn-warning btn-control control-configure-page', 'title' => Yii::t('admin.content.labels', 'Configure Page')));
//			$inner .= empty($item['itemOptions']['data-homepage']) ? CHtml::link('<i class="icon-eye-open"></i>','#',array('class' => 'btn btn-inverse btn-control control-page-visibility','title' => Yii::t('admin.content.labels','Visibility Options'))) : '';
      $inner .= empty($item['itemOptions']['data-homepage']) ? CHtml::link('<i class="icon-trash"></i>', '#', array('class' => 'btn btn-danger btn-control control-delete-page', 'title' => Yii::t('admin.content.labels', 'Delete Page'))) : '';
      $inner .= CHtml::closeTag('div');
    }
    else {
      $inner = CHtml::tag('span', $item['linkOptions'], $item['label']);
    }

    return $inner;
  }

  /**
   * Items preparation to CMenu compatible format
   *
   * @param array $items Menu items
   * @return array
   */
  private function prepareItems($items, $parent = '')
  {
    $newItems = array();

    foreach ($items as $item) {
      $url = explode('/', trim($item['url'], '/'));
      $url = array_pop($url);

      $itemOptions = array(
        'data-page-id' => $item['id'],
        'data-url' => $url,
        'data-host' => $item['host'],
        'data-config-url' => $item['configureUrl'] . (stripos($item['configureUrl'], '?') === false ? '?' : '&') . 'return=map',
        'data-parent-id' => $parent,
        'data-page-name' => CHtml::encode($item['label']),
        'class' => array(''),
      );

      if (isset($item['createUrl'])) {
        $itemOptions['data-create-url'] = $item['createUrl'] . (stripos($item['createUrl'], '?') === false ? '?' : '&') . 'return=map';
      }

      if (isset($item['editUrl'])) {
        $itemOptions['data-edit-url'] = $item['editUrl'] . (stripos($item['editUrl'], '?') === false ? '?' : '&') . 'return=map';
      }

      if ($item['homepage']) {
        $itemOptions['data-homepage'] = true;
        $itemOptions['data-disabled-item'] = true;
        $itemOptions['data-no-child'] = true;
        $itemOptions['class'][] = ' sitemap-disabled-item';
      }

      $itemOptions['data-visible'] = $item['visibility'];
      if ($item['visibility'] == VPageVisibilityCollection::HIDDEN) {
        $itemOptions['class'][] = ' sitemap-hidden';
      }

      if (isset($item['items']) && count($item['items'])) {
        $itemOptions['data-has-children'] = true;
      }

      $itemOptions['class'] = implode(' ', $itemOptions['class']);

      $row = array(
        'label' => $item['label'],
        'url' => $item['url'],
        'url-long' => trim($item['url'], '/') . (!empty($item['redirectUrl']) ? ' ⇢ ' . $item['redirectUrl'] : ''),
        'layout' => $item['layoutID'],
        'itemOptions' => $itemOptions,
        'linkOptions' => isset($item['linkOptions']) ? $item['linkOptions'] : array(),
      );

      if (isset($item['items'])) {
        $row['items'] = $this->prepareItems($item['items'], $item['id']);
      }

      $newItems[] = $row;
    }

    return $newItems;
  }

  private function registerScripts($cs)
  {
    $cs->registerScript('Draggable_SiteMap_Init', "
var dragging = false;
function onDragStart(e,el) {
  dragging = true;
}
function onDragStop(e,el) {
  setTimeout(function() {
    dragging = false;
  },50);
  updateUrl(el);
  updateChildren(el);
  el.closest('.sitemap').trigger('drag.sitemap',el);
}
function updateUrl(el) {
  var url = [];
  el.parents('li').each(function(e) {
    var self = $(this);
    url.push(self.data('url'));
  });
  url.reverse().push(el.data('url'));
  el.find('a.control-go-to').attr('href',el.data('host') + '/' + url.join('/'));
  el.find('a:eq(0) small').text('/' + url.join('/'));
  updateUrlChildren(el.find('>ul'),url);
}
function updateUrlChildren(el,topUrl) {
  el.each(function() {
    $(this).find('>li').each(function() {
      var self = $(this);
      var url = topUrl.slice();
      url.push(self.data('url'));
      self.find('a.control-go-to').attr('href',self.data('host') + '/' + url.join('/'));
      self.find('a:eq(0) small').text('/' + url.join('/'));
      updateUrlChildren(self.find('>ul'),url);
    });
  });
}
function updateChildren(el) {
  var root = el.closest('.sitemap');
  root.find('li').each(function() {
    var self = $(this);
    if (self.find('ul').length) {
      self.data('has-children',true);
    }
    else {
      self.data('has-children',false);
    }
  });
}
", CClientScript::POS_END);
    $cs->registerScript('Draggable_SiteMap_DeletePageDialog', "
$(document).on('click','.btn-control.control-delete-page',function(e) {
  e.preventDefault();
  var el = $(this).closest('li');
  var id = el.data('page-id');
  var children = el.data('has-children');
  var title = el.data('page-name').trim();
  var body = '<h4>" . Yii::t('admin.content.titles', 'Please confirm page removal') . "</h4><p><label class=\"checkbox\"><input type=\"checkbox\" id=\"confirmation\" value>" . Yii::t('admin.content.labels', 'I am sure to delete this page') . "</label></p>';
  if (children) {
    body += '<h4>" . Yii::t('admin.content.labels', 'Page has children pages') . "</h4><p><label class=\"radio\"><input type=\"radio\" name=\"children\" id=\"children1\" value=\"" . PageController::CONNECT_CHILDREN_TO_PARENT . "\" checked>" . Yii::t('admin.content.labels', 'Connect children pages to parent page') . "</label><label class=\"radio\"><input type=\"radio\" name=\"children\" id=\"children2\" value=\"" . PageController::DELETE_CHILDREN . "\">" . Yii::t('admin.content.labels', 'Delete children pages') . "</label></p>';
  }
  var modal = viraCoreConfirm('" . Yii::t('admin.content.titles', 'Page "{title}" removal confirmation') . "'.replace('{title}',title),body,function() {
    var confirmed = $('#confirmation',modal);
    var children = $('input[name=children]:checked',modal).eq(0).val();
    if (confirmed.prop('checked')) {
      modal.modal('hide');
      $.ajax({
        cache: false,
        url: '" . $this->controller->createUrl('delete') . "',
        type: 'POST',
        data: { id: id, children: children },
        dataType: 'text',
        complete: function(jqXHR, textStatus) {},
        success: function(jdata) {
          $('#sitemap').load('.',function() {
            viraCoreAlert('success','" . Yii::t('admin.content.messages', 'Page "{title}" successfully removed') . "'.replace('{title}',title));
          });
        },
        error: function(jqXHR, textStatus, errorThrown) {
          viraCoreAlert('error','" . Yii::t('admin.content.errors', 'An error occurred while processing: {error}') . "'.replace('{error}',jqXHR.responseText));
        }
      });
    }
    else {
      if (!confirmed.parent().hasClass('error')) {
        confirmed.parent().addClass('error').after('<span class=\"help-inline error\">" . Yii::t('admin.content.titles', 'Please confirm page removal') . "</span>');
      }
      return false;
    }
  },function(){},{ ok: '" . Yii::t('common', 'OK') . "', cancel: '" . Yii::t('common', 'Cancel') . "' });
});", CClientScript::POS_READY);

    $cs->registerScript('Draggable_SiteMap_MovePageDialog', "
$('#" . $this->id . "').on('drag.sitemap',function(e,el) {
  var element = $(el);
  if (!element.length) {
      return;
  }
  var title = '&laquo;' + element.data('page-name').trim() + '&raquo;';
  var parentID = '';
  var parent = element.parents('li:first');
  if (parent.length && parent.data('page-id')) {
    parentID = parent.data('page-id');
  }
  var id = element.data('page-id');
  var currentParentID = element.data('parent-id');
  var sorting = [];
  parent = element.closest('ul');
  parent.find('>li').each(function() {
    var id = $(this).data('page-id');
    if (id) {
      sorting.push(id);
    }
  });
  $.ajax({
    cache: false,
    url: '" . $this->controller->createUrl('move') . "',
    type: 'POST',
    data: { id: id, parent: parentID, 'sorting[]': sorting },
    dataType: 'text',
    complete: function(jqXHR, textStatus) {},
    success: function() {
      viraCoreAlert('success','" . Yii::t('admin.content.titles', 'Page "{title}" successfully moved') . "'.replace('{title}',title));
      element.data('parent-id',parentID);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      viraCoreAlert('error','" . Yii::t('admin.content.errors', 'An error occurred while processing: {error}') . "'.replace('{error}',jqXHR.responseText));
    }
  });
});", CClientScript::POS_READY);

    $cs->registerScript('Draggable_SiteMap_HidePageDialog', "
$(document).on('click','.btn-control.control-page-visibility',function(e) {
  e.preventDefault();
  var el = $(this).closest('li');
  var id = el.data('page-id');
  var title = el.data('page-name');
  var children = [];
  el.find('li').each(function() {
    children.push($(this).data('page-id'));
  });

  var body = '<h4>" . Yii::t('admin.content.titles', 'Select visibility option') . "</h4><p>" . str_replace(array("\r\n", "\n", "\r", "\t"), '', CHtml::dropDownList('visibility', '', Yii::app()->collection->pageVisibility->toArray(), array('id' => 'visibility-selector', 'class' => 'input-block-level'))) . "</p>';
  var modal = viraCoreConfirm('" . Yii::t('admin.content.titles', 'Page "{title}" visibility option') . "'.replace('{title}',title),body,function() {
    var visibility = $('#visibility-selector',modal).val();
    modal.modal('hide');
    $.ajax({
      cache: false,
      url: '" . $this->controller->createUrl('visibility') . "',
      type: 'POST',
      data: { id: id, 'children[]': children, visibility: visibility },
      dataType: 'text',
      complete: function(jqXHR, textStatus) {},
      success: function() {
        if (visibility != 0) {
          el.addClass('sitemap-hidden').data('visible',visibility);
          el.find('li').addClass('sitemap-hidden').data('visible',visibility);
        }
        else {
          el.removeClass('sitemap-hidden').data('visible',visibility);
          el.find('li').removeClass('sitemap-hidden').data('visible',visibility);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        viraCoreAlert('error','" . Yii::t('admin.content.errors', 'An error occurred while processing: {error}') . "'.replace('{error}',jqXHR.responseText));
      }
    });
  },function(){},{ ok: '" . Yii::t('common', 'OK') . "', cancel: '" . Yii::t('common', 'Cancel') . "' });
  $('#visibility-selector',modal).val(el.data('visible'));
});", CClientScript::POS_READY);

    $this->controller->widget('application.extensions.nestable.EjQueryNestable', array(
      'selector' => '#' . $this->id,
      'scriptPosition' => CClientScript::POS_END,
      'settings' => array(
        'onDragStart' => 'js:onDragStart',
        'onDragStop' => 'js:onDragStop',
      ),
    ));
  }
}
