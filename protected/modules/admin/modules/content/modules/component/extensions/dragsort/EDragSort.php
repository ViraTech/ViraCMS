<?php
/**
 * Wrapper for jQuery List DragSort (http://dragsort.codeplex.com/)
 *
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://opensource.org/licenses/MIT
 */
class EDragSort extends CWidget
{
  /**
   * @var string top block selector
   */
  public $selector = 'ul.dragsort';

  /**
   * Dragsort options
   */
  public $itemSelector;
  public $dragSelector;
  public $dragSelectorExclude;
  public $dragEnd;
  public $dragBetween;
  public $placeHolderTemplate;
  public $scrollContainer;
  public $scrollSpeed;

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
   * Init widget
   */
  public function init()
  {
    if ($this->onlyRun) {
      return;
    }

    $this->assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
    Yii::app()->clientScript->registerScriptFile($this->assetsUrl . '/js/jquery.dragsort' . (YII_DEBUG ? '' : '.min') . '.js', CClientScript::POS_END);
    Yii::app()->clientScript->registerCssFile($this->assetsUrl . '/css/style.css', 'screen,projection');
  }

  /**
   * Render and run widget
   */
  public function run()
  {
    if ($this->onlyInit) {
      return;
    }

    $options = array();

    if ($this->itemSelector) {
      $options['itemSelector'] = $this->itemSelector;
    }

    if ($this->dragSelector) {
      $options['dragSelector'] = $this->dragSelector;
    }

    if ($this->dragSelectorExclude) {
      $options['dragSelectorExclude'] = $this->dragSelectorExclude;
    }

    if ($this->dragEnd) {
      $options['dragEnd'] = strpos($this->dragEnd, 'js:') !== 0 ? 'js:' : '' . $this->dragEnd;
    }

    if ($this->dragBetween) {
      $options['dragBetween'] = !!$this->dragBetween;
    }

    if ($this->placeHolderTemplate) {
      $options['placeHolderTemplate'] = $this->placeHolderTemplate;
    }

    if ($this->scrollContainer) {
      $options['scrollContainer'] = $this->scrollContainer;
    }

    if ($this->scrollSpeed) {
      $options['scrollSpeed'] = $this->scrollSpeed;
    }

    Yii::app()->clientScript->registerScript(get_class($this) . '.' . $this->id, "
$('{$this->selector}').dragsort(" . (empty($options) ? '' : CJavaScript::encode($options)) . ");
");
  }
}
