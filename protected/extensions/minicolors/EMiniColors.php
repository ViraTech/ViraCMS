<?php
/**
 * Wrapper for jQuery MiniColors Plugin (https://github.com/claviska/jquery-minicolors)
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class EMiniColors extends CInputWidget
{
  /**
   * @var array plugin default settings
   */
  private $_defaultSettings = array(
    'animationSpeed' => 50,
    'animationEasing' => 'swing',
    'change' => null,
    'changeDelay' => 0,
    'control' => 'hue',
    'defaultValue' => '',
    'hide' => null,
    'hideSpeed' => 100,
    'inline' => false,
    'letterCase' => 'lowercase',
    'opacity' => false,
    'position' => 'bottom left',
    'show' => null,
    'showSpeed' => 100,
    'theme' => 'default'
  );

  /**
   * @var string URL of published assets
   */
  private $assetsUrl;

  /**
   * @var CClientScript
   */
  private $cs;

  /**
   * @var array plugin settings
   */
  public $settings = array();

  /**
   * @var integer Script position as it's accepted by CClientScript::registerScript
   */
  public $scriptPosition;

  public function init()
  {
    $this->assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
    $this->cs = Yii::app()->clientScript;
    $this->cs->registerCoreScript('jquery');
    $this->cs->registerScriptFile($this->assetsUrl . '/js/jquery.minicolors' . (YII_DEBUG ? '' : '.min') . '.js', $this->scriptPosition ? $this->scriptPosition : CClientScript::POS_HEAD);
    $this->cs->registerCssFile($this->assetsUrl . '/css/jquery.minicolors.css');
    list($this->name, $this->id) = $this->resolveNameId();
  }

  public function run()
  {
    $options = array();

    foreach ($this->settings as $key => $value) {
      if ($this->_defaultSettings[$key] != $value) {
        $options[$key] = $value;
      }
    }

    $options = empty($options) ? '' : CJavaScript::encode($options);

    if ($this->hasModel()) {
      echo '<div class="control-group">';
      echo CHtml::activeLabelEx($this->model, $this->attribute, array('class' => 'control-label'));
      echo '<div class="controls">';
    }
    echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
    if ($this->hasModel()) {
      echo '</div>';
      echo '</div>';
    }

    $this->cs->registerScript(get_class($this) . '#' . $this->id, "jQuery('#{$this->id}').minicolors({$options});", $this->scriptPosition ? $this->scriptPosition : CClientScript::POS_HEAD);
  }
}
