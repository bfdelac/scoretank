<?php

  session_start( );

  include 'stpage.php';
  //require_once 'facebook.php';
  require_once './vendor/autoload.php';
  use Facebook;

  $mysqli = sticonnect( );
  $user_id = fbconnect5( );

  echo addPreIframe( "Competition Fixture" );
?>
<SCRIPT>
function SendMResCB( data ) {
  if( data.inputflds ) {
	afields = data.inputflds.split( " " );
	var i = 0;
	for( i = 0; i < afields.length; i++ ) {
	  $( '#statusm' + afields[i] ).text( "processed" );
	}
  }
  if( data.error ) {
	if( data.message ) {
	  //new Dialog().showMessage('Error', data.message + ", " + data.error );
	  $( "#fbdialog" ).text( data.message + ", " + data.error );
	  $( "#fbdialog" ).dialog( 'open' );
	} else {
	  //new Dialog().showMessage('Error', data.error );
	  $( "#fbdialog" ).text( data.error );
	  $( "#fbdialog" ).dialog( 'open' );
	}
  	//var  fstat = document.getElementById( 'status' + inputfld.getId( ) );
  	//if( fstat ) {
	//  fstat.innerText = "error"; 
	//}
	//$( '#statusm' + afields[i] ).text( "processed" );
  } else {
  }
}

function SendMRes( inputfld ) {
  var fldids = inputfld.id;
  var fldvals = inputfld.value;
  var queryParams = { "req" : "MRes", "fields" : fldids, "vals" : fldvals<?php
		echo (', "fbSTAT" : "' . $_SESSION["fbSTATs"] . '"' );
	?> };
//$( "#fbdialog" ).text( 'message' );
//$( "#fbdialog" ).dialog( 'open' );
//new Dialog().showMessage( 'ScoreTank', "Message" );
//  $.post( "https://ssl4.westserver.net/scoretank.com.au/fb/xentry.php",
  $.post( "https://www.thebrasstraps.com/scoretank/fb/xentry.php",
  			queryParams,
			SendMResCB,
			"json" );
	
  //document.getElementById( 'status' + fldids ).innerText = "processing...";
  $( '#status' + fldids ).text( "processing..." );
}

function SendMResX( inputfld ) {
window.alert( "SendMResa" );
  var ajax = new Ajax( );
window.alert( "SendMResb" );
  ajax.responseType = Ajax.JSON;
window.alert( "SendMResc" );
  ajax.ondone = function( data ) {
	if( data.inputflds ) {
	  afields = data.inputflds.split( " " );
	  var i = 0;
	  for( i = 0; i < afields.length; i++ ) {
	    $( '#statusm' + afields[i] ).text( "processed" );
  		var  fstat = document.getElementById( 'statusm' + afields[i] );
  		if( fstat ) {
		  fstat.setTextValue( "processed" ); 
		}
	  }
	}
	if( data.error ) {
	  if( data.message ) {
		new Dialog().showMessage('Error', data.message + ", " + data.error );
	  } else {
		new Dialog().showMessage('Error', data.error );
	  }
//new Dialog( ).showMessage( 'Error', "test" );
  	  var  fstat = document.getElementById( 'status' + inputfld.getId( ) );
  	  if( fstat ) {
		fstat.setTextValue( "error" ); 
	  }
    } else {
//	  new Dialog().showMessage('Dialog', data.message );
	  if( document.getElementById( 'proceed' ) ) {
	 //   document.getElementById( 'proceed' ).setStyle('display','');
	//	document.getElementById( 'Displ' ).setDisabled( true );
	//	document.getElementById( 'xsubmit' ).setDisabled( true );
	  }
	}
  }
window.alert( "SendMRes0" );
  var fldids = inputfld.getId( );
window.alert( "SendMRes1" );
//new Dialog( ).showMessage( 'id', inputfld.getId( ) + " - " + inputfld.getValue( ) );
  var fldvals = inputfld.getValue( );
window.alert( "SendMRes2" );
  var queryParams = { "req" : "MRes", "fields" : fldids, "vals" : fldvals };
window.alert( "SendMRes3" );
//  ajax.post( "https://ssl4.westserver.net/scoretank.com.au/fb/xentry.php", queryParams );
  ajax.post( "https://www.thebrasstraps.com/scoretank/fb/xentry.php", queryParams );
  document.getElementById( 'status' + inputfld.getId( ) ).setTextValue( "processing..." );
}
</SCRIPT>

