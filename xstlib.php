<?php

	// Joomla links
	define('JOOMLA_MINIMUM_PHP', '5.3.10');
	if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
	{
		die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Joomla!');
	}
	$startTime = microtime(1);
	$startMem  = memory_get_usage();
	define('_JEXEC', 1);
	if (file_exists(__DIR__ . '/defines.php'))
	{
		include_once __DIR__ . '/defines.php';
	}
	if (!defined('_JDEFINES'))
	{
		define('JPATH_BASE', __DIR__);
		require_once JPATH_BASE . '/includes/defines.php';
	}
	require_once JPATH_BASE . '/includes/framework.php';

	// Set profiler start time and memory usage and mark afterLoad in the profiler.
	JDEBUG ? JProfiler::getInstance('Application')->setStart($startTime, $startMem)->mark('afterLoad') : null;

	// Instantiate the application.
	$app = JFactory::getApplication('site');

	$document = JFactory::getDocument();
	$document->setMimeEncoding('application/xml');
  
//	header( "Expires: -1" );
//	header( "Content-type:text/xml" );

	include "fb/stpage.php";
	include "fb/facebook.php";

function RollOverCh( $ROChKey, $season, $fbid, $stdb, $doc, $root, $roteams ) {
  $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonKey, Season.SeasonName, SportingBody.SBTZ, Championship.Status, Grade.GradeKey, Contest.ContestKey, ChampData.DataKey, NumFinalists, Competition.CompKey, Display " .
	           " FROM FBAccred, ChampData, (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
	           " WHERE (((Championship.ChampionshipKey)=" . $ROChKey . " )) " .
			   " AND Championship.DataKey = ChampData.DataKey " .
			   " AND FBAccred.FBID = " . $fbid .
			   " AND AccredRole = 1 " .
			   " AND AccredKey = Championship.ChampionshipKey ";

  if( !( $ChampRecq = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "e6", $stdb ) );
  }

  if( !( $ChampRec = $ChampRecq->fetch_assoc( ) ) ) {
	die( myserror( $doc, $root, $query, "e7: Unknown Championship", 0 ) );
  }
  $SportKey = $ChampRec['SportKey'];
  $GradeKey = $ChampRec['GradeKey'];
  $ContestKey = $ChampRec['ContestKey'];
  $CompKey = $ChampRec['CompKey'];
  $SeasonKey = 0;
  $DataKey = $ChampRec['DataKey'];
  $NumFinalists = $ChampRec['NumFinalists'];
  if( $NumFinalists == null ) {
    $NumFinalists = 0;
  }

  $query = "SELECT * FROM Season " .
			 "WHERE SeasonName = '" . $stdb->real_escape_string( $season ) . "'";
  if( !( $SeasonRecq = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "e8", $stdb ) );
  }

  if( !( $SeasonRec = $SeasonRecq->fetch_assoc( ) ) ) {
	$query = "select max( SeasonKey ) as MaxKey from Season";
	if( !( $SeasonMRecq = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "e9", $stdb ) );
	}
	if( $SeasonMRec = $SeasonMRecq->fetch_assoc( ) ) {
	  $SeasonKey = $SeasonMRec["MaxKey"] + 1;
	}

	$query = "INSERT into Season ( SeasonKey, SeasonName, RecDate ) " .
				" VALUES ( $SeasonKey, '" . $stdb->real_escape_string( $season ) . "', CURRENT_TIMESTAMP )";
	if( !( $stmt = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "e18", $stdb ) );
	}
  } else {
	$SeasonKey = $SeasonRec["SeasonKey"];
  }

  $query = "select max( ChampionshipKey ) as MaxKey from Championship";
  if( !( $ChampMRecq = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "e4", $stdb ) );
  }
  if( !( $ChampMRec = $ChampMRecq->fetch_assoc( ) ) ) {
	die( myserror( $doc, $root, $query, "e5: Unknown Championship", 0 ) );
  }

  $ChKey = $ChampMRec["MaxKey"] + 1;
  $query = "insert into Championship ( ChampionshipKey, CompKey, SeasonKey, ChampionshipDesc, NumFinalists, DataKey, Status, RolledOverFrom, RolledOverTeams ) " .
			" VALUES ( $ChKey, $CompKey, $SeasonKey, '', $NumFinalists, $DataKey, ' ', $ROChKey, $roteams )";
  if( !( $stmt = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "e10", $stdb ) );
  }
  $Chnode = addChildNode( $doc, $root, "q", $query );

  $initAuth = GenRef( $stdb, $doc, $root );
  $query = "insert into FBAccred ( FBID, AccredRole, AccredKey, InitAuth, Display ) values " .
  			" ( $fbid, 1, $ChKey, $initAuth[0], " . $ChampRec['Display'] . " )";
  if( !( $stmt = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "e16", $stdb ) );
  }
  return( $ChKey );
}

