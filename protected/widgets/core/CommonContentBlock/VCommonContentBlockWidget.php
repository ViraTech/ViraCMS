<?php
/**
 * ViraCMS Shared Content Block Widget
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VCommonContentBlockWidget extends VWidget
{
  public $contentID;
  private $_model;

  public function run()
  {
    $this->render('common-content-block');
  }

  public function getModel()
  {
    if ($this->_model === null) {
      $this->_model = VContentCommon::model()->findByPk($this->contentID);
    }

    return $this->_model;
  }

  public function getContent()
  {
    return $this->model ? $this->model->content : '';
  }

  public function registerAttached()
  {
    if ($this->model !== null) {
      if ($this->model->script) {
        $this->registerScript($this->model->script);
      }
      if ($this->model->style) {
        Yii::app()->getClientScript()->registerCss('Vira.Shared.Style#' . $this->id, $model->style);
      }
    }
  }

  public function getCacheKey()
  {
    return 'Vira.Shared.' . $this->contentID;
  }

  public function getParamsModel()
  {
    Yii::import($this->baseAlias . '.CommonContentBlock.forms.CommonContentBlockWidgetParams');
    return new CommonContentBlockWidgetParams();
  }

  public function getConfigView()
  {
    return $this->baseAlias . '.CommonContentBlock.views.configure';
  }
}
