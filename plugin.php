<?php

define('COMMENTING_PLUGIN_DIR', PLUGIN_DIR . '/Commenting');
require_once(COMMENTING_PLUGIN_DIR . '/helpers/commenting.php');

class CommentingPlugin extends Omeka_Plugin_Abstract
{
    
    protected $_hooks = array(
        'install',
        'uninstall',
        'public_append_to_items_show',
        'public_theme_header',
        'config_form',
        'config'
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
    
    public function hookPublicAppendToItemsShow()
    {
        if( (get_option('commenting_allow_public') == 1) || current_user() ) {
            $options = array('threaded'=> get_option('commenting_threaded'), 'approved'=>true);
            commenting_echo_comments($options);
            commenting_echo_comment_form();
        }

    }
    
    public function hookConfig($post)
    {
        foreach($post as $key=>$value) {
            set_option($key, $value);
        }
    }
    
    public function hookConfigForm()
    {
        include COMMENTING_PLUGIN_DIR . '/config_form.php';
        
    }
    
    public function filterAdminNavigationMain($tabs)
    {
        $tabs['Comments'] = uri('commenting/comment/browse');
        return $tabs;
    }
}

$commenting = new CommentingPlugin();
$commenting->setUp();