<?php
/**
 * ViraCMS System (backend) Controller
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSystemController extends VController
{
  /**
   * Access filters
   * @return array
   */
  public function filters()
  {
    return array(
      'accessControl',
    );
  }

  /**
   * Default access rules
   * @return array
   */
  public function accessRules()
  {
    return array(
      array(
        'deny',
        'users' => array('*'),
      ),
    );
  }

  /**
   * Controller init
   */
  public function init()
  {
    parent::init();
    $admin = Yii::app()->user->model;
    if ($admin && $admin->hasAttribute('languageID') && $admin->languageID) {
      Yii::app()->setLanguage($admin->languageID);
    }
    else {
      Yii::app()->setLanguage(Yii::app()->backendLanguage);
    }
  }
}
