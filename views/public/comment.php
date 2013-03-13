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
    <p class='comment-author-name'><?php echo $authorText?></p>
    <?php 
        $hash = md5(strtolower(trim($comment->author_email)));
        $url = "http://www.gravatar.com/avatar/$hash";
        echo "<img class='gravatar' src='$url' />";
    ?>
</div>
<div class='comment-body <?php if($comment->flagged):?>comment-flagged<?php endif;?> '><?php echo $comment->body; ?></div>
<p class='comment-flag' <?php if($comment->flagged): ?> style='display:none;'<?php endif;?> >Flag inappropriate</p>
<?php if(is_allowed('Commenting_Comment', 'unflag')): ?>
<p class='comment-unflag' <?php if(!$comment->flagged): ?>style='display:none;'<?php endif;?> >Unflag inappropriate</p>
<?php endif; ?>
<p class='comment-reply'>Reply</p>
