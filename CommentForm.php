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
            'label' => __('Website'),
        );
        $emailOptions = array(
            'label' => __('Email (required)'),
            'required' => true,
            'attribs' => array(
                'required' => 'required',
            ),
            'validators' => array(
                array('validator' => 'EmailAddress')
            )
        );
        $nameOptions = array('label' => __('Your name'));

        if ($user) {
            $emailOptions['value'] = $user->email;
            $nameOptions['value'] = $user->name;
        }
        $this->addElement('text', 'author_name', $nameOptions);
        $this->addElement('text', 'author_url', $urlOptions);
        $this->addElement('text', 'author_email', $emailOptions);
        $this->addElement('textarea', 'body', array(
            'label' => __('Comment'),
            'description' =>  __("Allowed tags:") . " &lt;p&gt;, &lt;a&gt;, &lt;em&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;",
            'id' => 'comment-form-body',
            'rows' => 6,
            'required' => true,
        ));

        //assume registered users are trusted and don't make them play recaptcha
        if (!$user && get_option('recaptcha_public_key') && get_option('recaptcha_private_key')) {
            $this->addElement('captcha', 'captcha',  array(
                'label' => __("Please verify you're a human"),
                'captcha' => Omeka_Captcha::getCaptcha()
            ));
            $this->getElement('captcha')->removeDecorator('ViewHelper');
        }

        $this->addElement('hidden', 'record_id', array('decorators' => array('ViewHelper') ));
        $this->addElement('hidden', 'path', array('decorators' => array('ViewHelper')));
        $this->addElement('hidden', 'record_type', array('decorators' => array('ViewHelper')));
        $this->addElement('hidden', 'parent_comment_id', array('id' => 'parent-id', 'value' => null, 'decorators' => array('ViewHelper')));
        fire_plugin_hook('commenting_form', array('comment_form' => $this));
        $this->addElement('submit', 'submit', array('label' => __('Submit')));
    }

    /**
     * Override wrapper classes for simplicity and to guarantee unique
     * selectors for applying cross-theme styles.
     */
    public function getDefaultElementDecorators()
    {
        return array(
            array('Description', array('tag' => 'p', 'class' => 'commenting-explanation', 'escape' => false)),
            'ViewHelper',
            array('Errors', array('class' => 'error')),
            'Label',
            array(array('FieldTag' => 'HtmlTag'), array('tag' => 'div', 'class' => 'commenting-field'))
        );
    }
}
