<?php
  header( "Access-Control-Allow-Origin: *" );

  session_start( );

  include '../xentrylib.php';
  include 'stpage.php';
  //include 'facebook.php';
  require_once 'facebook.php';
  require_once './vendor/autoload.php';
  use Facebook;

  $mysqli = sticonnect( );
//  $user_id = fbconnect( 1 );
  //$user_id = fbconnect5( );

//ob_start();
//print_r($_POST);
//$dataxp = ob_get_contents();
//ob_end_clean();
//file_put_contents("log.file",$dataxp);

  $fb = new Facebook\Facebook([
    'app_id' => '113970302173',
    'app_secret' => '6a1cc9469dec99e4e067876e56dbd473',
    'default_graph_version' => 'v2.5'
  ]);

  $user_id = 0;
  if( $_POST['fbSTAT'] ) {
    try {
      // Returns a `Facebook\FacebookResponse` object
      $response = $fb->get('/me?fields=id,name', $_POST['fbSTAT'] );
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
  //die( '{"error" : "PLx ' . $e->getMessage( ) . ' " }' );
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
  //die( '{"error" : "PL2" }' );
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    $user = $response->getGraphUser( );
    $user_id = $user["id"];
  } else {
    $user_id = fbconnect( );
  }
//  $userNode = $response->getGraphUser( );
//  $showval = $userNode->getName( );
//  die( '{"message" : "Authentication Key not available", "error" : "ScoreTank error( 4b - ' . $showval . ';' . ' )"}' );



//  $facebook = new Facebook($appapikey, $appsecret);
//  $user_id = "123";
//  $user_id = $facebook->require_login();
  if( isset( $_POST['authkey'] ) ) {
    $authkey = isset($_POST['authkey']) ? $_POST['authkey'] : "NULL";
    $displ = isset($_POST['Displ']) ? $_POST['Displ'] : "NULL";

    $query = "select * from FBAccred " .
      " where InitAuth = $authkey " .
          " and FBID is NULL";
    $chkq = $mysqli->query( $query ) or die( '{"error" : "ScoreTank error( 2 )"}' );

    if( !( $chk = $chkq->fetch_array( ) ) ) {
      die( '{"message" : "Authentication Key not available", "error" : "ScoreTank error( 4 )"}' );
    }

    $query = "update FBAccred set FBID = $user_id, Display = $displ " .
      " where InitAuth = $authkey " .
          " and FBID is NULL";

    $diag = "1";
    if( $authkey == 1161235502 ) {
      $diag .= " - " . $query;
    }
    $mysqli->query( $query ) or die( '{"error" : "ScoreTank error( ' . $diag . ' )"}' );

    $query = "select * from FBAccred " .
      " where InitAuth = $authkey " .
          " and FBID = $user_id ";
    $chkq = $mysqli->query( $query ) or die( '{"error" : "ScoreTank error( 2 )"}' );

    if( !( $chk = $chkq->fetch_array( ) ) ) {
      die( '{"message" : "Authentication Key already in use", "error" : "ScoreTank error( 3 )"}' );
    }

    echo '{"message" : "Authorisation registered."}';
    exit;
  }


  $query = "select * from FBAccred " .
    " where FBID = $user_id ";
  $chkq = $mysqli->query( $query ) or die( '{"error" : "ScoreTank error( 2 )"}' );

  if( !( $chk = $chkq->fetch_array( ) ) ) {
    die( '{"message" : "Authentication Key not available", "error" : "ScoreTank error( 4 - ' . $user_id . ';' . ' )"}' );
  }

  $tstaccum = " data: ";
  //$ikeys = split( '&', $_POST['fields'] );
  $ikeys = preg_replace( '/\D/', '', explode( '&', $_POST['fields'] ) );
  $vals = explode( '&', $_POST['vals'] );
 
$tstsplit = explode( '&', $_POST['fields'] );
//die( '{"error" : "cikeys: ' . $tstsplit[0] . '"}' );
//die( '{"error" : "cikeys: ' . count( $ikeys ) . ":" . $ikeys[0] . '"}' );
  $ikeys = array_filter( $ikeys );
if( count( $ikeys ) == 0 ) {
$ikeys[0] = "2021371506";
$vals[0] = "30-30";
}

  $accredOK = true;
  foreach( $ikeys as $ikey ) {
    $accredOK = $accredOK && CheckAccredi( $mysqli, $ikey, $user_id );
  }
  if( !$accredOK ) {
    die( '{"message" : "Authentication Key not available", "error" : "ScoreTank error(5)"}' );
  }

// "You should avoid building your own JSON, and instead use the PHP function json_encode"
  $i = 0;
  $chkey = 0;
  $rnum = 0;
  for( $i = 0; $i < count( $ikeys ); $i++ ) {
//die( '{"error" : "PLx 123" }' );
    list( $chkey, $rnum ) = ProcessResi( $mysqli, $ikeys[$i], $vals[$i], $user_id );
//die( json_encode( array( "error" => ( "PLx" . $chkey ) ) ) );
  }
//die( '{"error" : "PL' . $chkey . ':::' . $rnum . '" }' );
  ProcessLaddi( $mysqli, $chkey, $rnum );
  ProcessDerivedi( $mysqli, $chkey );

  echo '{"inputflds" : "' . join( " ", $ikeys ) . '", "message" : "Score registered: ' . join( ";", $vals ) . ' for ' . join( ";", $ikeys ) . '"' .
	// ', "dbg" : "' . print_r( $_SESSION, TRUE ) . '"' .
    '}';
//  echo print_r( $_SESSION, TRUE );
?>

