<?php
/**
 * Wrapper for CKEditor (http://ckeditor.com/)
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ECKEditor extends CInputWidget
{
  /**
   * @var string CKEditor mode - standard or inline
   */
  public $mode = 'standard';

  /**
   * @var string jQuery blocks selector for inline mode
   */
  public $selector;

  /**
   * @var boolean Init widget, but do not run
   */
  public $onlyInit = false;

  /**
   * @var boolean Run widget only, do not init
   */
  public $onlyRun = false;

  /**
   * @var string URL to published assets
   */
  private $assetsUrl;

  /**
   * @var boolean enable browsing files on the server
   */
  public $enableServerBrowsing = false;

  /**
   * @var string CKEditor toolbar type
   */
  public $toolbar = 'Full';

  /**
   * @var string body CSS class
   */
  public $bodyClass;

  /**
   * @var string CSS file(s)
   */
  public $contentCss;

  /**
   * @var integer element height in pixels
   */
  public $height = 500;

  /**
   * @var array params for url generation for file's server browsing
   */
  public $serverBrowsingParams = array();

  /**
   * Init widget
   */
  public function init()
  {
    if ($this->onlyRun) {
      return;
    }

    $this->assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
    Yii::app()->clientScript->registerScriptFile($this->assetsUrl . '/ckeditor/ckeditor.js', CClientScript::POS_END);

    $configureScript = '';
    if ($this->mode == 'inline') {
      $configureScript .= 'CKEDITOR.disableAutoInline = true;';
    }
    if ($this->enableServerBrowsing) {
      if (!empty(Yii::app()->editor->fileBrowserAction)) {
        $configureScript .= "CKEDITOR.config.filebrowserBrowseUrl = '" . Yii::app()->createUrl(Yii::app()->editor->fileBrowserAction, $this->serverBrowsingParams) . "';";
      }
      if (!empty(Yii::app()->editor->imageBrowserAction)) {
        $configureScript .= "CKEDITOR.config.filebrowserImageBrowseUrl = '" . Yii::app()->createUrl(Yii::app()->editor->imageBrowserAction, $this->serverBrowsingParams) . "';";
      }
      if (!empty(Yii::app()->editor->flashBrowserAction)) {
        $configureScript .= "CKEDITOR.config.filebrowserFlashBrowseUrl = '" . Yii::app()->createUrl(Yii::app()->editor->flashBrowserAction, $this->serverBrowsingParams) . "';";
      }
      if (!empty(Yii::app()->editor->videoBrowserAction)) {
        $configureScript .= "CKEDITOR.config.filebrowserVideoBrowseUrl = '" . Yii::app()->createUrl(Yii::app()->editor->videoBrowserAction, $this->serverBrowsingParams) . "';";
      }
    }

    Yii::app()->clientScript->registerScript(get_class($this) . '.ConfigureScript', $configureScript);
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
      echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
    }
    else {
      echo CHtml::textArea($this->name, $this->value, $this->htmlOptions);
    }

    $config = array(
      'toolbar' => $this->toolbar,
      'height' => $this->height . 'px',
    );

    if ($this->contentCss) {
      $config['contentsCss'] = $this->contentCss;
    }

    if ($this->bodyClass) {
      $config['bodyClass'] = $this->bodyClass;
    }

    $script = "CKEDITOR." . ($this->mode == 'inline' ? 'inline' : 'replace') . "('{$this->id}'," . CJavaScript::encode($config) . ");";

    Yii::app()->clientScript->registerScript(get_class($this) . '.' . $this->id, $script);
  }
}
