<?php
queue_css_file('commenting');
queue_js_file('commenting');
$pageTitle = __('Comments') . ' ' . __('(%s total)', $total_results);
echo head(array('title' => $pageTitle, 'bodyclass' => 'commenting browse'));
?>

<div id='primary'>
    <div class="pagination"><?php echo pagination_links(); ?></div>
    <?php echo flash(); ?>

    <?php if (!Omeka_Captcha::isConfigured()): ?>
    <p class="alert"><?php echo __("You have not entered your %s API keys under %s. We recommend adding these keys, or the commenting form will be vulnerable to spam.", '<a href="http://recaptcha.net/">reCAPTCHA</a>', "<a href='" . url('security#recaptcha_public_key') . "'>" . __('security settings') . "</a>");?></p>
    <?php endif; ?>

    <?php if (is_allowed('Commenting_Comment', 'update-approved') ) : //updateapproved is standing in for all moderation options?>
    <div id='commenting-batch-actions'>
        <button class="red" type="button" id="batch-delete" disabled><?php echo __("Delete"); ?></button>
        <button class="blue" type="button" id="batch-approve" disabled><?php echo __("Approve"); ?></button>
        <button class="blue" type="button" id="batch-unapprove" disabled><?php echo __("Unapprove"); ?></button>
        <?php if (get_option('commenting_wpapi_key') != ''): ?>
        <button class="blue" type="button" id="batch-report-spam" disabled><?php echo __("Report Spam"); ?></button>
        <button class="blue" type="button" id="batch-report-ham" disabled><?php echo __("Report Not Spam"); ?></button>
        <?php endif; ?>
        <button class="blue" type="button" id="batch-flag" disabled><?php echo __("Flag"); ?></button>
        <button class="blue" type="button" id="batch-unflag" disabled><?php echo __("Unflag"); ?></button>
    </div>
    <?php endif; ?>

    <?php echo common('quick-filters'); ?>
    <div style="clear: both">
        <input id="batch-select" type="checkbox"> <label for="batch-select"><?php echo __("Select All"); ?></label>
    </div>

    <table>

    <?php
    foreach ($comments as $comment) {
        echo $this->partial('comment.php', array('comment' => $comment));
    }
    ?>
    </table>
</div>

<?php echo foot(); ?>
