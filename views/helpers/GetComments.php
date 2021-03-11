<?php

class Commenting_View_Helper_GetComments extends Zend_View_Helper_Abstract
{
    public function getComments($record, $options = array())
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
        if(isset($options['order'])) {
            $select->order("added " . $options['order']);
        }
        return $commentTable->fetchObjects($select);
    }
}
