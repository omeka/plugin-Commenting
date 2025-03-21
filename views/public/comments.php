<div class="comments" data-comment-url-base="<?php echo admin_url('commenting/comment/'); ?>">
    <?php $label = get_option('commenting_comments_label'); ?>
    <?php if ($label == ''):?>
        <h2><?php echo __('Comments'); ?></h2>
    <?php else: ?>
        <h2><?php echo $label; ?></h2>
    <?php endif; ?>
    <?php echo fire_plugin_hook('commenting_prepend_to_comments', array('comments' =>$comments)); ?>

    <?php if($threaded) :?>
        <?php echo $this->partial('threaded-comments.php', array('comments' => $comments, 'parent_id'=>null)); ?>
    <?php else: ?>
        <?php foreach($comments as $comment): ?>
            <div id="comment-<?php echo $comment->id; ?>" class="comment<?php echo ($comment->flagged == 1) ? ' flagged' : ''; ?>">
            <?php echo $this->partial('comment.php', 'commenting', array('comment' => $comment)); ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
