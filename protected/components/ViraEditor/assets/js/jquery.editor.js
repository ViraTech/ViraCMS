/**
 * ViraCMS Page Content Editor Interface Component (jquery.editor.js v1.0.0)
 * is a part of ViraCMS Editor Component
 * @url https://github.com/ViraTech/ViraCMS
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 */
(function($)
{
	$.fn.viraEditorInterface = function(f)
	{
		var self = this;
		var options = self.data('options');

		self.init = function(opts)
		{
			options = $.extend({}, $.fn.viraEditorInterface.defaults, opts);
			self.data('options', options);
			options.currentMode = 'disabled';

			$(document).on('click','a,button,input[type=submit]',function(e) {
				if (!$(this).hasClass('.configurable') && !$(this).data('action')) {
					e.preventDefault();
					e.stopPropagation();
					e.stopImmediatePropagation();
					return false;
				}
			});

			$(document).on('click','a[data-action="insert-row"]',self.insert);
			$(document).on('click','a[data-action="insert-block"]',self.insert);
			$(document).on('click','a[data-action="insert-widget"]',self.insert);
			$(document).on('click','a[data-action="remove-row"]',self.delete);
			$(document).on('click','a[data-action="remove-block"]',self.delete);
			$(document).on('click','a[data-action="remove-widget"]',self.delete);
			$(document).on('click','.configurable',self.configure);

			$(document).on('submit','form',function(e){
				e.preventDefault();
				e.stopImmediatePropagation();
				return false;
			});

			return this;
		};

		self.insert = function(e)
		{
			e.preventDefault();
			e.stopPropagation();
			var $this = $(this);
			if ($this.data('action') === 'insert-row') {
				viraEditorApi.selectRowTemplate($this);
			}
			if ($this.data('action') === 'insert-block') {
				$this.closest('*[class*=span]').append($(['<div data-block="new" class="movable"><a href="#" class="vira-editor-delete" data-action="remove-block"></a><div class="', options.classes.blockMask, '"></div></div>'].join('')));
			}
			if ($this.data('action') === 'insert-widget') {
				viraEditorApi.selectWidget($this);
			}
			viraEditorApi.setContentChange(true);
			return false;
		};

		self.configure = function(e)
		{
			e.preventDefault();
			e.stopPropagation();
			var $this = $(this);
			viraEditorApi.configureWidget($this);
			viraEditorApi.setContentChange(true);
		};

		self.delete = function(e)
		{
			e.preventDefault();
			e.stopPropagation();
			var $this = $(this);
			if ($this.data('action') === 'remove-row') {
				viraEditorApi.removeRowConfirm($this);
			}
			if ($this.data('action') === 'remove-block') {
				$this.closest('*[data-block]').remove();
			}
			if ($this.data('action') === 'remove-widget') {
				$this.closest('*[data-widget]').remove();
			}
			viraEditorApi.setContentChange(true);
			return false;
		};

		self.save = function(e,callback)
		{
			e.preventDefault();
			var mode = self.mode();
			self.mode('disable');
			viraEditorApi.setContentChange(false);
			var content = [];
			$('body').find('*[data-area]').each(function()
			{
				var area = $(this);
				var rows = [];

				$('>[class*=container]>*[data-row]',area).each(function()
				{
					var row = $(this);

					var bc = 1;

					var template = row.clone(true,true);
					var blocks = [];

					$(template).find('*[data-content-stub],*[data-block],*[data-widget]').each(function()
					{
						var $this = $(this);
						if ($this.data('content-stub')) {
							$($this,template).replaceWith('###VIRA_CONTENT_STUB###');
						}
						else {
							if ($this.data('block')) {
								blocks.push({
									bc: bc,
									type: 'block',
									orig: $this.data('block'),
									content: $this.html()
								});
							}
							else {
								blocks.push({
									bc: bc,
									type: 'widget',
									orig: $this.data('widget'),
									widget: $this.data('widget-id'),
									config: $this.data('widget-config')
								});
							}
							$($this,template).replaceWith(['###VIRA_BLK_', bc, '_###'].join(''));
							bc++;
						}
					});

					rows.push({
						row: row.data('row'),
						template: template.html(),
						blocks: blocks
					});

					delete template;
				});

				content.push({
					area: area.data('area'),
					type: area.data('area-type'),
					rows: rows
				});
			});

			viraEditorApi.saveSpinner('show');

			$.ajax({
				cache: false,
				url: viraEditorApi.getActionUrl('save'),
				data: {
					pageID: pageID,
					layoutID: layoutID,
					systemID: systemID,
					siteID: siteID,
					languageID: viraEditorApi.getLanguageID(),
					data: content
				},
				dataType: 'json',
				type: 'post',
				complete: function()
				{
					viraEditorApi.saveSpinner('hide');
				},
				success: function(jdata, textStatus, jqXHR)
				{
					viraEditorApi.alert('success', 'save');
					viraEditorApi.setContentChange(false);
					if (typeof callback == 'function') {
						callback();
					}
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					viraEditorApi.alert('error', 'save', jqXHR.responseText);
				}
			});

			self.mode(mode);
		};

		self.destroy = function()
		{
			self.mode('disable');

			return this;
		};

		self.mode = function(mode)
		{
			if (typeof mode === 'undefined') {
				return options.currentMode;
			}

			var rows = {
				disable: function()
				{
					$('body').removeClass('vira-editor-enabled');
					$(self).designer('destroy');
					$(['.', options.classes.rowMask].join(''), self).each(function()
					{
						$(this).replaceWith($(this).html());
					});
					$(options.selectors.row, self).
							removeClass('movable').
							find('a[data-action]').remove();
				},
				move: function()
				{
					$('body').addClass('vira-editor-enabled');
					$(options.selectors.row, self).each(function()
					{
						var $this = $(this);
						$this.addClass('movable');
						$this.wrapInner(['<div class="', options.classes.rowMask, '"></div>'].join(''));
						$this.append($('<a href="#" class="vira-editor-insert" data-action="insert-row"></a>'));
						$this.append($('<a href="#" class="vira-editor-delete" data-action="remove-row"></a>'));
					});
					$(self).designer({
						dropTo: options.selectors.dropRow
					});
				}
			};

			var blocks = {
				disable: function()
				{
					$(self).designer('destroy');
					$(['.', options.classes.blockMask, ',.', options.classes.widgetMask, ',.', options.classes.configMask].join(''), self).each(function()
					{
						var $this = $(this);
						$this.find('a[data-action]').remove();
						$this.replaceWith($this.html());
					});
					$(options.selectors.dropBlock, self).
							removeClass(options.classes.highlightSpan).
							find('a[data-action]').
							remove();
					$([options.selectors.block, options.selectors.widget].join(','), self).
							removeClass('movable editable configurable').
							attr('contentEditable', 'false');
					$('body').removeClass('vira-editor-enabled').css('margin-top', 0);
					$('#top').remove();
					if (typeof CKEDITOR !== 'undefined') {
						for (i in CKEDITOR.instances) {
							CKEDITOR.instances[i].destroy();
						}
						CKEDITOR.removeListener('instanceReady');
						$('body').off('resize.CKEDITOR');
					}
				},
				move: function()
				{
					blocks.disable();
					$('body').addClass('vira-editor-enabled');
					$(options.selectors.block,self).each(function()
					{
						var $this = $(this);
						$this.addClass('movable').wrapInner(['<div class="', options.classes.blockMask, '"></div>'].join(''));
						$this.prepend($('<a href="#" class="vira-editor-delete" data-action="remove-block"></a>'));
					});
					$(options.selectors.stub,self).each(function()
					{
						$(this).addClass('movable').wrapInner(['<div class="', options.classes.blockMask, '"></div>'].join(''));
					});
					$(options.selectors.widget,self).each(function()
					{
						var $this = $(this);
						$this.addClass('movable').wrapInner(['<div class="', options.classes.widgetMask, '"></div>'].join(''));
						$this.prepend($('<a href="#" class="vira-editor-delete" data-action="remove-widget"></a>'));
					});
					$(options.selectors.dropBlock,self).each(function()
					{
						var $this = $(this);
						$this.addClass(options.classes.highlightSpan);
						$this.prepend($('<a href="#" class="vira-editor-insert" data-action="insert-block"></a>'));
						$this.prepend($('<a href="#" class="vira-editor-widget" data-action="insert-widget"></a>'));
					});
					$(self).designer({
						dropTo: options.selectors.dropBlock,
						onAfterDrag: function(e, el)
						{
							viraEditorApi.setContentChange(true);
						}
					});
				},
				edit: function()
				{
					blocks.disable();
					$('body').addClass('vira-editor-enabled').prepend($('<div id="top" class="container"></div>').css({position: 'fixed', top: 0, left: 0, right: 0, zIndex: 2000, overflow: 'visible'}));
					$(options.selectors.block, self).each(function()
					{
						var $this = $(this);
						$this.addClass('editable');
						$this.attr('contentEditable', 'true');
						$this.on('keyup change paste', function(e)
						{
							viraEditorApi.setContentChange(true);
						});
						if (typeof CKEDITOR !== 'undefined') {
							CKEDITOR.inline(this, {
								filebrowserBrowseUrl: actionUrl.fileBrowse,
								filebrowserImageBrowseUrl: actionUrl.imageBrowse,
								filebrowserFlashBrowseUrl: actionUrl.flashBrowse,
								filebrowserVideoBrowseUrl: actionUrl.videoBrowse,
								toolbar: 'Full',
								sharedSpaces: {
									top: 'top',
									bottom: 'bottom'
								}
							});
						}
					});
					$(options.selectors.widget, self).each(function()
					{
						var $this = $(this);
						$this.addClass('configurable');
						$this.wrapInner(['<div class="', options.classes.configMask, '"></div>'].join(''));
					});
					if (typeof CKEDITOR !== 'undefined') {
						CKEDITOR.on('instanceReady', function(e)
						{
							var resizeTop = function(e) {
								$('body').css('margin-top', $('#top').height() + 'px');
							};
							$(window).on('resize.CKEDITOR', resizeTop);
							resizeTop();
						});
					}
				}
			};

			if (options.currentMode === mode) {
				return;
			}

			if (mode === 'edit') {
				rows.disable();
				blocks.edit();
			}
			else if (mode === 'block') {
				rows.disable();
				blocks.move();
			}
			else if (mode === 'row') {
				blocks.disable();
				rows.move();
			}
			else {
				blocks.disable();
				rows.disable();
			}

			options.currentMode = mode;
		};

		if (self[f]) {
			return self[f].apply(this, Array.prototype.slice.call(arguments, 1));
		}
		else if (typeof f === 'object' || !f) {
			return self.init.apply(this, arguments);
		}
		else {
			$.error('Invalid usage of jQuery Editor Interface plugin.');
		}
	};

	$.fn.viraEditorInterface.defaults = {
		selectors: {
			row: '*[data-area] > *[class*=container] > *[data-row]',
			block: '*[data-area] > *[class*=container] > *[data-row] > *[class*=row] > *[class*=span] > *[data-block]',
			stub: '*[data-area] > *[class*=container] > *[data-row] > *[class*=row] > *[class*=span] > *[data-content-stub]',
			widget: '*[data-area] > *[class*=container] > *[data-row] > *[class*=row] > *[class*=span] > *[data-widget]',
			dropRow: '*[data-area] > *[class*=container]',
			dropBlock: '*[data-area] > *[class*=container] > *[data-row] > *[class*=row] > *[class*=span]'
		},
		classes: {
			blockMask: 'vira-editor-blk-mask',
			rowMask: 'vira-editor-row-mask',
			widgetMask: 'vira-editor-widget-mask',
			configMask: 'vira-editor-config-mask',
			highlightSpan: 'vira-editor-span-mask'
		}
	};
})(jQuery);