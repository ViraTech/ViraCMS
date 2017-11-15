<?php
/**
 * ViraCMS Static Page Model
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
 * @property string $layoutID page layout
 * @property string $class page renderer class
 * @property boolean $system system (service) page trigger
 * @property boolean $cacheable page can be cached
 * @property string $url page URL
 * @property string $redirectRoute route to transfer request
 * @property string $redirectParam param name of item request will be transferred
 * @property string $redirectValue param value of item request will be transferred
 * @property string $redirectUrl url to transfer request
 * @property string $parentID parent page ID
 * @property boolean $homepage page is home page
 * @property integer $visibility visibility options
 * @property integer $accessibility accessibility options
 * @property integer $position page position
 * @property VPageL10n[] $l10n localization model
 * @property VPage $parent parent page
 * @property VPage[] $children children pages
 */
class VPage extends VActiveRecord
{
  const REDIRECT_ITEM_DELIMITER = '#';

  /**
   * @var string page title in local language context, also used in filter
   */
  public $_title;

  /**
   * @var string user part of url for updating
   */
  public $updateUrl;

  /**
   * @var string redirect param and value
   */
  public $redirectItem;

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
          'createMessage' => 'Page [{id}] "{title}" of the site [{siteID}] "{siteTitle}" has been created',
          'createParams' => array(
            '{id}' => '$this->id',
            '{title}' => '$this->getTitle()',
            '{siteID}' => '$this->siteID',
            '{siteTitle}' => '$this->site ? $this->site->title : ""',
          ),
          'updateMessage' => 'Page [{id}] "{title}" of the site [{siteID}] "{siteTitle}" has been updated',
          'updateParams' => array(
            '{id}' => '$this->id',
            '{title}' => '$this->getTitle()',
            '{siteID}' => '$this->siteID',
            '{siteTitle}' => '$this->site ? $this->site->title : ""',
          ),
          'deleteMessage' => 'Page [{id}] "{title}" of the site [{siteID}] "{siteTitle}" has been removed',
          'deleteParams' => array(
            '{id}' => '$this->id',
            '{title}' => '$this->getTitle()',
            '{siteID}' => '$this->siteID',
            '{siteTitle}' => '$this->site ? $this->site->title : ""',
          ),
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
   * @return VPage
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_page}}';
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id,siteID', 'length', 'is' => 36),
      array('parentID', 'length', 'is' => 36, 'allowEmpty' => true),
      array('siteID,class,url', 'required'),
      array('updateUrl', 'required', 'on' => 'create,configure'),
      array('url,title,redirectRoute', 'length', 'max' => 255),
      array('redirectItem', 'length', 'max' => 8186),
      array('class,layoutID', 'length', 'max' => 64),
      array('updateUrl', 'match', 'pattern' => '/^\/?$|^[A-Za-z0-9-_]+$/', 'on' => 'create,configure'),
      array('redirectUrl', 'length', 'max' => 4094),
      array('cacheable', 'boolean'),
      array('visibility', 'in', 'range' => Yii::app()->collection->pageVisibility->getKeys()),
      array('accessibility', 'in', 'range' => Yii::app()->collection->pageAccessibility->getKeys()),
      array('position', 'numerical', 'integerOnly' => true),
      array('id,siteID,class,parentID,_title,url', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'siteID' => Yii::t('admin.content.labels', 'Site'),
      'layoutID' => Yii::t('admin.content.labels', 'Layout'),
      'class' => Yii::t('admin.content.labels', 'Renderer'),
      'parentID' => Yii::t('admin.content.labels', 'Parent Page'),
      'url' => Yii::t('admin.content.labels', 'Page URL'),
      'updateUrl' => Yii::t('admin.content.labels', 'Page URL'),
      'redirectRoute' => Yii::t('admin.content.labels', 'Forward/Redirect'),
      'redirectUrl' => Yii::t('admin.content.labels', 'Redirect to URL'),
      'cacheable' => Yii::t('admin.content.labels', 'Cacheable'),
      'visibility' => Yii::t('admin.content.labels', 'Visibility Option'),
      'accessibility' => Yii::t('admin.content.labels', 'Accessibility'),
      'position' => Yii::t('admin.content.labels', 'Position'),
    );
  }

  public function scopes()
  {
    return array(
      'noHomepage' => array('condition' => 'homepage=0'),
    );
  }

  public function relations()
  {
    return array(
      'site' => array(self::BELONGS_TO, 'VSite', 'siteID'),
      'layout' => array(self::BELONGS_TO, 'VSiteLayout', array('layoutID' => 'id', 'siteID' => 'siteID')),
      'l10n' => array(self::HAS_MANY, 'VPageL10n', 'pageID'),
      'currentL10n' => array(self::HAS_ONE, 'VPageL10n', 'pageID', 'on' => 'currentL10n.languageID=:currentLanguage', 'params' => array(':currentLanguage' => Yii::app()->getLanguage())),
      'parent' => array(self::BELONGS_TO, 'VPage', 'parentID'),
      'children' => array(self::HAS_MANY, 'VPage', 'parentID', 'order' => 'children.position ASC'),
      'rows' => array(self::HAS_MANY, 'VPageRow', 'pageID'),
      'blocks' => array(self::HAS_MANY, 'VPageBlock', 'pageID'),
    );
  }

  public function behaviors()
  {
    return CMap::mergeArray(
        array(
        'LocalizationBehavior' => array(
          'class' => 'VLocalizationBehavior',
        ),
        'GuidBehavior' => array(
          'class' => 'VGuidBehavior',
        ),
        'SeoBehavior' => array(
          'class' => 'VSeoBehavior',
        ),
        ), parent::behaviors()
    );
  }

  protected function afterFind()
  {
    parent::afterFind();
    $this->updateUrl = $this->getPageUrl();
    $this->redirectItem = implode(self::REDIRECT_ITEM_DELIMITER, array(
      $this->redirectParam,
      $this->redirectValue,
    ));
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      $this->url = !$this->homepage ? $this->getParentUrl() . '/' . $this->updateUrl : '/';

      return true;
    }

    return false;
  }

  protected function afterValidate()
  {
    parent::afterValidate();

    $criteria = new CDbCriteria();
    $criteria->compare('t.siteID', $this->siteID);
    $criteria->compare('t.url', $this->url);
    $criteria->compare('t.id', '<>' . $this->id);

    if (self::model()->count($criteria) > 0) {
      $this->addError('url', Yii::t('admin.content.errors', 'URL is already taken by another record.'));
    }
  }

  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $cmd = Yii::app()->db->createCommand()->select('MAX(position)')->from($this->tableName());
        $condition = 'siteID=:siteID';
        $params = array(':siteID' => $this->siteID);
        if ($this->parentID) {
          $condition .= ' AND parentID=:parentID';
          $params[':parentID'] = $this->parentID;
        }
        $cmd->where($condition, $params);
        $this->position = $cmd->queryScalar() + 1;
      }

      if (in_array($this->scenario, array('configure', 'move', 'updateUrl'))) {
        if ($this->children) {
          foreach ($this->children as $child) {
            $child->setScenario('updateUrl');
            $child->url = $this->url . '/' . $child->getPageUrl();
            $child->save(false);
          }
        }
      }

      if (in_array($this->scenario, array('create', 'configure'))) {
        switch (Yii::app()->collection->rendererAction->getRendererAction($this->class)) {
          case VRendererActionCollection::ACTION_REDIRECT:
            if (!empty($this->redirectItem) && stripos($this->redirectItem, self::REDIRECT_ITEM_DELIMITER) !== false) {
              list($this->redirectParam, $this->redirectValue) = explode(self::REDIRECT_ITEM_DELIMITER, $this->redirectItem);
            }
            else {
              $this->redirectParam = '';
              $this->redirectValue = '';
            }
            if ($this->redirectRoute == 'VPage') {
              if (($redirectPage = self::model()->findByPk($this->redirectValue)) == null) {
                $this->redirectUrl = '';
                $this->redirectRoute = '';
                $this->redirectParam = '';
                $this->redirectValue = '';
              }
              else {
                $this->redirectUrl = $redirectPage->createUrl();
              }
            }
            else {
              $param = $this->redirectParam && $this->redirectValue ? array($this->redirectParam => $this->redirectValue) : array();
              $this->redirectUrl = Yii::app()->createUrl($this->redirectRoute, $param);
            }
            break;

          case VRendererActionCollection::ACTION_EXTERNAL_REDIRECT:
            $this->redirectRoute = '';
            $this->redirectParam = '';
            $this->redirectItem = '';
            break;

          default:
            $this->redirectUrl = '';
            $this->redirectRoute = '';
            $this->redirectParam = '';
            $this->redirectValue = '';
        }
      }

      return true;
    }

    return false;
  }

  protected function afterDelete()
  {
    parent::afterDelete();
    if ($this->l10n) {
      foreach ($this->l10n as $l10n) {
        $l10n->delete();
      }
    }
    if ($this->children) {
      foreach ($this->children as $child) {
        $child->delete();
      }
    }
    if ($this->rows) {
      foreach ($this->rows as $row) {
        $row->delete();
      }
    }
    $this->clearCache();
  }

  protected function afterSave()
  {
    parent::afterSave();
    $this->clearCache();
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.parentID', $this->parentID);
    $criteria->compare('t.url', $this->url, true);
    $criteria->compare('t.cacheable', $this->cacheable);
    $criteria->compare('currentL10n.name', $this->_title, true);
    $criteria->compare('layout.id', $this->layoutID);
    $criteria->with = array(
      'currentL10n',
      'layout',
    );

    $this->addSiteCondition('siteID', $criteria);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->params['defaultPageSize'],
      ),
      'sort' => array(
        'defaultOrder' => $this->quoteColumn('t.url') . ' ASC',
      ),
    ));
  }

  /**
   * @return string page title in current language context
   */
  public function getTitle()
  {
    if (empty($this->_title)) {
      if ($this->hasRelated('currentL10n') && $this->currentL10n) {
        $this->_title = $this->currentL10n->name;
      }
      else {
        $l10n = $this->getL10nModel();
        $this->_title = $l10n->name;
      }
    }

    return $this->_title;
  }

  /**
   * Move page to another parent
   *
   * @param integer $parentID ID of new parent page
   * @return boolean
   */
  public function move($parentID)
  {
    $this->setScenario('move');
    $parent = self::model()->findByPk($parentID);

    if ($parentID && $parent == null) {
      return false;
    }
    elseif ($parent != null) {
      $parentUrl = trim($parent->url, ' \\./');
    }

    $this->parentID = $parent ? $parent->id : null;
    $this->parent = $parent;
    $this->url = (!empty($parentUrl) ? '/' . $parentUrl : '') . '/' . $this->getPageUrl();

    return $this->save();
  }

  /**
   * Return parent page URL
   *
   * @return string
   */
  public function getParentUrl()
  {
    if ($this->homepage || !$this->parent) {
      $url = null;
    }
    else {
      $url = $this->parent->url;
    }

    return $url;
  }

  /**
   * Return page URL without parent part
   *
   * @return string
   */
  public function getPageUrl()
  {
    if ($this->homepage) {
      $url = '/';
    }
    else {
      $url = explode('/', trim($this->url, ' \\./'));
      $url = array_pop($url);
    }

    return $url;
  }

  public function getUrlRoute()
  {
    return '/site/page';
  }

  public function getUrlParams($params = array())
  {
    $params['url'] = $this->url;
    return $params;
  }

  /**
   * Generate page URL
   *
   * @param boolean $absolute create absolute URL
   * @param array $params URL parameters
   * @return string
   */
  public function createUrl($absolute = false, $params = array())
  {
    $url = Yii::app()->createUrl($this->getUrlRoute(), $this->getUrlParams($params));

    if ($absolute) {
      $host = rtrim($this->site && $this->site->host ? $this->site->host : Yii::app()->request->hostInfo, ' /');
      if (stripos($host, 'http://') !== 0 && stripos($host, 'https://') !== 0) {
        $host = 'http://' . $host;
      }

      $url = $host . $url;
    }

    return $url;
  }

  /**
   * Compares access level for this page and return false if access is not allowed
   *
   * @return boolean
   */
  public function checkAccessibility()
  {
    $result = false;

    switch ($this->accessibility) {
      case VPageAccessibilityCollection::ADMINISTRATOR_ONLY:
        $result = Yii::app()->user->getType() == VAccountTypeCollection::ADMINISTRATOR;
        break;

      case VPageAccessibilityCollection::GUEST_ONLY:
        $result = Yii::app()->user->isGuest;
        break;

      case VPageAccessibilityCollection::AUTHENTICATED_ONLY:
        $result = !Yii::app()->user->isGuest;
        break;

      case VPageAccessibilityCollection::EVERYONE:
      default:
        $result = true;
    }

    return $result;
  }

  /**
   * Clear related to this page cache records
   */
  public function clearCache()
  {
    if (Yii::app()->hasComponent('cache')) {
      Yii::app()->cache->deleteTag('Vira.Pages');
    }

    Yii::app()->siteMap->clear($this->siteID);
  }
}
