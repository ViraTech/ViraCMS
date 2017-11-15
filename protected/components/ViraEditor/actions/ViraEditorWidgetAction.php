<?php
/**
 * ViraCMS Static Page Editor Widget Renderer Action Handler
 *
 * @package vira.core.core
 * @subpackage vira.core.editor
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ViraEditorWidgetAction extends CAction
{
  /**
   * Executes the action
   */
  public function run()
  {
    $r = Yii::app()->request;
    $c = $this->getController();

    if (($siteID = $r->getParam('siteID')) !== null) {
      Yii::app()->setSite($siteID);
    }

    if (($languageID = $r->getParam('languageID')) != null) {
      VLanguageHelper::setLanguage($languageID);
    }

    if (($pageID = $r->getParam('pageID')) !== null) {
      $c->setSubject(VPage::model()->findByPk($pageID));
    }

    $widget = $r->getParam('widget');
    $params = $r->getParam('params');

    if (!is_array($params)) {
      $params = array();
    }

    if (($widget = Yii::app()->widgetFactory->getWidgetByClassName($widget)) !== null) {
      $c->widget(implode('.', array($widget[ 'baseAlias' ], $widget[ 'class' ])), $params);
    }
  }
}
