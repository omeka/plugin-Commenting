<?php


class Commenting_CommentForm extends Omeka_Form
{
    
    public function init()
    {
        parent::init();
        $this->setAction(WEB_ROOT . '/commenting/comment/add');
/*
        $this->addElement('captcha', 'captcha',  array(
            'label' => "Please verify you're a human",
        	'captcha' => array(
                'captcha' => 'Figlet',
            )
        ));
        */
        $this->addElement('text', 'author_url', array('label'=>'Website') );
        $this->addElement('text', 'author_email',
            array(
            	'label'=>'Email',
            	'required'=>true,
            	'description'=>"Valid email address",
                'validators' => array(
                    array('validator' => 'EmailAddress'
                    )
                )
            ));
        $this->addElement('text', 'author_name', array('label'=>'Your name'));
        $this->addElement('textarea', 'body', array('label'=>'Comment', 'required'=>true));
        $request = Omeka_Context::getInstance()->getRequest();
        $params = $request->getParams();
        $model = ucfirst(Inflector::singularize($params['controller']));
        $this->addElement('text', 'record_id', array('value'=>$params['id'], 'hidden'=>true));
        $this->addElement('text', 'record_type', array('value'=>$model, 'hidden'=>true));
        $this->addElement('text', 'parent_comment_id', array('value'=>null, 'hidden'=>true));
        $this->addElement('submit', 'submit');
        
    }
    
    
}