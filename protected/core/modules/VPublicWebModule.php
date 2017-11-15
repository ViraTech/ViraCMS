<?php
/**
 * ViraCMS Default Frontend Web Module Component
 * Based On Yii Framework CWebModule Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPublicWebModule extends VWebModule
{
  public function init()
  {
    parent::init();

    // set error handler route
    Yii::app()->errorHandler->errorAction = '/error/error';

    // set user login route
    Yii::app()->user->setLoginUrl(array('/admin/auth/login'));
  }
}
