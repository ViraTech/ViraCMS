<?php
/**
 * ViraCMS Site Map & Menu Generator Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSiteMap extends VApplicationComponent
{
  /**
   * @var string cache component name
   */
  public $cache = 'cache';

  /**
   * @var string cache prefix
   */
  public $cachePrefix = 'Vira.SiteMap';

  /**
   * @var integer default cache timeout, sec
   */
  public $cacheTimeout = 600;

  /**
   * @var integer default tag dependency timeout
   */
  public $cacheTagTimeout = 30;

  /**
   * Generate multilevel site map
   * @param string $siteID site identifier
   * @return mixed array of items if any, null otherwise
   */
  public function get($siteID)
  {
    $items = $this->load($siteID);

    if ($items === false) {
      $site = Yii::app()->site && Yii::app()->site->id == $siteID ? Yii::app()->site : VSite::model()->findByPk($siteID);
      $items = $this->getRecursive($site);
      $this->save($siteID, $items);
    }

    return $items;
  }

  /**
   * Retrieve site map in recursive way
   * @param VSite $site site model
   * @param string $parentID parent page identifier
   * @return array site map suitable for CMenu compatible widget
   */
  protected function getRecursive($site, $parentID = '')
  {
    $items = array();
    $context = Yii::app()->getController();

    $criteria = new CDbCriteria;
    if ($parentID) {
      $criteria->compare('t.parentID', $parentID);
    }
    else {
      $criteria->condition = "(t.parentID IS NULL OR t.parentID = '')";
    }
    $criteria->compare('t.siteID', $site->id);
    $criteria->with = array('l10n');
    $criteria->order = 't.homepage DESC,t.position ASC';

    foreach (VPage::model()->findAll($criteria) as $page) {
      $row = array(
        'id' => $page->id,
        'renderer' => $page->class,
        'label' => $page->title,
        'titles' => CHtml::listData($page->l10n, 'languageID', 'name'),
        'url' => $page->createUrl(),
        'absoluteUrl' => $page->createUrl(true),
        'host' => empty($site->host) ? Yii::app()->request->hostInfo : (stripos($site->host, 'http://') === 0 || stripos($site->host, 'https://') === 0 ? $site->host : ('http://' . $site->host)),
        'redirectUrl' => $page->redirectUrl,
        'configureUrl' => $context->createUrl('/admin/content/page/config', array('id' => $page->id)),
        'homepage' => $page->homepage,
        'visibility' => $page->visibility,
        'layoutID' => $page->layoutID,
        'items' => $this->getRecursive($site, $page->id),
      );

      if (!$page->homepage) {
        $row['createUrl'] = $context->createUrl('/admin/content/page/create', array('site' => $page->siteID, 'parent' => $page->id));
      }

      if (Yii::app()->collection->rendererAction->getRendererAction($page->class) == VRendererActionCollection::ACTION_OUTPUT) {
        $row['editUrl'] = $context->createUrl('/admin/content/page/update', array('id' => $page->id));
      }

      $items[] = $row;
    }

    return $items;
  }

  /**
   * Generate multilevel site menu
   * @param string $siteID site identifier
   * @return mixed array of items if any, null otherwise
   */
  public function getMenu($siteID)
  {
    $authenticated = !Yii::app()->user->isGuest;
    $cacheKey = $this->getCacheKey(implode('.', array($siteID, intval($authenticated))));

    $items = $this->load($cacheKey);

    if ($items === false) {
      $site = Yii::app()->site && Yii::app()->site->id == $siteID ? Yii::app()->site : VSite::model()->findByPk($siteID);
      $items = $this->getMenuRecursive($site);
      $this->save($cacheKey, $items);
    }

    return $items;
  }

  /**
   * Retrieve site map in recursive way
   * @param VSite $site site model
   * @param string $parentID parent page identifier
   * @return array site map suitable for CMenu compatible widget
   */
  protected function getMenuRecursive($site, $parentID = '')
  {
    $items = array();

    if ($site instanceof VSite) {
      $authenticated = !Yii::app()->user->isGuest;
      $criteria = new CDbCriteria;
      if ($parentID) {
        $criteria->compare('t.parentID', $parentID);
      }
      else {
        $criteria->condition = "(t.parentID IS NULL OR t.parentID = '')";
      }
      $criteria->with = array('l10n');
      $criteria->compare('t.visibility', VPageVisibilityCollection::VISIBLE);
      $criteria->compare('t.visibility', $authenticated ? VPageVisibilityCollection::VISIBLE_AUTHENTICATED : VPageVisibilityCollection::HIDDEN_AUTHENTICATED, false, 'OR');
      $criteria->compare('t.siteID', $site->id);
      $criteria->order = 't.homepage DESC,t.position ASC';

      foreach (VPage::model()->findAll($criteria) as $page) {
        $items[] = array(
          'id' => $page->id,
          'parent' => $page->parentID,
          'label' => $page->title,
          'url' => $page->createUrl(),
          'items' => $this->getMenuRecursive($site, $page->id),
        );
      }
    }

    return $items;
  }

  /**
   * Return cache key for specified site identifier and current language
   * @param string $siteID site identifier
   * @param string $languageID language identifier (optional)
   * @return string
   */
  protected function getCacheKey($siteID, $languageID = null)
  {
    return implode('.', array($this->cachePrefix, $siteID, $languageID === null ? Yii::app()->getLanguage() : $languageID));
  }

  /**
   * Return cache tag key for specified site identifier
   * @param string $siteID site identifier
   * @return string
   */
  protected function getCacheTag($siteID)
  {
    return implode('.', array($this->cachePrefix, 'Tag', $siteID));
  }

  /**
   * Load data from cache component specified by self::$cache
   * @param string $cacheKey cache key
   * @return mixed value if cache component exist and key has been found, false otherwise
   */
  protected function load($cacheKey)
  {
    if (Yii::app()->hasComponent($this->cache)) {
      $cache = Yii::app()->getComponent($this->cache);

      return $cache->get($this->getCacheKey($cacheKey));
    }

    return false;
  }

  /**
   * Save data to cache component specified by self::$cache
   * @param string $siteID site identifier
   * @param mixed $value the value
   * @return boolean true if operation was successful
   */
  protected function save($siteID, $value)
  {
    if (Yii::app()->hasComponent($this->cache)) {
      $cache = Yii::app()->getComponent($this->cache);

      return $cache->set(
          $this->getCacheKey($siteID), $value, $this->cacheTimeout, new VTaggedCacheDependency(
          $this->getCacheTag($siteID), $this->cacheTagTimeout
          )
      );
    }

    return false;
  }

  /**
   * Clear cache for specified site
   * @param string $siteID site identifier
   */
  public function clear($siteID)
  {
    if (Yii::app()->hasComponent($this->cache)) {
      $cache = Yii::app()->getComponent($this->cache);
      $cache->deleteTag($this->getCacheTag($siteID));
    }
  }

  /**
   * Return site map items for future use in CHtml::dropDownList
   * @param string $siteID site identifier to get site map items for
   * @param string $attribute select attribute
   * @param boolean $padding need to pad children entries
   */
  public function getMapItems($siteID, $attribute = 'label', $padding = true)
  {
    return $this->formatMapItemsRecursive($this->getMenu($siteID), $attribute, $padding);
  }

  /**
   * Format site map items
   * @param array $items items for recursive use
   * @param string $attribute select attribute
   * @param boolean $padding need to pad children entries
   * @param integer $level level for recursive use
   * @return array
   */
  protected function formatMapItemsRecursive($items = array(), $attribute = 'label', $padding = true, $level = 0)
  {
    $return = array();

    foreach ($items as $item) {
      $return[$item['id']] = ($padding ? str_pad('', $level * 4, "\xc2\xa0") : '') . $item[$attribute];
      if (isset($item['items']) && is_array($item['items']) && count($item['items'])) {
        $return = CMap::mergeArray($return, $this->formatMapItemsRecursive($item['items'], $attribute, $padding, $level + 1));
      }
    }

    return $return;
  }
}
