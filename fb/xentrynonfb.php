<?php
  header( "Access-Control-Allow-Origin: *" );


function CAChamp( $ikey, $user_id, $tbl, $refcol ) {
  $query = "select * from $tbl, FBAccred " .
      " where $tbl.ChampionshipKey = FBAccred.AccredKey " .
      " and AccredRole = 1 " .			// Championship
	  " and $ikey = $refcol " .
	  " and FBAccred.FBID = " . $user_id;
  $ChkIdQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e51) - ' . mysql_error( ) . ' (' . $query . ')"}' );
  if( mysql_fetch_array( $ChkIdQ ) ) {
    return( true );
  }
//die( '{"error" : "' . $query . '" }' );
//printf( $query . "<BR>" );
  return( false );
}

function CATeam( $ikey, $user_id, $refcol, $MatchCol ) {
  $query = "select * from NMatch, FBAccred " .
      " where NMatch.$MatchCol = FBAccred.AccredKey " .
	  " and AccredRole = 2 " .		  // Team
	  " and $ikey = $refcol " .
	  " and FBAccred.FBID = " . $user_id;
  $ChkIdQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e52) - ' . mysql_error( ) . '"}' );
  if( mysql_fetch_array( $ChkIdQ ) ) {
    return( true );
  }
printf( $query . "<BR>" );
  return( false );
}

function CATeamFinal( $ikey, $user_id, $refcol, $MatchCol ) {
  $query = "select * from FMatch, FSeries, FBAccred " .
      " where FSeries.$MatchCol = FBAccred.AccredKey " .
	  " and AccredRole = 2 " .		  // Team
	  " and $ikey = FMatch.$refcol " .
	  " and FSeries.ChampionshipKey = FMatch.ChampionshipKey " .
	  " and FSeries.RoundNumber = FMatch.RoundNumber " .
	  " and FSeries.SeriesNumber = FMatch.SeriesNumber " .
	  " and FBAccred.FBID = " . $user_id;
  $ChkIdQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e53) - ' . mysql_error( ) . '"}' );
  if( mysql_fetch_array( $ChkIdQ ) ) {
    return( true );
  }
printf( $query . "<BR>" );
  return( false );
}

function CheckAccred( $ikey, $user_id ) {
return( true );
  if( CAChamp( $ikey, $user_id, "NMatch", "MatchRef" ) ) { return( true ); }
  if( CAChamp( $ikey, $user_id, "NMatch", "MatchRef2" ) ) { return( true ); }
  if( CAChamp( $ikey, $user_id, "FMatch", "MatchRef" ) ) { return( true ); }
//die( '{"error" : "mark" }' );
  if( CAChamp( $ikey, $user_id, "FMatch",  "MatchRef2" ) ) { return( true ); }
  if( CAChamp( $ikey, $user_id, "Round", "RoundRef" ) ) { return( true ); }
  if( CAChamp( $ikey, $user_id, "FRound", "RoundRef" ) ) { return( true ); }
  if( CATeam( $ikey, $user_id, "MatchRef", "HomeTeamKey" ) ) { return( true );}
  if( CATeam( $ikey, $user_id, "MatchRef2", "AwayTeamKey" ) ) { return( true );}
  if( CATeamFinal( $ikey, $user_id, "MatchRef", "HomeTeamKey" ) ) { return( true );}
  if( CATeamFinal( $ikey, $user_id, "MatchRef2", "AwayTeamKey" ) ) { return( true );}
  return( false );
}

function ProcessDerived ( $ChampKey ) {
  //see if there are any ladder-dependant positions that depend
  // on completed ladder positions (P)
    // $NMLRec
  $query = "SELECT Count(*) AS NMatchLeft
  FROM NMatch
  WHERE (((NMatch.ChampionshipKey)=".$ChampKey .") AND ((NMatch.Result Is Null) OR (NMatch.Result = '')))";
  $NMLRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e41)"}' );
  if( ( $NMLRec = mysql_fetch_array( $NMLRecQ ) ) && ($NMLRec["NMatchLeft"] > 0)) {
    return;
  }
  // Now, there are no matches left.  Are there any 'F's?
  // $DerivRec
  $query = "SELECT *
  FROM FSeries
  WHERE (FSeries.HomeDerivChamp=".$ChampKey ."
  AND FSeries.HomeDeriv = 'F')
  OR (FSeries.AwayDerivChamp=".$ChampKey ."
  AND FSeries.AwayDeriv = 'F')";
  $DerivRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e42)"}' );

