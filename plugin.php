<?php

define('COMMENTING_PLUGIN_DIR', PLUGIN_DIR . '/Commenting');
require_once(COMMENTING_PLUGIN_DIR . '/CommentingPlugin.php');

$commenting = new CommentingPlugin();
$commenting->setUp();
