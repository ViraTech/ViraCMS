/**
 * ViraCMS internal media plugin
 *
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 */
(function()
{
	CKEDITOR.plugins.add('viramedia', {
		lang: [ 'en', 'ru' ],
		init: function(editor)
		{
			editor.addCommand('viramedia',new CKEDITOR.dialogCommand('viramedia'));

			editor.ui.addButton('viramedia',
			{
				label: editor.lang.viramedia.button,
				toolbar: 'insert',
				command: 'viramedia',
				icon: this.path + 'img/icon.png'
			});

			CKEDITOR.dialog.add('viramedia',function(instance)
			{
				return {
					title: editor.lang.viramedia.title,
					minWidth: 400,
					minHeight: 100,
					contents: [{
						id: 'viramediaPlugin',
						expand: true,
						elements: [{
							type: 'hbox',
							widths: [ '70%', '30%' ],
							children: [{
								id: 'videoUrl',
								type: 'text',
								label: editor.lang.viramedia.txtVideoUrl,
								validate: function()
								{
									if (!this.getValue()) {
										alert( editor.lang.viramedia.emptyUrl );
										return false;
									}
								}
							},
							{
								type: 'button',
								id: 'browse',
								hidden: 'true',
								style: 'display:inline-block;margin-top:13px;',
								filebrowser: {
									action: 'Browse',
									target: 'viramediaPlugin:videoUrl',
									url: editor.config.filebrowserVideoBrowseUrl || editor.config.filebrowserBrowseUrl,
									params: {
										ext: 'flv,mp4'
									}
								},
								label : editor.lang.common.browseServer
							}]
						},
						{
							type: 'hbox',
							widths: [ '70%', '30%' ],
							children: [{
								id: 'imageUrl',
								type: 'text',
								label: editor.lang.viramedia.txtImageUrl,
							},
							{
								type: 'button',
								id: 'browse',
								hidden: 'true',
								style: 'display:inline-block;margin-top:13px;',
								filebrowser: {
									action: 'Browse',
									target: 'viramediaPlugin:imageUrl',
									url: editor.config.filebrowserImageBrowseUrl || editor.config.filebrowserBrowseUrl,
								},
								label : editor.lang.common.browseServer
							}]
						},
						{
							type: 'hbox',
							widths: [ '50%', '50%' ],
							children: [{
								type: 'text',
								id: 'txtWidth',
								width: '90%',
								label: editor.lang.viramedia.txtWidth,
								'default': '468',
								validate : function()
								{
									if (this.getValue()) {
										var width = parseInt(this.getValue()) || 0;

										if (width === 0) {
											alert(editor.lang.viramedia.invalidWidth);
											return false;
										}
									}
									else {
										alert(editor.lang.viramedia.noWidth);
										return false;
									}
								}
							},
							{
								type: 'text',
								id: 'txtHeight',
								width: '90%',
								label: editor.lang.viramedia.txtHeight,
								'default': '328',
								validate: function()
								{
									if (this.getValue()) {
										var height = parseInt(this.getValue()) || 0;

										if (height === 0) {
											alert(editor.lang.viramedia.invalidHeight);
											return false;
										}
									}
									else {
										alert(editor.lang.viramedia.noHeight);
										return false;
									}
								}
							}]
						}]
					}],
					onOk: function()
					{
						var videoUrl = this.getValueOf('viramediaPlugin','videoUrl'),
							imageUrl = this.getValueOf('viramediaPlugin','imageUrl'),
							width = this.getValueOf('viramediaPlugin','txtWidth'),
							height = this.getValueOf('viramediaPlugin','txtHeight'),
							url = typeof actionUrl.videoPlayback != 'undefined' ? actionUrl.videoPlayback : '/media/video/?v=_VIDEO_&i=_IMAGE_&w=_WIDTH_&h=_HEIGHT_';

							url = url.replace('_VIDEO_',encodeURIComponent(videoUrl));
							url = url.replace('_IMAGE_',encodeURIComponent(imageUrl));
							url = url.replace('_WIDTH_',encodeURIComponent(width));
							url = url.replace('_HEIGHT_',encodeURIComponent(height));

						content = '<iframe width="' + width + '" height="' + height + '" src="' + url + '" ';
						content += 'frameborder="0" allowfullscreen></iframe>';

						var instance = this.getParentEditor();
						instance.insertHtml(content);
					}
				};
			});
		}
	});
})();