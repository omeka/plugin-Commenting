(function ($) {

    var Commenting = {
    
        handleReply: function(event) {
            Commenting.moveForm(event);
        },
    
        finalizeMove: function() {
            $('#comment-form-body_parent').attr('style', '')
        },
    
        moveForm: function(event) {
            //first make tinyMCE go away so it is safe to move around in the DOM
            tinyMCE.EditorManager.execCommand('mceRemoveEditor', false, 'comment-form-body');
            $('#comment-form').insertAfter(event.target);
            commentId = Commenting.getCommentId(event.target);
            $('#parent-id').val(commentId);
            tinyMCE.EditorManager.execCommand('mceAddEditor', false, 'comment-form-body');
        },
    
        flagToggle: function(event) {
            event.preventDefault();
            var commentRow = $(event.target).parents('.comment').first();
            if (commentRow.hasClass('flagged')) {
                var flagAction = 'unflag';
            } else {
                var flagAction = 'flag';
            }
            var commentId = Commenting.getCommentId(event.target);
            var json = {'id': commentId };
            $.post(Commenting.pluginRoot + flagAction, json, Commenting.flagResponseHandler);
        },

        flagResponseHandler: function(response, status, jqxhr) {
            var comment = $('#comment-' + response.id);
            if(response.action == 'flagged') {
                comment.addClass('flagged').removeClass('unflagged');
            } else {
                comment.addClass('unflagged').removeClass('flagged');
            }
        },
    
        getCommentId: function(el) {
            return $(el).parents('.comment').attr('id').substring(8);
        },
    
        wysiwyg: function (params) {
            // Default parameters
            initParams = {
                convert_urls: false,
                selector: "#comment-form-body",
                menubar: false,
                statusbar: false,
                toolbar_items_size: "small",
                toolbar: "bold italic | bullist numlist | link code",
                plugins: "lists,link,code,paste,media,autoresize",
                autoresize_max_height: 500,
                entities: "160,nbsp,173,shy,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm",
                verify_html: false,
                add_unload_trigger: false
            };
    
            tinyMCE.init($.extend(initParams, params));
        }
    };
    
    $(document).ready(function() {
        $('.comment-reply').click(Commenting.handleReply);
        $('.flag-toggle').click(Commenting.flagToggle);
        Commenting.wysiwyg();
    });
})(jQuery);

