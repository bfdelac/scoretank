<?php
  ini_set('display_errors', 'On');
  header( "Access-Control-Allow-Origin: *" );


function ProcTeam( $teamkey, $roundnumber ) {
// This sub updates the following fields of the team record:
// Played, Won, Lost, Drawn
// For, Against, Points, Percentage, MatchRatio

// if a Roundnumber is included as $_[1], this routine is being used
//  to do a retrospective laddpos...
  $Won = 0;
  $Lost = 0;
  $HWon = 0;
  $HLost = 0;
  $AWon = 0;
  $ALost = 0;
  $Tied = 0;
  $PtsFor = 0;
  $PtsAg = 0;
  $PtsForSup = 0;
  $PtsAgSup = 0;
  $Byes = 0;
  $Forfeit = 0;
  $TRes = 0;
  $GDiff = 0;
  $GAway = 0;
  $ResBuf = array( );
  $RndClause = '';
  $BPoints = 0;    // points using the "B" score format
  if($roundnumber) {
	$RndClause = " AND RoundNumber <= ".$roundnumber;
  }
  // $HomeRes
  $query = "SELECT HomeTeamScore, AwayTeamScore, HomeTeamSupScore, AwayTeamSupScore, HomeTeamRawScore, AwayTeamRawScore, Result, Scheduled, MatchNumber, RoundNumber
	FROM NMatch
	WHERE HomeTeamKey = $teamkey
	AND Result IS NOT NULL " . $RndClause;
  $HomeResQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e11)"}' );
  $accum = "";
  while( $HomeRes = mysql_fetch_array( $HomeResQ ) ) {
    $TRes = '';
	$HomeRes["HomeTeamScore"] ? $PtsFor += $HomeRes["HomeTeamScore"] : 0;
	$HomeRes["AwayTeamScore"] ? $PtsAg += $HomeRes["AwayTeamScore"] : 0;
	$HomeRes["HomeTeamSupScore"] ? $PtsForSup += $HomeRes["HomeTeamSupScore"] : 0;
	$HomeRes["AwayTeamSupScore"] ? $PtsAgSup += $HomeRes["AwayTeamSupScore"] : 0;
	if(strtoupper($HomeRes["Result"]) == "H") {
	  $Won++;
	  $HWon++;
	  $TRes = 'W';
	} else if($HomeRes["Result"] == "A") {
	  $Lost++;
	  $HLost++;
	  $TRes = 'L';
	} else if($HomeRes["Result"] == "f") {
	  $Forfeit++;
	  $TRes = 'L';
	} else if($HomeRes["Result"] == "a") {
	  $Forfeit++;
	  $TRes = 'L';
	} else if(strtoupper($HomeRes["Result"]) == "B") {
	  //            if($HomeRes["Scheduled") lt $tm_buf) {
	  $Byes++;
	  //            }
	  $TRes = 'B';
	} else if($HomeRes["Result"]) {
	  $Tied++;
	  $TRes = 'T';
	}
	if( $HomeRes["Result"] ) {
	  // Teams get 1 point per set won
      $BPoints += $HomeRes["HomeTeamSupScore"];
//die( '{"error" : "ScoreTank error (bbbb...' . $BPoints . '-' . $HomeRes["HomeTeamSupScore"] . "-" . $HomeRes["HomeTeamRawScore"] . ') "}' );
	  if( $HomeRes["HomeTeamRawScore"] == "W" ) {
	    // complete washout
		// 4 points for a washout - ref Brad's email 20 Nov 2013
	    $BPoints += 4;
		// Refer also Brad's email 25 March 2015
	  } else if( $HomeRes["Result"] == "W" ) {
		// 1 point for a partial washout - ref Brad's email 30 Jul 2014
	    $BPoints += 1;
		$BPoints += ( 6 - $HomeRes["HomeTeamScore"] - $HomeRes["AwayTeamScore"] ) / 2;	// split unfinished sets
	  } else if( $HomeRes["Result"] == "B" ) {
	    // don't do anything
	  } else if( $HomeRes["HomeTeamScore"] > $HomeRes["AwayTeamScore"] ) {
	    // Teams get 2 points (win) if the number of games is greater
	    $BPoints += 2;
	  } else if( $HomeRes["HomeTeamScore"] == $HomeRes["AwayTeamScore"] ) {
	    if( $HomeRes["HomeTeamSupScore"] > $HomeRes["AwayTeamSupScore"] ) {
	      $BPoints += 2;
		} else if( $HomeRes["HomeTeamSupScore"] == $HomeRes["AwayTeamSupScore"] ) {
	      $BPoints += 1;
	    }
	  }
	  if( $HomeRes["AwayTeamRawScore"] && is_numeric( $HomeRes["AwayTeamRawScore"] ) ) {
	    if( !( ( $HomeRes["HomeTeamRawScore"] != "W" ) && ( $HomeRes["Result"] == "W" ) ) ) {
	      $BPoints += ( $HomeRes["AwayTeamRawScore"] / 2.0 );
		}
	  }
	}
	$ResBuf[$HomeRes["RoundNumber"].'.'.sprintf("%03d", $HomeRes["MatchNumber"])] = $TRes;
$accum .= "(" . $HomeRes["RoundNumber"] . " - " . $BPoints . ")";
  }

  // $AwayRes
  $query = "SELECT HomeTeamScore, AwayTeamScore, HomeTeamSupScore, AwayTeamSupScore, HomeTeamRawScore, AwayTeamRawScore, Result, MatchNumber, RoundNumber
	FROM NMatch
	Where AwayTeamKey = $teamkey AND Result IS NOT NULL".$RndClause;
  $AwayResQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error( e12 )"}' );
  while( $AwayRes = mysql_fetch_array( $AwayResQ ) ) {
	$TRes = '';
	$AwayRes["AwayTeamScore"] ? $PtsFor += $AwayRes["AwayTeamScore"] : 0 ;
	$AwayRes["HomeTeamScore"] ? $PtsAg += $AwayRes["HomeTeamScore"] : 0 ;
	$AwayRes["AwayTeamScore"] ? $GAway += $AwayRes["AwayTeamScore"] : 0;
	$AwayRes["AwayTeamSupScore"] ? $PtsForSup += $AwayRes["AwayTeamSupScore"] : 0 ;
	$AwayRes["HomeTeamSupScore"] ? $PtsAgSup += $AwayRes["HomeTeamSupScore"] : 0 ;
	if(strtoupper($AwayRes["Result"]) == "A") {
	  $Won++;
	  $AWon++;
	  $TRes = 'W';
	} else if($AwayRes["Result"] == "H") {
	  $Lost++;
	  $ALost++;
	  $TRes = 'L';
	} else if($AwayRes["Result"] == "f") {
	  $Forfeit++;
	  $TRes = 'L';
	} else if($AwayRes["Result"] == "h") {
	  $Forfeit++;
	  $TRes = 'L';
	} else if($AwayRes["Result"]) {
	  $Tied++;
	  $TRes = 'T';
	}
	if( $AwayRes["Result"] ) {
      $BPoints += $AwayRes["AwayTeamSupScore"];
	  if( $HomeRes["HomeTeamRawScore"] == "W" ) {
	    $BPoints += 4;
	  } else if( $AwayRes["Result"] == "W" ) {
	    $BPoints += 1;
		$BPoints += ( 6 - $AwayRes["HomeTeamScore"] - $AwayRes["AwayTeamScore"] ) / 2;	// split unfinished sets
	  } else if( $AwayRes["AwayTeamScore"] > $AwayRes["HomeTeamScore"] ) {
	    $BPoints += 2;
	  } else if( $AwayRes["HomeTeamScore"] == $AwayRes["AwayTeamScore"] ) {
	    if( $AwayRes["AwayTeamSupScore"] > $AwayRes["HomeTeamSupScore"] ) {
	      $BPoints += 2;
		} else if( $AwayRes["HomeTeamSupScore"] == $AwayRes["AwayTeamSupScore"] ) {
	      $BPoints += 1;
	    }
	  }
	  if( $AwayRes["AwayTeamRawScore"] && is_numeric( $AwayRes["AwayTeamRawScore"] ) ) {
	    if( !( ( $AwayRes["HomeTeamRawScore"] != "W" ) && ( $AwayRes["Result"] == "W" ) ) ) {
	    $BPoints += ( $AwayRes["AwayTeamRawScore"] / 2.0 );
		}
	  }
	}
	$ResBuf[$AwayRes["RoundNumber"].'.'.sprintf("%03d", $AwayRes["MatchNumber"])] = $TRes;
$accum .= "(" . $AwayRes["RoundNumber"] . " - " . $BPoints . ")";
  }
