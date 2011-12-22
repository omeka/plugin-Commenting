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
	},
	batchApprove: function() {
		ids = new Array();
		jQuery('input.batch-select-comment:checked').each(function() {
			ids[ids.length] = this.id.substring(14);
		});
		json = {'ids': ids};
		jQuery().post("batchApprove/", json, Commenting.batchApproveResponseHandler);
	},
	
	batchApproveResponseHandler: function(status, a, b) {
		if(response.status == 'ok') {
			
		} else {
			alert('Error trying to approve: ' + response.message);
		}
	},
	
	toggleSelected: function() {
		if(jQuery(this).is(':checked')) {
			Commenting.batchSelect();
		} else {
			Commenting.batchUnselect();
		}
	},
	
	batchSelect: function() {
		jQuery('input.batch-select-comment').attr('checked', 'checked');
	},
	
	batchUnselect: function() {
		jQuery('input.batch-select-comment').removeAttr('checked');
	}		
}

jQuery(document).ready(function() {
	jQuery('.approve').click(Commenting.approve);
	jQuery('#batch-select').click(Commenting.toggleSelected);
	jQuery('#batch-approve').click(Commenting.batchApprove);
}); 