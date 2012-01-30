<?php


class Commenting_CommentForm extends Omeka_Form
{

    public function init()
    {
        parent::init();
        $this->setAction(WEB_ROOT . '/commenting/comment/add');
        $this->setAttrib('id', 'comment-form');
        $user = current_user();

        //assume registered users are trusted and don't make them play recaptcha
        if(!$user) {
            $this->addElement('captcha', 'captcha',  array(
                'class' => 'hidden',
                'label' => "Please verify you're a human",
            	'captcha' => array(
                    'captcha' => 'ReCaptcha',
                    'pubkey' => get_option('recaptcha_public_key'),
                    'privkey' => get_option('recaptcha_private_key')
                )
            ));

        }

        $urlOptions = array(
        		'label'=>'Website',
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
        $model = commenting_get_model($request);
        $record_id = commenting_get_record_id($request);

        $this->addElement('text', 'record_id', array('value'=>$record_id, 'hidden'=>true, 'class' => 'hidden'));
        $this->addElement('text', 'path', array('value'=>  $request->getPathInfo(), 'hidden'=>true, 'class' => 'hidden'));
        if(isset($params['module'])) {
        	$this->addElement('text', 'module', array('value'=>$params['module'], 'hidden'=>true, 'class' => 'hidden'));
        }
        $this->addElement('text', 'record_type', array('value'=>$model, 'hidden'=>true, 'class' => 'hidden'));
        $this->addElement('text', 'parent_comment_id', array('id'=>'parent-id', 'value'=>null, 'hidden'=>true, 'class' => 'hidden'));
        $this->addElement('submit', 'submit');

    }
}