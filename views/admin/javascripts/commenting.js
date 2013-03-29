var Commenting = {
		
	elements: [],
	
	flag: function() {
        commentEl = jQuery(this).closest('div.comment');
        id = commentEl.attr('id').substring(8);
        Commenting.elements = [commentEl];
        json = {'ids': [id], 'flagged': 1};
        jQuery.post("update-flagged", json, Commenting.flagResponseHandler);
	},
	
	unflag: function() {
        commentEl = jQuery(this).closest('div.comment');
        id = commentEl.attr('id').substring(8);
        Commenting.elements = [commentEl];
        json = {'ids': [id], 'flagged': 0};
        jQuery.post("update-flagged", json, Commenting.flagResponseHandler);	    
	},
	
	flagResponseHandler: function(response, textStatus, jqReq) {
        if(response.status == 'ok') {
            for(var i=0; i < Commenting.elements.length; i++) {
                Commenting.elements[i].find('li.flagged').toggle();
                Commenting.elements[i].find('li.not-flagged').toggle();
            }
        } else {
            alert('Error trying to unapprove: ' + response.message);
        }   	    
	},
	
	approve: function() {
	    commentEl = jQuery(this).closest('div.comment');
		id = commentEl.attr('id').substring(8);
		Commenting.elements = [commentEl];
		json = {'ids': [id], 'approved': 1};
		jQuery.post("update-approved", json, Commenting.approveResponseHandler);
	},
	
	unapprove: function() {
        commentEl = jQuery(this).closest('div.comment'); 
		id = commentEl.attr('id').substring(8);
		Commenting.elements = [commentEl];
		json = {'ids': [id], 'approved': 0};
		jQuery.post("update-approved", json, Commenting.approveResponseHandler);				
	},
	
	
	approveResponseHandler: function(response, textStatus, jqReq) {
		if(response.status == 'ok') {
			for(var i=0; i < Commenting.elements.length; i++) {
			    Commenting.elements[i].find('li.approved').toggle();
			    Commenting.elements[i].find('li.unapproved').toggle();
			}
		} else {
			alert('Error trying to unapprove: ' + response.message);
		}		
	},	
	
	deleteResponseHandler: function(response, textStatus, jqReq) {
	    window.location.reload();	    
	},
	
	batchDelete: function() {
	    var ids = Commenting.getCheckedCommentIds();
        json = {'ids': ids};
        jQuery.post("batch-delete", json, Commenting.deleteResponseHandler);
	    
	},

    batchFlag: function() {
        var ids = Commenting.getCheckedCommentIds();
        json = {'ids': ids, 'flagged': 1};
        jQuery.post("update-flagged", json, Commenting.flagResponseHandler);
    },

    batchUnflag: function() {
        var ids = Commenting.getCheckedCommentIds();
        json = {'ids': ids, 'flagged': 0};
        jQuery.post("update-flagged", json, Commenting.flagResponseHandler);
    },  

    batchApprove: function() {
	    var ids = Commenting.getCheckedCommentIds();
		json = {'ids': ids, 'approved': 1};
		jQuery.post("update-approved", json, Commenting.approveResponseHandler);
	},

	batchUnapprove: function() {
	    var ids = Commenting.getCheckedCommentIds();
		json = {'ids': ids, 'approved': 0};
		jQuery.post("update-approved", json, Commenting.approveResponseHandler);
	},	
	
	reportSpam: function() {
        commentEl = jQuery(this).closest('div.comment');
		id = commentEl.attr('id').substring(8);
		Commenting.elements = [commentEl];
		json = {'ids': [id], 'spam': 1};
		jQuery.post("update-spam", json, Commenting.spamResponseHandler);		
	},
	
	reportHam: function() {
        commentEl = jQuery(this).closest('div.comment');
		id = commentEl.attr('id').substring(8);
		Commenting.elements = [commentEl];
		json = {'ids': [id], 'spam': 0};
		jQuery.post("update-spam", json, Commenting.spamResponseHandler);		
	},
	
	batchReportSpam: function() {
	    var ids = Commenting.getCheckedCommentIds();
		json = {'ids': ids, 'spam': true};
		jQuery.post("update-spam", json, Commenting.spamResponseHandler);		
	},
	
	batchReportHam: function() {
	    var ids = Commenting.getCheckedCommentIds();
		json = {'ids': ids, 'spam': false};
		jQuery.post("update-spam", json, Commenting.spamResponseHandler);		
	},
	
	spamResponseHandler: function(response, textStatus, jqReq)
	{
		if(response.status == 'ok') {
            for(var i=0; i < Commenting.elements.length; i++) {
                Commenting.elements[i].find('li.spam').toggle();
                Commenting.elements[i].find('li.ham').toggle();
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
	
	toggleActive: function() {
	    //toggle whether the bulk actions should be active
	    //check all in checkboxes, if any are checked, must be active
	    if(jQuery('.batch-select-comment:checked').length == 0) {
	        jQuery('#batch-delete').unbind('click');
	        jQuery('#batch-approve').unbind('click');
	        jQuery('#batch-unapprove').unbind('click');
	        jQuery('#batch-report-spam').unbind('click');
	        jQuery('#batch-report-ham').unbind('click');
	        jQuery('#batch-flag').unbind('click');
	        jQuery('#batch-unflag').unbind('click');
	        jQuery('#commenting-batch-actions > a').addClass('disabled');	        
	    } else {
	        jQuery('#batch-delete').click(Commenting.batchDelete);
	        jQuery('#batch-approve').click(Commenting.batchApprove);
	        jQuery('#batch-unapprove').click(Commenting.batchUnapprove);
	        jQuery('#batch-report-spam').click(Commenting.batchReportSpam);
	        jQuery('#batch-report-ham').click(Commenting.batchReportHam);
            jQuery('#batch-flag').click(Commenting.batchFlag);
            jQuery('#batch-unflag').click(Commenting.batchUnflag);	        
	        jQuery('#commenting-batch-actions > a').removeClass('disabled');	        
	    }
	    
	},
	
	batchSelect: function() {
		jQuery('input.batch-select-comment').attr('checked', 'checked');
		this.toggleActive();

	},
	
	batchUnselect: function() {
		jQuery('input.batch-select-comment').removeAttr('checked');
		this.toggleActive();
	},	
	
	getCheckedCommentIds: function() {
	    var ids = new Array();
        Commenting.elements = [];
        jQuery('input.batch-select-comment:checked').each(function() {
            var commentEl = jQuery(this).closest('div.comment');
            ids[ids.length] = commentEl.attr('id').substring(8);
            Commenting.elements[Commenting.elements.length] = commentEl;
        });
        return ids;
	}
}

jQuery(document).ready(function() {
	jQuery('.approve').click(Commenting.approve);
	jQuery('.unapprove').click(Commenting.unapprove);
	jQuery('.flag').click(Commenting.flag);
	jQuery('.unflag').click(Commenting.unflag);
	jQuery('#batch-select').click(Commenting.toggleSelected);
	jQuery('.report-ham').click(Commenting.reportHam);
	jQuery('.report-spam').click(Commenting.reportSpam);
	jQuery('.batch-select-comment').click(Commenting.toggleActive);
}); 