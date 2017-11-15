<?php
/**
 * Wrapper for jQuery Select2 (https://github.com/ivaynberg/select2)
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ESelect2 extends CInputWidget
{
  /**
   * @var array Select2 options
   */
  public $options = array();

  /**
   * @var array CHtml::dropDownList $data param
   */
  public $data = null;

  /**
   * @var integer Script position
   */
  public $scriptPosition = CClientScript::POS_END;

  /**
   * @var string Html element selector
   */
  public $selector;

  /**
   * @var array Select2 event handlers
   */
  public $events = array();

  public function run()
  {
    if ($this->selector == null) {
      list($this->name, $this->id) = $this->resolveNameId();
      $this->selector = '#' . $this->id;

      if (!isset($this->htmlOptions['multiple']) && isset($this->options['placeholder'])) {
        $this->data = array_merge(array('' => ''), $this->data);
      }

      if ($this->hasModel()) {
        echo CHtml::activeDropDownList($this->model, $this->attribute, $this->data, $this->htmlOptions);
      }
      else {
        $this->htmlOptions['id'] = $this->id;
        echo CHtml::dropDownList($this->name, $this->value, $this->data, $this->htmlOptions);
      }
    }
    elseif ($this->data !== null) {
      $data = array();
      foreach ($this->data as $dataID => $dataValue) {
        $data[] = array(
          'id' => $dataID,
          'text' => $dataValue,
        );
      }
      $this->options['data'] = $data;
    }

    $language = Yii::app()->getLanguage();
    $assetsDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
    $assetsUrl = Yii::app()->assetManager->publish($assetsDir);
    $cs = Yii::app()->getClientScript();
    $cs->registerCssFile($assetsUrl . '/css/select2.css');
    if ($this->scriptPosition === null) {
      $this->scriptPosition = $cs->coreScriptPosition;
    }
    $cs->registerScriptFile($assetsUrl . '/js/select2' . (YII_DEBUG ? '.min' : '') . '.js', $this->scriptPosition ? $this->scriptPosition : $cs->scriptPosition);
    if (file_exists($assetsDir . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'locale' . DIRECTORY_SEPARATOR . "select2_locale_{$language}.js")) {
      $cs->registerScriptFile($assetsUrl . "/js/locale/select2_locale_{$language}.js", $this->scriptPosition ? $this->scriptPosition : $cs->scriptPosition);
    }

    $options = $this->options ? CJavaScript::encode($this->options) : '';

    $eventHandlers = '';
    if (!empty($this->events)) {
      $eventHandlers = array();
      foreach ($this->events as $event => $handler) {
        $eventHandlers[] = "on(" . CJavaScript::encode($event) . "," . CJavaScript::encode($handler) . ")";
      }
      $eventHandlers = "jQuery('{$this->selector}')." . implode('.', $eventHandlers) . ';';
    }

    $cs->registerScript(__CLASS__ . '#' . $this->id, "jQuery('{$this->selector}').select2({$options});{$eventHandlers}");
  }
}
