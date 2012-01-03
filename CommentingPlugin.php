<?php

if(!class_exists('Omeka_Plugin_Abstract')) {
        
    abstract class Omeka_Plugin_Abstract
    {
        /**
         * Database object accessible to plugin authors.
         *
         * @var Omeka_Db
         */
        protected $_db;
        
        /**
         * Plugin hooks.
         *
         * Plugin authors should give an array containing hook names as values.
         * Each hook should have a corresponding hookCamelCased() method defined
         * in the child class. E.g: the after_save_form_record hook should
         * have a corresponding hookAfterSaveFormRecord() method.
         *
         * @var array
         */
        protected $_hooks;
        
        /**
         * Plugin filters.
         *
         * Plugin authors should give an array containing filter names as values.
         * Each filter should have a corresponding filterCamelCased() method
         * defined in the child class. E.g: the admin_navigation_main filter should
         * have a corresponding filterAdminNavigationMain() method.
         *
         * @var array
         */
        protected $_filters;
        
        /**
         * Plugin options.
         *
         * Plugin authors should give an array containing option names as keys and
         * their default values as values, if any. For example:
         * <code>
         * array('option_name1' => 'option_default_value1',
         *       'option_name2' => 'option_default_value2',
         *       'option_name3',
         *       'option_name4')
         * </code>
         *
         * @var array
         */
        protected $_options;
        
        /**
         * Construct the plugin object.
         *
         * Sets the database object. Plugin authors must call parent::__construct()
         * in the child class's constructor, if used.
         */
        public function __construct()
        {
            $this->_db = Omeka_Context::getInstance()->getDb();
        }
        
        /**
         * Set up the plugin to hook into Omeka.
         *
         * Adds the plugin's hooks and filters. Plugin writers must call this method
         * after instantiating their plugin class.
         */
        public function setUp()
        {
            $this->_addHooks();
            $this->_addFilters();
        }
        
        /**
         * Set options with default values.
         *
         * Plugin authors may want to use this convenience method in their install
         * hook callback.
         */
        protected function _installOptions()
        {
            $options = $this->_options;
            if (!is_array($options)) {
                return;
            }
            foreach ($options as $name => $value) {
                // Don't set options without default values.
                if (!is_string($name)) {
                    continue;
                }
                set_option($name, $value);
            }
        }
        
        /**
         * Delete all options.
         *
         * Plugin authors may want to use this convenience method in their uninstall
         * hook callback.
         */
        protected function _uninstallOptions()
        {
            $options = self::$_options;
            if (!is_array($options)) {
                return;
            }
            foreach ($options as $name => $value) {
                delete_option($name);
            }
        }
        
        /**
         * Validate and add hooks.
         */
        private function _addHooks()
        {
            $hookNames = $this->_hooks;
            if (!is_array($hookNames)) {
                return;
            }
            foreach ($hookNames as $hookName) {
                $functionName = 'hook' . Inflector::camelize($hookName);
                if (!is_callable(array($this, $functionName))) {
                    throw new Omeka_Plugin_Exception('Hook callback "' . $functionName . '" does not exist.');
                }
                add_plugin_hook($hookName, array($this, $functionName));
            }
        }
        
        /**
         * Validate and add filters.
         */
        private function _addFilters()
        {
            $filterNames = $this->_filters;
            if (!is_array($filterNames)) {
                return;
            }
            foreach ($filterNames as $filterName) {
                $functionName = 'filter' . Inflector::camelize($filterName);
                if (!is_callable(array($this, $functionName))) {
                    throw new Omeka_Plugin_Exception('Filter callback "' . $functionName . '" does not exist.');
                }
                add_filter($filterName, array($this, $functionName));
            }
        }
    }
       
}


class CommentingPlugin extends Omeka_Plugin_Abstract
{
    
