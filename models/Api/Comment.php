<?php

class Api_Comment extends Omeka_Record_Api_AbstractRecordAdapter implements Zend_Acl_Resource_Interface
{
    // Get the REST representation of a record.
    public function getRepresentation(Omeka_Record_AbstractRecord $comment)
    {
        $user = current_user();
        if($user->role == 'admin' || $user->role == 'super') {
            $allowAll = true;
        } else {
            $allowAll = false;
        }
        $representation = array(
                'id' => $comment->id,
                'url' => self::getResourceUrl("/comments/{$comment->id}"),
                'record_id' => $comment->record_id,
                'record_type' => $comment->record_type,
                'path' => $comment->path,
                'added' => self::getDate($comment->added),
                'body' => $comment->body,
                'author_name' => $comment->author_name,
                'author_url' => $comment->author_url,
                'approved' => (bool) $comment->approved,
                );

        if($allowAll) {
            $representation['ip'] = $comment->ip;
            $representation['user_agent'] = $comment->user_agent;
            $representation['flagged'] = $comment->flagged;
            $representation['is_spam'] = $comment->is_spam;
        }
        
        if($comment->parent_comment_id) {
            $representation['parent_comment'] = array(
                    'id' => $comment->parent_comment_id,
                    'resource' => 'comments',
                    'url' => self::getResourceUrl("/comments/{$comment->parent_comment_id}")
                    );
        } else {
            $representation['parent_comment'] = null;
        }
        
        $typeResource = Inflector::tableize($comment->record_type);
        $representation['record_url'] = array(
                'id' => $comment->record_id,
                'resource' => $typeResource,
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