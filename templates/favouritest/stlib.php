<?php

include __DIR__ . '/stpage.php';

if (!defined('_JDEFINES'))
{
 	define('JPATH_BASE', __DIR__ . '/../..');
 	require_once JPATH_BASE . '/includes/defines.php';
}

function href( $str ) {
  if( strlen( $_SERVER['PATH_INFO'] ) > 1 ) {
    return( " href='" . $str . "'" );
  }
  return( " href='index.php/" . $str . "'" );
}

function pagehead( $title, $showjq = true ) {
  $path_parts = pathinfo( $_SERVER["PHP_SELF"] );
  //$linkbase = $path_parts['dirname'];
  $linkbase = preg_split( "/(\/index.php)/", $_SERVER["REQUEST_URI"] )[0];
  $retval =
'<base href="' . $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . '" />' . "\n" .
'<meta http-equiv="content-type" content="text/html; charset=utf-8" />' . "\n" .
'<meta name="title" content="' . $title . '" />' . "\n" .
'<meta name="author" content="Administrator" />' . "\n" .
'<meta name="description" content="ScoreTank - Free Community Sports Management" />' . "\n" .
'<meta name="generator" content="Joomla! 1.6 - Open Source Content Management" />' . "\n" .
'<title>' . $title . '</title>' . "\n" .
//'<link href="/templates/favouritest/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />' . "\n" .
//'<script src="/media/system/js/core.js" type="text/javascript"></script>' . "\n" .
//'<script src="/media/system/js/mootools-core.js" type="text/javascript"></script>' . "\n" .
//'<script src="/media/system/js/caption.js" type="text/javascript"></script>' . "\n" .
//'<script src="/media/system/js/mootools-more.js" type="text/javascript"></script>' . "\n" .
//'<script src="/templates/favouritest/javascript/md_stylechanger.js" type="text/javascript" defer="defer"></script>' . "\n";
'<link href="' . $_SERVER["REQUEST_URI"] . '" rel="canonical" />' . "\n" .
'<link href="' . $linkbase . '/templates/favouritest/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />' . "\n" .
'<link rel="stylesheet" href="' . $linkbase . '/media/jui/css/bootstrap.min.css" type="text/css"/>' . "\n" .
'<link rel="stylesheet" href="' . $linkbase . '/media/jui/css/bootstrap-responsive.css" type="text/css"/>' . "\n";
if( $showjq ) {
	$retval .=
	'<script src="' . $linkbase . '/media/jui/js/jquery.min.js" type="text/javascript"></script>' . "\n" .
	'<script src="' . $linkbase . '/media/jui/js/jquery-noconflict.js" type="text/javascript"></script>' . "\n" .
	'<script src="' . $linkbase . '/media/jui/js/jquery-migrate.min.js" type="text/javascript"></script>' . "\n" .
	'<script src="' . $linkbase . '/media/system/js/caption.js" type="text/javascript"></script>' . "\n" .
	'<script src="' . $linkbase . '/media/jui/js/bootstrap.min.js" type="text/javascript"></script>' . "\n" .
	'<script type="text/javascript">' . "\n" .
	'jQuery(window).on(\'load\', function() { new JCaption(\'img.caption\');' . "\n" . '});' . "\n" . '</script>' . "\n";
}
  return( $retval );

}

function menuhead( ) {
  $retmenu = '<div class="moduletable">' .
//			  '<h3><span class="backh"><span class="backh2"><span class="backh3">ScoreTank info</span></span></span></h3>' .
			  '<ul class="menu">';
  return( $retmenu );
}

function menufoot( $script ) {
  $retmenu =  '</ul>' .	
  			  add_login( ) .
			 '</div>';
  return( $retmenu );
}

function menuli( $inum, $active, $pageurl, $pagename, $submenu = null, $disabled = false ) {
	if($disabled) {
		return ('');
		//return('<li id="item-' . $inum . '"><i>' . $pagename . '</i></li>');
	}
	$retmenu = '<li id="item-' . $inum . '" ' . ( $active ? 'class="current active"' : ( isset( $submenu ) ? 'class="active deeper parent"' : "" ) ) . '><a ' . href( $pageurl ) . '>' . $pagename . '</a>';
	if( isset( $submenu ) ) {
		$retmenu .= '<ul class="submenu">' . $submenu . '</ul>';
	}
	$retmenu .= '</li>';
	return( $retmenu );
}


function add_login( ) {
  if( !strstr( $_SERVER['SERVER_NAME'], 'thebrasstraps.com' ) ) {
	return '';
  }
  if(!( $_SERVER["REQUEST_SCHEME"] == "https" )) {
    return '';
  }

  return '<div id="loginbox"></div>';
}

function st_home( ) {
  $retval = "";
  $retval .= '<div class="item-page">' .
//  			  '<table width="99%" border="0" style="border:none; border-spacing:0px;"><tr><td>' .
			   '<h2>Welcome</h2>' .
//			  '</td><td align="right">' .
//			  '</td></tr></table>' .
			  '<p>ScoreTank is a free, interactive, easy to use system for keeping scores of sporting events and other sorts of competitions.</p>' .
			  '<p>It makes it easy to check on how your team\'s going and who you are playing next week, plus lots of other information. The system gives you the results online as soon as they are available, and calculates ladders and other statistics for you.</p>';
  $retval .=   '<ul>' .
				'<li><a ' . href( 'about-scoretank' ) . '>About ScoreTank</a></li>' .
  				'<li style="vertical-align:\'middle\'">Search the <a ' . href( 'championship-list' ) . '>list of Championships</a> or enter your team number:<br/>' .
				'<form style="vertical-align:\'middle\'" enctype="application/x-www-form-urlencoded" method="get" action="index.php/team">' .
   '<table style="border: 0px; border-collapse:collapse; border-width: 0px; vertical-align: top;"><tr style="vertical-align: top; border-width: 0px;"><td style="border-collapse:collapse; border-width: 0px;"><input name="team" type="text"/></td><td style="border-collapse:collapse; border-width: 0px;"><input type="submit" value="Go team!"/></td></tr></table>' .
				'</form></li>' .
//				'<li>Business Information and Contact details</li>' .
			   '</ul>' .
			  '</div>';

  $retmenu = "";
  $retmenu = '<div class="moduletable">' .
			  '<ul class="menu">' .
			   '<li id="item-101" class="current active"><a href="index.php">Welcome</a></li>' .
			   '<li id="item-103"><a href="index.php/about-scoretank">About ScoreTank</a></li>' .
			   '<li id="item-105"><a href="index.php/championship-list">Championship List</a></li>' .
			  '</ul>' .
			  add_login( ) .
			 '</div>';
  
  //$retmenu = "";
  return array( $retval, "", $retmenu );
}