//die( '{"error" : "ScoreTank Testing (temporary - come back in a few minutes) (e41)"}' );
  while($DerivRec = mysql_fetch_array( $DerivRecQ ) ) {
	//keep track of matches to update...?
//if( $DerivRec["SeriesNumber"] == 5 ) {
//die( '{"error" : "ScoreTank error (ex7 - ' . $DerivRec["SeriesNumber"] . "," . $DerivRec["SeriesNumber"] . ')"}' );
//}
	if($DerivRec["HomeDeriv"] == 'F' &&
	   $DerivRec["HomeDerivChamp"] == $ChampKey ) {
	  // Update home champ pos
	  // $NMLRec
	  $query = "SELECT TeamKey
		  FROM Team
		  WHERE (((ChampionshipKey)=".$ChampKey .") AND (LadderPos = ".$DerivRec["HomeDerivRank"]."))";
	  $NMLRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e43)"}' );
	  if( $NMLRec = mysql_fetch_array( $NMLRecQ ) ) {	
	    // there should be!
		// $UpdRec
		$query = "UPDATE FSeries SET HomeTeamKey = ".$NMLRec["TeamKey"]."
				WHERE ChampionshipKey = ". $DerivRec["ChampionshipKey"] ."
				AND RoundNumber = ".$DerivRec["RoundNumber"]."
				AND SeriesNumber = ".$DerivRec["SeriesNumber"];
		$UpdRec = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e44)"}' );
	  }
	}
	if($DerivRec["AwayDeriv"] == 'F' &&
	   $DerivRec["AwayDerivChamp"] == $ChampKey ) {
	  // Update home champ pos
	  // $NMLRec
	  $query = "SELECT TeamKey
			FROM Team
			WHERE (((ChampionshipKey)=".$ChampKey .") AND (LadderPos = ".$DerivRec["AwayDerivRank"]."))";
	  $NMLRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e45)"}' );
	  if( $NMLRec = mysql_fetch_array( $NMLRecQ ) ) {
		// there should be!
		// $UpdRec
		$query = "UPDATE FSeries SET AwayTeamKey = ".$NMLRec["TeamKey"]."
				WHERE ChampionshipKey = ".$DerivRec["ChampionshipKey"] ."
				AND RoundNumber = ".$DerivRec["RoundNumber"]."
				AND SeriesNumber = ".$DerivRec["SeriesNumber"];
		$UpdRec = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e46)"}' );
	  }
	}
  }
  // remember highest pos on ladder?
}

$SortOrdG = "";

function SortTeams( $a, $b ) {
  global $SortOrdG;
  $SortOrdArr = str_split( $SortOrdG );

  foreach( $SortOrdArr as $x ) {
	if($a[LaddDBCol($x)] != $b[LaddDBCol($x)]) {
	  return($b[LaddDBCol($x)] - $a[LaddDBCol($x)]);
	//if($TeamHash[$a][LaddDBCol($x)] != $TeamHash[$b][LaddDBCol($x)]) {
	//  return($TeamHash[$b][LaddDBCol($x)] -
	//		 $TeamHash[$a][LaddDBCol($x)]);
	}
  }
  return 0;
}

function UpdLaddPos( $champkey, $roundnum, $SortOrd ) {	// $SortOrd not used?
  // For each team in the championship, we'll get all their result info
  // up to a certain round and then sort them & stuff.
  // $LTeamRec
  global $SortOrdG;
  $SortOrdG = $SortOrd;
  $query = "SELECT TeamKey
	FROM Team
	WHERE ChampionshipKey = ". $champkey;
  $LTeamRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e41)"}' );
  while($LTeamRec = mysql_fetch_array( $LTeamRecQ ) ) {
	$RetRef = ProcTeam($LTeamRec["TeamKey"], $roundnum );
	//$StData .= "Team info: ".join(' ', keys %RetRef);
	//$RetRef["key"] = $LTeamRec["TeamKey"];
	$TeamHash[$LTeamRec["TeamKey"]] = $RetRef;
  }
  $Iter = 1;
$dbbbg = implode( '-', array_keys( $TeamHash ) ) . ";" . implode( '-', array_values( $TeamHash ) );
  usort( $TeamHash, "SortTeams" );
//$dbbbg = implode( '-', array_keys( $TeamHash ) ) . ";" . implode( '-', array_values( $TeamHash ) );
  $akeys = array_keys( $TeamHash );
