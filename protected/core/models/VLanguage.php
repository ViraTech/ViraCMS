<?php
/**
 * ViraCMS Language Model
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $id ISO code
 * @property boolean $active language is active
 * @property string $locale locale
 * @property string $title title
 * @property integer $index sorting index
 */
class VLanguage extends VActiveRecord
{
  const DEFAULT_LANGUAGES_CACHE_DURATION = 3600;

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
          'createMessage' => 'Language [{id}] "{title}" has been created',
          'createParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'updateMessage' => 'Language [{id}] "{title}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'enableMessage' => 'Language [{id}] "{title}" has been enabled',
          'enableParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'disableMessage' => 'Language [{id}] "{title}" has been disabled',
          'disableParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'deleteMessage' => 'Language [{id}] "{title}" has been removed',
          'deleteParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'translateCategory' => 'admin.translate.events',
        ),
      ));
    }
  }

  /**
   * @param string $className
   * @return VLanguage
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  /**
   * @inheritdoc
   */
  public function tableName()
  {
    return '{{core_language}}';
  }

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return array(
      array('id,title', 'required'),
      array('id', 'length', 'max' => 2),
      array('id', 'unique'),
      array('id', 'match', 'pattern' => '/^[a-z]+$/', 'message' => Yii::t('common', 'Only lowercased latin characters allowed.')),
      array('active', 'boolean'),
      array('index', 'numerical', 'integerOnly' => true),
      array('locale', 'length', 'max' => 15),
      array('title', 'length', 'max' => 255),
      array('id,title', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @inheritdoc
   */
  public function scopes()
  {
    return array(
      'defaultOrder' => array(
        'order' => $this->quoteColumn('t.index') . ' ASC',
      ),
      'noSource' => array(
        'condition' => $this->quoteColumn('t.id') . ' <> :sourceLanguageID',
        'params' => array(
          ':sourceLanguageID' => Yii::app()->sourceLanguage,
        ),
      ),
    );
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('admin.translate.labels', 'ISO Code'),
      'active' => Yii::t('admin.translate.labels', 'Active'),
      'title' => Yii::t('admin.translate.labels', 'Title'),
      'locale' => Yii::t('admin.translate.labels', 'Locale'),
      'index' => Yii::t('admin.translate.labels', 'Position'),
    );
  }

  /**
   * Enable language
   * @return boolean
   */
  public function enable()
  {
    $this->setScenario('enable');
    $this->active = true;
    return $this->save();
  }

  /**
   * Disable language
   * @return boolean
   */
  public function disable()
  {
    $this->setScenario('disable');
    $this->active = false;
    return $this->save();
  }

  /**
   * @inheritdoc
   */
  protected function beforeDelete()
  {
    if (parent::beforeDelete()) {
      if ($this->id == Yii::app()->sourceLanguage) {
        $this->addError('id', Yii::t('admin.translate.errors', 'You may not delete source language, but you can disable it.'));
      }

      return true;
    }

    return false;
  }

  /**
   * @inheritdoc
   */
  protected function afterSave()
  {
    parent::afterSave();
    Yii::app()->cache->flush();
  }

  /**
   * @inheritdoc
   */
  protected function afterDelete()
  {
    parent::afterDelete();
    Yii::app()->cache->flush();
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.active', $this->active);
    $criteria->compare('t.title', $this->title, true);
    $criteria->compare('t.index', $this->index);
    $criteria->compare('t.locale', $this->locale, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->params['defaultPageSize'],
      ),
      'sort' => array(
        'defaultOrder' => $this->quoteColumn('t.index') . ' ASC',
      ),
    ));
  }
}
