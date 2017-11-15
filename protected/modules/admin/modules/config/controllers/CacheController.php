<?php
/**
 * ViraCMS Cache Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class CacheController extends VSystemController
{
  public $layout = 'inner';

  public function accessRules()
  {
    return CMap::mergeArray(array(
        array(
          'allow',
          'actions' => array(
            'index',
          ),
          'roles' => array(
            'contentCache',
          ),
        ),
        ), parent::accessRules());
  }

  public function actionIndex()
  {
    $r = Yii::app()->request;
    if ($r->isPostRequest) {
      $success = false;
      if ($r->getParam('opcode')) {
        VCacheHelper::flushOpcodeCache();
        $success = true;
      }
      if ($r->getParam('app')) {
        VCacheHelper::flushAppCache();
        $success = true;
      }
      if ($r->getParam('config')) {
        VCacheHelper::flushConfigCache();
        $success = true;
      }
      if ($r->getParam('image')) {
        VCacheHelper::flushImageCache();
        $success = true;
      }
      if ($r->getParam('assets')) {
        VCacheHelper::flushAssetsCache();
        $success = true;
      }
      if ($success) {
        Yii::app()->user->setFlash('success', Yii::t('admin.content.titles', 'Cache flushed successfully'));
      }
      else {
        Yii::app()->user->setFlash('error', Yii::t('admin.content.errors', 'Please select cache section to flush'));
      }
      $this->redirect('index');
    }

    $this->setPageTitle(Yii::t('admin.content.titles', 'Site Cache'));
    $this->render('index');
  }
}
