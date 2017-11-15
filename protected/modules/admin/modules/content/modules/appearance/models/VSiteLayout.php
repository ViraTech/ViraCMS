<?php
/**
 * ViraCMS Page Layout Model
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id the primary key
 * @property string $siteID the site identifier
 * @property boolean $default is it default layout for this site?
 * @property string $title the title
 * @property string $linkColor links color
 * @property string $linkHoverColor hovered/active links color
 * @property string $linkVisitedColor visited links color
 * @property string $bodyBackgroundColor body background color
 * @property string $bodyBackgroundImage body background image identifier @see VContentImage
 * @property string $favIconImage favourite icon image identifier @see VContentImage
 * @property string $styleOverride additional style
 * @property string $metaTags additional meta tags
 */
class VSiteLayout extends VActiveRecord
{
  private $_originalID;

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
          'createMessage' => 'Page layout [{id}] "{title}" has been created',
          'createParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'updateMessage' => 'Page layout [{id}] "{title}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'deleteMessage' => 'Page layout [{id}] "{title}" has been removed',
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
   * @return VSiteLayout
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_site_layout}}';
  }

  public function primaryKey()
  {
    return array(
      'id',
      'siteID',
    );
  }

  public function relations()
  {
    return array(
      'site' => array(self::BELONGS_TO, 'VSite', 'siteID'),
      'rows' => array(self::HAS_MANY, 'VPageRow', array('siteID' => 'siteID', 'layoutID' => 'id')),
      'blocks' => array(self::HAS_MANY, 'VPageBlock', array('siteID' => 'siteID', 'layoutID' => 'id')),
      'backgroundImage' => array(self::BELONGS_TO, 'VContentImage', 'bodyBackgroundImage'),
      'iconImage' => array(self::BELONGS_TO, 'VContentImage', 'favIconImage'),
      'areas' => array(self::MANY_MANY, 'VPageArea', '{{core_layout_area}}(layoutID,siteID,pageAreaID)'),
    );
  }

  public function from($siteID)
  {
    $this->getDbCriteria()->mergeWith(array(
      'condition' => 'siteID=:siteID',
      'params' => array(
        ':siteID' => $siteID,
      ),
    ));

    return $this;
  }

  public function rules()
  {
    return array(
      array('id,siteID', 'required', 'on' => 'create'),
      array('siteID', 'length', 'is' => 36),
      array('title', 'length', 'max' => 255),
      array('bodyBackgroundImage,favIconImage', 'exist', 'className' => 'VContentImage', 'attributeName' => 'id', 'allowEmpty' => true),
      array('id', 'match', 'pattern' => '/^[a-z]+$/', 'message' => Yii::t('common', 'Only lowercased latin characters allowed.')),
      array('id', 'length', 'max' => 64),
      array('default', 'boolean'),
      array('bodyTextColor,bodyBackgroundColor,linkColor,linkHoverColor,linkVisitedColor', 'length', 'max' => 7),
      array('styleOverride,metaTags', 'length', 'max' => 65500),
      array('siteID', 'unsafe', 'except' => 'create,search'),
      array('id,siteID,title,siteID,default', 'safe', 'on' => 'search'),
    );
  }

  protected function afterFind()
  {
    parent::afterFind();
    $this->_originalID = $this->id;
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      if ($this->siteID && $this->id) {
        $c = self::model()->count('id=:id AND siteID=:siteID', array(
          ':id' => $this->id,
          ':siteID' => $this->siteID,
        ));
        if (!($c < ($this->isNewRecord ? 1 : 2))) {
          $this->addError('id', Yii::t('admin.content.errors', 'Layout "{id}" is already exists.', array('{id}' => $this->id)));
        }
      }

      return true;
    }

    return false;
  }

  protected function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->default) {
        self::model()->updateAll(array('default' => '0'), 'siteID=:siteID', array(':siteID' => $this->siteID));
      }

      return true;
    }

    return false;
  }

  protected function afterSave()
  {
    parent::afterSave();
    if (!$this->isNewRecord && $this->id != $this->_originalID) {
      foreach (VPageBlock::model()->findAllByAttributes(array('siteID' => $this->siteID, 'layoutID' => $this->_originalID)) as $block) {
        $block->layoutID = $this->id;
        $block->save();
      }
      foreach (VPageRow::model()->findAllByAttributes(array('siteID' => $this->siteID, 'layoutID' => $this->_originalID)) as $row) {
        $row->layoutID = $this->id;
        $row->save();
      }
    }
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('admin.content.labels', 'Layout ID'),
      'siteID' => Yii::t('admin.content.labels', 'Site'),
      'title' => Yii::t('admin.content.labels', 'Title'),
      'default' => Yii::t('admin.content.labels', 'Default Layout'),
      'linkColor' => Yii::t('admin.content.labels', 'Links Color'),
      'linkHoverColor' => Yii::t('admin.content.labels', 'Hovered Links Color'),
      'linkVisitedColor' => Yii::t('admin.content.labels', 'Visited Links Color'),
      'bodyTextColor' => Yii::t('admin.content.labels', 'Body Text Color'),
      'bodyBackgroundColor' => Yii::t('admin.content.labels', 'Body Background Color'),
      'bodyBackgroundImage' => Yii::t('admin.content.labels', 'Body Background Image'),
      'favIconImage' => Yii::t('admin.content.labels', 'Favourite Icon Image'),
      'styleOverride' => Yii::t('admin.content.labels', 'CSS Override'),
      'metaTags' => Yii::t('admin.content.labels', 'Additional Meta Tags'),
    );
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('t.default', $this->default);
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.title', $this->title, true);

    $this->addSiteCondition('siteID', $criteria);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        'pageVar' => 'page',
      ),
    ));
  }
}
