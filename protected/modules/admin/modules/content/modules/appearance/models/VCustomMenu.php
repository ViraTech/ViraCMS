<?php
/**
 * ViraCMS Custom Menu Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id primary key
 * @property string $siteID site identifier
 * @property string $title title
 */
class VCustomMenu extends VActiveRecord
{
  const CACHE_TAG_NAME = 'Vira.Custom.Menu';

  /**
   * @var array Menu items
   */
  private $_menu = null;

  /**
   * Initialize model
   */
  public function init()
  {
    parent::init();

    // attach administrative CRUD behaviours only when created inside the system CRUD controller
    if (is_a(Yii::app()->getController(), 'VSystemController')) {
      $this->attachBehaviors(array(
        'SystemLogBehavior' => array(
          'class' => 'VSystemLogBehavior',
          'createMessage' => 'Custom menu [{id}] "{title}" has been created',
          'createParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'updateMessage' => 'Custom menu [{id}] "{title}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'deleteMessage' => 'Custom menu [{id}] "{title}" has been removed',
          'deleteParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'translateCategory' => 'admin.content.events',
        ),
        'HistoryBehavior' => array(
          'class' => 'VHistoryBehavior',
        ),
      ));
    }
  }

  /**
   * @param string $className
   * @return VCustomMenu
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_custom_menu}}';
  }

  public function defaultScope()
  {
    return array(
      'with' => array(
        'items',
      ),
    );
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id,siteID', 'length', 'is' => 36),
      array('siteID,title', 'required'),
      array('title', 'length', 'max' => 255),
      array('id,title', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'items' => array(self::HAS_MANY, 'VCustomMenuItem', 'menuID', 'with' => array('page'), 'order' => 'items.position ASC'),
      'site' => array(self::BELONGS_TO, 'VSite', 'siteID'),
    );
  }

  public function behaviors()
  {
    return CMap::mergeArray(
        array(
        'GuidBehavior' => array(
          'class' => 'VGuidBehavior',
        ),
        ), parent::behaviors()
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'siteID' => Yii::t('admin.content.titles', 'Site'),
      'title' => Yii::t('admin.content.titles', 'Title'),
    );
  }

  public function forSite($siteID)
  {
    $this->getDbCriteria()->mergeWith(array(
      'condition' => 't.siteID=:siteID',
      'params' => array(
        ':siteID' => $siteID ? $siteID : Yii::app()->site->id,
      ),
    ));

    return $this;
  }

  /**
   * Auto filter by sites
   * @param boolean $currentSite filter by current site only
   * @return VCustomMenu
   */
  public function autoFilter($currentSite = false)
  {
    if ($currentSite) {
      $this->getDbCriteria()->mergeWith(array(
        'condition' => 't.siteID = :siteID',
        'params' => array(
          ':siteID' => Yii::app()->site->id,
        ),
      ));
    }
    elseif (Yii::app()->user->getAttribute('siteAccess') == 0) {
      $siteAccessList = Yii::app()->user->getModel()->getSiteAccessList();
      $criteria = new CDbCriteria();
      $criteria->addInCondition('t.siteID', $siteAccessList);
      $this->getDbCriteria()->mergeWith($criteria);
    }

    return $this;
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.title', $this->title, true);

    $this->addSiteCondition('siteID', $criteria);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
      ),
    ));
  }

  protected function afterFind()
  {
    if ($this->scenario == 'update') {
      $this->getMenu();
    }
    parent::afterFind();
  }

  protected function afterSave()
  {
    $this->clearMenu();
    $this->addMenuItems($this->_menu);
    $this->clearCache();
    parent::afterSave();
  }

  protected function afterDelete()
  {
    $this->clearMenu();
    $this->clearCache();
    parent::afterDelete();
  }

  public function clearMenu()
  {
    if ($this->items) {
      foreach ($this->items as $item) {
        $item->delete();
      }
    }
  }

  public function clearCache()
  {
    Yii::app()->cache->deleteTag(self::CACHE_TAG_NAME);
  }

  protected function addMenuItems($items = null, $parentID = '')
  {
    if (is_array($items)) {
      foreach ($items as $item) {
        $menuItem = new VCustomMenuItem('create');
        $menuItem->setAttributes(array(
          'menuID' => $this->id,
          'parentID' => $parentID,
          'pageID' => $item['pageID'],
          'url' => $item['url'],
          'position' => $item['position'],
          'target' => $item['target'],
          'anchor' => $item['anchor'],
          ), false);
        $menuItem->save();
        foreach ($item['titles'] as $languageID => $title) {
          $l10n = $menuItem->getL10nModel($languageID, false);
          $l10n->title = $title;
          $l10n->save();
        }
        if (isset($item['items']) && is_array($item['items'])) {
          $this->addMenuItems($item['items'], $menuItem->id);
        }
      }
    }
  }

  public function setMenu($menu = array())
  {
    $this->_menu = $menu;
  }

  public function getMenu()
  {
    if ($this->_menu === null) {
      $this->setMenu($this->createMenu());
    }

    return $this->_menu;
  }

  protected function createMenu()
  {
    return $this->createMenuRecursive('');
  }

  protected function createMenuRecursive($parentID)
  {
    $menu = array();

    if (count($this->items)) {
      foreach ($this->items as $item) {
        if ($item->parentID == $parentID) {
          $titles = array();
          if ($item->l10n) {
            foreach ($item->l10n as $l10n) {
              $titles[$l10n->languageID] = $l10n->title;
            }
          }
          $menu[] = array(
            'id' => $item->id,
            'pageID' => $item->pageID,
            'title' => $item->l10nModel->title,
            'titles' => $titles,
            'url' => $item->page ? $item->page->createUrl() : $item->url,
            'anchor' => $item->anchor,
            'target' => $item->target,
            'items' => $this->createMenuRecursive($item->id),
          );
        }
      }
    }

    return $menu;
  }
}
