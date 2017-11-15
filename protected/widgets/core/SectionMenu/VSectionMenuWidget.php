<?php
/**
 * ViraCMS Site Section Menu Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSectionMenuWidget extends VWidget
{
  const MENU_TYPE_NAV = 'nav';
  const MENU_TYPE_NAV_LIST = 'nav nav-list';
  const MENU_TYPE_NAV_PILLS = 'nav nav-pills';
  const MENU_TYPE_NAV_TABS = 'nav nav-tabs';
  const MENU_TYPE_NAV_PILLS_STACKED = 'nav nav-pills nav-stacked';
  const MENU_TYPE_NAV_TABS_STACKED = 'nav nav-tabs nav-stacked';
  const DEFAULT_MENU_TYPE = self::MENU_TYPE_NAV_LIST;

  public $addParentPage = false;
  public $parentPageID;
  public $menuType;
  private static $_currentUrl;

  public function run()
  {
    $this->render('section-menu');
  }

  public function getMenuItems()
  {
    $items = array();
    $page = VPage::model()->with(array(
        'l10n' => array('alias' => 'pageL10n'),
        'children',
        'children.l10n',
      ))->findByPk($this->parentPageID);

    if ($page) {
      $pages = array();

      if ($page->homepage) {
        $criteria = new CDbCriteria();
        $criteria->condition = "t.parentID = ''";
        $criteria->compare('t.siteID', Yii::app()->site->id);
        $criteria->compare('t.homepage', '0');
        $criteria->compare('t.visibility', VPageVisibilityCollection::VISIBLE);
        $criteria->order = 't.position ASC';
        $pages = VPage::model()->findAll($criteria);
      }
      elseif ($page->children) {
        $pages = $page->children;
      }

      if ($this->addParentPage) {
        if (($item = $this->formatItem($page)) !== null) {
          $items[] = $item;
        }
      }

      foreach ($pages as $child) {
        if (($item = $this->formatItem($child)) !== null) {
          $items[] = $item;
        }
      }
    }

    return $items;
  }

  protected function formatItem($page)
  {
    $l10n = $page->getL10nModel(Yii::app()->getLanguage(), false);

    if ($l10n) {
      $url = $page->createUrl();

      return array(
        'label' => $l10n->name,
        'url' => $url,
        'active' => $this->isItemActive($url),
      );
    }

    return null;
  }

  protected function isItemActive($url)
  {
    return trim($url, ' /') == $this->getCurrentUrl();
  }

  protected function getCurrentUrl()
  {
    if (self::$_currentUrl === null) {
      $controller = Yii::app()->getController();
      $origin = $controller->getOriginRoute();

      if (!empty($origin)) {
        $params = $controller->getOriginParams();
        self::$_currentUrl = trim(Yii::app()->createUrl('/' . $origin, $params), ' /');
      }
      else {
        self::$_currentUrl = trim(Yii::app()->createUrl('/' . $controller->route, $controller->actionParams), ' /');
      }
    }

    return self::$_currentUrl;
  }

  public function getCacheKey()
  {
    return 'Vira.Widget.SectionMenu.' . $this->parentPageID . $this->addParentPage . $this->menuType;
  }

  public function getCacheParams()
  {
    return array(
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
    Yii::import($this->baseAlias . '.SectionMenu.forms.VSectionMenuWidgetParams');
    return new VSectionMenuWidgetParams();
  }

  public function getConfigView()
  {
    return $this->baseAlias . '.SectionMenu.views.configure';
  }
}
