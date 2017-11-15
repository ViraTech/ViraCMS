<?php
/**
 * ColorBox extension (http://www.jacklmoore.com/colorbox/)
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class EColorBox extends CWidget implements VPhotoViewerWidgetInterface
{

  /**
   * @var mixed the calling object
   */
  public $owner;

  /**
   * @var VContentImage[] the gallery's images list
   */
  public $images = array();

  /**
   * @var array colorbox language, defaults to english
   */
  public $language;

  /**
   * @var array colorbox settings
   */
  public $settings = array();

  /**
   * @var array colorbox custom phrases
   */
  public $locale = array();

  /**
   * @var array colorbox content type settings
   */
  public $contentType = array();

  /**
   * @var array colorbox dimensions parameters
   */
  public $dimensions = array();

  /**
   * @var array colorbox slideshow parameters
   */
  public $slideshow = array();

  /**
   * @var array colorbox positioning parameters
   */
  public $positioning = array();

  /**
   * @var string callback fires before colorbox opens
   */
  public $onOpen;

  /**
   * @var string callback fires before attempting to load content
   */
  public $onLoad;

  /**
   * @var string callback fires right after content is displayed
   */
  public $onComplete;

  /**
   * @var string callback fires before close process activated
   */
  public $onCleanup;

  /**
   * @var string callback fires after colorbox is closed
   */
  public $onClosed;

  /**
   * @var boolean register assets CSS
   */
  public $registerCss = true;

  /**
   * @var array colorbox default settings
   */
  private $_defaultSettings = array(
    'transition'   => 'elastic',
    'speed'        => 350,
    'href'         => false,
    'title'        => false,
    'rel'          => false,
    'scalePhotos'  => true,
    'scrolling'    => true,
    'opacity'      => 0.85,
    'open'         => false,
    'returnFocus'  => true,
    'fastIframe'   => true,
    'preloading'   => true,
    'overlayClose' => true,
    'escKey'       => true,
    'arrowKey'     => true,
    'loop'         => true,
    'data'         => false,
    'className'    => false,
  );

  /**
   * @var array colorbox default phrases
   */
  private $_defaultLocale = array(
    'current'        => 'image {current} of {total}',
    'previous'       => 'previous',
    'next'           => 'next',
    'close'          => 'close',
    'xhrError'       => 'This content failed to load.',
    'imgError'       => 'This image failed to load.',
    'slideshowStart' => 'start slideshow',
    'slideshowStop'  => 'stop slideshow',
  );
  private $_defaultContentType = array(
    'iframe' => false,
    'inline' => false,
    'html'   => false,
    'photo'  => false,
  );

  /**
   * @var array colorbox default dimensions
   */
  private $_defaultDimensions = array(
    'width'         => false,
    'height'        => false,
    'innerWidth'    => false,
    'innerHeight'   => false,
    'initialWidth'  => 300,
    'initialHeight' => 100,
    'maxWidth'      => false,
    'maxHeight'     => false,
  );

  /**
   * @var array colorbox default slide show params
   */
  private $_defaultSlideShow = array(
    'slideshow'      => false,
    'slideshowSpeed' => 2500,
    'slideshowAuto'  => 'auto',
    'slideshowStart' => true,
  );

  /**
   * @var array colorbox default position settings
   */
  private $_defaultPositioning = array(
    'fixed'  => false,
    'top'    => false,
    'bottom' => false,
    'left'   => false,
    'right'  => false,
  );

  /**
   * @var string URL of published assets
   */
  private $assetsUrl;

  /**
   * @var string images list view file name or alias
   */
  public $listView = 'images';

  /**
   * @var string single image view file name or alias
   */
  public $imageView = 'image';

  /**
   * @var string the image tag widget js to be applied to
   */
  public $imageTag = 'a';

  /**
   * @var string thumbnail item css class
   */
  public $thumbnailCssClass = 'span4';

  public function init()
  {
    $this->assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
    $cs = Yii::app()->getClientScript();
    $cs->registerCoreScript('jquery');
    $cs->registerScriptFile($this->assetsUrl . '/js/jquery.colorbox' . (YII_DEBUG ? '' : '.min') . '.js');

    if (empty($this->language)) {
      $this->language = Yii::app()->language;
    }

    if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR .
        'assets' . DIRECTORY_SEPARATOR .
        'js' . DIRECTORY_SEPARATOR .
        'i18n' . DIRECTORY_SEPARATOR .
        'jquery.colorbox-' . $this->language . '.js')) {
      $cs->registerScriptFile($this->assetsUrl . '/js/i18n/jquery.colorbox-' . $this->language . '.js');
    }

    if ($this->registerCss) {
      $cs->registerCssFile($this->assetsUrl . '/css/colorbox.css');
    }
  }

  public function setOwner($owner)
  {
    $this->owner = $owner;
  }

  public function setImages($images)
  {
    $this->images = $images;
  }

  public function run()
  {
    if (count($this->images) == 1) {
      $image = current($this->images);
      $output = $this->render($this->imageView, array(
        'image' => $image,
        'owner' => $this->owner,
        ), true);
    }
    elseif (count($this->images)) {
      $this->settings[ 'rel' ] = 'cbox-grp-' . $this->id;
      $output = $this->render($this->listView, array(
        'images' => $this->images,
        'owner'  => $this->owner,
        ), true);
    }
    else {
      $output = '';
    }

    foreach ($this->images as $image) {
      if (isset($image[ 'image' ])) {
        $this->settings[ 'href' ] = "js:function(){ var el = $(this),url = el.data('full-image'); return url ? url : el.attr('href'); }";
      }
    }

    if (empty($this->dimensions[ 'maxWidth' ])) {
      $this->dimensions[ 'maxWidth' ] = '100%';
    }

    if (empty($this->dimensions[ 'maxHeight' ])) {
      $this->dimensions[ 'maxHeight' ] = '100%';
    }

    $this->registerScripts();

    echo CHtml::tag('div', array('id' => 'cb-' . $this->id), $output);
  }

  protected function registerScripts()
  {
    $options = array();

    foreach (array('onOpen', 'onLoad', 'onComplete', 'onCleanup', 'onClosed') as $callback) {
      if ($this->$callback) {
        $options[ $callback ] = $this->$callback;
      }
    }

    foreach ($this->settings as $key => $value) {
      if ($this->_defaultSettings[ $key ] != $value) {
        $options[ $key ] = $value;
      }
    }

    foreach ($this->locale as $key => $value) {
      if ($this->_defaultLocale[ $key ] != $value) {
        $options[ $key ] = $value;
      }
    }

    foreach ($this->contentType as $key => $value) {
      if ($this->_defaultContentType[ $key ] != $value) {
        $options[ $key ] = $value;
      }
    }

    foreach ($this->dimensions as $key => $value) {
      if ($this->_defaultDimensions[ $key ] != $value) {
        $options[ $key ] = $value;
      }
    }

    foreach ($this->slideshow as $key => $value) {
      if ($this->_defaultSlideShow[ $key ] != $value) {
        $options[ $key ] = $value;
      }
    }

    foreach ($this->positioning as $key => $value) {
      if ($this->_defaultPositioning[ $key ] != $value) {
        $options[ $key ] = $value;
      }
    }

    $options = empty($options) ? '' : CJavaScript::encode($options);

    Yii::app()->getClientScript()->registerScript(get_class($this) . '#' . $this->id, "jQuery('#cb-{$this->id} {$this->imageTag}').colorbox({$options});");
  }
}
