<?php
$title = __('Batch Delete Comments');
echo head(array(
    'title' => $title,
    'bodyclass' => 'comments batch-edit',
));
?>
<div title="<?php echo $title; ?>">

<form id="batch-edit-form" action="<?php echo html_escape(url('commenting/comment/batch-delete-submit')); ?>" method="post" accept-charset="utf-8">
    <section class="seven columns alpha">
        <fieldset id="comment-list" class="panel">
            <h2 class="two columns alpha"><?php echo __('Comments'); ?></h2>
            <div class="five columns omega">
                <ul>
                <?php
                $commentCheckboxes = array();
                foreach ($commentIds as $id) {
                    if (!($comment = get_record_by_id('comment', $id))) {
                        continue;
                    }

                    if (is_allowed('Commenting_Comment', 'update-approved')) {
                        $commentCheckboxes[$id] = metadata($comment, 'body', array('no_escape' => true, 'snippet' => '100'));
                    }
                    release_object($comment);
                }
                echo '<li>' . $this->formMultiCheckbox('comments[]', null, array('checked' => 'checked'), $commentCheckboxes, '</li><li>') . '</li>'; ?>
                </ul>

                <p class="explanation"><?php echo __('Checked comments will be deleted.'); ?></p>
            </div>
        </fieldset>
        <input type="hidden" name="delete" value="1">
    </section>

    <section class="three columns omega">
        <div  id="save" class="panel">
            <input type="submit" class="big red button" value="<?php echo __('Delete Comments'); ?>">
        </div>
    </section>
</form>

</div>
<?php echo foot(); ?>

