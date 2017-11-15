<?php
/**
 * ViraCMS Redirect Type Page Renderer Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VRedirectPageRenderer extends VApplicationComponent
{
  /**
   * @var VPage current page
   */
  private $_model;

  /**
   * Class constructor
   * @param VPage $model current page
   */
  function __construct($model)
  {
    $this->_model = $model;
  }

  /**
   * Perform redirect to specified in self::$page URL
   */
  public function render()
  {
    $controller = Yii::app()->getController();

    $redirectTo = $controller->createUrl('/site/index');

    if ($this->_model->redirectRoute == 'VPage') {
      if (($page = VPage::model()->findByPk($this->_model->redirectItem)) != null) {
        $redirectTo = $page->createUrl();
      }
      elseif ($this->_model->redirectUrl) {
        $redirectTo = $this->_model->redirectUrl;
      }
    }
    elseif ($this->_model->redirectUrl) {
      $redirectTo = $this->_model->redirectUrl;
    }
    elseif ($this->_model->redirectRoute) {
      $redirectTo = $controller->createUrl($this->_model->redirectRoute);
    }

    $controller->redirect($redirectTo);
  }
}