//$dbbbg = print_r( $TeamHash );
//die( '{"error" : "Debugging (will be gone shortly - e43.1)' . $dbbbg . '" }' ); // BBBBB
  foreach( $akeys as $akey ) {	// (sort //SortTeams keys %TeamHash) 
	$key = $TeamHash[$akey]["key"];
	//$LTeamRec
	$query = "SELECT COUNT(*) AS PosExists
		FROM TeamLadderPos
		WHERE TeamKey = ".$key.
		" AND RoundNumber = $roundnum";
	$LTeamRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e42)"}' );
	if( ( $LTeamRec = mysql_fetch_array( $LTeamRecQ ) ) && ($LTeamRec["PosExists"] > 0)) {
//die( '{"error" : "Debugging (will be gone shortly - BBB ' . ' ' . $roundnum . ' ) " }' ); // BBBBB
	  // $LTeamRec
	  $query = "UPDATE TeamLadderPos
		SET LadderPos = $Iter
		WHERE TeamKey = ".$key."
		AND RoundNumber = ".$roundnum;
//die( '{"error" : "Debugging (will be gone shortly - e42.1) " . $query }' ); // BBBBB
	  mysql_query( $query ) or die( '{"error" : "ScoreTank error (e42.1)"}' );
	} else {
//die( '{"error" : "Debugging (will be gone shortly - BBBB ' . ' ' . $roundnum . ' ) " }' ); // BBBBB
	  // $LTeamRec
	  $query = "INSERT INTO TeamLadderPos (TeamKey, RoundNumber, LadderPos) " .
			" VALUES ($key, $roundnum, $Iter)";
	  mysql_query( $query ) or die( '{"error" : "ScoreTank error (e43.1)"}' );
	}
	$Iter++;
  }
}

function ProcessLadd( $ChampKey, $RoundNum ) {
//$DataRec
$query = "SELECT DISTINCTROW ChampData.LadderSort
FROM ChampData INNER JOIN Championship ON ChampData.DataKey = Championship.DataKey
WHERE (( Championship.ChampionshipKey = $ChampKey ))";
  $DataRecQ = mysql_query( $query ) or
    die( json_encode( array( "error" => "ScoreTank error (e31)" ) ) );
	// die( json_encode( array( "error" => "ScoreTank error (e31)" . $query ) ) );
$DataRec = mysql_fetch_array( $DataRecQ );

//$LaddRec
$query = "SELECT DISTINCTROW Team.TeamKey, Team.HomeGroundKey, Team.Won, Team.Lost, Team.Drawn, Team.Byes, Team.Forfeit, Team.SFor, Team.Against, Team.ForSup, Team.AgainstSup, Team.Percentage, Team.Points, Team.MatchRatio, Team.Played, Team.TeamName, Team.LadderPos, Team.EqualPos
FROM Team
WHERE (( Team.ChampionshipKey =$ChampKey ))
ORDER BY ".LaddOrder($DataRec["LadderSort"]);

$LaddRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e32)"}' );
  
// $RndRec
$query = "SELECT DISTINCTROW RoundNumber
FROM NMatch
WHERE ChampionshipKey = $ChampKey
AND ( ( Result = '' ) OR ( Result is NULL ) )
ORDER BY RoundNumber";
$RndRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e33)"}' );
  if( $RndRec = mysql_fetch_array( $RndRecQ ) ) {
	$MaxRnd = $RndRec["RoundNumber"] - 1;
  } else {
	// $RndRec
	$query = "SELECT MAX(RoundNumber) AS MaxRnd
				FROM NMatch
				WHERE ChampionshipKey=$ChampKey";
	$RndRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e34)"}' );
	if($RndRec = mysql_fetch_array( $RndRecQ ) ) {
	  $MaxRnd = $RndRec["MaxRnd"];
	} else {
	  $MaxRnd = 0;
	}
  }

  $Iter = 0;
  $LastPoints = 0;
  while($LaddRec = mysql_fetch_array( $LaddRecQ ) ) {
    $Iter++;
	if(($Iter == 1) || ($LaddRec["Points"] != $LastPoints)) {
	  $Rank = $Iter;
	  $LastPoints = $LaddRec["Points"];
	}
	if(($LaddRec["LadderPos"] != $Iter) ||
		($LaddRec["EqualPos"] != $Rank)) {
	  // $UpdRec
	  $query = "UPDATE Team SET LadderPos = $Iter, EqualPos = $Rank WHERE TeamKey = ".$LaddRec["TeamKey"];
	  $UpdRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e33)"}' );
	}
	if($MaxRnd && ($RoundNum == $MaxRnd)) {
	  //#nb this means that we don't add LaddPos entries if there are unresolved matches
	  //#in a round
	  // $UpdRec
	  $query = "SELECT COUNT(*) AS PosExists
			FROM TeamLadderPos
			WHERE TeamKey = ".$LaddRec["TeamKey"].
			" AND RoundNumber = $MaxRnd";
	  $UpdRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e34)"}' );
	  $query = "SELECT COUNT(*) AS PosExists FROM TeamLadderPos WHERE TeamKey = ".$LaddRec["TeamKey"].  " AND RoundNumber = $MaxRnd";
