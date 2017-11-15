<?php
/**
 * ViraCMS Advanced Navigation Bar Widget
 * Stylized to Twitter Bootstrap 2
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
class VAdvancedNavbarWidget extends CMenu
{
  private $_basePath;
  private $_baseAlias;

  /**
   * @var integer Site ID
   */
  public $siteID;

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
   * @var string dropdown caret tag name
   */
  public $caretTagName = 'i';

  /**
   * @var string dropdown caret class name
   */
  public $caretCssClass = 'icon-angle-down';

  /**
   * @var string current URL
   */
  private $_currentUrl;

  public function init()
  {
    if (!$this->siteID) {
      $this->siteID = Yii::app()->site->id;
    }
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
      $this->items = $this->preprocessList(VCustomMenuItem::model()->findAll($criteria));
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
   * Render menu items in navbar style
   * @param array $items
   */
  protected function renderMenu($items)
  {
    if (count($items)) {
      echo CHtml::openTag('div', array('class' => 'navbar'));
      echo CHtml::openTag('div', array('class' => 'navbar-inner'));
      echo CHtml::openTag('ul', $this->htmlOptions);
      $this->renderMenuRecursive($items);
      echo '</ul>';
      echo '</div>';
      echo '</div>';
    }
  }

  /**
   * Preprocess menu items to create site menu depends on custom menu provided by $menuID
   *
   * @param array $items site map items
   * @param integer $parentID filter by parent
   * @param integer $level deep level
   * @return array
   */
  private function preprocessList($items, $parentID = 0, $level = 0)
  {
    $return = array();

    foreach ($items as $item) {
      if ($parentID != $item->parentID) {
        continue;
      }

      $children = $this->preprocessList($items, $item->id, $level + 1);
      $i = $this->createMenuItem(array(
        'label' => $item->l10nModel->title,
        'url' => $item->page ? $item->page->createUrl() : $item->url,
        'target' => $item->target,
        'items' => $children,
        ), $level);

      if ($this->plain) {
        $return[] = $i;
        $return = CMap::mergeArray($return, $children);
      }
      else {
        $i['items'] = $children;
        $return[] = $i;
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
  private function preprocess($items = null, $level = 0)
  {
    $return = array();

    foreach ($items as $item) {
      if ($this->parentID && $this->parentID != $item['parent'] && $this->parentID != $item['id']) {
        continue;
      }
      if (!empty($item['items']) && $this->children) {
        if ($level > 0 || $this->plain) {
          $i = $this->createMenuItem($item, $level);
          $children = $this->preprocess($item['items'], $level + 1);
          if ($this->plain) {
            $return[] = $i;
            $return = CMap::mergeArray($return, $children);
          }
          else {
            if (is_array($children)) {
              $i['items'] = $children;
              $return[] = $i;
            }
          }
        }
        else {
          if (!empty($item['items'])) {
            $children = $this->preprocess($item['items'], $level + 1);
            unset($item['items']);
            $item['hasChildren'] = true;
          }
          $i = $this->createMenuItem($item, $level);
          $return[] = $i;
          if (is_array($children)) {
            $blankItem = $this->createMenuItem(array(
              'label' => '',
              'url' => '#',
              'items' => true,
              ), $level);
            $blankItem['items'] = $children;
            $return = CMap::mergeArray($return, array($blankItem));
          }
        }
      }
      else {
        if ($this->parentID && $this->parentID == $item['id']) {
          continue;
        }
        $return[] = $this->createMenuItem($item, $level);
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
  private function createMenuItem($item, $level = 0)
  {
    $itemOptions = array();
    $linkOptions = array();
    $submenuOptions = array();
    $caret = '';
    if ($this->children) {
      if ($level == 0 && !empty($item['hasChildren'])) {
        $linkOptions['style'] = 'padding-right: 30px;';
      }
      if (!empty($item['target'])) {
        $itemOptions['target'] = $item['target'];
      }
      if (!empty($item['items'])) {
        $itemOptions['class'] = 'dropdown';
        $linkOptions['class'] = 'dropdown-toggle';
        $linkOptions['data-toggle'] = 'dropdown';
        if ($level == 0) {
          $itemOptions['style'] = 'margin-left: -40px;';
          $linkOptions['style'] = 'padding-left: 15px;';
        }
        $submenuOptions['class'] = 'dropdown-menu';
        if ($level) {
          $itemOptions['class'] = 'dropdown-submenu';
        }
        else {
          $caret = CHtml::tag($this->caretTagName, array('class' => $this->caretCssClass), '');
        }
      }
    }
    return array(
      'label' => ($this->encodeLabel ? CHtml::encode($item['label']) : $item['label']) . $caret,
      'url' => $item['url'],
      'active' => $item['url'] == $this->_currentUrl,
      'itemOptions' => $itemOptions,
      'linkOptions' => $linkOptions,
      'submenuOptions' => $submenuOptions,
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
    Yii::import($this->baseAlias . '.AdvancedNavbar.forms.AdvancedNavbarWidgetParams');
    return new AdvancedNavbarWidgetParams;
  }

  public function getConfigView()
  {
    return $this->baseAlias . '.AdvancedNavbar.views.configure';
  }
}
