<?php
/**
 * ViraCMS Site User's Error Reporting And Maintenance Stub Controller
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ErrorController extends VPublicController
{
  public $layout = 'error';

  /**
   * Error message renderer
   */
  public function actionError()
  {
    $error = Yii::app()->errorHandler->getError();

    if (Yii::app()->request->isAjaxRequest) {
      echo $error['code'] . ' ' . $error['message'];
      Yii::app()->end();
    }

    $error['header'] = Yii::t('common', 'An error occurred while processing');
    $error['title'] = Yii::t('common', 'Error {errorCode}', array(
      '{errorCode}' => $error['code'],
    ));

    $title = Yii::t('common', 'Error');
    $this->setTitle($title);
    $this->setPageTitle($title);
    $this->setBreadcrumbs(array($title));
    $this->renderErrorView($error);
  }

  /**
   * Renders error view
   * @param array $error the error data
   */
  protected function renderErrorView($error)
  {
    $innerView = VSystemPage::model()->find($this->getInnerViewCriteria($error['code']));

    if ($innerView != null) {
      $innerView->setReplacement(array(
        '###ERROR_HEADER###' => $error['header'],
        '###ERROR_TITLE###' => $error['title'],
        '###ERROR_CODE###' => $error['code'],
        '###ERROR_MESSAGE###' => $error['message'],
      ));
      $this->setSubject($innerView);
    }

    $this->render('error', $error);
  }

  /**
   * Returns erorr view database criteria
   * @param string $code the error code
   * @return \CDbCriteria
   */
  protected function getInnerViewCriteria($code)
  {
    $criteria = new CDbCriteria();

    $criteria->compare('t.siteID', Yii::app()->site->id);
    $criteria->compare('t.module', 'system');
    $criteria->compare('t.controller', 'error');
    $criteria->compare('t.view', 'error' . (int) $code);
    $criteria->with = array('l10n');

    return $criteria;
  }

  /**
   * Maintenance stub
   */
  public function actionMaintenance()
  {
    $this->layout = null;
    $this->renderPartial('maintenance');
  }
}
