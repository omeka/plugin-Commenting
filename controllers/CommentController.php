<?php

class Commenting_CommentController extends Omeka_Controller_Action
{
    protected $_browseRecordsPerPage = 10;
    
    public function init()
    {
        $this->_modelClass = 'Comment';
        
    }
    
    public function browseAction()
    {
        if(!$this->_hasParam('sort_field')) {
            $this->_setParam('sort_field', 'added');
        }
        
        if(!$this->_hasParam('sort_dir')) {
            $this->_setParam('sort_dir', 'd');
        }
        parent::browseAction();
    }
    
    public function addAction()
    {
        
        
        $destination = $_POST['req_uri'];
        
        $comment = new Comment();
        $form = $this->getForm();
        $valid = $form->isValid($this->getRequest()->getPost());
        if(!$valid) {
            $errors = $form->getErrors();
            foreach($errors as $element=>$elErrors) {
                foreach($elErrors as $error) {
                    $this->flashError($error);
                }
            }
            $destination .= "#comments-flash";
            $commentSession = new Zend_Session_Namespace('commenting', true);
            $commentSession->form = serialize($form);
            $this->_redirect($destination);
        }
        $this->flashSuccess("Your comment is awaiting moderation");
        //need getValue to run the filter
        $data = $_POST;
        $data['body'] = $form->getElement('body')->getValue();
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $data['approved'] = false;
        $comment->setArray($data);
        $comment->checkSpam();
        $comment->save();
        $destination .= "#comment-" . $comment->id;
        $this->redirect->gotoUrl($destination);

    }
    
    public function updateApprovedAction()
    {
        $commentIds = $_POST['ids'];
        $status = $_POST['status'];
        $table = $this->getTable();
        foreach($commentIds as $commentId) {
            $comment = $table->find($commentId);
            $comment->approved = status;
            try {
                $comment->save();
            } catch(Exception $e) {
                $response = array('status'=>'fail', 'message'=>$e->getMessage());
            }
        }
        $this->_helper->json($response);
    }
    
    public function approveAction()
    {
        $id = $_POST['id'];
        $comment = $this->getTable()->find($id);
        $comment->approved = true;
        try {
            $comment->save();
            $response = array('status'=>'ok');
        } catch(Exception $e) {
            $response = array('status'=>'fail', 'message'=>$e->getMessage());
        }
        $this->_helper->json($response);
    }
    
    public function reportHamAction()
    {
        $id = $_POST['id'];
        $comment = $this->getTable()->find($id);
        $comment->isSpam = false;
        $wordPressAPIKey = get_option('commenting_wpapi_key');
        $ak = new Zend_Service_Akismet($wordPressAPIKey, WEB_ROOT );
        $data = $this->getAkismetData();
        try{
            $ak->submitHam($data);
        } catch (Exception $e){
            
        }
        $comment->save();
    }
    
    public function reportSpamAction()
    {
        $id = $_POST['id'];
        $comment = $this->getTable()->find($id);
        $comment->isSpam = true;
        $wordPressAPIKey = get_option('commenting_wpapi_key');
        $ak = new Zend_Service_Akismet($wordPressAPIKey, WEB_ROOT );
        $data = $this->getAkismetData();
        try {
            $ak->submitSpam($data);
        } catch(Exception $e) {
            
        }
        $comment->save();
    }
    
    private function getForm()
    {
        require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
        return new Commenting_CommentForm();
    }
    
}