<?php

//parameters: 0- $MatchRec, 1- Score format, 2- Highlight teamname, 3- Lastdate
function MakeMatchEnt( $MatchRec, $sfmt, $lastdate ) {
  $LinkStr = "vs";
  if( $MatchRec["AwayTeamKey"] == -1 ) {
	return "      <tr class='match'><td>" . $MatchRec["HomeTeamName"]. "</td><td>Bye</td>";
  }
  $HText = $MatchRec["HomeTeamName"];
  $AText = $MatchRec["AwayTeamName"];
  $Addn = "";
  if( ($sfmt == 'T') && ($MatchRec["HomeTeamRawScore"]) &&
      ((strtoupper($MatchRec["Result"]) == 'H') ||
	   (strtoupper($MatchRec["Result"]) == 'A'))) {
    $Addn = "&nbsp;&nbsp;".$MatchRec["HomeTeamRawScore"];
  } else {
	$Addn = $MatchRec["HomeGroundName"].", ".fmttime($MatchRec["Scheduled"], $lastdate );
  }
  if( ( $sfmt == 'T' ) || ( $sfmt == 'B' ) ) {
    $Tooltip = "separate set scores with a space, eg 6-4 4-6 6-4\n" .
				"(show incomplete sets with an asterisk: 6-4 6-4 3-0*)\n";
  } else if( $sfmt == 'F' ) {
    $Tooltip = "<goals.behinds><dash><goals.behinds> eg 8.8-10.12 OR\n";
  } else {
    $Tooltip = "<home score><dash><away score> eg 50-36 OR\n";
  }
  $Tooltip .= "F- (home team forfeits) OR\n" .
			 "-F (away team forfeits) OR\n" .
			 "F-F (both forfeit) OR\n" .
			 "W (washout/no result)";
  return( "      <tr class='match'><td>" .
  			$HText . "</td><td>" . $LinkStr . "</td><td>" .
			$AText . "</td><td/><td>" . $Addn .
			   "</td><td><input type='text' id='m" . $MatchRec["MatchRef"] . "' onchange='SendMRes( this );' title='$Tooltip' value='" . $MatchRec["HomeTeamRawScore"] . "'></input><td id='statusm" . $MatchRec["MatchRef"] . "' style='font-style:italic;'></td></tr>\n");
}

function preFbTabX( ) {
  $retval = "";
  $retval .=  "<div fb_protected='true' class='fb_protected_wrapper'>";
  $retval .=   "<div class='tabs clearfix'>";
  $retval .=    "<center>";
  $retval .=     "<div class='left_tabs'>";
  $retval .=      "<ul class='toggle_tabs' id='toggle_tabs_unused'>";
  return( $retval );
}

function postFbTabX( ) {
  $retval = "";
  $retval .=      "</ul>";
  $retval .=     "</div>";
  $retval .=    "</center>";
  $retval .=   "</div>";
  $retval .=  "</div>";
  return( $retval );
}

function addFbTabXi( $mysqli, $text, $url, $class, $sel ) {
  $retval = "";
  $retval .=       "<li class='" . $class . "' >";
  $retval .=        "<a href='" . $url . "' class" . ( $sel ? "='selected' " : "" ) . " onclick='return true;' onmousedown>" . $text . "</a>";
  $retval .=       "</li>";
  return( $retval );
}


  if( !isset( $_REQUEST["champ"] ) ) {
    die( "Error" );
  }
  $ent = 0;
  if( isset( $_REQUEST["ent"] ) ) {
    $ent = 1;
  }
  $ChKey = $_REQUEST["champ"];
  if( !$user_id ) {
    die( "No user ID detected; please ensure you are logged on to Facebook.<br/><br/>Try opening <a href='https://www.thebrasstraps.com/scoretank/index.php/profile-selector' target='_blank'>https://www.thebrasstraps.com/scoretank/index.php/profile-selector</a> and then reload this page." );
  }
  $query = "select * from FBAccred where AccredRole = 1 and AccredKey = $ChKey and FBID = $user_id";
  //$AuthQ = $mysqli->query( $query );
  $AuthQ = $mysqli->query( $query );
  if( !$AuthQ ) {
    die( $_SERVER["QUERY_STRING"] . "<br/>\n" . $query . "<br/>" . mysqli_error( $mysqli ) . "(" . "1" . ")" );
  }
  //$AuthRec = $AuthQ->fetch_array( );
  $AuthRec = $AuthQ->fetch_array( );
  if( $ent && !$AuthRec ) {
    $ent = 0;
  }

