<?php

function commenting_echo_comments($options = array('approved'=>true))
{
    if(!isset($options['threaded'])) {
        $options['threaded'] = get_option('commenting_threaded');
    }
    $request = Omeka_Context::getInstance()->getRequest();
    $params = $request->getParams();
    $model = ucfirst(Inflector::singularize($params['controller']));
            
    $findArray = array(
        'record_type' => $model,
        'record_id' => $params['id']
    );

    $html = '';
    $html .= "<div id='comments-flash'>". flash(true) . "</div>";
    $html .= "<div class='comments'><h2>Comments</h2>";
    
    $html .= commenting_get_comments($params['id'], 'Item', $options);

    $html .= "</div>";

    echo $html;
    
    
}

function commenting_echo_comment_form()
{
    require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
    $commentSession = new Zend_Session_Namespace('commenting', true);
    
    if(isset($commentSession->form)) {
        $form = unserialize($commentSession->form);
    } else {
        $form = new Commenting_CommentForm();
    }
        
    echo $form;
    if(isset($commentSession->form)) {
        unset($commentSession->form);
    }
    
}

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
    if(isset($options['approved'])) {
        $params['approved'] = $options['approved'];
    }
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
            $html .= "<div class='comment-author'>";
            $html .= commenting_get_gravatar($comment);
            if(!empty($comment->author_name)) {
                $html .= "<p class='comment-author-name'>" . $comment->author_name . "</p>";
            }
            $html .= "</div>";
            
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

function commenting_render_comments($comments, $admin=false)
{
    $html = "";

    foreach($comments as $index=>$comment) {
        $html .= "<div id='comment-{$comment->id}' class='comment'>";
        if($admin) {
            $html .= commenting_render_admin($comment);
        }
        $html .= "<div class='comment-author'>";
        $html .= commenting_get_gravatar($comment);
        if(!empty($comment->author_name)) {
            $html .= "<p class='comment-author-name'>" . $comment->author_name . "</p>";
        }
        $html .= "</div>";
        $html .= "<div class='comment-body'>" . $comment->body . "</div>";

        $html .= "</div>";
    }
        
    return $html;
}

function commenting_comment_uri($comment, $includeHash = true)
{
    $uri = $comment->path;
    if($includeHash) {
        $uri .= "#comment-" . $comment->id;
    }
    return $uri;
}

function commenting_render_admin($comment)
{
    $html = "<div class='commenting-admin'>";
    $html .= "<input class='batch-select-comment' type='checkbox' />";
    $html .= "<ul class='comment-admin-menu'>";
    $html .= (bool) $comment->approved ? "<li class='unapprove'>Unapprove</li>" : "<li class='approve'>Approve</li>";
    $html .= (bool) $comment->spam ? "<li class='report-spam'>Report Spam</li>" : "<li class='report-ham'>Report Ham</li>";
    $html .= "<li><a href='" . commenting_comment_uri($comment) . "'>View</a></li>";
    $html .= "<li><a href='mailto:$comment->author_email'>$comment->author_email</a></li>";
    $html .= "</ul>";
    $html .= "</div>";
    return $html;
}

function commenting_get_gravatar($comment)
{
    $hash = md5(strtolower(trim($comment->author_email)));
    $url = "http://www.gravatar.com/avatar/$hash";
    return "<img class='commenting-gravatar' src='$url' />";
}
