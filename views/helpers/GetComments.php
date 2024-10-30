<?php

class Commenting_View_Helper_GetComments extends Zend_View_Helper_Abstract
{
    public function getComments($record)
    {
        $html = '<div id="comments-container">';
        $html .= '<div id="comment-main-container">';
        if (get_option('commenting_allow_public_view') == 1
            || is_allowed('Commenting_Comment', 'show')
        ) {
            $options = array('threaded' => get_option('commenting_threaded'), 'approved' => true);
            $comments = $this->fetchComments($record, $options);
            $html .= $this->view->partial('comments.php', array('comments' => $comments, 'threaded' => $options['threaded']));
        }
        $html .= "</div>";

        if (is_allowed('Commenting_Comment', 'add')) {
            $html .= '<div id="comments-status" aria-live="polite" aria-atomic="true"></div>';
            $html .= $this->view->getCommentForm($record);
        }
        $html .= "</div>";
        return $html;
    }

    public function fetchComments($record, $options = array())
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $record_id = $record->id;
        $record_type = get_class($record);

        $db = get_db();
        $commentTable = $db->getTable('Comment');
        $searchParams = array(
            'record_type' => $record_type,
            'record_id' => $record_id,
        );
        if(isset($options['approved'])) {
            $searchParams['approved'] = $options['approved'];
        }
        
        if(!is_allowed('Commenting_Comment', 'update-approved')) {
            $searchParams['flagged'] = 0;
            $searchParams['is_spam'] = 0;
        }
        $select = $commentTable->getSelectForFindBy($searchParams);
        if (isset($options['order'])) {
            $select->order("added " . $options['order']);
        } else {
            $select->order('added');
        }
        return $commentTable->fetchObjects($select);
    }
}
