<?php

  session_start( );

  include 'stpage.php';
  //require_once 'facebook.php';
  require_once './vendor/autoload.php';
  use Facebook;

  $mysqli = sticonnect( );
  $fbcon = 0;
  $user_id = fbconnect5( $fbcon );

//  if( $fbcon ) {
//    echo( "Has Connectino" );
//  }
  //  $ChampRec = new bdb;
  //  $DataRec  = new bdb;

  if( !isset( $_REQUEST["champ"] ) ) {
    print "No championship selected";
	die( );
  }
  if( !$user_id ) {
    die( "No user ID detected; please ensure you are logged on to Facebook." );
  }

  $ChKey = $_REQUEST["champ"];
  $query = "select * from FBAccred where AccredRole = 1 and AccredKey = $ChKey and FBID = $user_id";
  $AuthQ = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $AuthRec = $AuthQ->fetch_array( );
//die( $query );
//die( "marker chinfo" );

  $StData = "<H1>ScoreTank</H1><p/>" .
            "<fb:tabs>" .
	         "<fb:tab-item href='champ.php?champ=" . $ChKey . "' title='Ladder' selected='false'/>" .
		     "<fb:tab-item href='fixt.php?champ=" . $ChKey . "' title='Fixture' selected='false'/>" .
			 "<fb:tab-item href='chinfo.php?champ=" . $ChKey . "' title='Championship Info' selected='true'/>" .
			 ( $AuthRec ? ( "<fb:tab-item href='fixt.php?ent=1&champ=" . $ChKey . "' title='Enter Results' selected='false'/>" ) : "" ) .
		    "</fb:tabs>";

  $StData = addPreIframe( "Championship Info" );
  $StData .= fbStyles( );
  $StData .= '<div id="fb-root"></div>';
  $StData .= addBodyScript( );
  $StData .= "<div>";
  $StData .=  "<H1>ScoreTank</H1><p/>";
  $StData .= PreFbTab( );
  $StData .= addFbTab( "Ladder", "champ.php?champ=" . $ChKey, "first", 0 );
  $StData .= addFbTab( "Fixture", "fixt.php?champ=" . $ChKey, "", 0 );
  $StData .= addFbTab( "Championship Info", "chinfo.php?champ=" . $ChKey, ( $AuthRec ? "" : "last" ), 1 );
  if( $AuthRec ) {
    $StData .= addFbTab( "Enter Results", "fixt.php?ent=1&champ=" . $ChKey, "last", 0 );
    //$StData .= addFbTab( "TEST", "tdindex.php", "last", 0 );
  }
  $StData .= PostFbTab( );
  $StData .= "</div>";


// $ChampRec
  $query = "SELECT DISTINCTROW Sport.SportName, Sport.SportKey, SportingBody.SBAbbrev, SportingBody.SBSportingBodyName, Grade.GradeName, Season.SeasonName, SportingBody.SBTZ, Season.SeasonKey, SportingBody.SBKey " .
    " FROM (Season INNER JOIN ((Grade INNER JOIN ((SportingBody INNER JOIN Contest ON SportingBody.SBKey = Contest.SBKey) INNER JOIN Competition ON Contest.ContestKey = Competition.ContestKey) ON Grade.GradeKey = Competition.GradeKey) INNER JOIN Championship ON Competition.CompKey = Championship.CompKey) ON Season.SeasonKey = Championship.SeasonKey) INNER JOIN Sport ON Contest.SportKey = Sport.SportKey " .
    " WHERE (((Championship.ChampionshipKey)=$ChKey))";
    $ChampRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
    if( !( $ChampRec = $ChampRecq->fetch_array( ) ) ) {
      print "Could not find this championship(1).";
	  die( );
    }


// $DataRec
  $query = "SELECT * " .
    " FROM ChampData, Championship " .
    " WHERE Championship.ChampionshipKey = $ChKey AND Championship.DataKey = ChampData.DataKey";
//$DataRec->FetchRow();
  $DataRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
  if( !( $DataRec = $DataRecq->fetch_array( ) ) ) {
    print "Could not find this championship(2).";
	die;
  }

