<div class='comment-author'>
    <?php
        if(!empty($comment->author_name)) {
            $author = html_escape($comment->author_name);
            if(empty($comment->author_url)) {
                $authorText = $author;
            } else {
                $url = html_escape($comment->getAuthorUrl());
                $authorText = "<a href='{$url}' rel='nofollow'>{$author}</a>";
            }
        } else {
            $authorText = __("Anonymous");
        }
    ?>
    <p class='comment-author-name'><?php echo $authorText?></p>
    <?php
        $hash = md5(strtolower(trim($comment->author_email)));
        $url = "//www.gravatar.com/avatar/$hash";
        echo "<img class='gravatar' src='$url' />";
    ?>
</div>
<div class='comment-body <?php if($comment->flagged):?>comment-flagged<?php endif;?> '><?php echo $comment->body; ?></div>
<?php if(is_allowed('Commenting_Comment', 'unflag')): ?>
<p class='comment-flag' <?php if($comment->flagged): ?> style='display:none;'<?php endif;?> ><?php echo __("Flag inappropriate"); ?></p>
<p class='comment-unflag' <?php if(!$comment->flagged): ?>style='display:none;'<?php endif;?> ><?php echo __("Unflag inappropriate"); ?></p>
<?php endif; ?>

<?php if (is_allowed('Commenting_Comment', 'add') || get_option('commenting_allow_public') == 1): ?>
<p class='comment-reply'><?php echo __("Reply"); ?></p>
<?php endif; ?>
