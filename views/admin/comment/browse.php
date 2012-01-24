<?php
queue_css('commenting');
queue_js('commenting');
    head(array('title' => 'Comments', 'bodyclass' => 'primary'));

?>
<div id='primary'>
<div class="pagination"><?php echo pagination_links(); ?></div>
    <?php echo flash(); ?>
    <div id="browse-meta" class="group">
        <div id="browse-meta-lists">
            <ul class="navigation">
                <li><strong>Quick Filter</strong></li>
            <?php
                echo nav(array(
                    'All' => uri('commenting/comment/browse'),
                    'Approved' => uri('commenting/comment/browse?approved=1'),
                    'Needs Approval' => uri('commenting/comment/browse?approved=0')
                ));
            ?>
            </ul>
        </div>
    </div>
<h1>Comments</h1>
<?php if(has_permission('Commenting_Comment', 'updateapproved') ) :?>
<div id='comment-batch-actions'><input id='batch-select' type='checkbox' /> Select All | With Selected:
<ul class='comment-batch-actions'>
<li onclick="Commenting.batchApprove()">Approve</li>
<li onclick="Commenting.batchUnapprove()">Unapprove</li>
<?php if(get_option('commenting_wpapi_key') != ''): ?>
<li onclick="Commenting.batchReportSpam()">Report Spam</li>
<li onclick="Commenting.batchReportHam()">Report Ham</li>
<?php endif; ?>
</ul>
</div>
<?php endif; ?>

<?php echo commenting_render_comments($comments, true); ?>

</div>

<?php foot(); ?>