<?php
/**
 * ViraCMS Custom Navigation Bar Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCustomNavbarWidget extends VWidget
{
  public $menuID;
  public $position;
  public $fixed;
  public $container = 'container';
  public $brand = false;
  public $brandName;
  public $brandImageUrl;

  public function run()
  {
    $this->render('custom-navbar');
  }

  public function getMenuItems()
  {
    $items = array();

    if ($this->menuID) {
      $criteria = new CDbCriteria();
      $criteria->compare('t.menuID', $this->menuID);
      $criteria->with = array(
        'page',
        'l10n',
      );
      $criteria->order = 't.parentID ASC,t.position ASC';

      $items = $this->getMenuItemsRecursive(VCustomMenuItem::model()->findAll($criteria));
    }

    return $items;
  }

  protected function getMenuItemsRecursive($source, $parentID = '')
  {
    $items = array();

    foreach ($source as $item) {
      if ($item->parentID != $parentID) {
        continue;
      }

      $l10n = $item->getL10nModel();
      $data = array(
        'label' => $l10n ? $l10n->title : '--',
        'url' => $item->page ? $item->page->createUrl() : $item->url,
        'anchor' => $item->anchor,
        'target' => $item->target,
      );

      $data['active'] = $this->isItemActive($data['url']);

      $data['items'] = $this->getMenuItemsRecursive($source, $item->id);

      $items[] = $data;
    }

    return $items;
  }

  protected function isItemActive($url)
  {
    $controller = Yii::app()->getController();
    $origin = $controller->getOriginRoute();

    if (!empty($origin)) {
      $params = $controller->getOriginParams();
      return trim($url, ' /') == trim(Yii::app()->createUrl('/' . $origin, $params), ' /');
    }

    return trim($url, ' /') == trim(Yii::app()->createUrl('/' . $controller->route, $controller->actionParams), ' /');
  }

  public function getCacheKey()
  {
    return 'Vira.Widget.CustomNavbar.' . $this->menuID;
  }

  public function getCacheParams()
  {
    return array(
      'varyByExpression' => 'Yii::app()->site->id',
      'varyByLanguage' => true,
      'varyByRoute' => true,
      'varyByParam' => array(
        'url',
        'id',
        'page',
      ),
    );
  }

  public function getCacheDependency()
  {
    return new VTaggedCacheDependency('Vira.Pages', 86400);
  }

  public function getParamsModel()
  {
    Yii::import($this->baseAlias . '.CustomNavbar.forms.VCustomNavbarWidgetParams');
    return new VCustomNavbarWidgetParams();
  }

  public function getConfigView()
  {
    return $this->baseAlias . '.CustomNavbar.views.configure';
  }
}
