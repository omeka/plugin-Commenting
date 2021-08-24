(function ($) {

    var Commenting = {
    
        elements: [],

        statusClasses: {
            'flagged': 'flagged not-flagged',
            'approved': 'approved not-approved',
            'spam': 'spam not-spam'
        },

        actionToggle: function() {
            var actionInput = $(this);
            var action = actionInput.data('action');
            var ids = [];
            var status, activeClass, inactiveClass;

            if (actionInput.hasClass('batch-action')) {
                status = actionInput.data('status');
                ids = Commenting.getCheckedCommentIds();
                activeClass = (status == 1) ? action : 'not-' + action;
                inactiveClass = (status == 0) ? action : 'not-' + action;
                Commenting.elements.forEach(function(comment) {
                    comment.addClass(activeClass).removeClass(inactiveClass);
                });
            } else {
                var commentEl = actionInput.closest('.comment');
                status = (commentEl.hasClass(action)) ? 0 : 1;
                ids[0] = commentEl.attr('id').substring(8);
                Commenting.elements = [commentEl];
                activeClass = (status == 1) ? action : 'not-' + action;
                inactiveClass = (status == 0) ? action : 'not-' + action;
                commentEl.addClass(activeClass).removeClass(inactiveClass);
            }
            json = {'ids': ids, [action]: status};
            $.post('update-' + action, json, Commenting.genericResponseHandler);
        },

        genericResponseHandler: function(response) {
            if (response.status !== 'ok') {
                alert('Error performing action: ' + response.message);
            }  
        },

        manageClass: function(action, status) {
            if (status == 1) {
                return
            }
        },
            
        toggleSelected: function() {
            var checked = $(this).is(':checked');
            $('input.batch-select-comment').prop('checked', checked);
            Commenting.toggleActive();
        },
    
        toggleActive: function() {
            //toggle whether the bulk actions should be active
            //check all in checkboxes, if any are checked, must beactve
            $('#commenting-batch-actions button').prop('disabled',
                $('.batch-select-comment:checked').length == 0);
        },

        toggleCommentBody: function() {
            commentEl = $(this).closest('.comment');
            commentEl.find('.comment-body').toggleClass('active');
        },
        
        getCheckedCommentIds: function() {
            var ids = new Array();
            Commenting.elements = [];
            $('.comment input.batch-select-comment:checked').each(function() {
                var commentEl = $(this).closest('.comment');
                ids[ids.length] = commentEl.attr('id').substring(8);
                Commenting.elements[Commenting.elements.length] = commentEl;
            });
            return ids;
        }
    }
    
    $(document).ready(function() {

        $('a.action').click(function(e) {e.preventDefault();}).click(Commenting.actionToggle);
        $('.show-toggle').click(Commenting.toggleCommentBody);

        $('#batch-all-checkbox').click(Commenting.toggleSelected);
        $('.batch-select-comment').click(Commenting.toggleActive);
    
        $('.batch-action').click(Commenting.actionToggle);

        Omeka.quickFilter();
    });
})(jQuery);

