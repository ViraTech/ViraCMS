<?php
/**
 * ViraCMS Database Cache Component Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class DbCacheConfig extends BaseCacheConfig
{
  public $class = 'CDbCache';
  public $gCProbability = 1;
  public $cacheTableName = 'YiiCache';

  public function init()
  {
    if (Yii::app()->hasComponent('cache') && get_class(Yii::app()->cache) == $this->class) {
      $this->gCProbability = Yii::app()->cache->gCProbability;
      $this->cacheTableName = Yii::app()->cache->cacheTableName;
    }
  }

  public function rules()
  {
    return array(
      array('gCProbability,cacheTableName', 'required'),
      array('gCProbability', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 100),
      array('cacheTableName', 'length', 'min' => 3, 'max' => 64),
    );
  }

  public function attributeLabels()
  {
    return array(
      'gCProbability' => Yii::t('admin.labels', 'Garbage Collection Performing Probability'),
      'cacheTableName' => Yii::t('admin.labels', 'Database Table Name'),
    );
  }

  public function getFormAttributes()
  {
    return array(
      'gCProbability' => array(
        'type' => 'text',
        'width' => 'span2',
      ),
      'cacheTableName' => array(
        'type' => 'text',
        'width' => 'span8',
      ),
    );
  }

  public function attributeHints()
  {
    return array(
      'gCProbability' => Yii::t('admin.messages', 'Set in percents, zero means that garbage collection is never be performed.'),
    );
  }

  public function getConfiguration()
  {
    return array(
      'class' => $this->class,
      'gCProbability' => $this->gCProbability,
      'cacheTableName' => $this->cacheTableName,
    );
  }
}