//        $tz = $ChampRec->Data("SBTZ");
//        $tz =~ s/^\s+//;
//        if(length($tz) > 0) {
//            $ENV{TZ}=":$tz";
//        }
    $StData .= "<P/><H1>" . $ChampRec["SBAbbrev"]." ".$ChampRec["GradeName"]."</H1>\n    ";
    $StData .= "<P/><H3>Championship Parameters</H3>";
    $StData .= "<B>Sport:</B> ".$ChampRec["SportName"]. "<BR/>";
    $StData .= "<B>Season:</B> ".$ChampRec["SeasonName"]."<BR/>\n    ";
    $StData .= "<B>Points for a win:</B> " . $DataRec["WinPoints"] . "<br/>";
    $StData .= "<B>Points for a loss:</B> " . $DataRec["LossPoints"] . "<br/>";
    if( isset( $DataRec["TiePoints"] ) ) {
      $StData .= "<B>Points for a draw:</B> " . $DataRec["TiePoints"] . "<br/>";
    }
    if( isset($DataRec["WalkOverWinPoints"])) {
            $StData .= "<B>Points for being forfeited:</B> ".$DataRec["WalkOverWinPoints"] . "<br/>";
    }
    if( isset($DataRec["WalkOverWinScore"])) {
            $StData .= "<B>Score for being forfeited:</B> ".$DataRec["WalkOverWinScore"] . "<br/>";
    }
    if( isset($DataRec["WalkOverLossPoints"])) {
            $StData .= "<B>Points for forfeiting:</B> ".$DataRec["WalkOverLossPoints"] . "<br/>";
    }
    if( isset( $DataRec["WalkOverLossScore"])) {
            $StData .= "<b>Score for forfeiting:</b> ".$DataRec["WalkOverLossScore"] . "<br/>";
    }
    $StData .= "<B>Ladder columns to display:</B> ". LaddHead( $DataRec["LadderDisplay"] ) . "<br/>";
        #$LaddOrd = LaddOrder($DataRec["LadderSort"]);
        #$LaddOrd =~s / DESC//g;
    $LaddOrd = LaddHead( $DataRec["LadderSort"] );
        $StData .= "<B>Ladder calculated on:</B> ".$LaddOrd . "<br/>";
//        $StData .= "<B>Timezone:</B> ".( $tz ? $tz : "Not set (defaults to Melbourne)" ) . "<br/>";
    $StData .= "<B>Score Format:</B> ".( $DataRec["ScoreFormat"] == 'S' ? "Basic" : ( $DataRec["ScoreFormat"] == 'T' ? 'Set-based' : ( $DataRec["ScoreFormat"] == 'F' ? "Australian Football" : "No format specified" ))) . "<br/>";

if(false) {
  $query = "select * from FBAccred where AccredRole = 1 and AccredKey = " . $ChKey . " and Display = 1 and FBID is not null";
  $AdminQ = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $hasAdmin = 0;

//echo( $_SESSION["fbSTATs"] );

  while( $AdminRec = $AdminQ->fetch_array( ) ) {
    if( ! $hasAdmin ) {
	  $StData .= "<B>Administrators:</B><BR/>";
	} else {
	  //$StData .= ", ";
	}

    try {
      $response = $fbcon->get('/' . $AdminRec["FBID"] . '?fields=name,picture', $_SESSION["fbSTATs"] );
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }
    $auser = $response->getGraphUser( );

	//$user_details = $facebook->api_client->users_getInfo( $AdminRec["FBID"], array( 'name', 'timezone', 'profile_url' );
	//$user_details = $facebook->api_client->users_getInfo(598176078,'name, timezone, profile_url, pic_square');
	//$StData .= "<table><tr><td><fb:profile-pic uid='" . $AdminRec["FBID"] . "' linked='true'/></td><td valign='center'><fb:name uid='" . $AdminRec["FBID"] . "'/></td></tr></table>";// . $user_details[0]['name'];
	
    $StData .= "<table><tr><td><img src='" . $auser["picture"]["url"] . "'/></td><td valign='center'>" . $auser["name"] . "</td></tr></table>";// . $user_details[0]['name'];
	//$StData .= "<table><tr><td>" . $AdminRec["FBID"] . "</td><td valign='center'></td></tr></table>";// . $user_details[0]['name'];
	//$StData .= "<div class='UIProfileBox_Content'>" .
	//            "<div class='UIOneOff_Container'>" .
//				 "<div class='UIGridRenderer clearfix'>" .
//				  "<div class='UIGridRenderer_Row clearfix'>" .
//				   "<div class='UIPortrait_TALL'>" .
//	                "<fb:profile-pic uid='" . $AdminRec["FBID"] . "' linked='true'/><td valign='center'><fb:name uid='" . $AdminRec["FBID"] . "'/></td></tr></table>";
	$hasAdmin = 1;
  }
}

//print header;
//print FmtPage("Championship Parameters", $StIndex, $StData );
  $StData .= addPostIframe( );
  print $StData;

function LaddHead( $lcode ) {
    $TblHead = "";
    $lcode = preg_replace( '/\s/', '', $lcode );
	if( preg_match( '/J(\d+)/', $lcode, $matches ) ) {
        $LastCount = $matches[1];
		$parts = preg_split( '/J(\d+)/', $lcode );
        $Heads = implode( "J", $parts );
    } else {
        $Heads = $lcode;
    }
    for( $idx = 0; $idx < strlen( $Heads ); $idx++ ) {
        if($TblHead) {
            $TblHead .= ", ";
        }
        if( substr($Heads, $idx, 1) == 'J' ) {
            $ColHead = sprintf(LaddCol('J'), $LastCount);
            $TblHead .= $ColHead;
        } else {
            $TblHead .= LaddCol(substr($Heads, $idx, 1));
        }
    }
    return ($TblHead);
}


?>

