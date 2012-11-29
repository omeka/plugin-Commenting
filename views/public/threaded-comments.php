<?php 
foreach($comments as $comment) :  ?>
    <?php if($comment->parent_comment_id == $parent_id): ?>
        <?php echo $this->partial('comment.php', array('comment' => $comment)); ?>
            
        <div class='comment-children'>
            <?php echo $this->partial('threaded-comments.php', array('comments' => $comments, 'parent_id'=>$comment->id)); ?>
            
        </div>
    <?php endif; ?>
<?php endforeach; ?>