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
        if($viewRoles !== false) {
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