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
                'label'=>'Email (required)',
                'required'=>true,
                'validators' => array(
                    array('validator' => 'EmailAddress'
                    )
                )
            );
        $nameOptions =  array('label'=>'Your name');

        if($user) {
            $emailOptions['value'] = $user->email;
            $nameOptions['value'] = $user->name;
        }
        $this->addElement('text', 'author_name', $nameOptions);
        $this->addElement('text', 'author_url', $urlOptions);
        $this->addElement('text', 'author_email', $emailOptions);
        $this->addElement('textarea', 'body',
            array('label'=>'Comment',
                  'description'=>"Allowed tags: &lt;p&gt;, &lt;a&gt;, &lt;em&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;",
                 // 'rows' => 10,
                  'id'=>'comment-form-body',
                  'required'=>true,
                  'filters'=> array(
                      array('StripTags', array('allowTags' => array('p', 'em', 'strong', 'a','ul','ol','li'))),
                  ),
                )
            );
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        
        $record_id = $this->_getRecordId($params);
        $record_type = $this->_getRecordType($params);

        $this->addElement('text', 'record_id', array('value'=>$record_id, 
                                                    'hidden'=>true, 
                                                    'class' => 'hidden',
                                                    'decorators'=>array('ViewHelper') ));
        $this->addElement('text', 'path', array('value'=>  $request->getPathInfo(), 'hidden'=>true, 'class' => 'hidden', 'decorators'=>array('ViewHelper')));
        if(isset($params['module'])) {
            $this->addElement('text', 'module', array('value'=>$params['module'], 'hidden'=>true, 'class' => 'hidden', 'decorators'=>array('ViewHelper')));
        }
        $this->addElement('text', 'record_type', array('value'=>$record_type, 'hidden'=>true, 'class' => 'hidden', 'decorators'=>array('ViewHelper')));
        $this->addElement('text', 'parent_comment_id', array('id'=>'parent-id', 'value'=>null, 'hidden'=>true, 'class' => 'hidden', 'decorators'=>array('ViewHelper')));
        fire_plugin_hook('commenting_form', array('comment_form' => $this) );
        $this->addElement('submit', 'submit');
    }
    
    
    private function _getRecordId($params)
    {
    
        if(isset($params['module'])) {
            switch($params['module']) {
                case 'exhibit-builder':
                    //ExhibitBuilder uses slugs in the params, so need to negotiate around those
                    //to dig up the record_id and model
                    if(!empty($params['page_slug'])) {
                        $page = exhibit_builder_get_current_page();
                        $id = $page->id;
                    } else if(!empty($params['item_id'])) {
                        $id = $params['item_id'];
                    } else {
                        $section = exhibit_builder_get_current_section();
                        $id = $section->id;
                    }
                    break;
    
                default:
                    $id = $params['id'];
                    break;
            }
        } else {
            $id = $params['id'];
        }
        return $id;
    
    
    }
    
    private function _getRecordType($params)
    {
        if(isset($params['module'])) {
            switch($params['module']) {
                case 'exhibit-builder':
                    //ExhibitBuilder uses slugs in the params, so need to negotiate around those
                    //to dig up the record_id and model
                    if(!empty($params['page_slug'])) {
                        $page = exhibit_builder_get_current_page();
                        $model = 'ExhibitPage';
                    } else if(!empty($params['item_id'])) {
                        $model = 'Item';
                    } else {
                        $section = exhibit_builder_get_current_section();
                        $model = 'ExhibitSection';
                    }
                    break;
    
                default:
                    $model = Inflector::camelize($params['module']) . ucfirst( $params['controller'] );
                    break;
            }
        } else {
            $model = ucfirst(Inflector::singularize($params['controller']));
        }
        return $model;
    }    
    
}