<?php
/**
 * ViraCMS Audio Player Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VAudioPlayer extends VApplicationComponent
{
  const PARAM_AUDIO_URL = 'v';
  const PARAM_AUTOPLAY = 'a';
  const DEFAULT_AUTOPLAY = false;

  /**
   * @var string the audio player widget class name
   */
  public $audioWidgetClass;

  /**
   * @var array additional widget params
   */
  public $audioWidgetParams = array();

  /**
   * Returns external route for rendering video iframe
   * @return string
   */
  public function getRoute()
  {
    return '/media/audio';
  }

  /**
   * Returns the audio player iframe URL
   * @param mixed $audio the audio file object or URL of the audio file
   * @param boolean $autoplay the video autoplay flag
   * @return string
   */
  public function getUrl($audio, $autoplay = self::DEFAULT_AUTOPLAY)
  {
    $url = is_a($audio, 'VContentActiveRecord') ? $audio->getUrl() : $audio;

    $params = array(
      self::PARAM_AUDIO_URL => $url,
    );

    if ($autoplay) {
      $params[self::PARAM_AUTOPLAY] = '1';
    }

    return Yii::app()->createUrl($this->getRoute(), $params);
  }

  /**
   * Returns iframe code for audio playback
   * @param mixed $audio the media object or URL of the audio file
   * @param boolean $autoplay the autoplay flag
   * @return string
   */
  public function getCode($audio, $autoplay = self::DEFAULT_AUTOPLAY)
  {
    $params = array(
      'src' => $this->getUrl($audio, $autoplay),
      'style' => 'border: 0; padding: 0; margin: 0;',
    );

    if ($autoplay) {
      $params['autoplay'] = '';
    }

    return CHtml::tag('iframe', $params, '');
  }

  /**
   * Output audio player widget code
   */
  public function renderPlayer()
  {
    $r = Yii::app()->getRequest();

    return $this->getPlayerCode(
        $r->getParam(self::PARAM_AUDIO_URL), (bool) $r->getParam(self::DEFAULT_AUTOPLAY, self::DEFAULT_AUTOPLAY)
    );
  }

  /**
   * Create player widget and return the code
   * @param string $url the audio file URL
   * @param boolean $autoplay the autoplay flag
   * @return type
   */
  public function getPlayerCode($url, $autoplay = self::DEFAULT_AUTOPLAY)
  {
    $widget = $this->createWidget();

    ob_start();
    ob_implicit_flush(false);

    if ($widget instanceof VAudioPlayerWidgetInterface) {
      $widget->setAudioUrl($url);
      $widget->setAudioAutoplay($autoplay);
      $widget->run();
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

    if ($this->audioWidgetClass) {
      $config = $this->audioWidgetParams;
      $config['class'] = $this->audioWidgetClass;

      $widget = Yii::createComponent($config);
    }

    if (is_object($widget) && method_exists($widget, 'init')) {
      $widget->init();
    }

    return $widget;
  }
}
