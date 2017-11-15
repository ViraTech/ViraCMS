<?php
/**
 * ViraCMS Error Renderer Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ErrorController extends VSystemController
{
  public $layout = 'error';

  public function accessRules()
  {
    return CMap::mergeArray(
        array(
        array(
          'allow',
          'actions' => array(
            'error',
          ),
          'users' => array('*'),
        ),
        ), parent::accessRules()
    );
  }

  public function actionError()
  {
    $error = Yii::app()->errorHandler->getError();

    if (Yii::app()->request->isAjaxRequest) {
      echo $error['code'] . ' ' . $error['message'];
      Yii::app()->end();
    }

    $error['header'] = Yii::t('common', 'An error occurred while processing');
    $error['title'] = Yii::t('common', 'Error {errorCode}', array('{errorCode}' => $error['code']));

    $this->render('error', $error);
  }
}
