<?php
/**
 * ViraCMS Mail Templates Storage Model
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
 * @property string $module module identifier
 * @property string $name template name
 */
class VMailTemplate extends VActiveRecord
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
          'createMessage' => 'Mail template [{id}] "{subject}" has been created',
          'createParams' => array('{id}' => '$this->id', '{subject}' => '$this->getSubject()'),
          'updateMessage' => 'Mail template [{id}] "{subject}" has been updated',
          'updateParams' => array('{id}' => '$this->id', '{subject}' => '$this->getSubject()'),
          'deleteMessage' => 'Mail template [{id}] "{subject}" has been removed',
          'deleteParams' => array('{id}' => '$this->id', '{subject}' => '$this->getSubject()'),
          'translateCategory' => 'admin.content.events',
        ),
        'HistoryBehavior' => array(
          'class' => 'VHistoryBehavior',
        ),
      ));
    }
  }

  /**
   * @return VMailTemplate
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_mail_template}}';
  }

  public function relations()
  {
    return array(
      'l10n' => array(self::HAS_MANY, 'VMailTemplateL10n', 'templateID'),
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
        ), parent::behaviors()
    );
  }

  public function rules()
  {
    return array(
      array('id', 'unsafe', 'except' => 'create'),
      array('id', 'length', 'is' => 36),
      array('module,name', 'required'),
      array('name', 'length', 'max' => 255),
      array('module', 'length', 'max' => 32),
      array('id,module,name', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'module' => Yii::t('admin.content.labels', 'Module'),
      'name' => Yii::t('admin.content.labels', 'Template Name'),
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
    $criteria->compare('t.module', $this->module);
    $criteria->compare('t.name', $this->name, true);
    $criteria->with = array(
      'l10n',
    );

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
      ),
    ));
  }

  public function getSubject()
  {
    $model = $this->getL10nModel();
    return $model->subject;
  }
}
