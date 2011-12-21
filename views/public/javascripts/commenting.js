var Commenting = {
		
	moveForm: function(event) {
		jQuery('#comment-form').insertAfter(event.target);
		parentId = event.target.parentNode.id.substring(8);
		jQuery('#parent-id').val(parentId);
	}
}

jQuery(document).ready(function() {	
	jQuery('.comment-reply').click(Commenting.moveForm);	
});
		
		
	