function RollOverTeam( $ROChKey, $stdb, $doc, $root, $ChKey ) {
  $query = "select * from Team " .
  				" where ChampionshipKey = $ROChKey " .
				" order by TeamKey ";
  if( !( $TeamRecq = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "et1", $stdb ) );
  }
  $TeamKey = 0;
  $query = "select max( TeamKey ) as MaxKey from Team";
  if( !( $TeamMRecq = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "et2", $stdb ) );
  }
  if( $TeamMRec = $TeamMRecq->fetch_assoc( ) ) {
	$TeamKey = $TeamMRec["MaxKey"] + 1;
  }
  $TeamArr = array( );
  $TeamArr[-1] = -1;
  while( ( $TeamRec = $TeamRecq->fetch_assoc( ) ) ) {
	$query = "INSERT INTO Team " .
	   " ( TeamKey, ClubKey, ChampionshipKey, TeamName, HomeGroundKey,
	       Played, Won, Lost, Drawn, Byes, Forfeit, SFor, Against, ForSup, AgainstSup,
	     Points, Percentage, MatchRatio, LadderPos, EqualPos ) " .
		 " VALUES " .
		 " ( $TeamKey, 0, $ChKey, '" . $stdb->real_escape_string( $TeamRec['TeamName'] ) . "', " . $TeamRec['HomeGroundKey'] .
	", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 )";
	if( !( $stmt = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "e17", $stdb ) );
	}
	$Chnode = addChildNode( $doc, $root, "q", $query );
	$Chnode = addChildNode( $doc, $root, "Team" );
	addAttribute( $doc, $Chnode, "ROid", $TeamRec["TeamKey"] );
	addAttribute( $doc, $Chnode, "id", $TeamKey );
	$TeamArr[$TeamRec["TeamKey"]] = $TeamKey;
    $TeamKey++;
  }
  return( $TeamArr );
}

function UpdateByeSchedToLatestInRound( $stdb, $doc, $root, $ChKey, $roundnum ) {
  $query = "select scheduled from NMatch " .
           " where ChampionshipKey = $ChKey " .
           " and RoundNumber = $roundnum " .
           " and AwayTeamKey <> -1 " .
           " order by scheduled desc";

  if( !( $SchedRecQ = $stdb->query( $query ) ) )
  {
    die( myserror( $doc, $root, $query, "ubs", $stdb ) );
  }
  if( ( $SchedRec = $SchedRecQ->fetch_assoc( ) ) ) {
    $sched = $SchedRec['scheduled'];
    $query = "update NMatch " .
             " set scheduled = '" . $sched . "' " .
             " where ChampionshipKey = $ChKey " .
             " and RoundNumber = $roundnum " .
             " and AwayTeamKey = -1 ";

//die( myserror( $doc, $root, $query, "ubs3", $stdb ) );
    if( !( $stdb->query( $query ) ) ) {
      die( myserror( $doc, $root, $query, "ubs2", $stdb ) );
    }
  }
}

