<?php
/**
 * ViraCMS Content Row Template Model
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
 * @property string $template body
 */
class VContentTemplate extends VActiveRecord
{
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
          'createMessage' => 'Content row template [{id}] "{title}" has been created',
          'createParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'updateMessage' => 'Content row template [{id}] "{title}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{title}' => '$this->title'),
          'deleteMessage' => 'Content row template [{id}] "{title}" has been removed',
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
   * @return VContentTemplate
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_content_template}}';
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

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id', 'length', 'is' => 36),
      array('title,template', 'required'),
      array('title', 'length', 'max' => 255),
      array('template', 'length', 'max' => 65530),
      array('id,title', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'title' => Yii::t('admin.content.labels', 'Title'),
      'template' => Yii::t('admin.content.labels', 'Template Body'),
    );
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

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'sort' => array(
        'defaultOrder' => Yii::app()->db->quoteColumnName('t.title') . ' ASC',
      ),
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
      ),
    ));
  }

  /**
   * Named scope "order by template name"
   * @return \VContentTemplate
   */
  public function orderByName()
  {
    $this->getDbCriteria()->mergeWith(array(
      'order' => Yii::app()->db->quoteColumnName('t.title') . ' ASC',
    ));

    return $this;
  }
}
