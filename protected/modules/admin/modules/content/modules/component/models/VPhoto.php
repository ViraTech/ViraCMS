<?php
/**
 * ViraCMS Photo Gallery Model
 *
 * @package vira.core.core
 * @subpackage vira.core.bootstrap
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id primary key
 * @property string $siteID site identifier
 * @property string $languageID language identifier
 * @property boolean $public published flag
 * @property string $title photo gallery title
 */
class VPhoto extends VActiveRecord
{
  /**
   * @inheritdoc
   */
  public function init()
  {
    parent::init();

    // attach administrative CRUD behaviours only when created inside the system CRUD controller
    if (is_a(Yii::app()->getController(), 'VSystemController')) {
      $this->attachBehaviors(array(
        'GuidBehavior' => array(
          'class' => 'core.behaviors.VGuidBehavior',
          'type' => VGuidBehavior::GUID_STRAIGHT,
        ),
        'SystemLogBehavior' => array(
          'class' => 'VSystemLogBehavior',
          'createMessage' => 'Photo [{id}] "{title}" has been created',
          'createParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'updateMessage' => 'Photo [{id}] "{title}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'enableMessage' => 'Photo [{id}] "{title}" has been published',
          'enableParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'disableMessage' => 'Photo [{id}] "{title}" has been hidden',
          'disableParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'deleteMessage' => 'Photo [{id}] "{title}" has been removed',
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
   * @return VPhoto
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  /**
   * Table name
   * @return string
   */
  public function tableName()
  {
    return '{{core_photo}}';
  }

  /**
   * @inheritdoc
   */
  public function relations()
  {
    return array(
      'site' => array(self::BELONGS_TO, 'VSite', 'siteID'),
      'language' => array(self::BELONGS_TO, 'VLanguage', 'languageID'),
      // images
      'images' => array(self::HAS_MANY, 'VPhotoImage', 'ownerID', 'order' => 'images.sort ASC'),
      'primaryImage' => array(self::HAS_ONE, 'VPhotoImage', 'ownerID', 'order' => 'primaryImage.sort ASC'),
      'imageQty' => array(self::STAT, 'VPhotoImage', 'ownerID'),
      // related images
      'uploadedImages' => array(self::HAS_MANY, 'VContentImage', 'primaryKey', 'condition' => 'uploadedImages.className=:className', 'params' => array(':className' => __CLASS__)),
    );
  }

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return array(
      array('id', 'required', 'on' => 'create'),
      array('id', 'VGuidValidator', 'on' => 'create'),
      array('siteID', 'exist', 'className' => 'VSite', 'attributeName' => 'id', 'allowEmpty' => true),
      array('languageID', 'exist', 'className' => 'VLanguage', 'attributeName' => 'id'),
      array('title', 'required'),
      array('title', 'length', 'max' => 1022),
      array('public', 'boolean'),
      array('id,siteID,languageID,title,public', 'safe', 'on' => 'search'),
    );
  }

  public function scopes()
  {
    return array(
      'published' => array('condition' => 't.public>0'),
    );
  }

  /**
   * @inheritdoc
   */
  protected function afterDelete()
  {
    if ($this->images) {
      foreach ($this->images as $image) {
        $image->delete();
      }
    }

    parent::afterDelete();
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'siteID' => Yii::t('admin.content.labels', 'Site'),
      'languageID' => Yii::t('admin.content.labels', 'Language'),
      'title' => Yii::t('admin.content.labels', 'Title'),
      'public' => Yii::t('admin.content.labels', 'Published'),
    );
  }

  /**
   * Returns database search criteria for published photo galleries
   * related to the current site.
   * @return \CDbCriteria
   */
  public function getPublicCriteria()
  {
    $criteria = new CDbCriteria();

    $criteria->condition = "(t.siteID IS NULL OR t.siteID = '' OR t.siteID = :siteID)";
    $criteria->params[':siteID'] = Yii::app()->site->id;

    $criteria->compare('t.languageID', Yii::app()->getLanguage());
    $criteria->compare('t.public', '>0');

    $criteria->with = array(
      'images',
      'images.image',
    );

    $criteria->order = 't.id DESC';

    return $criteria;
  }

  /**
   * Widget named scope
   * @param string $photoID the photo gallery identifier (optional)
   * @param string $siteID the site identifier (optional)
   * @return RtpPhoto
   */
  public function widget($photoID = null, $siteID = null)
  {
    $criteria = $this->getPublicCriteria();

    $criteria->params[':siteID'] = $siteID ? $siteID : Yii::app()->site->id;

    if ($photoID) {
      $criteria->compare('t.id', $photoID);
    }

    $this->getDbCriteria()->mergeWith($criteria);

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
    $criteria->compare('t.public', $this->public);
    $criteria->compare('t.languageID', $this->languageID);

    $this->addSiteCondition('siteID', $criteria);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
      ),
      'sort' => array(
        'defaultOrder' => $this->quoteColumn('t.id') . ' DESC',
      ),
    ));
  }
}
