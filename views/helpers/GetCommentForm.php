<?php

class Commenting_View_Helper_GetCommentForm extends Zend_View_Helper_Abstract
{
    public function getCommentForm()
    {
        if ((get_option('commenting_allow_public') == 1) || is_allowed('Commenting_Comment', 'add')) {
            require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
            $commentSession = new Zend_Session_Namespace('commenting');
            $form = new Commenting_CommentForm();
            $this->_setRequestValues($form);
            if ($commentSession->post) {
                $form->isValid(unserialize($commentSession->post));
            }
            unset($commentSession->post);
            return $form;
        }
    }

    private function _setRequestValues($form)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $record_id = $this->_getRecordId($params);
        $record_type = $this->_getRecordType($params);

        $defaults = array(
            'record_id' => $record_id,
            'record_type' => $record_type,
            'path' => $request->getPathInfo(),
        );

        $form->setDefaults($defaults);
    }

    private function _getRecordId($params)
    {
        if (isset($params['module'])) {
            switch ($params['module']) {
                case 'exhibit-builder':
                    //ExhibitBuilder uses slugs in the params, so need to negotiate around those
                    //to dig up the record_id and model
                    if (!empty($params['page_slug_1'])) {
                        $page = get_current_record('exhibit_page', false);
                        $id = $page->id;
                    } else if (!empty($params['item_id'])) {
                        $id = $params['item_id'];
                    } else {
                        //todo: check the ifs for an exhibit showing an item
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

    private function _getRecordType($params)
    {
        if (isset($params['module'])) {
            switch ($params['module']) {
                case 'exhibit-builder':
                    //ExhibitBuilder uses slugs in the params, so need to negotiate around those
                    //to dig up the record_id and model
                    if (!empty($params['page_slug_1'])) {
                        $page = get_current_record('exhibit_page', false);
                        $model = 'ExhibitPage';
                    } else if (!empty($params['item_id'])) {
                        $model = 'Item';
                    } else {
                        //TODO: check for other possibilities
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
}
