<?php

class Commenting_CommentController extends Omeka_Controller_AbstractActionController
{
    protected $_browseRecordsPerPage = 10;

    public function init()
    {
        $this->_helper->db->setDefaultModelName('Comment');
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

    public function batchDeleteAction()
    {
        $commentIds = $this->_getParam('comments');
        $this->view->assign(compact('commentIds'));
        $this->render('batch-delete');
    }
    
    public function batchDeleteSubmitAction()
    {
        $ids = $_POST['comments'];
        foreach($ids as $id) {
            $record = $this->_helper->db->findById($id);
            $record->delete();
        }
        $this->_helper->flashMessenger(__('Comments were successfully deleted.'), 'success');
        $this->_helper->redirector('browse');
    }

    public function addAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $comment = new Comment();
        if($user = current_user()) {
            $comment->user_id = $user->id;
        }
        $comment->flagged = 0;
        $form = $this->getForm();
        $valid = $form->isValid($this->getRequest()->getPost());
        if (!$valid) {
            $this->getResponse()->setHttpResponseCode(422);
            $this->_helper->json(array('error' => __('Your submission was invalid. Please try again.')));
        }

        $record = get_record_by_id($_POST['record_type'], $_POST['record_id']);
        if (!$record) {
            $this->getResponse()->setHttpResponseCode(422);
            $this->_helper->json(array('error' => __('Your submission was invalid. Please try again.')));
        }

        $user = current_user();
        $role = $user ? $user->role : null;
        $reqAppCommentRoles = unserialize(get_option('commenting_reqapp_comment_roles'));
        $requiresApproval = in_array($role, $reqAppCommentRoles);
        //via Daniel Lind -- https://groups.google.com/forum/#!topic/omeka-dev/j-tOSAVdxqU
        $reqAppPublicComment = (bool) get_option('commenting_require_public_moderation');
        $requiresApproval = $requiresApproval || (!is_object(current_user()) && $reqAppPublicComment);
        //end Daniel Lind contribution

        $purifier = $this->_getHtmlPurifier();

        $data = $_POST;
        $data['body'] = $purifier->purify($form->getElement('body')->getValue());
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $data['approved'] = !$requiresApproval;
        $comment->setArray($data);
        $comment->checkSpam();
        $comment->save();

        $this->sendEmailNotifications($comment);

        if($requiresApproval) {
            $this->_helper->json(array('message' => __('Your comment is awaiting moderation')));
        }
        $this->_helper->json(array(
            'message' => __('Your comment was posted succesfully'),
            'fragment' => $requiresApproval ? "comment-status" : "comment-{$comment->id}",
            'comments' => $this->view->getComments($record),
        ));
    }

    public function updateSpamAction()
    {
        $commentIds = $_POST['ids'];
        $spam = $_POST['spam'];
        $table = $this->_helper->db->getTable();
        $wordPressAPIKey = get_option('commenting_wpapi_key');
        $ak = new Zend_Service_Akismet($wordPressAPIKey, WEB_ROOT );
        $response = array('errors'=> array());
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
                _log($e);
            }
        }
        $this->_helper->json($response);
    }

    public function updateApprovedAction()
    {
        $wordPressAPIKey = get_option('commenting_wpapi_key');
        $commentIds = $_POST['ids'];
        $status = $_POST['approved'];
        $table = $this->_helper->db->getTable();
        if(!$commentIds) {
            return;
        }
        foreach($commentIds as $commentId) {
            $comment = $table->find($commentId);
            if (!$comment) {
                $response =  $commentId;
                $this->_helper->json($response);
            }
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
        $this->_helper->json($response);
    }

    public function updateFlaggedAction()
    {
        $commentIds = $_POST['ids'];
        $flagged = $_POST['flagged'];

        if($commentIds) {
            foreach($commentIds as $id) {
                $comment = $this->_helper->db->getTable('Comment')->find($id);
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
        $comment = $this->_helper->db->getTable('Comment')->find($commentId);
        $comment->flagged = true;
        $comment->save();
        $this->emailFlagged($comment);
        $response = array('status'=>'ok', 'id'=>$commentId, 'action'=>'flagged');
        $this->_helper->json($response);
    }

    public function unflagAction() {
        $commentId = $_POST['id'];
        $comment = $this->_helper->db->getTable('Comment')->find($commentId);
        $comment->flagged = 0;
        $comment->save();
        $response = array('status'=>'ok', 'id'=>$commentId, 'action'=>'unflagged');
        $this->_helper->json($response);
    }

    private function emailFlagged($comment)
    {
        $mail = new Zend_Mail('UTF-8');
        $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
        $mail->setFrom(get_option('administrator_email'), get_option('site_title'));
        $mail->addTo(get_option('commenting_flag_email'));
        $subject = __("A comment on %s has been flagged as inappropriate", get_option('site_title'));
        $body = "<p>" . __("The comment %s has been flagged as inappropriate.", "<blockquote>{$comment->body}</blockquote>" ) . "</p>";
        $body .= "<p>" . __("You can manage the comment %s", "<a href='" . WEB_ROOT ."{$comment->path}'>" . __('here') . "</a>" ) . "</p>";
        $mail->setSubject($subject);
        $mail->setBodyHtml($body);
        try {
            $mail->send();
        } catch(Exception $e) {
            _log($e);
        }
    }

    private function sendEmailNotifications(Comment $comment)
    {
        $recipients = get_option('new_comment_notification_recipients');
        if (strlen($recipients)) {
            // run the whole stuff in background, we don't want delay our visitors
            Zend_Registry::get('bootstrap')->getResource('jobs')
                ->sendLongRunning('Job_CommentNotification', [
                    'id' => $comment->id,
                    'recipients' => (array) explode("\n", $recipients),
                    'webRoot' => rtrim(WEB_ROOT, '/'), // needed for correct URLs from background job to admin/public page
            ]);
        }
    }

    private function getForm()
    {
        require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
        return new Commenting_CommentForm();
    }

    protected function _getDeleteConfirmMessage($comment)
    {
        return __('This will delete the selected comment.');
    }

    private function _getHtmlPurifier()
    {
        require_once 'htmlpurifier/HTMLPurifier.auto.php';

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'a[href],p,em,strong,ul,ol,li');
        $config->set('HTML.TidyLevel', 'none');
        $config->set('HTML.Nofollow', true);
        $config->set('Cache.DefinitionImpl', null);
        return new HTMLPurifier($config);
    }
}
