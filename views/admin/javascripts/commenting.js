var Commenting = {
	approve: function() {
	id = jQuery(this).attr('id').substring(8);
	Commenting.element = this;
	jQuery.post("approve/", {'id': id}, Commenting.approveResponseHandler);

},
	
	approveResponseHandler: function(response, a, b) {
		if(response.status == 'ok') {
			jQuery(Commenting.element).replaceWith('Yes');	
		} else {
			alert('Error trying to approve: ' + response.message);
		}
		
	}
			
		
}

jQuery(document).ready(function() {
	jQuery('.approve').click(Commenting.approve);	
}); 