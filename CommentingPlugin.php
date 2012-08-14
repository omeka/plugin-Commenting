<?php


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
        'define_acl',
        'upgrade'
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
              `flagged` tinyint(1) NOT NULL DEFAULT '0',
              `is_spam` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `record_id` (`record_id`,`user_id`,`parent_comment_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
        $db->query($sql);

        set_option('commenting_comments_label', 'Comments');
        
        $commentRoles = array('super');
        $moderateRoles = array('super');
        set_option('commenting_comment_roles', serialize($commentRoles));
        set_option('commenting_moderate_roles', serialize($moderateRoles));
        set_option('commenting_moderated_comment_roles', serialize(array()));
        set_option('commenting_view_roles', serialize(array()));

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
        }
    }

    public function hookUninstall()
    {
        $db = get_db();
        $sql = "DROP TABLE IF EXISTS `$db->Comment`";
        $db->query($sql);
    }

    public function hookPublicThemeHeader()
    {
        queue_css('commenting');
        queue_js('commenting');
        queue_js_string("Commenting.pluginRoot = '" . WEB_ROOT . "/commenting/comment/'");
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
        $arrayedFields = array(
                'commenting_comment_roles',
                'commenting_moderate_roles',
                'commenting_view_roles',
                'commenting_moderated_comment_roles'
                );
        
        foreach($post as $key=>$value) {
            if( in_array($key, $arrayedFields)) {
                $value = serialize($value);
            }
            set_option($key, $value);
        }
        foreach($arrayedFields as $key) {
            if(!isset($_POST[$key])) {
                set_option($key, serialize(array()));
            }
        }
    }

    public function hookConfigForm()
    {
        include COMMENTING_PLUGIN_DIR . '/config_form.php';

    }

    public function hookDefineAcl($acl)
    {
        $acl->addResource('Commenting_Comment');
        $commentRoles = unserialize(get_option('commenting_comment_roles'));
        $moderatedCommentRoles = unserialize(get_option('commenting_moderated_comment_roles'));
        $moderateRoles = unserialize(get_option('commenting_moderate_roles'));
        $viewRoles = unserialize(get_option('commenting_view_roles'));


        if($viewRoles !== false) {
            foreach($viewRoles as $role) {
                //check that all the roles exist, in case a plugin-added role has been removed (e.g. GuestUser)
                if($acl->hasRole($role)) {
                    $acl->allow($role, 'Commenting_Comment', array('show', 'flag'));
                }
            }

            foreach($commentRoles as $role) {
                if($acl->hasRole($role)) {
                    $acl->allow($role, 'Commenting_Comment', array('add'));
                }
            }

            foreach($moderateRoles as $role) {
                if($acl->hasRole($role)) {
                    $acl->allow($role, 'Commenting_Comment', array('updateapproved', 'updatespam', 'unflag', 'update-flagged', 'noappcomment', 'browse', 'batch-delete'));
                }
            }

 
        }
        //comment without approval does not really limmit access to an action, but is handy for use in the controller
        
        $publicModeration = get_option('commenting_require_public_moderation');
        if(!$publicModeration) {
            $acl->allow(null, 'Commenting_Comment', array('noappcomment'));
        }
        
        foreach($moderatedCommentRoles as $role) {
            if($acl->hasRole($role)) {
                $acl->deny($role, 'Commenting_Comment', array('noappcomment'));
            }
        }        
        
        if(get_option('commenting_allow_public')) {
            $acl->allow(null, 'Commenting_Comment', array('show', 'add', 'flag'));
        }
    }

    public function filterAdminNavigationMain($tabs)
    {
        if(has_permission('Commenting_Comment', 'updateapproved') || has_permission('Commenting_Comment', 'updatespam')) {
            $tabs['Comments'] = uri('commenting/comment/browse');
        }

        return $tabs;
    }
}