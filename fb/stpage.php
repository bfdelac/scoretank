<?php

include __DIR__ . "/../fbcred.php";
include __DIR__ . "/../stdbcred.php";

function xmlentities( $string ) {
	return str_replace( array ( '&', '"', "'", '<', '>', '\\' ),
	                    array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' , '\\\\' ), $string );
}

function LaddDBCol( $colkey ) {
    return LaddCol( $colkey, 1 );
}

function LaddCol( $c, $db = null ) {
	if($c == 'P') { return( $db ? 'Played' : 'Played' ); }
	if($c == 'W') { return( $db ? 'Won' : 'Won' ); }
	if($c == 'L') { return( $db ? 'Lost' : 'Lost' ); }
//#   if($c == 'T') { return 'Ties' };
    if($c == 'D') { return( $db ? 'Drawn' : 'Drawn' ); }
    if($c == 'B') { return( $db ? 'Byes' : 'Byes' ); }
    if($c == 'F') { return( $db ? 'SFor' : 'For' ); }
    if($c == 'A') { return( $db ? 'Against' : 'Against' ); }
    if($c == 'S') { return( $db ? 'ForSup' : 'Sets For' ); }
    if($c == 'R') { return( $db ? 'AgainstSup' : 'Sets Against' ); }
    if($c == 'E') { return( $db ? 'Forfeit' : 'Forfeited' ); }
    if($c == 'X') { return( $db ? 'Points' : 'Points' ); }
    if($c == 'x') { return( $db ? 'FPoints' : 'Points' ); }
    if($c == 'M') { return( $db ? 'MatchRatio' : 'Match Ratio' ); }
    if($c == 'C') { return( $db ? 'Percentage' : '%' ); }
    if($c == 'H') { return( $db ? 'HWinLoss' : 'Home' ); }
    if($c == 'V') { return( $db ? 'AWinLoss' : 'Away' ); }
    if($c == 'K') { return( $db ? 'Streak' : 'Streak' ); }
    if($c == 'Y') { return( $db ? 'GAway' : 'Away Goals' ); }
    if($c == 'I') { return( $db ? 'GDiff' : 'Diff' ); }
    if($c == 'J') { return( $db ? 'LastN' : 'Last %d' ); }
//#    if($_[0] eq 'G') { return 'Set %' };
    return 'Error';
}

function LaddOrder( $LSort ) {
  $LaddSort = '';
  $LSort = preg_replace( '/(\s|H|V)/', '', $LSort );
  for( $idx = 0; $idx < strlen( $LSort ); $idx++ ) {
    $laddcode = substr( $LSort, $idx, 1 );
	$LaddSort .= LaddDBCol( $laddcode ) . " DESC, ";
  }
  return( $LaddSort . "TeamName" );
}

function FixtURL( ) {
  return( "fixt.php?champ=" );
}

function sticonnect( ) {
  return(
    mysqli_connect( "", stdb_username( ), stdb_password( ), stdb_dbname( ) ) ); 
}

function fbconnect( $retjson = null ) {
  $fbappid = fbcred_get_app_id( );

  $facebook = new Facebook(array(
    'appId'  => fbcred_get_app_id( ),
    'secret' => fbcred_get_app_secret( ),
//	'baseurl' => 'http://www.scoretank.com.au/fb/index.php',
	'cookie' => true
  ));

  // See if there is a user from a cookie
  $user = $facebook->getUser();

  if ($user) {
  //if( 0 ) {
    try {
      // Proceed knowing you have a logged in user who's authenticated.
      $user_profile = $facebook->api('/me');
      //$user_profile = $facebook->api('/'.$facebook_uid);
      return( $user_profile["id"] );
    } catch (FacebookApiException $e) {
  //echo '<h2>Error, user: ' . $user . '</h2>';
      return( $user );
      echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
      $user = null;
    }
  } else {
    if( isset( $retjson ) ) {
      die( '{"error" : "ScoreTank error( DZ )"}' );
    }
  }
  return( 0 );
}

function fbconnect5( &$fbcon = null ) {
  $fb = new Facebook\Facebook([
    'app_id' => fbcred_get_app_id( ),
    'app_secret' => fbcred_get_app_secret( ),
    'default_graph_version' => 'v2.5'
  ]);

  if( isset( $fbcon ) ) {
    $fbcon = $fb;
  }

  $helper = $fb->getPageTabHelper( );

  $accessToken = 0;
  try {
    $accessToken = $helper->getAccessToken();
    $_SESSION['fbSTAT'] = $accessToken;
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }

  $userId = $helper->getUserId( );
  //var_dump( $_SESSION );
  if( $userId ) {
    $_SESSION['fbSTUserId'] = $userId;
    $_SESSION['fbSTATs'] = $accessToken->getValue( );
    return( $userId );
  }

  if( $_SESSION['fbSTUserId'] ) {
    return( $_SESSION['fbSTUserId'] );
  }

  return( 0 );
}

function fbconnect5test( &$fbcon ) {
  $fb = new Facebook\Facebook([
    'app_id' => fbcred_get_test_app_id( ),
    'app_secret' => fbcred_get_test_app_secret( ),
    'default_graph_version' => 'v2.5'
  ]);

  if( isset( $fbcon ) ) {
    $fbcon = $fb;
  }

  $helper = $fb->getPageTabHelper( );

  $accessToken = 0;
  try {
    $accessToken = $helper->getAccessToken();
    $_SESSION['fbSTAT'] = $accessToken;
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }

  $userId = $helper->getUserId( );
  if( $userId ) {
    $_SESSION['fbSTUserId'] = $userId;
    $_SESSION['fbSTATs'] = $accessToken->getValue( );
    return( $userId );
  }

  if( $_SESSION['fbSTUserId'] ) {
    return( $_SESSION['fbSTUserId'] );
  }

  return( 0 );
}

function fbconnectDiag( ) {
  $facebook = new Facebook(array(
    'appId'  => fbcred_get_long_app_id( ),
    'secret' => fbcred_get_app_secret( ),
	'cookie' => true
  ));

  // See if there is a user from a cookie
  $user = $facebook->getUser();

  if ($user) {
	try {
	  // Proceed knowing you have a logged in user who's authenticated.
	  $user_profile = $facebook->api('/me');
//die( '{"error" : "ScoreTank error( DD )"}' );
	} catch (FacebookApiException $e) {
//die( '{"error" : "ScoreTank error( DX )"}' );
	  echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
	  $user = null;
	}
  } else {
//die( '{"error" : "ScoreTank error( DZ )"}' );
  }
  return( $user_profile["id"] );
}

function MakeFNameHead( $fname ) {
  return "      <TR><TD colspan='5' align='center' class='fnamehead'><B>" . $fname . "</B></TD></TR>\n";
}

function MakeMatchHead( $mh ) {
  return "      <TR><TD colspan='5' class='matchhead'><B>".$mh."</B></TD></TR>\n";
}

function MakeMatchHead_s( $mh ) {
  return "      <TR><TD colspan='5' class='matchhead'><B>".$mh."</B></TD><TD colspan='2'>&nbsp;</TD></TR>\n";
}


//parameters: 0- $MatchRec, 1- Score format, 2- Highlight teamname, 3- Lastdate
function MakeMatch( $MatchRec, $sfmt, $hiteam = null, $lastdate = null) {
  if($MatchRec["AwayTeamKey"] == -1) {
    if( $hiteam ) {
      return "      <TR class='match'><TD>Bye</TD><TD></TD></TR>";
    } else {
      return "      <TR class='match'><TD>" . $MatchRec["HomeTeamName"]. "</TD><TD>Bye</TD></TR>";
    }
  }
  if($MatchRec["Result"] == "H" ) {
    $LinkStr = "defeated&nbsp;";
    $HClass = "win";
    $AClass = "lose";
    if( $sfmt == $MatchRec["AwayTeamName"]) {
	} else {
    }
  } else if(($MatchRec["Result"]) == "h" ) {
    $LinkStr = "forfeited&nbsp;by&nbsp;";
    $HClass = "win";
    $AClass = "lose";
    if( $sfmt == $MatchRec["AwayTeamName"]) {
	} else {
	}
  } else if(($MatchRec["Result"]) == "A" ) {
    $HClass = "lose";
	$AClass = "win";
	$LinkStr = "lost&nbsp;to&nbsp;";
	if( $sfmt == $MatchRec["HomeTeamName"]) {
	} else {
	}
  } else if(($MatchRec["Result"]) == "a" ) {
    $HClass = "lose";
	$AClass = "win";
	$LinkStr = "forfeited&nbsp;to&nbsp;";
	if( $sfmt == $MatchRec["HomeTeamName"]) {
	} else {
	}
  } else if(strtoupper($MatchRec["Result"]) == "D" ) {
    $LinkStr = "drew with&nbsp;";
	$HClass = $AClass = "none";
  } else if($MatchRec["Result"] == "f" ) {
    $LinkStr = "multi forfeit&nbsp;";
	$HClass = $AClass = "none";
  } else if($MatchRec["Result"] == "W" ) {
    $LinkStr = "washout&nbsp;";
	$HClass = $AClass = "none";
  } else {
    $LinkStr = "vs";
	$HClass = $AClass = "none";
  }
  $HText = $MatchRec["HomeTeamName"];
  if($hiteam == $HText) {
    $HText = "<B>" . $HText . "</B>";
  }
  $AText = $MatchRec["AwayTeamName"];
  if( $hiteam == $AText) {
    $AText = "<B>" . $AText . "</B>";
  }
  if( ($sfmt == 'T') && ($MatchRec["HomeTeamRawScore"]) &&
      ((strtoupper($MatchRec["Result"]) == 'H') ||
	   (strtoupper($MatchRec["Result"]) == 'A'))) {
    $Addn = "&nbsp;&nbsp;".$MatchRec["HomeTeamRawScore"];
  } else {
    $Addn = $MatchRec["HomeGroundName"].", ".fmttime($MatchRec["Scheduled"], $lastdate );
  }
  if( ( ($sfmt == 'T') || ( $sfmt == 'B') ) && $MatchRec["Result"]) {
    if((strtoupper($MatchRec["Result"]) == 'H') ||
	   (strtoupper($MatchRec["Result"]) == 'A')) {
	  $HScore = $MatchRec["HomeTeamSupScore"];
	  $AScore = $MatchRec["AwayTeamSupScore"];
	}
    return ("      <TR class='match'><TD class='" . $HClass . "'>" . $HText . "</TD><TD>&#160;</TD><TD>" . $LinkStr . "</TD><TD class='" . $AClass . "'>" . $AText . "</TD><TD>" . $MatchRec["HomeTeamRawScore"] . " (game total: " . $MatchRec["HomeTeamScore"] . "-" . $MatchRec["AwayTeamScore"] . ")" . "</TD><TD/><TD>" . $Addn . "</TD></TR>\n");
  } else if (($sfmt == 'S') && $MatchRec["Result"]) {
    $HScore = $MatchRec["HomeTeamScore"];
	$AScore = $MatchRec["AwayTeamScore"];
  } else {
    $HScore = $MatchRec["HomeTeamRawScore"]."&nbsp;".$MatchRec["HomeTeamScore"];
	$AScore = $MatchRec["AwayTeamRawScore"]."&nbsp;".$MatchRec["AwayTeamScore"];
  }
  return ("      <TR class='match'><TD class='" . $HClass . "'>" . $HText . "</TD><TD>" . $HScore . "</TD><TD>" . $LinkStr . "</TD><TD class='" . $AClass . "'>" . $AText . "</TD><TD>" . $AScore . "</TD><TD/><TD>" . $Addn . "</TD></TR>\n");
}

function MakeMatch2( $MatchRec, $sfmt, $hiteam = null, $lastdate = null) {
  if( $MatchRec["AwayTeamKey"] == -1 ) {
	if( $hiteam ) {
	  return( "      <TR class='match'><TD>Bye</TD></TR>" );
    } else {
	  return( "      <TR class='match'><TD>" . $MatchRec["HomeTeamName"] . "</TD><TD/><TD>Bye</TD></TR>" );
	}
  }
  if(((strtoupper($MatchRec["Result"]) == "H" ) && !($MatchRec["ReverseHA"] )) ||
     ((strtoupper($MatchRec["Result"]) == "A" ) &&  ($MatchRec["ReverseHA"] ))) {
	$LinkStr = "defeated&nbsp;";
	$HClass = "win";
	$AClass = "lose";
  } else if(((strtoupper($MatchRec["Result"]) == "A" ) && !($MatchRec["ReverseHA"] )) ||
            ((strtoupper($MatchRec["Result"]) == "A" ) &&  ($MatchRec["ReverseHA"] ))) {
	$LinkStr = "lost&nbsp;to&nbsp;";
	$HClass = "lose";
	$AClass = "win";
  } else if(strtoupper($MatchRec["Result"]) == "D" ) {
    $LinkStr = "drew&nbsp;with&nbsp;";
	$HClass = $AClass = "none";
  } else {
    $LinkStr = "vs";
	$HClass = $AClass = "none";
  }
  if($MatchRec["ReverseHA"] ) {
    $AText = $MatchRec["HomeTeamName"];
	$HText = $MatchRec["AwayTeamName"];
  } else {
    $HText = $MatchRec["HomeTeamName"];
	$AText = $MatchRec["AwayTeamName"];
  }
  if( $sfmt == $HText) {
    $HText = b($HText);
  }
  if( $sfmt == $AText) {
    $AText = b($AText);
  }
  if( ($sfmt == 'T') && ($MatchRec["HomeTeamRawScore"])) {
    $Addn = $MatchRec["HomeTeamRawScore"];
  } else {
    $Addn = $MatchRec["HomeGroundName"].", ".fmttime($MatchRec["Scheduled"], $lastdate);
  }
  if((($sfmt == 'T') || ($sfmt == 'S')) && $MatchRec["Result"]) {
    if($MatchRec["ReverseHA"]) {
	  $AScore = $MatchRec["HomeTeamScore"];
	  $HScore = $MatchRec["AwayTeamScore"];
	} else {
	  $HScore = $MatchRec["HomeTeamScore"];
	  $AScore = $MatchRec["AwayTeamScore"];
	}
  } else {
    if($MatchRec["ReverseHA"]) {
	  $AScore = $MatchRec["HomeTeamRawScore"]." ".$MatchRec["HomeTeamScore"];
	  $HScore = $MatchRec["AwayTeamRawScore"]." ".$MatchRec["AwayTeamScore"];
	} else {
	  $HScore = $MatchRec["HomeTeamRawScore"]." ".$MatchRec["HomeTeamScore"];
	  $AScore = $MatchRec["AwayTeamRawScore"]." ".$MatchRec["AwayTeamScore"];
	}
  }
  return( "      <TR class='match'><TD class='" . $HClass . "'>" . $HText . "</TD><TD>" . $HScore . "</TD><TD>" . $LinkStr . "</TD><TD class='" . $AClass . "'>" . $AText . "</TD><TD>" . $AScore . "</TD><TD/><TD>" . $Addn . "</TD></TR>\n" );
}

//Sometimes home, sometimes away... bdb?  bdb remote?
function FinalTeami( $mysqli, $teamkey, $deriv, $rank, $chkey ) {
  //first, do we know the team key, $teamkey? If so, just get the name.
  if($teamkey > 0) {
    if($TName = GetTeamNamei( $mysqli, $teamkey )) {
	  return($TName);
	}
	return("Error!");
  }
  //OK, we don't know which team it's going to be yet.
  //Return the derivation
  if($deriv == 'F') {
    //Ladder position
	return("Ladder Position: ".$rank );
  }
  //Then again, it might be the winner or loser of another match.
  //ignore other championships for now
  //$MRec = new bdb;
  $query = ("SELECT DISTINCTROW RoundNumber, SeriesName, RSeriesNumber
  FROM FSeries
  WHERE ChampionshipKey = ".$chkey ."
  AND SeriesNumber = " . $rank );
  $MRecq = $mysqli->query( $query ) or die( $mysqli->error( ) .
			"<BR/>" . $query );
  if($MRec = $MRecq->fetch_array( ) ) {
    if($MRec["SeriesName"]) {
	  $MDescr = $MRec["SeriesName"];
	} else {
	  $MDescr = "Finals Round ".$MRec["RoundNumber"]."<BR/>Match ".$MRec["RSeriesNumber"];
	  //    $RDesc = new bdb;
	  $query = ("SELECT DISTINCTROW RoundName from FRound
	  where ChampionshipKey = ".$chkey."
	  AND RoundNumber = ".$MRec["RoundNumber"]);
	  $RDescq = $mysqli->query( $query ) or die( $mysqli->error( ) );
	  if($RDesc = $RDescq->fetch_array( ) ) {
	    if( $RDesc["RoundName"] ) {
		  $MDescr = $RDesc["RoundName"]."<BR/>Match ".$MRec["RSeriesNumber"];
		}
	  }
	}
  } else {
    $MDescr = "[Match not found]";
  }
  if($deriv == 'W') {
    return('Winner, '.$MDescr);
  }
  if($deriv == 'L') {
    return('Loser, '.$MDescr);
  }
  //and $chkey can be cross-championship
  return ("Error!");
}


function GetTeamNamei( $mysqli, $tkey ) {
  if(!$TeamHash[$tkey]) {
    //$TRec = new bdb;
    $query = "SELECT DISTINCTROW Team.TeamName
    FROM Team
    WHERE Team.TeamKey =".$tkey;
    $TRecq = $mysqli->query( $query ) or die( $mysqli->error( ) );
    if($TRec = $TRecq->fetch_array( ) ) {
	  $TeamHash[$tkey] = $TRec['TeamName'];
    } else {
	  return( undefined );
    }
  }
  return($TeamHash[$tkey]);
}

function fmttime( $scheduled, $lastdate ) {
  $tm = strtotime( $scheduled );
  $ld = strtotime( $lastdate );

  $tma = getdate( $tm );
  $lda = getdate( $ld );
  if( ( $tma["year"] == $lda["year"] ) &&
      ( $tma["mon"] == $lda["mon"] ) &&
	  ( $tma["mday"] == $lda["mday"] ) ) {
    return( date( "g:iA", $tm ) );
  }
  return( date( "g:iA", $tm ) . ", <B>" . date( "D, j M Y", $tm ) . "</B>" );
}

function ListFansi( $mysqli, $TeamNum, $user_id ) {
  $StData = "";
  $query = "select * from FBAccred where AccredRole = 3 and AccredKey = $TeamNum";
  $tmemq = $mysqli->query( $query ) or die( $mysqli->error( ) );
  $numfans = 0;
  $hasyou = 0;
  while( $tmemr = $tmemq->fetch_array( $tmemq ) ) {
	$numfans++;
	$StData .= "<table><tr><td><fb:profile-pic uid='" . $tmemr["FBID"] . "' linked='true'/></td><td valign='center'><fb:name uid='" . $tmemr["FBID"] . "'/></td></tr></table>";
	if( $tmemr["FBID"] == $user_id ) {
	  $hasyou = 1;
	}
  }
  if( !$numfans ) {
	$StData .= "(no fans)";
  }

  if( !$hasyou ) {
	$StData .= "<P/><P/><A HREF='' onclick='BecomeFan( $TeamNum, true ); return false;'>Become a fan</A>";
  } else {
	$StData .= "<P/><P/><A HREF='' onclick='BecomeFan( $TeamNum, false ); return false;'>Stop being a fan</A>";
  }
  return( $StData );
}

function preFbTab( ) {
  $retval = "";
  $retval .=  "<div fb_protected='true' class='fb_protected_wrapper'>";
  $retval .=   "<div class='tabs clearfix'>";
  $retval .=    "<center>";
  $retval .=     "<div class='left_tabs'>";
  $retval .=      "<ul class='toggle_tabs' id='toggle_tabs_unused'>";
  return( $retval );
}

function postFbTab( ) {
  $retval = "";
  $retval .=      "</ul>";
  $retval .=     "</div>";
  $retval .=    "</center>";
  $retval .=   "</div>";
  $retval .=  "</div>";
  return( $retval );
}

function fbStyles( ) {
$retval = "<style type='text/css'>";
$retvalX = "";

$retval .= ' body { ' .
' font-size: 11px; font-family: "lucida grande",tahoma,verdana,arial,sans-serif; color: #333; line-height: 1.28;  text-align: left; direction: ltr; } ';

$retval .= ' h1 { ' .
' display: block; ' .
' font-size: 2em; ' .
// ' -webkit-margin-before: 0.67em; ' .
// ' -webkit-margin-after: 0.67em; ' .
// ' -webkit-margin-start: 0.67em; ' .
// ' -webkit-margin-end: 0.67em; ' .
' font-weight: bold; ' .
' } ';

$retval .= ' h1, h2, h3, h4, h5, h6 { ' .
' font-size: 13px; ' .
' color: #333; ' .
' margin: 0; ' .
' padding: 0; ' .
' } ';

$retval .= ' h1 { ' .
' font-size: 14px; ' .
' } ';

$retval .= ' .clearfix::after { ' .
' clear: both; ' .
' content: "."; ' .
' display: block; ' .
' font-size: 0; ' .
' height: 0; ' .
' line-height: 0; ' .
' visibility: hidden; ' .
' } ';


$retval .= ' div { ' .
' display: block; ' .
' } ';

$retval .= ' .clearfix { ' .
// ' zoom: 1; ' .
' } ';

$retval .= ' .tabs { ' .
' padding: 0; ' .
' border-bottom: 1px solid #CCC; ' .
' } ';

$retval .= ' .tabs .left_tabs { ' .
' padding: 0; ' .
' border-bottom: 1px solid #CCC; ' .
' padding-left: 10px; ' .
' float: left; ' .
' } ';

$retval .= ' .toggle_tabs { ' .
' text-align: center; ' .
' margin-bottom: -1px; ' .
' } ';

$retval .= ' ul { ' .
' list-style-type: none; ' .
' margin: 0; ' .
' padding: 0; ' .
' } ';

$retval .= ' li { ' .
' display: list-item; ' .
//' text-align: -webkit-match-parent; ' .
' } ';

$retval .= ' .toggle_tabs li { ' .
' display: inline; ' .
' padding: 2px 0 3px; ' .
//' background: #F1F1F1 url(http://static.ak.fbcdn.net/rsrc.php/v2/ys/r/YoX0fw76s5z.gif) top left repeat-x; ' .
' } ';

$retval .= ' a { ' .
' cursor: pointer; ' .
' color: #3B5998; ' .
' text-decoration: none; ' .
' } ';

$retval .= ' .toggle_tabs li a { ' .
' border: 1px solid #898989; ' .
' border-left: 0; ' .
' color: #333; ' .
' font-weight: bold; ' .
' padding: 2px 8px 3px 9px; ' .
' display: inline-block; ' .
' } ';

$retval .= ' .toggle_tabs li.first a { ' .
' border: 1px solid #898989; ' .
' } ';

$retval .= ' .toggle_tabs li a.selected { ' .
' margin-left: -1px; ' .
' background: #6D84B4; ' .
' border: 1px solid #3B5998; ' .
' border-left: 1px solid #5973A9; ' .
' border-right: 1px solid #5973A9; ' .
' color: white; ' .
' } ';

$retval .= ' td, td.label { ' .
' font-size: 11px; ' .
' text-align: left; ' .
' } ';

$retval .= ' td, th { ' .
' display: table-cell;  vertical-align: inherit; ' .
' } ';

$retval .= "</style>\n";

return $retval;
}

function addFbTab( $text, $url, $class, $sel = null) {
  $retval = "";
  $retval .=       "<li class='" . $class . "' >";
  //$retval .=        "<a href='http://apps.facebook.com/scoretank/" . $url . "' class" . ( $sel ? "='selected' " : "" ) . " onclick='return true;' onmousedown>" . $text . "</a>";
  $retval .=        "<a href='" . $url . "' class" . ( $sel ? "='selected' " : "" ) . " onclick='return true;' onmousedown>" . $text . "</a>";
  $retval .=       "</li>";
  return( $retval );
}

function addPreIframe( $title ) {
  $retval = "";
  $retval .= "<!DOCTYPE html>\n";
  $retval .= '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">' . "\n";
  $retval .= "<head>";
  $retval .=  '<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>' . "\n";
  $retval .=  "<title>" . $title . "</title>\n";
  $retval .= "</head>\n";
  $retval .= "<body>\n";
  return( $retval );
}

function addBodyScript( ) {
  $retval = "";
  $retval .= '<script type="text/javascript" src="https://connect.facebook.net/en_US/all.js"></script>' . "\n";
  $retval .= '<script type="text/javascript">' . "\n";
  $retval .= 'FB.init({' . "\n";
  //$retval .=   "appId  : '<" . "?=\$fbconfig['appid']?" . ">',\n";
  //$retval .=   "appId  : '<" . "?=\$fbconfig['appid']?" . ">',\n";
  $retval .=   "status : true,\n";
  $retval .=   "cookie : true,\n";
  $retval .=   "xfbml  : true\n";
  $retval .= "});\n";
  $retval .= "</script>\n";
  return( $retval );
}

function addPostIframe( ) {
  $retval = "";
  $retval .= "</body>\n";
  $retval .= "</html>\n";
  return( $retval );
}

function XXmyserror( $doc, $root, $query, $str, $mysqli ) {
  if( $query ) {
    $node = $doc->createElement( 'query', $query );
	$root->appendChild( $node );
  }
  $node = $doc->createElement( 'str', $str );
  $root->appendChild( $node );
  if( $mysqli ) {
	$node = $doc->createElement( 'error', $mysqli->error );
	$root->appendChild( $node );
  }
  return( $doc->saveXML( ) );
}

function GenRefQuery( $query, $stdb, $doc, $root ) {
if( 1 ) {
  $t1 = $stdb->query( $query );
  if( !$t1 ) {
	if( !$stdb ) {
	  die( "Error" );
	}
    die( myserror( $doc, $root, $query, "gr", $stdb ) ); 
  }
  if( $t1->fetch_assoc( ) ) {
	return( false );
  }
} else {
  echo( "<a>" . $query . "</a>" );
}
  return( true ); // OK - no clashes found
}

function GenRefQueries( $r1, $r2, $tbl, $col1, $col2, $stdb, $doc, $root ) {
  if( !GenRefQuery( "select * from $tbl " .
	" where ( $col1 = $r1 ) " .
	" or ( $col1 = $r2 ) " .
	( $col2 ? " or ( $col2 = $r1 ) " .
	" or ( $col2 = $r2 ) " : "" ), $stdb, $doc, $root ) ) {
	  return ( false );
  }
  return( true );
}

function GenRef( $stdb, $doc, $root ) {
  while( true ) {
    $r1 = mt_rand( 1, 2147483640 );
	$r2 = mt_rand( 1, 2147483640 );

	if( GenRefQueries( $r1, $r2, "NMatch", "MatchRef", "MatchRef2", $stdb, $doc, $root ) &&
		GenRefQueries( $r1, $r2, "MatchHist", "MatchRef", "MatchRef2", $stdb, $doc, $root ) &&
		GenRefQueries( $r1, $r2, "FMatch", "MatchRef", "MatchRef2", $stdb, $doc, $root ) &&
		GenRefQueries( $r1, $r2, "FMatchHist", "MatchRef", "MatchRef2", $stdb, $doc, $root ) &&
		GenRefQueries( $r1, $r2, "Round", "RoundRef", "", $stdb, $doc, $root ) &&
		GenRefQueries( $r1, $r2, "RoundHist", "RoundRef", "", $stdb, $doc, $root ) &&
		GenRefQueries( $r1, $r2, "FRound", "RoundRef", "", $stdb, $doc, $root ) &&
		GenRefQueries( $r1, $r2, "FRoundHist", "RoundRef", "", $stdb, $doc, $root ) &&
		GenRefQueries( $r1, $r2, "FSeries", "SeriesRef", "", $stdb, $doc, $root ) &&
		GenRefQueries( $r1, $r2, "FSeriesHist", "SeriesRef", "", $stdb, $doc, $root ) ) {
	  $ret = array( $r1, $r2 );
	  return( $ret );
	}
  }
}

?>
