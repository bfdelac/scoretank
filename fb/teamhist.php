<?php

  include 'stpage.php';
  include 'facebook.php';
  $mysqli = sticonnect( );
  $user_id = fbconnect( );

$TeamNum = $_REQUEST["team"];

//$TeamRec
$query = "SELECT *
FROM Team
WHERE TeamKey = $TeamNum";
  $TeamRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
  if( $TeamRec = $TeamRecq->fetch_array( ) ) {
    $teamtbl = 'Team';
    $matchtbl = 'NMatch';
    $tlptbl = 'TeamLadderPos';
  } else {
    $query = "SELECT *
FROM TeamHist
WHERE TeamKey = $TeamNum";
    $TeamRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
    $TeamRec = $TeamRecq->fetch_array( );
    $teamtbl = 'TeamHist';
    $matchtbl = 'MatchHist';
    $tlptbl = 'TeamLadderPosHist';
  }
  $ChKey = $TeamRec["ChampionshipKey"];

  $query = "select * from FBAccred where AccredRole = 1 and AccredKey = $ChKey and FBID = $user_id";
  $AuthQ = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $AuthRec = $AuthQ->fetch_array( );

//$MatchRec
$query = "SELECT RoundNumber, HTeam.TeamName As HomeTeamName, HomeTeamKey, ATeam.TeamName As AwayTeamName, AwayTeamKey, Venue, HomeGroundName, HomeGroundAddress, Scheduled, Result, HomeTeamRawScore, HomeTeamScore, HomeTeamSupScore, AwayTeamRawScore, AwayTeamScore, AwayTeamSupScore
FROM $matchtbl, $teamtbl HTeam, $teamtbl ATeam, HomeGround
WHERE HTeam.TeamKey = $matchtbl.HomeTeamKey AND ATeam.TeamKey = $matchtbl.AwayTeamKey AND (HTeam.TeamKey = $TeamNum OR ATeam.TeamKey = $TeamNum)  AND $matchtbl.Venue = HomeGround.HomeGroundKey
ORDER BY RoundNumber, MatchNumber";
  $MatchRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );

//$DataRec
$query = "SELECT * FROM Championship, ChampData
WHERE Championship.DataKey = ChampData.DataKey AND Championship.ChampionshipKey = ".$TeamRec["ChampionshipKey"];
  $DataRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $DataRec = $DataRecq->fetch_array( );

//$TZRec = new bdb;
//$TZRec->Sql("select SportingBody.SBTZ, Sport.SportKey, Sport.SportName, Grade.GradeName from SportingBody, Championship, Competition, Contest, Sport, Grade
//Where Championship.ChampionshipKey = ".$TeamRec->Data("ChampionshipKey").
//" And Championship.CompKey = Competition.CompKey
//And Competition.ContestKey = Contest.ContestKey
//And Contest.SBKey = SportingBody.SBKey
//And Competition.GradeKey = Grade.GradeKey
//And Contest.SportKey = Sport.SportKey");
//if($TZRec->FetchRow()) {
    //$tz = $TZRec->Data("SBTZ");
    //$tz =~ s/^\s+//;
    //if(length($tz) > 0) {
        //$ENV{TZ}=":$tz";
    //}
