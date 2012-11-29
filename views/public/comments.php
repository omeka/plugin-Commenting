<div class='comments'>
    <h2>Comments</h2>

    <div id='comments-flash'><?php echo flash(true); ?></div>
    <?php echo fire_plugin_hook('commenting_prepend_to_comments', array('comments' =>$comments)); ?>

    <?php if($threaded) :?>
        <?php echo $this->partial('threaded-comments.php', array('comments' => $comments, 'parent_id'=>null)); ?>
    <?php else: ?>
        <?php foreach($comments as $comment): ?>
            <?php echo $this->partial('comment.php', array('comment' => $comment)); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>