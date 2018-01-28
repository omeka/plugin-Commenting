var Commenting = {

    handleReply: function(event) {
        Commenting.moveForm(event);
    },

    finalizeMove: function() {
        jQuery('#comment-form-body_parent').attr('style', '')  
    },

    moveForm: function(event) {
        //first make tinyMCE go away so it is safe to move around in the DOM
        tinyMCE.EditorManager.execCommand('mceRemoveEditor', false, 'comment-form-body');
        jQuery('#comment-form').insertAfter(event.target);
        commentId = Commenting.getCommentId(event.target);
        jQuery('#parent-id').val(commentId);
        tinyMCE.EditorManager.execCommand('mceAddEditor', false, 'comment-form-body');
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

if(typeof Omeka == 'undefined' ) {
    Omeka = {};
}

if(typeof Omeka.wysiwyg == 'undefined') {
    Omeka.wysiwyg = function (params) {
        // Default parameters
        initParams = {
            convert_urls: false,
            selector: "#comment-form-body",
            menubar: false,
            statusbar: false,
            toolbar_items_size: "small",
            toolbar: "bold italic underline | alignleft aligncenter alignright | bullist numlist | link formatselect code",
            plugins: "lists,link,code,paste,media,autoresize",
            autoresize_max_height: 500,
            entities: "160,nbsp,173,shy,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm",
            verify_html: false,
            add_unload_trigger: false
        };

        tinyMCE.init(jQuery.extend(initParams, params));
    };
}

jQuery(document).ready(function() {
    jQuery('.comment-reply').click(Commenting.handleReply);
    jQuery('.comment-flag').click(Commenting.flag);
    jQuery('.comment-unflag').click(Commenting.unflag);
    Omeka.wysiwyg();
});
