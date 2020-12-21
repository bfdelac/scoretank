<?php
  header( "Access-Control-Allow-Origin: *" );

  session_start( );

  include 'stpage.php';
  //require_once 'facebook.php';
  require_once './vendor/autoload.php';
  use Facebook;

  $stdb = sticonnect( );
// $user_id = fbconnectDiag( );
//  $user_id = fbconnect( );

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
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    $user = $response->getGraphUser( );
    $user_id = $user["id"];
  } else {
    $user_id = fbconnect( );
  }

  if( isset( $_POST['authkey'] ) ) {
    $authkey = isset($_POST['authkey']) ? $_POST['authkey'] : "NULL";
    $displ = isset($_POST['Displ']) ? $_POST['Displ'] : "NULL";

    $query = "select * from FBAccred " .
      " where InitAuth = $authkey " .
	  " and FBID is NULL";
    $chkq = $stdb->query( $query ) or die( '{"error" : "ScoreTank error( 2 )"}' );

    if( !( $chk = $chkq->fetch_array( ) ) ) {
      die( '{"message" : "Authentication Key not available", "error" : "ScoreTank error( 4 )"}' );
    }

    $query = "update FBAccred set FBID = $user_id, Display = $displ " .
      " where InitAuth = $authkey " .
	  " and FBID is NULL";

//die( '{"error" : "' . $query . '"}' );
    $diag = "1";
	if( $authkey == 1161235502 ) {
	  $diag .= " - " . $query;
	}
    $stdb->query( $query ) or die( '{"error" : "ScoreTank error( ' . $diag . ' )"}' );

    $query = "select * from FBAccred " .
      " where InitAuth = $authkey " .
	  " and FBID = $user_id ";
    $chkq = $stdb->query( $query ) or die( '{"error" : "ScoreTank error( 2 )"}' );

    if( !( $chk = $chkq->fetch_array( ) ) ) {
      die( '{"message" : "Authentication Key already in use", "error" : "ScoreTank error( 3 )"}' );
    }

    echo '{"message" : "Authorisation registered."}';
  } else if( isset( $_POST["req"] ) ) {
    if( !strcmp( $_POST["req"], "becomefan" ) ) {
      $query = "delete from FBAccred where FBID = $user_id and AccredRole = 3";
	  $stdb->query( $query ) or die( '{"error" : "ScoreTank error( f1 )"}' );
	  if( isset( $_POST["becomingfan"] ) && ( $_POST["becomingfan"] == 1 ) ) {
    	$query = "insert into FBAccred ( FBID, AccredRole, AccredKey, Display ) values ( $user_id, 3, " . $_POST["teamkey"] . ", 0x11 )";
		$stdb->query( $query ) or die( '{"error" : "ScoreTank error( f2 )"}' );
	  }
	  echo( '{"message" : "success", "fbml_newHTML" : "' . ListFans( $_POST["teamkey"], $user_id ) . '"}' );
	}
  }
?>

