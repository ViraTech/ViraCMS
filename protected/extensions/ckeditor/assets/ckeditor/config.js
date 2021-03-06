/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config)
{
	config.skin = 'moono';
	config.extraPlugins = 'youtube,viramedia,sourcedialog,sharedspace,video,fakeobjects,iframe';

	config.toolbar_Basic =
	[
		[ 'Sourcedialog' ],
		[ 'PasteText','PasteFromWord' ],
    	[ 'Bold', 'Italic', 'Underline', 'Strike', '-' , 'RemoveFormat'],
		[ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ],
		[ 'NumberedList','BulletedList' ],
		[ 'Link', 'Unlink' ],
		[ 'Format' ]
	];

	config.toolbar_Full =
	[
	    { name: 'source',      items : [ 'Sourcedialog' ] },
	    { name: 'do',          items : [ 'Undo','Redo' ] },
	    { name: 'clipboard',   items : [ 'PasteText','PasteFromWord' ] },
	    { name: 'tools',       items : [ 'CreateDiv','Blockquote','ShowBlocks' ] },
	    { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent' ] },
	    { name: 'insert',      items : [ 'Image','viramedia','Video','Flash','Youtube','IFrame','-','Table','HorizontalRule' ] },
	    { name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
	    '/',
	    { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'alignment',   items : [ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
	    { name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
	    { name: 'colors',      items : [ 'TextColor','BGColor' ] }
	];

	config.toolbar = 'Basic';
	config.indentOffset = 5;
	config.removePlugins = 'elementspath';
	config.extraAllowedContent = 'iframe[*];video[*]{*};ul(*);img[*](*){*};map[*](*){*};area[*](*){*}';
	//config.forcePasteAsPlainText = true;

	if (typeof siteInternalPages !== 'undefined') {
		CKEDITOR.on('dialogDefinition',function(ev)
		{
			var dialogName = ev.data.name;
			var dialogDefinition = ev.data.definition;
			if (dialogName === 'link') {
				var infoTab = dialogDefinition.getContents('info');
				var added = false;
				for (var i = 0; i < infoTab.elements.length; i++) {
					if (infoTab.elements[i].id == 'internalPageSelector') {
						added = true;
						break;
					}
				}
				if (!added) {
					infoTab.add({
						type: 'vbox',
						id: 'internalPageSelector',
						children: [{
							type: 'select',
							className: 'ck_select_internal_link',
							id: 'internalPage',
							label: CKEDITOR.lang[CKEDITOR.lang.detect()].common.internalPage || 'Select Site Page',
							items: siteInternalPages,
							setup: function(data)
							{
								this.allowOnChange = false;
								this.setValue(data.url ? data.url.url : '' );
								this.allowOnChange = true;
							},
							onChange: function(ev)
							{
								var dialog = CKEDITOR.dialog.getCurrent();
								dialog.setValueOf('info', 'url', this.getValue());
							},
							width: '100%',
							style: 'width: 100%;'
						}],
						width: '100%'
					});
					dialogDefinition.onFocus = function()
					{
						var urlField = this.getContentElement('info', 'url');
						urlField.select();
					};
				}
			}
		});
	}
};
