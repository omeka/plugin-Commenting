Commenting = {
        toggleCommentOptions: function() {
            jQuery('div#non-public-options').toggle();
            if(jQuery(this).attr('checked') == 'checked') {
                jQuery('div#commenting-moderate-public').show();
            } else {
                jQuery('div#commenting-moderate-public').removeAttr('checked');
                jQuery('div#commenting-moderate-public').hide();
            }
            Commenting.toggleModerateOptions();
        },

        toggleViewOptions: function() {
            jQuery('div.view-options').toggle();
        },

        toggleModerateOptions: function() {
            if( jQuery('input#commenting_allow_public').attr('checked') == 'checked') {
                jQuery('div#commenting-moderate-public').show();
                if(jQuery('input#commenting_require_public_moderation').attr('checked') == 'checked') {
                    jQuery('div#moderate-options').show();
                } else {
                    jQuery('div#moderate-options').hide();
                }
            } else {
                jQuery('div#moderate-options').show();
                jQuery('div#commenting-moderate-public').hide();
                jQuery('input#commenting_require_public_moderation').removeAttr('checked');
            }
        }
};

jQuery(document).ready(function() {
    jQuery('input#commenting_allow_public').click(Commenting.toggleCommentOptions);
    jQuery('input#commenting_allow_public_view').click(Commenting.toggleViewOptions);
    jQuery('input#commenting_require_public_moderation').click(Commenting.toggleModerateOptions);

    //if public commenting is on
    if(jQuery('input#commenting_allow_public').attr('checked') == 'checked') {
        jQuery('div#non-public-options').hide();
        jQuery('div#commenting-moderate-public').show();
    } else {
        jQuery('div#commenting-moderate-public').hide();
        jQuery('div#commenting-moderate-public').attr('checked', '');
        jQuery('div.moderate-options').show();
    }

    Commenting.toggleModerateOptions();
    if(jQuery('input#commenting_allow_public_view').attr('checked') == 'checked') {
        jQuery('div.view-options').hide();
    }

});
