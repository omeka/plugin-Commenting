<?php
queue_css_file('commenting');
queue_js_file('commenting');
$pageTitle = __('Comments') . ' ' . __('(%s total)', $total_results);
$wpApiKey = (get_option('commenting_wpapi_key') != '');
echo head(array('title' => $pageTitle, 'bodyclass' => 'commenting browse'));
?>

<div id='primary'>
    
    <?php if (!Omeka_Captcha::isConfigured()): ?>
        <p class="flash alert"><?php echo __("You have not entered your %s API keys under %s. We recommend adding these keys, or the commenting form will be vulnerable to spam.", '<a href="https://www.google.com/recaptcha/about/">reCAPTCHA</a>', "<a href='" . url('security#fieldset-captcha') . "'>" . __('security settings') . "</a>");?></p>
    <?php endif; ?>
    <?php echo flash(); ?>
    
    <?php if (count($comments) > 0): ?>
    <div class="pagination"><?php echo pagination_links(); ?></div>
                
    <?php echo common('quick-filters'); ?>
    
    <?php if (is_allowed('Commenting_Comment', 'update-approved') ) : //updateapproved is standing in for all moderation options?>
    <div id='commenting-batch-actions' class="table-actions">
        <button class="small batch-action" data-action="approved" data-status="1" type="button" id="batch-approve" disabled><?php echo __("Approve"); ?></button>
        <button class="small batch-action" data-action="approved" data-status="0" type="button" id="batch-unapprove" disabled><?php echo __("Unapprove"); ?></button>
        <?php if ($wpApiKey): ?>
            <button class="small batch-action" data-action="spam" data-status="0" type="button" id="batch-report-spam" disabled><?php echo __("Report Spam"); ?></button>
            <button class="small batch-action" data-action="spam" data-status="1" type="button" id="batch-report-ham" disabled><?php echo __("Report Not Spam"); ?></button>
        <?php endif; ?>
        <button class="small batch-action" data-action="flagged" data-status="0" type="button" id="batch-flag" disabled><?php echo __("Flag"); ?></button>
        <button class="small batch-action" data-action="flagged" data-status="1" type="button" id="batch-unflag" disabled><?php echo __("Unflag"); ?></button>
        <button class="small batch-action" data-action="delete" type="button" id="batch-delete" disabled><?php echo __("Delete"); ?></button>
        </div>
    <?php endif; ?>
    <table>
        <thead>
            <th class="batch-edit-heading"><label for="batch-all-checkbox" class="sr-only"><?php echo __('Select all rows'); ?></label><input type="checkbox" name="batch-all-checkbox" id="batch-all-checkbox" class="batch-select-comment" title="<?php echo __('Select all rows'); ?>"></th>
            <th><?php echo __('Comment'); ?></th>
            <th><?php echo __('Author'); ?></th>
            <th><?php echo __('Item'); ?></th>
            <th><?php echo __('Date Created'); ?></th>
        </thead>
        <tbody>
        <?php foreach ($comments as $comment): ?>
            <?php echo $this->partial('comment.php', array('comment' => $comment, 'wpApiKey' => $wpApiKey)); ?>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php else: ?>
        <h2><?php echo __('You have no comments.'); ?></h2>
    <?php endif; ?>
</div>

<?php echo foot(); ?>
