<?php
/**
 * ViraCMS Static Page Editor Widget Configuration Action Handler
 *
 * @package vira.core.core
 * @subpackage vira.core.editor
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ViraEditorConfigureAction extends CAction
{
  /**
   * Executes the action
   */
  public function run()
  {
    $r = Yii::app()->request;

    if (($siteID = $r->getParam('siteID')) !== null) {
      Yii::app()->setSite($siteID);
    }

    $name = $r->getParam('widget');
    $config = Yii::app()->widgetFactory->getWidgetByClassName($name);

    if ($config !== null) {
      Yii::import(implode('.', array($config[ 'baseAlias' ], $config[ 'class' ])));

      $params = $r->getParam('params', array());
      if (empty($params)) {
        $block = VPageBlock::model()->findByPk($r->getParam('id'));

        if ($block !== null) {
          $params = @unserialize($block->content);
        }
      }

      $widget = new $name;
      if (!empty($widget->paramsModel)) {
        $model = $widget->paramsModel;
        $model->attributes = $params;

        if ($r->getParam('update')) {
          $model->attributes = $r->getParam(get_class($model), array());
          $json = array(
            'status' => 'ok',
          );

          if (!$model->validate()) {
            $json[ 'status' ] = 'error';
            $json[ 'form' ] = $this->render($widget->configView, $model, true);
          }
          else {
            $json[ 'params' ] = $model->attributes;
            $json[ 'params' ] = $json[ 'params' ];
          }

          echo CJSON::encode($json);

          Yii::app()->end();
        }

        $this->render($widget->configView, $model);
      }
      else {
        if ($r->getParam('update')) {
          $json = array(
            'status' => 'ok',
            'params' => null,
          );

          echo CJSON::encode($json);

          Yii::app()->end();
        }
        else {
          $this->controller->renderFile(Yii::app()->editor->getViewPath() . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . 'none.php', array());
        }
      }
    }
  }

  /**
   * Render widget configuration form
   * @param string $view view name
   * @param mixed $model widget configuration form
   * @param boolean $return return rendered content or send it to the output
   * @return mixed
   */
  public function render($view, $model, $return = false)
  {
    $file = implode(DIRECTORY_SEPARATOR, array(
      Yii::app()->editor->getViewPath(),
      'widget',
      'configure.php'
    ));

    return $this->controller->renderFile($file, array(
        'view'  => $view,
        'model' => $model,
        ), $return);
  }
}
