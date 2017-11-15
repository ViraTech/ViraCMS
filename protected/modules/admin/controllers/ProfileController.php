<?php
/**
 * ViraCMS Administrator's Profile Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ProfileController extends VSystemController
{
  public $layout = 'inner';

  public function accessRules()
  {
    return CMap::mergeArray(
        array(
        array(
          'allow',
          'actions' => array(
            'index',
          ),
          'roles' => array_keys(Yii::app()->authManager->getAdminRoles()),
        ),
        ), parent::accessRules()
    );
  }

  public function actionIndex()
  {
    $r = Yii::app()->request;
    $model = $this->loadModel(Yii::app()->user->id);

    if ($r->isAjaxRequest) {
      if ($r->getParam('ajax') == 'security-log-grid') {
        $this->renderPartial('security', array(
          'account' => $model,
          'model' => new VLogAuth('search'),
        ));
      }

      Yii::app()->end();
    }

    if ($r->isPostRequest) {
      $model->attributes = $r->getParam(get_class($model), array());
      if (!empty($model->newPassword) || !empty($model->newPasswordConfirm)) {
        $model->setScenario('passwordUpdate');
      }
      if ($model->save()) {
        Yii::app()->user->setFlash('success', Yii::t('admin.messages', 'Your profile successfully updated'));
      }
    }

    $this->setPageTitle(Yii::t('admin.titles', 'My Profile'));
    $this->render('index', array(
      'model' => $model,
    ));
  }

  private function loadModel($id)
  {
    $model = VSiteAdmin::model()->findByPk($id);

    if ($model === null) {
      throw new CHttpException(404, Yii::t('common', 'Not found'));
    }

    return $model;
  }
}
