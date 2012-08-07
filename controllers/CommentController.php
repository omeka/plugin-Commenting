<?php

class Commenting_CommentController extends Omeka_Controller_Action
{
    protected $_browseRecordsPerPage = 10;


    public function init()
    {
        if (version_compare(OMEKA_VERSION, '2.0-dev', '>=')) {
            $this->_helper->db->setDefaultModelName('Comment');
        } else {
            $this->_modelClass = 'Comment';
        }
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
        $destination = $_POST['path'];
        $destArray = array(
            'module' => Inflector::camelize($_POST['module']),
            'controller'=> strtolower(Inflector::pluralize($_POST['record_type'])),
            'action' => 'show',
            'id' => $_POST['record_id']
        );

        $comment = new Comment();
        $form = $this->getForm();
        $valid = $form->isValid($this->getRequest()->getPost());
        if(!$valid) {
            $destination .= "#comments-flash";
            $commentSession = new Zend_Session_Namespace('commenting');
            $commentSession->post = serialize($_POST);

            $this->redirect->gotoUrl($destination);
        }
        $noApprovalNeeded = $this->isAllowed('noappcomment');
        if(!$noApprovalNeeded) {
            $this->flashSuccess("Your comment is awaiting moderation");
        }

        //need getValue to run the filter
        $data = $_POST;
        $data['body'] = $form->getElement('body')->getValue();
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $data['approved'] = $noApprovalNeeded;
        $comment->setArray($data);
        $comment->checkSpam();
        $comment->flagged = 0;
        $comment->save();
        $destination .= "#comment-" . $comment->id;
        $this->redirect->gotoUrl($destination);
    }

    public function updatespamAction()
    {
        $commentIds = $_POST['ids'];
        $spam = $_POST['spam'];
        $table = $this->getTable();
        $wordPressAPIKey = get_option('commenting_wpapi_key');
        $ak = new Zend_Service_Akismet($wordPressAPIKey, WEB_ROOT );
        $response = array('errors'=> array());
        if($commentIds) {    
            foreach($commentIds as $commentId) {
                $comment = $table->find($commentId);
                $data = $comment->getAkismetData();
                if($spam) {
                    $submitMethod = 'submitSpam';
                } else {
                    $submitMethod = 'submitHam';
                }
                try{
                    $ak->$submitMethod($data);
                    //only save the update if updating to Akismet is successful
                    $comment->is_spam = $spam;
                    $comment->save();
                    $response['status'] = 'ok';
                } catch (Exception $e){
                    $response['status'] = 'fail';
                    $response['errors'][] = array('id'=>$comment->id);
                    $response['message'] = $e->getMessage();
                    _log($e->getMessage());
                }
            }
        } else {
            $response = array('status'=>'empty', 'message'=>'No Comments Found');
        }
        $this->_helper->json($response);
    }

    public function updateapprovedAction()
    {
        $wordPressAPIKey = get_option('commenting_wpapi_key');
        $commentIds = $_POST['ids'];
        $status = $_POST['approved'];
        $table = $this->getTable(); 
        if($commentIds) {          
            foreach($commentIds as $commentId) {
                $comment = $table->find($commentId);
                $comment->approved = $status;
                //if approved, it isn't spam
                if( ($status == 1) && ($comment->is_spam == 1) ) {
                    $comment->is_spam = 0;
                    $ak = new Zend_Service_Akismet($wordPressAPIKey, WEB_ROOT );
                    $data = $comment->getAkismetData();
                    try {
                        $ak->submitHam($data);
                        $response = array('status'=>'ok');
                        $comment->save();
                    } catch (Exception $e) {
                        _log($e->getMessage());
                        $response = array('status'=>'fail', 'message'=>$e->getMessage());
                    }
    
                } else {
                    try {
                        $comment->save();
                        $response = array('status'=>'ok');
                    } catch(Exception $e) {
                        $response = array('status'=>'fail', 'message'=>$e->getMessage());
                        _log($e->getMessage());
                    }
                }
    
            }
        } else {
            $response = array('status'=>'empty', 'message'=>'No Comments Found');
        }
        $this->_helper->json($response);
    }

    public function updateFlaggedAction()
    {
        $commentIds = $_POST['ids'];
        $flagged = $_POST['flagged'];
        
        if($commentIds) {
            foreach($commentIds as $id) {
                $comment = $this->findById($id);
                $comment->flagged = $flagged;        
                $comment->save();
            }
        } else {
            $response = array('status'=>'empty', 'message'=>'No Comments Found');
        }
        if($flagged) {
            $action = 'flagged';
        } else {
            $action = 'unflagged';
        }
        $response = array('status'=>'ok', 'action'=>$action, 'ids'=>$commentIds);
        $this->_helper->json($response);
    }
    
    public function flagAction() {
        $commentId = $_POST['id'];
        $comment = $this->findById($commentId);
        $comment->flagged = true;
        $comment->save();
        $response = array('status'=>'ok', 'id'=>$commentId, 'action'=>'flagged');
        $this->_helper->json($response);
    }
    
    public function unflagAction() {
        $commentId = $_POST['id'];
        $comment = $this->findById($commentId);
        $comment->flagged = 0;
        $comment->save();
        $response = array('status'=>'ok', 'id'=>$commentId, 'action'=>'unflagged');
        $this->_helper->json($response);        
    }

    private function getForm()
    {
        require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
        return new Commenting_CommentForm();
    }

}