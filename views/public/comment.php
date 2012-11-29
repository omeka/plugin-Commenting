<div id="comment-<?php echo $comment->id; ?>" class='comment'>
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
    </div>
    <div class='comment-body'><?php echo $comment->body; ?></div>
    <p class='comment-reply'>Reply</p>
</div>