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
        if(!$user && get_option('recaptcha_public_key') && get_option('recaptcha_private_key')) {
            $this->addElement('captcha', 'captcha',  array(
                'class' => 'hidden',
                'label' => "Please verify you're a human",
                'captcha' => array(
                    'captcha' => 'ReCaptcha',
                    'pubkey' => get_option('recaptcha_public_key'),
                    'privkey' => get_option('recaptcha_private_key'),
                    'ssl' => true //make the connection secure so IE8 doesn't complain. if works, should branch around http: vs https:
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
            if(plugin_is_active('UserProfiles')) {
                $urlOptions['value'] = WEB_ROOT . "/user-profiles/profiles/user/id/{$user->id}";
            }
            
            $emailOptions['value'] = $user->email;
            if(version_compare(OMEKA_VERSION, '2.0-dev', '>=')) {
                $nameOptions['value'] = $user->name;
            } else {
                $nameOptions['value'] = $user->first_name . " " . $user->last_name;
            }

            $this->addElement('text', 'user_id', array('value'=>$user->id,  'hidden'=>true));
        }
        $this->addElement('text', 'author_url', $urlOptions);


        $this->addElement('text', 'author_email', $emailOptions);

        $this->addElement('text', 'author_name', $nameOptions);
        $this->addElement('textarea', 'commenting_body',
            array('label'=>'Comment',
                  'description'=>"Allowed tags: <p>, <a>, <em>, <strong>",
                  'rows'=>5,
                  'filters'=> array(
                      array('StripTags', array('allowTags' => array('p', 'em', 'strong', 'a'))),
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
        fire_plugin_hook('commenting_append_to_form', $this);
        $this->addElement('submit', 'submit');
    }
}