//  print_r( $_SESSION );

//  $StData = "<style type='text/css'>";
//  $StData .= "</style>\n";
  $StData = "";
  $StData .= '<link type="text/css" href="jquery-ui-1.8.21.custom/css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="Stylesheet" />';
  $StData .= '<script type="text/javascript" src="jquery-1.7.2.min.js"></script>';
  $StData .= '<script type="text/javascript" src="jquery-ui-1.8.21.custom/js/jquery-ui-1.8.21.custom.min.js"></script>';
  $StData .= '<div id="fb-root"></div>';
  $StData .= addBodyScript( );
  $StData .= '<script type="text/javascript">';
  $StData .= '$(document).ready( function( ) { $( "#fbdialog" ).dialog( { modal: true, autoOpen: false } ) } );';
  //$StData .= '$(document).ready( function( ) { $( "#fbdialog" ).dialog( ) } );';
  $StData .= '</script>';
  $StData .= fbStyles( );
  //$StData .= '<base target="_parent" />';
  $StData .= "<div>";
  $StData .= "<div id='fbdialog' title='ScoreTank'>Message from ScoreTank</div>\n";
  $StData .=  "<h1>ScoreTank</h1><p/>";
  $StData .= preFbTab( );
  $StData .= addFbTab( "Ladder", "champ.php?champ=" . $ChKey, "first", 0 );
  $StData .= addFbTab( "Fixture", "fixt.php?champ=" . $ChKey, "", !$ent );
  $StData .= addFbTab( "Championship Info", "chinfo.php?champ=" . $ChKey, ( $AuthRec ? "" : "last" ), 0 );
//  $StData .= addFbTab( "Championship Info", "http://apps.facebook.com/scoretank/chinfo.php?champ=" . $ChKey, ( $AuthRec ? "" : "last" ), 0 );
  if( $AuthRec ) {
    $StData .= addFbTab( "Enter Results", "fixt.php?ent=1&champ=" . $ChKey, "last", $ent );
  }
  $StData .= postFbTab( );
  $StData .= "</div>";


//    $ChampRec = new bdb;
$query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Championship.Status " .
" FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
" WHERE (((Championship.ChampionshipKey)=$ChKey))";
  $ChampRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $ChampRecr = $ChampRecq->fetch_array( );
  if( ( $ChampRecr === NULL ) ) {
    print( "<h1>Unknown Championship</h1>An error has occurred." );
	die( );
  }
    if($ChampRecr["Status"] == 'H') {
      $matchtbl = 'MatchHist';
      $teamtbl = 'TeamHist';
    } else {
      $matchtbl = 'NMatch';
      $teamtbl = 'Team';
    }
//$MatchRec
$query = "SELECT DISTINCTROW $matchtbl.ChampionshipKey, $matchtbl.RoundNumber, $matchtbl.MatchNumber, $teamtbl.TeamName AS HomeTeamName, Team_1.TeamKey AS AwayTeamKey, Team_1.TeamName AS AwayTeamName, HomeGround.HomeGroundName, $matchtbl.Scheduled, $matchtbl.HomeTeamRawScore, $matchtbl.HomeTeamScore, $matchtbl.HomeTeamSupScore, $matchtbl.AwayTeamRawScore, $matchtbl.AwayTeamScore, $matchtbl.AwayTeamSupScore, $matchtbl.Result, $matchtbl.MatchRef " .
" FROM ($teamtbl INNER JOIN ($matchtbl INNER JOIN $teamtbl AS Team_1 ON $matchtbl.AwayTeamKey = Team_1.TeamKey) ON $teamtbl.TeamKey = $matchtbl.HomeTeamKey) INNER JOIN HomeGround ON $matchtbl.Venue = HomeGround.HomeGroundKey " .
" WHERE ((($matchtbl.ChampionshipKey)=$ChKey)) " .
" ORDER BY $matchtbl.ChampionshipKey, $matchtbl.RoundNumber, $matchtbl.MatchNumber";
  $MatchRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );

