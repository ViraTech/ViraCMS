<?php
/**
 * ViraCMS Redis Cache Component Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class RedisCacheConfig extends BaseCacheConfig
{
  public $class = 'CRedisCache';
  public $hostname = 'localhost';
  public $port = 6379;
  public $database = 0;

  public function init()
  {
    if (Yii::app()->hasComponent('cache') && get_class(Yii::app()->cache) == $this->class) {
      $this->hostname = Yii::app()->cache->hostname;
      $this->port = Yii::app()->cache->port;
      $this->database = Yii::app()->cache->database;
    }
  }

  public function rules()
  {
    return array(
      array('hostname,port,database', 'required'),
      array('hostname', 'length', 'min' => 1, 'max' => 255),
      array('port', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 65530,),
      array('database', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 100),
    );
  }

  public function attributeLabels()
  {
    return array(
      'hostname' => Yii::t('admin.labels', 'Redis Server Host Name'),
      'port' => Yii::t('admin.labels', 'Redis Server TCP Port'),
      'database' => Yii::t('admin.labels', 'Redis Database #'),
    );
  }

  public function getFormAttributes()
  {
    return array(
      'hostname' => array(
        'type' => 'text',
        'width' => 'span5',
      ),
      'port' => array(
        'type' => 'text',
        'width' => 'span2',
      ),
      'database' => array(
        'type' => 'text',
        'width' => 'span2',
      ),
    );
  }

  public function getConfiguration()
  {
    return array(
      'class' => $this->class,
      'hostname' => $this->hostname,
      'port' => $this->port,
      'database' => $this->database,
    );
  }
}
