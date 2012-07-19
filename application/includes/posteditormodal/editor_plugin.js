/**
 * editor_plugin_src.js
 *
 * Opens a jquery dialog with iframe containg the cityindex post editor.
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('posteditormodal');

	tinymce.create('tinymce.plugins.PosteditormodalPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mcePosteditormodal');
			ed.addCommand('mcePosteditormodal', function() {
				
				tb_show('', '/wp-admin/admin-ajax.php?action=get_modal_editor&posteditormodal_action=excel_to_table&_wpnonce='+posteditor_modal_nonce+'TB_iframe=true');
				return;
				
				//get post editor area width
				var width = jQuery('#content_ifr').contents().find('body').width();
				
				//open dialog with iframe
				jQuery('<div width="'+width+'" height="90%"><iframe id="posteditor-modal" src="/wp-admin/admin-ajax.php?action=get_modal_editor&amp;_wpnonce='+posteditor_modal_nonce+'" style="width:100%;height:100%"></iframe></div>').dialog({
					modal: true,
					width : width+100,
					height: jQuery(window).height()-150,
					dialogClass : 'posteditorModal',
					buttons : {
						"Done" : function(){	//add contents to post editor
							var content = '';
							
							//if tinymce is not loaded then get content from textarea
							if(!jQuery("#posteditor-modal")[0].contentWindow.tinyMCE)
								content = jQuery('#posteditor-modal')[0].contentWindow.jQuery('textarea[name=data]').val();
							//else get tinymce content
							else
								content = jQuery('#posteditor-modal')[0].contentWindow.tinyMCE.activeEditor.getContent();
							
							//set post editor data and close
							ed.selection.setContent(content);
							jQuery(this).dialog("close");
						},
						"Reset" : function(){
							jQuery('#posteditor-modal')[0].contentWindow.tinyMCE.activeEditor.setContent('');
							jQuery('#posteditor-modal')[0].contentWindow.jQuery('textarea[name=data]').html('');
							jQuery('#posteditor-modal')[0].contentWindow.jQuery('#results-data').hide();
							jQuery('#posteditor-modal')[0].contentWindow.jQuery('#raw-data').show();
						},
						"Cancel" : function(){ //close dialog
							jQuery(this).dialog("close");
						}
					},
					
					//fix for modal drag perfomance issues @see http://stackoverflow.com/questions/7145317/jquery-ui-dialog-around-iframe-performance-issues
					dragStart: function (event, ui) {
						jQuery('iframe', this).each(function() {
							jQuery('<div class="ui-draggable-iframeFix" style="background: transparent;"></div>')
								.css({
									width: '100%', 
									height: '100%',
									position: 'absolute', 
									opacity: '1', 
									zIndex: 1000, 
									overflowX: 'hidden'
								})
							.css(jQuery(this).position())
							.appendTo(jQuery(this).offsetParent());
						});
					},
					dragStop: function (event, ui) {
						jQuery("div.ui-draggable-iframeFix").each(function() {
							this.parentNode.removeChild(this);
						}); //Remove frame helpers
					}
				});
			});

			// Register posteditormodal button
			ed.addButton('posteditormodal', {
				title : 'posteditormodal.desc',
				cmd : 'mcePosteditormodal',
				image : url + '/img/posteditormodal.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('posteditormodal', n.nodeName == 'IMG');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Posteditormodal plugin',
				author : 'Some author',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/posteditormodal',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('posteditormodal', tinymce.plugins.PosteditormodalPlugin);
})();