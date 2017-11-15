<?php
/**
 * Wrapper for Ace editor (http://ace.ajax.org/)
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class EAce extends CInputWidget
{
  /**
   * @var string Ace editor syntax highlighter mode (e.g. html or php)
   */
  public $mode = 'html';

  /**
   * @var string Ace editor theme name
   */
  public $theme = 'textmate';

  /**
   * @var integer Font size in pixels
   */
  public $fontSize = 14;

  /**
   * @var boolean Init widget, but do not run
   */
  public $onlyInit = false;

  /**
   * @var string Tag name for editor block
   */
  public $tag = 'div';

  /**
   * @var boolean Run widget only, do not init
   */
  public $onlyRun = false;

  /**
   * @var boolean set editor to read-only mode
   */
  public $readonly = false;

  /**
   * @var string URL to published assets
   */
  private $assetsUrl;

  /**
   * Init widget
   */
  public function init()
  {
    if ($this->onlyRun) {
      return;
    }

    $this->assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
    Yii::app()->clientScript->registerScriptFile($this->assetsUrl . '/ace.js', CClientScript::POS_HEAD);
  }

  /**
   * Render and run widget
   */
  public function run()
  {
    if ($this->onlyInit) {
      return;
    }

    list($this->name, $this->id) = $this->resolveNameId();

    $this->htmlOptions['id'] = $this->id;
    if ($this->hasModel()) {
      echo CHtml::activeHiddenField($this->model, $this->attribute, array('id' => 'origin_' . $this->id));
      $value = $this->model->getAttribute($this->attribute);
    }
    else {
      echo CHtml::hiddenField($this->name, $this->value, array('id' => 'origin_' . $this->id));
      $value = $this->value;
    }
    echo CHtml::tag($this->tag, $this->htmlOptions, CHtml::encode($value));

    $cs = Yii::app()->clientScript;
    $cs->registerScript(__CLASS__ . '_' . $this->id . '_Variables', "
var {$this->id}_ace;", CClientScript::POS_HEAD);
    $cs->registerScript(__CLASS__ . '_' . $this->id, "{$this->id}_ace = ace.edit('{$this->id}');
{$this->id}_ace.getSession().setMode('ace/mode/{$this->mode}');
{$this->id}_ace.setTheme('ace/theme/{$this->theme}');
{$this->id}_ace.setReadOnly({$this->readonly});
jQuery('#{$this->id}').css({ fontSize: {$this->fontSize} });
jQuery('#origin_{$this->id}').closest('form').submit(function(e) { $('#origin_{$this->id}').val({$this->id}_ace.getValue()); return true; });", CClientScript::POS_READY);
  }
}