    protected $_hooks = array(
        'install',
        'uninstall',
        'public_append_to_items_show',
        'public_append_to_collections_show',
        'public_theme_header',
        'admin_theme_header',
        'config_form',
        'config',
        'define_acl'
    );
    
    protected $_filters = array(
        'admin_navigation_main'
    );
    
    
    public function hookInstall()
    {
        $db = get_db();
        $sql = "
            CREATE TABLE IF NOT EXISTS `$db->Comment` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `record_id` int(10) unsigned NOT NULL,
              `record_type` tinytext COLLATE utf8_unicode_ci NOT NULL,
              `path` tinytext COLLATE utf8_unicode_ci NOT NULL,
              `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `body` text COLLATE utf8_unicode_ci NOT NULL,
              `author_email` tinytext COLLATE utf8_unicode_ci,
              `author_url` tinytext COLLATE utf8_unicode_ci,
              `author_name` tinytext COLLATE utf8_unicode_ci,
              `ip` tinytext COLLATE utf8_unicode_ci,
              `user_agent` tinytext COLLATE utf8_unicode_ci,
              `user_id` int(11) DEFAULT NULL,
              `parent_comment_id` int(11) DEFAULT NULL,
              `approved` tinyint(1) NOT NULL DEFAULT '0',
              `is_spam` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `record_id` (`record_id`,`user_id`,`parent_comment_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
        $db->exec($sql);
        
        $commentRoles = array('super');
        $moderateRoles = array('super');
        set_option('commenting_comment_roles', serialize($commentRoles));
        set_option('commenting_moderate_roles', serialize($moderateRoles));
        
    }
    
    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->Comment`";
        $db->exec($sql);
    }
    
    public function hookPublicThemeHeader()
    {
        queue_css('commenting');
        queue_js('commenting');
    }
    
    public function hookAdminThemeHeader()
    {
        queue_css('commenting');
    }
    
    public function hookPublicAppendToItemsShow()
    {
        $options = array('threaded'=> get_option('commenting_threaded'), 'approved'=>true);
        commenting_echo_comments($options);
        commenting_echo_comment_form();
    }
    
    public function hookPublicAppendToCollectionsShow()
    {
        $options = array('threaded'=> get_option('commenting_threaded'), 'approved'=>true);
        commenting_echo_comments($options);
        commenting_echo_comment_form();
    }
    
    public function hookConfig($post)
    {
        foreach($post as $key=>$value) {
            if( ($key == 'commenting_comment_roles') || ($key == 'commenting_moderate_roles') || ($key == 'commenting_view_roles')  ) {
                $value = serialize($value);
            }
            set_option($key, $value);
        }
    }
    
    public function hookConfigForm()
    {
        include COMMENTING_PLUGIN_DIR . '/config_form.php';
        
    }
    
    public function hookDefineAcl($acl)
    {
        $resourceList = array(
            'Commenting_Comment' => array(
                'add', 'updateapproved', 'updatespam', 'show'
            )
        );
        
        $acl->loadResourceList($resourceList);
        $commentRoles = unserialize(get_option('commenting_comment_roles'));
        $moderateRoles = unserialize(get_option('commenting_moderate_roles'));
        $viewRoles = unserialize(get_option('commenting_view_roles'));

        foreach($viewRoles as $role) {
            $acl->allow($role, 'Commenting_Comment', array('show'));
        }
        
        foreach($commentRoles as $role) {
            $acl->allow($role, 'Commenting_Comment', array('add'));
        }
        
        foreach($moderateRoles as $role) {
            $acl->allow($role, 'Commenting_Comment', array('updateapproved'));
            $acl->allow($role, 'Commenting_Comment', array('updatespam'));
        }
        
        if(get_option('commenting_allow_public')) {
            $acl->allow(null, 'Commenting_Comment', array('show', 'add'));
        }
    }
    
    public function filterAdminNavigationMain($tabs)
    {
        $tabs['Comments'] = uri('commenting/comment/browse');
        return $tabs;
    }
}