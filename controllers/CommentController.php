<?php

class Commenting_CommentController extends Omeka_Controller_Action
{
    
    public function init()
    {
        $this->_modelClass = 'Comment';
        
    }
    
    public function addAction()
    {
        $varName = strtolower($this->_modelClass);
        $class = $this->_modelClass;
        $destArray = array(
        	'controller'=> strtolower(Inflector::pluralize($_POST['record_type'])),
            'action' => 'show',
            'id' => $_POST['record_id']
        
        );
        $destination = implode('/', $destArray);
        
        $record = new $class();

        $form = $this->getForm();
    
        
        //can I assign the form to the view being redirected to, so that the append hooks
        //can work with whatever data is there about how to render the form/error messages, etc?
        $valid = $form->isValid($this->getRequest()->getPost());
        if(!$valid) {
            $errors = $form->getErrors();
            //echo $form->render();
           // die();
            //$form->setErrorMessages($errors);
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

        $record->saveForm($_POST);
        $destination .= "#comment-" . $record->id;
        $this->redirect->gotoUrl($destination);

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
    
    private function getForm()
    {
        require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
        return new Commenting_CommentForm();
    }
    
}