//$DataRec
$query = "SELECT DISTINCTROW ChampData.WinPoints, ChampData.LossPoints, ChampData.TiePoints, ChampData.DrawPoints, ChampData.ByePoints, ChampData.ForfeitPoints, ChampData.WalkOverWinScore, ChampData.WalkOverLossScore, ChampData.WalkOverWinPoints, ChampData.WalkOverLossPoints, ChampData.ScoreFormat, ChampData.RoundInterval, ChampData.LadderDisplay, ChampData.LadderSort " .
" FROM ChampData INNER JOIN Championship ON ChampData.DataKey = Championship.DataKey " .
" WHERE (((Championship.ChampionshipKey)=$ChKey))";
  $DataRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $DataRec = $DataRecq->fetch_array( ); // MYSQL_ASSOC );

//    $tz = $ChampRec["SBTZ"];
//    $tz =~ s/^\s+//;
//    if(length($tz) > 0) {
//        $ENV{TZ}=":$tz";
//    }

    $StData   .= "<p><H1>" . $ChampRecr["SBAbbrev"]." ".$ChampRecr["GradeName"] ."</H1>\n".
                $ChampRecr["SportName"]. " - ".$ChampRecr["SeasonName"].
                "<P/><P/><P/>\n";

    $LastRound = 0;
    $LastDate = "";
    $FixtStr = "";
    while( $MatchRec = $MatchRecq->fetch_array( ) ) { // MYSQL_ASSOC ) ) {
        if( $LastRound != $MatchRec["RoundNumber"] ) {
            if( $FixtStr ) {
                $FixtStr = $FixtStr."     <tr><td><br/></td></tr>\n";
                if($LastRound == 1) {
                    $DFixtStr = $FixtStr;
                }
            }
            $LastRound = $MatchRec["RoundNumber"];
            $FixtStr = $FixtStr . MakeMatchHead( "Round " . $LastRound );
        }
		if( $ent ) {
          $FixtStr = $FixtStr . MakeMatchEnt( $MatchRec, $DataRec["ScoreFormat"], $LastDate );
		} else {
          $FixtStr = $FixtStr . MakeMatch( $MatchRec, $DataRec["ScoreFormat"], "", $LastDate );
		}
        $LastDate = $MatchRec["Scheduled"];
    }
//FINALS
    //$FRec = new bdb;
    //$FMRec = new bdb;
    //$TRec = new bdb;
    //$FRRec = new bdb;

//FRound required for display, for title...


    //$FRec
	$query = "SELECT DISTINCTROW FSeries.ChampionshipKey, FSeries.RoundNumber, FSeries.SeriesNumber, FSeries.SeriesName, FSeries.HomeTeamKey, FSeries.HomeDeriv, FSeries.HomeDerivRank, FSeries.HomeDerivChamp, FSeries.AwayTeamKey, FSeries.AwayDeriv, FSeries.AwayDerivRank, FSeries.AwayDerivChamp, FSeries.Result, FMatch.MatchNumber, FMatch.HomeTeamRawScore, FMatch.HomeTeamScore, FMatch.HomeTeamSupScore, FMatch.AwayTeamRawScore, FMatch.AwayTeamScore, FMatch.AwayTeamSupScore, FMatch.Venue, FMatch.Scheduled, FMatch.Result, FMatch.ReverseHA, " .
