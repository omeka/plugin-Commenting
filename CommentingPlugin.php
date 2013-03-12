<?php

define('COMMENTING_PLUGIN_DIR', PLUGIN_DIR . '/Commenting');

class CommentingPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'public_items_show',        
        'public_collections_show',
        'public_head',
        'admin_head',
        'config_form',
        'config',
        'define_acl',
        'upgrade'
    );

    protected $_filters = array(
        'admin_navigation_main',
        'search_record_types'
    );
    
    public function hookInstall()
    {
        $db = $this->_db;
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
              `flagged` tinyint(1) NOT NULL DEFAULT '0',
              `is_spam` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `record_id` (`record_id`,`user_id`,`parent_comment_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
        $db->query($sql);
        set_option('commenting_comment_roles', serialize(array('super')));
        set_option('commenting_moderate_roles', serialize(array('super')));
        set_option('commenting_reqapp_comment_roles', serialize(array()));
        set_option('commenting_view_roles', serialize(array('super')));

    }

    public function hookUpgrade($old, $new)
    {
        switch($old) {
            case '1.0' :
                if(!get_option('commenting_comment_roles')) {
                    $commentRoles = array('super');
                    set_option('commenting_comment_roles', serialize($commentRoles));
                }

                if(!get_option('commenting_moderate_roles')) {
                    $moderateRoles = array('super');
                    set_option('commenting_moderate_roles', serialize($moderateRoles));
                }

                if(!get_option('commenting_noapp_comment_roles')) {
                    set_option('commenting_noapp_comment_roles', serialize(array()));
                }

                if(!get_option('commenting_view_roles')) {
                    set_option('commenting_view_roles', serialize(array()));
                }
            break;
            
            case '1.1':
                $db = $this->_db;
                $sql = "ALTER TABLE `comments` ADD `flagged` BOOLEAN NOT NULL AFTER `approved` ";
                $db->query($sql);
                break;
                
        }
    }

    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->Comment`";
        $db->query($sql);
    }

    public function hookPublicHead()
    {
        queue_css_file('commenting');
        queue_js_file('commenting');
        queue_js_file('tiny_mce', 'javascripts/vendor/tiny_mce');
        queue_js_string("Commenting.pluginRoot = '" . WEB_ROOT . "/commenting/comment/'");
    }

    public function hookAdminHead()
    {
        queue_css_file('commenting');
    }

    public static function showComments($args)
    {    
        if( (get_option('commenting_allow_public') == 1) 
                || (get_option('commenting_allow_public_view') == 1) 
                || is_allowed('Commenting_Comment', 'show') ) {
            if(isset($args['view'])) {
                $view = $args['view'];
            } else {
                $view = get_view();
            }
            
            $view->addHelperPath(COMMENTING_PLUGIN_DIR . '/helpers', 'Commenting_View_Helper_');
            $options = array('threaded'=> get_option('commenting_threaded'), 'approved'=>true);
            $comments = $view->getComments($options);
            echo $view->partial('comments.php', array('comments'=>$comments, 'threaded'=>$options['threaded']));
        }
        
        if( (get_option('commenting_allow_public') == 1) 
                || is_allowed('Commenting_Comment', 'add') ) {
            echo "<div id='comment-main-container' class='seven columns'>";
            echo $view->getCommentForm();
            echo "</div>";
        }    
    }
    
    public function hookPublicItemsShow($args)
    {
        $this::showComments($args);
    }

    public function hookPublicCollectionsShow($args)
    {
        $this::showComments($args);
    }

    public function hookConfig($args)
    {
        $post = $args['post'];
        foreach($post as $key=>$value) {
            if( ($key == 'commenting_comment_roles') ||
                ($key == 'commenting_moderate_roles') ||
                ($key == 'commenting_view_roles') ||
                ($key == 'commenting_reqapp_comment_roles')
            ) {
                $value = serialize($value);
            }
            set_option($key, $value);
        }
    }

    public function hookConfigForm()
    {
        include COMMENTING_PLUGIN_DIR . '/config_form.php';
    }

    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $acl->addResource('Commenting_Comment');
        $commentRoles = unserialize(get_option('commenting_comment_roles'));        
        $moderateRoles = unserialize(get_option('commenting_moderate_roles'));
        $viewRoles = unserialize(get_option('commenting_view_roles'));
        $acl->allow(null, 'Commenting_Comment', array('flag'));
        if($viewRoles !== false) {
            foreach($viewRoles as $role) {
                //check that all the roles exist, in case a plugin-added role has been removed (e.g. GuestUser)
                if($acl->hasRole($role)) {
                    $acl->allow($role, 'Commenting_Comment', array('show'));
                }
            }

            foreach($commentRoles as $role) {
                if($acl->hasRole($role)) {
                    $acl->allow($role, 'Commenting_Comment', array('add'));
                }
            }

            foreach($moderateRoles as $role) {
                if($acl->hasRole($role)) {
                    $acl->allow($role, 'Commenting_Comment', array(
                                'updateapproved',
                                'updatespam',
                                'batch-delete',
                                'delete'
                                ));
                }
            }

            if(get_option('commenting_allow_public')) {
                $acl->allow(null, 'Commenting_Comment', array('show', 'add'));
            }
        }
    }

    public function filterAdminNavigationMain($tabs)
    {
        if(is_allowed('Commenting_Comment', 'updateapproved') || is_allowed('Commenting_Comment', 'updatespam')) {
            $tabs[] = array('uri'=> url('commenting/comment/browse'), 'label'=>'Comments' );
        }

        return $tabs;
    }
    
    public function filterSearchRecordTypes($types)
    {
        $types['Comment'] = __('Comments');
        return $types;
        
    }
}