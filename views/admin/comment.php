<?php
try {
    $record = get_db()->getTable($comment->record_type)->find($comment->record_id);
} catch (Zend_Db_Statement_Mysqli_Exception $e) {
    $record = null;
}
if ($record) {
    $recordType = get_class($record);
} else {
    $recordType = '';
    $label = __('Unknown Record');
}
// try hard to dig up a likely label from the metadata or properties
try {
    $label = metadata($record, array('Dublin Core', 'Title'));
} catch(BadMethodCallException $e) {

} catch (InvalidArgumentException $e) {

}

if(empty($label)) {
    try {
        $label = metadata($record, 'name');
    } catch(InvalidArgumentException $e) {

    }
}

if(empty($label)) {
    try {
        $label = metadata($record, 'title');
    } catch(InvalidArgumentException $e) {

    }
}

if(empty($label)) {
    try {
        $label = metadata($record, 'label');
    } catch(InvalidArgumentException $e) {

    }
}

//sad trombone. couldn't find a label!
if(empty($label)) {
    $label = __('[Untitled]');
}

$recordLink = sprintf('<a target="_blank" href="%s">%s</a>', record_url($comment, 'show'), $label);

if(!empty($comment->author_name)) {
    $author = html_escape($comment->author_name);
    if(empty($comment->author_url)) {
        $authorText = $author;
    } else {
        $url = html_escape($comment->getAuthorUrl());
        $authorText = "<a href='{$url}'>{$author}</a>";
    }
} else {
    $authorText = __('Anonymous');
}

$isApproved = $comment->approved;
$isSpam = $comment->is_spam;
$isFlagged = $comment->flagged;

$commentStatus = array();
$commentStatus[] = ($isApproved) ? 'approved' : 'unapproved';
$commentStatus[] = ($isFlagged) ? 'flagged' : 'unflagged';
$commentStatus[] = ($isSpam) ? 'spam' : 'not-spam';
?>

<tr id="comment-<?php echo $comment->id; ?>" class="comment <?php echo implode(' ', $commentStatus); ?>">
    <td><input class='batch-select-comment' type='checkbox'></td>
    <td>
        <?php echo $comment->body; ?>
        <ul class="action-links group">
            <li class="approval-action status">
                <span class="green"><?php echo __("Approved"); ?></span>
                <span class="red"><?php echo __("Not Approved"); ?></span>
            </li>
            <li class="flag-action status">
                <span class="red"><?php echo  __("Flagged Inappropriate"); ?></span>
                <span class="green"><?php echo __("Not Flagged"); ?></span>
            </li>
            <?php if(get_option('commenting_wpapi_key') != ''): ?>
            <li class="spam-action status">
                <span class="red"><?php echo __("Spam"); ?></span>
                <span class="green"><?php echo __("Not Spam"); ?></span>
            </li>
            <?php endif; ?>
        </ul>
        <ul class="action-links group">
            <li>
                <a class="approval-action action" href="#" data-action="approved">
                    <span class="red"><?php echo __('Approve'); ?></span>
                    <span class="green"><?php echo __('Unapprove'); ?></span>
                </a>
            </li>
            <?php if(get_option('commenting_wpapi_key') != ''): ?>
            <li>
                <a class="spam-action action" href="#" data-action="spam">
                    <span class="green"><?php echo __('Report Spam'); ?></span>
                    <span class="red"><?php echo __('Report Not Spam'); ?></span>
                </a>
            </li>
            <?php endif;?>
            <li>
                <a class="flag-action action" href="#" data-action="flagged">
                    <span class="red"><?php echo __('Unflag Inappropriate'); ?></span>
                    <span class="green"><?php echo __('Flag Inappropriate'); ?></span>
                </a>
            </li>
            <li>
                <a href='<?php echo record_url($comment, 'delete-confirm'); ?>'><?php echo __("Delete"); ?></a>
            </li>
        </ul>
    </td>
    <td><?php echo $authorText; ?></td>
    <td><?php echo $recordLink; ?></td>
    <td><?php echo format_date($comment->added, Zend_Date::DATETIME_MEDIUM); ?>
</tr>