function champlist( ) {
  $retval = "";
  $retval = "Championship List";
  $mysqli = stconnect( );

  $retval  = '<div class="item-page">' . "\n";
  $retval .=  '<h2>Championship List</h2>' . "\n";
  $retval .= "Here are a selection of recent championships. If your championship does not appear, contact your competition administrator for the direct link.<p/>\n";

  $query =  "SELECT DISTINCTROW Sport.SportName, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Season.RecDate, Championship.ChampionshipKey, Championship.Status, Grade.GradeName, Season.SeasonName " .
				" FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) " .
				" INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
				" WHERE ( ( Championship.Status <> 'H' ) && ( Championship.Status <> 'D' ) ) " .
				" AND Season.RecDate > DATE_SUB( CURDATE(), INTERVAL 3 YEAR ) " .
				" ORDER BY Sport.SportName, SportingBody.SBAbbrev, Season.RecDate DESC, Championship.Sort, Championship.ChampionshipKey, Grade.GradeName";

  $GradeList = "";
  $ChList = "";
  $SList = "";
  $SIdx = 0;
  $LastSport = "";
  $index = 0;

  if( !( $ChListQ = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(cl2): " . $mysqli->error ) );
  }
  while( $ChListR = $ChListQ->fetch_array( ) ) {
    $index++;

	if( $ChListR["SportName"] != $LastSport ) {
	  $SIdx++;
	  if( $LastSport != "" ) {
		$ChList .= " " . "<ul>" . $GradeList . "</ul>\n";
		$GradeList = "";
	  }
/* The following looks backwards but it's because if I put an <a id/> before a sport, it makes the whole sport name an anchor, perhaps a bug in jquery?...
*/
	  $ChList .= "<h3>" . htmlspecialchars( $ChListR["SportName"] ) . "<a id='" . htmlspecialchars( $SIdx ) . "'>&nbsp;</a></h3>\n";
	  //$ChList .= "<h3><a id='" . htmlspecialchars( $SIdx ) . "'/><br/>" . htmlspecialchars( $ChListR["SportName"] ) . "</h3>\n";
	  $LastSport = $ChListR["SportName"];
	  $LastSBAbbrev = "";
	  if( $SList ) {
	    $SList .= " | ";
	  }
	  $SList .= "<a href='#" . $SIdx . "'>" . $ChListR["SportName"] . ( $ChListR["Status"] == 'D' ? '*' : '' ) . "</a>";
	}
	if( $ChListR["SBAbbrev"] != $LastSBAbbrev ) {
	  if( $LastSBAbbrev != "" ) {
	    $ChList .= " <ul>" . $GradeList . "</ul>\n";
		$GradeList = "";
	  }
	  $ChList .= " <h4>" . htmlspecialchars( $ChListR["SBAbbrev"] ) . " - " . htmlspecialchars( $ChListR["SBSportingBodyName"] ) . "</h4>\n";
	  $LastSBAbbrev = $ChListR["SBAbbrev"];
	  $LastSeason = "";
	}
//	if( $ChListR["SeasonName"] != $LastSeason ) {
//	  if( $LastSeason != "" ) {
//	    $ChList .= " <ul>" . $GradeList . "</ul>\n";
//		$GradeList = "";
//	  }
//	  $ChList .= " " . $ChListR["SeasonName"] . "\n";
//	  $LastSeason = $ChListR["SeasonName"];
//	}
	$demostat = '';
	if( $ChListR["Status"] == 'D' ) {
	  $demostat = ' - Demo';
	}
	$GradeList .= " <li>" . $ChListR["SeasonName"] . " - <a href='champ?champ=" . $ChListR["ChampionshipKey"] . "'>" . $ChListR["GradeName"] . "</a>" . $demostat . "</li>\n";
  }
  if($index == 0) {
	$ChList = "     There are no championships currently in the system.\n    ";
  } else {
	$ChList .= " <ul>" . $GradeList . "</ul>\n";
  }
  $retval .= $SList . "<p/>";
  $retval .= $ChList;
 
  $retval .= '</div>';

  $retmenu = menuhead( ) .
  				menuli( 101, 0, 'welcome', 'Welcome', null ) .
  				menuli( 103, 0, 'about-scoretank', 'About ScoreTank', null ) .
  				menuli( 105, 1, 'championship-list', 'Championship List', null ) .
  			 menufoot( 'champlist' );
  return array( $retval, "", $retmenu );
}

function LaddHead( $displ ) {
  $matches = 0;
  if( preg_match( '/J(\d+)/', $displ, $matches ) ) {
	$LastCount = $matches[1];
	$parts = preg_split( '/J(\d+)/', $displ );
	$Heads = implode( "J", $parts );
  } else {
	$Heads = $displ;
  }
  $TblHead = '<td></td><th style="text-align:left;">Ladder</th>';
  for( $idx = 0; $idx < strlen( $Heads ); $idx++ ) {
	if( substr($Heads, $idx, 1) == 'J' ) {
	  $ColHead = sprintf( LaddCol('J'), $LastCount );
	  $TblHead .= "<th>" . xmlentities( $ColHead ) . "</th>";
	} else {
	  $TblHead .= "<th>" . xmlentities( LaddCol( substr( $Heads, $idx, 1 ) ) ) . "</th>";
	}
  }
  return( "<tr class='ladderhead'>" . $TblHead . "</tr>\n" );
}

function LaddRow( $iter, $LadderDisplay, $TeamRecR ) {
  $LadderDisplay = preg_replace( "/\d/", "", $LadderDisplay );
  $TblRow = "<td>" . $iter . "</td>";
  $TblRow .= "<td style='text-align:left;'><a href='../index.php/team?team=" . $TeamRecR["TeamKey"] . "'>" . xmlentities( $TeamRecR["TeamName"] ) . "</a></td>";
  for( $idx = 0; $idx < strlen( $LadderDisplay ); $idx++ ) {
    $colval = $TeamRecR[LaddDBCol(substr($LadderDisplay, $idx, 1))];
	if( !isset( $colval ) ) {
	  $TblRow .= "<td>&nbsp;</td>";
	} else if( preg_match( '/\./', $colval ) ) {
	  $TblRow .= "<td>" . sprintf( "%.1f", $colval ) . "</td>";
	} else if( substr( $LadderDisplay, $idx, 1 ) == 'K' ) {
	  if( $colval < 0 ) {
		$TblRow .= '<td>' . abs( $colval ) . 'L</td>';	  } else if($_ > 0) {
		$TblRow .= '<td>' . $colval . 'W</td>';
	  } else {
	    $TblRow .= "<td>0</td>";
	  }
	} else {
	  $TblRow = $TblRow . "<td>" . $colval . "</td>";
	}
  }
  return( "     <tr class='ladderdata'>" . $TblRow . "</tr>\n" );
}

function addEnterResSubMenu($ChKey, $sel = 0) {
	if( !strstr( $_SERVER['SERVER_NAME'], 'thebrasstraps.com' ) ) {
		return '';
	}
	if(!( $_SERVER["REQUEST_SCHEME"] == "https" )) {
		return '';
	}
	$user = JFactory::getUser();
	return menuli( 113, $sel, 'enter-results?champ=' . $ChKey, 'Enter results', null, $disabled = ($user->id <= 0) );
}

function champ( ) {
  $retval = "Championship ladder";

  $ChKey = JRequest::getVar( 'champ' );
  if( !( preg_match('/^\d+$/', $ChKey ) ) || ( !$ChKey ) ) {
	return( htmlspecialchars( "Error(ch1)" ) );
  }

  $mysqli = stconnect( );
  $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Season.SeasonKey, SportingBody.SBKey, Championship.Status " .
      " FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
	  " WHERE Championship.ChampionshipKey = $ChKey";
  $ChampRec = $mysqli->query( $query );
  if( !$ChampRec ) {
  	return( htmlspecialchars( "Error(ch2): " . $mysqli->error ) );
  }
  // ChampRec;
  $query = "SELECT ChampData.LadderDisplay, ChampData.LadderSort " .
    " FROM ChampData, Championship  " .
	" WHERE Championship.ChampionshipKey = $ChKey AND Championship.DataKey = ChampData.DataKey";
  $DataRec = $mysqli->query( $query );
  if( !$DataRec ) {
	return( htmlspecialchars( "Error(ch3): " . $mysqli->error ) );
  }
  $DataRecR = $DataRec->fetch_array( );

  $TblData = "";
  if( !( $ChampRecR = $ChampRec->fetch_array( ) ) ) {
  } else {
	$teamtbl = 'Team';
	if( $ChampRecR['Status'] == 'H' ) {
	  $teamtbl = 'TeamHist';
	}
	$query = " SELECT DISTINCTROW * " .
		" FROM " . $teamtbl .
		" WHERE ChampionshipKey = " . $ChKey .
		" ORDER BY " . LaddOrder( $DataRecR["LadderSort"] );
	$TeamRec = $mysqli->query( $query );
	if( !$TeamRec ) {
	  return( htmlspecialchars( "Error(ch4): " . $mysqli_error( ) ) );
	}
	$tz = $ChampRecR["SBTZ"];
	if(!is_null($tz)) {
		$tz = preg_replace( '/^\s+/', '', $tz );
		if(strlen($tz) > 0) {
		//            $ENV{TZ}=":$tz";
		}
	}
    $Iter = 1;
	$TblData = LaddHead( $DataRecR["LadderDisplay"] );
	while( $TeamRecR = $TeamRec->fetch_array( ) ) {
	  $TblData .= LaddRow($Iter, $DataRecR["LadderDisplay"], $TeamRecR );
	  $Iter++;
	}
	$StData .= "    <table border='1' style='border-collapse: collapse; border-style: solid; border-width: thin;'>\n" . $TblData . "</table><P>";
	//        "<a href='" . FixtURL( ) . $ChKey . "'>Click</a> for the championship fixture.<P>\n    ";
  }

  $Heading = $ChampRecR["GradeName"] . " - Ladder";

  $retval  = '<div class="item-page">' . "\n";
  $retval .=  '<h2>' .  htmlspecialchars( $ChampRecR["SBAbbrev"] . " " . $ChampRecR["GradeName"] ) . '</h2>' . "\n";
  $retval .=  "<h3>" . $ChampRecR["SportName"] . " - " . $ChampRecR["SeasonName"] . "</h3>\n";
  $retval .= '<table class="champladder">' . $TblData . '</table>';
  $retval .= '</div>';
  $submenu = menuli( 107, 0, 'fixture?champ=' . $ChKey, 'Fixture' );
  $submenu .= addEnterResSubMenu($ChKey);
  $retmenu = menuhead( ) .
  				menuli( 101, 0, 'welcome', 'Welcome', null ) .
  				menuli( 103, 0, 'about-scoretank', 'About ScoreTank', null ) .
  				menuli( 105, 0, 'championship-list', 'Championship List', null ) .
  				menuli( 106, 1, 'champ?champ=' . $ChKey, htmlspecialchars( $ChampRecR["SBAbbrev"] . " " . $ChampRecR["GradeName"] ), $submenu ) .
  			 menufoot( "champ" );
//  return( array( $retval, "", $retmenu ) );
  return array( $retval, pagehead( "Ladder - " . htmlspecialchars( $ChampRecR["SBAbbrev"] . " " . $ChampRecR["GradeName"] ) ), $retmenu );
}

function team( ) {
  $retval = "";
  $TeamNum = 0;
  $mode = 0;
  if( $TeamNum = JRequest::getVar( 'team' ) ) {
    if( !( preg_match('/^\d+$/', $TeamNum ) ) || ( !$TeamNum ) ) {
	  return( htmlspecialchars( "Error(t1a)" ) );
	}
	$mode = 1; // summary
  } else if( $TeamNum = JRequest::getVar( 'teamfixt' ) ) {
    if( !( preg_match('/^\d+$/', $TeamNum ) ) || ( !$TeamNum ) ) {
	  return( htmlspecialchars( "Error(t1b)" ) );
	}
	$mode = 2;
  } else if( $TeamNum = JRequest::getVar( 'teamven' ) ) {
    if( !( preg_match('/^\d+$/', $TeamNum ) ) || ( !$TeamNum ) ) {
	  return( htmlspecialchars( "Error(t1c)" ) );
	}
	$mode = 3;
  } else {
	return( htmlspecialchars( "Error(t1)" ) );
  }
  $mysqli = stconnect( );

  //$TeamRec
  $query = "SELECT * FROM Team WHERE TeamKey = $TeamNum";
  $TeamRecq = $mysqli->query( $query );
  if( !$TeamRecq ) {
	return( htmlspecialchars( "Error(t2): " . $mysqli->error ) );
  }
  if( $TeamRec = $TeamRecq->fetch_array( ) ) {
	$matchtbl = 'NMatch';
	$teamtbl = 'Team';
  } else {
	$matchtbl = 'MatchHist';
	$teamtbl = 'TeamHist';
	$query = ("SELECT * FROM TeamHist WHERE TeamKey = $TeamNum");
	$TeamRecq = $mysqli->query( $TeamRecq );
	if( !TeamRecq ) {
	  return( htmlspecialchars( "Error(t3): " . $mysqli->error ) );
	}
	$TeamRec = $TeamRecq->fetch_array( );
  }
  $ChKey = $TeamRec["ChampionshipKey"];

  //$DataRec
  $query = ("SELECT * FROM Championship, ChampData " .
	" WHERE Championship.DataKey = ChampData.DataKey AND Championship.ChampionshipKey = " . $TeamRec["ChampionshipKey"] );
  $DataRecq = $mysqli->query( $query );
  if( !$DataRecq ) {
    return( htmlspecialchars( "Error(t4): " . $mysqli->error ) );
  }
  $DataRec = $DataRecq->fetch_array( );

  //$MatchRec
  $query = "SELECT RoundNumber, HTeam.TeamName As HomeTeamName, HomeTeamKey, ATeam.TeamName As AwayTeamName, AwayTeamKey, Venue, HomeGroundName, HomeGroundAddress, Scheduled, Result, HomeTeamRawScore, HomeTeamScore, HomeTeamSupScore, AwayTeamRawScore, AwayTeamScore, AwayTeamSupScore " .
			" FROM $matchtbl, $teamtbl HTeam, $teamtbl ATeam, HomeGround " .
			" WHERE HTeam.TeamKey = HomeTeamKey AND ATeam.TeamKey = AwayTeamKey AND (HTeam.TeamKey = $TeamNum OR ATeam.TeamKey = $TeamNum) " .
			" AND Venue = HomeGround.HomeGroundKey " .
			" AND $matchtbl.ChampionshipKey > 0 " . // because some matches were commented out by setting ChKey to -ve
			" ORDER BY RoundNumber, MatchNumber";
  $MatchRecq = $mysqli->query( $query );
  if( !$MatchRecq ) {
	return( htmlspecialchars( "Error(t5): " . $mysqli->error ) );
  }

  //$FMatchRec
  $query = "SELECT FMatch.RoundNumber, " .
	" HTeam.TeamName as HomeTeamName, HomeTeamKey, " .
	" ATeam.TeamName As AwayTeamName, AwayTeamKey, " .
	" Venue, HomeGroundName, HomeGroundAddress, Scheduled, FMatch.Result, " .
	" HomeTeamRawScore, HomeTeamScore, HomeTeamSupScore, " .
	" AwayTeamRawScore, AwayTeamScore, AwayTeamSupScore, " .
	" SeriesName, RoundName " .
	" FROM FMatch, Team HTeam, Team ATeam, HomeGround, FSeries, FRound " .
	" WHERE HTeam.TeamKey = HomeTeamKey AND ATeam.TeamKey = AwayTeamKey " .
	" AND (HTeam.TeamKey = $TeamNum OR ATeam.TeamKey = $TeamNum) " .
	" AND Venue = HomeGround.HomeGroundKey " .
	" AND FSeries.ChampionshipKey = FMatch.ChampionshipKey " .
	" AND FSeries.RoundNumber = FMatch.RoundNumber " .
	" AND FSeries.SeriesNumber = FMatch.SeriesNumber " .
	" AND FRound.ChampionshipKey = FSeries.ChampionshipKey AND FRound.RoundNumber = FSeries.RoundNumber " .
	" ORDER BY RoundNumber, MatchNumber";
  if( !( $FMatchRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(t6): " . $mysqli->error ) );
  }

  $retval  = '<div class="item-page">' . "\n";
  $titledesc = "";
  if( $mode == 1 ) {
    $retval .=  '<h2>' .  htmlspecialchars( "Summary of results for: " . $TeamRec["TeamName"] ) . '</h2>' . "\n";
	$titledesc = "Summary";
  } else if( $mode == 2 ) {
    $retval .=  '<h2>' .  htmlspecialchars( "Fixture for: " . $TeamRec["TeamName"] ) . '</h2>' . "\n";
	$titledesc = "Fixture";
  } else if( $mode == 3 ) {
    $retval .=  '<h2>' .  htmlspecialchars( "Venues for: " . $TeamRec["TeamName"] ) . '</h2>' . "\n";
	$titledesc = "Venues";
  }

$dbg = "";
  if( !( $MatchRec = $MatchRecq->fetch_array( ) ) ) {
    $retval .= "No matches found";
  } else {
	$StData .= "Current <a " . href( 'champ?champ=' . $TeamRec["ChampionshipKey"] ) . ">ladder</a> position: ".$TeamRec["LadderPos"]. "\n";
	if( $TeamRec["EqualPos"] != $TeamRec["LadderPos"] ) {
	  $StData .= "(Equal " . $TeamRec["EqualPos"] . ")";
	}
	if( $mode == 1 ) {
	  $StData .= "<p/><a" . href( "team?teamfixt=" . $TeamNum ) . ">Click</a> for team fixture.<p/>\n";
	} else {
	  $StData .= "<p/><a" .  href( "team?team=" . $TeamNum ) . ">Click</a> for team summary.<p/>\n";
	}
	$MatchStr = "";
	$timenow = time( );
	$Streak = 0;
	$LastRound = 0;
	$LastSer = 0;
	$Recent = "";
	$Upcoming = "";
	$RoundStr = "";
	$FixtStr = "";

	$FRec = $MatchRec;  //FixtureRec
	$Finals = 0;
	do {
	  $FHead = "";
	  if( $FRec["RoundNumber"] != $LastRound ) {
		$SumStr = "";
		if( $RoundStr ) {
		  $FixtStr .= $RoundStr. "<tr><td><br/></td></tr>";
		}
		$RoundStr = "";
	  }
$dbg .= "i";
	  if( $Finals == 1 ) {
		$FStr = "";
		if( $LastRound != $FRec["RoundNumber"] ) {
		  if( $FRec["RoundName"] ) {
		    $FStr = MakeMatchHead_s( $FRec["RoundName"] );
		  }
		}

		if( ( $LastRound != $FRec["RoundNumber"] ) ||
			( $LastSer != $FRec["SeriesNumber"] ) ) {
		  if( $SumStr ) {
		    $SumStr .= "     <tr><td><br/></td></tr>\n";
		  }
		  $LastRound = $FRec["RoundNumber"];
		  $LastSer = $FRec["SeriesNumber"];
		  if($FRec["SeriesName"]) {
			$FHead = MakeFNameHead($FRec["SeriesName"]);
			$SumStr .= $FStr.$FHead.MakeMatch($FRec, $DataRec["ScoreFormat"], $TeamRec["TeamName"]);
		  }
		}
	  } else {
	    if( ! $SumStr ) {
		  $SumStr = MakeMatchHead_s("Round ".$FRec["RoundNumber"] ).MakeMatch($FRec, $DataRec["ScoreFormat"], $TeamRec["TeamName"]);
		} else { //add to existing round
		  $SumStr = $SumStr.MakeMatch($FRec, $DataRec["ScoreFormat"], $TeamRec["TeamName"]);
		}
	  }
	  if( !$RoundStr ) {
		$RoundStr = MakeMatchHead_s( "Round " . $FRec["RoundNumber"] );
	  }
//$dbg .= $RoundStr;
	  if( ( strtotime( $FRec["Scheduled"] ) > $timenow ) &&
	  	  ( $FRec["RoundNumber"] > $LastRound ) ) {
		$SumStr = "";
		if( !$UpcomingRound ) {
		  $UpcomingRound = $FRec["RoundNumber"];
		}
	  }
	  
	  if( !$UpcomingRound ) {
		$Recent = $SumStr;
	  }
	  if( $UpcomingRound == $FRec["RoundNumber"] ) {
		if( ! $Upcoming ) {
		  $Upcoming = MakeMatchHead_s("Round ".$UpcomingRound ).MakeMatch($FRec, $DataRec["ScoreFormat"], $TeamRec["TeamName"]);
		} else {
		  $Upcoming .= MakeMatch($FRec, $DataRec["ScoreFormat"], $TeamRec["TeamName"]);
		}
	  }
	  $LastRound = $FRec["RoundNumber"];
	  
	  if( ( $mode == 3 ) && ($FRec["AwayTeamKey"] != -1 ) ) {
		$ven = preg_replace( '/\n/', '<br/>', $FRec["HomeGroundAddress"] );
		$ts = strtotime( $FRec["Scheduled"] );

		$RoundStr .= "<tr class='match'><td>" . $FRec["HomeTeamName"] . "</td><td>vs</td><td>" . $FRec["AwayTeamName"] . "</td></tr>" .
				"<tr><td colspan='6'><b>" . date( "D, j M Y", $ts ) . "</b> " . date( "g:ia", $ts ) . " at ".$FRec["HomeGroundName"] . "</td></tr>" .
				"<tr><td colspan='6'>" . $ven . "</td></tr>";
	  } else {
	    $RoundStr = $RoundStr.$FHead.MakeMatch($FRec, $DataRec["ScoreFormat"], $TeamRec["TeamName"]);
	  }
	  //Calculate the streak
	  if(strtoupper($FRec["Result"]) == 'H') {
		if($FRec["HomeTeamName"] == $TeamRec["TeamName"]) {
		  $Streak = ( $Streak > 0 ) ? ++$Streak : 1;
		} else {
		  $Streak = ( $Streak < 0 ) ? --$Streak : -1;
		}
	  } else if(strtoupper($FRec["Result"]) == 'A') {
		if($FRec["AwayTeamName"] == $TeamRec["TeamName"]) {
		  $Streak = ( $Streak > 0 ) ? ++$Streak : 1;
		} else {
		  $Streak = ( $Streak < 0 ) ? --$Streak : -1;
	    }
	  } else if($FRec["Result"] && ($FRec["Result"] != 'B') && ($FRec["Result"] != 'W')) {
		$Streak = 0;
	  }
	  //$Fetch = $FRec->FetchRow( );
	  $FRec = $MatchRecq->fetch_array( );
	  if( $Finals == 1 ) {
		$LastSer = $FRec["SeriesNumber"];
	  }
	  if( !$FRec && ( $Finals == 0 ) ) {
		$Finals = 1;
		// $FRec = $FMatchRec;
		// $Fetch = $FRec->FetchRow( );
		$LastRound = 0;
		
		$MatchRecq = $FMatchRecq;
		if( $FRec = $MatchRecq->fetch_array( ) ) {
		  $RoundStr .= "<tr><td><br/></td></tr>" . MakeMatchHead_s("FINALS");	//	 . "<TR><TD><BR/></TD></TR>";
		}
	  }
    } while( $FRec );
	$FixtStr .= $RoundStr . "<tr><td><br/></td></tr>";

	$Matches = "";
	if( $Recent ) {
	  $Matches = "<tr><td colspan='6'><h3>Last Round:</h3></td></tr>\n".$Recent."<tr><td><br/></td></tr>";
	}
	if( $Upcoming ) {
	  $Matches = $Matches. "<tr><td colspan='6'><h3>Next Round:</h3></td></tr>\n".$Upcoming. "<tr><td><br/></td></tr>";
	}
	if( $mode == 1 ) {
	  $StData .= "     <table border='0' class='teamfixttbl'>" . $Matches . "</table>\n";
	  if( $Streak > 1 ) {
	    $StData .= "     <h3>Current Streak: $Streak wins</h3>";
	  } else if( $Streak == 1 ) {
	    $StData .= "     <h3>Current Streak: 1 win</h3>";
	  } else if( $Streak == -1 ) {
	    $StData .= "     <h3>Current Streak: 1 loss</h3>";
	  } else if( $Streak < -1 ) {
	    $StData .= "     <h3>Current Streak: ".abs($Streak)." losses</h3>";
	  } else {
	    $StData .= "     <h3>Current Streak: No wins or losses</h3>";
	  }
	} else {
	  $StData .= "     <table border='0' class='teamfixttbl'>\n      ".$FixtStr."     </table>\n";
	}
	$StData .= "\n    ";
  }
  $retval .= $StData;
//  $retval .= "<span>" . htmlspecialchars( $dbg ) . "</span>";
  $retval .= '</div>';
  $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Championship.Status " .
		" FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
		" WHERE (((Championship.ChampionshipKey)=$ChKey))";
  if( !( $ChampRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(f2): " . $mysqli->error ) );
  }
  if( !( $ChampRec = $ChampRecq->fetch_array( ) ) ) {
	return( htmlspecialchars( "Error(f3): Unknown Championship" ) );
  }
  $submenu = menuli( 109, ( $mode == 2 ), 'team?teamfixt=' . $TeamNum, 'Team Fixture', null ) .
			 menuli( 110, 0, 'team/teamhist?team=' . $TeamNum, 'Team History', null ) .
			 menuli( 111, ( $mode == 3 ), 'team?teamven=' . $TeamNum, 'Team Venues', null );
  $retmenu = menuhead( ) .
  				menuli( 101, 0, 'welcome', 'Welcome', null ) .
  				menuli( 103, 0, 'about-scoretank', 'About ScoreTank', null ) .
  				menuli( 105, 0, 'championship-list', 'Championship List', null ) .
//  				menuli( 106, 0, 'champ?champ=' . $ChKey, 'Championship' ) .
  				menuli( 106, 0, 'champ?champ=' . $ChKey, htmlspecialchars( $ChampRec["SBAbbrev"]." ".$ChampRec["GradeName"] ), null ) .
//  				menuli( 107, 0, 'fixture?champ=' . $ChKey, 'Fixture' ) .
  				menuli( 108, 0, 'team?team=' . $TeamNum, $TeamRec['TeamName'], $submenu ) .
  			 menufoot( "team" );
  //$retmenu = "";
  return array( $retval, pagehead( $titledesc . " - " . $TeamRec['TeamName'] ), $retmenu );
}

function teamhist( ) {
  if( !( $TeamNum = JRequest::getVar( 'team' ) ) ) {
	return( htmlspecialchars( "Error(th1)" ) );
  }
  if( !( preg_match('/^\d+$/', $TeamNum ) ) || ( !$TeamNum ) ) {
	return( htmlspecialchars( "Error(th1a)" ) );
  }
  $mysqli = stconnect( );
  $retval  = '<div class="item-page">' . "\n";
  //$TeamRec
  $query = "SELECT * FROM Team WHERE TeamKey = $TeamNum";
  if( !( $TeamRecq = $mysqli->query( $query ) )  ) {
	return( htmlspecialchars( "Error(th2): " . $mysqli->error ) );
  }
  if( $TeamRec = $TeamRecq->fetch_array( ) ) {
	$teamtbl = 'Team';
	$matchtbl = 'NMatch';
	$tlptbl = 'TeamLadderPos';
  } else {
	$query = "SELECT * FROM TeamHist WHERE TeamKey = $TeamNum";
	if( !( $TeamRecq = $mysqli->query( $query ) ) ) {
	  return( htmlspecialchars( "Error(th3): " . $mysqli->error ) );
	}
	$TeamRec = $TeamRecq->fetch_array( );
	$teamtbl = 'TeamHist';
	$matchtbl = 'MatchHist';
	$tlptbl = 'TeamLadderPosHist';
  }
  $ChKey = $TeamRec["ChampionshipKey"];

  //$MatchRec
  $query = "SELECT RoundNumber, HTeam.TeamName As HomeTeamName, HomeTeamKey, ATeam.TeamName As AwayTeamName, AwayTeamKey, Venue, HomeGroundName, HomeGroundAddress, Scheduled, Result, HomeTeamRawScore, HomeTeamScore, HomeTeamSupScore, AwayTeamRawScore, AwayTeamScore, AwayTeamSupScore " .
	  " FROM $matchtbl, $teamtbl HTeam, $teamtbl ATeam, HomeGround " .
	  " WHERE HTeam.TeamKey = $matchtbl.HomeTeamKey AND ATeam.TeamKey = $matchtbl.AwayTeamKey AND (HTeam.TeamKey = $TeamNum OR ATeam.TeamKey = $TeamNum)  AND $matchtbl.Venue = HomeGround.HomeGroundKey " .
	  " ORDER BY RoundNumber, MatchNumber";
  if( !( $MatchRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(th3): " . $mysqli->error ) );
  }

  //$DataRec
  $query = "SELECT * FROM Championship, ChampData " .
				" WHERE Championship.DataKey = ChampData.DataKey AND Championship.ChampionshipKey = " . $TeamRec["ChampionshipKey"];
  if( !( $DataRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(th4): " . $mysqli->error ) );
  }
  $DataRec = $DataRecq->fetch_array( );

  $StData = "<h2>History of ladder position for: " . $TeamRec["TeamName"] . "</h2>\n";
  $Heading = $TeamRec["TeamName"]." - History";

  $ChartScript = '';
  if( !( $MatchRec = $MatchRecq->fetch_array( ) ) ) {
	$StData .= "    <H2>No matches found</H2>";
  } else {
	$StData .= "     Current <a " . href( '../champ?champ=' . $TeamRec["ChampionshipKey"] ) . ">ladder</a> position: " . $TeamRec["LadderPos"] . "\n     ";
	if( $TeamRec["EqualPos"] != $TeamRec["LadderPos"] ) {
	  $StData .= "      (Equal " . $TeamRec["EqualPos"] . ")";
	}
	$StData .= "<p/>\n";

	//$PosRec
	$query = "SELECT Count(TeamKey) AS NumTeams FROM $teamtbl WHERE $teamtbl.ChampionshipKey = ".$TeamRec["ChampionshipKey"];
	if( !( $PosRecq = $mysqli->query( $query ) ) ) {
	  return( htmlspecialchars( "Error(th5): " . $mysqli->error ) );
	}
	$PosRec = $PosRecq->fetch_array( );
	$laddpos = array( );
	for($NumTeams = 1; $NumTeams <= $PosRec["NumTeams"]; $NumTeams++) {
	  $laddpos[] = $NumTeams;
	}
	$laddposses = join( ",", array_reverse($laddpos));
	$NumTeams = $PosRec["NumTeams"];

	$values = '';
	//$PosRec
	$query = "SELECT * FROM $tlptbl WHERE TeamKey = $TeamNum " .
			" ORDER BY RoundNumber";
	if( !( $PosRecq = $mysqli->query( $query ) ) ) {
	  return( htmlspecialchars( "Error(th6): " . $mysqli->error ) );
	}
	$numround = 1;
	$xvals = array( );
	$vals = array( );
	while( $PosRec = $PosRecq->fetch_array( ) ) {
	  $xvals[] = $numround++;
	  $vals[] = $NumTeams - $PosRec["LadderPos"] + 1;
	}
	$query = "SELECT Max(RoundNumber) AS NumRounds " .
	  " FROM $matchtbl " .
	  " WHERE $matchtbl.HomeTeamKey >= 0 AND $matchtbl.ChampionshipKey =  " . $TeamRec["ChampionshipKey"];
	if( !( $PosRecq = $mysqli->query( $query ) ) ) {
	  return( htmlspecialchars( "Error(th7): " . $mysqli->error ) );
	}
	//# why HomeTeamKey >= 0???? NFIdea - without it, SQL fails for some reason.
	$PosRec = $PosRecq->fetch_array( );
	while( $numround <= $PosRec["NumRounds"] ) {
	  $xvals[] = $numround++;
	  $vals[] = 0;
	}
	// insert any missing values...?
	$values = join( ",", $vals );
	$arnd = array( );
	//for($numrounds = 1; $numrounds <= $PosRec["NumRounds"]; $numrounds++ ) {
	//   $arnd[] = $numrounds;
	//}
	//$rounds = join( '|', $arnd );
	for( $numrounds = 1; $numrounds <= $PosRec["NumRounds"]; $numrounds++ ) {
	  $arnd[] = $numrounds;
	}
	$rounds = join( '|', $arnd );
	$numrounds = $PosRec["NumRounds"];
	$NTarr = array( );
	$NTpos = array( );
	for( $i = $NumTeams; $i >= 1; $i-- ) {
	  $NTarr[] = $i;
	  $NTpos[] = sprintf( "%.2f", ( 1 + $NumTeams - $i ) * 100 / ( $NumTeams ) );
	}
	$StData .= "<img src='http://chart.apis.google.com/chart?" .
				"cht=bvs&" .
				"chs=400x300&" .
				"chxt=x,y&" .
				"chxl=0:|" . $rounds . "|1:|" . join( "|", $NTarr ) . "&" .
				"chxp=1," . join( ",", $NTpos ) . "&" .
				"chd=t:" . $values . "&" .
				"chco=4d89f9&" .
				"chbh=a&" .
				"chg=0," . sprintf( "%.2f", ( 100 / $NumTeams ) ) . "&" .
				"chtt=Ladder+position+by+round&" .
				"chds=0," . $NumTeams . "'/>";

	$ChartScript .= '<script type="text/javascript" src="https://www.google.com/jsapi"></script>' . "\n";
	$ChartScript .= '<script type="text/javascript">' . "\n";
	$ChartScript .= " google.load('visualization', '1.0', {'packages':['corechart']});\n";
	$ChartScript .= " google.setOnLoadCallback(drawChart);\n";
	$ChartScript .= " function drawChart() {\n";
	$ChartScript .= "  var data = new google.visualization.DataTable();\n";
	$ChartScript .= "  data.addColumn('string', 'Round');\n";
	$ChartScript .= "  data.addColumn('number', 'Position');\n";
	$ChartScript .= "  data.addRows([ ['1', 3], ['2', 1], ['3', 1], ['4', 1], ['5', 2] ]);\n";
	$ChartScript .= "  var options = { " .
		" 'title':'TEST - Ladder position by round'," .
		" 'width':400, " .
		" 'height':300, " .
		" 'legend':{ 'position': 'none'}, " .
		" 'vAxis':{" .
			" 'direction': -1," .
			" 'baseline': 9," .
			" 'minValue': 1," .
			" 'gridlines':{ 'count': 10 }" .
		" }" .
		"};\n";
	$ChartScript .= "  var chart = new google.visualization.ColumnChart(document.getElementById('ladderpos_div'));\n";
	$ChartScript .= "  chart.draw(data, options);\n";
	$ChartScript .= " }\n";
	$ChartScript .= "</script>\n";
	///$StData .= $ChartScript;
//	$StData .= '<div id="ladderpos_div" style="width: 400px; height: 300px;"></div>' . "\n";
  }
  $retval .= $StData;
  $retval .= '</div>';
  $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Championship.Status " .
		" FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
		" WHERE (((Championship.ChampionshipKey)=$ChKey))";
  if( !( $ChampRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(f2): " . $mysqli->error ) );
  }
  if( !( $ChampRec = $ChampRecq->fetch_array( ) ) ) {
	return( htmlspecialchars( "Error(f3): Unknown Championship" ) );
  }
  $submenu = menuli( 109, 0, '../team?teamfixt=' . $TeamNum, 'Team Fixture' ) .
			 menuli( 110, 1, 'teamhist?team=' . $TeamNum, 'Team History' ) .
			 menuli( 111, 0, '../team?teamven=' . $TeamNum, 'Team Venues' );
  $retmenu = menuhead( ) .
  				menuli( 101, 0, '../welcome', 'Welcome' ) .
  				menuli( 103, 0, '../about-scoretank', 'About ScoreTank' ) .
  				menuli( 105, 0, '../championship-list', 'Championship List' ) .
  				menuli( 106, 0, '../champ?champ=' . $ChKey, htmlspecialchars( $ChampRec["SBAbbrev"]." ".$ChampRec["GradeName"] ) ) .
// 				menuli( 107, 0, 'fixture?champ=' . $ChKey, 'Fixture' ) .
  				menuli( 108, 0, '../team?team=' . $TeamNum, $TeamRec['TeamName'], $submenu ) .
			 menufoot( "teamhist" );
  return array( $retval, pagehead( "History - " . $TeamRec['TeamName'] ), $retmenu );
  // eventually want to include the ChartScript but for now I can't
  // get it to hide the top and bottom vertical axis labels
  // or the top gridline, or make the lines dashed...
//  return array( $retval, pagehead( "History - " . $TeamRec['TeamName'] ) . $ChartScript, $retmenu );
}

// $ent: 0 = show the fixture with read-only results
//       1 = allow the logged-in user to enter results
//       2 = allow the facebook user to enter results (deprecated)
function fixt( $ent = 0 ) {
  if(!( $_SERVER["REQUEST_SCHEME"] == "https" )) {
    $ent = 0;
  }
  $user = JFactory::getUser();
  if($user->id <= 0) {
	  $ent = 0;
  }
  if( !( $ChKey = JRequest::getVar( 'champ' ) ) ) {
	return (array( "Championship error(f1)",
	 			  pagehead( "Error" ),
	 			  menuhead( ) . menuli( 101, 0, 'welcome', 'Welcome', null ) . menufoot( "champ" ) ) );
  }
  if( !( preg_match('/^\d+$/', $ChKey ) ) || ( !$ChKey ) ) {
	return( htmlspecialchars( "Error(f1a)" ) );
  }
  $mysqli = stconnect( );
  $retval = "";

  $scriptprefix = "";
  if( strstr( $_SERVER['SERVER_NAME'], 'thebrasstraps.com' ) ) {
    $scriptprefix = "/scoretank";
  }

  if( $ent > 0 ) {
    $retval .= '<link type="text/css" href="' . $scriptprefix . '/jqlib/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" rel="stylesheet"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery-1.5.2.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/sarissa.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/sarissa_ieemu_xpath.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery.xslTransform.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery-ui-1.8.11.custom/development-bundle/ui/jquery-ui-1.8.11.custom.js"></script>' . "\n";
    $retval .= "<script type='text/javascript' src='" . $scriptprefix . "/templates/favouritest/stlib.js'></script>\n";

	if( $ent == 2 ) {
		//$retval .= "<div id='fb-root'></div>\n";
		//$retval .= "<div id='fbdialog' title='ScoreTank'>Message from ScoreTank</div>\n";
		//$retval .= '<script type="text/javascript" src="https://connect.facebook.net/en_US/all.js"></script>';
		$retval .=	'<script type="text/javascript">' . "\n";
		//$retval .= '$(document).ready( function( ) { $( "#fbdialog" ).dialog( { modal: true, autoOpen: false } ) } );';
		$retval .= '$(document).ready( function( ) { } );';
		// $retval .= "window.fbAsyncInit = function( ) {\n" .
		// 	//               " window.alert( 'fbAI' ); " .
		// 			" FB.init({  \n" .
		// 							"  appId  : '" . fbcred_get_app_id( ) . "', \n" .
		// 									"  status : true, \n" . // check login status
		// 									"  cookie : true, \n" . // enable cookies to allow the server to access the session
		// 									"  xfbml  : true \n" .  // parse XFBML
		// 							" }); " .
		// 					" FB.login( function( response ) {\n" .
		// 					"   if( response.authResponse ) {\n" .
		// 					//"     window.alert( 'Successfully logged in' );" .
		// 					"     onLoggedIn( $ChKey );" .
		// 					"   } else {\n" .
		// 					"     window.alert( 'You must be logged in' );" .
		// 					"   }\n" .
		// 					" })\n" .
		// 					"}\n";
		//$retval .= 'function loadPage() { };';
		//$retval .= 'function onLoggedIn() { jQuery( ".scoreentry" ).removeAttr("disabled"); }';
		$retval .= 'function SendMatchRes( inputfld ) { SendMatchResLib( inputfld ); }';
		$retval .= '</script>' . "\n";
	} else if( $ent == 1 ) {
		$user = JFactory::getUser();
		$jwt = 0;	//	JWT::encode(array( "userid" => $user->id, "champ" => $ChKey, "exp" => (time() + (15 * 60)) ), jwt_secret( ));

		$retval .= '<script type="text/javascript">' . "\n";
		$retval .= '$(document).ready( function( ) { jQuery( ".scoreentry" ).removeAttr("disabled"); } );';
		$retval .= 'function SendMatchRes( inputfld ) { SendMatchResLib( inputfld ); }';
		$retval .= '</script>' . "\n";
			
//		$retval .= '<input type="button" name="test" value="test1" onclick="console.log(' . "'testbutton'".
//				 '); sendTest( \'' . $jwt . '\' );"></input>';
  	}
  }

  $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Championship.Status " .
		" FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
		" WHERE (((Championship.ChampionshipKey)=$ChKey))";
  if( !( $ChampRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(f2): " . $mysqli->error ) );
  }
  if( !( $ChampRec = $ChampRecq->fetch_array( ) ) ) {
	return( htmlspecialchars( "Error(f3): Unknown Championship" ) );
  }
  if($ChampRec["Status"] == 'H') {
	$matchtbl = 'MatchHist';
	$teamtbl = 'TeamHist';
  } else {
	$matchtbl = 'NMatch';
	$teamtbl = 'Team';
  }

  // MatchRec
  $query = "SELECT DISTINCTROW $matchtbl.ChampionshipKey, $matchtbl.RoundNumber, $matchtbl.MatchNumber, $teamtbl.TeamName AS HomeTeamName, Team_1.TeamKey AS AwayTeamKey, Team_1.TeamName AS AwayTeamName, HomeGround.HomeGroundName, $matchtbl.Scheduled, $matchtbl.HomeTeamRawScore, $matchtbl.HomeTeamScore, $matchtbl.HomeTeamSupScore, $matchtbl.AwayTeamRawScore, $matchtbl.AwayTeamScore, $matchtbl.AwayTeamSupScore, $matchtbl.Result, $matchtbl.MatchRef " .
		" FROM ($teamtbl INNER JOIN ($matchtbl INNER JOIN $teamtbl AS Team_1 ON $matchtbl.AwayTeamKey = Team_1.TeamKey) ON $teamtbl.TeamKey = $matchtbl.HomeTeamKey) INNER JOIN HomeGround ON $matchtbl.Venue = HomeGround.HomeGroundKey " .
		" WHERE ((($matchtbl.ChampionshipKey)=$ChKey)) " .
		" ORDER BY $matchtbl.ChampionshipKey, $matchtbl.RoundNumber, $matchtbl.MatchNumber";
  if( !( $MatchRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(f4): " . $mysqli->error ) );
  }

  //$DataRec
  $query = "SELECT DISTINCTROW ChampData.WinPoints, ChampData.LossPoints, ChampData.TiePoints, ChampData.DrawPoints, ChampData.ByePoints, ChampData.ForfeitPoints, ChampData.WalkOverWinScore, ChampData.WalkOverLossScore, ChampData.WalkOverWinPoints, ChampData.WalkOverLossPoints, ChampData.ScoreFormat, ChampData.RoundInterval, ChampData.LadderDisplay, ChampData.LadderSort " .
		" FROM ChampData INNER JOIN Championship ON ChampData.DataKey = Championship.DataKey " .
		" WHERE (((Championship.ChampionshipKey)=$ChKey))";
  if( !( $DataRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(f5): " . $mysqli->error ) );
  }
  $DataRec = $DataRecq->fetch_array( );

  //    $tz = $ChampRec["SBTZ"];
  //    $tz =~ s/^\s+//;
  //    if(length($tz) > 0) {
  //        $ENV{TZ}=":$tz";
  //    }

  //$StData = "<h2><a " . href( 'fixture?champ=' . $ChKey ) . ">" . htmlspecialchars( $ChampRec["SBAbbrev"]." ".$ChampRec["GradeName"] ) . "</a></h2>\n";
  //$StData .= $ChampRec["SportName"]. " - ".$ChampRec["SeasonName"] . "<p/>";
  $LastRound = 0;
  $LastDate = "";
  $FixtStr = "";
  while( $MatchRec = $MatchRecq->fetch_array( ) ) {
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
	$FixtStr = $FixtStr .
		( ( $ent > 0 ) ? MakeMatchEnt( $MatchRec, $DataRec["ScoreFormat"], $LastDate) :
                                 MakeMatch( $MatchRec, $DataRec["ScoreFormat"], "", $LastDate ) );
	$LastDate = $MatchRec["Scheduled"];
  }

  //$FRec
  $query = "SELECT DISTINCTROW FSeries.ChampionshipKey, FSeries.RoundNumber, FSeries.SeriesNumber, FSeries.SeriesName, FSeries.HomeTeamKey, FSeries.HomeDeriv, FSeries.HomeDerivRank, FSeries.HomeDerivChamp, FSeries.AwayTeamKey, FSeries.AwayDeriv, FSeries.AwayDerivRank, FSeries.AwayDerivChamp, FSeries.Result, FMatch.MatchNumber, FMatch.HomeTeamRawScore, FMatch.HomeTeamScore, FMatch.HomeTeamSupScore, FMatch.AwayTeamRawScore, FMatch.AwayTeamScore, FMatch.AwayTeamSupScore, FMatch.Venue, FMatch.Scheduled, FMatch.Result, FMatch.ReverseHA, " .
	  " FMatch.MatchRef " .
	  " FROM FSeries , FMatch " .
	  " WHERE (((FSeries.ChampionshipKey)=$ChKey) OR (((FSeries.HomeDerivChamp)=$ChKey)) OR ((FSeries.AwayDerivChamp)=$ChKey)) " .
	  " AND ((FSeries.SeriesNumber = FMatch.SeriesNumber) AND (FSeries.RoundNumber = FMatch.RoundNumber) AND (FSeries.ChampionshipKey = FMatch.ChampionshipKey)) " .
	  " ORDER BY FSeries.RoundNumber, FSeries.RSeriesNumber, FSeries.SeriesNumber, FMatch.MatchNumber";
  if( !( $FRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(f6): " . $mysqli->error ) );
  }
  $LastRound = 0;
  $LastSer = 0;
  while( $FRec = $FRecq->fetch_array( ) ) {
    if( !$FStr ) {
	  $FStr .= "<tr><td><br/></td></tr>" . MakeMatchHead("FINALS") . "<tr><td><br/></td></tr>";
	}
	//if new series...? $FStr
	if( $LastRound != $FRec["RoundNumber"] ) {
	  // $FRRec
	  $query = ("SELECT DISTINCTROW FRound.RoundName " .
									" FROM FRound " .
									" WHERE ChampionshipKey = " . $FRec["ChampionshipKey"] .
									" AND RoundNumber = ".$FRec["RoundNumber"]);
	  if( !( $FRRecq = $mysqli->query( $query ) ) ) {
		return( htmlspecialchars( "Error(f7): " . $mysqli->error ) );
	  }
	  if( $FRRec = $FRRecq->fetch_array( ) ) {
		if($FRRec["RoundName"]) {
		  if( $FixtStr ) {
//			$FixtStr .= "     <TR><TD><BR/></TD></TR>\n";
		  }
		  $FStr .= MakeMatchHead($FRRec["RoundName"]);
		}
	  }
	}
	if( ( $LastRound != $FRec["RoundNumber"]) ||
	    ( $LastSer != $FRec["SeriesNumber"])) {
	  if( $FixtStr ) {
//		$FixtStr .= "     <TR><TD><BR/></TD></TR>\n";
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
		$query = ("SELECT DISTINCTROW HomeGround.HomeGroundName, HomeGround.HomeGroundAddress FROM HomeGround WHERE (((HomeGround.HomeGroundKey)=".$DummyRec['Venue']."))");
		if( !( $TRecq = $mysqli->query( $query ) ) ) {
		  return( htmlspecialchars( "Error(f8): " . $mysqli->error ) );
		}
		if($TRec = $TRecq->fetch_array( ) ) {
		  $VenHash[$DummyRec['Venue']] = $TRec["HomeGroundName"];
		} else {
		  $VenHash[$DummyRec['Venue']] = 'Venue not found ('.$DummyRec['Venue'].')';
		}
	  }
	  $DummyRec['HomeGroundName'] = $VenHash[$DummyRec['Venue']];
	}
	if( $ent > 0 ) {
	  $FStr .= MakeMatchEnt( $DummyRec, $DataRec["ScoreFormat"], $LastDate );
	} else {
	  $FStr .= MakeMatch( $DummyRec, $DataRec["ScoreFormat"], "", $LastDate );
	}
	$LastDate = $DummyRec["Scheduled"];
  }
  $FixtStr .= $FStr;
  //ENDFINALS

  $StData = "     <table class='teamfixttbl' border='0'>\n      ".$FixtStr."     </table>\n<p/>";
  $retval .= $StData;
  $submenu = menuli( 107, 1 - $ent, 'fixture?champ=' . $ChKey, 'Fixture' );
  $submenu .= addEnterResSubMenu($ChKey, $sel = ($ent > 0 ? 1: 0));
  $headstr = "Fixture - ";
//  if($ent > 0) {
//    $submenu .= menuli( 113, 1, 'enter-results?champ=' . $ChKey, 'Enter Results' );
//    $headstr = "Enter Results - ";
//  }
  $retmenu = menuhead( ) .
  				menuli( 101, 0, 'welcome', 'Welcome' ) .
  				menuli( 103, 0, 'about-scoretank', 'About ScoreTank' ) .
  				menuli( 105, 0, 'championship-list', 'Championship List' ) .
  				menuli( 106, 0, 'champ?champ=' . $ChKey, htmlspecialchars( $ChampRec["SBAbbrev"]." ".$ChampRec["GradeName"] ), $submenu ) .
  			 menufoot( "fixt" );
  return array( $retval, pagehead( $headstr . htmlspecialchars( $ChampRec["SBAbbrev"] . " " . $ChampRec["GradeName"] ) ), $retmenu );
}

function fixtentbypass( ) {
  $ChKey = 126;
  $ent = 1;
  $mysqli = stconnect( );
  $retval = "";

  $scriptprefix = "";
  if( strstr( $_SERVER['SERVER_NAME'], 'thebrasstraps.com' ) ) {
    $scriptprefix = "/scoretank";
  }

    $retval .= '<link type="text/css" href="' . $scriptprefix . '/jqlib/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" rel="stylesheet"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery-1.5.2.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/sarissa.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/sarissa_ieemu_xpath.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery.xslTransform.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery-ui-1.8.11.custom/development-bundle/ui/jquery-ui-1.8.11.custom.js"></script>' . "\n";
    $retval .= "<script type='text/javascript' src='" . $scriptprefix . "/templates/favouritest/stlib.js'></script>\n";

    $retval .= '<script type="text/javascript">' . "\n";
    $retval .= '$(document).ready( function( ) { jQuery( ".scoreentry" ).removeAttr("disabled"); } );';
    $retval .= 'function SendMatchRes( inputfld ) { SendMatchResLibBypass( inputfld ); }';
    $retval .= '</script>' . "\n";

  $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Championship.Status " .
		" FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
		" WHERE (((Championship.ChampionshipKey)=$ChKey))";
  if( !( $ChampRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(f2): " . $mysqli->error ) );
  }
  if( !( $ChampRec = $ChampRecq->fetch_array( ) ) ) {
	return( htmlspecialchars( "Error(f3): Unknown Championship" ) );
  }
  if($ChampRec["Status"] == 'H') {
	$matchtbl = 'MatchHist';
	$teamtbl= 'TeamHist';
  } else {
	$matchtbl = 'NMatch';
	$teamtbl = 'Team';
  }

  // MatchRec
  $query = "SELECT DISTINCTROW $matchtbl.ChampionshipKey, $matchtbl.RoundNumber, $matchtbl.MatchNumber, $teamtbl.TeamName AS HomeTeamName, Team_1.TeamKey AS AwayTeamKey, Team_1.TeamName AS AwayTeamName, HomeGround.HomeGroundName, $matchtbl.Scheduled, $matchtbl.HomeTeamRawScore, $matchtbl.HomeTeamScore, $matchtbl.HomeTeamSupScore, $matchtbl.AwayTeamRawScore, $matchtbl.AwayTeamScore, $matchtbl.AwayTeamSupScore, $matchtbl.Result, $matchtbl.MatchRef " .
		" FROM ($teamtbl INNER JOIN ($matchtbl INNER JOIN $teamtbl AS Team_1 ON $matchtbl.AwayTeamKey = Team_1.TeamKey) ON $teamtbl.TeamKey = $matchtbl.HomeTeamKey) INNER JOIN HomeGround ON $matchtbl.Venue = HomeGround.HomeGroundKey " .
		" WHERE ((($matchtbl.ChampionshipKey)=$ChKey)) " .
		" ORDER BY $matchtbl.ChampionshipKey, $matchtbl.RoundNumber, $matchtbl.MatchNumber";
  if( !( $MatchRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(f4): " . $mysqli->error ) );
  }

  //$DataRec
  $query = "SELECT DISTINCTROW ChampData.WinPoints, ChampData.LossPoints, ChampData.TiePoints, ChampData.DrawPoints, ChampData.ByePoints, ChampData.ForfeitPoints, ChampData.WalkOverWinScore, ChampData.WalkOverLossScore, ChampData.WalkOverWinPoints, ChampData.WalkOverLossPoints, ChampData.ScoreFormat, ChampData.RoundInterval, ChampData.LadderDisplay, ChampData.LadderSort " .
		" FROM ChampData INNER JOIN Championship ON ChampData.DataKey = Championship.DataKey " .
		" WHERE (((Championship.ChampionshipKey)=$ChKey))";
  if( !( $DataRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(f5): " . $mysqli->error ) );
  }
  $DataRec = $DataRecq->fetch_array( );

  $StData = "<h2><a " . href( 'fixture?champ=' . $ChKey ) . ">" . htmlspecialchars( $ChampRec["SBAbbrev"]." ".$ChampRec["GradeName"] ) . "</a></h2>\n";
  $StData .= $ChampRec["SportName"]. " - ".$ChampRec["SeasonName"] . "<p/>";
  $LastRound = 0;
  $LastDate = "";
  $FixtStr = "";
  while( $MatchRec = $MatchRecq->fetch_array( ) ) {
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
	$FixtStr = $FixtStr .
		( ( $ent > 0 ) ? MakeMatchEnt( $MatchRec, $DataRec["ScoreFormat"], $LastDate) :
                                 MakeMatch( $MatchRec, $DataRec["ScoreFormat"], "", $LastDate ) );
	$LastDate = $MatchRec["Scheduled"];
  }

  $query = "SELECT DISTINCTROW FSeries.ChampionshipKey, FSeries.RoundNumber, FSeries.SeriesNumber, FSeries.SeriesName, FSeries.HomeTeamKey, FSeries.HomeDeriv, FSeries.HomeDerivRank, FSeries.HomeDerivChamp, FSeries.AwayTeamKey, FSeries.AwayDeriv, FSeries.AwayDerivRank, FSeries.AwayDerivChamp, FSeries.Result, FMatch.MatchNumber, FMatch.HomeTeamRawScore, FMatch.HomeTeamScore, FMatch.HomeTeamSupScore, FMatch.AwayTeamRawScore, FMatch.AwayTeamScore, FMatch.AwayTeamSupScore, FMatch.Venue, FMatch.Scheduled, FMatch.Result, FMatch.ReverseHA, " .
	  " FMatch.MatchRef " .
	  " FROM FSeries , FMatch " .
	  " WHERE (((FSeries.ChampionshipKey)=$ChKey) OR (((FSeries.HomeDerivChamp)=$ChKey)) OR ((FSeries.AwayDerivChamp)=$ChKey)) " .
	  " AND ((FSeries.SeriesNumber = FMatch.SeriesNumber) AND (FSeries.RoundNumber = FMatch.RoundNumber) AND (FSeries.ChampionshipKey = FMatch.ChampionshipKey)) " .
	  " ORDER BY FSeries.RoundNumber, FSeries.RSeriesNumber, FSeries.SeriesNumber, FMatch.MatchNumber";
  if( !( $FRecq = $mysqli->query( $query ) ) ) {
	return( htmlspecialchars( "Error(f6): " . $mysqli->error ) );
  }
  $LastRound = 0;
  $LastSer = 0;
  while( $FRec = $FRecq->fetch_array( ) ) {
    if( !$FStr ) {
	  $FStr .= "<tr><td><br/></td></tr>" . MakeMatchHead("FINALS") . "<tr><td><br/></td></tr>";
	}
	//if new series...? $FStr
	if( $LastRound != $FRec["RoundNumber"] ) {
	  // $FRRec
	  $query = ("SELECT DISTINCTROW FRound.RoundName " .
									" FROM FRound " .
									" WHERE ChampionshipKey = " . $FRec["ChampionshipKey"] .
									" AND RoundNumber = ".$FRec["RoundNumber"]);
	  if( !( $FRRecq = $mysqli->query( $query ) ) ) {
		return( htmlspecialchars( "Error(f7): " . $mysqli->error ) );
	  }
	  if( $FRRec = $FRRecq->fetch_array( ) ) {
		if($FRRec["RoundName"]) {
		  if( $FixtStr ) {
//			$FixtStr .= "     <TR><TD><BR/></TD></TR>\n";
		  }
		  $FStr .= MakeMatchHead($FRRec["RoundName"]);
		}
	  }
	}
	if( ( $LastRound != $FRec["RoundNumber"]) ||
	    ( $LastSer != $FRec["SeriesNumber"])) {
	  if( $FixtStr ) {
//		$FixtStr .= "     <TR><TD><BR/></TD></TR>\n";
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
		$query = ("SELECT DISTINCTROW HomeGround.HomeGroundName, HomeGround.HomeGroundAddress FROM HomeGround WHERE (((HomeGround.HomeGroundKey)=".$DummyRec['Venue']."))");
		if( !( $TRecq = $mysqli->query( $query ) ) ) {
		  return( htmlspecialchars( "Error(f8): " . $mysqli->error ) );
		}
		if($TRec = $TRecq->fetch_array( ) ) {
		  $VenHash[$DummyRec['Venue']] = $TRec["HomeGroundName"];
		} else {
		  $VenHash[$DummyRec['Venue']] = 'Venue not found ('.$DummyRec['Venue'].')';
		}
	  }
	  $DummyRec['HomeGroundName'] = $VenHash[$DummyRec['Venue']];
	}
	if( $ent > 0 ) {
	  $FStr .= MakeMatchEnt( $DummyRec, $DataRec["ScoreFormat"], $LastDate );
	} else {
	  $FStr .= MakeMatch( $DummyRec, $DataRec["ScoreFormat"], "", $LastDate );
	}
	$LastDate = $DummyRec["Scheduled"];
  }
  $FixtStr .= $FStr;
  //ENDFINALS

  $StData .= "     <table class='teamfixttbl' border='0'>\n      ".$FixtStr."     </table>\n<p/>";
  $retval .= $StData;
  $headstr = "Fixture - ";
  if($ent > 0) {
    $headstr = "Enter Results - ";
  }
  $retmenu = menuhead( ) .
  				menuli( 101, 0, 'welcome', 'Welcome' ) .
  			 menufoot( "fixt" );
  return array( $retval, pagehead( $headstr . htmlspecialchars( $ChampRec["SBAbbrev"] . " " . $ChampRec["GradeName"] ) ), $retmenu );
}

function about( ) {
  $retmenu = menuhead( ) .
  				menuli( 101, 0, 'welcome', 'Welcome', null ) .
  				menuli( 103, 1, 'about-scoretank', 'About ScoreTank', null ) .
  				menuli( 105, 0, 'championship-list', 'Championship List', null ) .
  			 menufoot( 'about' );
  return array( "", "", $retmenu );
}

function profileselector( ) {
  $retval = "";
  //$retval .= '<link type="text/css" href="/jqlib/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" rel="stylesheet"></script>' . "\n";
  //$retval .= '<script type="text/javascript" src="/jqlib/jquery-1.5.2.js"></script>' . "\n";
  //$retval .= '<script type="text/javascript" src="/jqlib/sarissa.js"></script>' . "\n";
  //$retval .= '<script type="text/javascript" src="/jqlib/sarissa_ieemu_xpath.js"></script>' . "\n";
  //$retval .= '<script type="text/javascript" src="/jqlib/jquery.xslTransform.js"></script>' . "\n";
  //$retval .= '<script type="text/javascript" src="/jqlib/jquery-ui-1.8.11.custom/development-bundle/ui/jquery-ui-1.8.11.custom.js"></script>' . "\n";
  //$retval .= "<script type='text/javascript' src='/templates/favouritest/stlib.js'></script>\n";
  $retval .= "<div id='fb-root'></div>\n";
  $retval .= '<script type="text/javascript" src="https://connect.facebook.net/en_US/all.js"></script>' .
       		 '<script type="text/javascript">' . "\n";
  $retval .= "window.fbAsyncInit = function( ) {\n" .
	         " FB.init({  \n" .
			         "  appId  : '" . fbcred_get_app_id( ) . "', \n" .
					 "  status : true, \n" . // check login status
					 "  cookie : true, \n" . // enable cookies to allow the server to access the session
					 "  xfbml  : true \n" .  // parse XFBML
				" }); " .
			 " FB.login( function( response ) {\n" .
			 "   if( response.authResponse ) {\n" .
			 "     window.alert( 'Successfully logged in' );" .
                         "     document.getElementById('LoginProfile').innerHTML = 'Successfully logged in; you should now be able to access the Facebook pages that you are authorised to use.<br/><br/>You can now close this window.';" .
			 //"     loadPage( );" .
			 "   } else {\n" .
			 "     window.alert( 'You must be logged in' );" .
			 "   }\n" .
			 " })\n" .
			 "}\n";
  $retval .= '</script>' . "\n";
  $retval .= "<p/><span id='LoginProfile'>Please log in and select your Facebook profile via the popup box (ensure popups are enabled)</span><p/>";
  //$retval .= "<span id='adminpage'></span>";
  //$retval .= "<div id='matchDialog' title='Match'></div>";
  //$retval .= "<div id='champDialog' title='Championship'></div>";
  //$retval .= "<div id='teamDialog' title='Team'></div>";
  $retmenu = menuhead( ) .
  				menuli( 101, 0, 'welcome', 'Welcome' ) .
			  '</ul>' .	
			 '</div>';

  return array( $retval, "", $retmenu );
}


function pagehead2( $title, $showjq = true ) {
	$path_parts = pathinfo( $_SERVER["PHP_SELF"] );
	$linkbase = "/scoretank";
	$retval =
	'<base href="' . $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . '" />' . "\n" .
	'<meta http-equiv="content-type" content="text/html; charset=utf-8" />' . "\n" .
	'<meta name="title" content="' . $title . '" />' . "\n" .
	'<meta name="author" content="Administrator" />' . "\n" .
	'<meta name="description" content="ScoreTank - Free Community Sports Management" />' . "\n" .
	'<meta name="generator" content="Joomla! 1.6 - Open Source Content Management" />' . "\n" .
	'<title>' . $title . '</title>' . "\n" .
	'<link href="' . $_SERVER["REQUEST_URI"] . '" rel="canonical" />' . "\n" .
	'<link href="' . $linkbase . '/templates/favouritest/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />' . "\n" .
	'<link rel="stylesheet" href="' . $linkbase . '/media/jui/css/bootstrap.min.css" type="text/css"/>' . "\n" .
	'<link rel="stylesheet" href="' . $linkbase . '/media/jui/css/bootstrap-responsive.css" type="text/css"/>' . "\n";
	if( $showjq ) {
		$retval .= '<script type="text/javascript" src="' . $linkbase . '/media/jui/js/jquery.min.js"></script>' . "\n";
		$retval .= '<script type="text/javascript" src="' . $linkbase . '/jqlib/sarissa.js"></script>' . "\n";
		$retval .= '<script type="text/javascript" src="' . $linkbase . '/jqlib/sarissa_ieemu_xpath.js"></script>' . "\n";
		$retval .= '<script type="text/javascript" src="' . $linkbase . '/jqlib/jquery.xslTransform.js"></script>' . "\n";
		$retval .= '<script type="text/javascript" src="' . $linkbase . '/jqlib/jquery-ui-1.8.11.custom/development-bundle/ui/jquery-ui-1.8.11.custom.js"></script>' . "\n";
		$retval .= "<script type='text/javascript' src='" . $linkbase . "/templates/favouritest/stlib.js'></script>\n";
	
		// $retval .=
		// '<script src="' . $linkbase . '/media/jui/js/jquery.min.js" type="text/javascript"></script>' . "\n" .
		// '<script src="' . $linkbase . '/media/jui/js/jquery-noconflict.js" type="text/javascript"></script>' . "\n" .
		// '<script src="' . $linkbase . '/media/jui/js/jquery-migrate.min.js" type="text/javascript"></script>' . "\n" .
		$retval .= '<script src="' . $linkbase . '/media/system/js/caption.js" type="text/javascript"></script>' . "\n";
		// '<script src="' . $linkbase . '/media/jui/js/bootstrap.min.js" type="text/javascript"></script>' . "\n" .
		$retval .= '<script type="text/javascript">' . "\n";
		$retval .= '  $(document).ready( function() { ' .
			' console.log("docready"); ' .
			' initialiseCompAdmin(); ' .
			' });' . "\n";
		$retval .= 'jQuery(window).on(\'load\', function() { new JCaption(\'img.caption\');' . "\n" . '});' . "\n";
		$retval .= '</script>' . "\n";
	}
	return( $retval );
  
}
  
function create( ) {
	//$mysqli = sticonnect( );
	if( !strstr( $_SERVER['SERVER_NAME'], 'thebrasstraps.com' ) ) {
		return st_home( );
	}
	if(!( $_SERVER["REQUEST_SCHEME"] == "https" )) {
		return st_home( );
	}


	$retval = "";

    $scriptprefix = "/scoretank";

if(false) {
	$retval .= '<link type="text/css" href="' . $scriptprefix . '/jqlib/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" rel="stylesheet"></script>' . "\n";
	// note! 27 Aug 2016... this jQuery call is a 2nd one, as /media/jui/js/jquery.min.js is already loaded. Let's aim at cleaning this up in future!
	// this causes a conflict - nb 1.5.2 doesn't support isNumeric
	//$retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery-1.5.2.js"></script>' . "\n";
	$retval .= '<script type="text/javascript" src="/media/jui/js/jquery.min.js"></script>' . "\n";
	$retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/sarissa.js"></script>' . "\n";
	$retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/sarissa_ieemu_xpath.js"></script>' . "\n";
	$retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery.xslTransform.js"></script>' . "\n";
	$retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery-ui-1.8.11.custom/development-bundle/ui/jquery-ui-1.8.11.custom.js"></script>' . "\n";
	$retval .= "<script type='text/javascript' src='" . $scriptprefix . "/templates/favouritest/stlib.js'></script>\n";
	$retval .= '<script type="text/javascript">' . "\n";
	//$retval .= '$(document).ready( function( ) { } );';
	$retval .= '</script>' . "\n";
}
	$retval .= "<span>This is a test system for Admin Data Entry. It uses Facebook authentication &amp; ScoreTank authorisation.</span><p/>";
	$retval .= "<span id='adminpage'></span>";
	$retval .= "<div id='matchDialog' title='Match'></div>";
	$retval .= "<div id='champDialog' title='Championship'></div>";
	$retval .= "<div id='teamDialog' title='Team'></div>";
	$retval .= "<span id='scriptprefix' style='display:none;'>" . $scriptprefix . "</div>";
  $retmenu = menuhead( ) .
				  menuli( 101, 0, 'welcome', 'Welcome' ) .
			 menufoot( 'create' );

	$pagehead = pagehead2('Competition Admin', true);
  	return array( $retval, $pagehead, $retmenu );
}

function getScriptPrefix( ) {
	if( strstr( $_SERVER['SERVER_NAME'], 'thebrasstraps.com' ) ) {
		return( "/scoretank" );
	}
}

function reactable( ) {
	$scriptprefix = "";
	if( strstr( $_SERVER['SERVER_NAME'], 'thebrasstraps.com' ) ) {
	  $scriptprefix = "/scoretank";
	}
	$retval = "";
	$retval .= "<h1>Reactable from php</h1>";
	$retval .= "<p>Test page</p>";
	$retval .= "<div id='root'/>";
	$retval .= '<script src="https://unpkg.com/react@17/umd/react.development.js" crossorigin></script>';
	$retval .= '<script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js" crossorigin></script>';
	$retval .= '<script src="' . $scriptprefix . '/templates/favouritest/rtest_button.js"></script>';
	return array( $retval, pagehead( "Reactable", false ), "" );
}

function stfbauth( ) {
  $retval = "";

  $scriptprefix = "";
  if( strstr( $_SERVER['SERVER_NAME'], 'thebrasstraps.com' ) ) {
    $scriptprefix = "/scoretank";
  }

  $authkey = 0;
  if(( $_SERVER["REQUEST_SCHEME"] == "https" ) &&
       isset( $_REQUEST["authkey"] ) ) {
    $authkey = $_REQUEST["authkey"];

    $retval .= '<link type="text/css" href="' . $scriptprefix . '/jqlib/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" rel="stylesheet"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery-1.5.2.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/sarissa.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/sarissa_ieemu_xpath.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery.xslTransform.js"></script>' . "\n";
    $retval .= '<script type="text/javascript" src="' . $scriptprefix . '/jqlib/jquery-ui-1.8.11.custom/development-bundle/ui/jquery-ui-1.8.11.custom.js"></script>' . "\n";
    $retval .= "<script type='text/javascript' src='" . $scriptprefix . "/templates/favouritest/stlib.js'></script>\n";
    $retval .= "<div id='fb-root'></div>\n";
    $retval .= '<script type="text/javascript" src="https://connect.facebook.net/en_US/all.js"></script>' .
       		 '<script type="text/javascript">' . "\n";
    $retval .= "window.fbAsyncInit = function( ) {\n" .
	         " FB.init({  \n" .
			         "  appId  : '" . fbcred_get_app_id( ) . "', \n" .
					 "  status : true, \n" . // check login status
					 "  cookie : true, \n" . // enable cookies to allow the server to access the session
					 "  xfbml  : true \n" .  // parse XFBML
				" }); " .
			 " FB.login( function( response ) {\n" .
			 "   if( response.authResponse ) {\n" .
			 "     loadStfbAuthPage( );" .
			 "   } else {\n" .
			 "     window.alert( 'You must be logged in' );" .
			 "   }\n" .
			 " })\n" .
			 "}\n";
    $retval .= "function sendAuth( authtype ) { var Displ =  document.getElementById( 'Displ' ).value; if( Displ < 0 ) { window.alert( 'Please select whether you want your name displayed' ); return; }\n";
    $retval .= 'var queryParams = { "req" : "auth", "Authtype" : authtype, "Displ" : Displ, "authkey" : ' . $authkey . ' };';
  //$.post( "https://ssl4.westserver.net/scoretank.com.au/fb/xstfbauth.php", queryParams, sendAuthCB, "json" );
    $retval .= 'jQuery.post( "https://www.thebrasstraps.com/scoretank/fb/xentry.php", queryParams, sendAuthCB, "json" ); }' . "\n";
    $retval .= '</script>' . "\n";

    $query = "select * from FBAccred where InitAuth = $authkey and FBID is NULL";
    $mysqli = stconnect( );
    $ARecq = $mysqli->query( $query );
    $AccredFor = "";
    $retstr = "Error";
    if( $ARecq ) {
      if( $ARec = $ARecq->fetch_array( ) ) {
        if( $ARec["AccredRole"] == 1 ) {
           $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Season.SeasonKey, SportingBody.SBKey, Championship.Status, Championship.ChampionshipKey " .
           " FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
           " WHERE Championship.ChampionshipKey = " . $ARec["AccredKey"];
           $ChRecq = $mysqli->query( $query ) or die( $mysqli->error );
           $ChRec = $ChRecq->fetch_array( );
           $AccredFor = "<H1>" . $ChRec["SBAbbrev"]." ".$ChRec["GradeName"] ."</H1>\n    ".
                                   $ChRec["SportName"]. " - ".$ChRec["SeasonName"]."<p>\n    ";
           $retstr = "<p>Authorising for entering scores, for Championship: " . $AccredFor . "<p><p/>\n";
           $retstr .= "<p>Click the button below to allow you to be able to enter results for this Championship.</p>";

           $retstr .= '<table><tr style="display:none;"><td>Display your name as an administrator?  <select id="Displ" onchange="chgDispl( this );" disabled="1">';
           $retstr .= '<option value="0">Select...</option><option value="0">Do not display</option><option value="1">Display</option>';
           $retstr .= '</select></td></tr><tr><td align="center">';
           $retstr .= '<input id="xsubmit" type="button" value="Become admin" class="inputbutton" onclick="sendAuth( ); return false;" disabled="1"/>';
           $retstr .= '</td></tr></table>';
           $retstr .= '<A href="index.php/champ?champ=' . $ChRec["ChampionshipKey"] . '" id="proceed" style="display:none;">Proceed to Championship page</A>';
        }
      }
    }
    $retval .= $retstr;

  } else {
    $retval .= "<p>Error - please check the URL</p>";
  }
  $retmenu = menuhead( ) .
  				menuli( 101, 0, 'welcome', 'Welcome' ) .
			  '</ul>' .	
			 '</div>';
  
  return array( $retval, "", $retmenu );
}

function renderST( $inst ) {
  $retval = "";
  $jinput = JFactory::getApplication()->input;
  $ArticleID = $jinput->getInt('id' );
  if( !( preg_match('/^\d+$/', $ArticleID ) ) || ( !$ArticleID ) ) {
	return( htmlspecialchars( "Error(r1)" ) );
  }
  if( $ArticleID == 9 ) {
    return( st_home( ) );
  }
  if( $ArticleID == 3 ) {
    return( champlist( ) );
  }
  if( $ArticleID == 2 ) {
    return( champ( ) );
  }
  if( $ArticleID == 7 ) {
    return( team( ) );
  }
  if( $ArticleID == 8 ) {
    return( teamhist( ) );
  }
  if( $ArticleID == 5 ) {
    return( fixt( ) );
  }
  if( $ArticleID == 4 ) {
    return( create( ) );
  }
  if( $ArticleID == 11 ) {
    return( profileselector( ) );
  }
  if( $ArticleID == 1 ) {
    return( about( ) );
  }
  if( $ArticleID == 12 ) {
    return( fixt( 1 ) );
  }
  if( $ArticleID == 13 ) {
    return( stfbauth( ) );
  }
  if( $ArticleID == 14 ) {
    return( fixtentbypass( ) );
  }
  if( $ArticleID == 15 ) {
	return( reactable( ) );
  }

  return( array( "", "", "" ) );
}

?>

