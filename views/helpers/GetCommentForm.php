<?php

class Commenting_View_Helper_GetCommentForm extends Zend_View_Helper_Abstract
{
    public function getCommentForm($record)
    {
        if (is_allowed('Commenting_Comment', 'add')) {
            require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
            $form = new Commenting_CommentForm();
            $this->_setRequestValues($form, $record);
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
