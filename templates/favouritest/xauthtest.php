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

require_once JPATH_BASE . '/jwt/src/BeforeValidException.php';
require_once JPATH_BASE . '/jwt/src/ExpiredException.php';
require_once JPATH_BASE . '/jwt/src/SignatureInvalidException.php';
require_once JPATH_BASE . '/jwt/src/JWT.php';

require_once JPATH_BASE . '/stdbcred.php';

use \Firebase\JWT\JWT;

$user = JFactory::getUser();

$isloggedin = (!$user->get('guest')) ? 'logged in' : 'logged out';


$key = jwt_secret( );
//$token = array( "testuserid" => 999 );
//$jwt = JWT::encode($token, $key);
//$decoded = JWT::decode($jwt, $key, array('HS256'));

$ujwt = '';
if( isset( $_POST['userIdJwt'] ) ) {
	$ujwt = $_POST['userIdJwt'];
}

//$posteddata = file_get_contents('php://input');
//$posteddata = '{ "req" : "test", "userIdJwt" : "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0ZXN0dXNlcmlkIjo5OTl9.MdFKNJxBz5WSLv4gbgwW5ru6zZfF4U_kQ8gEipYxjPI" }';
//$postdata = json_decode($ujwt, true);
//$ujwt = $postdata["userIdJwt"]; 
//$ujwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0ZXN0dXNlcmlkIjo5OTl9.MdFKNJxBz5WSLv4gbgwW5ru6zZfF4U_kQ8gEipYxjPI";
try {
	$decodedjwt = JWT::decode($ujwt, $key, array('HS256'));
} catch(\Firebase\JWT\ExpiredException $e) {
	die('{"error" : "expired"}');
}
//$decodeduserid = 9998;

echo '{"data" : "hello world", "loggedin" : "' . $isloggedin .
	//	'", "jwttest" : "' . $jwt .
	//	'", "jwt" : "' . $decodedjwt .
		'", "user" : "' . $decodedjwt->userid .
		'", "champ" : "' . $decodedjwt->champ .
	//	'", "p" : "' . $postdata["userIdJwt"] .
		'", "jp" : "' . $ujwt .
	//	'", "pp" : "' . file_get_contents('php://input') .
		'" }';

?>
