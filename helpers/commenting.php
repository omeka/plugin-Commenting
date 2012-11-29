<?php

function commenting_echo_comments($options = array('approved'=>true), $comments = null)
{
    if( (get_option('commenting_allow_public') == 1) || (get_option('commenting_allow_public_view') == 1) || has_permission('Commenting_Comment', 'show') ) {
        if(!isset($options['threaded'])) {
            $options['threaded'] = get_option('commenting_threaded');
        }
        if(!$comments) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $params = $request->getParams();
            $model = commenting_get_model($request);
            $record_id = commenting_get_record_id($request);
            $comments = commenting_get_comments($record_id, $model, $options);
        }
        $html = '';
        $html .= "<div id='comments-flash'>". flash(true) . "</div>";
        $html .= "<div class='comments'><h2>" . get_option('commenting_comments_label') . "</h2>";
        $html = apply_filters('commenting_prepend_to_comments', $html, $comments);
        
        if(isset($options['threaded']) && $options['threaded']) {
            $html .= commenting_render_threaded_comments($comments);
        } else {
            $html .= commenting_render_comments($comments);
        }
        $html .= "</div>";
        echo $html;
    }
}

function commenting_echo_comment_form()
{
    if( (get_option('commenting_allow_public') == 1) || has_permission('Commenting_Comment', 'add') ) {
        require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
        $commentSession = new Zend_Session_Namespace('commenting');
         $form = new Commenting_CommentForm();
         if($commentSession->post) {
             $form->isValid(unserialize($commentSession->post));
         }
         echo $form;
         unset($commentSession->post);
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
    if(!has_permission('Commenting_Comment', 'unflag')) {
        $params['flagged'] = 0;
    }
    
    $select = $commentTable->getSelectForFindBy($params);
    if(isset($options['order'])) {
        $select->order("ORDER BY added " . $options['order']);
    }

    return $commentTable->fetchObjects($select);
}

function commenting_render_threaded_comments($comments, $parent_id = null)
{

    $html = "";

    foreach($comments as $index=>$comment) {
        if($comment->parent_comment_id == $parent_id) {
            $comment_html = "<div id='comment-{$comment->id}' class='comment'>";
            $comment_html .= "<div class='comment-author'>";
            $comment_html .= commenting_get_gravatar($comment);
            if(!empty($comment->author_name)) {
                if(empty($comment->author_url)) {
                    $text = $comment->author_name;
                } else {
                    $text = "<a href='{$comment->author_url}'>{$comment->author_name}</a>";
                }
                $comment_html .= "<p class='comment-author-name'>$text</p>";
            }

            $comment_html .= "</div>";
            $comment_html .= "<div class='comment-body" ; 
            if($comment->flagged) {
                $comment_html .= " comment-flagged";
            } 
                
            $comment_html .= "'>" . $comment->body . "</div>";
            $comment_html .= "<p><span class='comment-time'>" . $comment->added . "</span>";
            $comment_html .= "</p>";
            $comment_html .= "<p><span class='comment-reply'>Reply</span>";
            if(!is_admin_theme()) {
                $comment_html .= "<span class='comment-flag' " ;
                if($comment->flagged) {
                    $comment_html .= "style='display:none;'";
                }
                $comment_html .= ">Flag inappropriate</span>";
                if(has_permission('Commenting_Comment', 'unflag')  )  {
                    $comment_html .= "<span class='comment-unflag' ";
                    if(!$comment->flagged) {
                        $comment_html .= "style='display:none' ";
                    }
                
                    $comment_html .= ">Remove flag</span>";
                }                
            }

            $comment_html .= "</p>";
            $comment_html = apply_filters('commenting_append_to_comment', $comment_html, $comment);
            $html .= $comment_html;
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
            if(empty($comment->author_url)) {
                $text = $comment->author_name;
            } else {
                $text = "<a href='{$comment->author_url}'>{$comment->author_name}</a>";
            }
            $html .= "<p class='comment-author-name'>$text</p>";
        }
        $html .= "</div>";
        $html .= "<div class='comment-body" ; 
        if($comment->flagged) {
            $html .= " comment-flagged";
        } 
            
        $html .= "'>" . $comment->body . "</div>";
        $html .= "<p><span class='comment-time'>" . $comment->added . "</span></p>";
        if(!is_admin_theme()) {
            $html .= "<p><span class='comment-flag' ";
            if($comment->flagged) {
                $html .= "style='display:none;'";
            }
            $html .= ">Flag inappropriate</span>";
            if(has_permission('Commenting_Comment', 'unflag')  )  {
                $html .= "<span class='comment-unflag' ";
                if(!$comment->flagged) {
                    $html .= "style='display:none' ";
                }
            
                $html .= ">Remove flag</span>";
            }
            
            
            $html .= "</p>";
            
            $html .= "<p><span class='comment-flag'>Flag inappropriate</span>";
            if(has_permission('Commenting_Comment', 'unflag') && ($comment->flagged == 1) )  {
                $html .= "<span class='comment-unflag'>Remove flag</span>";
            }
            $html .= "</p>";            
        }

        $html = apply_filters('commenting_append_to_comment', $html, array('comment'=>$comment));
        $html .= "</div>";
    }
    return $html;

}

function commenting_comment_uri($comment, $includeHash = true)
{
    $uri = PUBLIC_BASE_URL . $comment->path;

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
    $html .= (bool) $comment->approved ? "<li><span class='approved'>Approved</span><span class='unapprove'>Unapprove</span></li>" : "<li><span class='unapproved'>Not Approved</span><span class='approve'>Approve</span></li>";
    if(get_option('commenting_wpapi_key') != '') {
        $html .= (bool) $comment->is_spam ? "<li><span class='spam'>Spam</span><span class='report-ham'>Report Ham</span></li>" : "<li><span class='ham'>Ham</span><span class='report-spam'>Report Spam</span></li>";
    }
    if($comment->flagged) {
        $html .= "<li><span class='comment-unflag'>Remove flag</span><span style='display:none' class='comment-flag'>Flag inappropriate</span></li>";
    } else {
        $html .= "<li><span style='display:none' class='comment-unflag'>Remove flag</span><span class='comment-flag'>Flag inappropriate</span></li>";
    }
    
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

function commenting_get_model($request = null)
{
    if(is_null($request)) {
        $request = Omeka_Context::getInstance()->getRequest();
    }
    $params = $request->getParams();
    if(isset($params['module'])) {
        switch($params['module']) {
            case 'exhibit-builder':
                //ExhibitBuilder uses slugs in the params, so need to negotiate around those
                //to dig up the record_id and model
                if(!empty($params['page_slug'])) {
                    $page = exhibit_builder_get_current_page();
                    $model = 'ExhibitPage';
                } else if(!empty($params['item_id'])) {
                    $model = 'Item';
                } else {
                    $section = exhibit_builder_get_current_section();
                    $model = 'ExhibitSection';
                }
                break;

            default:
                $model = Inflector::camelize($params['module']) . ucfirst( $params['controller'] );
                break;
        }
    } else {
        $model = ucfirst(Inflector::singularize($params['controller']));
    }
    return $model;
}

function commenting_get_record_id($request = null)
{
    if(is_null($request)) {
        $request = Omeka_Context::getInstance()->getRequest();
    }
    $params = $request->getParams();

    if(isset($params['module'])) {
        switch($params['module']) {
            case 'exhibit-builder':
                //ExhibitBuilder uses slugs in the params, so need to negotiate around those
                //to dig up the record_id and model
                if(!empty($params['page_slug'])) {
                    $page = exhibit_builder_get_current_page();
                    $id = $page->id;
                } else if(!empty($params['item_id'])) {
                    $id = $params['item_id'];
                } else {
                    $section = exhibit_builder_get_current_section();
                    $id = $section->id;
                }
                break;

            default:
                $id = $params['id'];
                break;
        }
    } else {
        $id = $params['id'];
    }
    return $id;
}
