<?php
/**
 * ViraCMS Widget Renderer Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VWidgetRenderer extends VApplicationComponent
{
  /**
   * @var string widget class name
   */
  protected $_widget;

  /**
   * @var array widget properties
   */
  protected $_properties;

  /**
   * @var boolean render widget scripts
   */
  protected $_scripts = true;

  /**
   * @var boolean enable cache
   */
  protected $_cache = true;

  /**
   * Initialize renderer
   * @param array $data widget configuration data
   */
  function __construct($data)
  {
    $data = @unserialize($data);

    if (is_array($data) && isset($data['class'])) {
      $this->_widget = $data['class'];
      $this->_properties = is_array($data['params']) ? $data['params'] : array();
    }
  }

  /**
   * Enable custom javascript rendering
   */
  public function enableScripts()
  {
    $this->_scripts = true;
  }

  /**
   * Disable custom javascript rendering
   */
  public function disableScripts()
  {
    $this->_scripts = false;
  }

  /**
   * Enable output cacheing
   */
  public function enableCache()
  {
    $this->_cache = true;
  }

  /**
   * Disable output cacheing
   */
  public function disableCache()
  {
    $this->_cache = false;
  }

  /**
   * Return that this type of content is dynamic
   * @return boolean
   */
  public function getIsDynamic()
  {
    return true;
  }

  /**
   * Render the widget
   * @return string
   */
  public function render()
  {
    $controller = Yii::app()->getController();

    $widget = $this->createWidget($controller, Yii::app()->widgetFactory->getWidgetByClassName($this->_widget));

    $output = $this->renderWidget($controller, $widget);

    return $output;
  }

  /**
   * Create the widget in current context
   * @param VController $controller the controller
   * @param array $config widget configuration
   * @return mixed
   */
  protected function createWidget($controller, $config)
  {
    $widget = false;

    if (is_array($config)) {
      try {
        $widget = $controller->createWidget(
          implode('.', array($config['baseAlias'], $config['class'])), $this->_properties
        );
      }
      catch (Exception $e) {
        $widget = false;
      }
    }

    return $widget;
  }

  /**
   * Render widget contents
   * @param VController $controller the controller
   * @param mixed $widget the widget object
   * @return string
   */
  protected function renderWidget($controller, $widget)
  {
    if (!is_a($widget, 'CWidget')) {
      return;
    }

    if (!$this->_cache && property_exists($widget, 'cacheEnabled')) {
      $widget->cacheEnabled = false;
    }

    if (!$this->_scripts) {
      $scripts = Yii::app()->getClientScript()->scripts;
    }

    if ($this->_cache && is_a($widget, 'VWidget')) {
      $output = $controller->widget('application.widgets.core.VAutoCacheWidget', array(
        'widget' => $widget,
        ), true);
    }
    else {
      ob_start();
      ob_implicit_flush(false);
      $widget->run();
      $output = ob_get_clean();
    }

    if (!$this->_scripts) {
      Yii::app()->getClientScript()->scripts = $scripts;
    }

    return $output;
  }
}
