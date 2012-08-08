Commenting = {
        
        toggleCommentOptions: function() {
            jQuery('div#non-public-options').toggle('slow');
        },
        
        toggleViewOptions: function() {
            jQuery('div.view-options').toggle('slow');
        },
        
        toggleModerateOptions: function() {
            jQuery('div.moderate-options').toggle('slow');
        }
   
};

jQuery(document).ready(function() {
    jQuery('input#commenting_allow_public').click(Commenting.toggleCommentOptions);
    jQuery('input#commenting_allow_public_view').click(Commenting.toggleViewOptions);
    jQuery('input#commenting_require_public_moderation').click(Commenting.toggleModerateOptions);

    if(jQuery('input#commenting_allow_public').attr('checked') == 'checked') {
        jQuery('div#non-public-options').hide();        
    }
    if(jQuery('input#commenting_allow_public_view').attr('checked') == 'checked') {
        jQuery('div.view-options').hide();
    }    
    if(jQuery('input#commenting_require_public_moderation').attr('checked') != 'checked') {
        jQuery('div.moderate-options').hide();
    } 
    
});
