jQuery(document).ready(function() {

	if(posteditor_show_results){
		jQuery('#results').tinymce({
			// Location of TinyMCE script
			script_url : posteditor_url + '/application/includes/tinymce/jscripts/tiny_mce/tiny_mce.js',
			// General options
			theme : "advanced",
			plugins : "table,autolink,paste",
			theme_advanced_buttons1 : "cut,copy,paste|link,unlink,anchor",
			theme_advanced_buttons2 : "tablecontrols",
			width: 568,
			/*plugins : "posteditormodal,autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
			// Theme options
			theme_advanced_buttons1 : "posteditormodal,save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",*/
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : false,
			// Example content CSS (should be your site CSS)
			content_css : theme_dir + '/editor-style.css'/*,
			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",
			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}*/
		});
		jQuery('#results_tbl').width('100%');
		jQuery('#raw-data').hide();
	}
	else{
		jQuery('#results').hide();
		jQuery('#raw-data').show();
	}
}); 