//if( $teamkey == 777 ) { die( '{"error" : "ScoreTank error (accum...' . $accum . ') "}' ); }

  $Played = $Won + $Lost + $Tied + $Forfeit;
  $GDiff = $PtsFor - $PtsAg;
  if($Played) {
	$MatchRatio = ($Won + ($Tied / 2)) / $Played * 100;
  } else {
	$MatchRatio = 0;
  }
  if($PtsAg > 0) {
	$Percentage = $PtsFor / $PtsAg * 100;
  } else {
	$Percentage = $PtsFor * 100;
  }
  $Matches = array_keys($ResBuf);
  $SortMatches = $Matches;
  sort( $SortMatches );
  $Streak = 0;
  $LastWin = 0;
  $LastLoss = 0;
  //die( '{"error" : "ScoreTank error( e1 )"}' 
  // $TeamRec
  $query = "SELECT LadderDisplay, TeamName, ScoreFormat
  FROM ChampData, Championship, Team
  WHERE Team.TeamKey = ".$teamkey."
  AND Team.ChampionshipKey = Championship.ChampionshipKey
  AND Championship.DataKey = ChampData.DataKey";
  $TeamRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error( e13 )"}' );
  if($TeamRec = mysql_fetch_array( $TeamRecQ ) ) {
    $LastCount = 0;
	if( preg_match( "J(\d*)", $TeamRec["LadderDisplay"], $pm ) ) {
	  $LastCount = $pm[1];
	}

	$MoreStreak = 0;
	if( preg_match( "K", $DataRec["LadderDisplay"]  ) ) {
	  $MoreStreak = 1;
	}
	while(($LMatch = array_shift($SortMatches)) &&
			(($LastCount > 0) ||
			$MoreStreak)) {
	  if($ResBuf[$LMatch] == 'W') {
		if($MoreStreak && ($Streak > -1)) {
		  $Streak++;
		} else {
		  $MoreStreak = 0;
		}
		if($LastCount> 0) {
		  $LastWin++;
		}
		$LastCount--;
	  } else if($ResBuf[$LMatch] == 'L') {
		if($MoreStreak && ($Streak < 1)) {
		  $Streak--;
		} else {
		  $MoreStreak = 0;
		}
		if($LastCount> 0) {
		  $LastLoss++;
		}
		$LastCount--;
	  } else if($ResBuf[$LMatch] == 'T') {
		if($MoreStreak) {
		  $Streak = 0;
		}
		$LastCount--;
	  } else {
	    // ignore byes in calculating streaks
	  }
	}
  }
}

