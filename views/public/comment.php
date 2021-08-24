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
    <?php
        $hash = md5(strtolower(trim($comment->author_email)));
        $url = "//www.gravatar.com/avatar/$hash";
        echo "<img class='gravatar' src='$url' />";
    ?>
    <p class='comment-author-name'><?php echo $authorText?></p>
</div>
<div class="comment-body"><?php echo $comment->body; ?></div>
<div class="action-links">
    <?php if(is_allowed('Commenting_Comment', 'unflag')): ?>
    <a href="#" class="flag-action action">
        <span class="green"><?php echo  __("Flag inappropriate"); ?></span>
        <span class="red"><?php echo __("Unflag inappropriate"); ?></span>
    </a>
    <?php endif; ?>

    <?php if (is_allowed('Commenting_Comment', 'add') || get_option('commenting_allow_public') == 1): ?>
    <a href="#" class='reply-action action'><?php echo __("Reply"); ?></a>
    <?php endif; ?>
</div>