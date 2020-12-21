<?php

  header( "Expires: -1" );
  header( "Content-type:text/xml" );

  include "fb/stpage.php";
//  require_once 'facebook.php';
  $stdb = sticonnect( );

?>
<stteam>
<?php

function xGenRefQuery( $query ) {
  $tl = mysql_query( $query ) or die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ) . "</err></stteam>" );
  if( mysql_fetch_array( $tl ) ) {
    return( false );
  }
  return( true ); // OK - no clashes found
}

function xGenRefQueries( $r1, $r2, $tbl, $col1, $col2 ) {
  if( !GenRefQuery( "select * from $tbl " .
  			 " where ( $col1 = $r1 ) " .
			 " or ( $col1 = $r2 ) " .
			 ( $col2 ? " or ( $col2 = $r1 ) " .
			           " or ( $col2 = $r2 ) " : "" ) ) ) {
	return ( false );
  }

  return( true );
}

function xGenRef( ) {
  while( true ) {
    $r1 = mt_rand( 1, 2147483640 );
    $r2 = mt_rand( 1, 2147483640 );

	if( GenRefQueries( $r1, $r2, "NMatch", "MatchRef", "MatchRef2" ) &&
	    GenRefQueries( $r1, $r2, "MatchHist", "MatchRef", "MatchRef2" ) &&
		GenRefQueries( $r1, $r2, "FMatch", "MatchRef", "MatchRef2" ) &&
		GenRefQueries( $r1, $r2, "FMatchHist", "MatchRef", "MatchRef2" ) &&
		GenRefQueries( $r1, $r2, "Round", "RoundRef", "" ) &&
		GenRefQueries( $r1, $r2, "RoundHist", "RoundRef", "" ) &&
		GenRefQueries( $r1, $r2, "FRound", "RoundRef", "" ) &&
		GenRefQueries( $r1, $r2, "FRoundHist", "RoundRef", "" ) &&
		GenRefQueries( $r1, $r2, "FSeries", "SeriesRef", "" ) &&
		GenRefQueries( $r1, $r2, "FSeriesHist", "SeriesRef", "" ) ) {
	  $ret = array( $r1, $r2 );
	  return( $ret );
	}
  }
}

