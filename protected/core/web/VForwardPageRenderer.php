<?php
/**
 * ViraCMS Forward Type Page Renderer Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VForwardPageRenderer extends VApplicationComponent
{
  private $_model;

  /**
   * Class constructor
   * @param VPage $model page
   */
  public function __construct($model)
  {
    $this->_model = $model;
  }

  /**
   * Run forward action
   */
  public function render()
  {
    $controller = Yii::app()->getController();

    if ($this->_model->redirectRoute == 'VPage') {
      if (($page = VPage::model()->findByPk($this->_model->redirectItem)) == null) {
        throw new CHttpException(404);
      }
      $controller->forward('/site/page', true, array('url' => $page->url), $this->_model);
    }
    else {
      $params = array();
      if ($this->_model->redirectParam && $this->_model->redirectValue) {
        $params[$this->_model->redirectParam] = $this->_model->redirectValue;
      }
      $controller->forward($this->_model->redirectRoute, true, $params, $this->_model);
    }
  }
}
