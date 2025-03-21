(function ($) {

    var Commenting = {
    
        handleReply: function(event) {
            //first make tinyMCE go away so it is safe to move around in the DOM
            event.preventDefault();
            tinyMCE.EditorManager.execCommand('mceRemoveEditor', false, 'comment-form-body');
            var currentComment = $(event.target).closest('.comment');
            $('#comment-form').appendTo(currentComment);
            commentId = Commenting.getCommentId(currentComment);
            $('#parent-id').val(commentId);
            tinyMCE.EditorManager.execCommand('mceAddEditor', false, 'comment-form-body');
            $('#comment-form').find(':focusable').first().focus();
        },
    
        finalizeMove: function() {
            $('#comment-form-body_parent').attr('style', '')
        },
    
        flagToggle: function(event) {
            event.preventDefault();
            var commentRow = $(event.target).closest('.comment');
            commentRow.toggleClass('flagged');
            var commentId = Commenting.getCommentId(commentRow);
            var json = {'id': commentId };
            var flagAction = (commentRow.hasClass('flagged')) ? 'flag' : 'unflag';
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
            return el.attr('id').substring(8);
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
        },

        submitForm: function (e) {
            e.preventDefault();
            var form = $(this);
            $.post(form.attr('action'), form.serialize())
                .done(function (data) {
                    if (data.comments) {
                        tinyMCE.EditorManager.execCommand('mceRemoveEditor', false, 'comment-form-body');
                        $('#comments-container').replaceWith(data.comments);
                        tinyMCE.EditorManager.execCommand('mceAddEditor', false, 'comment-form-body');
                    }
                    if (data.fragment) {
                        window.location.hash = data.fragment;
                    }
                    $('#author_name').focus();
                    setTimeout(function() { 
                        $('#comments-status').addClass('success').removeClass('error').text(data.message);
                    }, 1000);
                })
                .fail(function (jqXHR) {
                    $('#comments-status').addClass('error').removeClass('success').text(jqXHR.responseJSON.error);
                });
        }
    };
    
    $(document).ready(function() {
        $('body').on('click', 'a.action', function(e) {
            e.preventDefault();
            var actionLink = $(this);

            if (actionLink.hasClass('reply-action')) {
                Commenting.handleReply(e);
            }
            if (actionLink.hasClass('flag-action')) {
                Commenting.flagToggle(e);
            }
        });
        
        $('body').on('submit', '#comment-form', Commenting.submitForm);
        Commenting.wysiwyg();
        Commenting.pluginRoot = $('.comments').data('commentUrlBase');
    });
})(jQuery);
