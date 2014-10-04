<?php

class Commenting_View_Helper_GetComments extends Zend_View_Helper_Abstract
{
    public function getComments($options = array(), $record = null)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        if ($record) {
            $record_type = get_class($record);
            $record_id = $record->id;
        }
        else {
            $record_type = $this->_getRecordType($params);
            $record_id = $this->_getRecordId($params);
        }

        $db = get_db();
        $commentTable = $db->getTable('Comment');
        $searchParams = array(
                'record_type' => $record_type,
                'record_id' => $record_id,
        );
        if (isset($options['approved'])) {
            $searchParams['approved'] = $options['approved'];
        }

        if (!is_allowed('Commenting_Comment', 'update-approved')) {
            $searchParams['flagged'] = 0;
            $searchParams['is_spam'] = 0;
        }

        $select = $commentTable->getSelectForFindBy($searchParams);
        if (isset($options['order'])) {
            $select->order("ORDER BY added " . $options['order']);
        }
        return $commentTable->fetchObjects($select);
    }

    /**
     * Helper to get record id from request params.
     *
     * @see plugins/Commenting/forms/CommentForm.php
     *
     * @todo To be merged.
     */
    private function _getRecordId($params)
    {
        if (isset($params['module'])
                && $params['module'] == 'commenting'
                && $params['controller'] == 'comment'
                && $params['action'] == 'add'
            ) {
            return $params['record_id'];
        }

        if (isset($params['module'])) {
            switch ($params['module']) {
                case 'exhibit-builder':
                    $view = get_view();
                    // ExhibitBuilder uses slugs in the params, so need to
                    // negotiate around those to dig up the record_id and model.
                    if (isset($view->exhibit) && isset($view->exhibit_pages)) {
                        $id = $view->exhibit->id;
                    }
                    elseif (isset($view->exhibit_page)) {
                        $id = $view->exhibit_page->id;
                    }
//todo: check the ifs for an exhibit showing an item
                    elseif (isset($params['item_id'])) {
                        $id = $params['item_id'];
                    }
                    else {
                        $id = isset($params['id']) ? $params['id'] : null;
                    }
                    break;

                default:
                    $id = isset($params['id']) ? $params['id'] : null;
                    break;
            }
        }
        // Default for collections, items and files.
        else {
            $id = $params['id'];
        }
        return $id;
    }

    /**
     * Helper to get record type from request params.
     *
     * @see plugins/Commenting/forms/CommentForm.php
     *
     * @todo To be merged.
     */
    private function _getRecordType($params)
    {
        if (isset($params['module'])
                && $params['module'] == 'commenting'
                && $params['controller'] == 'comment'
                && $params['action'] == 'add'
            ) {
            return $params['record_type'];
        }

        if (isset($params['module'])) {
            switch ($params['module']) {
                case 'exhibit-builder':
                    $view = get_view();
                    // ExhibitBuilder uses slugs in the params, so need to
                    // negotiate around those to dig up the record_id and model.
                    if (isset($view->exhibit) && isset($view->exhibit_pages)) {
                        $model = 'Exhibit';
                    }
                    elseif (isset($view->exhibit_page)) {
                        $model = 'ExhibitPage';
                    }
// Todo: check the ifs for an exhibit showing an item.
                    else {
                        $model = 'Item';
                    }
                    break;

                default:
                    $model = Inflector::camelize($params['module']) . ucfirst( $params['controller'] );
                    break;
            }
        }
        // Default for collections, items and files.
        else {
            $model = ucfirst(Inflector::singularize($params['controller']));
        }
        return $model;
    }
}