// $MRRef - Match or Round Ref
function ProcessRes( $MRRef, $Score, $user_id ) {
  $TName = "NMatch";

  $ChkScoreless = 0;
  $DataRec = 0;
  if( false ) {
    // process round... ref www/entered.cgi
  } else {
    // Quick check- is it a final?
    // we should also check here for RoundRefs and FSeriesRefs

	// $DataRec
	$query = "SELECT DISTINCTROW ChampData.ScoreFormat, FSeries.RoundNumber, FSeries.SeriesNumber, FMatch.ChampionshipKey, FSeries.HomeTeamKey, FSeries.AwayTeamKey, FMatch.MatchRef
		FROM ((ChampData INNER JOIN Championship ON ChampData.DataKey = Championship.DataKey) INNER JOIN FMatch ON Championship.ChampionshipKey = FMatch.ChampionshipKey) INNER JOIN FSeries ON (FMatch.SeriesNumber = FSeries.SeriesNumber) AND (FMatch.RoundNumber = FSeries.RoundNumber) AND (Championship.ChampionshipKey = FSeries.ChampionshipKey)
		WHERE FMatch.MatchRef = $MRRef";
	$DataRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error( e1 )"}' );
	if( $DataRec = mysql_fetch_array( $DataRecQ ) ) {
	  // Finals!
//die( '{"error" : "xFinals ' . $MRRef . '"}' );
	  if( !$DataRec["HomeTeamKey"] ||
	      ($DataRec["HomeTeamKey"] <= 0 ) ||
		  !$DataRec["AwayTeamKey"] ||
		  ($DataRec["HomeTeamKey"] <= 0 )) {
		die( '{"error" : "Results may not be entered for this match yet"}' );
	  }
	  $TName = "FMatch";
	  $ChkScoreless = 1;
	} else {
	  // We're doing a match
	  // $DataRec
	  $query = "SELECT DISTINCTROW ChampData.ScoreFormat, NMatch.MatchRef, NMatch.HomeTeamKey, NMatch.AwayTeamKey, NMatch.ChampionshipKey, NMatch.RoundNumber
	    FROM (ChampData INNER JOIN Championship ON ChampData.DataKey = Championship.DataKey) INNER JOIN NMatch ON Championship.ChampionshipKey = NMatch.ChampionshipKey
	    WHERE NMatch.MatchRef = $MRRef";
      $DataRecQ = mysql_query( $query  ) or die( '{"error" : "ScoreTank error( e1 )"}' );
	  if( !( $DataRec = mysql_fetch_array( $DataRecQ ) ) ) {
		if( false ) {   //  if( $_[2] ) 
		  // $DataRec
		} else {
		  // $DataRec
		  $query = "SELECT *
		      FROM MatchHist
			  WHERE MatchRef = $MRRef";
		  $DataRecQ = mysql_query( $query ) or die( "" );
		  if( $DataRec = mysql_fetch_array( $DataRecQ ) ) {
		    die( '{"error" : "Score entry for this match has closed"}' );
		  }
		}
	  }
	  // return( "Cannot find match");
    }
  }
  if( ! $ChkScoreless ) {
	// $UpdRec
	$query = " SELECT Count(*) AS NMatchLeft " .
			 " FROM NMatch " .
			 " WHERE (((NMatch.ChampionshipKey)= " . $DataRec["ChampionshipKey"]. " ) AND ((NMatch.Result Is Null) OR (NMatch.Result = '' )))";
	$UpdRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e3)"}' );
	if( $UpdRec = mysql_fetch_array( $UpdRecQ ) ) {
      if( false )  /// BBBBBB
//	  if(! $UpdRec["NMatchLeft"] )
	  {
		if( false ) { // $_[2] 
		  die( '{"error" : "Results are closed for these matches"}' );
		} else {
		  die( '{"error" : "Results are closed for this match (1)"}' );
		}
	  } else {
	    // all OK
	  }
	} else {
	  if( false ) { // $_[2]
	    die( '{"error" : "Results are closed for these matches"}' );
	  } else {
		die( '{"error" : "Results are closed for this match (2)"}' );
	  }
	}
	$ChkScoreless = 1;
  }

  $MatchRef = $DataRec["MatchRef"];
  if( ( $DataRec["ScoreFormat"] == "T" ) || ( $DataRec["ScoreFormat"] == "B" ) ) {
	$Fmt = "[^0-9\-\* ]";
  } else if( $DataRec["ScoreFormat"] == "F" ) {
	$Fmt = "[^0-9\-\*\. ]";
  } else {
	$Fmt = "[^0-9\-\*]";
  }
  if(!strlen($Score = trim($Score))) {
    die( json_encode( array( "error" => "Error: No score entered" ) ) );
  }
  $home = 0;
  $away = 0;
  $HTScore = 0;
  $ATScore = 0;
  $ATRaw = "null";
  $res = '';

}


  include 'stpage.php';
  require_once 'facebook.php';

  echo "<h1>Hi from xentry.php</h1>\n";
  exit;
 

?>

