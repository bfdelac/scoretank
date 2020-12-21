<script>
<!--
function chgDispl( obj ) {
  //if( obj.get
  if( document.getElementById( 'Displ' ).getSelectedIndex( ) > 0 ) {
    document.getElementById( 'xsubmit' ).setDisabled( false );
  } else {
    document.getElementById( 'xsubmit' ).setDisabled( true );
  }
}

function sendAuth( authtype ) {
  var ajax = new Ajax( );
  ajax.responseType = Ajax.JSON;
  ajax.ondone = function( data ) {
    //document
	if( data.error ) {
	  if( data.message ) {
	    new Dialog().showMessage('Dialog', data.message + ", " + data.error );
	  } else {
	    new Dialog().showMessage('Dialog', data.error );
	  }
	} else {
	  new Dialog().showMessage('Dialog', data.message );
	  if( document.getElementById( 'proceed' ) ) {
	    document.getElementById( 'proceed' ).setStyle('display',''); 
	    document.getElementById( 'Displ' ).setDisabled( true );
	    document.getElementById( 'xsubmit' ).setDisabled( true );
	  }
	}
  }
  var Displ =  document.getElementById( 'Displ' ).getValue( );
  if( Displ < 0 ) {
	new Dialog().showMessage('Dialog', 'Please select whether you want your name displayed' );
    return;
  }
  var queryParams = { "req" : "auth", "Authtype" : authtype,
        "Displ" : Displ
<?php
  if( isset( $_REQUEST["authkey"] ) ) {
    echo ', "authkey" : ' . $_REQUEST["authkey"];
  }
?>
  };
  ajax.post( "http://www.scoretank.com.au/fb/xstfbauth.php", queryParams );
}

//-->
</script>
<?php

  include 'stpage.php';
  require_once 'facebook.php';
  stconnect( );
  $user_id = fbconnect( );

  $authkey = 0;
  if( isset( $_REQUEST["authkey"] ) ) {
    $authkey = $_REQUEST["authkey"];
  }
  if( !$authkey ) {
    die( "Error" );
  }

function GetAccredRoleStr( $arole ) {
  if( $arole == 1 ) {
    return( "Championship" );
  }
  if( $arole == 2 ) {
    return( "Team" );
  }
}

  $query = "select * from FBAccred where InitAuth = $authkey and FBID is NULL";
  $ARecq = mysql_query( $query ) or die( "Error (1), " . mysql_error( ) );
  // FBID
  // AccredRole 1->Champ, 2->Team, 3->TeamFan
  // AccredKey
  // InitAuth
  // Display = 0 - hide, 1 - public
  //  mask 0xF0: 0x10 - status update feed
  if( $ARec = mysql_fetch_array( $ARecq, MYSQL_ASSOC ) ) {
	$AccredFor = "";
    if( $ARec["AccredRole"] == 1 ) {
	   $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Season.SeasonKey, SportingBody.SBKey, Championship.Status " .
	   " FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
	   " WHERE Championship.ChampionshipKey = " . $ARec["AccredKey"];
	   $ChRecq = mysql_query( $query ) or die( mysql_error( ) );
	   $ChRec = mysql_fetch_array( $ChRecq, MYSQL_ASSOC );
	   $AccredFor = "<H1>" . $ChRec["SBAbbrev"]." ".$ChRec["GradeName"] ."</H1>\n    ".
		                   $ChRec["SportName"]. " - ".$ChRec["SeasonName"]."<p>\n    ";
	}
    print "Authorising for entering scores, for " . GetAccredRoleStr( $ARec["AccredRole"] ) . ": " . $AccredFor . "<P><P/>\n";
	print "Click the button below to allow you to be able to enter results for this " . GetAccredRoleStr( $ARec["AccredRole"] );
?>
<P/>
<table><tr><td>
 Display your name as a <?php print GetAccredRoleStr( $ARec["AccredRole"] ); ?> administrator?
   <select id="Displ" onchange="chgDispl( this );">
    <option value="-1">Select...</option>
	<option value="0">Do not display</option>
	<option value="1">Display</option>
   </select></td></tr><tr><td align="center">
 <input id="xsubmit" type="button" value="Become admin" class="inputbutton" onclick="sendAuth( ); return false;" disabled="1"/>
 </td></tr></table>
<?php
    if( $ARec["AccredRole"] == 1 ) {
      printf( '<A href="champ.php?champ=%d" id="proceed" style="display:none;">Proceed to Championship page</A>', $ARec["AccredKey"] );
    }
  } else {
    echo "Authorisation key already in use.";
  }
 
  
?>

