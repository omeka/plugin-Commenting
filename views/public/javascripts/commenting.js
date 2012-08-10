var Commenting = {
		
	moveForm: function(event) {
		jQuery('#comment-form').insertAfter(event.target);
		commentId = Commenting.getCommentId(event.target);
		jQuery('#parent-id').val(commentId);
	},
	
	flag: function(event) {
	    var commentId = Commenting.getCommentId(event.target);
	    var json = {'id': commentId }; 
	    jQuery.post(Commenting.pluginRoot + "flag", json, Commenting.flagResponseHandler);
	    
	},

    unflag: function(event) {
        var commentId = Commenting.getCommentId(event.target);
        var json = {'id': commentId }; 
        jQuery.post(Commenting.pluginRoot + "unflag", json, Commenting.flagResponseHandler);
        
    },	
	
	flagResponseHandler: function(response, status, jqxhr) {
	    var comment = jQuery('#comment-' + response.id);
	    if(response.action == 'flagged') {
	        comment.find('div.comment-body').addClass('comment-flagged');
	        comment.find('span.comment-flag').hide();
	        comment.find('span.comment-unflag').show();

	    }
	    
	    if(response.action == 'unflagged') {	       
	        comment.find('div.comment-body').removeClass('comment-flagged');
	        //remove the flag span
	        comment.find('span.comment-flag').show();	        
	        comment.find('span.comment-unflag').hide();

	    }
	    
	},
	
	getCommentId: function(el) {
	    return jQuery(el).parents('div.comment').first().attr('id').substring(8);
	}
};

jQuery(document).ready(function() {	
	jQuery('.comment-reply').click(Commenting.moveForm);
	jQuery('.comment-flag').click(Commenting.flag);
	jQuery('.comment-unflag').click(Commenting.unflag);
});
		
		
	