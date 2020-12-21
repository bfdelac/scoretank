<SCRIPT>
function SendMRes( inputfld ) {
  var ajax = new Ajax( );
  ajax.responseType = Ajax.JSON;
  ajax.ondone = function( data ) {
	if( data.inputflds ) {
	  afields = data.inputflds.split( " " );
	  var i = 0;
	  for( i = 0; i < afields.length; i++ ) {
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
  var fldids = inputfld.getId( );
//new Dialog( ).showMessage( 'id', inputfld.getId( ) + " - " + inputfld.getValue( ) );
  var fldvals = inputfld.getValue( );
  var queryParams = { "req" : "MRes", "fields" : fldids, "vals" : fldvals };
  ajax.post( "http://www.scoretank.com.au/fb/xentry.php", queryParams );
  document.getElementById( 'status' + inputfld.getId( ) ).setTextValue( "processing..." );
}
</SCRIPT>

<?php

//parameters: 0- $MatchRec, 1- Score format, 2- Highlight teamname, 3- Lastdate
function MakeMatchEnt( $MatchRec, $sfmt, $lastdate ) {
  $LinkStr = "vs";
  if( $MatchRec["AwayTeamKey"] == -1 ) {
	return "      <TR class='match'><TD>" . $MatchRec["HomeTeamName"]. "</TD><TD>Bye</TD>";
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
  return( "      <TR class='match'><TD>" .
  			$HText . "</TD><TD>" . $LinkStr . "</TD><TD>" .
			$AText . "</TD><TD/><TD>" . $Addn .
			   "</TD><TD><INPUT TYPE='text' id='m" . $MatchRec["MatchRef"] . "' onchange='SendMRes( this );' title='$Tooltip' value='" . $MatchRec["HomeTeamRawScore"] . "'></INPUT><TD id='statusm" . $MatchRec["MatchRef"] . "' style='font-style:italic;'></TD></TR>\n");
}


  include 'stpage.php';
  require_once 'facebook.php';
  stconnect( );
  $user_id = fbconnect( );

  if( !isset( $_REQUEST["champ"] ) ) {
    die( "Error" );
  }
  $ent = 0;
  if( isset( $_REQUEST["ent"] ) ) {
    $ent = 1;
  }
  $ChKey = $_REQUEST["champ"];
  $query = "select * from FBAccred where AccredRole = 1 and AccredKey = $ChKey and FBID = $user_id";
  $AuthQ = mysql_query( $query ) or die( mysql_error( ) );
  $AuthRec = mysql_fetch_array( $AuthQ );
  if( $ent && !$AuthRec ) {
    $ent = 0;
  }

  $StData = "<H1>ScoreTank</H1><p/>" .
      "<fb:tabs>" .
	   "<fb:tab-item href='champ.php?champ=" . $ChKey . "' title='Ladder' selected='false'/>" .
	   "<fb:tab-item href='fixt.php?champ=" . $ChKey . "' title='Fixture' selected='" . ( $ent ? "false" : "true" ) . "'/>" .
	   "<fb:tab-item href='chinfo.php?champ=" . $ChKey . "' title='Championship Info' />" .
       ( $AuthRec ? ( "<fb:tab-item href='fixt.php?ent=1&champ=" . $ChKey . "' title='Enter Results' selected='" . ( $ent ? "true" : "false" ) . "'/>" ) : "" ) .
      "</fb:tabs>";

//    $ChampRec = new bdb;
$query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Championship.Status " .
" FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
" WHERE (((Championship.ChampionshipKey)=$ChKey))";
  $ChampRecq = mysql_query( $query ) or die( mysql_error( ) );
  if( !( $ChampRec = mysql_fetch_array( $ChampRecq, MYSQL_ASSOC ) ) ) {
    print( "<h1>Unknown Championship</h1>An error has occurred." );
	die( );
  }
    if($ChampRec["Status"] == 'H') {
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
  $MatchRecq = mysql_query( $query ) or die( mysql_error( ) );

//$DataRec
$query = "SELECT DISTINCTROW ChampData.WinPoints, ChampData.LossPoints, ChampData.TiePoints, ChampData.DrawPoints, ChampData.ByePoints, ChampData.ForfeitPoints, ChampData.WalkOverWinScore, ChampData.WalkOverLossScore, ChampData.WalkOverWinPoints, ChampData.WalkOverLossPoints, ChampData.ScoreFormat, ChampData.RoundInterval, ChampData.LadderDisplay, ChampData.LadderSort " .
" FROM ChampData INNER JOIN Championship ON ChampData.DataKey = Championship.DataKey " .
" WHERE (((Championship.ChampionshipKey)=$ChKey))";
  $DataRecq = mysql_query( $query ) or die( mysql_error( ) );
  $DataRec = mysql_fetch_array( $DataRecq, MYSQL_ASSOC );

//    $tz = $ChampRec["SBTZ"];
//    $tz =~ s/^\s+//;
//    if(length($tz) > 0) {
//        $ENV{TZ}=":$tz";
//    }

    $StData   .= "<p><H1>" . $ChampRec["SBAbbrev"]." ".$ChampRec["GradeName"] ."</H1>\n".
                $ChampRec["SportName"]. " - ".$ChampRec["SeasonName"].
                "<P/><P/><P/>\n";

    $LastRound = 0;
    $LastDate = "";
    $FixtStr = "";
    while( $MatchRec = mysql_fetch_array( $MatchRecq, MYSQL_ASSOC ) ) {
        if( $LastRound != $MatchRec["RoundNumber"] ) {
            if( $FixtStr ) {
                $FixtStr = $FixtStr."     <TR><TD><BR/></TD></TR>\n";
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
  $FRecq = mysql_query( $query ) or die( mysql_error( ) . "<BR/>" . $query );

  $LastRound = 0;
  $LastSer = 0;
  while( $FRec = mysql_fetch_array( $FRecq, MYSQL_ASSOC ) ) {
    if( !$FStr ) {
	  $FStr .= "<TR><TD><BR/></TD></TR>" . MakeMatchHead("FINALS") . "<TR><TD><BR/></TD></TR>";
	}
    //if new series...? $FStr
    if( $LastRound != $FRec["RoundNumber"] ) {
	  // $FRRec
      $query = ("SELECT DISTINCTROW FRound.RoundName
FROM FRound
WHERE ChampionshipKey = " . $FRec["ChampionshipKey"]."
AND RoundNumber = ".$FRec["RoundNumber"]);
      $FRRecq = mysql_query( $query ) or die( mysql_error( ) );
      if( $FRRec = mysql_fetch_array( $FRRecq, MYSQL_ASSOC ) ) {
        if($FRRec["RoundName"]) {
          if( $FixtStr ) {
            $FixtStr .= "     <TR><TD><BR/></TD></TR>\n";
          }
          $FStr .= MakeMatchHead($FRRec["RoundName"]);
        }
      }
    }

    if(( $LastRound != $FRec["RoundNumber"]) ||
       ( $LastSer != $FRec["SeriesNumber"])) {
      if( $FixtStr ) {
        $FixtStr .= "     <TR><TD><BR/></TD></TR>\n";
      }
      $LastRound = $FRec["RoundNumber"];
      $LastSer = $FRec["SeriesNumber"];
      if($FRec["SeriesName"]) {
        $FStr .= MakeFNameHead($FRec["SeriesName"]);
      }
    }
    $DummyRec = $FRec;
    $DummyRec['HomeTeamName'] = FinalTeam($DummyRec['HomeTeamKey'],
                                          $DummyRec['HomeDeriv'],
                                          $DummyRec['HomeDerivRank'],
                                          $DummyRec['HomeDerivChamp']);
    $DummyRec['AwayTeamName'] = FinalTeam($DummyRec['AwayTeamKey'],
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
        $TRecq = mysql_query( $query ) or die( mysql_error( ) );
        if($TRec = mysql_fetch_array( $TRecq, MYSQL_ASSOC )) {
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

//print $ChampRec["GradeName"] . " - Fixture";
print $StData;

?>

