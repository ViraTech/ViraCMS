<?php
/**
 * ViraCMS File Cache Component Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class FileCacheConfig extends BaseCacheConfig
{
  public $class = 'CFileCache';
  public $gCProbability = 1;

  public function init()
  {
    if (Yii::app()->hasComponent('cache') && get_class(Yii::app()->cache) == $this->class) {
      $this->gCProbability = min(1, floor(Yii::app()->cache->gCProbability / 10000));
    }
  }

  public function rules()
  {
    return array(
      array('gCProbability', 'required'),
      array('gCProbability', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 100),
    );
  }

  public function attributeLabels()
  {
    return array(
      'gCProbability' => Yii::t('admin.labels', 'Garbage Collection Performing Probability'),
    );
  }

  public function getFormAttributes()
  {
    return array(
      'gCProbability' => array(
        'type' => 'text',
        'width' => 'span2',
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
      'gCProbability' => $this->gCProbability * 10000,
    );
  }
}