//die( '{ "error" : "PL2a - ' . $query . '" }' );
	  if( ($UpdRec = mysql_fetch_array( $UpdRecQ ) ) && ($UpdRec["PosExists"] > 0)) {
		// $UpdRec
		$query = "UPDATE TeamLadderPos
			SET LadderPos = $Iter
			WHERE TeamKey = ".$LaddRec["TeamKey"]."
			AND RoundNumber = $MaxRnd";
//die( '{"error" : "Debugging (will be gone shortly - e35) " . $query }' ); // BBBBB
		mysql_query( $query ) or die( '{"error" : "ScoreTank error (e35)" }' );
	  } else {
		// $UpdRec
//die( '{ "error" : "PL2d: ' . $UpdRec["PosExists"] . '" }' );
$query = "INSERT INTO TeamLadderPos (TeamKey, RoundNumber, LadderPos) VALUES ()";
//$query = "INSERT INTO TeamLadderPos ()
//VALUES (" . $LaddRec["TeamKey"] . ", $MaxRnd, $Iter )";
		$query = "INSERT INTO TeamLadderPos (TeamKey, RoundNumber, LadderPos) VALUES (".$LaddRec["TeamKey"].", $MaxRnd, $Iter)";
//die( '{"error" : "Debugging (will be gone shortly - e36) " . $query }' ); // BBBBB
		mysql_query( $query ) or die( '{"error" : "ScoreTank error (e36: ' . $query . ' - ' . mysql_error( ) . ')" }' );
//die( '{ "error" : "PL2c: ' . $query . '" }' );
	  }
//die( '{ "error" : "PL2" }' );
	}
  }
//die( '{"error" : "PL1" }' );
  $UpdRnd = $RoundNum;
  if($MaxRnd) {
	while($UpdRnd <= $MaxRnd) {
	  UpdLaddPos($ChampKey, $UpdRnd, $DataRec["LadderSort"]);
	  $UpdRnd++;
	}
  }
}

function GetForfeitScore( $res, $champkey ) {
  // $ForfeitRec
  $query = "SELECT DISTINCTROW ChampData.WalkOverWinScore, ChampData.WalkOverLossScore, ChampData.WalkOverWinPoints, ChampData.WalkOverLossPoints
FROM ChampData INNER JOIN Championship ON ChampData.DataKey = Championship.DataKey
WHERE (((Championship.ChampionshipKey)=$champkey ))";
  $ForfeitRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e22)"}' );
  $ForfeitRec = mysql_fetch_array( $ForfeitRecQ );
  if($res == 'h') {       //home forfeited
	return array('h', $ForfeitRec["WalkOverWinScore"], $ForfeitRec["WalkOverLossScore"], $ForfeitRec["WalkOverWinPoints"], $ForfeitRec["WalkOverLossPoints"]);
  }
  if($res == 'a') {
	return array('a', $ForfeitRec["WalkOverLossScore"], $ForfeitRec["WalkOverWinScore"], $ForfeitRec["WalkOverLossPoints"], $ForfeitRec["WalkOverWinPoints"]);
  }
  if($res == 'f') { //BOTH forfeited!!!
	return array('f', $ForfeitRec["WalkOverLossScore"], $ForfeitRec["WalkOverLossScore"], $ForfeitRec["WalkOverLossPoints"], $ForfeitRec["WalkOverLossPoints"]);
  }
  return(array('', 0, 0, 0, 0));
}

