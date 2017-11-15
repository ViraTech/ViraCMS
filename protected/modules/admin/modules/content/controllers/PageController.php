<?php
/**
 * ViraCMS Static Pages Management Controller
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class PageController extends VSystemController
{
  const CONNECT_CHILDREN_TO_PARENT = 1;
  const DELETE_CHILDREN = 2;

  public $layout = 'inner';

  public function accessRules()
  {
    return array(
      array(
        'allow',
        'actions' => array(
          'index',
          'map',
        ),
        'roles' => array(
          'sitePageCreate',
          'sitePageUpdate',
          'sitePageConfig',
          'sitePageDelete',
        ),
      ),
      array(
        'allow',
        'actions' => array(
          'create',
        ),
        'roles' => array(
          'sitePageCreate',
        ),
      ),
      array(
        'allow',
        'actions' => array(
          'update',
        ),
        'roles' => array(
          'sitePageUpdate',
        ),
      ),
      array(
        'allow',
        'actions' => array(
          'config',
          'move',
          'visibility',
          'ajax',
        ),
        'roles' => array(
          'sitePageConfig',
        ),
      ),
      array(
        'allow',
        'actions' => array(
          'delete',
        ),
        'roles' => array(
          'sitePageDelete',
        ),
      ),
      parent::accessRules()
    );
  }

  public function actionIndex()
  {
    $r = Yii::app()->request;
    $model = $this->getModel();
    $model->unsetAttributes();

    $model->attributes = $r->getParam(get_class($model), array());

    if ($r->isAjaxRequest) {
      if ($r->getParam($model->getGridID()) !== null) {
        $this->renderPartial('grid', array(
          'model' => $model,
        ));
        Yii::app()->end();
      }
    }

    $this->setPageTitle(Yii::t('admin.content.titles', 'Static Pages'));
    $this->render('index', array(
      'model' => $model,
    ));
  }

  public function actionMap($site = null)
  {
    $user = Yii::app()->user->model;
    $site = $this->getSite($site);

    if (!$user->siteAccess && !$user->hasSiteAccess($site->id)) {
      throw new CHttpException(403, Yii::t('admin.content.errors', 'Access to this site is restricted.'));
    }

    $params = array(
      'model' => $this->getModel(),
      'site' => $site,
      'widget' => $this->module->sitemap,
      'alias' => 'application.widgets.sitemaps.' . $this->module->sitemap . '.' . $this->module->sitemap . 'Widget',
      'sites' => CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'),
    );

    if (Yii::app()->request->isAjaxRequest) {
      $this->renderPartial('sitemap', $params);
      Yii::app()->end();
    }

    $this->setPageTitle(Yii::t('admin.content.titles', 'Site "{title}" Map', array('{title}' => $site->title)));
    $this->render('map', $params);
  }

  public function actionCreate($site = null)
  {
    $site = $this->getSite($site);
    $r = Yii::app()->request;
    $model = $this->getModel('create');
    $model->siteID = $site->id;
    $model->class = Yii::app()->collection->pageRenderer->getDefaultRenderer();

    if ($r->isPostRequest) {
      if ($this->updateModel($model)) {
        Yii::app()->user->setFlash('success', Yii::t('admin.content.messages', 'Page "{title}" successfully created', array('{title}' => $model->title)));
        $this->redirect(array($r->getParam('return', 'index'), 'site' => $model->siteID));
      }
    }
    elseif ($r->getParam('parent')) {
      $model->parentID = $r->getParam('parent');
    }

    $title = Yii::t('admin.content.titles', 'New Page');
    $this->setPageTitle($title);
    $this->render('config', array(
      'model' => $model,
      'site' => $site,
      'languages' => VLanguageHelper::getLanguages(),
      'parentUrl' => $model->getParentUrl(),
      'parentPages' => $this->getParentPages($model),
      'parentUrls' => $this->getParentUrls($model),
      'title' => $title,
    ));
  }

  public function actionConfig($id)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);
    $model->setScenario('configure');

    if ($r->isPostRequest) {
      if ($this->updateModel($model)) {
        Yii::app()->user->setFlash('success', Yii::t('admin.content.messages', 'Page "{title}" successfully updated', array('{title}' => $model->title)));
        $this->redirect(array($r->getParam('return', 'index'), 'site' => $model->siteID));
      }
    }

    $title = Yii::t('admin.content.titles', 'Page "{title}" Options', array('{title}' => $model->title));
    $this->setPageTitle($title);
    $this->render('config', array(
      'model' => $model,
      'site' => $model->site,
      'languages' => VLanguageHelper::getLanguages(),
      'parentUrl' => $model->getParentUrl(),
      'parentPages' => $this->getParentPages($model),
      'parentUrls' => $this->getParentUrls($model),
      'title' => $title,
    ));
  }

  public function actionUpdate($id)
  {
    $this->layout = 'no-footer';
    $model = $this->loadModel($id);

    if (Yii::app()->collection->rendererAction->getRendererAction($model->class) != VRendererActionCollection::ACTION_OUTPUT) {
      Yii::app()->user->setFlash('error', Yii::t('admin.content.errors', 'Can not edit this type of page.'));
      $this->redirect(array('index'));
    }

    Yii::app()->editor->setModel($model);

    $title = Yii::t('admin.content.titles', 'Edit Page "{title}"', array('{title}' => $model->title));
    $this->setPageTitle($title);
    $this->render('update', array(
      'model' => $model,
      'title' => $title,
    ));
  }

  public function actionDelete()
  {
    $r = Yii::app()->request;

    $model = $this->loadModel($r->getParam('id'));

    if ($r->isAjaxRequest || $r->isPostRequest) {
      if ($model->children && $r->getParam('children', self::CONNECT_CHILDREN_TO_PARENT) == self::CONNECT_CHILDREN_TO_PARENT) {
        foreach ($model->children as $child) {
          $child->parentID = $model->parent ? $model->parent->id : null;
          $url = explode('/', trim($child->url, '/'));
          $url = array_pop($url);
          $child->url = ($model->parent ? $model->parent->url : '') . '/' . $url;
          $child->save(false);
        }
      }
      $model->getRelated('children', true);
      $model->delete();
      if ($r->isAjaxRequest) {
        Yii::app()->end();
      }
      else {
        Yii::app()->user->setFlash('success', Yii::t('admin.content.messages', 'Page "{title}" successfully removed', array('{title}' => $model->title)));
        $this->redirect(array('index'));
      }
    }

    $this->render('delete', array(
      'model' => $model,
    ));
  }

  public function actionMove()
  {
    $r = Yii::app()->request;
    if (!$r->isAjaxRequest) {
      $this->redirect($this->createUrl('index'));
    }

    $id = $r->getParam('id');
    $parentID = $r->getParam('parent');
    $sorting = $r->getParam('sorting');

    $model = $this->loadModel($id);
    if ($model->parentID != $parentID && !$model->move($parentID)) {
      echo $model->getFirstError();
      Yii::app()->end();
    }

    if (is_array($sorting)) {
      foreach ($sorting as $position => $id) {
        $model = $this->loadModel($id);
        $model->position = $position;
        if (!$model->save()) {
          echo $model->getFirstError();
          Yii::app()->end();
        }
      }
    }
  }

  public function actionVisibility()
  {
    $r = Yii::app()->request;
    $id = $r->getParam('id');
    $children = $r->getParam('children');
    $visibility = $r->getParam('visibility');
    if (!$r->isAjaxRequest) {
      $this->redirect(array('index'));
    }

    $model = $this->loadModel($id);
    $model->setScenario('visibility');
    $model->visibility = $visibility;
    if (!$model->save(false)) {
      echo $model->getFirstError();
      Yii::app()->end();
    }

    if (is_array($children)) {
      foreach ($children as $id) {
        $model = $this->loadModel($id);
        $model->visibility = $visibility;
        $model->save(false);
      }
    }
  }

  public function actionAjax()
  {
    $r = Yii::app()->request;

    if (($route = $r->getParam('route')) !== null) {
      echo CJSON::encode($this->getItems($route, $r->getParam('pageID'), $r->getParam('siteID')));
      Yii::app()->end();
    }

    if (($site = $r->getParam('site')) !== null) {
      $model = $this->getModel('create');
      $model->siteID = $site;
      echo CJSON::encode(array(
        'parentPages' => $this->getParentPages($model),
        'parentUrls' => $this->getParentUrls($model),
        'layouts' => CHtml::listData(VSiteLayout::model()->from($site)->findAll(), 'id', 'title')
      ));
      Yii::app()->end();
    }
  }

  /**
   * Return parent pages label list
   *
   * @param VPage $model page generate parents for
   * @return array
   */
  private function getParentPages($model)
  {
    return $this->getSitemapItems($model, 'label', array('pad' => 2, 'noHomepage' => true, 'skipCurrent' => true), Yii::app()->siteMap->get($model->siteID));
  }

  /**
   * Return redirect pages list
   *
   * @param VPage $model page generate parents for
   * @return array
   */
  private function getRedirectPages($model)
  {
    return $this->getSitemapItems($model, 'label', array('pad' => 2, 'noHomepage' => true), Yii::app()->siteMap->get($model->siteID));
  }

  /**
   * Return parent pages URL list
   *
   * @param VPage $model page generate parents for
   * @return array
   */
  private function getParentUrls($model)
  {
    return $this->getSitemapItems($model, 'url', array('noHomepage' => true), Yii::app()->siteMap->get($model->siteID));
  }

  /**
   * Filter and return site map items
   *
   * @param VPage $model filter by model
   * @param string $attribute return attribute
   * @param array $config additional options
   * @param array $items items for recursive use
   * @param integer $level level for recursive use
   * @return array
   */
  private function getSitemapItems($model, $attribute, $config, $items = array(), $level = 0)
  {
    $return = array();

    foreach ($items as $item) {
      if (isset($config['noHomepage']) && $config['noHomepage'] && $item['homepage']) {
        continue;
      }
      if (isset($config['skipCurrent']) && $config['skipCurrent'] && $item['id'] == $model->id) {
        continue;
      }
      $return[$item['id']] = (isset($config['pad']) ? str_pad('', $config['pad'] * $level * 2, "\xc2\xa0") : '') . $item[$attribute];
      if (isset($item['items']) && count($item['items'])) {
        $return = CMap::mergeArray($return, $this->getSitemapItems($model, $attribute, $config, $item['items'], $level + 1));
      }
    }

    return $return;
  }

  public function getEntryPoints()
  {
    $points = array();

    foreach ($this->module->parentModule->modules as $id => $config) {
      $module = $this->module->parentModule->getModule($id);
      foreach ($module->getEntryPoints() as $route => $label) {
        $points[$route] = $label;
      }
    }

    return CMap::mergeArray(
        array('VPage' => Yii::t('admin.content.labels', 'Site Page')), $points
    );
  }

  public function getItems($route, $page = null, $siteID = null)
  {
    if ($route == 'VPage') {
      if (!($page instanceof VPage)) {
        $page = $this->loadModel($page);
      }

      $items = array();
      foreach ($this->getRedirectPages($page) as $id => $label) {
        $items['id' . VPage::REDIRECT_ITEM_DELIMITER . $id] = $label;
      }

      return $items;
    }
    else {
      $modules = array_filter(explode('/', $route));
      if ($modules !== array()) {
        $modules = array_slice($modules, 0, count($modules) - 2);
        $module = $this->module->parentModule->getModule($modules[0]);
        if (count($modules) > 1) {
          $modules = array_slice($modules, 1);
          foreach ($modules as $id) {
            $module = $module->getModule($id);
          }
        }
      }

      return isset($module) && $module instanceof VWebModule ? $module->getAvailableItems(array_slice(explode('/', $route), -2), $siteID) : array();
    }
  }

  /**
   * Create model
   *
   * @param string $scenario model scenario
   * @return VPage
   */
  private function getModel($scenario = 'search')
  {
    return new VPage($scenario);
  }

  /**
   * Find and return model by ID
   *
   * @param integer $id
   * @return VPage
   * @throws CHttpException
   */
  private function loadModel($id)
  {
    $model = VPage::model()->with(array('site', 'l10n'))->findByPk($id);
    if ($model === null) {
      throw new CHttpException(404, Yii::t('admin.content.errors', 'Page ID {id} not found', array('{id}' => $id)));
    }
    return $model;
  }

  /**
   * Update model
   *
   * @param VPage $model model
   * @return boolean model has been successfully updated
   */
  private function updateModel(&$model)
  {
    $r = Yii::app()->request;
    $model->attributes = $r->getParam(get_class($model), array());
    $model->populateL10nModels($r);
    $model->populateSeoModels($r);

    $validated = true;
    $validated &= $model->validate();
    $validated &= $model->validateL10nModels();
    $validated &= $model->validateSeoModels();

    if ($validated) {
      $model->save(false);
      $model->saveL10nModels(false);
      $model->saveSeoModels(false);

      return true;
    }

    return false;
  }

  private function getSite($site)
  {
    $site = $site == null ? Yii::app()->site : VSite::model()->findByPk($site);
    if ($site == null) {
      VSite::model()->findByAttributes(array('default' => 1));
    }
    return $site;
  }
}
