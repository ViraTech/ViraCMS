<?php
/**
 * ViraCMS Application Widget's Factory Component
 * Based On Yii Framework CWidgetFactory Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VWidgetFactory extends CWidgetFactory
{
  /**
   * @var array available widgets
   *
   * Format is:
   * <pre>
   * array(
   *   array(
   *     'class' => 'WidgetClass',
   *     'baseAlias' => 'application.widgets',
   *     'name' => 'Widget Name',
   *     'translate' => 'TranslateCategory',
   *     'category' => 'custom',
   *   ),
   * )
   * </pre>
   */
  public $available = array();

  /**
   * @var array widget categories
   */
  public $categories = array();

  /**
   * @var string widgets base alias
   */
  public $defaultBaseAlias = 'application.widgets';

  /**
   * @return array registered widgets
   */
  public function getAvailableWidgets()
  {
    return $this->available;
  }

  /**
   * @param string $class widget identifier, can be full widget alias or simply class name
   * @return array widget configuration described in config file
   */
  public function getWidgetByClassName($class)
  {
    $available = $this->getAvailableWidgets();
    $found = null;

    foreach ($available as $widget) {
      if (empty($widget['class'])) {
        continue;
      }

      if (empty($widget['baseAlias'])) {
        $widget['baseAlias'] = implode('.', array($this->defaultBaseAlias, $widget['category']));
      }

      $widgetClass = explode('.', $widget['class']);
      $widgetClass = array_pop($widgetClass);

      if ($widgetClass == $class || $widget['class'] == $class) {
        $found = $widget;
        break;
      }
    }

    return $found;
  }

  /**
   * Return number of widgets for specified category
   * @param string $category widgets category
   * @return integer
   */
  public function getWidgetCount($category = null)
  {
    return count($this->getWidgets($category));
  }

  /**
   * Return widgets list for specified category
   * @param string $category widgets category
   * @return array
   */
  public function getWidgets($category = null)
  {
    $found = array();

    foreach ($this->available as $widget) {
      if ($category !== null && $widget['category'] != $category) {
        continue;
      }
      $id = explode('.', $widget['class']);
      $id = end($id);
      $found[] = array(
        'id' => $id,
        'title' => Yii::t($widget['translate'], $widget['name']),
      );
    }

    return $found;
  }

  /**
   * @return string non-configurable widget explain message
   */
  public function getNoConfigurationMessage()
  {
    return Yii::t('common', 'This widget is no need to configure');
  }
}