function GetLaddPoints( $teamkey, $Won, $Tied, $Lost, $Byes, $Forfeit ) {
  // $PtRec
  $query = "SELECT DISTINCTROW ChampData.WinPoints, ChampData.LossPoints, ChampData.TiePoints, ChampData.DrawPoints, ChampData.ByePoints, ChampData.ForfeitPoints
FROM (ChampData INNER JOIN Championship ON ChampData.DataKey = Championship.DataKey) INNER JOIN Team ON Championship.ChampionshipKey = Team.ChampionshipKey
WHERE Team.TeamKey = $teamkey ";
  $PtRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e21)"}' );
  $PtRec = mysql_fetch_array( $PtRecQ );
  $retval = ($Won     * $PtRec["WinPoints"]) +
	     ($Tied    * $PtRec["TiePoints"]) +
		 ($Lost    * $PtRec["LossPoints"]) +
		 ($Byes    * $PtRec["ByePoints"]) +
		 ($Forfeit * $PtRec["ForfeitPoints"]);
  return( $retval );
}

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
	  //} else if( $AwayRes["Result"] == "B" ) {
	  //  // don't do anything - not possible
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
  if( $TeamRec["ScoreFormat"] == "B" ) {
    $Points = $BPoints;
  } else {
    $Points = GetLaddPoints($teamkey, $Won, $Tied, $Lost, $Byes, $Forfeit);
  }
  if( $roundnumber ) {
    // We haven't got the full gamut of results - just up to round $_[1],
	// which is going to be used by the calling procedure
	$RetBuf['TeamName'] = $TeamRec["TeamName"];
	$RetBuf['SFor'] = $PtsFor;
	$RetBuf['Against'] = $PtsAg;
	$RetBuf['ForSup'] = $PtsForSup;
	$RetBuf['AgainstSup'] = $PtsAgSup;
	$RetBuf['Won'] = $Won;
	$RetBuf['Lost'] = $Lost;
	$RetBuf['Tied'] = $Tied;
	$RetBuf['Byes'] = $Byes;
	$RetBuf['Forfeit'] = $Forfeit;
	$RetBuf['Played'] = $Played;
	$RetBuf['Points'] = $Points;
	$RetBuf['FPoints'] = $BPoints;
	$RetBuf['MatchRatio'] = $MatchRatio;
	$RetBuf['Percentage'] = $Percentage;
	$RetBuf['HWinLoss'] = $HWon."-".$HLost;
	$RetBuf['AWinLoss'] = $AWon."-".$ALost;
	$RetBuf['Streak'] = $Streak;
	$RetBuf['LastN'] = $LastWin."-".$LastLoss;
	$RetBuf['GDiff'] = $GDiff;
	$RetBuf['GAway'] = $GAway;
	$RetBuf['key'] = $teamkey;
	return($RetBuf);
  }
  // $TeamRec
  $query = "UPDATE Team SET SFor = $PtsFor, Against = $PtsAg, ForSup = $PtsForSup, AgainstSup = $PtsAgSup, Won = $Won, Lost = $Lost, Drawn = $Tied, Byes = $Byes, Forfeit = $Forfeit, Played = $Played, Points = $Points, FPoints = $BPoints, MatchRatio = $MatchRatio, Percentage = $Percentage, HWinLoss = '$HWon-$HLost', AWinLoss = '$AWon-$ALost', Streak = $Streak, LastN = '$LastWin-$LastLoss', GDiff = $GDiff, GAway = $GAway
	WHERE TeamKey = $teamkey";
  mysql_query( $query ) or die( '{"error" : "ScoreTank error (e14) "}' );
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
	$DataRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error( e1 ) - ' . mysql_error( ) . ' "}' );
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
if( false ) {  /// BBBBBB
//	  if(! $UpdRec["NMatchLeft"] ) {
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

  if( strtoupper($Score) == 'W' ) {
	$res = 'W';   // washout
	if( $DataRec["ScoreFormat"] == "B" ) {
	  // $HTScore & $ATScore are the number of games = 0;
//	  $home = 3;
//	  $away = 3;
	}
  } else if ( strtoupper($Score) == "F-F" ) {
	list($res, $HTScore, $ATScore, $hpts, $apts) = GetForfeitScore("f", $DataRec["ChampionshipKey"]);
  } else if ( strtoupper($Score) == "F-" ) {
	list($res, $HTScore, $ATScore, $hpts, $apts) = GetForfeitScore("a", $DataRec["ChampionshipKey"]);
  } else if ( strtoupper($Score) == "-F" ) {
	list($res, $HTScore, $ATScore, $hpts, $apts) = GetForfeitScore("h", $DataRec["ChampionshipKey"]);
  } else if( preg_match( $Fmt, $Score ) ) { // ( $Score =~ /$Fmt/ )
	die( '{"error" : "Unrecognized character in score string.  Please try again."}' );
  } else {
	$ATRaw = "null";
	if( $DataRec["ScoreFormat"] == "F" ) {
	  $Score = preg_replace( " ", "", $Score );
	  if( preg_match( '^([0-9]+)\.([0-9]+)-([0-9]+)\.([0-9]+)$', $Score, $matches ) ) {
	    list( $whole, $hg, $hb, $ag, $ab ) = $matches;
		$HTScore = $hg * 6 + $hb;
		$ATScore = $ag * 6 + $ab;
		$HTRaw   = $hg.'.'.$hb;
		$ATRaw   = $ag.'.'.$ab;
		if( $HTScore > $ATScore ) {
		  $res = 'H';
		} else if( $HTScore < $ATScore ) {
		  $res = 'A';
		} else {
		  $res = 'D';
		}
	  } else {
	    die( '{"error" : "Cannot recognize score: $Score"}' );
	  }
	} else if( $DataRec["ScoreFormat"] == "B" ) {
	  // Burwood Tennis Club format...
	  // need to calculate
	  //    * Points (= sets won + 2 bonus points if a team has the highest number of games in a match)
	  //    * Games For, Games Against
	  //    * Win, Loss, Draw, Sets For, Sets Against
	  $WashoutSets = 0;
	  $homeDiffAvail = 0;
	  $awayDiffAvail = 0;

	  foreach( split( ' ' , $Score ) as $setscore ) {
        $pregmatches = preg_match( '/^([0-9]+)-([0-9]+)([*]?)$/', $setscore, $matches );
	    if( $pregmatches == 0 ) {
		  die( '{"error" : "Invalid format for score ' . $setscore . '"}' );
		}
		$HTScore += $matches[1];		// home team total number of games
		$ATScore += $matches[2];

		if( !$matches[3] ) {
		  // if not an incomplete set
		  if( $matches[1] > $matches[2] ) {
		    if( $matches[1] >= 6 ) {
			  $home++;					// home team number of sets
			} else {
			  $home += 0.5;
			  $away += 0.5;
			}
		  } else if( $matches[1] < $matches[2] ) {
		    if( $matches[2] >= 6 ) {
			  $away++;
			} else {
			  $home += 0.5;
			  $away += 0.5;
			}
		  }
		} else {
//		  $home += 0.5;
//		  $away += 0.5;
	  	  $WashoutSets++;

		  if( $matches[2] < 6 ) {
	        $homeDiffAvail += ( 6 - $matches[2] );
		  }
		  if( $matches[1] < 6 ) {
	        $awayDiffAvail += ( 6 - $matches[1] );
		  }
		}
	  }
	  $bH = 0;  // bonus points, based on total # games
	  $bA = 0;
	  if( $WashoutSets ) {
	    $bA = 1;
	    $bH = 1;
	  } else if( $HTScore > $ATScore ) {
	    $bH = 2;
	  } else if( $HTScore < $ATScore ) {
	    $bA = 2;
	  } else {
	    $bA = 1;
	    $bH = 1;
	  }
	  if( $WashoutSets ) {
	    $res = 'W';
	    // but maybe a winner can still be determined?
		if( $HTScore > $ATScore ) {
		  if( $HTScore > ( $ATScore + $awayDiffAvail ) ) {
	    	$res = 'H';
		  }
		} else if( $HTScore < $ATScore ) {
		  if( ( $HTScore + $homeDiffAvail ) < $ATScore ) {
	    	$res = 'A';
		  }
		}
	    $ATRaw = $WashoutSets;
	  } else if( ( $HTScore ) > ( $ATScore ) ) {
		$res = 'H';
	  } else if( ( $HTScore ) < ( $ATScore ) ) {
		$res = 'A';
	  } else {
	    if( $home > $away ) {
		  $res = 'H';
		} else if( $home < $away ) {
		  $res = 'A';
		} else {
		  $res = 'D';
	    }
	  }
//die( '{"error" : "Home: ' . $home . ', Away: ' . $away . '"}' );

	} else {
	  foreach( split( ' ' , $Score ) as $setscore ) {
        $pregmatches = preg_match( '/^([0-9]+)-([0-9]+)([*]?)$/', $setscore, $matches );
	    if( $pregmatches == 0 ) {
		  die( '{"error" : "Invalid format for score ' . $setscore . '"}' );
		}
		$HTScore += $matches[1];
		$ATScore += $matches[2];
		if( !$matches[3] ) {
		  if( $matches[1] > $matches[2] ) {
			$home++;
		  } else if( $matches[1] < $matches[2] ) {
			$away++;
		  }
		}
	  }
	  if( $home > $away ) {
		$res = 'H';
	  } else if( $home < $away ) {
		$res = 'A';
	  } else {
		$res = 'D';
	  }
	}
  }
  if( ( $DataRec["ScoreFormat"] == "T" ) ||
      ( $DataRec["ScoreFormat"] == "B" ) ) {
	// $UpdRec
	$query = "UPDATE $TName SET Result = '$res', HomeTeamScore = $HTScore, AwayTeamScore = $ATScore, HomeTeamSupScore = $home, AwayTeamSupScore = $away, HomeTeamRawScore = '$Score', AwayTeamRawScore = $ATRaw
			WHERE MatchRef = $MatchRef";
  } else if( $DataRec["ScoreFormat"] == "F" ) {
	$query = "UPDATE $TName SET Result = '$res', HomeTeamScore = $HTScore, AwayTeamScore = $ATScore, HomeTeamSupScore = 0, AwayTeamSupScore = 0, HomeTeamRawScore = '$HTRaw', AwayTeamRawScore = '$ATRaw'
			WHERE MatchRef = $MatchRef";
  } else {
	$query = "UPDATE $TName SET Result = '$res', HomeTeamScore = $HTScore, AwayTeamScore = $ATScore, HomeTeamSupScore = 0, AwayTeamSupScore = 0, HomeTeamRawScore = '$Score'
			WHERE MatchRef = $MatchRef";
  }
  mysql_query( $query ) or
  	die( '{"error" : "ScoreTank error (e4 - ' . $res . ', ' . $HTScore . ', ' . $ATScore . ', ' . $Score . ', ' . ')"}' );
	// die( json_encode( array( "error" => $query ) ) );

  // Now, if it's a final, check to see if the series is finished;
  if( $TName == "FMatch" ) {
	// $SerRec
	$query = "SELECT Count(*) as TypeCount, Result
			FROM FMatch
			WHERE FMatch.ChampionshipKey = ".$DataRec["ChampionshipKey"]."
			AND FMatch.RoundNumber = ".$DataRec["RoundNumber"]."
			AND FMatch.SeriesNumber = ".$DataRec["SeriesNumber"]."
			GROUP BY Result";
	$ResTot = 0;
	$ResHash = array( );
	$SerRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error( e5 )"}' );
	while( $SerRec = mysql_fetch_array( $SerRecQ ) ) {
	  $ResTot += $SerRec["TypeCount"];
	  $ResHash[$SerRec["Result"]] = $SerRec["TypeCount"];
	}
	$FWTeamNum = null;
	$FLTeamNum = null;

	if( ( $ResHash['H'] + $ResHash['h'] ) > ( $ResTot / 2 ) ) {
	  $SerRes = 'H';
	  $FWTeamNum = $DataRec["HomeTeamKey"];
	  $FLTeamNum = $DataRec["AwayTeamKey"];
	} else if( ( $ResHash['A'] + $ResHash['a'] ) > ( $ResTot / 2 ) ) {
	  $SerRes = 'A';
	  $FWTeamNum = $DataRec["AwayTeamKey"];
	  $FLTeamNum = $DataRec["HomeTeamKey"];
    } else {
	  $SerRes = null;
	}
	if($SerRes) {
	  // $UpdRec
	  $query = "UPDATE FSeries
			SET Result = '$SerRes'
			WHERE ChampionshipKey = ".$DataRec["ChampionshipKey"]."
			AND RoundNumber = ".$DataRec["RoundNumber"]."
			AND SeriesNumber = ".$DataRec["SeriesNumber"];
	  mysql_query( $query ) or die( '{"error": "ScoreTank error (e5)"}' );
	  // Now, do any other series depend on this result?  If so, we can update them.
	  // $FRec
	  $query = "SELECT *
			FROM FSeries
			WHERE HomeDerivChamp = ".$DataRec["ChampionshipKey"]."
			AND HomeDerivRank = ".$DataRec["SeriesNumber"];
	  $FRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e6)"}' );
	  while( $FRec = mysql_fetch_array( $FRecQ ) ) {
		$FTeamNum = null;
		if($FRec["HomeDeriv"] == 'W') {
		  $FTeamNum = $FWTeamNum;
		} else if($FRec["HomeDeriv"] == 'L') {
		  $FTeamNum = $FLTeamNum;
		}
		if($FTeamNum) {
		  // $UpdRec
		  $query = "UPDATE FSeries
			SET HomeTeamKey = $FTeamNum
			WHERE ChampionshipKey = ".$FRec["ChampionshipKey"]."
			AND SeriesNumber = ".$FRec["SeriesNumber"];
		  mysql_query( $query ) or die( '{"error" : "ScoreTank error (e7)"}' );
		}
	  }
	  // $FRec
	  $query = "SELECT *
			FROM FSeries
			WHERE AwayDerivChamp = ".$DataRec["ChampionshipKey"]."
			AND AwayDerivRank = ".$DataRec["SeriesNumber"];
	  $FRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e8: ' . mysql_error( ) . ')"}' );
	  while($FRec = mysql_fetch_array( $FRecQ ) ) {
		$FTeamNum = null;
		if($FRec["AwayDeriv"] == 'W') {
		  $FTeamNum = $FWTeamNum;
	    } else if($FRec["AwayDeriv"] == 'L') {
		  $FTeamNum = $FLTeamNum;
		}
//die( '{"error" : "ScoreTank error (ex7 - ' . $FTeamNum . "," . $DataRec["SeriesNumber"] . ')"}' );
		if($FTeamNum) {
		  // $UpdRec
		  $query = "UPDATE FSeries
			SET AwayTeamKey = $FTeamNum
			WHERE ChampionshipKey = ".$FRec["ChampionshipKey"]."
			AND SeriesNumber = ".$FRec["SeriesNumber"];
		  mysql_query( $query ) or die( '{"error" : "ScoreTank error (e7)"}' );
		}
	  }
	}
  } else {
    ProcTeam($DataRec["HomeTeamKey"]);
	ProcTeam($DataRec["AwayTeamKey"]);
	//if( $ByeRound < $DataRec["RoundNumber"] ) 
	if( true ) {
	  // $ByeRec
	  // this query gets the ...?
	  $query = "SELECT RoundNumber FROM NMatch
			WHERE ChampionshipKey = ".$DataRec["ChampionshipKey"].
			" AND AwayTeamKey <> -1
			AND RoundNumber > ".$DataRec["RoundNumber"].
			" ORDER BY RoundNumber";
	  $ByeRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e8)"}' );
	  if( $ByeRec = mysql_fetch_array( $ByeRecQ ) ) {
		$ByeRound = $ByeRec["RoundNumber"] - 1;
	  } else {
		// $ByeRec
		$query = "SELECT Max(RoundNumber) As MaxRoundNumber from NMatch
				WHERE ChampionshipKey = ".$DataRec["ChampionshipKey"];
		$ByeRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e9)"}' );
		$ByeRec = mysql_fetch_array( $ByeRecQ );
		$ByeRound = $ByeRec["MaxRoundNumber"];
	  }
	  // #print "BR = $ByeRound\n";
	  // $ByeRec
	  $query = "SELECT * FROM NMatch
			WHERE ChampionshipKey = ".$DataRec["ChampionshipKey"].
			" AND AwayTeamKey = -1
			AND ( (Result <> 'B' ) or ( Result is NULL ) )
			AND RoundNumber <= $ByeRound";