function getFixture( $stdb, $champ ) {
  $retval = "";
  $query = "select *, ht.TeamKey as hTeamKey, ht.TeamName as hTeamName, " .
  			"	at.TeamKey as aTeamKey, at.TeamName as aTeamName " .
			"from NMatch, Team ht, Team at, HomeGround " .
  			" where NMatch.ChampionshipKey = " . $champ .
			" and NMatch.HomeTeamKey = ht.TeamKey " .
			" and NMatch.AwayTeamKey = at.TeamKey " .
			" and NMatch.Venue = HomeGround.HomeGroundKey " .
			" order by RoundNumber, MatchNumber ";

  $m1 = $stdb->query( $query );
  if( !$m1 ) {
	die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ). "</err><m1/></stteam>" );
  }
  //while( $m1r = mysql_fetch_assoc( $m1 ) ) 
  while( $m1r = $m1->fetch_array( ) ) {
    $scheddt = strtotime( $m1r["Scheduled"] );
    $retval .= sprintf( "<match r='%d' m='%d'>", $m1r["RoundNumber"], $m1r["MatchNumber"] );
	$retval .= sprintf( "<sched>%s</sched>", xmlentities( $m1r["Scheduled"] ) );
	$retval .= sprintf( "<textdate>%s</textdate>", xmlentities( strftime( "%a, %e %b %Y", $scheddt ) ) );
	$retval .= sprintf( "<texttime>%s</texttime>", xmlentities( strftime( "%H:%M", $scheddt ) ) );
	$retval .= sprintf( "<hteam key='%d'>%s</hteam>", $m1r["hTeamKey"], xmlentities( $m1r["hTeamName"] ) );
	$retval .= sprintf( "<ateam key='%d'>%s</ateam>", $m1r["aTeamKey"], xmlentities( $m1r["aTeamName"] ) );
	$retval .= sprintf( "<venue key='%d'>%s</venue>", $m1r["Venue"], xmlentities( $m1r["HomeGroundName"] ) );
	$retval .= sprintf( "</match>" );
  }
  $retval .= "<qq>" . xmlentities( $query ) . "</qq>";

  $query = "select * from FRound " .
  			" where ChampionshipKey = " . $champ .
			" order by RoundNumber";
  $f1 = $stdb->query( $query );
  if( !$f1 ) {
	die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ). "</err><m1/></stteam>" );
  }

  while( $f1r = $f1->fetch_array( ) ) {
    $fretval = sprintf( "<fround r='%d'>", $f1r["RoundNumber"] );
	$fretval .= sprintf( "<frname>%s</frname>", xmlentities( $f1r["RoundName"] ) );
	
	$query = "select * " .
//				"	ht.TeamKey as hTeamKey, ht.TeamName as hTeamName, " .
//	            "   at.TeamKey as aTeamKey, at.TeamName as aTeamName " .
				" from FSeries " . // ", Team ht, Team at " .
			  " where FSeries.ChampionshipKey = " . $champ .
			  " and RoundNumber = " . $f1r["RoundNumber"] .
//			  " and FSeries.HomeTeamKey = ht.TeamKey " .
//			  " and FSeries.AwayTeamKey = at.TeamKey " .
			  " order by SeriesNumber ";
	$s1 = $stdb->query( $query );
	if( !$s1 ) {
	  die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ). "</err><m1/></stteam>" );
	}
	while( $s1r = $s1->fetch_array( ) ) {
	  $fretval .= sprintf( "<fseries sno='%d' hk='%d' hd='%s' hdr='%d' hdc='%d' ak='%d' ad='%s' adr='%d' adc='%d'>", $s1r["SeriesNumber"],
	  		$s1r["HomeTeamKey"], $s1r["HomeDeriv"], $s1r["HomeDerivRank"], $s1r["HomeDerivChamp"],
			$s1r["AwayTeamKey"], $s1r["AwayDeriv"], $s1r["AwayDerivRank"], $s1r["AwayDerivChamp"] );
	  if( $s1r["HomeTeamKey"] ) {
		$query = "select * from Team " .
				" where TeamKey = " . $s1r["HomeTeamKey"];
		$t1 = $stdb->query( $query );
		if( !$t1 ) {
		  die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ). "</err><mt1/></stteam>" );
		}
		if( $t1r = $t1->fetch_array( ) ) {
	      $fretval .= sprintf( "<hteam key='%d'>%s</hteam>", $s1r["HomeTeamKey"], xmlentities( $t1r["TeamName"] ) );
		}
	  }
	  if( $s1r["AwayTeamKey"] ) {
		$query = "select * from Team " .
				" where TeamKey = " . $s1r["AwayTeamKey"];
		$t1 = $stdb->query( $query );
		if( !$t1 ) {
		  die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ). "</err><mt1/></stteam>" );
		}
		if( $t1r = $t1->fetch_array( ) ) {
	      $fretval .= sprintf( "<ateam key='%d'>%s</ateam>", $s1r["AwayTeamKey"], xmlentities( $t1r["TeamName"] ) );
		}
	  }
	  $fretval .= sprintf( "<sername>%s</sername>", xmlentities( $s1r["SeriesName"] ) );

      $query = "select * " .
			"from FMatch, HomeGround " .
  			" where FMatch.ChampionshipKey = " . $champ .
			" and FMatch.RoundNumber = " . $f1r["RoundNumber"] .
			" and FMatch.SeriesNumber = " . $s1r["SeriesNumber"] .
			" and FMatch.Venue = HomeGround.HomeGroundKey " .
			" order by MatchNumber ";

	  $m1 = $stdb->query( $query );
	  if( !$m1 ) {
		die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ). "</err><m1/></stteam>" );
	  }
	  while( $m1r = $m1->fetch_array( ) ) {
	    $scheddt = strtotime( $m1r["Scheduled"] );
	    $fretval .= sprintf( "<fmatch r='%d' m='%d'>", $m1r["RoundNumber"], $m1r["MatchNumber"] );
		$fretval .= sprintf( "<sched>%s</sched>", xmlentities( $m1r["Scheduled"] ) );
		$fretval .= sprintf( "<textdate>%s</textdate>", xmlentities( strftime( "%a, %e %b %Y", $scheddt ) ) );
		$fretval .= sprintf( "<texttime>%s</texttime>", xmlentities( strftime( "%H:%M", $scheddt ) ) );
		$fretval .= sprintf( "<venue key='%d'>%s</venue>", $m1r["Venue"], xmlentities( $m1r["HomeGroundName"] ) );
		$fretval .= sprintf( "</fmatch>" );
	  }
	  $fretval .= sprintf( "</fseries>" );
	}

	$fretval .= "</fround>\n";
    $retval .= $fretval;
  }
  $retval .= "<qq>" . xmlentities( $query ) . "</qq>";
  return( $retval );
}

