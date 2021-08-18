(function ($) {

    var Commenting = {
    
        elements: [],

        statusClasses: {
            'flagged': 'flagged unflagged',
            'approved': 'approved unapproved',
            'spam': 'spam not-spam'
        },

        actionToggle: function() {            
            commentEl = $(this).closest('.comment');
            id = commentEl.attr('id').substring(8);
            action = $(this).data('action');
            status = (commentEl.hasClass(action)) ? 0 : 1;
            Commenting.elements = [commentEl];
            
            commentEl.toggleClass(Commenting.statusClasses[action]);
            json = {'ids': [id], [action]: status};
            $.post('update-' + action, json, Commenting.genericResponseHandler);
        },

        genericResponseHandler: function(response) {
            if (response.status !== 'ok') {
                alert('Error performing action: ' + response.message);
            }  
        },
            
        deleteResponseHandler: function(response) {
            window.location.reload();
        },
    
        batchDelete: function() {
            var ids = Commenting.getCheckedCommentIds();
            json = {'ids': ids};
            $.post("batch-delete", json, Commenting.deleteResponseHandler);
    
        },
    
        batchFlag: function() {
            var ids = Commenting.getCheckedCommentIds();
            json = {'ids': ids, 'flagged': 1};
            $.post("update-flagged", json, Commenting.flagResponseHandler);
        },
    
        batchUnflag: function() {
            var ids = Commenting.getCheckedCommentIds();
            json = {'ids': ids, 'flagged': 0};
            $.post("update-flagged", json, Commenting.flagResponseHandler);
        },
    
        batchApprove: function() {
            var ids = Commenting.getCheckedCommentIds();
            json = {'ids': ids, 'approved': 1};
            $.post("update-approved", json, Commenting.approveResponseHandler);
        },
    
        batchUnapprove: function() {
            var ids = Commenting.getCheckedCommentIds();
            json = {'ids': ids, 'approved': 0};
            $.post("update-approved", json, Commenting.approveResponseHandler);
        },
    
        reportSpam: function() {
            commentEl = $(this).closest('div.comment');
            id = commentEl.attr('id').substring(8);
            Commenting.elements = [commentEl];
            json = {'ids': [id], 'spam': 1};
            $.post("update-spam", json, Commenting.spamResponseHandler);
        },
    
        reportHam: function() {
            commentEl = $(this).closest('div.comment');
            id = commentEl.attr('id').substring(8);
            Commenting.elements = [commentEl];
            json = {'ids': [id], 'spam': 0};
            $.post("update-spam", json, Commenting.spamResponseHandler);
        },
    
        batchReportSpam: function() {
            var ids = Commenting.getCheckedCommentIds();
            json = {'ids': ids, 'spam': true};
            $.post("update-spam", json, Commenting.spamResponseHandler);
        },
    
        batchReportHam: function() {
            var ids = Commenting.getCheckedCommentIds();
            json = {'ids': ids, 'spam': false};
            $.post("update-spam", json, Commenting.spamResponseHandler);
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
            if($(this).is(':checked')) {
                Commenting.batchSelect();
            } else {
                Commenting.batchUnselect();
            }
        },
    
        toggleActive: function() {
            //toggle whether the bulk actions should be active
            //check all in checkboxes, if any are checked, must be active
            $('#commenting-batch-actions button').prop('disabled',
                $('.batch-select-comment:checked').length == 0);
        },
    
        batchSelect: function() {
            $('input.batch-select-comment').prop('checked', true);
            this.toggleActive();
        },
    
        batchUnselect: function() {
            $('input.batch-select-comment').prop('checked', false);
            this.toggleActive();
        },
    
        getCheckedCommentIds: function() {
            var ids = new Array();
            Commenting.elements = [];
            $('input.batch-select-comment:checked').each(function() {
                var commentEl = $(this).closest('div.comment');
                ids[ids.length] = commentEl.attr('id').substring(8);
                Commenting.elements[Commenting.elements.length] = commentEl;
            });
            return ids;
        }
    }
    
    $(document).ready(function() {

        $('a.action').click(function(e) {e.preventDefault();});
        $('.approval-action').click(Commenting.actionToggle);
        $('.flag-action').click(Commenting.actionToggle);
        $('.spam-action').click(Commenting.actionToggle);
        
        $('#batch-select').click(Commenting.toggleSelected);
        $('.batch-select-comment').click(Commenting.toggleActive);
    
        $('#batch-delete').click(Commenting.batchDelete);
        $('#batch-approve').click(Commenting.batchApprove);
        $('#batch-unapprove').click(Commenting.batchUnapprove);
        $('#batch-report-spam').click(Commenting.batchReportSpam);
        $('#batch-report-ham').click(Commenting.batchReportHam);
        $('#batch-flag').click(Commenting.batchFlag);
        $('#batch-unflag').click(Commenting.batchUnflag);
    });
})(jQuery);

