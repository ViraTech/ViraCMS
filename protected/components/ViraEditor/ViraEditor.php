<?php
/**
 * ViraCMS Static Page Editor Component
 *
 * @package vira.core.core
 * @subpackage vira.core.editor
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ViraEditor extends VApplicationComponent
{
  const FEATURE_DEFAULT = 'default';
  const FEATURE_EDITOR = 'editor';
  const FEATURE_DESIGNER = 'designer';

  /**
   * @var boolean is editor embedded on the page
   */
  private $_embedded = false;

  /**
   * @var string features enabled
   */
  private $_features;

  /**
   * @var string update (save) page action route
   */
  public $updateAction = '/admin/content/editor/save';

  /**
   * @var string update (save) page action class path
   */
  public $updateActionClass = 'application.components.ViraEditor.actions.ViraEditorSaveAction';

  /**
   * @var string image browser action route
   */
  public $imageBrowserAction = '/admin/content/editor/image';

  /**
   * @var string image browser action class path
   */
  public $imageBrowserActionClass = 'application.components.ViraEditor.actions.ViraEditorImageBrowserAction';

  /**
   * @var string flash apps action route
   */
  public $flashBrowserAction = '/admin/content/editor/flash';

  /**
   * @var string flash apps action class path
   */
  public $flashBrowserActionClass = 'application.components.ViraEditor.actions.ViraEditorFlashBrowserAction';

  /**
   * @var string video browser action route
   */
  public $videoBrowserAction = '/admin/content/editor/video';

  /**
   * @var string video browser action class path
   */
  public $videoBrowserActionClass = 'application.components.ViraEditor.actions.ViraEditorVideoBrowserAction';

  /**
   * @var string file browser action route
   */
  public $fileBrowserAction = '/admin/content/editor/file';

  /**
   * @var string file browser action class path
   */
  public $fileBrowserActionClass = 'application.components.ViraEditor.actions.ViraEditorFileBrowserAction';

  /**
   * @var string widget renderer action route
   */
  public $widgetAction = '/admin/content/editor/widget';

  /**
   * @var string widget renderer action class path
   */
  public $widgetActionClass = 'application.components.ViraEditor.actions.ViraEditorWidgetAction';

  /**
   * @var string widget configirator action route
   */
  public $configureAction = '/admin/content/editor/configure';

  /**
   * @var string widget configurator action class path
   */
  public $configureActionClass = 'application.components.ViraEditor.actions.ViraEditorConfigureAction';

  /**
   * @var string page renderer class path
   */
  public $pageRendererClass = 'application.components.ViraEditor.ViraEditorRenderer';

  /**
   * @var array browser action additional parameters
   */
  public $serverBrowsingParams = array();

  /**
   * @var string site identifier
   */
  private $_siteID;

  /**
   * @var string page identifier
   */
  private $_pageID;

  /**
   * @var string layout identifier
   */
  private $_layoutID;

  /**
   * @var string system page identifier
   */
  private $_systemID;

  /**
   * @var VPage|VSiteLayout|VSystemPage
   */
  private $_model;

  /**
   * @var VController contextual controller
   */
  private $_context;

  /**
   * @var string external link to published assets
   */
  private $assetsUrl;

  /**
   * @var string page update (save) URL generated from action route
   */
  private $updateUrl;

  /**
   * @var string image browser URL generated from action route
   */
  private $imageBrowserUrl;

  /**
   * @var string flash apps browser URL generated from action route
   */
  private $flashBrowserUrl;

  /**
   * @var string video files browser URL generated from action route
   */
  private $videoBrowserUrl;

  /**
   * @var string files browser URL generated from action route
   */
  private $fileBrowserUrl;

  /**
   * @var string widget renderer URL generated from action route
   */
  private $widgetUrl;

  /**
   * @var string widget configurator URL generated from action route
   */
  private $configureUrl;

  /**
   * @var array site pages
   */
  private $internalPages;

  /**
   * @var CClientScript
   */
  private $_cs;

  /**
   * @var mixed the page renderer component
   */
  private $_renderer;

  /**
   * The editor initialization
   */
  public function init()
  {
    $this->_context = Yii::app()->getController();
    $this->_cs = Yii::app()->getClientScript();
    $this->assetsUrl = Yii::app()->assetManager->publish($this->getBasePath() . DIRECTORY_SEPARATOR . 'assets');
  }

  /**
   * Creates and initializes the editor's page renderer component
   * @param mixed $subject the editing subject
   * @param string $siteID the site identifier (optional)
   * @param string $languageID the language identifier (optional)
   * @return mixed
   */
  public function createRenderer($subject, $siteID = null, $languageID = null)
  {
    Yii::import($this->pageRendererClass);

    $className = explode('.', $this->pageRendererClass);
    $className = array_pop($className);

    $this->_renderer = new $className($subject, $siteID, $languageID);

    return $this->_renderer;
  }

  /**
   * Returns the editor's page renderer component
   * @return mixed
   */
  public function getRenderer()
  {
    return $this->_renderer;
  }

  /**
   * Embed editor to the page
   * @param VPage $model page
   */
  public function embed($model = null)
  {
    if ($model) {
      $this->setModel($model);
    }

    $this->serverBrowsingParams[ 'siteID' ] = $this->_siteID;
    $this->serverBrowsingParams[ 'className' ] = $this->getModelClass();
    $this->serverBrowsingParams[ 'primaryKey' ] = $this->getModelPk();

    $this->updateUrl = $this->_context->createUrl($this->updateAction);
    $this->imageBrowserUrl = $this->_context->createUrl($this->imageBrowserAction, $this->serverBrowsingParams);
    $this->videoBrowserUrl = $this->_context->createUrl($this->videoBrowserAction, $this->serverBrowsingParams);
    $this->flashBrowserUrl = $this->_context->createUrl($this->flashBrowserAction, $this->serverBrowsingParams);
    $this->fileBrowserUrl = $this->_context->createUrl($this->fileBrowserAction, $this->serverBrowsingParams);
    $this->widgetUrl = $this->_context->createUrl($this->widgetAction);
    $this->configureUrl = $this->_context->createUrl($this->configureAction);
    $this->internalPages = CJavaScript::encode($this->prepareLinks());

    $this->registerEmbedAssets();

    $this->_embedded = true;
  }

  /**
   * Checks if editor has been embedded
   * @return boolean
   */
  public function getIsEmbedded()
  {
    return $this->_embedded;
  }

  /**
   * Set page model
   * @param VPage $model page
   */
  public function setModel($model)
  {
    $this->_model = $model;
    $this->_siteID = $model->siteID;

    $this->_features = array(self::FEATURE_DEFAULT);

    if ($model instanceof VPage) {
      $this->_pageID = $model->id;
      $this->_features[] = self::FEATURE_EDITOR;
      $this->_features[] = self::FEATURE_DESIGNER;
    }

    if ($model instanceof VSiteLayout) {
      $this->_layoutID = $model->id;
      $this->_features[] = self::FEATURE_EDITOR;
      $this->_features[] = self::FEATURE_DESIGNER;
    }

    if ($model instanceof VSystemPage) {
      $this->_systemID = $model->id;
      $this->_features[] = self::FEATURE_EDITOR;
    }

    $this->_features = implode('|', $this->_features);
  }

  /**
   * Return page model if any set, null otherwise
   * @return mixed
   */
  public function getModel()
  {
    return $this->_model;
  }

  /**
   * Return model class name
   * @return string
   */
  public function getModelClass()
  {
    return is_object($this->_model) ? get_class($this->_model) : 'Internal';
  }

  /**
   * Return model primary key (can be array for complex PKs)
   * @return mixed
   */
  public function getModelPk()
  {
    return is_a($this->_model, 'VActiveRecord') ? $this->_model->getPrimaryKey() : Yii::app()->user->id;
  }

  /**
   * Render editor control buttons
   * @param VPage $model
   */
  public function renderControls($model)
  {
    $this->_context->renderFile($this->getViewPath() . DIRECTORY_SEPARATOR . 'controls.php', array(
      'model' => $model,
    ));
    $this->registerControlScripts($model);
  }

  /**
   * Register editor control javascript
   */
  public function registerControlScripts($model)
  {
    $this->_cs->registerScriptFile($this->assetsUrl . '/js/jquery.confirm.js', CClientScript::POS_END);
    $this->_cs->registerScript(get_class($this) . '#Init', "
$('#iframe-wrapper iframe').on('load',function()
{
	var iframe = $(this)[0],
		contentUpdated = false;

	var leavePage = function(e)
	{
		if (!e) {
			e = window.event;
		}

		e.preventDefault();
		e.stopPropagation();
		e.cancelBubble = true;

		return e.returnValue = '" . Yii::t('vira_editor', 'You have unsaved data. Leaving this page will discard all changes.') . "';
	};

	viraEditorApi = this.contentWindow.viraEditorApi;
	viraEditorApi.getContentChange = function()
	{
		return contentUpdated;
	};
	viraEditorApi.setContentChange = function(flag)
	{
		contentUpdated = !!flag;
		window.onbeforeunload = contentUpdated ? leavePage : null;
	};
	viraEditorApi.selectRowTemplate = function(context)
	{
		$('#vira-editor-select-row-template a[data-template]').on('click',function(e)
		{
			e.preventDefault();
			context.closest('*[data-row]').after($([ '<div data-row=\"new-row\" class=\"movable\"><div class=\"vira-editor-row-mask\">', $(this).data('template'), '</div><a href=\"#\" class=\"vira-editor-insert\" data-action=\"insert-row\"></a><a href=\"#\" class=\"vira-editor-delete\" data-action=\"remove-row\"></a></div>' ].join('')));
			$('#vira-editor-select-row-template').modal('hide');
			$('#vira-editor-select-row-template a[data-template]').off('click');
		});
		$('#vira-editor-select-row-template').modal('show');
	};
	viraEditorApi.removeRowConfirm = function(context)
	{
		var modal = viraCoreConfirm('" . Yii::t('vira_editor', 'Row Delete Confirmation') . "','" . Yii::t('vira_editor', 'Are you sure to delete row?') . "',function()
		{
			var row = context.closest('*[data-row]'),
				area = context.closest('*[data-area]'),
				stub = row.find('*[data-content-stub]').detach();
			if (area.find('>[class*=container]>*[data-row]').length > 1) {
				row.remove();
			}
			else {
				row.find('*[class*=row]>*[class*=span]').empty();
			}
			area.find('>[class*=container]>*[data-row] *[class*=row] [class*=span]:first').append(stub);
			modal.modal('hide');
		},null,{ ok: '" . Yii::t('common', 'Yes') . "', cancel: '" . Yii::t('common', 'No') . "' });
	};
	viraEditorApi.selectWidget = function(context)
	{
		$('#vira-editor-select-widget a[data-widget-id]').on('click',function(e)
		{
			e.preventDefault();
			var widget = $([ '<div data-widget=\"new-widget\" data-widget-id=\"', $(this).data('widget-id'), '\" data-widget-config=\"\" class=\"movable\"><a href=\"#\" class=\"vira-editor-delete\" data-action=\"remove-widget\"></a><div class=\"vira-editor-widget-mask\"></div></div>' ].join(''));
			widget.appendTo(context.closest('*[class*=span]'));
			viraEditorApi.renderWidget(widget);
			$('#vira-editor-select-widget').modal('hide');
			$('#vira-editor-select-widget a[data-widget-id]').off('click');
		});
		$('#vira-editor-select-widget').modal('show');
	};
	viraEditorApi.renderWidget = function(widget,callback)
	{
		$.ajax({
			cache: false,
			url: viraEditorApi.getActionUrl('widget'),
			data: { siteID: siteID, languageID: viraEditorApi.getLanguageID(), widget: widget.data('widget-id'), params: widget.data('widget-config') },
			type: 'post',
			success: function(data,textStatus,jqXHR)
			{
				widget.find('.vira-editor-config-mask:first,.vira-editor-widget-mask:first').html(data);
				if (typeof callback == 'function') {
					callback(widget);
				}
			},
			error: function(jqXHR,textStatus,errorThrown)
			{
				widget.remove();
				viraCoreAlert('error','" . Yii::t('vira_editor', 'An error occurred while processing widget.') . "','middle');
			}
		});
	};
	viraEditorApi.formatParams = function(config,prefix)
	{
		var params = [],
			prefix = typeof prefix !== 'undefined' ? prefix : '';

		if (typeof config !== 'undefined') {
			for (var i in config) {
				if (typeof config[i] === 'object') {
					params = params.concat(viraEditorApi.formatParams(config[i],prefix + '[' + i + ']'));
				}
				else {
					params.push({
						name: 'params' + prefix + '[' + i + ']',
						value: config[i]
					});
				}
			}
		}

		return params;
	};
	viraEditorApi.configureWidget = function(widget,callback)
	{
		var url = viraEditorApi.getActionUrl('configure');
		url += url.indexOf('?') != -1 ? '&' : '?';
		url += 'widget=' + encodeURI(widget.data('widget-id'));
		url += '&siteID=' + encodeURI(siteID);

		var modal = $('#vira-editor-widget-config');

		$.ajax({
			cache: false,
			url: url,
			type: 'post',
			dataType: 'text',
			data: viraEditorApi.formatParams(widget.data('widget-config')),
			success: function(data,textStatus,jqXHR)
			{
				$('.modal-body',modal).html(data);
				modal.modal('show');

				$('[data-action=submit]',modal).on('click',function(e)
				{
					$.ajax({
						cache: false,
						url: url,
						type: 'post',
						dataType: 'json',
						data: $('form',modal).serialize(),
						success: function(jdata,textStatus,jqXHR)
						{
							if (jdata.status == 'ok') {
								widget.data('widget-config',jdata.params);
								modal.modal('hide');
								viraEditorApi.renderWidget(widget);
								$('[data-action=submit]',modal).off('click');
							}
							else {
								$('.modal-body',modal).html(jdata.form);
							}
						},
						error: function(jqXHR,textStatus,errorThrown)
						{
							modal.modal('hide');
							viraCoreAlert('error','" . Yii::t('vira_editor', 'An error occurred while widget configuring.') . "','middle');
						}
					});
				});
			},
			error: function(jqXHR,textStatus,errorThrown)
			{
				modal.modal('hide');
				viraCoreAlert('error','" . Yii::t('vira_editor', 'An error occurred while widget configuring.') . "','middle');
			}
		});
	};
	viraEditorApi.spinner = null;
	viraEditorApi.saveSpinner = function(mode)
	{
		if (mode == 'show') {
			viraEditorApi.spinner = viraCoreLoading('" . Yii::t('common', 'Saving...') . "');
		}
		else {
			 if (viraEditorApi.spinner !== null) {
				viraEditorApi.spinner.modal('hide');
				viraEditorApi.spinner = null;
			 }
		}
	};
	viraEditorApi.alert = function(type,category,errorMessage)
	{
		if (category === 'save') {
			if (type === 'success') {
				var message = '" . $this->getShortMessage($model) . "';
			}
			else if (type === 'error') {
				var message =  typeof errorMessage != 'undefined' ? errorMessage : '" . Yii::t('vira_editor', 'An error occurred while page content being updated.') . "';
			}
		}

		if (typeof message !== 'undefined') {
			viraCoreAlert(type,message,'middleCenter',type == 'error' ? { autoClose: false } : {});
		}
	}
	viraEditorApi.changeLanguage = function(language)
	{
		var url = iframe.src;
		url = url.replace(/(lng(=|\/))\w+/,'$1' + encodeURIComponent(language));
		iframe.src = url;
	};
	viraEditorApi.getLanguageID = function()
	{
		var currentLang = $('#vira-editor-language-select > a:eq(0)');
		return currentLang ? currentLang.data('lang-id') : " . Yii::app()->getLanguage() . ";
	};
	$('#vira-editor-mode-select a').each(function()
	{
		var button = $(this),
			set = function(e)
			{
				e.preventDefault();
				viraEditorApi.setMode($(this).data('mode'));
			},
			disable = function(e)
			{
				e.preventDefault();
			};

		if (viraEditorApi.checkFeature(button.data('feature'))) {
			button.removeClass('disabled');
			button.on('click',set);
		}
		else {
			button.on('click',disable);
		}
	});
	$('#vira-editor-width-select a').removeClass('disabled').on('click',function(e)
	{
		$('#iframe-wrapper').css('width',$(this).data('width'));
	});
	$('#vira-editor-language-select > a:eq(0)').removeClass('disabled').next('ul').find('a').on('click',function(e)
	{
		e.preventDefault();
		var currentLang = $('#vira-editor-language-select > a:eq(0)');
		if (currentLang.data('lang') !== $(this).data('lang')) {
			currentLang.find('span').text($(this).text());
			currentLang.data('lang',$(this).data('lang'));
			currentLang.data('lang-id',$(this).data('lang-id'));
			viraEditorApi.changeLanguage($(this).data('lang'));
		}
	});
	$('#vira-editor-save-contents').removeClass('disabled').off('click').on('click',function(e)
	{
		viraEditorApi.save(e,function()
		{
			var returnUrl = viraEditorApi.getReturnUrl();
			if (returnUrl) {
				setTimeout(function()
				{
					document.location = returnUrl;
				},300);
			}
		});
	});
	$('#vira-editor-cancel-update').removeClass('disabled').off('click').viraConfirm({
		enabled: viraEditorApi.getContentChange,
		callbacks: {
			ok: function()
			{
				window.onbeforeunload = null;
				document.location = $(this).attr('href');
			}
		},
		locale: {
			title: '" . Yii::t('vira_editor', 'Cancel Updates') . "',
			message: '" . Yii::t('vira_editor', 'Are you sure to cancel all updates on this page?') . "',
			buttons: {
				ok: '" . Yii::t('common', 'OK') . "',
				cancel: '" . Yii::t('common', 'Cancel') . "'
			}
		}
	});
});
");
  }

  /**
   * Register necessary assets for editor embedding
   */
  public function registerEmbedAssets()
  {
    $this->_context->widget('ext.ckeditor.ECKEditor', array('onlyInit' => true));
    $this->registerEmbedStyles();
    $this->registerEmbedScripts();
  }

  /**
   * Register editor embedding stylesheets
   */
  private function registerEmbedStyles()
  {
    $this->_cs->registerCssFile($this->assetsUrl . '/css/style.css');
  }

  /**
   * Register editor embedding javascript
   */
  public function registerEmbedScripts()
  {
    $this->_cs->registerCoreScript('jquery');
    $this->_cs->registerScriptFile($this->assetsUrl . '/js/jquery.designer.js', CClientScript::POS_END);
    $this->_cs->registerScriptFile($this->assetsUrl . '/js/jquery.editor.js', CClientScript::POS_END);
    $this->_cs->registerScript(get_class($this) . '#Init', "
$('body').viraEditorInterface();
viraEditorApi.setMode = function(mode) { $('body').viraEditorInterface('mode',mode); };
viraEditorApi.getMode = function() { return $('body').viraEditorInterface('mode'); };
viraEditorApi.checkFeature = function(value)
{
	return '{$this->_features}'.indexOf(value) != -1;
};
viraEditorApi.getReturnUrl = function()
{
	return '{$this->getReturnUrl($this->_model)}';
};
viraEditorApi.getActionUrl = function(v)
{
	if (typeof actionUrl[v] !== 'undefined') {
		return actionUrl[v];
	}

	return '';
};
viraEditorApi.save = function(e,callback)
{
	$('body').viraEditorInterface('save',e,callback);
};
");
    $this->_cs->registerScript(get_class($this) . '.' . 'Variables', "
var viraEditorApi = {},
	siteID = '{$this->_siteID}',
	pageID = '{$this->_pageID}',
	layoutID = '{$this->_layoutID}',
	systemID = '{$this->_systemID}',
	actionUrl = {
		save: '{$this->updateUrl}',
		imageBrowse: '{$this->imageBrowserUrl}',
		videoBrowse: '{$this->videoBrowserUrl}',
		flashBrowse: '{$this->flashBrowserUrl}',
		fileBrowse: '{$this->fileBrowserUrl}',
		widget: '{$this->widgetUrl}',
		configure: '{$this->configureUrl}',
		videoPlayback: '" . Yii::app()->videoPlayer->getUrl('_VIDEO_', '_IMAGE_', '_WIDTH_', '_HEIGHT_') . "'
	},
	siteInternalPages = {$this->internalPages};
", CClientScript::POS_END);
  }

  /**
   * Return path to views directory
   * @return string
   */
  public function getViewPath()
  {
    return $this->getBasePath() . DIRECTORY_SEPARATOR . 'views';
  }

  /**
   * Return class file base path
   * @return string
   */
  private function getBasePath()
  {
    $reflector = new ReflectionClass(get_class($this));
    return dirname($reflector->getFileName());
  }

  /**
   * Return internal image URL
   * @param string $image image name (with extension)
   * @return string
   */
  public function getImageUrl($image)
  {
    return $this->assetsUrl . '/img/' . $image;
  }

  /**
   * Prepare and return links to site pages
   * @param array $links links generated by siteMap component
   * @param integer $level deep level (used in recursion)
   * @return array
   */
  private function prepareLinks($links = null, $level = 0)
  {
    if ($links === null) {
      $links = Yii::app()->siteMap->getMenu(Yii::app()->site->id);
    }
    if ($level == 0) {
      $return = array(
        array('', ''),
      );
    }
    else {
      $return = array();
    }
    foreach ($links as $link) {
      $inner = array();
      if (isset($link[ 'items' ]) && is_array($link[ 'items' ]) && count($link[ 'items' ])) {
        $inner = $this->prepareLinks($link[ 'items' ], $level + 1);
      }
      $return[] = array(
        str_pad($link[ 'label' ], $level * 3 + strlen($link[ 'label' ]), ' ', STR_PAD_LEFT),
        $link[ 'url' ],
      );
      if (count($inner)) {
        foreach ($inner as $innerLink) {
          $return[] = $innerLink;
        }
      }
    }

    return $return;
  }

  /**
   * Returns the success message text
   * @param mixed $model the model
   * @return string
   */
  private function getShortMessage($model)
  {
    if ($model instanceof VPage) {
      return Yii::t('vira_editor', 'Page content has been updated.');
    }

    if ($model instanceof VSiteLayout) {
      return Yii::t('vira_editor', 'Layout content has been updated.');
    }

    if ($model instanceof VSystemPage) {
      return Yii::t('vira_editor', 'System page content has been updated.');
    }
  }

  /**
   * Returns the URL for redirect to
   * @param mixed $model the model
   * @return string
   */
  private function getReturnUrl($model)
  {
    if ($model instanceof VPage) {
      $route = '/admin/content/page/index';
    }

    if ($model instanceof VSiteLayout) {
      $route = '/admin/content/appearance/layout/index';
    }

    if ($model instanceof VSystemPage) {
      $route = '/admin/config/system/index';
    }

    return isset($route) ? Yii::app()->createAbsoluteUrl($route) : null;
  }
}