function getTeams( $stdb, $champkey, &$HomeGroundArr ) {
  $retval = "";
  $query = "select * from Team " .
 		" where ChampionshipKey = " . $champkey .
		" order by TeamName";
  $tl = $stdb->query( $query );
  if( !$tl ) {
	die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ) . "</err><tl>" . print_r( $tl ) . "</tl></stteam>" );
  }
  $i = 0;
  while( $tlr = $tl->fetch_array( ) ) {
    $retval .= sprintf( "<team key='%d' sort='%d'>", $tlr["TeamKey"], $i++ );
	$retval .= sprintf( "<teamname>%s</teamname>", xmlentities( $tlr["TeamName"] ) );
	if( $tlr["HomeGroundKey"] ) {
	  $retval .= sprintf( "<homeground key='%d'/>", $tlr["HomeGroundKey"] );
	  $HomeGroundArr[] = $tlr["HomeGroundKey"] ;
	}
	$retval .= sprintf( "</team>" );
  }
  return( $retval );
}

  if( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
    if( isset( $_REQUEST['champ'] ) ) {
	  $chkey = $_REQUEST['champ'];
	  $RolledOverFrom = 0;
	  $RolledOverTeams = 0;

	  printf( '<champ key="%d"/>', $chkey );
	  $HomeGroundArr = array( );

	  $query = "select NumFinalists, RolledOverFrom, RolledOverTeams from Championship " .
	  		" where ChampionshipKey = $chkey ";
	  $nf = $stdb->query( $query );
	  if( $nfr = $nf->fetch_array( ) ) {
	    if( $nfr["NumFinalists"] ) {
	      printf( "<numfinalists>%d</numfinalists>", $nfr["NumFinalists"] );
	    }
	    if( $nfr["RolledOverFrom"] ) {
	      $RolledOverFrom = $nfr["RolledOverFrom"];
	    }
	    if( $nfr["RolledOverTeams"] ) {
	      $RolledOverTeams = $nfr["RolledOverTeams"];
	    }
	  }

	  echo( getTeams( $stdb, $chkey, $HomeGroundArr ) );
	  if( isset( $_REQUEST['showvenues'] ) ) {
	    $v = 0;
	    if( count( $HomeGroundArr ) > 0 ) {
	      $query = "select * from HomeGround " .
				" where HomeGroundKey in ( " . join( ", ", array_unique( $HomeGroundArr ) ) . " )";
		  printf( "<qq>%s</qq>", xmlentities( $query ) );
		  $v1 = $stdb->query( $query );
		  if( !$v1 ) {
		    die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ) . "</err></stteam>" );
		  }
		  while( $v1r = $v1->fetch_array( ) ) {
		    //printf( "<venue key='%d' sort='%d'>%s</venue>", $v1r["HomeGroundKey"], $v++, xmlentities( $v1r["HomeGroundName"] ) );
		    //printf( "<venue key='%d' sort='%d'><name>%s</name><addr>%s</addr></venue>", $v1r["HomeGroundKey"], $v++, xmlentities( $v1r["HomeGroundName"] ) );
		    printf( "<venue key='%d' sort='%d'><name>%s</name><addr>%s</addr></venue>", $v1r["HomeGroundKey"], $v++, xmlentities( $v1r["HomeGroundName"] ), xmlentities( $v1r["HomeGroundAddress"] ) );
		  }
	      }
	      if( ( $RolledOverFrom > 0 ) && ( $RolledOverTeams == 0 ) ) {
		$HGArr2 = array( );
		$dummy = getTeams( $stdb, $RolledOverFrom, $HGArr2 );
printf( "<dd>%s</dd>", $dummy );
		if( count( $HGArr2 ) > 0 ) {
		  $query = "select * from HomeGround " .
                                " where HomeGroundKey in ( " . join( ", ", array_unique( $HGArr2 ) ) . " )";
		  printf( "<qqro>%s</qqro>", xmlentities( $query ) );
		  $v2 = $stdb->query( $query );
		  if( !$v2 ) {
		    die( "<qqroe>" . xmlentities( $query ) . "</qqroe>" . "<err>" . xmlentities( mysql_error( ) ) . "</err></stteam>" );
		  }
		  while( $v2r = $v2->fetch_array( ) ) {
		    printf( "<venue key='%d' sort='%d'><name>%s</name><addr>%s</addr></venue>", $v2r["HomeGroundKey"], $v++, xmlentities( $v2r["HomeGroundName"] ), xmlentities( $v2r["HomeGroundAddress"] ) );
		  }
		}
	      }
	  }
	} else if( $_REQUEST['fixt'] ) {
	  if( $_REQUEST['editmatch'] ) {
	    echo( "<editmatch id='" . $_REQUEST['editmatch'] . "'/>" );
	  }
	  echo( getFixture( $stdb, $_REQUEST['fixt'] ) );
	} else if( $_REQUEST['genref'] ) {
	  $MR = GenRef( $stdb, 0, 0 );
	  echo( "<MR0>" . $MR[0] . "</MR0><MR1>" . $MR[1] . "</MR1>" );
	}
  } else {	// POST
    if( isset( $_REQUEST['function'] ) ) {
	  if( !strcmp( $_REQUEST['function'], "NewMatch" ) ) {
	    $MR = GenRef( $stdb, 0, 0 );
	    $RNum = $_REQUEST["RNum"];
		$ChKey = $_REQUEST["ChKey"];
		$MatchNum = $_REQUEST["MatchNum"];
		$HKey = $_REQUEST["HKey"];
		$AKey = $_REQUEST["AKey"];
		$Venue = $_REQUEST["Venue"];
		$Sched = $_REQUEST["Sched"];
        $query = "insert into NMatch " .
	      " ( RoundNumber, ChampionshipKey, MatchNumber, HomeTeamKey, AwayTeamKey, Venue, Scheduled, MatchRef, MatchRef2 ) " .
		  " values ( $RNum, $ChKey, $MatchNum, $HKey, $AKey, $Venue, '" . $Sched . ":00', " . $MR[0] . ", " . $MR[1] . " ) ";
//die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ) . "</err></stteam>" );

	    printf( "<qi>%s</qi>", xmlentities( $query ) );

		$m1 = $stdb->query( $query );
		if( !$m1 ) {
		  die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ) . "</err></stteam>" );
		}
		echo getFixture( $stdb, $_REQUEST["ChKey"] );
	  } else if( !strcmp( $_REQUEST['function'], "AddTeam" ) ) {
		// get the HomeGroundKey
		$HomeGroundKey = $_REQUEST["teamGroundKey"];
		if( !$HomeGroundKey ) {
		  $query = "select * from HomeGround " .
		  		" where HomeGroundName = '" . $stdb->escape_string( $_REQUEST["teamGround"] ) . "'" .
				" and HomeGroundAddress = '" . $stdb->escape_string( $_REQUEST["groundAddr"] ) . "'";
		  $hg = $stdb->query( $query );
		  if( !$hg ) {
		    die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ) . "</err></stteam>" );
		  }
		  if( $hgr = $hg->fetch_array( ) ) {
		    $HomeGroundKey = $hgr["HomeGroundKey"];
		  } else {
		    $query = "SELECT MAX(HomeGroundKey) As MaxKey FROM HomeGround";
		    $hg = $stdb->query( $query );
			if( !$hg ) {
			  die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ) . "</err></stteam>" );
			}
			if( $hgr = $hg->fetch_array( ) ) {
			  $HomeGroundKey = $hgr["MaxKey"] + 1;
			} else {
			  $HomeGroundKey = 1;
			}
		    $query = "insert into HomeGround ( HomeGroundKey, HomeGroundName, HomeGroundAddress ) VALUES " .
			   " ( " . $HomeGroundKey . ", '" . $stdb->escape_string( $_REQUEST["teamGround"] ) . "', '" .  $stdb->escape_string( $_REQUEST["groundAddr"] ) . "' ) ";
		    $hg = $stdb->query( $query );
			if( !$hg ) {
			  die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ) . "</err></stteam>" );
			}
		  }
		}
