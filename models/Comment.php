<?php


class Comment extends Omeka_Record_AbstractRecord
{
    public $id;
    public $record_id;
    public $record_type;
    public $path;
    public $added;
    public $body;
    public $author_email;
    public $author_url;
    public $author_name;
    public $ip;
    public $user_agent;
    public $user_id;
    public $parent_comment_id;
    public $approved;
    public $flagged;
    public $is_spam;


    protected function _initializeMixins()
    {
        // Add the search mixin.
        $this->_mixins[] = new Mixin_Search($this);
        $this->_mixins[] = new Mixin_Timestamp($this, 'added', null);
    }    

    protected function afterSave($args)
    {
        // A record's search text is public by default, but there are times
        // when this is not desired, e.g. when an item is marked as
        // private. @todo: Make a check to see if the record is public or private.
        
        if (!$this->approved || $this->is_spam) {
            // Setting the search text to private makes it invisible to
            // most users.
            $this->setSearchTextPrivate();
        }
    
        // Set the record's title. This will be used to identify the record
        // in the search results.
        
        //comments don't have titles
        $this->setSearchTextTitle(snippet($this->body, 0, 40));
    
        // Set the record's search text. Records that implement the
        // Mixin_ElementText mixin during _initializeMixins() will
        // automatically have all element texts added. Note that you
        // can add multiple search texts, which simply appends them.
        //$this->addSearchText($recordTitle);
        
        $this->addSearchText($this->body);
    }    
    
    public function checkSpam()
    {
        $wordPressAPIKey = get_option('commenting_wpapi_key');
        if(!empty($wordPressAPIKey)) {
            $ak = new Zend_Service_Akismet($wordPressAPIKey, WEB_ROOT );
            $data = $this->getAkismetData();
            try {
                $this->is_spam = $ak->isSpam($data);
            } catch (Exception $e) {
                $this->is_spam = 1;
            }
        } else {
            //if not using Akismet, assume only registered users are commenting
            $this->is_spam = 0;
        }
    }

    public function getAkismetData()
    {
        $serverUrlHelper = new Zend_View_Helper_ServerUrl;
        $permalink = $serverUrlHelper->serverUrl() . $this->path;
        $data = array(
            'user_ip' => $this->ip,
            'user_agent' => $this->user_agent,
            'permalink' => $permalink,
            'comment_type' => 'comment',
            'comment_author_email' => $this->author_email,
            'comment_content' => $this->body

        );
        if($this->author_url) {
            $data['comment_author_url'] = $this->author_url;
        }

        if($this->author_name) {
            $data['comment_author_name'] = $this->author_name;
        }
        return $data;
    }
        
    protected function _validate()
    {
        if(trim(strip_tags($this->body)) == '' ) {
            $this->addError('body', "Can't leave an empty comment!");
        }
    }
  
    public function getRecordUrl($action = 'show')
    {
        switch($action) {
            case 'show':
                set_theme_base_url('public');
                $url = url($this->path) . "#comment-" . $this->id;
                revert_theme_base_url();
                return $url;
                break;
                
            default:
                //sadly, I made the plugin name, and so the controller name, different 
                //because of the extant, but not maintained, Comments plugin
                //return parent::getRecordUrl($action);
                return url("commenting/comment/$action/id/{$this->id}");
        }
    }

    public function setArray($data)
    {
        if(empty($data['parent_comment_id'])) {
            $data['parent_comment_id'] = null;
        }
        parent::setArray($data);
    }

}