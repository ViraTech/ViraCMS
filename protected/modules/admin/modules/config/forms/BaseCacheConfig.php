<?php
/**
 * ViraCMS Base Cache Component Configuration Form Class
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
abstract class BaseCacheConfig extends CFormModel
{
  public $class;
  public $tested = true;

  abstract function getConfiguration();

  abstract function getFormAttributes();

  public function attributeHints()
  {
    return array();
  }

  protected function afterValidate()
  {
    if (!$this->hasErrors()) {
      $this->test();
    }
    parent::afterValidate();
  }

  public function test()
  {
    $error = '';
    $cache = Yii::createComponent($this->configuration);
    if (!$cache) {
      $this->tested = false;
      $error = Yii::t('admin.errors', 'Can not create application component.');
    }

    if ($this->tested) {
      try {
        $cache->init();
      }
      catch (Exception $e) {
        $error = $e->getMessage();
        $this->tested = false;
      }
    }

    if ($this->tested) {
      try {
        $data = $cache->set('test', 'test', 0);
      }
      catch (Exception $e) {
        $error = $e->getMessage();
        $this->tested = false;
      }
    }

    if ($this->tested) {
      try {
        $data = $cache->get('test');
        $cache->delete('test');
      }
      catch (Exception $e) {
        $error = $e->getMessage();
        $this->tested = false;
      }

      if ($data != 'test') {
        $this->tested = false;
      }
    }

    if (!$this->tested) {
      $this->addError('tested', $error ? $error : Yii::t('admin.errors', 'Cache test has been failed.'));
    }

    return $this->tested;
  }
}