function RollOverMatch( $ROChKey, $stdb, $doc, $root, $ChKey, $TeamArr, $StartDt ) {
  $query = "select * from NMatch where ChampionshipKey = $ROChKey" .
  			" ORDER BY RoundNumber, MatchNumber";
  if( !( $MatchRecq = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "em1", $stdb ) );
  }
  $sch = -1;
  while( ( $MatchRec = $MatchRecq->fetch_assoc( ) ) ) {
	if( $sch < 0 ) {
	  $datetime = $MatchRec['Scheduled'];
	  $sdate = date( 'Y-m-d', strtotime( $datetime ) );
	  $query = "select DateDiff( '" . $StartDt . "', '" . $sdate . "' ) as DtDiff ";
	  $Chnode = addChildNode( $doc, $root, "qqqq", $query );
	  if( !( $DtRecq = $stdb->query( $query ) ) ) {
		die( myserror( $doc, $root, $query, "dt2", $stdb ) );
	  }
	  if( $DtRec = $DtRecq->fetch_assoc( ) ) {
		$sch = $DtRec["DtDiff"];
	  }
	}
	$mref = GenRef( $stdb, $doc, $root );
	$msch = "''";
	if( $MatchRec['Scheduled'] > 0 ) {
	  $msch = " DATE_ADD( '" . $MatchRec['Scheduled'] . "', INTERVAL " . $sch . " DAY ) ";
	}
        else
        {
          $msch = "'" . $StartDt . "'";
        }
	$query = "insert into NMatch ( RoundNumber, ChampionshipKey, MatchNumber, HomeTeamKey, AwayTeamKey, " .
     			" HomeTeamRawScore, HomeTeamScore, HomeTeamSupScore, AwayTeamRawScore, " .
	    		" AwayTeamScore, AwayTeamSupScore, Venue, Scheduled, Result, MatchRef, MatchRef2 ) " .
				" VALUES ( " . $MatchRec['RoundNumber'] . ", $ChKey, " . $MatchRec['MatchNumber'] . ", " . $TeamArr[$MatchRec["HomeTeamKey"]] . ", " . $TeamArr[$MatchRec["AwayTeamKey"]] . ", " .
				" NULL, NULL, NULL, NULL, " .
				" NULL, NULL, " . $MatchRec['Venue'] . ", $msch, ' ', " . $mref[0] . ", 0 ) ";
	$Chnode = addChildNode( $doc, $root, "q", $query );
	if( !( $stmt = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "e15", $stdb ) );
	}
        UpdateByeSchedToLatestInRound( $stdb, $doc, $root, $ChKey, $MatchRec['RoundNumber'] );
    $query = "SELECT * FROM Round " .
				" WHERE RoundNumber = " . $MatchRec['RoundNumber'] . " AND ChampionshipKey = " . $ChKey;
	if( !( $RoundRecq = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "rr2", $stdb ) );
	}
	if( !$RoundRecq->fetch_assoc( ) ) {
	  $rkey = GenRef( $stdb, $doc, $root );
	  $query = "INSERT INTO Round " .
	  		" ( RoundNumber, ChampionshipKey, RoundRef ) VALUES " .
			" ( " . $MatchRec['RoundNumber'] . ", $ChKey, " . $rkey[0] . " )";
	  $Chnode = addChildNode( $doc, $root, "qr", $query );
	  if( !( $stmt = $stdb->query( $query ) ) ) {
		die( myserror( $doc, $root, $query, "e19", $stdb ) );
	  }
	}
  }

  $query = "select * from FMatch where ChampionshipKey = $ROChKey" .
  			" ORDER BY RoundNumber, MatchNumber";
  if( !( $MatchRecq = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "em2", $stdb ) );
  }
  while( ( $MatchRec = $MatchRecq->fetch_assoc( ) ) ) {
	if( $sch < 0 ) {
	  $datetime = $MatchRec['Scheduled'];
	  $sdate = date( 'Y-m-d', strtotime( $datetime ) );
	  $query = "select DateDiff( '" . $StartDt . "', '" . $sdate . "' ) as DtDiff ";
	  $Chnode = addChildNode( $doc, $root, "qqqq", $query );
	  if( !( $DtRecq = $stdb->query( $query ) ) ) {
		die( myserror( $doc, $root, $query, "dt2", $stdb ) );
	  }
	  if( $DtRec = $DtRecq->fetch_assoc( ) ) {
		$sch = $DtRec["DtDiff"];
	  }
	}
	$mref = GenRef( $stdb, $doc, $root );
	$msch = "''";
	if( $MatchRec['Scheduled'] > 0 ) {
	  $msch = " DATE_ADD( '" . $MatchRec['Scheduled'] . "', INTERVAL " . $sch . " DAY ) ";
	}
	$query = "insert into FMatch ( ChampionshipKey, RoundNumber, SeriesNumber, MatchNumber, " .
     			" HomeTeamRawScore, HomeTeamScore, HomeTeamSupScore, AwayTeamRawScore, AwayTeamScore, AwayTeamSupScore, " .
	    		" Venue, Scheduled, Result, MatchRef, MatchRef2, ReverseHA ) " .
				" VALUES ( $ChKey, " . $MatchRec['RoundNumber'] . ", " . $MatchRec['SeriesNumber'] . ", " . $MatchRec['MatchNumber'] . ", " . 
				" NULL, NULL, NULL, NULL, NULL, NULL, " .
				$MatchRec['Venue'] . ", $msch, ' ', " . $mref[0] . ", " . $mref[1] . ", " . $MatchRec['ReverseHA'] . " ) ";
	$Chnode = addChildNode( $doc, $root, "q", $query );
	if( !( $stmt = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "e15", $stdb ) );
	}
    $query = "SELECT * FROM FRound " .
				" WHERE RoundNumber = " . $MatchRec['RoundNumber'] . " AND ChampionshipKey = " . $ChKey;
	if( !( $RoundRecq = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "rr2", $stdb ) );
	}
	if( !$RoundRecq->fetch_assoc( ) ) {
	  $rkey = GenRef( $stdb, $doc, $root );
	  $query = "INSERT INTO FRound " .
	  		" ( RoundNumber, ChampionshipKey, RoundRef, RoundName ) SELECT " .
			"   RoundNumber, " . $ChKey . ", " . $rkey[0] . ", RoundName" .
			"   FROM FRound " .
			"   WHERE ChampionshipKey = " . $ROChKey .
			"   AND RoundNumber = " . $MatchRec['RoundNumber'];
	  $Chnode = addChildNode( $doc, $root, "qr", $query );
	  if( !( $stmt = $stdb->query( $query ) ) ) {
		die( myserror( $doc, $root, $query, "e20", $stdb ) );
	  }
	}
    $query = "SELECT * FROM FSeries " .
				" WHERE RoundNumber = " . $MatchRec['RoundNumber'] . " AND ChampionshipKey = " . $ChKey .
				" AND SeriesNumber = " . $MatchRec['SeriesNumber'];
	if( !( $SeriesRecq = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "sr2", $stdb ) );
	}
	if( !$SeriesRecq->fetch_assoc( ) ) {
	  $rkey = GenRef( $stdb, $doc, $root );
	  $query = "INSERT INTO FSeries " .
	  		" ( ChampionshipKey, RoundNumber, SeriesNumber, SeriesName, " .
			"   HomeTeamKey, HomeDeriv, HomeDerivRank, HomeDerivChamp, " .
			"   AwayTeamKey, AwayDeriv, AwayDerivRank, AwayDerivChamp, " .
			"   Result, SeriesRef, RSeriesNumber ) SELECT " .
			  " $ChKey, RoundNumber, SeriesNumber, SeriesName, " .
			  "   NULL, HomeDeriv, HomeDerivRank, $ChKey, " .
			  "   NULL, AwayDeriv, AwayDerivRank, $ChKey, " .
			  "   NULL, " . $rkey[0] . ", RSeriesNumber " .
			  " FROM FSeries " .
			  " WHERE ChampionshipKey = " . $ROChKey .
			  " AND RoundNumber = " . $MatchRec['RoundNumber'] .
			  " AND SeriesNumber = " . $MatchRec['SeriesNumber'];
	  $Chnode = addChildNode( $doc, $root, "qr", $query );
	  if( !( $stmt = $stdb->query( $query ) ) ) {
		die( myserror( $doc, $root, $query, "e12", $stdb ) );
	  }
	}
  }

  return( $sch );
}

