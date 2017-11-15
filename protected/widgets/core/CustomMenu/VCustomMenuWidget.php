<?php
/**
 * ViraCMS Custom Menu Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCustomMenuWidget extends VWidget
{
  const MENU_TYPE_NAV = 'nav';
  const MENU_TYPE_NAV_LIST = 'nav nav-list';
  const MENU_TYPE_NAV_PILLS = 'nav nav-pills';
  const MENU_TYPE_NAV_TABS = 'nav nav-tabs';
  const MENU_TYPE_NAV_PILLS_STACKED = 'nav nav-pills nav-stacked';
  const MENU_TYPE_NAV_TABS_STACKED = 'nav nav-tabs nav-stacked';
  const DEFAULT_MENU_TYPE = self::MENU_TYPE_NAV_LIST;

  public $menuID;
  public $menuType;

  public function run()
  {
    $this->render('custom-menu');
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

      foreach (VCustomMenuItem::model()->findAll($criteria) as $item) {
        $l10n = $item->getL10nModel();
        $data = array(
          'label' => $l10n ? $l10n->title : '--',
          'url' => $item->page ? $item->page->createUrl() : $item->url,
          'anchor' => $item->anchor,
          'target' => $item->target,
        );

        $data['active'] = $this->isItemActive($data['url']);

        $items[] = $data;
      }
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
    return 'Vira.Widget.CustomMenu.' . $this->menuID . $this->menuType;
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
    Yii::import($this->baseAlias . '.CustomMenu.forms.VCustomMenuWidgetParams');
    return new VCustomMenuWidgetParams();
  }

  public function getConfigView()
  {
    return $this->baseAlias . '.CustomMenu.views.configure';
  }
}
