<?php
/**
 * ViraCMS Message's Source Model
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $hash (default) MD5 hash of message source text
 * @property string $module module
 * @property string $category category
 * @property string $source source message text
 */
class VTranslateSource extends VActiveRecord
{
  const MESSAGE_CUT = 100;

  public $hashFunction = 'md5';

  /**
   * @param string $className
   * @return VTranslateSource
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_translate_source}}';
  }

  public function primaryKey()
  {
    return array(
      'hash',
      'module',
      'category',
    );
  }

  public function getId()
  {
    if ($this->isNewRecord) {
      return null;
    }

    return implode(',', array($this->hash, $this->module, $this->category));
  }

  public function relations()
  {
    return array(
      'translations' => array(self::HAS_MANY, 'VTranslate', array(
        'hash' => 'hash',
        'module' => 'module',
        'category' => 'category',
      )),
    );
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      $this->hash = $this->getMessageHash($this->source);

      $max = $this->isNewRecord ? 0 : 1;
      $count = self::model()->countByAttributes(array(
          'hash' => $this->hash,
          'module' => $this->module,
          'category' => $this->category,
        )) > $max;

      if ($count) {
        if ($this->getScenario() != 'auto') {
          $this->addError('source', Yii::t('admin.translate.errors', 'Source message for selected module and category already exists.'));
        }
        return false;
      }

      return true;
    }

    return false;
  }

  protected function beforeDelete()
  {
    if (parent::beforeDelete()) {
      VTranslate::model()->deleteAllByAttributes(array(
        'hash' => $this->hash,
        'module' => $this->module,
        'category' => $this->category,
      ));
      return true;
    }

    return false;
  }

  public function rules()
  {
    return array(
      array('category,source,hash', 'required'),
      array('hash', 'length', 'max' => 128),
      array('module,category', 'length', 'max' => 32),
      array('source', 'length', 'min' => 1),
      array('module,category,source', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'module' => Yii::t('admin.translate.labels', 'Module'),
      'category' => Yii::t('admin.translate.labels', 'Category'),
      'source' => Yii::t('admin.translate.labels', 'Message'),
    );
  }

  /**
   * Performs search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.module', $this->module, true);
    $criteria->compare('t.category', $this->category, true);
    $criteria->compare('t.source', $this->source, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        'pageVar' => 'page',
      ),
      'sort' => array(
        'defaultOrder' => $this->quoteColumn('t.module') . ' ASC,' .
        $this->quoteColumn('t.category') . ' ASC',
      ),
    ));
  }

  public function getMessageHash($message)
  {
    return call_user_func($this->hashFunction, $message);
  }
}
