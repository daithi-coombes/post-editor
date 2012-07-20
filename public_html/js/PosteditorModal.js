var posteditor_modal;

jQuery(document).ready(function($) {
	
	posteditor_modal = new PostEditorModal($);
	posteditor_modal.set_dimensions();
	$(window).resize( posteditor_modal.set_dimensions );
}); 

var PostEditorModal = function($){
	
	this.set_dimensions = function(){
		
		//set content height
		var header_height = $('#media-upload-header').height();
		var doc_height = $(document).height();
		$('#content').height( doc_height - header_height );
		
	}
}