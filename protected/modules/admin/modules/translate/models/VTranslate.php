<?php
/**
 * ViraCMS Message Translation Model
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
 * @property string $module module name
 * @property string $category message category name
 * @property string $languageID language ID
 * @property string $translate translated message text
 */
class VTranslate extends VActiveRecord
{
  const MESSAGE_CUT = 100;

  /**
   * @param string $className
   * @return VTranslate
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_translate}}';
  }

  public function primaryKey()
  {
    return array(
      'hash',
      'module',
      'category',
      'languageID',
    );
  }

  public function getId()
  {
    if ($this->isNewRecord) {
      return null;
    }

    return implode(',', array($this->hash, $this->module, $this->category, $this->languageID));
  }

  public function relations()
  {
    return array(
      'source' => array(self::BELONGS_TO, 'VTranslateSource', array(
          'hash' => 'hash',
          'module' => 'module',
          'category' => 'category',
        )),
      'language' => array(self::BELONGS_TO, 'VLanguage', 'languageID'),
    );
  }

  public function rules()
  {
    return array(
      array('hash,category,languageID,translate', 'required'),
      array('languageID', 'length', 'max' => 2),
      array('hash', 'length', 'max' => 128),
      array('module,category', 'length', 'max' => 32),
      array('languageID,module,category,translate', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'module' => Yii::t('admin.translate.labels', 'Module'),
      'category' => Yii::t('admin.translate.labels', 'Category'),
      'languageID' => Yii::t('admin.translate.labels', 'Language'),
      'translate' => Yii::t('admin.translate.labels', 'Translation'),
    );
  }

  protected function beforeValidate()
  {
    if (parent::beforeValidate()) {
      $count = self::model()->countByAttributes(array(
          'hash' => $this->hash,
          'module' => $this->module,
          'category' => $this->category,
          'languageID' => $this->languageID,
        )) > ($this->isNewRecord ? 0 : 1);

      if ($count) {
        if ($this->getScenario() != 'auto') {
          $this->addError('languageID', Yii::t('admin.translate.errors', 'Translation for selected module, category, language and source message is already exists.'));
        }
        return false;
      }

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
    $criteria->with[] = 'language';
    $criteria->with[] = 'source';
    $criteria->compare('t.languageID', $this->languageID);
    $criteria->compare('t.module', $this->module, true);
    $criteria->compare('t.category', $this->category, true);
    $criteria->compare('t.translate', $this->translate, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array(
        'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        'pageVar' => 'page',
      ),
      'sort' => array(
        'defaultOrder' => $this->quoteColumn('t.module') . ' ASC,' .
        $this->quoteColumn('t.category') . ' ASC,' .
        $this->quoteColumn('t.languageID') . ' ASC',
      ),
    ));
  }
}
