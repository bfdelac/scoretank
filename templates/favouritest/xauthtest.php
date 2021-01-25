<?php
header( "Access-Control-Allow-Origin: *" );

define('_JEXEC', 1);

if (file_exists(__DIR__ . '/../../defines.php'))
{
 	include_once __DIR__ . '/../../defines.php';
}

if (!defined('_JDEFINES'))
{
 	define('JPATH_BASE', __DIR__ . '/../..');
 	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

$user = JFactory::getUser();

$isloggedin = (!$user->get('guest')) ? 'logged in' : 'logged out';


echo '{"data" : "hello world", "loggedin" : "' . $isloggedin .'" }';

?>