//die( '{"error" : "testing (back soon)", "HTK" : "' . $query . '"}' );
	  $ByeRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e10)"}' );
	  while($ByeRec = mysql_fetch_array( $ByeRecQ ) ) {
		// $UpdBRec
		$query = "UPDATE NMatch SET Result = 'B' WHERE MatchRef = ".$ByeRec["MatchRef"];
		$UpdBRecQ = mysql_query( $query ) or die( '{"error" : "ScoreTank error (e11)"}' );
		////$StData .=          "UPDATE NMatch SET Result = \'B\' WHERE MatchRef = ".$ByeRec["MatchRef"]."<br>\n";
// die( '{"error" : "testing (back soon)", "HTK" : "' . $ByeRec["HomeTeamKey"] . '"}' );
		ProcTeam($ByeRec["HomeTeamKey"]);
	  }
	}
	// print "xxx ppp\n";
  }
//die( '{ "error" : "DR' . '" }' );
  //die( json_encode( array( "error" => ( "ChXxx" . $DataRec["ChampionshipKey"] ) ) ) );

  return array($DataRec["ChampionshipKey"], $DataRec["RoundNumber"]);
}


  include 'stpage.php';
//  require_once 'facebook.php';
  stconnect( );
  $user_id = 0;	//	fbconnect( 1 );


