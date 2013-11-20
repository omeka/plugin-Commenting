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
	        comment.find('p.comment-flag').hide();
	        comment.find('p.comment-unflag').show();
	    }
	    
	    if(response.action == 'unflagged') {
	        comment.find('div.comment-body').removeClass('comment-flagged');
	        comment.find('p.comment-flag').show();	        
	        comment.find('p.comment-unflag').hide();
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

if(typeof Omeka == 'undefined' ) {
    Omeka = {};
}

if(typeof Omeka.wysiwyg == 'undefined') {
    Omeka.wysiwyg = function (params) {
        // Default parameters
        initParams = {
                convert_urls: false,
                mode: "textareas", // All textareas
                theme: "advanced",
                theme_advanced_toolbar_location: "top",
                theme_advanced_statusbar_location: "none",
                theme_advanced_toolbar_align: "left",
                theme_advanced_buttons1: "bold,italic,underline,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,formatselect,code",
                theme_advanced_buttons2: "",
                theme_advanced_buttons3: "",
                plugins: "paste,inlinepopups,media",
                media_strict: false,
                width: "100%"
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

}

jQuery(document).ready(function() {	
	jQuery('.comment-reply').click(Commenting.handleReply);
	jQuery('.comment-flag').click(Commenting.flag);
	jQuery('.comment-unflag').click(Commenting.unflag);
	var commentingWysiwyg = {
	        elements: 'comment-form-body',
	        mode: 'exact',
	        valid_child_elements: "ul[li],ol[li]",
	        theme_advanced_buttons1: "bold,italic,underline,link,bullist,numlist,|,code",
	    };	        
	Omeka.wysiwyg(commentingWysiwyg);
});
		
		
	