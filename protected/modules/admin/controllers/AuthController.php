<?php
/**
 * ViraCMS Administrator Authentication Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class AuthController extends VSystemController
{
  public $layout = 'auth';

  public function accessRules()
  {
    return CMap::mergeArray(
        array(
        array(
          'allow',
          'actions' => array(
            'index',
            'login',
            'restore',
          ),
          'users' => array('*'),
        ),
        array(
          'allow',
          'actions' => array(
            'logout',
          ),
          'users' => array('@'),
        ),
        ), parent::accessRules()
    );
  }

  /**
   * Index action stub
   */
  public function actionIndex()
  {
    if (Yii::app()->user->isGuest) {
      $this->redirect(array('login'));
    }

    $this->redirect(array('default/index'));
  }

  /**
   * Administrator login
   */
  public function actionLogin()
  {
    $lang = Yii::app()->request->getParam('lang', null);
    if ($lang) {
      Yii::app()->setLanguage($lang);
    }

    $counter = Yii::app()->user->getState('Vira.AdminLogin.Counter', 0);

    $model = new AdminCredentials;
    $model->unsetAttributes();
    $model->enableCaptcha = &$counter;

    if (Yii::app()->request->isPostRequest) {
      $model->attributes = Yii::app()->request->getParam(get_class($model), array());
      if ($model->validate() && $model->login()) {
        $this->redirect(
          Yii::app()->user->returnUrl &&
          Yii::app()->user->returnUrl !== '/' &&
          stripos(Yii::app()->user->returnUrl, 'login') === false &&
          stripos(Yii::app()->user->returnUrl, 'logout') === false ?
            Yii::app()->user->returnUrl :
            array('default/index')
        );
      }
      Yii::app()->user->setState('Vira.AdminLogin.Counter',  ++$counter);
      $model->password = '';
      $model->captcha = '';
    }

    $this->render('login', array(
      'legend' => Yii::t('admin.registry.titles', 'Administrators Entrance'),
      'model' => $model,
    ));
  }

  /**
   * Administrator logout
   */
  public function actionLogout()
  {
    Yii::app()->user->logout();
    $this->redirect(array('/site/index'));
  }

  /**
   * Restore password action
   */
  public function actionRestore()
  {
    $r = Yii::app()->request;

    if (($key = $r->getParam('_', null)) !== null) {
      $restore = VPasswordRestore::model()->findByPk($key);

      if ($restore === null) {
        throw new CHttpException(403, Yii::t('common', 'access denied'));
      }

      VLanguageHelper::setLanguage($restore->languageID);

      if ($restore->expire < time()) {
        throw new CHttpException(403, Yii::t('common', 'link is expired'));
      }

      $success = false;
      $account = VSiteAdmin::model()->findByAttributes(array('email' => $restore->email));

      if ($account !== null && $account->setPassword(Yii::app()->passwordGenerator->generate())) {
        $restore->delete();
        $success = true;
      }

      $this->renderRestoreView('result', array(
        'success' => $success,
      ));
    }
    else {
      $restore = new VPasswordRestore;

      if (($attributes = $r->getParam(get_class($restore), null)) !== null) {
        $restore->attributes = $attributes;
        $restore->area = VIdentity::AREA_ADMIN;
        $restore->languageID = Yii::app()->getLanguage();

        if ($restore->save()) {
          $this->renderRestoreView('request');
          Yii::app()->end();
        }
      }

      $restore->captcha = '';

      $this->renderRestoreView('restore', array(
        'model' => $restore,
      ));
    }
  }

  protected function renderRestoreView($action, $params = array())
  {
    switch ($action) {
      case 'request':
        $viewName = 'success';
        $params['header'] = Yii::t('admin.registry.messages', 'Operation successfully completed');
        $params['message'] = Yii::t('admin.registry.messages', 'Further instructions sent directly to your e-mail address');
        break;

      case 'result':
        $success = isset($params['success']) ? $params['success'] : false;
        $viewName = $success ? 'success' : 'error';
        $params['header'] = $success ?
          Yii::t('admin.registry.titles', 'Your password successfully updated') :
          Yii::t('admin.registry.errors', 'An error occurred while processing request');
        $params['message'] = $success ?
          Yii::t('admin.registry.messages', 'New password has been sent to your e-mail address') :
          Yii::t('admin.registry.errors', 'Please try again or contact site administrator');
        break;

      default:
        $viewName = 'restore';
    }

    $params['legend'] = Yii::t('admin.registry.titles', 'Password Restore');

    $this->render($viewName, $params);
  }
}
