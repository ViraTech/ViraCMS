<?php
/**
 * ViraCMS Page Renderer Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class RenderController extends VSystemController
{
  public $layout;
  protected $_subject;

  public function accessRules()
  {
    return array_merge(array(
      array(
        'allow',
        'actions' => array(
          'page',
        ),
        'roles' => array(
          'sitePageUpdate',
        ),
      ),
      array(
        'allow',
        'actions' => array(
          'layout',
        ),
        'roles' => array(
          'contentPageLayout',
        ),
      ),
      array(
        'allow',
        'actions' => array(
          'system',
        ),
        'roles' => array(
          'siteSystemPage',
        ),
      ),
      ), parent::accessRules());
  }

  protected function beforeAction($action)
  {
    if (parent::beforeAction($action)) {
      if (!Yii::app()->hasComponent('editor')) {
        throw new CHttpException(400, Yii::t('admin.content.errors', 'Editor component is not exists or not configured properly.'));
      }

      return true;
    }

    return false;
  }

  public function actionPage($id)
  {
    if (($this->_subject = VPage::model()->with(array('l10n', 'site'))->findByPk($id)) == null) {
      echo Yii::t('admin.content.errors', 'Page {id} not found in the database.', array('{id}' => $id));
      Yii::app()->end();
    }

    $this->renderEditor();
  }

  public function actionLayout($id, $site)
  {
    if (($this->_subject = VSiteLayout::model()->findByPk(array('id' => $id, 'siteID' => $site))) == null) {
      echo Yii::t('admin.content.errors', 'Layout {id} not found in the database.', array('{id}' => $id));
      Yii::app()->end();
    }

    $this->renderEditor();
  }

  public function actionSystem($id)
  {
    if (($this->_subject = VSystemPage::model()->with(array('currentL10n', 'site'))->findByPk($id)) == null) {
      echo Yii::t('admin.content.errors', 'System page {id} not found in the database.', array('{id}' => $id));
      Yii::app()->end();
    }

    $this->renderEditor();
  }

  protected function renderEditor()
  {
    if (!empty($this->_subject->layoutID)) {
      $this->layout = $this->_subject->layoutID;
    }
    $this->setSite($this->_subject->site);
    $renderer = Yii::app()->editor->createRenderer($this->_subject);
    $renderer->render();
  }

  public function getBreadcrumbs()
  {
    if ($this->_subject instanceof VPage) {
      $path = $this->scanSitemap($this->_subject->id, Yii::app()->siteMap->get(Yii::app()->site->id));
      $this->setBreadcrumbs(array_reverse($path));
    }

    return parent::getBreadcrumbs();
  }

  protected function scanSitemap($id, $sitemap, $level = 0)
  {
    $return = array();

    foreach ($sitemap as $page) {
      if ($page['id'] == $id) {
        $return = array($page['url'] => $page['label']);
        break;
      }
      elseif (isset($page['items']) && is_array($page['items'])) {
        $return = $this->scanSitemap($id, $page['items'], $level + 1);
        if ($return != array()) {
          $return[$page['url']] = $page['label'];
          break;
        }
      }
    }

    return $return;
  }

  protected function setSite($site)
  {
    Yii::app()->setSite($site);
    Yii::app()->setTheme($site->theme, VThemeManager::THEME_FRONTEND);
    $this->themeUrl = Yii::app()->getTheme()->baseUrl;
    $this->registerThemeFiles();
    $this->layout = 'default';
  }

  public function getSubject()
  {
    return $this->_subject;
  }

  public function getPage()
  {
    return $this->_subject;
  }

  /**
   * @inheritdoc
   */
  public function getIsHomePage()
  {
    return $this->_subject instanceof VPage ? $this->_subject->homepage : parent::getIsHomePage();
  }
}
