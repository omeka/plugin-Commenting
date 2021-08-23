(function ($) {

    var Commenting = {
    
        elements: [],

        statusClasses: {
            'flagged': 'flagged not-flagged',
            'approved': 'approved not-approved',
            'spam': 'spam not-spam'
        },

        actionToggle: function() {
            actionInput = $(this);
            action = actionInput.data('action');

            if (actionInput.hasClass('batch-action')) {
                status = actionInput.data('status');
                ids = Commenting.getCheckedCommentIds();
                activeClass = (status == 1) ? action : 'not-' + action;
                inactiveClass = (status == 0) ? action : 'not-' + action;
                Commenting.elements.forEach(function(comment) {
                    comment.addClass(activeClass).removeClass(inactiveClass);
                });
            } else {
                commentEl = actionInput.closest('.comment');
                status = (commentEl.hasClass(action)) ? 0 : 1;
                ids = commentEl.attr('id').substring(8);
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

        $('a.action').click(function(e) {e.preventDefault();});
        $('.approval-action, .flag-action, .spam-action').click(Commenting.actionToggle);

        $('#batch-all-checkbox').click(Commenting.toggleSelected);
        $('.batch-select-comment').click(Commenting.toggleActive);
    
        $('.batch-action').click(Commenting.actionToggle);
    });
})(jQuery);

