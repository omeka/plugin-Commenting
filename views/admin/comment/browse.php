<?php
queue_css_file('commenting');
queue_js_file('commenting');
$pageTitle = __('Comments') . ' ' . __('(%s total)', $total_results);
echo head(array('title' => $pageTitle, 'bodyclass' => 'commenting browse'));

?>
<div id='primary'>
<div class="pagination"><?php echo pagination_links(); ?></div>


<?php echo flash(); ?>
<?php if(!Omeka_Captcha::isConfigured()): ?>
<p class="alert">You have not entered your <a href="http://recaptcha.net/">reCAPTCHA</a> API keys under <a href="<?php echo url('security#recaptcha_public_key'); ?>">security settings</a>. We recommend adding these keys, or the commenting form will be vulnerable to spam.</p>
<?php endif; ?>

    
    
    
<?php if(is_allowed('Commenting_Comment', 'update-approved') ) : //updateapproved is standing in for all moderation options?>
<div id='commenting-batch-actions'>
<a class="small blue button disabled" id="batch-delete" >Delete</a>
<a class="small blue button disabled" id="batch-approve" >Approve</a>
<a class="small blue button disabled" id="batch-unapprove" >Unapprove</a>
<?php if(get_option('commenting_wpapi_key') != ''): ?>
<a class="small blue button disabled" id="batch-report-spam" onclick="Commenting.batchReportSpam()">Report Spam</a>
<a class="small blue button disabled" id="batch-report-ham" onclick="Commenting.batchReportHam()">Report Not Spam</a>
<?php endif; ?>
<a class="small blue button disabled" id="batch-flag" >Flag</a>
<a class="small blue button disabled" id="batch-unflag" >Unflag</a>
</div>
<?php endif; ?>
<?php echo common('quick-filters'); ?>
<div style="clear: both">
    <input id='batch-select' type='checkbox' /> Select All
</div>
<?php 
    foreach($comments as $comment) {
        echo $this->partial('comment.php', array('comment' => $comment));
    }    
?>

</div>

<?php echo foot(); ?>