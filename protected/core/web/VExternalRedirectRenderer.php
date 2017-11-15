<?php
/**
 * ViraCMS External Redirect Renderer Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VExternalRedirectRenderer extends VApplicationComponent
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
   * Perform redirect to specified URL
   */
  public function render()
  {
    $controller = Yii::app()->getController();

    $url = empty($this->_model->redirectUrl) ? $controller->createUrl('/site/index') : $this->_model->redirectUrl;

    $controller->redirect($url);
  }
}
