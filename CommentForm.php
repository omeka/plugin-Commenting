<?php


class Commenting_CommentForm extends Omeka_Form
{

    public function init()
    {
        parent::init();
        $this->setAction(WEB_ROOT . '/commenting/comment/add');
        $this->setAttrib('id', 'comment-form');
        $user = current_user();

        $urlOptions = array(
                'label'=>__('Website'),
            );
        $emailOptions = array(
                'label'=>__('Email (required)'),
                'required'=>true,
                'validators' => array(
                    array('validator' => 'EmailAddress'
                    )
                )
            );
        $nameOptions =  array('label'=> __('Your name'));

        if($user) {
            $emailOptions['value'] = $user->email;
            $nameOptions['value'] = $user->name;
        }
        $this->addElement('text', 'author_name', $nameOptions);
        $this->addElement('text', 'author_url', $urlOptions);
        $this->addElement('text', 'author_email', $emailOptions);
        $this->addElement('textarea', 'body',
            array('label'=>__('Comment'),
                  'description'=> __("Allowed tags:") . " &lt;p&gt;, &lt;a&gt;, &lt;em&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;",
                  'id'=>'comment-form-body',
                  'rows' => 6,
                  'required'=>true,
                  'filters'=> array(
                      array('StripTags',
                              array('allowTags' => array('p', 'span', 'em', 'strong', 'a','ul','ol','li'),
                                    'allowAttribs' => array('style', 'href')
                                     ),
                              ),
                      ),
                )
            );

        //assume registered users are trusted and don't make them play recaptcha
        if(!$user && get_option('recaptcha_public_key') && get_option('recaptcha_private_key')) {
            $this->addElement('captcha', 'captcha',  array(
                'label' => __("Please verify you're a human"),
                'captcha' => array(
                    'captcha' => 'ReCaptcha',
                    'pubkey' => get_option('recaptcha_public_key'),
                    'privkey' => get_option('recaptcha_private_key'),
                    'ssl' => true //make the connection secure so IE8 doesn't complain. if works, should branch around http: vs https:
                )
            ));
            $this->getElement('captcha')->removeDecorator('ViewHelper');
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $record_id = $this->_getRecordId($params);
        $record_type = $this->_getRecordType($params);

        $this->addElement('hidden', 'record_id', array('value'=>$record_id, 'decorators'=>array('ViewHelper') ));
        $this->addElement('hidden', 'path', array('value'=>  $request->getPathInfo(), 'decorators'=>array('ViewHelper')));
        if(isset($params['module'])) {
            $this->addElement('hidden', 'module', array('value'=>$params['module'], 'decorators'=>array('ViewHelper')));
        }
        $this->addElement('hidden', 'record_type', array('value'=>$record_type, 'decorators'=>array('ViewHelper')));
        $this->addElement('hidden', 'parent_comment_id', array('id'=>'parent-id', 'value'=>null, 'decorators'=>array('ViewHelper')));
        fire_plugin_hook('commenting_form', array('comment_form' => $this) );
        $this->addElement('submit', 'submit', array('label'=>__('Submit')));
    }


    private function _getRecordId($params)
    {
        if(isset($params['module'])) {
            switch($params['module']) {
                case 'exhibit-builder':
                    //ExhibitBuilder uses slugs in the params, so need to negotiate around those
                    //to dig up the record_id and model
                    if(!empty($params['page_slug_1'])) {
                        $page = get_current_record('exhibit_page', false);
                        $id = $page->id;
                    } else if(!empty($params['item_id'])) {
                        $id = $params['item_id'];
                    } else {
//todo: check the ifs for an exhibit showing an item
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
                    if(!empty($params['page_slug_1'])) {
                        $page = get_current_record('exhibit_page', false);
                        $model = 'ExhibitPage';
                    } else if(!empty($params['item_id'])) {
                        $model = 'Item';
                    } else {
//TODO: check for other possibilities
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

    /**
     * Override wrapper classes for simplicity and to guarantee unique
     * selectors for applying cross-theme styles.
     */
    public function getDefaultElementDecorators()
    {
        return array(
            array('Description', array('tag' => 'p', 'class' => 'commenting-explanation', 'escape'=>false)),
            'ViewHelper',
            array('Errors', array('class' => 'error')),
            'Label',
            array(array('FieldTag' => 'HtmlTag'), array('tag' => 'div', 'class' => 'commenting-field'))
        );
    }
}
