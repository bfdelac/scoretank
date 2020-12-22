<?php

  include 'stpage.php';
  require_once 'facebook.php';
  stconnect( );
  $user_id = fbconnect( );

//  $appapikey = '103424ce8ec89f93620faeb04713764c';
//  $appsecret = '6a1cc9469dec99e4e067876e56dbd473';
//  $facebook = new Facebook($appapikey, $appsecret);
//  $user_id = "123";
//  $user_id = $facebook->require_login();

  if( isset( $_POST['authkey'] ) ) {
    $authkey = isset($_POST['authkey']) ? $_POST['authkey'] : "NULL";
    $displ = isset($_POST['Displ']) ? $_POST['Displ'] : "NULL";

    $query = "select * from FBAccred " .
      " where InitAuth = $authkey " .
	  " and FBID is NULL";
    $chkq = mysql_query( $query ) or die( '{"error" : "ScoreTank error( 2 )"}' );

    if( !( $chk = mysql_fetch_array( $chkq ) ) ) {
      die( '{"message" : "Authentication Key not available", "error" : "ScoreTank error( 4 )"}' );
    }

    $query = "update FBAccred set FBID = $user_id, Display = $displ " .
      " where InitAuth = $authkey " .
	  " and FBID is NULL";

//die( '{"error" : "' . $query . '"}' );
    mysql_query( $query ) or die( '{"error" : "ScoreTank error( 1 )"}' );

    $query = "select * from FBAccred " .
      " where InitAuth = $authkey " .
	  " and FBID = $user_id ";
    $chkq = mysql_query( $query ) or die( '{"error" : "ScoreTank error( 2 )"}' );

    if( !( $chk = mysql_fetch_array( $chkq ) ) ) {
      die( '{"message" : "Authentication Key already in use", "error" : "ScoreTank error( 3 )"}' );
    }

    echo '{"message" : "Authorisation registered."}';
  } else if( isset( $_POST["req"] ) ) {
    if( !strcmp( $_POST["req"], "becomefan" ) ) {
      $query = "delete from FBAccred where FBID = $user_id and AccredRole = 3";
	  mysql_query( $query ) or die( '{"error" : "ScoreTank error( f1 )"}' );
	  if( isset( $_POST["becomingfan"] ) && ( $_POST["becomingfan"] == 1 ) ) {
    	$query = "insert into FBAccred ( FBID, AccredRole, AccredKey, Display ) values ( $user_id, 3, " . $_POST["teamkey"] . ", 0x11 )";
		mysql_query( $query ) or die( '{"error" : "ScoreTank error( f2 )"}' );
	  }
	  echo( '{"message" : "success", "fbml_newHTML" : "' . ListFans( $_POST["teamkey"], $user_id ) . '"}' );
	}
  }
?>