" FMatch.MatchRef " .
" FROM FSeries , FMatch " .
" WHERE (((FSeries.ChampionshipKey)=$ChKey) OR (((FSeries.HomeDerivChamp)=$ChKey)) OR ((FSeries.AwayDerivChamp)=$ChKey)) " .
" AND ((FSeries.SeriesNumber = FMatch.SeriesNumber) AND (FSeries.RoundNumber = FMatch.RoundNumber) AND (FSeries.ChampionshipKey = FMatch.ChampionshipKey)) " .
" ORDER BY FSeries.RoundNumber, FSeries.RSeriesNumber, FSeries.SeriesNumber, FMatch.MatchNumber";
  $FRecq = $mysqli->query( $query ) or die( $mysqli->error( ) . "<br/>" . $query );

  $LastRound = 0;
  $LastSer = 0;
  while( $FRec = $FRecq->fetch_array( ) ) { //  MYSQL_ASSOC ) ) {
    if( !$FStr ) {
	  $FStr .= "<tr><td><br/></td></tr>" . MakeMatchHead("FINALS") . "<tr><td><br/></td></tr>";
	}
    //if new series...? $FStr
    if( $LastRound != $FRec["RoundNumber"] ) {
	  // $FRRec
      $query = ("SELECT DISTINCTROW FRound.RoundName
FROM FRound
WHERE ChampionshipKey = " . $FRec["ChampionshipKey"]."
AND RoundNumber = ".$FRec["RoundNumber"]);
      $FRRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
      if( $FRRec = $FRRecq->fetch_array( ) ) {  // MYSQL_ASSOC ) ) {
        if($FRRec["RoundName"]) {
          if( $FixtStr ) {
            $FixtStr .= "     <tr><td><br/></td></tr>\n";
          }
          $FStr .= MakeMatchHead($FRRec["RoundName"]);
        }
      }
    }

    if(( $LastRound != $FRec["RoundNumber"]) ||
       ( $LastSer != $FRec["SeriesNumber"])) {
      if( $FixtStr ) {
        $FixtStr .= "     <tr><td><br/></td></tr>\n";
      }
      $LastRound = $FRec["RoundNumber"];
      $LastSer = $FRec["SeriesNumber"];
      if($FRec["SeriesName"]) {
        $FStr .= MakeFNameHead($FRec["SeriesName"]);
      }
    }
    $DummyRec = $FRec;
    $DummyRec['HomeTeamName'] = FinalTeami( $mysqli,
					  $DummyRec['HomeTeamKey'],
                                          $DummyRec['HomeDeriv'],
                                          $DummyRec['HomeDerivRank'],
                                          $DummyRec['HomeDerivChamp']);
    $DummyRec['AwayTeamName'] = FinalTeami( $mysqli,
					  $DummyRec['AwayTeamKey'],
                                          $DummyRec['AwayDeriv'],
                                          $DummyRec['AwayDerivRank'],
                                          $DummyRec['AwayDerivChamp']);
    if(! $DummyRec['Venue']) {
      $DummyRec['HomeGroundName'] = "Venue TBA";
    } else {
      if(!$VenHash[$DummyRec['Venue']] > 0) {
//$TRec
$query = ("SELECT DISTINCTROW HomeGround.HomeGroundName, HomeGround.HomeGroundAddress
FROM HomeGround
WHERE (((HomeGround.HomeGroundKey)=".$DummyRec['Venue']."))");
        $TRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
        if($TRec = $TRecq->fetch_array( ) ) {     // MYSQL_ASSOC )) {
          $VenHash[$DummyRec['Venue']] = $TRec["HomeGroundName"];
        } else {
          $VenHash[$DummyRec['Venue']] = 'Venue not found ('.$DummyRec['Venue'].')';
        }
      }
      $DummyRec['HomeGroundName'] = $VenHash[$DummyRec['Venue']];
    }
	if( $ent ) {
      $FStr .= MakeMatchEnt( $DummyRec, $DataRec["ScoreFormat"], $LastDate );
	} else {
      $FStr .= MakeMatch2( $DummyRec, $DataRec["ScoreFormat"], "", $LastDate );
	}
    $LastDate = $DummyRec["Scheduled"];
  }
  $FixtStr .= $FStr;
//ENDFINALS

  $StData = $StData."     <table border='0'>\n      ".$FixtStr."     </table>\n<P/>";
  $StData .= addPostIframe( );

//print $ChampRec["GradeName"] . " - Fixture";
print $StData;

?>

