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
class CaptchaController extends VSystemController
{
  public function accessRules()
  {
    return CMap::mergeArray(
        array(
        array(
          'allow',
          'actions' => array(
            'index',
            'captcha',
          ),
          'users' => array('*'),
        ),
        ), parent::accessRules()
    );
  }

  public function actions()
  {
    return array(
      'captcha' => Yii::app()->theme->getCaptchaOptions(),
    );
  }

  public function actionIndex()
  {
    $this->forward('captcha');
  }
}
