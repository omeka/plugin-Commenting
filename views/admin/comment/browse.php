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
<?php echo commenting_render_comments($comments); ?>
</div>

<?php foot(); ?>