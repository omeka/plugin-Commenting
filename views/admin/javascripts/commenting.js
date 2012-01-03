var Commenting = {
		
	elements: [],
	
	approve: function() {
		commentEl = jQuery(this.parentNode.parentNode.parentNode.parentNode); 
		id = commentEl.attr('id').substring(8);
		Commenting.elements = [commentEl];
		json = {'ids': [id], 'approved': 1};
		jQuery.post("updateApproved", json, Commenting.approveResponseHandler);
	},
	
	unapprove: function() {
		commentEl = jQuery(this.parentNode.parentNode.parentNode.parentNode); 
		id = commentEl.attr('id').substring(8);
		Commenting.elements = [commentEl];
		json = {'ids': [id], 'approved': 0};
		jQuery.post("updateApproved", json, Commenting.unapproveResponseHandler);				
	},
	
	approveResponseHandler: function(response, a, b) {
		if(response.status == 'ok') {
			for(var i=0; i < Commenting.elements.length; i++) {
				var unapproveEl = jQuery(document.createElement('span'));
				unapproveEl.text("Unapprove");
				unapproveEl.addClass('unapprove');
				unapproveEl.click(Commenting.unapprove);
				approveEl = Commenting.elements[i].find('span.approve');
				unapprovedEl = Commenting.elements[i].find('span.unapproved');
				unapprovedEl.attr('class', 'approved');
				unapprovedEl.text('Approved');
				approveEl.replaceWith(unapproveEl);	
			}
		} else {
			alert('Error trying to approve: ' + response.message);
		}		
	},
	
	unapproveResponseHandler: function(response, a, b) {
		if(response.status == 'ok') {
			for(var i=0; i < Commenting.elements.length; i++) {
				var approveEl = jQuery(document.createElement('span'));
				approveEl.text("Approve");
				approveEl.addClass('approve');
				approveEl.click(Commenting.approve);
				unapproveEl = Commenting.elements[i].find('span.unapprove');
				approvedEl = Commenting.elements[i].find('span.approved');
				approvedEl.attr('class', 'unapproved');
				approvedEl.text('Not Approved');
				unapproveEl.replaceWith(approveEl);	
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
			Commenting.elements[Commenting.elements.length] = target;
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
			Commenting.elements[Commenting.elements.length] = target; 
		});
		json = {'ids': ids, 'approved': 0};
		jQuery.post("updateApproved", json, Commenting.unapproveResponseHandler);
	},	
	
	reportSpam: function() {
		commentEl = jQuery(this.parentNode.parentNode.parentNode.parentNode); 
		id = commentEl.attr('id').substring(8);
		Commenting.elements = [commentEl];
		json = {'ids': [id], 'spam': 1};
		jQuery.post("updateSpam", json, Commenting.spamResponseHandler);		
	},
	
	reportHam: function() {
		commentEl = jQuery(this.parentNode.parentNode.parentNode.parentNode); 
		id = commentEl.attr('id').substring(8);
		Commenting.elements = [commentEl];
		json = {'ids': [id], 'spam': 0};
		jQuery.post("updateSpam", json, Commenting.hamResponseHandler);		
	},
	
	batchReportSpam: function() {
		ids = new Array();
		jQuery('input.batch-select-comment:checked').each(function() {
			var target = jQuery(this.parentNode.parentNode);
			ids[ids.length] = target.attr('id').substring(8);
			Commenting.elements[Commenting.elements.length] = target;
		});
		json = {'ids': ids, 'spam': true};
		jQuery.post("updateSpam", json, Commenting.spamResponseHandler);		
	},
	
	batchReportHam: function() {
		ids = new Array();
		jQuery('input.batch-select-comment:checked').each(function() {
			var target = jQuery(this.parentNode.parentNode);
			ids[ids.length] = target.attr('id').substring(8);
			Commenting.elements[Commenting.elements.length] = target;
		});
		json = {'ids': ids, 'spam': false};
		jQuery.post("updateSpam", json, Commenting.hamResponseHandler);		
	},
	
	spamResponseHandler: function(response, a, b)
	{
		if(response.status == 'ok') {
			for(var i=0; i < Commenting.elements.length; i++) {
				var reportHamEl = jQuery(document.createElement('span'));
				reportHamEl.text("Report Ham");
				reportHamEl.addClass('report-ham');
				reportHamEl.click(Commenting.reportHam);
				reportSpamEl = Commenting.elements[i].find('span.report-spam');
				hamEl = Commenting.elements[i].find('span.ham');
				hamEl.attr('class', 'spam');
				hamEl.text('Spam');
				reportSpamEl.replaceWith(reportHamEl);	
			}
		} else {
			alert('Error trying to submit spam: ' + response.message);
		}		
	},
	
	hamResponseHandler: function(response, a, b)
	{
		if(response.status == 'ok') {
			for(var i=0; i < Commenting.elements.length; i++) {
				var reportSpamEl = jQuery(document.createElement('span'));
				reportSpamEl.text("Report Spam");
				reportSpamEl.addClass('report-spam');
				reportSpamEl.click(Commenting.reportSpam);
				reportHamEl = Commenting.elements[i].find('span.report-ham');
				spamEl = Commenting.elements[i].find('span.spam');
				spamEl.attr('class', 'ham');
				spamEl.text('Ham');
				reportHamEl.replaceWith(reportSpamEl);	
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