function RollOverFRound( $ROChKey, $stdb, $doc, $root, $ChKey ) {
  $query = "select * from FRound where ChampionshipKey = $ROChKey" .
  			" ORDER BY RoundNumber";
  if( !( $FRoundRecq = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "em1", $stdb ) );
  }
  while( ( $FRoundRec = $FRoundRecq->fetch_assoc( ) ) ) {
	$rkey = GenRef( $stdb, $doc, $root );
	$frkey = $rkey[0];
	$query = "INSERT INTO FRound " .
	  " ( RoundNumber, ChampionshipKey, RoundRef, RoundName ) VALUES " .
	  " ( " . $FRoundRec['RoundNumber'] . ", $ChKey, $frkey, '" .
		$stdb->real_escape_string( $FRoundRec['RoundName'] ) .
	  "' ) ";
	
	if( !( $stmt = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "e13", $stdb ) );
	}
	$Chnode = addChildNode( $doc, $root, "qfr", $query );
  }
}

function RollOverFMatch( $ROChKey, $stdb, $doc, $root, $ChKey, $sch ) {
  $query = " SELECT * FROM FSeries " .
		" WHERE ChampionshipKey = " . $ROChKey;
//		" AND RoundNumber = ".$fmatch{'roundnum'}."
//  AND RSeriesNumber = ".$fmatch{'matchnum'}
  if( !( $FSeriesRecq = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "em1", $stdb ) );
  }
  while( ( $FSeriesRec = $FSeriesRecq->fetch_assoc( ) ) ) {
	$rkey = GenRef( $stdb, $doc, $root );
	$query = "INSERT INTO FSeries " .
			" ( ChampionshipKey, RoundNumber, SeriesNumber, RSeriesNumber, SeriesName, " .
			" HomeTeamKey, HomeDeriv, HomeDerivRank, HomeDerivChamp, " .
			" AwayTeamKey, AwayDeriv, AwayDerivRank, AwayDerivChamp, Result, SeriesRef) VALUES " .
			" ( " . $ChKey . ", " . $FSeriesRec['RoundNumber'] . ", " . $FSeriesRec['SeriesNumber'] . ", " . $FSeriesRec['RSeriesNumber'] . ", NULL, " .
			  " NULL, '" . $FSeriesRec['HomeDeriv'] . "', " . $FSeriesRec['HomeDerivRank'] . ", $ChKey, " .
			  " NULL, '" . $FSeriesRec['AwayDeriv'] . "', " . $FSeriesRec['AwayDerivRank'] . ", $ChKey, NULL, " . $rkey[0] . ")";
	if( !( $stmt = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "e11", $stdb ) );
	}
	$Chnode = addChildNode( $doc, $root, "qfs", $query );
  }

  $query = " SELECT * FROM FMatch " .
		" WHERE ChampionshipKey = " . $ROChKey;
