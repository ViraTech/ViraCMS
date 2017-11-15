<?php
/**
 * ViraCMS Static Page Editor Server Files Browsing Action Handler
 *
 * @package vira.core.core
 * @subpackage vira.core.editor
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ViraEditorFileBrowserAction extends CAction
{
  private $_editor;
  private $_functionNumber;
  private $_language;
  private $_params = array();

  /**
   * Executes the action
   */
  public function run()
  {
    Yii::app()->clientScript->registerCssFile(Yii::app()->controller->coreCssUrl('mime'));
    $r = Yii::app()->request;
    $this->_editor = $r->getParam('CKEditor');
    $this->_functionNumber = $r->getParam('CKEditorFuncNum');
    $this->_language = $r->getParam('langCode');
    $this->_params = array(
      'group'      => $r->getParam('group', 'object'),
      'siteID'     => $r->getParam('siteID'),
      'className'  => $r->getParam('className'),
      'primaryKey' => $r->getParam('primaryKey'),
      'siteID'     => $r->getParam('siteID'),
    );
    if (isset($this->_params[ 'primaryKey' ]) && is_array($this->_params[ 'primaryKey' ])) {
      $this->_params[ 'primaryKey' ] = implode(',', $this->_params[ 'primaryKey' ]);
    }

    $model = new VContentFile('search');
    $model->unsetAttributes();
    $model->setAttributes(array(
      'filename' => $r->getParam('name'),
      'siteID'   => $this->_params[ 'siteID' ],
    ));

    switch ($this->_params[ 'group' ]) {
      case 'all':
        break;

      case 'section':
        $model->setAttributes(array(
          'className' => $this->_params[ 'className' ],
        ));
        break;

      case 'object':
      default:
        $model->setAttributes(array(
          'className'  => $this->_params[ 'className' ],
          'primaryKey' => $this->_params[ 'primaryKey' ],
        ));
    }

    $params = array(
      'model'  => $model,
      'params' => $this->_params,
    );

    if ($r->isAjaxRequest) {
      $this->controller->renderPartial(($viewFile = $this->controller->getViewFile('thumbnails')) != false ? $viewFile : 'application.components.ViraEditor.views.browse.file.thumbnails', $params);
      Yii::app()->end();
    }

    $this->registerScripts();

    $this->controller->layout = 'default';
    $this->controller->render(($viewFile = $this->controller->getViewFile('index')) != false ? $viewFile : 'application.components.ViraEditor.views.browse.file.index', $params);
  }

  /**
   * Register necessary user javascript code with ClientScript component
   */
  private function registerScripts()
  {
    $cs = Yii::app()->controller->cs;
    $cs->registerScript(get_class($this) . '_SelectFile', "
$(document).on('click','a.select-file',function(e)
{
	e.preventDefault();
	window.opener.CKEDITOR.tools.callFunction({$this->_functionNumber},$(this).data('src'));
	window.close();
});
");

    $cs->registerScript(get_class($this) . '_ViewType', "
$('#select-block-view,#select-list-view').click(function(e)
{
	var self = $(this),
		data = $('#activate-filter').serialize();
	data.type = self.attr('id');
	$.fn.yiiListView.update('select-file',{
		data: data
	});
});
");

    $cs->registerScript(get_class($this) . '_ActivateFilter', "
$('#activate-filter select').on('change',function(e)
{
	$(this).closest('form').submit();
});
$('#clear-filter').click(function(e)
{
	e.preventDefault();
	var form = $('#activate-filter');
	form.find('input,select').val('');
	form.submit();
});
$('#activate-filter').submit(function(e)
{
	e.preventDefault();
	var form = $(this);
	$.fn.yiiListView.update('select-file',{
		data: form.serialize()
	});
});
");
  }
}
