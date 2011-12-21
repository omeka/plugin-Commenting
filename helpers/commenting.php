<?php

/**
 *
 * Get the comments for a record
 *
 * $options like:
 * array(
 * 		'sort' => 'ASC', //the order to sort on the time added column
 * 		'threaded'=> true, //whether to show threaded comments
 *
 *
 *
 * @param string $record_type
 * @param int $record_id
 * @param array $options
 */

function commenting_get_comments($record_id, $record_type = 'Item', $options=array())
{
    $db = get_db();
    $commentTable = $db->getTable('Comment');
    $params = array(
        'record_type' => $record_type,
        'record_id' => $record_id,
    );
    $select = $commentTable->getSelectForFindBy($params);
    if(isset($options['order'])) {
        $select->order("ORDER BY added " . $options['order']);
    }
    
    $comments = $commentTable->fetchObjects($select);
    if(isset($options['threaded']) && $options['threaded']) {
        return commenting_render_threaded_comments($comments);
    } else {
        return commenting_render_comments($comments);
    }
   
}

function commenting_render_threaded_comments($comments, $parent_id = null)
{

    $html = "";

    foreach($comments as $index=>$comment) {
        if($comment->parent_comment_id == $parent_id) {
            $html .= "<div id='comment-{$comment->id}' class='comment'>";
            if(!empty($comment->author_name)) {
                $html .= "<p class='comment-author'>From: " . $comment->author_name . "</p>";
            }
            
            $html .= "<div class='comment-body'>" . $comment->body . "</div>";
            $html .= "<p class='comment-reply'>Reply</p>";
            $html .= "<div class='comment-children'>";

            $html .= commenting_render_threaded_comments($comments, $comment->id);
            $html .= "</div>";

            $html .= "</div>";
            unset($comments[$index]);
        }
    }
        
    return $html;
}

function commenting_render_comments($comments)
{
    $html = "";

    foreach($comments as $index=>$comment) {
        $html .= "<div id='comment-{$comment->id}' class='comment'>";
        if(!empty($comment->author_name)) {
            $html .= "<p class='comment-author'>From: " . $comment->author_name . "</p>";
        }
        $html .= "<div class='comment-body'>" . $comment->body . "</div>";
        $html .= "</div>";
    }
        
    return $html;
}