//		" AND RoundNumber = ".$fmatch{'roundnum'}."
//  AND RSeriesNumber = ".$fmatch{'matchnum'}
  if( !( $FMatchRecq = $stdb->query( $query ) ) ) {
	die( myserror( $doc, $root, $query, "em1", $stdb ) );
  }
  while( ( $FMatchRec = $FMatchRecq->fetch_assoc( ) ) ) {
	$rkey = GenRef( $stdb, $doc, $root );
	$msch = "''";
	if( $FMatchRec['Scheduled'] > 0 ) {
	  $msch = " DATE_ADD( '" . $FMatchRec['Scheduled'] . "', INTERVAL " . $sch . " DAY ) ";
	}
	$query = "INSERT INTO FMatch " .
			" ( ChampionshipKey, RoundNumber, SeriesNumber, MatchNumber, " .
			"   HomeTeamRawScore, HomeTeamScore, HomeTeamSupScore, " .
			"   AwayTeamRawScore, AwayTeamScore, AwayTeamSupScore, " .
			"   Venue, Scheduled, Result, MatchRef, MatchRef2, ReverseHA) VALUES " .
			" ( " . $ChKey . ", " . $FMatchRec['RoundNumber'] . ", " . $FMatchRec['SeriesNumber'] . ", " . $FMatchRec['MatchNumber'] . ", " .
			  " NULL, NULL, NULL, " .
			  " NULL, NULL, NULL, " .
			  $FMatchRec['Venue'] . ", $msch, NULL, " . $rkey[0] . ", " . $rkey[1] . ", 0 )";
	if( !( $stmt = $stdb->query( $query ) ) ) {
	  die( myserror( $doc, $root, $query, "e14", $stdb ) );
	}
	$Chnode = addChildNode( $doc, $root, "qfm", $query );
  }
}

