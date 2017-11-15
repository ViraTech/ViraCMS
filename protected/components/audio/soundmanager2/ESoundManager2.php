<?php
/**
 * Wrapper for Sound Manager 2 player (http://www.schillmania.com/projects/soundmanager2/)
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ESoundManager2 extends CWidget implements VAudioPlayerWidgetInterface
{
  /**
   * @var string URL of published assets
   */
  private $assetsUrl;

  /**
   * @var string the skin name
   */
  public $skin = '360player';

  /**
   * @var array skin options override
   */
  public $skinOptions = array();

  /**
   * @var string audio file URL
   */
  public $url;

  /**
   * @var string audio file title
   */
  public $title;

  /**
   * @var boolean auto play flag
   */
  public $autoplay = false;

  public function init()
  {
    $this->assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
  }

  public function getWidgetId()
  {
    return 'sndmgr2-' . substr(md5($this->url), 0, 6);
  }

  public function getSwfUrl()
  {
    return $this->assetsUrl . '/swf';
  }

  public function getOptions()
  {
    switch ($this->skin) {
      case '360player':
        return array(
          'url'          => $this->getSwfUrl(),
          'flashVersion' => 9,
          'preferFlash'  => false,
        );

      default:
        $playingJs = "js:function(){ var a = $('#{$this->getWidgetId()} .play'); a.addClass('playing').find('i').attr('class',a.data('icon-playing')); }";
        $standByJs = "js:function(){ var a = $('#{$this->getWidgetId()} .play'); a.removeClass('playing').find('i').attr('class',a.data('icon-standby')); }";
        $durationJs = "js:function(e){console.log(e);}";

        $options = array(
          'id'             => $this->getWidgetId(),
          'url'            => $this->url,
          'volume'         => 50,
          'autoPlay'       => $this->autoplay,
          'onplay'         => $playingJs,
          'onstop'         => $standByJs,
          'onpause'        => $standByJs,
          'onresume'       => $playingJs,
          'onfinish'       => $standByJs,
          'durationchange' => $durationJs,
        );

        return array(
          'url'          => $this->getSwfUrl(),
          'flashVersion' => 9,
          'preferFlash'  => false,
          'onready'      => "js:function(){soundManager.createSound(" . CJavaScript::encode($options) . ");}",
        );
    }
  }

  public function setAudioUrl($url)
  {
    $this->url = $url;
  }

  public function setAudioAutoplay($autoplay)
  {
    $this->autoplay = $autoplay;
  }

  public function registerCss()
  {
    $cs = Yii::app()->getClientScript();
    switch ($this->skin) {
      case '360player':
        $cs->registerCssFile($this->assetsUrl . '/css/skins/' . $this->skin . '/style.css');
        break;

      default:
        $cs->registerCssFile($this->assetsUrl . '/css/style.css');
    }
  }

  public function registerJs()
  {
    $cs = Yii::app()->getClientScript();
    $cs->registerScriptFile($this->assetsUrl . '/js/soundmanager2' . (YII_DEBUG ? '' : '.min') . '.js');
    $cs->registerScript($this->getWidgetId() . '#setup', "soundManager.setup(" . CJavaScript::encode($this->getOptions()) . ");");

    switch ($this->skin) {
      case '360player':
        $cs->registerScriptFile($this->assetsUrl . '/js/skins/360player/berniecode-animator' . (YII_DEBUG ? '' : '.min') . '.js');
        //$cs->registerScriptFile($this->assetsUrl . '/js/skins/360player/excanvas' . (YII_DEBUG ? '' : '.min') . '.js');
        $cs->registerScriptFile($this->assetsUrl . '/js/skins/360player/360player' . (YII_DEBUG ? '' : '.min') . '.js');
        /*
          $options = array(
          'playNext' => false,
          'autoPlay' => $this->autoplay,
          'allowMultiple' => false,
          'loadRingColor' => '#ccc',
          'playRingColor' => '#000',
          'backgroundRingColor' => '#eee',
          'animDuration' => 500,
          'animTransition' => 'js:Animator.tx.bouncy',
          );

          $options = CMap::mergeArray($options,$this->skinOptions);

          $cs->registerScript($this->getWidgetId() . '#skinSetup',"threeSixtyPlayer.config=" . CJavaScript::encode($options) .";");
         */
        break;

      default:
        $cs->registerScript($this->getWidgetId() . '#control', $this->getControlScript());
    }
  }

  public function getControlScript()
  {
    return "$('#{$this->getWidgetId()} .play').on('click',function(e){" .
      "e.preventDefault();" .
      "if($(this).hasClass('playing')){" .
      "soundManager.pause('{$this->getWidgetId()}');" .
      "}" .
      "else{" .
      "soundManager.play('{$this->getWidgetId()}');" .
      "}" .
      "});";
  }

  public function run()
  {
    $this->render($this->skin ? 'skins/' . $this->skin . '/player' : 'player');
    $this->registerJs();
    $this->registerCss();
  }
}
