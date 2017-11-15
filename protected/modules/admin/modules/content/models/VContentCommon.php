<?php
/**
 * ViraCMS Shared Content Block Model
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
 * @property string $title title
 * @property string $content html contents
 * @property string $style attached stylesheet
 * @property string $script attached javascript
 */
class VContentCommon extends VActiveRecord
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
          'createMessage' => 'Shared content block [{id}] "{title}" has been created',
          'createParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'updateMessage' => 'Shared content block [{id}] "{title}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'deleteMessage' => 'Shared content block [{id}] "{title}" has been removed',
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
   * @return VContentCommon
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
    return '{{core_content_common}}';
  }

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id', 'VGuidValidator'),
      array('content,style,script', 'safe', 'on' => 'create,update'),
      array('title', 'length', 'max' => 1022),
      array('title', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @inheritdoc
   */
  protected function afterSave()
  {
    parent::afterSave();
    $this->clearCache();
  }

  /**
   * @inheritdoc
   */
  protected function afterDelete()
  {
    parent::afterDelete();
    $this->clearCache();
  }

  /**
   * @inheritdoc
   */
  public function behaviors()
  {
    return CMap::mergeArray(
        array(
        'guid' => array(
          'class' => 'VGuidBehavior',
        ),
        ), parent::behaviors()
    );
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'title' => Yii::t('admin.content.labels', 'Block Title'),
      'content' => Yii::t('admin.content.labels', 'Block Content'),
    );
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.title', $this->title, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        'pageVar' => 'page',
      ),
      'sort' => array(
        'defaultOrder' => $this->quoteColumn('t.id') . ' DESC',
      ),
    ));
  }

  /**
   * Named scope "order by name"
   * @return VContentCommon
   */
  public function orderByName()
  {
    $this->getDbCriteria()->mergeWith(array(
      'order' => $this->quoteColumn('t.title') . ' ASC',
    ));

    return $this;
  }

  /**
   * Delete cache tag
   */
  public function clearCache()
  {
    if (Yii::app()->hasComponent('cache')) {
      Yii::app()->cache->delete('Vira.Shared.' . $this->id);
    }
  }
}
