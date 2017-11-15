<?php
/**
 * ViraCMS Public Events Manager Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VEventManager extends CApplicationComponent
{
  /**
   * @var array configured public events and it's handlers
   */
  public $events = array();

  /**
   * @var array event handlers classes cache
   */
  private $_h = array();

  /**
   * Get, verify and attach events to component
   * @param CComponent $component the component to attach events to
   */
  public function attach($component)
  {
    foreach ($this->events as $event) {
      if (count($event) < 3) {
        continue;
      }

      if (is_a($component, $event[0])) {
        $this->attachHandler($component, $event);
      }
    }
  }

  /**
   * Attach provided event handler to the component
   * @param CComponent $component the component
   * @param array $event the event handler as defined in configuration file
   */
  protected function attachHandler($component, $event)
  {
    if (is_array($event[2]) && count($event[2]) > 1) {
      $handler = $this->getEventHandler($event[2][0]);
      if ($handler !== false && $event[2][1]) {
        $component->attachEventHandler($event[1], array($handler, $event[2][1]));
      }
    }
  }

  /**
   * Return cached event handler class object or create a new one if it's exist
   * @param string $className
   * @return mixed generally @see VEventHandler class object
   */
  protected function getEventHandler($className)
  {
    if (stripos($className, '.') !== false) {
      $classPath = explode('.', $className);
      $className = $classPath[count($classPath) - 1];
      $classPath = implode('.', $classPath);
    }
    if (!isset($this->_h[$className])) {
      if (isset($classPath)) {
        Yii::import($classPath);
      }
      if (@class_exists($className)) {
        $this->_h[$className] = new $className;
      }
    }

    return isset($this->_h[$className]) ? $this->_h[$className] : false;
  }
}
