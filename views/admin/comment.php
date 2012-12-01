<div id="comment-<?php echo $comment->id; ?>" class='comment'>

    <div class='commenting-admin four columns alpha'>
        <input class='batch-select-comment' type='checkbox' />
        <ul class='comment-admin-menu'>
        <?php if ($comment->approved): ?>
            <li><span class='status approved'>Approved</span><span class='unapprove action'>Unapprove</span></li>
        <?php else: ?>
            <li><span class='status unapproved'>Not Approved</span><span class='approve action'>Approve</span></li>
        <?php endif;?>
        <?php if(get_option('commenting_wpapi_key') != ''): ?>
            <?php if($comment->is_spam): ?>
                <li><span class='status spam'>Spam</span><span class='report-ham action'>Report Ham</span></li>
            <?php else: ?>
                <li><span class='status ham'>Ham</span><span class='report-spam action'>Report Spam</span></li>
            <?php endif; ?>
        
        <?php endif;?>
            <?php if($comment->flagged): ?>
            <li><span class='status flagged'>Flagged</span><span class='unflag action'>Unflag</span></li>
            <?php else: ?>
            <li><span class='status not-flagged'>Not Flagged</span><span class='flag action'>Flag Inappropriate</span></li>
            <?php endif;?>
            <li><a href='<?php echo $comment->getAbsoluteUrl(false); ?>'>View Page</a></li>
            <li class='comment-author'>
                <?php 
                    if(!empty($comment->author_name)) {
                        if(empty($comment->author_url)) {
                            $authorText = $comment->author_name;
                        } else {
                            $authorText = "<a href='{$comment->author_url}'>{$comment->author_name}</a>";
                        }
                    } else {
                        $authorText = "Anonymous";
                    }   
                    echo $authorText;
                ?>            
            <a href='mailto:<?php echo $comment->author_email; ?>'><?php echo $comment->author_email; ?></a>
            </li>
            
        </ul>
        
    </div>

    <div class='comment-body three columns omega'><?php echo $comment->body; ?></div>
    
</div>