<?php

  header( "Expires: -1" );
  header( "Content-type:text/xml" );

  include "stpage.php";
  include "facebook.php";

function myserror( $doc, $root, $query, $str, $mysqli ) {
  if( $query ) {
    $node = $doc->createElement( 'query', $query );
    $root->appendChild( $node );
  }
  $node = $doc->createElement( 'str', $str );
  $root->appendChild( $node );
  if( $mysqli ) {
    $node = $doc->createElement( 'error', $mysqli->error );
    $root->appendChild( $node );
  }

  return( $doc->saveXML( ) );
}

  $doc = new DomDocument( '1.0' );
  $doc->formatOutput = true;
  $root = $doc->createElement( 'stlib' );
  $root = $doc->appendChild( $root );
  $stdb = sticonnect( );
  $fbid = fbconnect( );
  if( !$fbid ) {
	die( myserror( $doc, $root, 0, "efb", 0 ) );
  }
  $query = "select * from FBAccred where FBID = $fbid";
  $acq = $stdb->query( $query );
die( "<ql>" . $query . "</ql>"  );
  if( !( $acq ) ) {
	die( myserror( $doc, $root, $query, "e1", $stdb ) );
  }
  while( $acr = $acq->fetch_assoc( ) ) {
    $node = $doc->createElement( "FBID", $acr['FBID'] );
	$root->appendChild( $node );
  }

  echo $doc->saveXML( );

?>


