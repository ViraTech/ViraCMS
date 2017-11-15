<?php
/**
 * ViraCMS Video Player Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VVideoPlayer extends VApplicationComponent
{
  const PARAM_VIDEO_URL = 'v';
  const PARAM_IMAGE_URL = 'i';
  const PARAM_WIDTH = 'w';
  const PARAM_HEIGHT = 'h';
  const PARAM_AUTOPLAY = 'a';
  const DEFAULT_WIDTH = 640;
  const DEFAULT_HEIGHT = 385;
  const DEFAULT_AUTOPLAY = false;

  /**
   * @var string the video player widget class name
   */
  public $videoWidgetClass;

  /**
   * @var array additional widget params
   */
  public $videoWidgetParams = array();

  /**
   * Returns external route for rendering video iframe
   * @return string
   */
  public function getRoute()
  {
    return '/media/video';
  }

  /**
   * Returns the video player iframe URL
   * @param mixed $video the media object or URL of the video file
   * @param mixed $image the image object or URL of the image file
   * @param integer $width the video player width
   * @param integer $height the video player height
   * @param boolean $autoplay the video autoplay flag
   * @return string
   */
  public function getUrl($video, $image = null, $width = self::DEFAULT_WIDTH, $height = self::DEFAULT_HEIGHT, $autoplay = self::DEFAULT_AUTOPLAY)
  {
    $videoUrl = is_a($video, 'VContentActiveRecord') ? $video->getUrl() : $video;
    $imageUrl = is_a($image, 'VContentActiveRecord') ? $image->getUrl($width, $height, true) : $image;

    $params = array(
      self::PARAM_VIDEO_URL => $videoUrl,
      self::PARAM_IMAGE_URL => $imageUrl,
      self::PARAM_WIDTH => $width,
      self::PARAM_HEIGHT => $height,
    );

    if ($autoplay) {
      $params[self::PARAM_AUTOPLAY] = '1';
    }

    return Yii::app()->createUrl($this->getRoute(), $params);
  }

  /**
   * Returns iframe code for video playback
   * @param mixed $video the media object or URL of the video file
   * @param mixed $image the image object or URL of the image file
   * @param integer $width the video player width
   * @param integer $height the video player height
   * @param boolean $autoplay the video autoplay flag
   * @return string
   */
  public function getCode($video, $image = null, $width = self::DEFAULT_WIDTH, $height = self::DEFAULT_HEIGHT, $autoplay = self::DEFAULT_AUTOPLAY)
  {
    $params = array(
      'src' => $this->getUrl($video, $image, $width, $height, $autoplay),
      'width' => $width,
      'height' => $height,
      'style' => 'border: 0; padding: 0; margin: 0;',
    );

    if ($autoplay) {
      $params['autoplay'] = '';
    }

    return CHtml::tag('iframe', $params, '');
  }

  /**
   * Output video player widget code
   */
  public function renderPlayer()
  {
    $r = Yii::app()->getRequest();

    return $this->getPlayerCode(
        $r->getParam(self::PARAM_VIDEO_URL), $r->getParam(self::PARAM_IMAGE_URL), (int) $r->getParam(self::PARAM_WIDTH, self::DEFAULT_WIDTH), (int) $r->getParam(self::PARAM_HEIGHT, self::DEFAULT_HEIGHT), (bool) $r->getParam(self::DEFAULT_AUTOPLAY, self::DEFAULT_AUTOPLAY)
    );
  }

  /**
   * Create player widget and return the code
   * @param string $videoUrl the video URL
   * @param string $imageUrl the image URL
   * @param integer $width the video player width
   * @param integer $height the video player height
   * @param boolean $autoplay the video autoplay flag
   * @return type
   */
  public function getPlayerCode($videoUrl, $imageUrl = null, $width = self::DEFAULT_WIDTH, $height = self::DEFAULT_HEIGHT, $autoplay = self::DEFAULT_AUTOPLAY)
  {
    $widget = $this->createWidget();

    ob_start();
    ob_implicit_flush(false);

    if ($widget instanceof VVideoPlayerWidgetInterface) {
      $widget->setVideoUrl($videoUrl);
      $widget->setImageUrl($imageUrl);
      $widget->setVideoWidth($width);
      $widget->setVideoHeight($height);
      $widget->setVideoAutoplay($autoplay);
      $widget->run();
    }
    else {
      echo CHtml::image($imageUrl ? $imageUrl : Yii::app()->theme->getPlaceholderUrl($width, $height), '');
    }

    return ob_get_clean();
  }

  /**
   * Creates the widget
   * @return mixed
   */
  protected function createWidget()
  {
    $widget = null;

    if ($this->videoWidgetClass) {
      $config = $this->videoWidgetParams;
      $config['class'] = $this->videoWidgetClass;

      $widget = Yii::createComponent($config);
    }

    if (is_object($widget) && method_exists($widget, 'init')) {
      $widget->init();
    }

    return $widget;
  }
}
