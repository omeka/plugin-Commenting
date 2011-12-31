<?php

class CommentTable extends Omeka_Db_Table
{
    
    protected $_alias = 'ct';
    
    public function applySearchFilters($select, $params)
    {
        $columns = $this->getColumns();
        foreach($columns as $column) {
            if(array_key_exists($column, $params)) {
                $select->where($this->_alias . ".$column = ? ", $params[$column]);
            }
        }
        return $select;
    }
    
    public function applySorting($select, $sortField, $sortDir)
    {
        parent::applySorting($select, $sortField, $sortDir);
    }
    
}