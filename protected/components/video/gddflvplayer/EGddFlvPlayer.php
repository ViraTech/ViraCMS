<?php
/**
 * Wrapper for GDD FLV player (http://www.gdd.ro/free-flash-flv-player)
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class EGddFlvPlayer extends CWidget implements VVideoPlayerWidgetInterface
{
  /**
   * @var string URL of published assets
   */
  private $assetsUrl;

  /**
   * @var string Flash plugins page
   */
  public $pluginspage = 'http://www.macromedia.com/go/getflashplayer';

  /**
   * @var integer Player width
   */
  public $width = 468;

  /**
   * @var integer Player height
   */
  public $height = 328;

  /**
   * @var boolean Allow player to full screen playback
   */
  public $allowFullScreen = true;

  /**
   * @var string Flash quality. Possible values: best, high, medium, low
   */
  public $quality = 'best';

  /**
   * @var string Window Mode. Possible values: window, direct, opaque, transparent, gpu
   */
  public $wMode = 'transparent';

  /**
   * @var string Allow flash access to javascript. Possible values: always, sameDomain, never
   */
  public $allowScriptAccess = 'always';

  /**
   * @var string MIME type of flash application
   */
  public $type = 'application/x-shockwave-flash';

  /**
   * @var boolean GDD FLV player option. Start playing automatically
   */
  public $autoplay = false;

  /**
   * @var string GDD FLV player option. Source of FLV or MP4 video file. Required
   */
  public $vdo;

  /**
   * @var integer GDD FLV player option. Initial volume in percents. Optional. Defaults is 75
   */
  public $sound = 75;

  /**
   * @var string GDD FLV player option. Intro or commercial block videofile. Optional
   */
  public $advert;

  /**
   * @var string GDD FLV player option. Intro or commercial block title. Optional
   */
  public $advertDesc;

  /**
   * @var string GDD FLV player option. URL of image representing logo. Optional
   */
  public $myLogo;

  /**
   * @var string GDD FLV player option. On-screen logo position. Optional. Possible values: TL, TR, BL, BR
   */
  public $logoPosition;

  /**
   * @var string GDD FLV player option. Statistics tracker URL. Optional
   */
  public $tracker;

  /**
   * @var integer GDD FLV player option. Buffer size in seconds. Optional. Defaults to 2 seconds
   */
  public $buffer;

  /**
   * @var string GDD FLV player option. Video splash screen URL. Optional
   */
  public $splashScreen;

  /**
   * @var string GDD FLV player option. If not empty creates a clickable button over video with this URL. Optional
   */
  public $clickTag;

  /**
   * @var string GDD FLV player option. At the end of current video automatically load this video (must be an URL). Optional
   */
  public $endClipAction;

  /**
   * @var boolean GDD FLV player option. Loop video file. Optional. Defaults to false
   */
  public $loop = true;

  public function init()
  {
    $this->assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
  }

  public function setVideoUrl($url)
  {
    $this->vdo = $url;
  }

  public function setImageUrl($url)
  {
    $this->splashScreen = $url;
  }

  public function setVideoWidth($width)
  {
    $this->width = $width;
  }

  public function setVideoHeight($height)
  {
    $this->height = $height;
  }

  public function setVideoAutoplay($autoplay)
  {
    $this->autoplay = $autoplay;
  }

  public function run()
  {
    $playerParams = array_filter(array(
      'autoplay'      => $this->autoplay ? 'true' : null,
      'vdo'           => urlencode($this->vdo),
      'sound'         => $this->sound,
      'advert'        => $this->advert,
      'advertdesc'    => $this->advertDesc,
      'mylogo'        => $this->myLogo,
      'logoposition'  => $this->logoPosition,
      'tracker'       => $this->tracker,
      'buffer'        => $this->buffer,
      'splashscreen'  => $this->splashScreen,
      'clickTAG'      => $this->clickTag,
      'endclipaction' => $this->endClipAction,
      'loop'          => $this->loop ? 'true' : null,
    ));

    $flashvars = array();

    foreach ($playerParams as $key => $value) {
      $flashvars[] = $key . '=' . CHtml::encode($value);
    }

    $params = array(
      'flashvars'         => '?&' . implode('&', $flashvars),
      'width'             => $this->width,
      'height'            => $this->height,
      'allowFullScreen'   => $this->allowFullScreen ? 'true' : 'false',
      'quality'           => $this->quality,
      'wmode'             => $this->wMode,
      'allowScriptAccess' => $this->allowScriptAccess,
      'pluginspage'       => $this->pluginspage,
      'type'              => $this->type,
      'src'               => $this->assetsUrl . '/gddflvplayer.swf',
      'video'             => $this->vdo,
    );

    echo CHtml::tag('embed', $params, '');
  }
}
