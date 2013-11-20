<?php

class Commenting_View_Helper_GetComments extends Zend_View_Helper_Abstract
{
    
    private function _getRecordId($params)
    {
        if(isset($params['module'])) {
            switch($params['module']) {
                case 'exhibit-builder':
                    //ExhibitBuilder uses slugs in the params, so need to negotiate around those
                    //to dig up the record_id and model
                    if(isset($this->view->exhibit_page)) {
                        $id = $this->view->exhibit_page->id;
                    } else {
                        $id = $params['item_id'];
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
        if(isset($params['module'])) {
            switch($params['module']) {
                case 'exhibit-builder':
                    //ExhibitBuilder uses slugs in the params, so need to negotiate around those
                    //to dig up the record_id and model
                    if(!empty($params['page_slug_1'])) {
                        $model = 'ExhibitPage';
                    } else {
                        $model = 'Item';
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
    
    public function getComments($options = array(), $record_id = null, $record_type = null) 
    {
            
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
            
        if(!$record_id) {
            $record_id = $this->_getRecordId($params);
        }
        
        if(!$record_type) {
            $record_type = $this->_getRecordType($params);
        }

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
        if(isset($options['order'])) {
            $select->order("ORDER BY added " . $options['order']);
        }
        return $commentTable->fetchObjects($select);        
    }
}