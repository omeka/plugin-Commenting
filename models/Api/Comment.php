<?php

class Api_Comment extends Omeka_Record_Api_AbstractRecordAdapter implements Zend_Acl_Resource_Interface
{
    // Get the REST representation of a record.
    public function getRepresentation(Omeka_Record_AbstractRecord $comment)
    {

        $representation = array(
                'id' => $comment->id,
                'url' => self::getResourceUrl("/comments/{$comment->id}"),
                'record_id' => $comment->record_id,
                'record_type' => $comment->record_type,
                'path' => $comment->path,
                'ip' => $comment->ip,
                'user_agent' => $comment->user_agent,
                'added' => self::getDate($comment->added),
                'body' => $comment->body,
                'author_name' => $comment->author_name,
                'author_url' => $comment->author_url,
                'parent_comment_id' => $comment->parent_comment_id ? $comment->parent_comment_id : null,
                'approved' => (bool) $comment->approved,
                'flagged' => (bool) $comment->flagged,
                'is_spam' => (bool) $comment->is_spam                
                );
        $typeResource = Inflector::tableize($comment->record_type);
        $representation['record_url'] = array(
                'id' => $comment->record_id,
                'type' => $comment->record_type,                
                'url' => self::getResourceUrl("/$typeResource/{$comment->record_id}")
                );
        if($comment->user_id) {
            $representation['user'] = array(
                    'id' => $comment->user_id,
                    'url' => self::getResourceUrl("/users/{$comment->user_id}")
                    );
        } else {
            $representation['user'] = null;
        }
        
        $user = current_user();
        if($user && is_allowed('Commenting_Comment', 'update-approved')) {
            $representation['author_email'] = $comment->author_email;
        }
        
        
        return $representation;
    }
    
    public function getResourceId()
    {
        return "Commenting_Comment";
    }
    
    // Set data to a record during a POST request.
    public function setPostData(Omeka_Record_AbstractRecord $record, $data)
    {
        // Set properties directly to a new record.
    }
    
    // Set data to a record during a PUT request.
    public function setPutData(Omeka_Record_AbstractRecord $record, $data)
    {
        // Set properties directly to an existing record.
    }    
}