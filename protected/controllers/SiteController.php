<?php
/**
 * ViraCMS Static Pages Controller
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class SiteController extends VPublicController
{
  /**
   * Static page renderer
   * @param VPage $model page model
   */
  public function renderStaticPage($model)
  {
    if ($model != null) {
      if ($model->checkAccessibility()) {
        $this->setSubject($model);
        $this->render('index');
      }
      else {
        throw new CHttpException(403, Yii::t('common', 'Access is not allowed to requested page. You can try to {authenticate}.', array('{authenticate}' => CHtml::link(Yii::t('common', 'authenticate'), $this->createUrl('/auth/login')))));
      }
    }
    else {
      throw new CHttpException(404, Yii::t('common', "Requested page either isn't exists or just created. Please return back later."));
    }
  }

  /**
   * Index page
   */
  public function actionIndex()
  {
    $model = VPage::model()->with(array('l10n'))->findByAttributes(array(
      'siteID' => Yii::app()->site->id,
      'homepage' => 1,
    ));

    $this->renderStaticPage($model);
  }

  /**
   * Any other static page found by it's URL
   * @param string $url page URL as it's defined at page configuration form
   */
  public function actionPage($url)
  {
    $model = VPage::model()->with(array('l10n'))->findByAttributes(array(
      'siteID' => Yii::app()->site->id,
      'url' => $url,
    ));

    $this->renderStaticPage($model);
  }

  /**
   * Override the current theme to another
   * @param string $name theme name
   * @param string $returnUrl return URL
   */
  public function actionTheme($name, $returnUrl = null)
  {
    $cookie = new CHttpCookie(self::THEME_OVERRIDE_COOKIE_NAME, $name, array(
      'path' => '/',
      'httpOnly' => true,
      'expire' => strtotime(gmdate('Y-m-d 23:59:59')),
    ));
    Yii::app()->getRequest()->cookies[self::THEME_OVERRIDE_COOKIE_NAME] = $cookie;
    $this->redirect($returnUrl ? $returnUrl : array('/site/index'));
  }

  /**
   * Format breadcrumbs for a page
   * @return array
   */
  public function getBreadcrumbs()
  {
    if ($this->_subject !== null && $this->_subject instanceof VPage) {
      $path = $this->scanSitemap($this->_subject->id, Yii::app()->siteMap->get(Yii::app()->site->id));
      $this->setBreadcrumbs(array_reverse($path));
    }

    return parent::getBreadcrumbs();
  }

  /**
   * Backward sitemap scan for breadcrumbs
   * @param integer $id page identifier
   * @param array $sitemap current site map
   * @param integer $level level deep
   * @return array
   */
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
}
