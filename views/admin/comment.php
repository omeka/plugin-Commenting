<div id="comment-<?php echo $comment->id; ?>" class='comment'>

    <div class='commenting-admin'>
        <input class='batch-select-comment' type='checkbox' />
        <ul class='comment-admin-menu'>
        <?php if ($comment->approved): ?>
            <li><span class='approved'>Approved</span><span class='unapprove'>Unapprove</span></li>
        <?php else: ?>
            <li><span class='unapproved'>Not Approved</span><span class='approve'>Approve</span></li>
        <?php endif;?>
        <?php if(get_option('commenting_wpapi_key') != ''): ?>
            <?php if($comment->is_spam): ?>
                <li><span class='spam'>Spam</span><span class='report-ham'>Report Ham</span></li>
            <?php else: ?>
                <li><span class='ham'>Ham</span><span class='report-spam'>Report Spam</span></li>
            <?php endif; ?>
        
        <?php endif;?>
            <li><a href='<?php echo $comment->getAbsoluteUrl(false); ?>'>View Page</a></li>
            <li><a href='mailto:<?php echo $comment->author_email; ?>'><?php echo $comment->author_email; ?></a></li>
        </ul>
        
    </div>

    <div class='comment-author'>
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
        ?>
        <p class='comment-author-name'><?php echo $authorText; ?></p>
    </div>
    
    <div class='comment-body'><?php echo $comment->body; ?></div>
    
</div>