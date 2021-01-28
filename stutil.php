<?php

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

function sticonnect( ) {
    return(
      mysqli_connect( "", stdb_username( ), stdb_password( ), stdb_dbname( ) ) ); 
}
  
  

?>
