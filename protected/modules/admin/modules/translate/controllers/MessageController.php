<?php
/**
 * ViraCMS Translated Messages Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class MessageController extends VSystemCrudController
{
  protected $actions = array(
    'index',
    'create',
    'update',
    'delete',
    'upload',
  );

  protected $accessRules = array(
    '*' => array('translateTranslate'),
  );

  public function actionCreate()
  {
    $model = $this->getModel('create');

    $hash = Yii::app()->request->getParam('hash');
    $module = Yii::app()->request->getParam('module');
    $category = Yii::app()->request->getParam('category');
    $lid = Yii::app()->request->getParam('lid');

    $source = VTranslateSource::model()->findByPk(array(
      'hash' => $hash,
      'module' => $module,
      'category' => $category,
    ));
    if ($source == null) {
      throw new CHttpException(404, Yii::t('admin.translate.errors', 'Source message not found.'));
    }
    $model->hash = $source->hash;
    $model->module = $source->module;
    $model->category = $source->category;
    $model->languageID = $lid;

    $this->processAjaxRequest('create', $model);
    $this->updateModel($model);
    $this->setPageTitle($this->getTitle('create', array('model' => $model)));
    $this->renderUpdate($model, array(
      'source' => $source,
    ));
  }

  public function actionUpload()
  {
    $r = Yii::app()->request;
    $model = new UploadTranslationForm();
    $languages = CHtml::listData(VLanguageHelper::getLanguages(), 'id', 'title');
    unset($languages[Yii::app()->sourceLanguage]);

    if ($r->isPostRequest) {
      $model->attributes = $r->getParam(get_class($model), array());
      if ($model->validate()) {
        $source = VTranslateSource::model();
        $destTable = VTranslate::model()->tableName();
        $uploaded = 0;
        $found = 0;
        $db = Yii::app()->db;
        $count = $db->createCommand()->
          select('COUNT(*)')->
          from($source->tableName())->
          where(array(
          'AND',
          'module=:module',
          'category=:category',
          'hash=:hash',
        ));
        $cmd = $db->createCommand();
        $f = fopen($model->file->tempName, 'r');
        while (!feof($f)) {
          $row = fgetcsv($f, 0, ';', '"');
          if (is_array($row) && count($row) > 3) {
            $hash = $source->getMessageHash($row[2]);
            if ($hash) {
              if ($model->encoding != Yii::app()->charset) {
                $row[3] = mb_convert_encoding($row[3], Yii::app()->charset, $model->encoding);
              }
              if ($count->queryScalar(array(
                  ':module' => $row[0],
                  ':category' => $row[1],
                  ':hash' => $hash,
                )) > 0) {
                try {
                  $cmd->insert($destTable, array(
                    'hash' => $hash,
                    'module' => $row[0],
                    'category' => $row[1],
                    'languageID' => $model->languageID,
                    'translate' => $row[3],
                  ));
                }
                catch (Exception $e) {
                  $cmd->update(
                    $destTable, array(
                    'translate' => $row[3],
                    ), 'hash=:hash AND module=:module AND category=:category AND languageID=:languageID', array(
                    ':hash' => $hash,
                    ':module' => $row[0],
                    ':category' => $row[1],
                    ':languageID' => $model->languageID,
                    )
                  );
                }
                $found++;
              }
            }
          }
          $uploaded++;
        }
        fclose($f);
        Yii::app()->user->setFlash('success', Yii::t('admin.translate.messages', 'Uploaded {n} message.|Uploaded {n} messages.', array($uploaded)) . ' ' .
          Yii::t('admin.translate.messages', 'Found {n} message.|Found {n} messages.', array($found))
        );
        $this->redirect(array('index'));
      }
    }

    $this->setPageTitle($this->getTitle('upload'));
    $this->render('upload', array(
      'model' => $model,
      'languages' => $languages,
    ));
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.translate.titles', 'Translated Messages');

      case 'create':
        return Yii::t('admin.translate.titles', 'New Message Translation');

      case 'update':
        return Yii::t('admin.translate.titles', 'Update Message Translation');

      case 'delete':
        return Yii::t('admin.translate.titles', 'Delete Message Translation');

      case 'upload':
        return Yii::t('admin.translate.titles', 'Upload Translated Messages');

      case 'mass':
        return Yii::t('admin.translate.titles', 'Mass action with translated messages');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.translate.messages', 'Message translation has been successfully created');

      case 'update':
        return Yii::t('admin.translate.messages', 'Message translation has been successfully updated');

      case 'delete':
        return Yii::t('admin.translate.messages', 'Message translation has been successfully removed');
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.translate.messages', 'Are you sure to delete message translation?');
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.translate.messages', 'Are you sure to delete selected message translations?');
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
        return Yii::t('admin.translate.messages', 'Message translations has been successfully removed');
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
    return Yii::t('admin.translate.errors', 'Message translation not found');
  }

  public function getModelTitle($model)
  {
    return $model->translate;
  }

  public function getModel($scenario = 'search')
  {
    return new VTranslate($scenario);
  }

  public function getPlainModel($scenario)
  {
    return VTranslate::model();
  }
}
