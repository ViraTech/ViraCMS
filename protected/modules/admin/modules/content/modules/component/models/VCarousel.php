<?php
/**
 * ViraCMS Core Carousel Model
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
 * @property boolean $public published flag
 */
class VCarousel extends VActiveRecord
{
  public $_title;

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
          'createMessage' => 'Carousel [{id}] "{title}" has been created',
          'createParams' => array('{id}' => '$this->id', '{title}' => '$this->getTitle()'),
          'updateMessage' => 'Carousel [{id}] "{title}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{title}' => '$this->getTitle()'),
          'enableMessage' => 'Carousel [{id}] "{title}" has been published',
          'enableParams' => array('{id}' => '$this->id', '{title}' => '$this->getTitle()'),
          'disableMessage' => 'Carousel [{id}] "{title}" has been hidden',
          'disableParams' => array('{id}' => '$this->id', '{title}' => '$this->getTitle()'),
          'deleteMessage' => 'Carousel [{id}] "{title}" has been removed',
          'deleteParams' => array('{id}' => '$this->id', '{title}' => '$this->getTitle()'),
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
   * @return VCarousel
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
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
        ), parent::behaviors()
    );
  }

  public function tableName()
  {
    return '{{core_carousel}}';
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id,siteID', 'length', 'is' => 36),
      array('siteID', 'required'),
      array('public', 'boolean'),
      array('public,siteID,_title', 'safe', 'on' => 'search'),
    );
  }

  public function scopes()
  {
    return array(
      'published' => array('condition' => 't.public>0'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'siteID' => Yii::t('admin.content.labels', 'Site'),
      'public' => Yii::t('admin.content.labels', 'Published'),
      'imagesQty' => Yii::t('admin.content.labels', 'Number of Images'),
      '_title' => Yii::t('admin.content.labels', 'Title'),
    );
  }

  public function relations()
  {
    return array(
      'site' => array(self::BELONGS_TO, 'VSite', 'siteID'),
      'images' => array(self::HAS_MANY, 'VCarouselImage', 'carouselID', 'order' => Yii::app()->db->quoteColumnName('images.position') . ' ASC'),
      'imagesQty' => array(self::STAT, 'VCarouselImage', 'carouselID'),
      'l10n' => array(self::HAS_MANY, 'VCarouselL10n', 'carouselID'),
      'currentL10n' => array(self::HAS_ONE, 'VCarouselL10n', 'carouselID', 'on' => 'currentL10n.languageID=:currentLanguage', 'params' => array(':currentLanguage' => Yii::app()->getLanguage())),
    );
  }

  public function enable()
  {
    $this->setScenario('enable');
    $this->public = true;
    return $this->save();
  }

  public function disable()
  {
    $this->setScenario('disable');
    $this->public = false;
    return $this->save();
  }

  protected function afterSave()
  {
    parent::afterSave();
    $this->clearCache();
  }

  protected function afterDelete()
  {
    parent::afterDelete();

    if ($this->images) {
      foreach ($this->images as $image) {
        $image->delete();
      }
    }

    if ($this->l10n) {
      foreach ($this->l10n as $l10n) {
        $l10n->delete();
      }
    }

    $this->clearCache();
  }

  /**
   * @return string carousel title in current language context
   */
  public function getTitle()
  {
    if (empty($this->_title)) {
      if ($this->hasRelated('currentL10n') && $this->currentL10n) {
        $this->_title = $this->currentL10n->title;
      }
      else {
        $l10n = $this->getL10nModel();
        $this->_title = $l10n->title;
      }
    }

    return empty($this->_title) ? $this->id : $this->_title;
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.public', $this->public);
    $criteria->compare('currentL10n.title', $this->_title);
    $criteria->with = array(
      'site',
      'currentL10n',
    );

    $this->addSiteCondition('siteID', $criteria);

    $sort = new CSort();
    $sort->defaultOrder = 't.id DESC';
    $sort->attributes = array(
      'public',
      'siteID' => array(
        'asc' => 'site.title ASC',
        'desc' => 'site.title DESC',
      ),
      '_title' => array(
        'asc' => 'currentL10n.title ASC',
        'desc' => 'currentL10n.title DESC',
      ),
    );

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
      ),
      'sort' => $sort,
    ));
  }

  /**
   * Clear cache tag associated with this content type
   */
  public function clearCache()
  {
    Yii::app()->cache->deleteTag('Vira.Content.Core.Carousel');
  }
}