function addChildNode( $doc, $parent, $name, $val = null ) {
  $child = "";
  if( isset( $val ) ) {
	$child = $doc->createElement( $name, $val );
  } else {
	$child = $doc->createElement( $name );
  }
  $parent->appendChild( $child );
  return( $child );
}
function addAttribute( $doc, $node, $name, $val ) {
  $att = $doc->createAttribute( $name );
  $att->value = $val;
  $node->appendChild( $att );
  return( $att );
}

function get_facebook_cookie( $app_id, $app_secret ) {
  $args = array();
  parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
  ksort($args);
  $payload = '';
  foreach ($args as $key => $value) {
	if ($key != 'sig') {
	  $payload .= $key . '=' . $value;
    }
  }
  if (md5($payload . $app_secret) != $args['sig']) {
    return null;
  }
  return $args;
}

function myserror( $doc, $root, $query, $str, $mysqli ) {
  if( $query ) {
    $node = $doc->createElement( 'query', $query );
    $root->appendChild( $node );
  }
  $node = $doc->createElement( 'str', $str );
  $root->appendChild( $node );
  if( $mysqli ) {
    $node = $doc->createElement( 'error', "Error text: " . mysqli_error( $mysqli ) );
    $root->appendChild( $node );
  } else {
    $node = $doc->createElement( 'error', "not mysqli" );
  }

  return( $doc->saveXML( ) );
}

	$stdb = sticonnect( );
	$fbid = 0;
	$fbid = fbconnect( );
	//  $fbid = get_facebook_cookie( getAppId( ), getAppSecret( ) );

	$doc = new DomDocument( '1.0' );
	$doc->formatOutput = true;
	$root = $doc->createElement( 'stlib' );
	$root = $doc->appendChild( $root );
	$Chnode = addChildNode( $doc, $root, "test", "test" );
	$user_id = NULL;

  if( !$fbid ) {
    //if( ( $_SERVER['REQUEST_METHOD'] != 'GET' ) ||
    //    ( !isset( $_REQUEST['genref'] ) ) )
	$user = JFactory::getUser();
	if($user && ($user->id > 0)) {
		$user_id = $user->id;
//		$info = "efb = " . $user_id;
//		if(!$user)
//    	die( myserror( $doc, $root, 0, $info, 0 ) );
	} else {
    	die( myserror( $doc, $root, 0, "efb", 0 ) );
    }
  }

  if( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
    if( isset( $_REQUEST['ro111'] ) ) {
		$Chnode = addChildNode( $doc, $root, "ro111", "ro111" );
        $ChKey = 118;
        $ROChKey = 75;
		$TeamArr = RollOverTeam( $ROChKey, $stdb, $doc, $root, $ChKey );
		$startdt = '2018-08-12';
		$sch = RollOverMatch( $ROChKey, $stdb, $doc, $root, $ChKey, $TeamArr, $startdt );
    } else if( isset( $_REQUEST['test'] ) ) {
	  // Load the details of the Rolled Over championship:
	  $ROChKey = 98;
	  $season = 'Test';
	  $startdt = '2015-02-05';
	  $roteams = 1;
	  $rnode = addChildNode( $doc, $root, "ro" );
	  addAttribute( $doc, $rnode, "rochkey", $ROChKey );
	  addAttribute( $doc, $rnode, "season", $season );
	  addAttribute( $doc, $rnode, "startdt", $startdt );
	  addAttribute( $doc, $rnode, "roteams", $roteams );

//	  $ChKey = -1;
		// 0. don't need to insert sporting body, copy from existing
		// 1. championship
	  $ChKey = RollOverCh( $ROChKey, $season, $fbid, $stdb, $doc, $root, $roteams );
	  addAttribute( $doc, $rnode, "ChKey", $ChKey );
		// 2. teams
	  if( $roteams ) {
	    $TeamArr = RollOverTeam( $ROChKey, $stdb, $doc, $root, $ChKey );
	  }
	  if( strlen( $startdt ) > 0 ) {
	    // 3. match
	    $sch = RollOverMatch( $ROChKey, $stdb, $doc, $root, $ChKey, $TeamArr, $startdt );
	    // 4. finals rounds
//	    RollOverFRound( $ROChKey, $stdb, $doc, $root, $ChKey );
	    // 5. finals match
//	    RollOverFMatch( $ROChKey, $stdb, $doc, $root, $ChKey, $sch );
	  }
	} else if( isset( $_REQUEST['genref'] ) ) {
		$mref = GenRef( $stdb, $doc, $root );
		$Chnode = addChildNode( $doc, $root, "ref", $mref[0] );
		$Chnode = addChildNode( $doc, $root, "ref2", $mref[1] );
	} else if( isset( $_REQUEST['listchamps'] ) ) {
		if( isset( $_REQUEST['default'] ) ) {
			$Dnode = addChildNode( $doc, $root, "default" );
			addAttribute( $doc, $Dnode, "default", $_REQUEST['default'] );
		}
		if(!$fbid) {
			$query = "select * from FBAccred " .
						" where AccredRole = 4 " .
						" and FBID = $user_id";
		} else {
			$query = "select * from FBAccred " .
						" where AccredRole = 1 " .
						" and FBID = $fbid";
		}
		$acq = $stdb->query( $query );
	//die( "<ql>" . $query . "</ql>"  );
		if( !( $acq ) ) {
			die( myserror( $doc, $root, $query, "e1", $stdb ) );
		}
		$Chnode = addChildNode( $doc, $root, "Test" );
			addAttribute( $doc, $Chnode, "Iter", 0 );
		while( $acr = $acq->fetch_assoc( ) ) {
			$Chnode = addChildNode( $doc, $root, "Test1" );
			addAttribute( $doc, $Chnode, "Iter", 0 );
			$query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonKey, Season.SeasonName, SportingBody.SBTZ, Championship.Status, Grade.GradeKey " .
				" FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
						" WHERE (((Championship.ChampionshipKey)=" . $acr['AccredKey'] . " ))";
			if( !( $ChampRecq = $stdb->query( $query ) ) ) {
			die( myserror( $doc, $root, $query, "e2", $stdb ) );
			}
			if( !( $ChampRec = $ChampRecq->fetch_assoc( ) ) ) {
			die( myserror( $doc, $root, $query, "e3: Unknown Championship", 0 ) );
			}
			$Chnode = addChildNode( $doc, $root, "Championship" );
			addAttribute( $doc, $Chnode, "ChKey", $acr['AccredKey'] );
			$Gnode = addChildNode( $doc, $Chnode, "Grade" );
			addAttribute( $doc, $Gnode, "GradeKey", $ChampRec["GradeKey"] );
			addChildNode( $doc, $Gnode, "name", $ChampRec["GradeName"] );
			$Snode = addChildNode( $doc, $Chnode, "Season" );
			addAttribute( $doc, $Snode, "SeasonKey", $ChampRec["SeasonKey"] );
			addChildNode( $doc, $Snode, "name", $ChampRec["SeasonName"] );
		}
	} else if( isset( $_REQUEST['isadmin'] ) ) {
          $query = "select * from FBAccred " .
                          " where AccredRole = 1 " .
                          " and AccredKey = " . $_REQUEST['isadmin'] .
                          " and FBID = $fbid";
          $acq = $stdb->query( $query );
          if( !( $acq )) {
            die( myserror( $doc, $root, $query, "e2", $stdb ) );
          }
          if( $acr = $acq->fetch_assoc( ) ) {
            $Chnode = addChildNode( $doc, $root, "admin" );
            addAttribute( $doc, $Chnode, "val", 1 );
            addAttribute( $doc, $Chnode, "fbid", $fbid );
          }
        }
  } else { 	// POST
	if( isset( $_REQUEST['function'] ) ) {
	  if( !strcmp( $_REQUEST['function'], "RollOver" ) ) {
	    // Load the details of the Rolled Over championship:
		$ROChKey = $_REQUEST['rochampkey'];
		$season = $_REQUEST['season'];
		$startdt = $_REQUEST['startdt'];
		$roteams = 0;
		if( isset( $_REQUEST['roteams'] ) ) {
		  $roteams = $_REQUEST['roteams'];
		}
		$rnode = addChildNode( $doc, $root, "ro" );
		addAttribute( $doc, $rnode, "rochkey", $ROChKey );
		addAttribute( $doc, $rnode, "season", $season );
		addAttribute( $doc, $rnode, "startdt", $startdt );
		addAttribute( $doc, $rnode, "roteams", $roteams );

		$ChKey = -1;
		// 0. don't need to insert sporting body, copy from existing
		// 1. championship
		$ChKey = RollOverCh( $ROChKey, $season, $fbid, $stdb, $doc, $root, $roteams );
		addAttribute( $doc, $rnode, "ChKey", $ChKey );
		// 2. teams
		if( $roteams ) {
		  $TeamArr = RollOverTeam( $ROChKey, $stdb, $doc, $root, $ChKey );
		}
		if( strlen( $startdt ) > 0 ) {
		  // 3. match
		  $sch = RollOverMatch( $ROChKey, $stdb, $doc, $root, $ChKey, $TeamArr, $startdt );
		  // 4. finals rounds
		  //RollOverFRound( $ROChKey, $stdb, $doc, $root, $ChKey );
		  // 5. finals match
		  //RollOverFMatch( $ROChKey, $stdb, $doc, $root, $ChKey, $sch );
		}
	  } else if( !strcmp( $_REQUEST['function'], "UpdateMatch" ) ) {
		$Chnode = addChildNode( $doc, $root, "function", "UpdateMatch" );
		$hkey = $_REQUEST['hkey'];
		$akey = $_REQUEST['akey'];
		$mnum = $_REQUEST['mnum'];
		$rnum = $_REQUEST['rnum'];
		$chkey = $_REQUEST['chkey'];
		$Venue = $_REQUEST['Venue'];
		$Sched = $_REQUEST['Sched'];
		$hd = $_REQUEST['hd'];
		$hdr = $_REQUEST['hdr'];
		$ad = $_REQUEST['ad'];
		$adr = $_REQUEST['adr'];
	    $query = "select * from FBAccred where FBID = $fbid and AccredRole = 1 and AccredKey = $chkey";
		if( !( $AccredRecq = $stdb->query( $query ) ) ) {
		  die( myserror( $doc, $root, $query, "mr2a", $stdb ) );
		}
		if( !( $AccredRecq->fetch_assoc( ) ) ) {
		  die( myserror( $doc, $root, "", "not authorised", "" ) );
		}

		$Chnode = addChildNode( $doc, $root, "hd", "val=" . $hd );
if( is_string( $hd ) ) {
 $Chnode = addChildNode( $doc, $root, "hdnn", "val=" . $hd );
if( strlen( $hd ) > 0 ) {
 $Chnode = addChildNode( $doc, $root, "hdnz", "val=" . $hd );
}
}
		if( is_string( $hd ) && ( strlen( $hd ) > 0 ) ) {
		  // It's a FINAL
		  $query = "update FSeries set HomeDeriv = '$hd', HomeDerivRank = $hdr, AwayDeriv = '$ad', AwayDerivRank = $adr " .
			" where ChampionshipKey = $chkey and SeriesNumber = $mnum ";
		  if( !( $MatchRecq = $stdb->query( $query ) ) ) {
			die( myserror( $doc, $root, $query, "mr2", $stdb ) );
		  }
		  $Chnode = addChildNode( $doc, $root, "queryfs", $query );

		  $query = "update FMatch set Venue = $Venue, " .
		  			" Scheduled = '" . $Sched . ":00'" .
			" where ChampionshipKey = $chkey " .
			" and SeriesNumber = $mnum " .
			" and MatchNumber = 1 ";
		  if( !( $MatchRecq = $stdb->query( $query ) ) ) {
			die( myserror( $doc, $root, $query, "mr2", $stdb ) );
		  }
		  $Chnode = addChildNode( $doc, $root, "queryfm", $query );

		} else {
		  // It's a normal season match
	      $query = "update NMatch set HomeTeamKey = $hkey, AwayTeamKey = $akey " .
			( isset( $Venue ) ? ( ", Venue = " . $Venue ) : "" ) .
			( isset( $Sched ) ? ( ", Scheduled = '" . $Sched . ":00'" ) : "" ) .
			" where ChampionshipKey = $chkey and RoundNumber = $rnum and MatchNumber = $mnum ";
		  if( !( $MatchRecq = $stdb->query( $query ) ) ) {
			die( myserror( $doc, $root, $query, "mr2", $stdb ) );
		  }
                  UpdateByeSchedToLatestInRound( $stdb, $doc, $root, $chkey, $rnum );
		}
	  }
	}
  }
  echo $doc->saveXML( );

?>


