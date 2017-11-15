<?php
/**
 * ViraCMS Page Editor Functions Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class EditorController extends VSystemController
{
  public $layout = null;
  private $_page = null;

  public function accessRules()
  {
    return array(
      array(
        'allow',
        'actions' => array(
          'save',
          'image',
          'flash',
          'file',
          'video',
          'widget',
          'configure',
          'upload',
        ),
        'roles' => array('sitePageUpdate',),
      ),
      parent::accessRules()
    );
  }

  protected function beforeAction($action)
  {
    if ($this->action->id == 'widget') {
      Yii::app()->setTheme(Yii::app()->themeManager->frontendTheme, VThemeManager::THEME_FRONTEND);
    }

    parent::beforeAction($action);

    return true;
  }

  public function filters()
  {
    return array(
      'accessControl',
      'ajaxOnly + save,widget',
      'ajaxOnly + upload',
    );
  }

  public function actions()
  {
    return array(
      'save' => Yii::app()->editor->updateActionClass,
      'image' => Yii::app()->editor->imageBrowserActionClass,
      'video' => Yii::app()->editor->videoBrowserActionClass,
      'flash' => Yii::app()->editor->flashBrowserActionClass,
      'file' => Yii::app()->editor->fileBrowserActionClass,
      'widget' => Yii::app()->editor->widgetActionClass,
      'configure' => Yii::app()->editor->configureActionClass,
    );
  }

  public function actionLinks()
  {
    $links = $this->prepareLinks(Yii::app()->siteMap->getMenu(Yii::app()->site->id));

    $this->renderPartial('links', array(
      'internalLinks' => $links,
      'libraryUrl' => $this->createUrl('library'),
    ));
  }

  public function prepareLinks($links, $level = 0)
  {
    $return = array();
    foreach ($links as $link) {
      $inner = array();
      if (isset($link['items']) && is_array($link['items']) && count($link['items'])) {
        $inner = $this->prepareLinks($link['items'], $level + 1);
      }
      $return[$link['url']] = str_pad($link['label'], $level * 3 + strlen($link['label']), ' ', STR_PAD_LEFT);
      if (count($inner)) {
        foreach ($inner as $url => $title) {
          $return[$url] = $title;
        }
      }
    }
    return $return;
  }

  public function actionUpload($type)
  {
    $r = Yii::app()->request;
    $result = array();
    $filename = $r->getParam('filename');
    $file = $this->processUpload($filename);
    if ($file !== false) {
      $info = finfo_open(FILEINFO_MIME_TYPE);
      switch ($type) {
        case 'file':
          $model = new VContentFile('auto');
          break;
        case 'image':
          $model = new VContentImage('auto');
          break;
        case 'video':
        case 'flash':
          $model = new VContentMedia('auto');
          break;
      }
      $model->siteID = $r->getParam('siteID', Yii::app()->site->id);
      $model->className = $r->getParam('className', 'Internal');
      $model->primaryKey = $r->getParam('primaryKey', Yii::app()->user->id);
      if (is_array($model->primaryKey)) {
        $model->primaryKey = implode('_', $model->primaryKey);
      }
      $model->filename = $filename;
      $model->path = $file;
      if (!$model->save()) {
        throw new CHttpException(400, Yii::t('admin.content.errors', 'Can not save model {model}: {error}', array('{model}', get_class($model), '{error}' => $model->getFirstError())));
      }
      $result['success'] = true;
    }
    else {
      $result['error'] = 'An error occurred while processing';
    }
    echo CJSON::encode($result);
  }

  private function processUpload($filename)
  {
    $upload = CUploadedFile::getInstanceByName($filename);

    if ($upload instanceof CUploadedFile) {
      $tmpFile = $upload->tempName;
    }
    else {
      $tmpFile = tempnam(Yii::app()->runtimePath, 'imgxhr');
      $tmpFileHandle = fopen($tmpFile, 'w');
      $input = fopen("php://input", "r");
      $realSize = stream_copy_to_stream($input, $tmpFileHandle);
      fclose($input);
      fclose($tmpFileHandle);
      if (isset($_SERVER['CONTENT_LENGTH']) && $realSize !== intval($_SERVER['CONTENT_LENGTH'])) {
        @unlink($tmpFile);
        return false;
      }
    }

    return Yii::app()->storage->addFile($tmpFile, $filename);
  }

  public function setPage($page)
  {
    $this->_page = $page instanceof VPage ? $page : VPage::model()->findByPk($page);
  }

  public function getPage()
  {
    return $this->_page;
  }
}
