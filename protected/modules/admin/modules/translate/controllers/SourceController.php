<?php
/**
 * ViraCMS Source Messages Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.translate
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class SourceController extends VSystemCrudController
{
  const CSV_ENCODING = 'cp1251';

  private $_languages;

  protected $actions = array(
    'index',
    'create',
    'update',
    'delete',
    'source',
    'download',
  );

  protected $accessRules = array(
    '*' => array('translateTranslate'),
  );

  public function actionDownload($file)
  {
    $filename = basename($file);
    $path = Yii::app()->runtimePath . DIRECTORY_SEPARATOR . $filename;
    if (file_exists($path) && is_readable($path)) {
      header('Content-Disposition: attachment; filename=' . $filename);
      header('Last-Modified: ' . date('D, d M Y H:i:s T', filemtime($path)));
      $f = fopen($path, 'r');
      fpassthru($f);
      fclose($f);
      @unlink($path);
    }
    else {
      throw new CHttpException(400, Yii::t('admin.translate.errors', 'File {file} not found or is not readable.', array('{file}' => $filename)));
    }
  }

  public function actionSource()
  {
    $r = Yii::app()->request;
    $model = new DownloadSourceMessageForm();

    $this->setPageTitle($this->getTitle('source'));

    if ($r->isPostRequest) {
      $model->attributes = $r->getParam(get_class($model), array());
      if ($model->validate()) {
        $tmpFile = $this->generateCsv($model);
        if ($tmpFile) {
          $this->cs->registerMetaTag('1;url=' . $this->createAbsoluteUrl('download', array('file' => $tmpFile)), '', 'refresh');
        }
        $this->render('download', array(
          'tmpFile' => $tmpFile,
        ));
        Yii::app()->end();
      }
    }

    $languages = CHtml::listData(VLanguageHelper::getLanguages(), 'id', 'title');
    unset($languages[Yii::app()->sourceLanguage]);
    $this->render('source', array(
      'model' => $model,
      'languages' => $languages,
    ));
  }

  protected function generateCsv($form)
  {
    $tmpFile = false;
    $messages = $this->getMessages($form);

    if (count($messages)) {
      $tmpFile = 'viracms_messages_' . ($form->languageID ? $form->languageID : 'lng') . '_' . $form->encoding . '.csv';
      $encode = $form->encoding != Yii::app()->charset;

      $f = fopen(Yii::app()->runtimePath . DIRECTORY_SEPARATOR . $tmpFile, 'w');
      foreach ($messages as $message) {
        if ($encode) {
          foreach ($message as &$value) {
            $encoded = @iconv(Yii::app()->charset, $form->encoding, $value);
            if ($encoded) {
              $value = $encoded;
            }
          }
        }
        fputcsv($f, $message, ';', '"');
      }
      fclose($f);
    }

    return $tmpFile;
  }

  protected function generateXml($model)
  {
    $tmpFile = 'viracms_messages_' . ($model->languageID ? $model->languageID : 'lng') . '_' . $model->encoding . '.xml';
    $data = array();
    $messages = $this->getMessages($model->languageID);

    foreach ($messages as $message) {
      if (strncasecmp($model->encoding, Yii::app()->charset, max(strlen($model->encoding), strlen(Yii::app()->charset))) == 0) {
        foreach ($message as &$value) {
          $encoded = @iconv(Yii::app()->charset, $model->encoding, $value);
          if ($encoded) {
            $value = $encoded;
          }
        }
      }
      $msg = array();
      $msg['source'] = $message['source'];
      if (isset($message['translate'])) {
        $msg['translate'] = $message['translate'];
      }
      $data[$message['module']][$message['category']][] = $msg;
    }

    $content = '<?xml version="1.0" encoding="' . $model->encoding . '" ?>' . PHP_EOL;
    $content .= CHtml::openTag('messages') . PHP_EOL;
    $currentModule = null;
    foreach ($data as $module => $messages) {
      if ($module !== $currentModule) {
        $content .= "\t" . CHtml::openTag('module', array('id' => $module)) . PHP_EOL;
      }
      $currentCategory = null;
      foreach ($messages as $category => $list) {
        if ($category !== $currentCategory) {
          $content .= "\t\t" . CHtml::openTag('category', array('id' => $category)) . PHP_EOL;
        }
        foreach ($list as $text) {
          $content .= "\t\t\t" . CHtml::openTag('message') . PHP_EOL;
          if (isset($text['source'])) {
            $content .= "\t\t\t\t" . CHtml::tag('source', array('language' => Yii::app()->sourceLanguage), $text['source']) . PHP_EOL;
          }
          if ($model->languageID) {
            $content .= "\t\t\t\t" . CHtml::tag('translate', array('language' => $model->languageID), isset($text['translate']) ? $text['translate'] : '') . PHP_EOL;
          }
          else {
            $content .= "\t\t\t\t" . CHtml::tag('translate', array('language' => 'nolang'), '') . PHP_EOL;
          }
          $content .= "\t\t\t" . CHtml::closeTag('message') . PHP_EOL;
        }
        if ($category !== $currentCategory) {
          $content .= "\t\t" . CHtml::closeTag('category') . PHP_EOL;
        }
      }
      if ($module !== $currentModule) {
        $content .= "\t" . CHtml::closeTag('module') . PHP_EOL;
        $currentModule = $module;
      }
    }
    $content .= CHtml::closeTag('messages') . PHP_EOL;

    file_put_contents(Yii::app()->runtimePath . DIRECTORY_SEPARATOR . $tmpFile, $content);

    return $tmpFile;
  }

  protected function getMessages($form)
  {
    $sourceTableName = VTranslateSource::model()->tableName();
    $translationTableName = VTranslate::model()->tableName();

    $command = Yii::app()->db->createCommand();

    $select = 's.module,s.category,s.source';
    if ($form->languageID) {
      $select .= ',t.translate';
    }

    $command->select($select);
    $command->from($sourceTableName . ' s');

    if ($form->languageID) {
      $command->leftJoin($translationTableName . ' t', 't.hash=s.hash AND t.module=s.module AND t.category=s.category AND t.languageID=:languageID', array(
        ':languageID' => $form->languageID,
      ));
      if ($form->withoutTranslation) {
        $command->where("(t.translate IS NULL OR t.translate='')");
      }
    }

    $command->order('s.module ASC,s.category ASC');

    return $command->queryAll();
  }

  public function getTitle($view, $params = array())
  {
    extract($params);

    switch ($view) {
      case 'index':
        return Yii::t('admin.translate.titles', 'Source Messages');

      case 'create':
        return Yii::t('admin.translate.titles', 'New Source Message');

      case 'update':
        return Yii::t('admin.translate.titles', 'Update Source Message');

      case 'delete':
        return Yii::t('admin.translate.titles', 'Delete Source Message');

      case 'source':
        return Yii::t('admin.translate.titles', 'Download Source Messages');

      case 'mass':
        return Yii::t('admin.translate.titles', 'Mass action with source messages');
    }

    return '';
  }

  protected function getActionSuccessMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'create':
        return Yii::t('admin.translate.messages', 'Source message has been successfully created');

      case 'update':
        return Yii::t('admin.translate.messages', 'Source message has been successfully updated');

      case 'delete':
        return Yii::t('admin.translate.messages', 'Source message has been successfully removed');
    }
    parent::getActionSuccessMessage($action, $params);
  }

  public function getActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.translate.messages', 'Are you sure to delete source message?');
    }

    return parent::getActionConfirmMessage($action, $params);
  }

  public function getMassActionConfirmMessage($action, $params = array())
  {
    extract($params);
    switch ($action) {
      case 'delete':
        return Yii::t('admin.translate.messages', 'Are you sure to delete selected source messages?');
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
        return Yii::t('admin.translate.messages', 'Source messages has been successfully removed');
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
    return Yii::t('admin.translate.errors', 'Source message not found');
  }

  public function getModelTitle($model)
  {
    return $model->source;
  }

  public function getModel($scenario = 'search')
  {
    return new VTranslateSource($scenario);
  }

  public function getPlainModel($scenario)
  {
    return VTranslateSource::model();
  }

  public function renderTranslationButtons($model)
  {
    if (empty($this->_languages)) {
      $this->_languages = CHtml::listData(VLanguage::model()->noSource()->findAll(), 'id', 'title');
    }

    $params = array();
    if (($page = Yii::app()->request->getParam('page', false))) {
      $params['page'] = $page;
    }

    return $this->renderPartial('buttons', array(
      'model' => $model,
      'languages' => $this->_languages,
      'create' => 'message/create',
      'update' => 'message/update',
      'returnUrl' => urlencode($this->createUrl('/admin/translate/source/index', $params)),
      ), true, false);
  }
}