//}

  $StData = addPreIframe( "Team Ladder History" );
  $StData .= "<div id='fb-root'></div><H1>ScoreTank</H1><P/>";
  $StData .= addBodyScript( );
  $StData .= "<fb:tabs>" .
	          "<fb:tab-item href='champ.php?champ=" . $ChKey . "' title='Ladder' selected='false'/>" .
			  "<fb:tab-item href='fixt.php?champ=" . $ChKey . "' title='Fixture' selected='false'/>" .
			  "<fb:tab-item href='chinfo.php?champ=" . $ChKey . "' title='Championship Info' />" .
			  ( $AuthRec ? ( "<fb:tab-item href='fixt.php?ent=1&champ=" . $ChKey . "' title='Enter Results' selected='false'/>" ) : "" ) .
			  "<fb:tab-item href='team.php?team=" . $TeamNum . "' title='Team Summary' />" .
			  "<fb:tab-item href='team.php?teamfixt=" . $TeamNum . "' title='Team Fixture' />" .
			  "<fb:tab-item href='team.php?teamven=" . $TeamNum . "'  title='Team Venues' />" .
			  "<fb:tab-item href='teamhist.php?team=" . $TeamNum . "' title='Season Graph' selected='true'/>" .
			 "</fb:tabs>";

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
  $StData .= addFbTab( "Team Summary", "team.php?team=" . $TeamNum, "", 0 );
  $StData .= addFbTab( "Team Fixture", "team.php?teamfixt=" . $TeamNum, "", 0 );
  $StData .= addFbTab( "Team Venues", "team.php?teamven=" . $TeamNum, "", 0 );
  $StData .= addFbTab( "Season Graph", "teamhist.php?team=" . $TeamNum, "last", 1 );
							    $StData .= PostFbTab( );
								  $StData .= "</div>";

  $StData .= "<p/>";

    //$StIndex = IndexTitle($TZRec->Data("GradeName")).
               //IndexLadd($ChKey, br).
               //IndexFixt($ChKey, br).
               //IndexTitle($TeamRec->Data("TeamName")).
               //IndexTeamSumm($TeamNum, br).
               //IndexTeamFixt($TeamNum, br).
               //IndexTeamVen($TeamNum, br).
               //IndexTitle($TZRec->Data("SportName")).
               //IndexLinks($TZRec->Data("SportKey"), p).
               //IndexTitle("ScoreTank Info").
               //IndexCopy(p).
               //IndexMain(br);


    $StData .= "<H1>History of ladder position for: ".$TeamRec["TeamName"]."</H1>\n";
    $Heading = $TeamRec["TeamName"]." - History";

    if( !( $MatchRec = $MatchRecq->fetch_array( ) ) ) {
      $StData .= "    <H2>No matches found</H2>";
    } else {
      $StData = $StData."     Current <A href='champ.php?champ=".$TeamRec["ChampionshipKey"] . "'>ladder</A> position: ".$TeamRec["LadderPos"] . "\n     ";
      if ($TeamRec["EqualPos"] != $TeamRec["LadderPos"]) {
        $StData = $StData."      (Equal ".$TeamRec["EqualPos"].")";
      }
	  $StData .= "<p/>\n";

        //$PosRec
	  $query = "SELECT Count(TeamKey) AS NumTeams
FROM $teamtbl
WHERE $teamtbl.ChampionshipKey = ".$TeamRec["ChampionshipKey"];
	  $PosRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
	  $PosRec = $PosRecq->fetch_array( );
	  $laddpos = array( );
      for($NumTeams = 1; $NumTeams <= $PosRec["NumTeams"]; $NumTeams++) {
          $laddpos[] = $NumTeams;
      }
      $laddposses = join( ",", array_reverse($laddpos));
      $NumTeams = $PosRec["NumTeams"];

      $values = '';
      //$PosRec
	  $query = "SELECT *
FROM $tlptbl
WHERE TeamKey = $TeamNum
ORDER BY RoundNumber";
	  $PosRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
      $numround = 1;
	  $xvals = array( );
	  $vals = array( );
      while( $PosRec = $PosRecq->fetch_array( ) ) {
        $xvals[] = $numround++;
        $vals[] = $NumTeams - $PosRec["LadderPos"] + 1;
      }

//# $StData .= "SELECT Max(RoundNumber) AS NumRounds FROM $matchtbl WHERE $matchtbl.ChampionshipKey =  ".$TeamRec["ChampionshipKey"]." ";
//$PosRec
$query = "SELECT Max(RoundNumber) AS NumRounds
FROM $matchtbl
WHERE $matchtbl.HomeTeamKey >= 0 AND $matchtbl.ChampionshipKey =  ".$TeamRec["ChampionshipKey"];
	  $PosRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
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
        

//<p>
//<param name=dataset0yValues value="'.$values.'">
//<param name=dataset0xValues value="'.join(",", @xvals).'">
//<param name=plotAreaColor value="FFFFCC">
//<param name=backgroundColor value="FFFFCC">
//<param name=barBaseLine value="'.($NumTeams * 1.01).'">
//<param name=dwellXString value="Round: #">
//<param name=dwellYString value="Position: #">
//<param name=yAxisTitle value="Ladder Position">
//<param name=yAxisStart value="'.($NumTeams).'">
//<param name=yAxisEnd value="1">
//<param name=yAxisOptions value="noAutoScale, rotateTitle">
//<param name=yAxisLabelCount value="'.($NumTeams - 1).'">
//<param name=yAxisTickCount value="'.($NumTeams - 1).'">
//<param name=xAxisTitle value="Round Number">
//<param name=xAxisLabelCount value="'.($numrounds + 1).'">
//<param name=xAxisTickCount value="'.($numrounds + 1).'">
//<param name=xAxisStart value="-1">
//<param name=xAxisEnd value="'.($numrounds).'">
//<param name=xAxisOptions value="noAutoScale">
//<param name=dataset0Labels value="'.$rounds.'">
//<param name=xAxisLabels value="'.$rounds.",".'">
////<param name=yAxisLabels value=",'.$laddposses.'">
////<param name=yAxisLabelPrecision value="0">
      //$StData .= "<img src='http://chart.apis.google.com/chart?cht=bvs&chs=200x125&chd=t:10,50,60,80,40|50,60,100,40,20&chco=4d89f9,c6d9fd&chbh=20&chds=0,160'/>";
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
		   //"chxl=0:|1|2|3|4|5|6|7|8|9|1:|10|1&" .
		   "chxl=0:|" . $rounds . "|1:|" . join( "|", $NTarr ) . "&" .
		   "chxp=1," . join( ",", $NTpos ) . "&" .
		   //"chd=t:8,4,4,4,3,2,1,8,9&" .
		   "chd=t:" . $values . "&" .
		   "chco=4d89f9&" .
		   "chbh=a&" .
		   "chg=0," . sprintf( "%.2f", ( 100 / $NumTeams ) ) . "&" .
		   "chtt=Ladder+position+by+round&" .
		   "chds=0," . $NumTeams . "'/>";
    }

//print header;
//print FmtPage($Heading, $StIndex, $StData );
  $StData .= addPostIframe( );
print $StData;

?>

