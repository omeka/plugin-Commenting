<?php


class Commenting_CommentForm extends Omeka_Form
{
    
    public function init()
    {
        parent::init();
        $this->setAction(WEB_ROOT . '/commenting/comment/add');
        $this->setAttrib('id', 'comment-form');
/*
        $this->addElement('captcha', 'captcha',  array(
            'label' => "Please verify you're a human",
        	'captcha' => array(
                'captcha' => 'ReCaptcha',
                'pubkey' => get_option('recaptcha_public_key'),
                'privkey' => get_option('recaptcha_private_key')
            )
        ));
//        */
        
        $urlOptions = array(
        		'label'=>'Website',
             //   'validators' => array('validator' => 'Hostname')
            );
        $emailOptions = array(
            	'label'=>'Email',
            	'required'=>true,
                'validators' => array(
                    array('validator' => 'EmailAddress'
                    )
                )
            );
        $nameOptions =  array('label'=>'Your name');
        $user = current_user();
        if($user) {
            $urlOptions['value'] = WEB_ROOT;
            $emailOptions['value'] = $user->email;
            $nameOptions['value'] = $user->first_name . " " . $user->last_name;
            $this->addElement('text', 'user_id', array('value'=>$user->id,  'hidden'=>true));
        }
        $this->addElement('text', 'author_url', $urlOptions);


        $this->addElement('text', 'author_email', $emailOptions);

        $this->addElement('text', 'author_name', $nameOptions);
        $this->addElement('textarea', 'body',
            array('label'=>'Comment',
                  'description'=>"Allowed tags: <p>, <a>, <em>, <strong>, <ul>, <ol>, <li>",
            	 'required'=>true,
                  'filters'=> array(
                      array('StripTags', array('p', 'em', 'strong', 'a','ul','ol','li')),
                  ),
                )
            );
        
        
        $request = Omeka_Context::getInstance()->getRequest();
        $params = $request->getParams();
        $model = ucfirst(Inflector::singularize($params['controller']));
        $this->addElement('text', 'record_id', array('value'=>$params['id'], 'hidden'=>true));
        $this->addElement('text', 'record_type', array('value'=>$model, 'hidden'=>true));
        $this->addElement('text', 'parent_comment_id', array('id'=>'parent-id', 'value'=>null, 'hidden'=>true));
        $this->addElement('submit', 'submit');
        
    }
    
    
}