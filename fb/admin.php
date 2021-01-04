<?php

  include 'stpage.php';
  require_once 'facebook.php';
  stconnect( );
  fbconnect( );

$appapikey = '103424ce8ec89f93620faeb04713764c';
$appsecret = '6a1cc9469dec99e4e067876e56dbd473';
$facebook = new Facebook($appapikey, $appsecret);
$user_id = $facebook->require_login();

  $query = "select * from FBAccred " .
  			" where AccredRole = 0 " .
			" and FBID = " . $user_id;
  $ChkId = mysql_query( $query ) or die( '{"error" : "ScoreTank error (a1) - ' . mysql_error( ) . '"}' );

  if( !mysql_fetch_array( $ChkId ) ) {
    die( "You are not registered as a ScoreTank administrator." );
  }

  echo "<h1>ScoreTank Admin - enter/modify championship</h1>";
 
?>
<script type="text/javascript">

function ChangeText( textobj, id ) {
  var ajax = new Ajax( );
  ajax.responseType = Ajax.JSON;
window.alert( "ChangeText: " + textobj.value );
  var queryParams = { "req" : id, "textval" : textobj.value };

}

function OnDropDown( obj ) {
  new Dialog().showMessage('Dialog', "Hello" );
}

</script>

<P/>
<table>
 <tr><th>Sporting Body:</th><td><input type="text" id="STSBName" onchange="ChangeText( this, 1 );"><button id="STSBNameDrop" onclick="OnDropDown( this );">&#9660;</button></td></tr>
 <tr><th>Sport:</th><td><input type="text" id="STSport" onchange="ChangeText( this, 2 );"></td></tr>
</table>


