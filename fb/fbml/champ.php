<?php

  include 'stpage.php';
  require_once 'facebook.php';
  stconnect( );
  $user_id = fbconnect( );

  if( isset( $_REQUEST["champ"] ) ) {
    $ChKey = $_REQUEST["champ"];
  } else {
    $ChKey = 73;
  }

  $query = "select * from FBAccred where AccredRole = 1 and AccredKey = $ChKey and FBID = $user_id";
  $AuthQ = mysql_query( $query ) or die( mysql_error( ) );
  $AuthRec = mysql_fetch_array( $AuthQ );

  $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Season.SeasonKey, SportingBody.SBKey, Championship.Status " .
    " FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
    " WHERE Championship.ChampionshipKey = $ChKey";
  $ChampRec = mysql_query( $query ) or die( mysql_error( ) );
  // ChampRec;
  $query = "SELECT ChampData.LadderDisplay, ChampData.LadderSort " .
    " FROM ChampData, Championship  " .
    " WHERE Championship.ChampionshipKey = $ChKey AND Championship.DataKey = ChampData.DataKey";
  $DataRec = mysql_query( $query ) or die( mysql_error( ) );
  $DataRecR = mysql_fetch_array( $DataRec, MYSQL_ASSOC );

  $StData = "<H1>ScoreTank</H1><p/>" .
    "<fb:tabs>" .
     "<fb:tab-item href='champ.php?champ=" . $ChKey . "' title='Ladder' selected='true'/>" .
     "<fb:tab-item href='fixt.php?champ=" . $ChKey . "' title='Fixture' selected='false'/>" .
	 "<fb:tab-item href='chinfo.php?champ=" . $ChKey . "' title='Championship Info' />" .
     ( $AuthRec ? ( "<fb:tab-item href='fixt.php?ent=1&champ=" . $ChKey . "' title='Enter Results' selected='false'/>" ) : "" ) .
	"</fb:tabs>";
  if( !( $ChampRecR = mysql_fetch_array( $ChampRec, MYSQL_ASSOC ) ) ) {
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
    $TeamRec = mysql_query( $query ) or die( mysql_error( ) );
        $tz = $ChampRecR["SBTZ"];
        $tz = preg_replace( '/^\s+/', '' );
        if(strlen($tz) > 0) {
//            $ENV{TZ}=":$tz";
        }
        $StData .=  "<P/><H1>" . $ChampRecR["SBAbbrev"]." ".$ChampRecR["GradeName"] ."</H1>\n    ".
                   $ChampRecR["SportName"]. " - ".$ChampRecR["SeasonName"]."<p>\n    ";
        $Iter = 1;
        $TblData = LaddHead($DataRecR["LadderDisplay"]);
        while ($TeamRecR = mysql_fetch_array( $TeamRec, MYSQL_ASSOC ) ) {
            $TblData = $TblData.LaddRow($Iter, $DataRecR["LadderDisplay"], $TeamRecR );
            $Iter++;
        }
        $StData = $StData . "    <table border='1' style='border-collapse: collapse; border-style: solid; border-width: thin;'>\n" . $TblData .
		    "</table><P>";
//        "<a href='" . FixtURL( ) . $ChKey . "'>Click</a> for the championship fixture.<P>\n    ";
    }
    $Heading = $ChampRecR["GradeName"]." - Ladder";

//print FmtPage($Heading, $StIndex, $StData );
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

