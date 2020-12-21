<?php

  session_start( );

  include 'stpage.php';
  //require_once 'facebook.php';
  require_once './vendor/autoload.php';
  use Facebook;

  $StData = addPreIframe( "Championship" );

  $mysqli = sticonnect( );
  $user_id = fbconnect5( );

  if( isset( $_REQUEST["champ"] ) ) {
    $ChKey = $_REQUEST["champ"];
  } else {
    $ChKey = 73;
  }

  if( !$user_id ) {
    die( "No user ID detected; please ensure you are logged on to Facebook." );
  }

  $query = "select * from FBAccred where AccredRole = 1 and AccredKey = $ChKey and FBID = $user_id";
  $AuthQ = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $AuthRec = $AuthQ->fetch_array( );

  $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Season.SeasonKey, SportingBody.SBKey, Championship.Status " .
    " FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
    " WHERE Championship.ChampionshipKey = $ChKey";
  $ChampRec = $mysqli->query( $query ) or die( $mysqli->error( ) );
  // ChampRec;
  $query = "SELECT ChampData.LadderDisplay, ChampData.LadderSort " .
    " FROM ChampData, Championship  " .
    " WHERE Championship.ChampionshipKey = $ChKey AND Championship.DataKey = ChampData.DataKey";
  $DataRec = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $DataRecR = $DataRec->fetch_array( );

//  print_r( $_SESSION );

  $StData .= "<H1>ScoreTank</H1><p/>" .
    "<fb:tabs>" .
     "<fb:tab-item href='champ.php?champ=" . $ChKey . "' title='Ladder' selected='true'/>" .
     "<fb:tab-item href='fixt.php?champ=" . $ChKey . "' title='Fixture' selected='false'/>" .
	 "<fb:tab-item href='chinfo.php?champ=" . $ChKey . "' title='Championship Info' />" .
     ( $AuthRec ? ( "<fb:tab-item href='fixt.php?ent=1&champ=" . $ChKey . "' title='Enter Results' selected='false'/>" ) : "" ) .
	"</fb:tabs>";
  $StData = fbStyles( );
  $StData .= '<div id="fb-root"></div>';
  $StData .= addBodyScript( );
  $StData .= "<div>";
  $StData .=  "<H1>ScoreTank</H1><p/>";
  $StData .= PreFbTab( );
  $StData .= addFbTab( "Ladder", "champ.php?champ=" . $ChKey, "first", 1 );
  $StData .= addFbTab( "Fixture", "fixt.php?champ=" . $ChKey, "", 0 );
  $StData .= addFbTab( "Championship Info", "chinfo.php?champ=" . $ChKey, ( $AuthRec ? "" : "last" ), 0 );
  if( $AuthRec ) {
    $StData .= addFbTab( "Enter Results", "fixt.php?ent=1&champ=" . $ChKey, "last", 0 );
  }
  $StData .= PostFbTab( );
  $StData .= "</div>";
  if( !( $ChampRecR = $ChampRec->fetch_array( ) ) ) {
  } else {
    if( $ChampRecR['Status'] == 'H') {
      $teamtbl = 'TeamHist';
    } else {
      $teamtbl = 'Team';
    }
    $query = " SELECT DISTINCTROW * " .
            " FROM " . $teamtbl .
            " WHERE ChampionshipKey = " . $ChKey .
            " ORDER BY " . LaddOrder( $DataRecR["LadderSort"] );
    $TeamRec = $mysqli->query( $query ) or die( $mysqli->error( ) );
        $tz = $ChampRecR["SBTZ"];
        $tz = preg_replace( '/^\s+/', '' );
        if(strlen($tz) > 0) {
//            $ENV{TZ}=":$tz";
        }
        $StData .=  "<P/><H1>" . $ChampRecR["SBAbbrev"]." ".$ChampRecR["GradeName"] ."</H1>\n    ".
                   $ChampRecR["SportName"]. " - ".$ChampRecR["SeasonName"]."<p>\n    ";
        $Iter = 1;
        $TblData = LaddHead($DataRecR["LadderDisplay"]);
        while ($TeamRecR = $TeamRec->fetch_array( ) ) {
            $TblData = $TblData.LaddRow($Iter, $DataRecR["LadderDisplay"], $TeamRecR );
            $Iter++;
        }
        $StData = $StData . "    <table border='1' style='border-collapse: collapse; border-style: solid; border-width: thin;'>\n" . $TblData .
		    "</table><P>";
//        "<a href='" . FixtURL( ) . $ChKey . "'>Click</a> for the championship fixture.<P>\n    ";
    }
    $Heading = $ChampRecR["GradeName"]." - Ladder";

//print FmtPage($Heading, $StIndex, $StData );
  $StData .= addPostIframe( );
print $StData;

function LaddHead( $displ ) {
  $matches = 0;
  if( preg_match( '/J(\d+)/', $displ, $matches ) ) {
    $LastCount = $matches[1];
    $parts = preg_split( '/J(\d+)/', $displ );
    $Heads = implode( "J", $parts );
  } else {
    $Heads = $displ;
  }
  $TblHead = '<td></td><th>Ladder</th>';
  for( $idx = 0; $idx < strlen( $Heads ); $idx++ ) {
    if( substr($Heads, $idx, 1) == 'J' ) {
      $ColHead = sprintf( LaddCol('J'), $LastCount );
      $TblHead .= "<th style='text-align:center;'>" . xmlentities( $ColHead ) . "</th>";
    } else {
      $TblHead .= "<th style='text-align:center;'>" . xmlentities( LaddCol( substr( $Heads, $idx, 1 ) ) ) . "</th>";
    }
  }
  return( "<tr class='ladderhead'>" . $TblHead . "</tr>\n" );
}

function LaddRow( $iter, $LadderDisplay, $TeamRecR ) {
	$LadderDisplay = preg_replace( "/\d/", "", $LadderDisplay );
    $TblRow = "<td style='text-align:right;'>" . $iter . "</td>";
    $TblRow .= "<td style='text-align:left;'><a href='team.php?team=" . $TeamRecR["TeamKey"] . "'>" . xmlentities( $TeamRecR["TeamName"] ) . "</a></td>";
    for( $idx = 0; $idx < strlen( $LadderDisplay ); $idx++ ) {
        $colval = $TeamRecR[LaddDBCol(substr($LadderDisplay, $idx, 1))];
        if( !isset( $colval ) ) {
            $TblRow .= "<td>&nbsp;</td>";
        } else if( preg_match( '/\./', $colval ) ) {
            $TblRow .= "<td style='text-align:right;'>" . sprintf( "%.1f", $colval ) . "</td>";
        } else if( substr( $LadderDisplay, $idx, 1 ) == 'K' ) {
            if( $colval < 0 ) {
                $TblRow .= '<td style="text-align:right;">' . abs( $colval ) . 'L</td>';
            } else if($_ > 0) {
                $TblRow .= '<td style="text-align:right;">' . $colval . 'W</td>';
            } else {
                $TblRow .= "<td style='text-align:right;'>0</td>";
            }
        } else {
            $TblRow = $TblRow . "<td style='text-align:right;'>" . $colval . "</td>";
        }
    }
    return( "     <tr class='ladderdata'>" . $TblRow . "</tr>\n" );
}

?>

