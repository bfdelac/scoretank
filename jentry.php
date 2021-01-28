<?php
/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.3.10');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
	die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Joomla!');
}

// Saves the start time and memory usage.
$startTime = microtime(1);
$startMem  = memory_get_usage();

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';
require_once 'xentrylib.php';
include 'stdbcred.php';
include 'stutil.php';


// Set profiler start time and memory usage and mark afterLoad in the profiler.
JDEBUG ? JProfiler::getInstance('Application')->setStart($startTime, $startMem)->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');

$document = JFactory::getDocument();
$document->setMimeEncoding('application/json');

	$user = JFactory::getUser();
	$user_id = $user->id;

	$mysqli = sticonnect( );

	$fields = $_POST['fields'];
	if(strlen($fields) <= 0) {
		die( '{"message" : "No data submitted", "error" : "ScoreTank error(6)"}' );
	}
	$ikeys = preg_replace( '/\D/', '', explode( '&', $fields ) );
	$vals = explode( '&', $_POST['vals'] );
	$accredOK = true;
	foreach( $ikeys as $ikey ) {
		$accredOK = $accredOK && CheckAccredi( $mysqli, $ikey, $user_id, 4 );
	}
	if( !$accredOK ) {
		die( '{"message" : "Authentication Key not available", "error" : "ScoreTank error(5)"}' );
	}

	$i = 0;
	$chkey = 0;
	$rnum = 0;
	for( $i = 0; $i < count( $ikeys ); $i++ ) {
		list( $chkey, $rnum ) = ProcessResi( $mysqli, $ikeys[$i], $vals[$i], $user_id );
	}
	//die( '{"message" : "Test", "error" : "ScoreTank error(551)"}' );
	ProcessLaddi( $mysqli, $chkey, $rnum );
	ProcessDerivedi( $mysqli, $chkey );

	echo '{"inputflds" : "' . join( " ", $ikeys ) . '", "message" : "Score registered: ' . join( ";", $vals ) . ' for ' . join( ";", $ikeys ) . '"' . '}'; 
?>

