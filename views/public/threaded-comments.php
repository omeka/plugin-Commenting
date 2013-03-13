<?php 
foreach($comments as $comment) :  ?>
    <?php if($comment->parent_comment_id == $parent_id): ?>
    <div id="comment-<?php echo $comment->id; ?>" class='comment'>
        <?php echo $this->partial('comment.php', array('comment' => $comment)); ?>
        <div class='comment-children'>
            <?php echo $this->partial('threaded-comments.php', array('comments' => $comments, 'parent_id'=>$comment->id)); ?>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>