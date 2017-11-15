<?php
/**
 * ViraCMS CAPTCHA Controller
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class CaptchaController extends VPublicController
{
  public function actions()
  {
    return array(
      'captcha' => $this->getCaptchaOptions(),
    );
  }

  public function actionIndex()
  {
    $this->forward('captcha');
  }

  /**
   * Returns the captcha options regarding to current theme
   * @return array
   */
  protected function getCaptchaOptions()
  {
    $defaultOptions = array(
      'class' => 'VCaptchaAction',
    );

    $this->setTheme();

    $captchaOptions = Yii::app()->getTheme()->getCaptchaOptions();

    return is_array($captchaOptions) ? CMap::mergeArray($defaultOptions, $captchaOptions) : $defaultOptions;
  }
}
