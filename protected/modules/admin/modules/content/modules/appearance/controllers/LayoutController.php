<?php
/**
 * ViraCMS Page Layouts Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class LayoutController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'create',
    'update',
    'delete',
    'config',
    'copy',
  );

  protected $accessRules = array(
    '*' => array('contentPageLayout'),
  );

  public function actionCreate()
  {
    $model = $this->getModel('create');
    $this->processAjaxRequest('create', $model);
    $this->updateModel($model);
    $this->setPageTitle($this->getTitle('create', array('model' => $model)));
    $this->renderUpdate($model, array(), 'config');
  }

  public function actionConfig($id, $copied = false)
  {
    $model = $this->loadModel($id, $copied ? 'copied' : 'update');
    $this->updateModel($model);
    $this->setPageTitle($this->getTitle('config', array('model' => $model)));
    $this->renderUpdate($model, array(), 'config');
  }

  public function actionUpdate($id)
  {
    $this->layout = 'no-footer';
    parent::actionUpdate($id);
  }

  public function actionCopy($id)
  {
    $model = $this->loadModel($id);
    $copy = $this->getModel('create');
    $copy->setAttributes($model->getAttributes(), false);
    $copy->id .= 'copy';
    $copy->default = 0;
    if ($copy->save()) {
      $compliance = array();
      if ($model->blocks) {
        foreach ($model->blocks as $block) {
          $className = get_class($block);
          $blockCopy = new $className('create');
          $blockCopy->setAttributes($block->getAttributes(), false);
          $blockCopy->id = $blockCopy->getGuid();
          $blockCopy->layoutID = $copy->id;
          if ($blockCopy->save()) {
            $compliance[$block->id] = $blockCopy->id;
          }
        }
      }
      if ($model->rows) {
        foreach ($model->rows as $row) {
          $className = get_class($row);
          $rowCopy = new $className('create');
          $rowCopy->setAttributes($row->getAttributes(), false);
          $rowCopy->layoutID = $copy->id;
          if (preg_match_all('/(###VIRA_BLOCK_ID_(\d+)_###)/', $rowCopy->template, $matches)) {
            for ($i = 0; $i < count($matches[0]); $i++) {
              if (isset($compliance[$matches[2][$i]])) {
                $rowCopy->template = str_replace($matches[1][$i], '###VIRA_BLOCK_ID_' . $compliance[$matches[2][$i]] . '_###', $rowCopy->template);
              }
            }
          }
          $rowCopy->save();
        }
      }
      $this->redirect(array('config', 'id' => implode(',', $copy->getPrimaryKey()), 'copied' => '1'));
    }
    else {
      Yii::app()->user->setFlash('error', Yii::t('admin.content.errors', 'An error occurred while copying: {error}.', array(
          '{error}' => $copy->getFirstError(),
      )));
      $this->redirect(array('index'));
    }
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.content.titles', 'Page Layouts');

      case 'create':
        return Yii::t('admin.content.titles', 'New Page Layout');

      case 'config':
        return Yii::t('admin.content.titles', 'Configure Page Layout "{title}"', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.titles', 'Update Page Layout "{title}"', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.titles', 'Delete Page Layout "{title}"', array('{title}' => $model->title));

      case 'mass':
        return Yii::t('admin.content.titles', 'Mass action with page layouts');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.content.messages', 'Page layout "{title}" has been successfully created', array('{title}' => $model->title));

      case 'update':
        return Yii::t('admin.content.messages', 'Page layout "{title}" has been successfully updated', array('{title}' => $model->title));

      case 'delete':
        return Yii::t('admin.content.messages', 'Page layout "{title}" has been successfully removed', array('{title}' => $model->title));
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete page layout "{title}"?', array('{title}' => $model->title));
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Are you sure to delete selected page layouts?');
    }

    return parent::getMassActionConfirmMessage($action, $params);
  }

  public function getActionButtonConfig($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return array(
          'type' => 'danger',
          'buttonType' => 'submit',
          'label' => Yii::t('common', 'Delete'),
          'icon' => 'icon-trash',
          'htmlOptions' => array(
            'name' => 'delete',
            'data-loading-text' => '<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Removing...'),
          ),
        );
    }

    return parent::getActionButtonConfig($action, $params);
  }

  public function getMassActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.content.messages', 'Page layouts has been successfully removed');
    }

    return parent::getMassActionSuccessMessage($action, $params);
  }

  public function getErrorMessage($params = array())
  {
    extract($params);
    return Yii::t('common', 'An error occurred while processing: {error}.', array('{error}' => $model->getFirstError()));
  }

  public function getMassActionErrorMessage($params = array())
  {
    extract($params);
    return Yii::t('common', 'Please select something!');
  }

  public function getNotFoundErrorMessage($params = array())
  {
    return Yii::t('admin.content.errors', 'Page layout not found');
  }

  protected function beforeUpdateModel(&$model, $action = null)
  {
    if (parent::beforeUpdateModel($model, $action)) {
      if (in_array($action ? $action : $this->action->id, array('create', 'config'))) {

        return $model->validate();
      }

      return true;
    }

    return false;
  }

  protected function afterUpdateModel($model, $action = null)
  {
    if (Yii::app()->request->getParam('edit', 0) == 1) {
      $this->redirect(array('update', 'id' => implode(',', $model->getPrimaryKey())));
    }
    parent::afterUpdateModel($model, $action);
  }

  protected function preprocessData(&$data)
  {
    $data['areas'] = CHtml::listData($data['model']->areas, 'id', 'title');
  }

  public function getModelTitle($model)
  {
    return $model->title;
  }

  public function getModel($scenario = 'search')
  {
    $model = new VSiteLayout($scenario);

    if ($scenario == 'create') {
      $model->siteID = Yii::app()->site->id;
    }

    return $model;
  }

  public function getPlainModel($scenario)
  {
    return VSiteLayout::model();
  }
}
