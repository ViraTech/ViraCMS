<?php
/**
 * Application collections component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCollection extends VApplicationComponent
{
  /**
   * @var array configured collections
   */
  public $collections = array();

  /**
   * @var array import aliases
   */
  public $import = array();

  public function init()
  {
    foreach ($this->import as $alias) {
      Yii::import($alias);
    }

    parent::init();
  }

  public function __get($name)
  {
    if (isset($this->collections[$name])) {
      if (!empty($this->collections[$name]['class'])) {
        if (stripos($this->collections[$name]['class'],'.') !== false) {
          $className = Yii::import($this->collections[$name]['class']);
        }
        else {
          $className = $this->collections[$name]['class'];
        }
        if (class_exists($className)) {
          if (empty($this->collections[$name]['instance'])) {
            $this->collections[$name]['instance'] = new $className;
          }
          return $this->collections[$name]['instance'];
        }
      }
    }

    return parent::__get($name);
  }
}