// echo( "<testonly/></stteam>" );
// return;
	    // get the new TeamKey
		$teamKey = 0;
		$query = "select MAX(TeamKey) as MaxKey from Team";
echo( "<qq>" . xmlentities( $query ) . "</qq>" );
		$tl = $stdb->query( $query );
		if( !$tl ) {
		  die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ) . "</err></stteam>" );
		}
	    if( $tlr = $tl->fetch_array( ) ) {
		  $teamKey = $tlr["MaxKey"] + 1;
echo( "<mk0/>" );
		} else {
		  $teamKey = 1;
echo( "<mk1/>" );
		}
//die( "<qq>HGKey = $HomeGroundKey, ChKey = " . $_REQUEST["ChKey"] . ", x = " . $_REQUEST["x"] . ", tN = " . $_REQUEST["teamName"] . ", tGK = " . $_REQUEST["teamGroundKey"] . "</qq></stteam>" );

	    $query = "insert into Team " .
		  " ( TeamKey, ClubKey, ChampionshipKey, TeamName, HomeGroundKey, Played, Won, Lost, Drawn, Byes, Forfeit, SFor, Against, ForSup, AgainstSup, Points, Percentage, MatchRatio, LadderPos, EqualPos ) values " .
		  " ( " . $teamKey . ", 0, " . $_REQUEST["ChKey"] . ", '" . $stdb->escape_string( $_REQUEST['teamName'] ) . "', " . $HomeGroundKey .
		  ", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ) ";
//echo( xmlentities( $query ) );
		$tq = $stdb->query( $query );
		if( !$tq ) {
		  die( "<qq>" . xmlentities( $query ) . "</qq>" . "<err>" . xmlentities( mysql_error( ) ) . "</err></stteam>" );
		}
	    $HomeGroundArr = array( );
		echo( getTeams( $stdb, $_REQUEST["ChKey"], $HomeGroundArr ) );
		printf( "<newTeam key='%d'/>", $teamKey );
	  }
	}
  }
?>
</stteam>

