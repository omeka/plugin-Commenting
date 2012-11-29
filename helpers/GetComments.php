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
    
    private function _getRecordType($params)
    {
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
        $select = $commentTable->getSelectForFindBy($searchParams);
        if(isset($options['order'])) {
            $select->order("ORDER BY added " . $options['order']);
        }
        return $commentTable->fetchObjects($select);        
        
        
    }
}