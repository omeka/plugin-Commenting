var Commenting = {
		
        
    handleReply: function(event) {        
        Commenting.moveForm(event);    
    },
    
    finalizeMove: function() {
        jQuery('#comment-form-body_parent').attr('style', '')  
    },
    
	moveForm: function(event) {
	    //first make tinyMCE go away so it is safe to move around in the DOM
	    tinyMCE.execCommand('mceRemoveControl', false, 'comment-form-body');
		jQuery('#comment-form').insertAfter(event.target);
		commentId = Commenting.getCommentId(event.target);
		jQuery('#parent-id').val(commentId);
		tinyMCE.execCommand('mceAddControl', false, 'comment-form-body');
		
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

/**
 * Add the TinyMCE WYSIWYG editor to a page.
 * Default is to add to all textareas.
 * Modified from the admin-side global.js Omeka.wysiwyg
 *
 * @param {Object} [params] Parameters to pass to TinyMCE, these override the
 * defaults.
 */
Commenting.wysiwyg = function (params) {
    // Default parameters
    initParams = {
        plugins: "paste,inlinepopups",
        convert_urls: false,
        mode: "exact", 
        elements: 'comment-form-body',
        object_resizing: true,
        theme: "advanced",
        theme_advanced_toolbar_location: "top",
        force_br_newlines: false,
        forced_root_block: 'p', // Needed for 3.x
        remove_linebreaks: true,
        fix_content_duplication: false,
        fix_list_elements: true,
        valid_child_elements: "ul[li],ol[li]",
        theme_advanced_buttons1: "bold,italic,underline,link",
        theme_advanced_buttons2: "",
        theme_advanced_buttons3: "",
        theme_advanced_toolbar_align: "left"
    };

    // Overwrite default params with user-passed ones.
    for (var attribute in params) {
        // Account for annoying scripts that mess with prototypes.
        if (params.hasOwnProperty(attribute)) {
            initParams[attribute] = params[attribute];
        }
    }

    tinyMCE.init(initParams);
};



jQuery(document).ready(function() {	
	jQuery('.comment-reply').click(Commenting.handleReply);
	jQuery('.comment-flag').click(Commenting.flag);
	jQuery('.comment-unflag').click(Commenting.unflag);
	Commenting.wysiwyg();
});
		
		
	