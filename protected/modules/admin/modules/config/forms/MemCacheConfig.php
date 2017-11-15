<?php
/**
 * ViraCMS MemCache Component Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class MemCacheConfig extends BaseCacheConfig
{
  public $class = 'CMemCache';
  public $useMemcached = false;
  public $server0Host = '127.0.0.1';
  public $server0Port = 11211;
  public $server0Weight = 50;
  public $server1Host = '';
  public $server1Port = '';
  public $server1Weight = '';
  public $server2Host = '';
  public $server2Port = '';
  public $server2Weight = '';

  public function init()
  {
    if (Yii::app()->hasComponent('cache') && get_class(Yii::app()->cache) == $this->class) {
      $cache = Yii::app()->cache;
      $this->useMemcached = $cache->useMemcached;
      if (isset($cache->servers[0])) {
        $this->server0Host = $cache->servers[0]->host;
        $this->server0Port = $cache->servers[0]->port;
        $this->server0Weight = $cache->servers[0]->weight;
      }
      if (isset($cache->servers[1])) {
        $this->server1Host = $cache->servers[1]->host;
        $this->server1Port = $cache->servers[1]->port;
        $this->server1Weight = $cache->servers[1]->weight;
      }
      if (isset($cache->servers[2])) {
        $this->server2Host = $cache->servers[2]->host;
        $this->server2Port = $cache->servers[2]->port;
        $this->server2Weight = $cache->servers[2]->weight;
      }
    }
  }

  public function rules()
  {
    return array(
      array('useMemcached', 'boolean'),
      array('server0Host,server0Port,server0Weight', 'required'),
      array('server0Host', 'length', 'min' => 1, 'max' => 255),
      array('server0Port', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 65530),
      array('server0Weight', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 100),
      array('server1Host,server2Host', 'length', 'min' => 1, 'max' => 255, 'allowEmpty' => true),
      array('server1Port,server2Port', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 65530, 'allowEmpty' => true),
      array('server1Weight,server2Weight', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 100, 'allowEmpty' => true),
    );
  }

  public function attributeLabels()
  {
    return array(
      'server0Host' => Yii::t('admin.labels', 'Server #{n} Host Name', array(1)),
      'server0Port' => Yii::t('admin.labels', 'Server #{n} TCP Port', array(1)),
      'server0Weight' => Yii::t('admin.labels', 'Server #{n} Weight', array(1)),
      'server1Host' => Yii::t('admin.labels', 'Server #{n} Host Name', array(2)),
      'server1Port' => Yii::t('admin.labels', 'Server #{n} TCP Port', array(2)),
      'server1Weight' => Yii::t('admin.labels', 'Server #{n} Weight', array(2)),
      'server2Host' => Yii::t('admin.labels', 'Server #{n} Host Name', array(3)),
      'server2Port' => Yii::t('admin.labels', 'Server #{n} TCP Port', array(3)),
      'server2Weight' => Yii::t('admin.labels', 'Server #{n} Weight', array(3)),
      'useMemcached' => Yii::t('admin.labels', 'Use Memcached extension instead of Memcache'),
    );
  }

  public function getFormAttributes()
  {
    return array(
      'server0Host' => array(
        'type' => 'text',
        'width' => 'span5',
      ),
      'server0Port' => array(
        'type' => 'text',
        'width' => 'span2',
      ),
      'server0Weight' => array(
        'type' => 'text',
        'width' => 'span2',
      ),
      'server1Host' => array(
        'type' => 'text',
        'width' => 'span5',
      ),
      'server1Port' => array(
        'type' => 'text',
        'width' => 'span2',
      ),
      'server1Weight' => array(
        'type' => 'text',
        'width' => 'span2',
      ),
      'server2Host' => array(
        'type' => 'text',
        'width' => 'span5',
      ),
      'server2Port' => array(
        'type' => 'text',
        'width' => 'span2',
      ),
      'server2Weight' => array(
        'type' => 'text',
        'width' => 'span2',
      ),
      'useMemcached' => array(
        'type' => 'boolean',
      ),
    );
  }

  public function attributeHints()
  {
    return array(
      'server0Weight' => Yii::t('admin.messages', 'Probability of using this server among all servers'),
    );
  }

  public function getConfiguration()
  {
    $servers = array(
      array(
        'host' => $this->server0Host,
        'port' => $this->server0Port,
        'weight' => $this->server0Weight,
      ),
    );

    if ($this->server1Host && $this->server1Port) {
      $servers[] = array(
        'host' => $this->server1Host,
        'port' => $this->server1Port,
        'weight' => $this->server1Weight,
      );
    }

    if ($this->server2Host && $this->server2Port) {
      $servers[] = array(
        'host' => $this->server2Host,
        'port' => $this->server2Port,
        'weight' => $this->server2Weight,
      );
    }

    return array(
      'class' => $this->class,
      'useMemcached' => $this->useMemcached,
      'servers' => $servers,
    );
  }
}
