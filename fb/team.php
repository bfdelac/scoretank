<script>
<!--

function BecomeFan( teamkey, becomingfan ) {
  var ajax = new Ajax( );
  ajax.responseType = Ajax.JSON;
  ajax.ondone = function( data ) {
	if( data.message ) {
    new Dialog( ).showMessage( 'Dialog1', data.message );
	} else {
    new Dialog( ).showMessage( 'Dialog2', data.error );
	}
	if( data.fbml_newHTML ) {
	  document.getElementById( "fanlist" ).setInnerFBML( data.fbml_newHTML );
	}
  }
  var queryParams = { "req" : "becomefan", "teamkey" : teamkey, "becomingfan" : becomingfan ? 1 : 0 };
  ajax.post( "http://www.scoretank.com.au/fb/xstfbauth.php", queryParams );
}

//-->

</script>

<?php

  include 'stpage.php';
  include 'facebook.php';
  $mysqli = sticonnect( );
  $user_id = fbconnect( );

  if( isset( $_REQUEST["team"] ) ) {
    $TeamNum = $_REQUEST["team"];
    $mode = 1;  //summary
  } else if( isset( $_REQUEST["teamfixt"] ) ) {
    $TeamNum = $_REQUEST["teamfixt"];
    $mode = 2;  //etc
  } else if ( isset( $_REQUEST["teamven"] ) ) {
    $TeamNum = $_REQUEST["teamven"];
    $mode = 3;  //etc
  }

  //$TeamRec
  $query = ("SELECT * FROM Team WHERE TeamKey = $TeamNum");
  $TeamRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
  if( $TeamRec = $TeamRecq->fetch_array( ) ) {
    $matchtbl = 'NMatch';
    $teamtbl = 'Team';
  } else {
    $matchtbl = 'MatchHist';
    $teamtbl = 'TeamHist';
    $query = ("SELECT *
FROM TeamHist
WHERE TeamKey = $TeamNum");
    $TeamRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
    $TeamRec = $TeamRecq->fetch_array( );
  }
  $ChKey = $TeamRec["ChampionshipKey"];
  $query = "select * from FBAccred where AccredRole = 1 and AccredKey = $ChKey and FBID = $user_id";
  $AuthQ = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $AuthRec = $AuthQ->fetch_array( );

  $StData = addPreIframe( "Team" );
  $StData .= "<div id='fb-root'></div><H1>ScoreTank</H1><p/>";
  $StData .= addBodyScript( );
  $StData .= "<fb:tabs>" .
              "<fb:tab-item href='champ.php?champ=" . $ChKey . "' title='Ladder' selected='false'/>" .
	          "<fb:tab-item href='fixt.php?champ=" . $ChKey . "' title='Fixture' selected='false'/>" .
			  "<fb:tab-item href='chinfo.php?champ=" . $ChKey . "' title='Championship Info' />" .
	( $AuthRec ? ( "<fb:tab-item href='fixt.php?ent=1&champ=" . $ChKey . "' title='Enter Results' selected='false'/>" ) : "" ) .
              "<fb:tab-item href='team.php?team=" . $TeamNum . "' title='Team Summary' " . ( ( $mode == 1 ) ? "selected='true'" : "" ) . "/>" .
              "<fb:tab-item href='team.php?teamfixt=" . $TeamNum . "' title='Team Fixture' " . ( ( $mode == 2 ) ? "selected='true'" : "" ) . "/>" .
              "<fb:tab-item href='team.php?teamven=" . $TeamNum . "'  title='Team Venues' " .  ( ( $mode == 3 ) ? "selected='true'" : "" ) . "/>" .
              "<fb:tab-item href='teamhist.php?team=" . $TeamNum . "' title='Season Graph' selected='false'/>" .
		     "</fb:tabs>";
if( 1 ) {
  $StData = fbStyles( );
  $StData .= "<div>";
  $StData .=  "<H1>ScoreTank</H1><p/>";
  $StData .= PreFbTab( );
  $StData .= addFbTab( "Ladder", "champ.php?champ=" . $ChKey, "first", 0 );
  $StData .= addFbTab( "Fixture", "fixt.php?champ=" . $ChKey, "", 0 );
  $StData .= addFbTab( "Championship Info", "chinfo.php?champ=" . $ChKey, "", 0 );
  if( $AuthRec ) {
    $StData .= addFbTab( "Enter Results", "fixt.php?ent=1&champ=" . $ChKey, "", $ent );
  }
  $StData .= addFbTab( "Team Summary", "team.php?team=" . $TeamNum, "", ( $mode == 1 ) );
  $StData .= addFbTab( "Team Fixture", "team.php?teamfixt=" . $TeamNum, "", ( $mode == 2 ) );
  $StData .= addFbTab( "Team Venues", "team.php?teamven=" . $TeamNum, "", ( $mode == 3 ) );
  $StData .= addFbTab( "Season Graph", "teamhist.php?team=" . $TeamNum, "last", 0 );
  $StData .= PostFbTab( );
  $StData .= "</div>";
}

  //$DataRec
  $query = ("SELECT * FROM Championship, ChampData
WHERE Championship.DataKey = ChampData.DataKey AND Championship.ChampionshipKey = ".$TeamRec["ChampionshipKey"]);
  $DataRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $DataRec = $DataRecq->fetch_array( );

  //$MatchRec
  $query = ("SELECT RoundNumber, HTeam.TeamName As HomeTeamName, HomeTeamKey, ATeam.TeamName As AwayTeamName, AwayTeamKey, Venue, HomeGroundName, HomeGroundAddress, Scheduled, Result, HomeTeamRawScore, HomeTeamScore, HomeTeamSupScore, AwayTeamRawScore, AwayTeamScore, AwayTeamSupScore
FROM $matchtbl, $teamtbl HTeam, $teamtbl ATeam, HomeGround
WHERE HTeam.TeamKey = HomeTeamKey AND ATeam.TeamKey = AwayTeamKey AND (HTeam.TeamKey = $TeamNum OR ATeam.TeamKey = $TeamNum)  AND Venue = HomeGround.HomeGroundKey
ORDER BY RoundNumber, MatchNumber");
  $MatchRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );

  //$FMatchRec
  $query = ( "SELECT FMatch.RoundNumber,
HTeam.TeamName as HomeTeamName, HomeTeamKey,
ATeam.TeamName As AwayTeamName, AwayTeamKey,
Venue, HomeGroundName, HomeGroundAddress, Scheduled, FMatch.Result,
HomeTeamRawScore, HomeTeamScore, HomeTeamSupScore,
AwayTeamRawScore, AwayTeamScore, AwayTeamSupScore,
SeriesName, RoundName
FROM FMatch, Team HTeam, Team ATeam, HomeGround, FSeries, FRound
WHERE HTeam.TeamKey = HomeTeamKey AND ATeam.TeamKey = AwayTeamKey
AND (HTeam.TeamKey = $TeamNum OR ATeam.TeamKey = $TeamNum)
AND Venue = HomeGround.HomeGroundKey
AND FSeries.ChampionshipKey = FMatch.ChampionshipKey
AND FSeries.RoundNumber = FMatch.RoundNumber
AND FSeries.SeriesNumber = FMatch.SeriesNumber
AND FRound.ChampionshipKey = FSeries.ChampionshipKey AND FRound.RoundNumber = FSeries.RoundNumber
ORDER BY RoundNumber, MatchNumber" );
  $FMatchRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );


//$TZRec = new bdb;
//$TZRec->Sql("select SportingBody.SBTZ, Sport.SportKey, Sport.SportName, Grade.GradeName from SportingBody, Championship, Competition, Contest, Sport, Grade
//Where Championship.ChampionshipKey = ".$TeamRec->Data("ChampionshipKey").
//" And Championship.CompKey = Competition.CompKey
//And Competition.ContestKey = Contest.ContestKey
//And Contest.SBKey = SportingBody.SBKey
//And Competition.GradeKey = Grade.GradeKey
//And Contest.SportKey = Sport.SportKey");
//if($TZRec->FetchRow()) {
//    $tz = $TZRec->Data("SBTZ");
//    $tz =~ s/^\s+//;
//    if(length($tz) > 0) {
//        $ENV{TZ}=":$tz";
//    }
//}

//    $StIndex = IndexTitle($TZRec->Data("GradeName")).
//               IndexLadd($ChKey, br).
//               IndexFixt($ChKey, br).
//               IndexTitle($TeamRec->Data("TeamName")).
//               ($mode != 1 ? IndexTeamSumm($TeamNum, br) : "").
//               ($mode != 2 ? IndexTeamFixt($TeamNum, br) : "").
//               ($mode != 3 ? IndexTeamVen($TeamNum, br)  : "").
//               IndexTeamGraph($TeamNum, br).
//               IndexTitle($TZRec->Data("SportName")).
//               IndexLinks($TZRec->Data("SportKey"), p).
//               IndexTitle("ScoreTank Info").
//               IndexCopy(p).
//               IndexMain(br);


  $StData .= "<P/>";
  if ($mode == 1) {
    $StData .= "<H1>Summary of results for: ".$TeamRec["TeamName"]."</H1>\n";
    $Heading = $TeamRec["TeamName"]." - Summary";
  } else if ($mode == 2 ) {
    $StData .= "<H1>Fixture for: ".$TeamRec["TeamName"]."</H1>\n";
    $Heading = $TeamRec["TeamName"]." - Fixture";
  } else {
    $StData .= "<H1>Venues for: ".$TeamRec["TeamName"]."</H1>\n";
    $Heading = $TeamRec["TeamName"]." - Venues";
  }

  if( !( $MatchRec = $MatchRecq->fetch_array( ) ) ) {
    $StData .= "    <H2>No matches found</H2>";
  } else {
    $StData .= "     Current <A href='champ.php?champ=".$TeamRec["ChampionshipKey"]."'>ladder</A> position: ".$TeamRec["LadderPos"]. "\n     ";
    if( $TeamRec["EqualPos"] != $TeamRec["LadderPos"] ) {
      $StData .= "      (Equal ".$TeamRec["EqualPos"].")";
    }
    if( $mode == 1 ) {
      $StData = $StData."<P/><A href='team.php?teamfixt=" . $TeamNum . "'>Click</A> for team fixture.<P/>\n";
    } else {
      $StData = $StData."<P/><A href='team.php?team=" . $TeamNum . "'>Click</A> for team summary.<P/>\n";
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

    $FRec = $MatchRec;	//FixtureRec
    $Finals = 0;
    do {
            $FHead = "";
            if( $FRec["RoundNumber"] != $LastRound ) {
                $SumStr = "";
                if( $RoundStr ) {
                    $FixtStr = $FixtStr.$RoundStr. "<TR><TD><BR/></TD></TR>";
                }
                $RoundStr = "";
            }
            if( $Finals == 1 ) {
                $FStr = "";
                if( $LastRound != $FRec["RoundNumber"] ) {
                    if( $FRec["RoundName"] ) {
                        $FStr = MakeMatchHead( $FRec["RoundName"] );
                    }
                }

                if( ( $LastRound != $FRec["RoundNumber"] ) ||
                    ( $LastSer != $FRec["SeriesNumber"] ) ) {
                    if( $SumStr ) {
                        $SumStr .= "     <TR><TD><BR/></TD></TR>\n";
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
                    $SumStr = MakeMatchHead("Round ".$FRec["RoundNumber"] ).MakeMatch($FRec, $DataRec["ScoreFormat"], $TeamRec["TeamName"]);
                } else { //add to existing round
                    $SumStr = $SumStr.MakeMatch($FRec, $DataRec["ScoreFormat"], $TeamRec["TeamName"]);
                }
            }
            if( ! $RoundStr ) {
                if( $Finals ) {
                    $RoundStr = MakeMatchHead("Round ".$FRec["RoundNumber"] );
                } else {
                    $RoundStr = MakeMatchHead("Round ".$FRec["RoundNumber"] );
                }
            }
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
                    $Upcoming = MakeMatchHead("Round ".$UpcomingRound ).MakeMatch($FRec, $DataRec["ScoreFormat"], $TeamRec["TeamName"]);
                } else {
                    $Upcoming .= MakeMatch($FRec, $DataRec["ScoreFormat"], $TeamRec["TeamName"]);
                }
            }
            $LastRound = $FRec["RoundNumber"];

            if( ( $mode == 3 ) && ($FRec["AwayTeamKey"] != -1 ) ) {
                $ven = $FRec["HomeGroundAddress"];
				$ven = preg_replace( '\n', '<BR/>' );
                $ts = strtotime( $FRec["Scheduled"] );
//$time = strftime( "%I:%M%p", 0, substr($_, 14, 2), substr($_, 11, 2), substr($_, 8, 2), substr($_, 5, 2) - 1, substr($_, 2, 2));
//$date = strftime( "%d&nbsp;%b&nbsp;%y ", 0, substr($_, 14, 2), substr($_, 11, 2), substr($_, 8, 2), substr($_, 5, 2) - 1, substr($_, 2, 2));
//$time =~ s/^0//;
//$date =~ s/^0//;
                $RoundStr .= "<TR class='match'><TD>" . $FRec["HomeTeamName"] . "</TD><TD>vs</TD><TD>" . $FRec["AwayTeamName"] . "</TD></TR>" .
  "<TR><TD colspan='6'><B>" . date( "D, j M Y", $ts ) . "</B> " . date( "g:ia", $ts ) . " at ".$FRec["HomeGroundName"] . "</TD></TR>" .
  "<TR><TD colspan='6'>" . $ven . "</TD></TR>";
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
            } else if($FRec["Result"] &&
                    ($FRec["Result"] != 'B') &&
                    ($FRec["Result"] != 'W')) {
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
                  $RoundStr .= "<TR><TD><BR/></TD></TR>" . MakeMatchHead("FINALS") . "<TR><TD><BR/></TD></TR>";
                }
            }
    } while( $FRec );
    $FixtStr = $FixtStr.$RoundStr. "<TR><TD><BR/></TD></TR>";

    $Matches = "";
    if( $Recent ) {
      $Matches = "<TR><TD colspan='6'><H3>Last Round:</H3></TR>\n".$Recent."<TR><TD><BR/></TD></TR>";
    }
    if( $Upcoming ) {
      $Matches = $Matches. "<TR><TD colspan='6'><H3>Next Round:</H3>\n".$Upcoming. "<TR><TD><BR/></TD></TR>";
    }
    if( $mode == 1 ) {
      $StData = $StData."     <table border='0'>".$Matches."</table>\n";

      if( $Streak > 1 ) {
        $StData .= "     <H3>Current Streak: $Streak wins</H3>";
      } else if( $Streak == 1 ) {
        $StData .= "     <H3>Current Streak: 1 win</H3>";
      } else if( $Streak == -1 ) {
        $StData .= "     <H3>Current Streak: 1 loss</H3>";
      } else if( $Streak < -1 ) {
        $StData .= "     <H3>Current Streak: ".abs($Streak)." losses</H3>";
      } else {
        $StData .= "     <H3>Current Streak: No wins or losses</H3>";
      }
    } else {
      $StData .= "     <TABLE border='0'>\n      ".$FixtStr."     </TABLE>\n";
    }
    $StData .= "\n    ";
  }

//print header;
//print FmtPage($Heading, $StIndex, $StData );

  if( isset( $_REQUEST["debug"] ) ) {
    $StData .= "<P/><H2>Fans</H2>\n";
	$StData .= "<span id='fanlist'>";
    $StData .= ListFans( $TeamNum, $user_id );
	$StData .= "</span>";
  }
  $StData .= addPostIframe( );
  print $StData;
//if( $mode == 1 ) {
//    HitLog( "team", $TeamNum );
//} else {
//    HitLog( "teamfixt", $TeamNum );
//}

?>