//  $query = "select * from FBAccred " .
//    " where FBID = $user_id ";
//  $chkq = mysql_query( $query ) or die( '{"error" : "ScoreTank error( 2 )"}' );

//  if( !( $chk = mysql_fetch_array( $chkq ) ) ) {
 //   die( '{"message" : "Authentication Key not available", "error" : "ScoreTank error( 4 )"}' );
//  }


//  $tstaccum = " data: ";
  //$ikeys = split( '&', $_POST['fields'] );
// $ikeys = preg_replace( '/\D/', '', split( '&', $_POST['fields'] ) );
//  $vals = split( '&', $_POST['vals'] );
 
//  $ikeys = array_filter( $ikeys );
//if( count( $ikeys ) == 0 ) {
//$ikeys[0] = "2021371506";
//$vals[0] = "30-30";
//}

//  $accredOK = true;
//  foreach( $ikeys as $ikey ) {
//    $accredOK = $accredOK && CheckAccred( $ikey, $user_id );
//  }
//  if( !$accredOK ) {
//    die( '{"message" : "Authentication Key not available", "error" : "ScoreTank error(5)"}' );
//  }

// "You should avoid building your own JSON, and instead use the PHP function json_encode"
//  $i = 0;
//  $chkey = 0;
//  $rnum = 0;
//  for( $i = 0; $i < count( $ikeys ); $i++ ) {
//    list( $chkey, $rnum ) = ProcessRes( $ikeys[$i], $vals[$i], $user_id );
//die( json_encode( array( "error" => ( "PLx" . $chkey ) ) ) );
//  }
//die( '{"error" : "PL' . $chkey . ':::' . $rnum . '" }' );
  ProcessRes( "1215706995", "2-6 2-6 5-6 4-6 6-4 1-6", 0 );
  ProcessLadd( 105, 10 );
  ProcessDerived( 105 );
//die( '{"error" : "PLx" }' );

  echo '{"inputflds" : "' . join( " ", $ikeys ) . '", "message" : "Score registered: ' . join( ";", $vals ) . ' for ' . join( ";", $ikeys ) . '"}';
?>

