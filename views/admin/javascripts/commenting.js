var Commenting = {
	approve: function() {
		id = jQuery(this.parentNode.parentNode.parentNode).attr('id').substring(8);
		Commenting.element = this;
		json = {'ids': [id], 'approved': true};
		jQuery.post("updateApproved", json, Commenting.approveResponseHandler);
	},
	
	unapprove: function() {
		id = jQuery(this.parentNode.parentNode.parentNode).attr('id').substring(8);
		Commenting.element = this;
		json = {'ids': [id], 'approved': false};
		jQuery.post("updateApproved", json, Commenting.approveResponseHandler);				
	},
	
	approveResponseHandler: function(response, a, b) {
		if(response.status == 'ok') {
			var unapproveEl = jQuery(document.createElement('li'));
			unapproveEl.text("Unapprove");
			unapproveEl.addClass('unapprove');
			unapproveEl.click(Commenting.unapprove);
			jQuery(Commenting.element).replaceWith(unapproveEl);	
		} else {
			alert('Error trying to approve: ' + response.message);
		}		
	},
	batchApprove: function() {
		ids = new Array();
		jQuery('input.batch-select-comment:checked').each(function() {
			ids[ids.length] = this.id.substring(14);
		});
		json = {'ids': ids, 'approved': true};
		console.log(json);
		jQuery.post("updateApproved", json, Commenting.batchApproveResponseHandler);
	},

	batchUnapprove: function() {
		ids = new Array();
		jQuery('input.batch-select-comment:checked').each(function() {
			ids[ids.length] = this.id.substring(14);
		});
		json = {'ids': ids, 'approved': false};
		jQuery.post("updateApproved", json, Commenting.batchUnapproveResponseHandler);
	},	
	
	reportSpam: function() {
		id = jQuery(this.parentNode.parentNode.parentNode).attr('id').substring(8);
		Commenting.element = this;
		json = {'ids': [id], 'spam': true};
		jQuery.post("updateSpam", json, Commenting.updateSpamResponseHandler);		
	},
	
	reportHam: function() {
		id = jQuery(this.parentNode.parentNode.parentNode).attr('id').substring(8);
		Commenting.element = this;
		json = {'ids': [id], 'spam': false};
		jQuery.post("updateSpam", json, Commenting.updateHamResponseHandler);		
	},
	
	
	batchReportSpam: function() {
		ids = new Array();
		jQuery('input.batch-select-comment:checked').each(function() {
			ids[ids.length] = this.id.substring(14);
		});
		json = {'ids': ids, 'spam': true};
		jQuery.post("updateSpam", json, Commenting.batchSpamResponseHandler);		
	},
	
	batchReportHam: function() {
		ids = new Array();
		jQuery('input.batch-select-comment:checked').each(function() {
			ids[ids.length] = this.id.substring(14);
		});
		json = {'ids': ids, 'spam': false};
		jQuery.post("updateSpam", json, Commenting.batchHamResponseHandler);		
	},
	
	batchApproveResponseHandler: function(status, a, b) {
		if(response.status == 'ok') {
			
		} else {
			alert('Error trying to approve: ' + response.message);
		}
	},
	
	batchSpamResponseHandler: function(status, a, b)
	{
		
	},
	
	batchHamResponseHandler: function(status, a, b)
	{
		
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