<?php
/**
 * ViraCMS Media iFrame Renderer
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class MediaController extends VController
{
  public $layout = 'media';

  /**
   * Render audio player code
   */
  public function actionAudio()
  {
    $this->render('player', array(
      'player' => Yii::app()->audioPlayer->renderPlayer(),
    ));
  }

  /**
   * Render video player code
   */
  public function actionVideo()
  {
    $this->render('player', array(
      'player' => Yii::app()->videoPlayer->renderPlayer(),
    ));
  }
}
