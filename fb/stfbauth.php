<?php

  session_start( );

  include 'stpage.php';
  //require_once 'facebook.php';
  require_once './vendor/autoload.php';
  use Facebook;

  $stdb = sticonnect( );
  $user_id = fbconnect5( );

  echo addPreIframe( "ScoreTank Authorisation" );
  echo addBodyScript( );
?>
<link type="text/css" href="jquery-ui-1.8.21.custom/css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="jquery-ui-1.8.21.custom/js/jquery-ui-1.8.21.custom.min.js"></script>
<div id="fb-root"></div>
<script type="text/javascript">
  $(document).ready( function( ) { $( "#fbdialog" ).dialog( { modal: true, autoOpen: false } ) } );
</script>

<script>
<!--
function chgDispl( obj ) {
  //if( obj.get
  // var fldids = inputfld.id;
  // var fldvals = inputfld.value;
  var Displ = document.getElementById( 'Displ' );
//  window.alert( "Dv: " + Displ.value );
  if( Displ.value >= 0 ) {
    document.getElementById( 'xsubmit' ).disabled = "";
  } else {
    document.getElementById( 'xsubmit' ).disabled = "disabled";
  }

  return;

//  if( document.getElementById( 'Displ' ).getSelectedIndex( ) > 0 ) {
//    document.getElementById( 'xsubmit' ).setDisabled( false );
//  } else {
//    document.getElementById( 'xsubmit' ).setDisabled( true );
//  }
}

function sendAuthCB( data ) {
//window.alert( "calledback" );
  if( data.error ) {
	if( data.message ) {
	  window.alert( data.message + ", " + data.error );
	} else {
	  window.alert( data.error );
	}
  } else {
	window.alert( data.message );
	$( '#proceed' ).attr( 'style', '' ); 
	$( '#Displ' ).attr( 'disabled', true );	//	setDisabled( true );
	$( '#xsubmit' ).attr( 'disabled', true );	// setDisabled( true );
  }
}

function sendAuth( authtype ) {
  var Displ =  document.getElementById( 'Displ' ).value;
  if( Displ < 0 ) {
    window.alert( 'Please select whether you want your name displayed' );
    return;
  }
  var queryParams = { "req" : "auth", "Authtype" : authtype,
			"Displ" : Displ,
			"fbSTAT" : "<?php echo ( $_SESSION["fbSTATs"] ); ?>"
<?php
	  if( isset( $_REQUEST["authkey"] ) ) {
		echo ', "authkey" : ' . $_REQUEST["authkey"];
	  }
?>
						  };
  //window.alert( "posting" );
  //$.post( "https://ssl4.westserver.net/scoretank.com.au/fb/xstfbauth.php", queryParams, sendAuthCB, "json" ); 
  $.post( "https://www.thebrasstraps.com/scoretank/fb/xstfbauth.php", queryParams, sendAuthCB, "json" ); 
}

function sendAuthX( authtype ) {
  var ajax = new Ajax( );
  ajax.responseType = Ajax.JSON;
  ajax.ondone = function( data ) {
    //document
	if( data.error ) {
	  if( data.message ) {
	    //new Dialog().showMessage('Dialog', data.message + ", " + data.error );
	    window.alert( data.message + ", " + data.error );
	  } else {
	    //new Dialog().showMessage('Dialog', data.error );
	    window.alert( data.error );
	  }
	} else {
	  //new Dialog().showMessage('Dialog', data.message );
	  window.alert( data.message );
	  if( document.getElementById( 'proceed' ) ) {
	    document.getElementById( 'proceed' ).setStyle('display',''); 
	    document.getElementById( 'Displ' ).setDisabled( true );
	    document.getElementById( 'xsubmit' ).setDisabled( true );
	  }
	}
  }
  var Displ =  document.getElementById( 'Displ' ).value;
  if( Displ < 0 ) {
	// new Dialog().showMessage('Dialog', 'Please select whether you want your name displayed' );
	window.alert( 'Please select whether you want your name displayed' );
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
  //ajax.post( "https://ssl4.westserver.net/scoretank.com.au/fb/xstfbauth.php", queryParams );
  ajax.post( "https://www.thebrasstraps.com/scoretank/fb/xstfbauth.php", queryParams );
}

//-->
</script>
<?php

  if( !$user_id ) {
    die( "No user ID detected; please ensure you are logged on to Facebook." );
  }

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
if(!$stdb) {
  echo '<p>stfailed</p>';
} else {
}
  //echo '<p>i: ' . $stdb . '</p>';
  $ARecq = $stdb->query( $query );
  if( !$ARecq ) {
    die( "Error (1), " . $stdb->error( ) );
  }
  // FBID
  // AccredRole 1->Champ, 2->Team, 3->TeamFan
  // AccredKey
  // InitAuth
  // Display = 0 - hide, 1 - public
  //  mask 0xF0: 0x10 - status update feed

  if( $ARec = $ARecq->fetch_array( ) ) {
    $AccredFor = "";
    if( $ARec["AccredRole"] == 1 ) {
	   $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Season.SeasonKey, SportingBody.SBKey, Championship.Status " .
	   " FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
	   " WHERE Championship.ChampionshipKey = " . $ARec["AccredKey"];
	   $ChRecq = $stdb->query( $query ) or die( $stdb->error( ) );
	   $ChRec = $ChRecq->fetch_array( );
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
 
echo addPostIframe( );
  
?>

