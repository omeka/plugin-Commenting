<div class='comments'>
    <?php $label = get_option('commenting_comments_label'); ?>
    <?php if ($label == ''):?>
        <h2><?php echo __('Comments'); ?></h2>
    <?php else: ?>
        <h2><?php echo $label; ?></h2>
    <?php endif; ?>
    <?php if ($flash = flash()): ?>
    <div id='comments-flash'><?php echo $flash; ?></div>
    <?php endif; ?>
    <?php echo fire_plugin_hook('commenting_prepend_to_comments', array('comments' => $comments)); ?>
    <?php if (empty($comments)): ?>
    <p><?php echo __('No comment yet! Be the first to add one!'); ?></p>
    <?php endif; ?>
    <?php if ($threaded) :?>
        <?php echo $this->partial('common/threaded-comments.php', array('comments' => $comments, 'parent_id' => null)); ?>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
        <div id="comment-<?php echo $comment->id; ?>" class='comment'>
            <?php echo $this->partial('common/comment.php', array('comment' => $comment)); ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
