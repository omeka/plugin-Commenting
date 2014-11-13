<?php 
$record = get_db()->getTable($comment->record_type)->find($comment->record_id);
// try hard to dig up a likely label from the metadata or properties
try {
    $label = metadata($record, array('Dublin Core', 'Title'));
} catch(BadMethodCallException $e) {
    
} 

if(empty($label)) {
    try {
        $label = metadata($record, 'name');
    } catch(InvalidArgumentException $e) {
        
    }
}

if(empty($label)) {
    try {
        $label = metadata($record, 'title');
    } catch(InvalidArgumentException $e) {

    }
}

if(empty($label)) {
    try {
        $label = metadata($record, 'label');
    } catch(InvalidArgumentException $e) {

    }
}

//sad trombone. couldn't find a label!
if(empty($label)) {
    $label = __('[Untitled]');
}
?>

<div id="comment-<?php echo $comment->id; ?>" class='comment'>
    <div class='commenting-admin four columns alpha'>
        <input class='batch-select-comment' type='checkbox' />
        <ul class='comment-admin-menu'>
            <li class='comment-target'>
            <span class='comment-author'>
                <?php 
                    if(!empty($comment->author_name)) {
                        if(empty($comment->author_url)) {
                            $authorText = $comment->author_name;
                        } else {
                            $authorText = "<a href='{$comment->author_url}'>{$comment->author_name}</a>";
                        }
                    } else {
                        $authorText = __('Anonymous');
                    }   
                    echo $authorText;
                ?>            
            </span>
            <?php echo __('on'); ?> <?php echo __(get_class($record)); ?> <a target='_blank' href='<?php echo record_url($comment, 'show'); ?>'><?php echo $label; ?></a>
            </li>
        
            <li class='approved' <?php echo $comment->approved ? "" : "style='display:none'"; ?>><span class='status approved'><?php echo __("Approved"); ?></span><span class='unapprove action'><?php echo __("Unapprove"); ?></span></li>
            <li class='unapproved' <?php echo $comment->approved ? "style='display:none'" : "";  ?>><span class='status unapproved'><?php echo __("Not Approved"); ?></span><span class='approve action'><?php echo __("Approve"); ?></span></li>
            <?php if(get_option('commenting_wpapi_key') != ''): ?>
                <li class='spam' <?php echo $comment->is_spam ? "" : "style='display:none'"; ?>><span class='status spam'><?php echo __("Spam"); ?></span><span class='report-ham action'><?php echo __("Report Not Spam"); ?></span></li>
                <li class='ham' <?php echo $comment->is_spam ? "style='display:none'" : "";  ?>><span class='status ham'><?php echo __("Not Spam"); ?></span><span class='report-spam action'><?php echo __("Report Spam"); ?></span></li>
            
            <?php endif;?>
            <li class='flagged' <?php echo $comment->flagged ? "" : "style='display:none'"; ?>><span class='status flagged'><?php echo __("Flagged Inappropriate"); ?></span><span class='unflag action'><?php echo __("Unflag"); ?></span></li>
            <li class='not-flagged' <?php echo $comment->flagged ? "style='display:none'" : "";  ?>><span class='status not-flagged'><?php echo __("Not Flagged"); ?></span><span class='flag action'><?php echo __("Flag Inappropriate"); ?></span></li>
            <li class='delete'><a id='delete' class='action' href='<?php echo record_url($comment, 'delete-confirm'); ?>'><?php echo __("Delete"); ?></a></li>
        </ul>
    </div>
    
    <div class='comment-body three columns omega'><?php echo $comment->body; ?></div>
    
</div>