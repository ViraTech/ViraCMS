<?php
/**
 * ViraCMS Advanced Site Menu Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @todo need to be refactored ASAP
 */
Yii::import('zii.widgets.CMenu');
class VAdvancedSiteMenuWidget extends CMenu
{
  private $_basePath;
  private $_baseAlias;

  /**
   * @var integer Custom menu ID
   */
  public $menuID = 0;

  /**
   * @var boolean render children items (if no custom menu specified)
   */
  public $children = true;

  /**
   * @var integer Parent item ID (if no custom menu specified)
   */
  public $parentID = null;

  /**
   * @var render plain (one-level) menu
   */
  public $plain = false;

  /**
   * @var string CSS class for top element
   */
  public $cssClass = 'nav';

  /**
   * @var boolean activate parents of active item as well
   */
  public $activateParents = true;

  /**
   * @var boolean encode menu labels
   */
  public $encodeLabel = false;

  /**
   * @var string current URL
   */
  private $_currentUrl;

  public function init()
  {
    $originRoute = Yii::app()->controller->getOriginRoute();
    $originParams = Yii::app()->controller->getOriginParams();
    $this->_currentUrl = $this->controller->createUrl('/' . ($originRoute ? $originRoute : $this->controller->route), ($originParams ? $originParams : $this->controller->actionParams));
    if (stripos($this->_currentUrl, '?') !== false) {
      $this->_currentUrl = substr($this->_currentUrl, 0, stripos($this->_currentUrl, '?'));
    }
    if ($this->menuID) {
      $criteria = new CDbCriteria;
      $criteria->compare('t.menuID', $this->menuID);
      $criteria->with = array(
        'page',
        'l10n',
      );
      $criteria->order = 't.parentID ASC,t.position ASC';
      $items = VCustomMenuItem::model()->findAll($criteria);

      $this->items = $this->preprocessList($items);
    }
    else {
      $this->items = $this->preprocess(Yii::app()->siteMap->getMenu(Yii::app()->site->id));
    }
    if (empty($this->htmlOptions['class'])) {
      $this->htmlOptions['class'] = $this->cssClass;
    }
    else {
      $this->htmlOptions['class'] .= ' ' . $this->cssClass;
    }
    parent::init();
  }

  /**
   * Preprocess menu items to create site menu depends on custom menu provided by $menuID
   *
   * @param array $items site map items
   * @param integer $parentID return children items for this parent
   * @return array
   */
  private function preprocessList($items, $parentID = 0)
  {
    $return = array();

    foreach ($items as $item) {
      if ($parentID != $item->parentID) {
        continue;
      }

      $children = $this->preprocessList($items, $item->id);

      $i = $this->createMenuItem(array(
        'label' => $item->l10nModel->title,
        'target' => $item->target,
        'url' => $item->page ? $item->page->createUrl() : $item->url,
      ));

      if ($this->plain) {
        $return[$item['id']] = $i;
        $return = CMap::mergeArray($return, $children);
      }
      else {
        $i['items'] = $children;
        $return[$item['id']] = $i;
      }
    }

    return $return;
  }

  /**
   * Preprocess menu items to make format suitable for futher usage in CMenu
   *
   * @param type $items
   * @return type
   */
  private function preprocess($items = null)
  {
    $return = array();

    foreach ($items as $item) {
      if ($this->parentID !== null && $this->parentID != $item['parent'] && $this->parentID != $item['id']) {
        continue;
      }
      if (!empty($item['target'])) {
        $itemOptions['target'] = $item['target'];
      }
      if (!empty($item['items']) && $this->children) {
        $i = $this->createMenuItem($item);
        $children = $this->preprocess($item['items']);
        if ($this->plain) {
          $return[$item['id']] = $i;
          $return = CMap::mergeArray($return, $children);
        }
        else {
          $i['items'] = $children;
          $return[$item['id']] = $i;
        }
      }
      else {
        if ($this->parentID !== null && $this->parentID == $item['id']) {
          continue;
        }
        $return[$item['id']] = $this->createMenuItem($item);
      }
    }

    return $return;
  }

  /**
   * Create menu item array suitable for futher usage in CMenu
   *
   * @param array $item site menu item
   * @return array
   */
  private function createMenuItem($item)
  {
    return array(
      'label' => $this->encodeLabel ? CHtml::encode($item['label']) : $item['label'],
      'url' => $item['url'],
      'active' => $item['url'] == $this->_currentUrl,
    );
  }

  public function getBaseAlias()
  {
    if (empty($this->_baseAlias)) {
      $widget = Yii::app()->widgetFactory->getWidgetByClassName(get_class($this));
      $this->_baseAlias = $widget['baseAlias'];
    }

    return $this->_baseAlias;
  }

  public function getBasePath()
  {
    if (empty($this->_basePath)) {
      $reflection = new ReflectionClass(get_class($this));
      $this->_basePath = dirname($reflection->getFileName());
    }

    return $this->_basePath;
  }

  public function getParamsModel()
  {
    Yii::import($this->baseAlias . '.AdvancedSiteMenu.forms.AdvancedSiteMenuWidgetParams');
    return new AdvancedSiteMenuWidgetParams;
  }

  public function getConfigView()
  {
    return $this->baseAlias . '.AdvancedSiteMenu.views.configure';
  }
}
