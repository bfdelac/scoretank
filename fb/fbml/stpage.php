<?php

function xmlentities( $string ) {
	return str_replace( array ( '&', '"', "'", '<', '>', '\\' ),
	                    array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' , '\\\\' ), $string );
}

function LaddDBCol( $colkey ) {
    return LaddCol( $colkey, 1 );
}

function LaddCol( $c, $db ) {
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

function stconnect( ) {
  mysql_connect( "", "scoretank", "juOdfK" );
  mysql_select_db( "stdb" );
}

function old_fbconnect( ) {
  $appapikey = '103424ce8ec89f93620faeb04713764c';
  $appsecret = '6a1cc9469dec99e4e067876e56dbd473';
  $facebook = new Facebook($appapikey, $appsecret);
  $user_id = $facebook->require_login();
  return( $user_id );
}

function fbconnect( ) {
  $facebook = new Facebook(array(
    'appId'  => '103424ce8ec89f93620faeb04713764c',
    'secret' => '6a1cc9469dec99e4e067876e56dbd473',
  ));

  // See if there is a user from a cookie
  $user = $facebook->getUser();

  if ($user) {
	try {
	  // Proceed knowing you have a logged in user who's authenticated.
	  $user_profile = $facebook->api('/me');
	} catch (FacebookApiException $e) {
	  echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
	  $user = null;
	}
  }
  return( $user_profile["id"] );
}

function MakeFNameHead( $fname ) {
  return "      <TR><TD colspan='5' align='center' class='fnamehead'><B>" . $fname . "</B></TD></TR>\n";
}

function MakeMatchHead( $mh ) {
  return "      <TR><TD colspan='5' class='matchhead'><B>".$mh."</B></TD></TR>\n";
}


//parameters: 0- $MatchRec, 1- Score format, 2- Highlight teamname, 3- Lastdate
function MakeMatch( $MatchRec, $sfmt, $hiteam, $lastdate ) {
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

function MakeMatch2( $MatchRec, $sfmt, $hiteam, $lastdate ) {
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
function FinalTeam( $teamkey, $deriv, $rank, $chkey ) {
  //first, do we know the team key, $teamkey? If so, just get the name.
  if($teamkey > 0) {
    if($TName = GetTeamName($teamkey)) {
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
  $MRecq = mysql_query( $query ) or die( mysql_error( ) . "<BR/>" . $query );
  if($MRec = mysql_fetch_array( $MRecq, MYSQL_ASSOC ) ) {
    if($MRec["SeriesName"]) {
	  $MDescr = $MRec["SeriesName"];
	} else {
	  $MDescr = "Finals Round ".$MRec["RoundNumber"]."<BR/>Match ".$MRec["RSeriesNumber"];
	  //    $RDesc = new bdb;
	  $query = ("SELECT DISTINCTROW RoundName from FRound
	  where ChampionshipKey = ".$chkey."
	  AND RoundNumber = ".$MRec["RoundNumber"]);
	  $RDescq = mysql_query( $query ) or die( mysql_error( ) );
	  if($RDesc = mysql_fetch_array( $RDescq, MYSQL_ASSOC ) ) {
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


function GetTeamName( $tkey ) {
  if(!$TeamHash[$tkey]) {
    //$TRec = new bdb;
    $query = "SELECT DISTINCTROW Team.TeamName
    FROM Team
    WHERE Team.TeamKey =".$tkey;
    $TRecq = mysql_query( $query ) or die( mysql_error( ) );
	if($TRec = mysql_fetch_array( $TRecq ) ) {
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

function ListFans( $TeamNum, $user_id ) {
  $StData = "";
  $query = "select * from FBAccred where AccredRole = 3 and AccredKey = $TeamNum";
  $tmemq = mysql_query( $query ) or die( mysql_error( ) );
  $numfans = 0;
  $hasyou = 0;
  while( $tmemr = mysql_fetch_array( $tmemq ) ) {
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

$retval .= ' body { ' .
' font-size: 11px; font-family: "lucida grande",tahoma,verdana,arial,sans-serif; color: #333; line-height: 1.28;  text-align: left; direction: ltr; } ';

$retval .= ' h1 { ' .
' display: block; ' .
' font-size: 2em; ' .
' -webkit-margin-before: 0.67em; ' .
' -webkit-margin-after: 0.67em; ' .
' -webkit-margin-start: 0.67em; ' .
' -webkit-margin-end: 0.67em; ' .
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
' zoom: 1; ' .
' } ';

$retval .= ' . tabs { ' .
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
' text-align: -webkit-match-parent; ' .
' } ';

$retval .= ' .toggle_tabs li { ' .
' display: inline; ' .
' padding: 2px 0 3px; ' .
' background: #F1F1F1 url(http://static.ak.fbcdn.net/rsrc.php/v2/ys/r/YoX0fw76s5z.gif) top left repeat-x; ' .
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

function addFbTab( $text, $url, $class, $sel ) {
  $retval = "";
  $retval .=       "<li class='" . $class . "' >";
  $retval .=        "<a href='" . $url . "' class" . ( $sel ? "='selected' " : "" ) . " onclick='return true;' onmousedown>" . $text . "</a>";
  $retval .=       "</li>";
  return( $retval );
}

?>

