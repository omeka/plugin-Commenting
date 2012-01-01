var Commenting = {
		
	elements: [],
	
	approve: function() {
		id = jQuery(this.parentNode.parentNode.parentNode.parentNode).attr('id').substring(8);
		Commenting.elements = [jQuery(this)];
		json = {'ids': [id], 'approved': 1};
		jQuery.post("updateApproved", json, Commenting.approveResponseHandler);
	},
	
	unapprove: function() {
		id = jQuery(this.parentNode.parentNode.parentNode.parentNode).attr('id').substring(8);
		Commenting.elements = [jQuery(this)];
		json = {'ids': [id], 'approved': 0};
		jQuery.post("updateApproved", json, Commenting.unapproveResponseHandler);				
	},
	
	approveResponseHandler: function(response, a, b) {
		if(response.status == 'ok') {
			for(var i=0; i < Commenting.elements.length; i++) {
				var unapproveEl = jQuery(document.createElement('li'));
				unapproveEl.text("Unapprove");
				unapproveEl.addClass('unapprove');
				unapproveEl.click(Commenting.unapprove);
				Commenting.elements[i].replaceWith(unapproveEl);	
			}
		} else {
			alert('Error trying to approve: ' + response.message);
		}		
	},
	
	unapproveResponseHandler: function(response, a, b) {
		if(response.status == 'ok') {
			for(var i=0; i < Commenting.elements.length; i++) {
				var approveEl = jQuery(document.createElement('li'));
				approveEl.text("Approve");
				approveEl.addClass('approve');
				approveEl.click(Commenting.approve);
				Commenting.elements[i].replaceWith(approveEl);	
			}
		} else {
			alert('Error trying to unapprove: ' + response.message);
		}		
	},	
	batchApprove: function() {
		ids = new Array();
		Commenting.elements = [];
		jQuery('input.batch-select-comment:checked').each(function() {
			var target = jQuery(this.parentNode.parentNode);
			ids[ids.length] = target.attr('id').substring(8);
			Commenting.elements[Commenting.elements.length] = target.find('li.approve');
		});
		json = {'ids': ids, 'approved': 1};
		jQuery.post("updateApproved", json, Commenting.approveResponseHandler);
	},

	batchUnapprove: function() {
		ids = new Array();
		Commenting.elements = [];
		jQuery('input.batch-select-comment:checked').each(function() {
			var target = jQuery(this.parentNode.parentNode);
			ids[ids.length] = target.attr('id').substring(8);
			Commenting.elements[Commenting.elements.length] = target.find('li.unapprove'); 
		});
		json = {'ids': ids, 'approved': 0};
		jQuery.post("updateApproved", json, Commenting.unapproveResponseHandler);
	},	
	
	reportSpam: function() {
		id = jQuery(this.parentNode.parentNode.parentNode.parentNode).attr('id').substring(8);
		Commenting.elements = [jQuery(this)];
		json = {'ids': [id], 'spam': true};
		jQuery.post("updateSpam", json, Commenting.spamResponseHandler);		
	},
	
	reportHam: function() {
		id = jQuery(this.parentNode.parentNode.parentNode.parentNode).attr('id').substring(8);
		Commenting.elements = [jQuery(this)];
		json = {'ids': [id], 'spam': false};
		jQuery.post("updateSpam", json, Commenting.hamResponseHandler);		
	},
	
	batchReportSpam: function() {
		ids = new Array();
		jQuery('input.batch-select-comment:checked').each(function() {
			var target = jQuery(this.parentNode.parentNode);
			ids[ids.length] = target.attr('id').substring(8);
			Commenting.elements[Commenting.elements.length] = target.find('li.report-spam');
		});
		json = {'ids': ids, 'spam': true};
		jQuery.post("updateSpam", json, Commenting.spamResponseHandler);		
	},
	
	batchReportHam: function() {
		ids = new Array();
		jQuery('input.batch-select-comment:checked').each(function() {
			var target = jQuery(this.parentNode.parentNode);
			ids[ids.length] = target.attr('id').substring(8);
			Commenting.elements[Commenting.elements.length] = target.find('li.report-ham');
		});
		json = {'ids': ids, 'spam': false};
		jQuery.post("updateSpam", json, Commenting.hamResponseHandler);		
	},
	
	spamResponseHandler: function(response, a, b)
	{
		if(response.status == 'ok') {
			for(var i=0; i < Commenting.elements.length; i++) {
				var reportHamEl = jQuery(document.createElement('li'));
				reportHamEl.text("Report Ham");
				reportHamEl.addClass('report-ham');
				reportHamEl.click(Commenting.reportHam);
				Commenting.elements[i].replaceWith(reportHamEl);	
			}
		} else {
			alert('Error trying to submit spam: ' + response.message);
		}		
	},
	
	hamResponseHandler: function(response, a, b)
	{
		if(response.status == 'ok') {
			for(var i=0; i < Commenting.elements.length; i++) {
				var reportSpamEl = jQuery(document.createElement('li'));
				reportSpamEl.text("Report Spam");
				reportSpamEl.addClass('report-spam');
				reportSpamEl.click(Commenting.reportSpam);
				Commenting.elements[i].replaceWith(reportSpamEl);	
			}
		} else {
			alert('Error trying to submit ham: ' + response.message);
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
	jQuery('.unapprove').click(Commenting.unapprove);
	jQuery('#batch-select').click(Commenting.toggleSelected);
	jQuery('.report-ham').click(Commenting.reportHam);
	jQuery('.report-spam').click(Commenting.reportSpam);
}); 