<?php

class Commenting_View_Helper_GetCommentForm extends Zend_View_Helper_Abstract
{
    public function getCommentForm($record)
    {
        if ((get_option('commenting_allow_public') == 1) || is_allowed('Commenting_Comment', 'add')) {
            require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
            $commentSession = new Zend_Session_Namespace('commenting');
            $form = new Commenting_CommentForm();
            $this->_setRequestValues($form, $record);
            if ($commentSession->post) {
                $form->isValid(unserialize($commentSession->post));
            }
            unset($commentSession->post);
            return $form;
        }
    }

    private function _setRequestValues($form, $record)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $record_id = $record->id;
        $record_type = get_class($record);

        $defaults = array(
            'record_id' => $record_id,
            'record_type' => $record_type,
            'path' => $request->getPathInfo(),
        );

        $form->setDefaults($defaults);
    }
}
