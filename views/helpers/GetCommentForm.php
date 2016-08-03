<?php

class Commenting_View_Helper_GetCommentForm extends Zend_View_Helper_Abstract
{
    
    public function getCommentForm()
    {        
        if( (get_option('commenting_allow_public') == 1) || is_allowed('Commenting_Comment', 'add') ) {
            require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
            $commentSession = new Zend_Session_Namespace('commenting');
            $form = new Commenting_CommentForm();
            if($commentSession->post) {
                $form->isValid(unserialize($commentSession->post));
            }
            unset($commentSession->post);
            return $form;
        }                
    }
}