
var posteditor_modal_ett;

jQuery(document).ready(function(){
	
	posteditor_modal_ett = new PosteditorModalETT();
	
	jQuery('#results').tinymce({
		// Location of TinyMCE script
		script_url : posteditor_url + '/application/includes/tinymce/jscripts/tiny_mce/tiny_mce.js',
		// General options
		theme : "advanced",
		plugins : "table,autolink,paste",
		theme_advanced_buttons1 : "cut,copy,paste,|,link,unlink,anchor,|,tablecontrols",
		//theme_advanced_buttons2 : "",
		//width: 568,
		theme_advanced_resizing : true,
		theme_advanced_resize_horizontal : false,
		// Example content CSS (should be your site CSS)
		content_css : theme_dir + '/editor-style.css'
	});
});


var PosteditorModalETT = function(){
	
	/**
	 * Adds the formated html content to the main post editor.
	 *
	 * @method
	 * @public
	 * @member PostedirotModalETT
	 */
	this.insert_to_editor = function(){
		
		var content = '';
							
		//if tinymce is not loaded then get content from textarea - 		if(!jQuery("#posteditor-modal")[0].contentWindow.tinyMCE)
		if(!tinyMCE)
			content = jQuery('textarea[name=data]').val();
		//else get tinymce content
		else
			content = tinyMCE.activeEditor.getContent();
		
		//set post editor data and close
		window.parent.tinyMCE.execCommand('mceInsertContent', false, content);
		window.parent.tb_remove();
		return;
	}
	
	/**
	 * Resets the modal form.
	 * 
	 * @method
	 * @publicS
	 * @member PosteditorModalETT
	 */
	this.reset_form = function(){
		tinyMCE.activeEditor.setContent('');
		jQuery('textarea[name=data]').html('');
	}
}