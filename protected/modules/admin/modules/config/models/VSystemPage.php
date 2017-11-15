<?php
/**
 * ViraCMS System Page Model
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
 * @property string $module system page module
 * @property string $controller controller page belongs to
 * @property string $view view name, usually concatenated category and index, e.g. error404
 * @property integer $timeUpdated model update timestamp
 * @property integer $updatedBy administrator identifier who has updated model
 */
class VSystemPage extends VActiveRecord
{
  /**
   * @var string page title in local language context, also used in filter
   */
  public $_title;

  /**
   * @var string module/controller/view, primary key
   */
  public $_mcv;

  /**
   * @var array content replacement details
   */
  protected $_replace;

  /**
   * @param string $className
   * @return VSystemPage
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_system_page}}';
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id', 'length', 'is' => 36),
      array('siteID,_mcv,layoutID', 'required'),
      array('siteID', 'length', 'is' => 36),
      array('layoutID', 'length', 'max' => 36, 'allowEmpty' => true),
      array('module,controller,view', 'length', 'max' => 255),
      array('id,siteID,layoutID,module,controller,view', 'safe', 'on' => 'search'),
      array('_mcv', 'safe', 'on' => 'create,update'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'siteID' => Yii::t('admin.content.labels', 'Site'),
      'layoutID' => Yii::t('admin.content.labels', 'Layout'),
      'timeUpdated' => Yii::t('admin.content.labels', 'Date & Time Updated'),
      'updatedBy' => Yii::t('admin.content.labels', 'Updated By Administrator'),
      '_mcv' => Yii::t('admin.content.labels', 'System View'),
      '_title' => Yii::t('admin.content.labels', 'Title'),
    );
  }

  public function relations()
  {
    return array(
      'site' => array(self::BELONGS_TO, 'VSite', 'siteID'),
      'layout' => array(self::BELONGS_TO, 'VSiteLayout', array('layoutID' => 'id', 'siteID' => 'siteID')),
      'l10n' => array(self::HAS_MANY, 'VSystemPageL10n', 'systemPageID'),
      'currentL10n' => array(self::HAS_ONE, 'VSystemPageL10n', 'systemPageID', 'on' => 'currentL10n.languageID=:currentLanguage', 'params' => array(':currentLanguage' => Yii::app()->getLanguage())),
      'mcv' => array(self::BELONGS_TO, 'VSystemView', array('module', 'controller', 'view')),
      'whoUpdated' => array(self::BELONGS_TO, 'VSiteAdmin', 'updatedBy'),
    );
  }

  public function behaviors()
  {
    return CMap::mergeArray(
        array(
        'localization' => array(
          'class' => 'VLocalizationBehavior',
        ),
        'guid' => array(
          'class' => 'VGuidBehavior',
        ),
        ), parent::behaviors()
    );
  }

  protected function afterFind()
  {
    parent::afterFind();
    $this->_mcv = implode(',', array(
      $this->module,
      $this->controller,
      $this->view,
    ));
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      $mcv = explode(',', $this->_mcv);
      if (count($mcv) > 2) {
        list($this->module, $this->controller, $this->view) = $mcv;
      }

      if ($this->isNewRecord) {
        $criteria = new CDbCriteria();
        $criteria->compare('t.siteID', $this->siteID);
        $criteria->compare('t.module', $this->module);
        $criteria->compare('t.controller', $this->controller);
        $criteria->compare('t.view', $this->view);
        if (self::count($criteria)) {
          $this->addError('_mcv', Yii::t('admin.content.errors', 'System page for selected view is already exists.'));
        }
      }

      return true;
    }

    return false;
  }

  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      $this->timeUpdated = time();
      $this->updatedBy = Yii::app()->hasComponent('user') ? Yii::app()->user->id : '';

      return true;
    }

    return false;
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.module', $this->module, true);
    $criteria->compare('t.controller', $this->controller, true);
    $criteria->compare('t.view', $this->view, true);
    $criteria->compare('currentL10n.name', $this->_title, true);
    $criteria->compare('t.layoutID', $this->layoutID);
    $criteria->with = array(
      'currentL10n',
      'layout',
      'mcv',
    );

    $this->addSiteCondition('siteID', $criteria);

    $sort = new CSort();
    $sort->attributes = array(
      '_title' => array(
        'asc' => 'currentL10n.title ASC',
        'desc' => 'currentL10n.title DESC',
      ),
      '_mcv' => array(
        'asc' => 'mcv.title ASC',
        'desc' => 'mcv.title DESC',
      ),
      'siteID',
    );
    $sort->defaultOrder = $this->quoteColumn('t.module') . ' ASC,' .
      $this->quoteColumn('t.controller') . ' ASC,' .
      $this->quoteColumn('t.view') . ' ASC';

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->params['defaultPageSize'],
      ),
      'sort' => $sort,
    ));
  }

  /**
   * Get page title
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
   * Set view content replacement pairs
   * @param array $replacement the replacement strings
   */
  public function setReplacement($replacement)
  {
    $this->_replace = $replacement;
  }

  /**
   * Return content params as array for replacement of key to value
   * @param array $additional additional params
   * @return array
   */
  public function getParams($additional = array())
  {
    $params = CMap::mergeArray(
        is_array($this->_replace) ? $this->_replace : array(), is_array($additional) ? $additional : array()
    );

    return $params;
  }

  /**
   * Get system view content for specified language
   * @param string $languageID language identifier
   * @return string
   */
  public function getContent($languageID)
  {
    return $this->getL10nModel($languageID, false)->content;
  }
}
