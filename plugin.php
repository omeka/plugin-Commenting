<?php

define('COMMENTING_PLUGIN_DIR', PLUGIN_DIR . '/Commenting');
require_once(COMMENTING_PLUGIN_DIR . '/helpers/commenting.php');

class CommentingPlugin extends Omeka_Plugin_Abstract
{
    
    protected $_hooks = array(
        'install',
        'uninstall',
        'public_append_to_items_show',
        'public_theme_header'
    );
    
    public function hookInstall()
    {
        $db = get_db();
        $sql = "
            CREATE TABLE IF NOT EXISTS `$db->Comment` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `record_id` int(10) unsigned NOT NULL,
              `record_type` tinytext COLLATE utf8_unicode_ci NOT NULL,
              `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `body` text COLLATE utf8_unicode_ci NOT NULL,
              `author_email` tinytext COLLATE utf8_unicode_ci,
              `author_url` tinytext COLLATE utf8_unicode_ci,
              `author_name` tinytext COLLATE utf8_unicode_ci,
              `ip` tinytext COLLATE utf8_unicode_ci,
              `user_id` int(11) DEFAULT NULL,
              `parent_comment_id` int(11) DEFAULT NULL,
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
        require_once(COMMENTING_PLUGIN_DIR . '/CommentForm.php');
        $commentSession = new Zend_Session_Namespace('commenting', true);
        //get the existing comments
        $request = Omeka_Context::getInstance()->getRequest();
        $params = $request->getParams();
        $model = ucfirst(Inflector::singularize($params['controller']));
                
        $findArray = array(
            'record_type' => $model,
            'record_id' => $params['id']
        );

        $html = '';
        $html .= "<div id='comments-flash'>". flash(true) . "</div>";
        $html .= "<div class='comments'><h2>Comments</h2>";
        $options = array('threaded'=>true);
        $html .= commenting_get_comments($params['id'], 'Item', $options);

        $html .= "</div>";
        echo $html;
        
        //get the form -- need to figure out how guarantee it goes at end.
        //also tricky: getting all the threaded comments, and options for threading/not, etc
        
        
        if(isset($commentSession->form)) {
            $form = unserialize($commentSession->form);
        } else {
            $form = $this->getForm();
        }
            
        echo $form;
        if(isset($commentSession->form)) {
            unset($commentSession->form);
        }
    }
    
    private function getForm()
    {
        
        return new Commenting_CommentForm();
    }
}

$commenting = new CommentingPlugin();
$commenting->setUp();