
function initialiseMatchDialog( ) {
	console.log("pophtml");
	$( '#matchDialog' ).html(
		"<table border='0'>" +
		  "<tr><td></td></tr>" +
		  "<tr><td width='12%'></td><td>Home team:</td><td align='left' id='dlgHomeTd'></td></tr>" +
		  "<tr><td></td><td align='right'>Away team:</td><td align='left' id='dlgAwayTd' class='ateamtd'></td></tr>" +
		  "<tr><td></td><td align='right'>Venue:</td><td align='left' id='dlgVenueTd' class='venuetd'></td></tr>" +
		  "<tr><td></td><td align='right'>Scheduled:</td><td align='left' id='dlgSchedTd'><input type='text' id='dlgMatchdate'/><input type='text' size='6' MaxLength='6' id='dlgMatchtime'/></td></tr>" +
		"</table>" );
	console.log("matchdate");
	$( "#dlgMatchdate" ).datepicker( {
    	dateFormat: "D, d M yy"
  	} );
	console.log("dialog");

	$( '#matchDialog' ).dialog( {
		autoOpen: false,
		modal: true,
		width: 600,
		height: 350,
		buttons: {
			'Ok': function( ) {
				console.log("matchDialog Ok fired");
if(false) {
				if(!jQuery('#matchDialog').dialog('isOpen')) {
					return;
				}
		
				var hkey = 0;
				var akey = 0;
				var hd = '';
				var hdr = 0;
				var ad = '';
				var adr = 0;
				var isfinal = ( jQuery( "#dlgHomeTd .dlgTeamSelSpan" ).css( "display" ) == 'none' );
				if( isfinal ) {
				  hd = jQuery( '#dlgHomeTd .dlgTeamDerivSelSpan select' ).val( );
				  if( hd == 'F' ) {
					hdr = jQuery( '#dlgHomeTd .dlgTeamDerivSelRank .LadderPosSel' ).val( );
				  } else {
					hdr = jQuery( '#dlgHomeTd .dlgTeamDerivSelRank .WinLose' ).val( );
				  }
				  ad = jQuery( '#dlgAwayTd .dlgTeamDerivSelSpan select' ).val( );
				  if( ad == 'F' ) {
					adr = jQuery( '#dlgAwayTd .dlgTeamDerivSelRank .LadderPosSel' ).val( );
				  } else {
					adr = jQuery( '#dlgAwayTd .dlgTeamDerivSelRank .WinLose' ).val( );
				  }
				  if( hd == '' ) {
					window.alert( "Please select a Home formula" );
					jQuery( '#dlgHomeTd .dlgTeamDerivSelSpan select' ).focus( );
					return;
				  }
				  if( ad == '' ) {
					window.alert( "Please select an Away formula" );
					jQuery( '#dlgAwayTd .dlgTeamDerivSelSpan select' ).focus( );
					return;
				  }
				  if( ( hdr == '' ) || ( hdr < 0 ) ) {
					window.alert( "Please select a Home formula team source" );
					jQuery( '#dlgHomeTd .dlgTeamDerivSelRank select' ).focus( );
					return;
				  }
				  if( ( adr == '' ) || ( adr < 0 ) ) {
					window.alert( "Please select an Away formula team source" );
					jQuery( '#dlgAwayTd .dlgTeamDerivSelRank select' ).focus( );
					return;
				  }
				  if( ( hd == ad ) && ( hdr == adr ) ) {
					  console.log("HA formulae");
					window.alert( "Home and Away formulae must be different" );
					return;
				  }
				} else {
				  hkey = jQuery( '#dlgHomeTd .dlgTeamSelSpan select' ).val( );
				  akey = jQuery( '#dlgAwayTd .dlgTeamSelSpan select' ).val( );
				  if( hkey <= 0 ) {
					window.alert( "Please select a Home team" );
					jQuery( '#dlgHomeTd .dlgTeamSelSpan select' ).focus( );
					return;
				  }
				  if( akey < 0 ) {
					window.alert( "Please select an Away team" );
					jQuery( '#dlgAwayTd .dlgTeamSelSpan select' ).focus( );
					return;
				  }
				  if( hkey == akey ) {
					console.log("HA teams");
					window.alert( "Home and Away teams must be different" );
					return;
				  }
				}
				$venue = -1;
				if( ( !isfinal ) && ( akey == 0 ) ) {
				  akey = -1; //BYE special values
				} else {
				  $venue = jQuery( "#dlgVenueTd :selected" ).val( );
				  if( $venue == "" ) {
					window.alert( "Please select the venue" );
					jQuery( '#dlgVenueTd select' ).focus( );
					return;
				  }
				}
		
				if( jQuery( "#dlgMatchdate" ).val( ) == "" ) {
				  window.alert( "Please select the match date" );
				  jQuery( "#dlgMatchdate" ).datepicker( "show" );
				}
				if( jQuery( "#dlgMatchtime" ) == "" ) {
				  window.alert( "Please select the match time" );
				  return;
				}
				var sched = jQuery.datepicker.formatDate( "yy-mm-dd", jQuery( "#dlgMatchdate" ).datepicker( 'getDate' ) ) + " " + jQuery( "#dlgMatchtime" ).val( );
		
		//window.alert( "Posting" );
				jQuery.post( jQuery("#scriptprefix").html() + "/xstlib.php", {
					function: "UpdateMatch",
					hkey: hkey,
					akey: akey,
					Venue: $venue,
					Sched: sched,
					mnum: document.matchnum,
					rnum: document.roundnum,
					chkey: jQuery( '#ChampSel' ).val( ),
					hd: hd,
					hdr: hdr,
					ad: ad,
					adr: adr
				  },
				  function( xData ) {
						jQuery( '#matchDialog' ).dialog( 'close' );
					jQuery( "#ChampFixture" ).getTransform( jQuery("#scriptprefix").html() + "/xfixture.xsl",
			jQuery("#scriptprefix").html() + "/xteam.php?editmatch=1&fixt=" + jQuery( '#ChampSel' ).val( ) );
					jQuery.get( jQuery("#scriptprefix").html() + "/xteam.php?showvenues=1&champ=" + jQuery( '#ChampSel' ).val( ),
					  function( xData ) {
					setupInlineMatch( xData );
					  } );
				  },
				  "xml"
				);
}
			},
			'Cancel': function( ) {
				console.log("matchDialog Close fired");
				jQuery( '#matchDialog' ).dialog( 'close' );
				console.log("matchDialog Close finished");
			}
		}
	} );
}

function initialiseChampDialog() {
	$( '#champDialog' ).html(
		"<table border='0'>" +
			"<tr><td></td></tr>" +
			"<tr><td width='10%'></td><td>New&nbsp;season&nbsp;name:</td><td id='seasonTd' style='text-align:left;'><input type='text' maxlength='80' style='width:20em;' id='NewSeasonName'></input></td></tr>" +
			"<tr><td></td><td>Roll over teams?</td><td id='seasonTd' style='text-align:left;'><input type='checkbox' id='ROteams'></input></td></tr>" +
			"<tr><td></td><td>Season start date:<br/>(only if rolling over matches)</td><td id='SeasonStartTd' style='text-align:left;'><input type='text' id='SStartDt'></input></td></tr>" +
		"</table>" );
	$( "#SStartDt" ).datepicker( {
		dateFormat: "D, d M yy"
	} );
	$( '#champDialog' ).dialog( {
		autoOpen: false,
		modal: true,
		width: 600,
		height: 400,
		buttons: {
			"Ok": function( ) {
				var SeasonName = jQuery.trim( $( '#NewSeasonName' ).val( ) );
				if( SeasonName.length <= 0 ) {
					window.alert( "Please enter a name for the new season" );
					return;
				}
				var StartDt = $( '#SStartDt' ).val( );
				if( StartDt.length > 0 ) {
					StartDt = jQuery.datepicker.formatDate( "yy-mm-dd", $( "#SStartDt" ).datepicker( 'getDate' ) );
				}
				var ROTeam = $( "#ROteams:checked" ).length;
				jQuery.post( $("#scriptprefix").html() + "/xstlib.php", {
						function: "RollOver",
						rochampkey: $( '#ChampSel' ).val( ),
						season: SeasonName,
						startdt: StartDt,
						roteams: ROTeam
					},
					function( xData ) {
						window.alert( "Championship rolled over" );
						def = xData.documentElement.getElementsByTagName( "ro" );
						defch = -1;
						if( def.length ) {
							defch = def[0].getAttribute( "ChKey" );
						}
						$( '#champDialog' ).dialog( 'close' );

						loadPage(defch);
						//$( '#adminpage' ).getTransform( $("#scriptprefix").html() + "/xstlib.xsl", $("#scriptprefix").html() + "/xstlib.php?listchamps=1&default=" + defch );
						//			$( "#ChampFixture" ).getTransform( "/xfixture.xsl", "/xteam.php?editmatch=1&fixt=" + $( '#ChampSel' ).val( ) );
						changeChamp( $( '#ChampSel' ) );
					},
					"xml"
				);
			},
			"Cancel": function( ) {
				$( '#champDialog' ).dialog( 'close' );
			}
		}
	});
}

function initialiseTeamDialog( ) {
	jQuery( '#teamDialog' ).html(
		"<table border='0'>" +
		 "<tr><td></td></tr>" +
		 "<tr><td width='10%'>&nbsp;</td><td style='text-align:right;'>Team&nbsp;Name</td><td id='teamNameTd' style='text-align:left;'><input type='text' maxlength='80' style='width:20em;' id='teamName'></input></td></tr>" +
		 "<tr><td>&nbsp;</td><td style='text-align:right;'>Team&nbsp;Home&nbsp;Ground</td><td style='text-align:left;' id='teamHGtd'><input type='hidden' id='teamGroundKey'></input><input type='hidden' id='teamGround'>207</input><button id='selTeam'>&#9660;</button></td></tr>" +
			  //teamGroundKey: $( "#teamGround" ).val( ),
		 "<tr><td>&nbsp;</td><td style='text-align:right;'>Home&nbsp;Ground&nbsp;Address</td><td style='text-align:left;'><textarea id='groundAddr' readonly='readonly'></textarea><input type='hidden' id='groundAddrIH></input></td></tr>" +
		"</table>" );
  
	jQuery( "#teamDialog" ).dialog( {
	  autoOpen: false,
	  modal: true,
	  width: 600,
	  height: 400,
	  buttons: {
		"Ok": function( ) {
		  var teamName = jQuery.trim( jQuery( '#teamName' ).val( ) );
		  if( teamName.length <= 0 ) {
			window.alert( "Please enter a name for the new team" );
			return;
		  }
				  var hg = jQuery( "#teamHGtd select option:selected" ).val( );
				  if( ( hg == "" ) || ( hg < 0 ) ) {
			window.alert( "Please select a home ground" );
			return;
		  }
		  jQuery.post( jQuery("#scriptprefix").html() + "/xteam.php", {
			  function: "AddTeam",
			  ChKey: jQuery( '#ChampSel' ).val( ),
			  x: "xxx",
			  teamGroundKey: hg,
			  teamName: teamName,
	  //		teamGroundKey: $( "#teamGround" ).val( ),
	  //		teamGroundKey: $( "#teamGroundKey" ).val( )
			  groundAddr: jQuery( "#groundAddr" ).val( )
			},
			function( xData ) {
			  var jData = jQuery( xData );
			  //window.alert( "returned: " + $.xsl.serialize( xData ) );
			  if( jData.find( "newTeam" ) ) {
				jQuery( "#" + document.changingTeam ).getTransform( jQuery("#scriptprefix").html() + "/xteam.xsl", xData );
				jData.find( "newTeam" ).each( function( ) {
				  jQuery( "#" + document.changingTeam + " select" ).val( jQuery( this ).attr( "key" ) );
				} );
				jQuery( "#dlgMatchdate" ).show( );
					jQuery( "#dlgMatchtime" ).show( );
				jQuery( "#teamDialog" ).dialog( 'close' );
			  } else {
				window.alert( xData.xml );
			  }
			},
			"xml"
		  );
		},
		"Cancel": function( ) {
		  jQuery( "#teamDialog" ).dialog( 'close' );
		}
	  }
	} );  
}

function initialiseCompAdmin() {
	console.log("initialiseCompAdmin");
	initialiseMatchDialog( );
	initialiseChampDialog( );
	initialiseTeamDialog( );
};

function changeTeam( selCtrl ) {
//window.alert( "ph: " + $(selCtrl).parent( ).parent( ).parent( ).html( ) );
  if( jQuery(selCtrl).parents( ).hasClass( "ateamtd" ) ) {
    jQuery( "#dlgMatchdate" ).show( );
    jQuery( "#dlgMatchtime" ).show( );
    var htmlblock = jQuery(selCtrl).parent( ).parent( ).parent( ).parent( ).parent( );
	if( selCtrl.options[selCtrl.selectedIndex].value == 0 ) {
	// if it's a Bye, set the venue to disabled
//window.alert( "hh: " + $( htmlblock ).html( ) );
	  jQuery( htmlblock ).find( ".venuetd select" ).attr( 'disabled', 'disabled' );
	  jQuery( htmlblock ).find( "#dlgMatchdate" ).hide( );
	  jQuery( htmlblock ).find( "#dlgMatchtime" ).hide( );
	  return;
	}
	jQuery( htmlblock ).find( ".venuetd select" ).removeAttr( 'disabled' );
  }
//window.alert( "selsel: " + selCtrl.selectedIndex );
  if( selCtrl.options[selCtrl.selectedIndex].value != -1 ) {
    return;
  }
  // Create a new Team
  jQuery( "#teamDialog input" ).val( "" );
  jQuery( "#teamDialog textarea" ).val( "" );
  var selPar = selCtrl.parentNode;
  document.changingTeam = selPar.id;
  while( selPar && document.changingTeam == "" ) {
    selPar = selPar.parentNode;
    document.changingTeam = selPar.id;
  }
  jQuery.get( jQuery("#scriptprefix").html() + "/xteam.php?showvenues=1&champ=" + jQuery( '#ChampSel' ).val( ),
      function( venData, textStatus, jqXHR ) {
        //initFixt( data );xData
        document.venData = venData;
	jQuery( "#teamDialog #teamHGtd" ).getTransform( jQuery("#scriptprefix").html() + "/xvenue.xsl", document.venData );
	jQuery( "#teamDialog" ).dialog( 'open' );
      } );
}

function changeChamp( selCtrl ) {
	var selVal = $( '#ChampSel' ).val( );
	if( selVal > 0 ) {
		$( '#rollOver' ).removeAttr( 'disabled' );
	} else {
		$( '#rollOver' ).attr( 'disabled', 'disabled' );
	}
	$.get( $("#scriptprefix").html() + "/xteam.php?editmatch=1&fixt=" + $( '#ChampSel' ).val( ),
		function( data, textStatus, jqXHR ) {
			initFixt( data );
		} );
}

function changeTeamDeriv( selObj ) {
  //window.alert( $( selObj ).val( ) );
  // W, L, F
  var selVal = jQuery( selObj ).val( );
  if( selVal == 'F' ) {
	//$( "#dlgHomeTd" ).getTransform( "/xteam.xsl", xData );
    //$( '#dlgTeamDerivSelRank' ).getTransform( "", "/xteam.php?champ=" . $( '#ChampSel' ).val( ) );
    jQuery( selObj ).parent( ).parent( ).find( ".LadderPosSel" ).show( );
    jQuery( selObj ).parent( ).parent( ).find( ".WinLose" ).hide( );
  } else {
    jQuery( selObj ).parent( ).parent( ).find( ".LadderPosSel" ).hide( );
    jQuery( selObj ).parent( ).parent( ).find( ".WinLose" ).show( );
  }
}

function EditMatch( roundnum, matchnum, hkey, akey, vkey, sched, hd, hdr, ad, adr ) {
//window.alert( "EM: r = " + roundnum + ", m = " + matchnum + ", akey = " + akey + ", v = " + vkey + ", sched = " + sched + ", hd = " + hd );
  jQuery( "#dlgMatchdate" ).show( );
  jQuery( "#dlgMatchtime" ).show( );
  jQuery( ".venuetd select" ).removeAttr( 'disabled' );
  if( hd ) {
//window.alert( "has hd" );
    jQuery( ".dlgTeamSelSpan" ).hide( );
    //$( "#dlgHomeTd .dlgTeamSelSpan" ).hide( );
	jQuery( ".dlgTeamDerivSelSpan select" ).html( '<option/><option value="F">Ladder Pos:</option>' );
	if( roundnum > 1 ) {
	  jQuery( ".dlgTeamDerivSelSpan select" ).append( '<option value="W">Winner match:</option>' );
	  jQuery( ".dlgTeamDerivSelSpan select" ).append( '<option value="L">Loser match:</option>' );
	}
    jQuery( ".dlgTeamDerivSelSpan" ).show( );
	// SELECT OPTIONS FROM PREVIOUS FINAL ROUNDS
	jQuery( ".dlgTeamDerivSelRank .WinLose" ).html( '<option/>' );
	// for each final that's in a round prior to this one...
	var jData = jQuery( document.xdata );
	var frnd = 1;
	for( frnd = 1; frnd < roundnum; frnd++ ) {
	  jData.find( 'fround[r = ' + frnd + ']' ).each( function( i, j ) {
		jQuery( this ).find( 'fseries' ).each( function( k, l ) {
		  jQuery( ".dlgTeamDerivSelRank .WinLose" ).append( jQuery( '<option>', { value: jQuery( this ).attr( "sno" ), text: jQuery( this ).find( 'sername' ).text( ) } ) );
		} );
	  } );
	}

    jQuery( ".dlgTeamDerivSelRank" ).show( );
    //$( "#dlgHomeTd .dlgTeamDerivSelSpan" ).show( );
    //$( "#dlgHomeTd .dlgTeamDerivSelRank" ).show( );
    //$( "#dlgAwayTd .dlgTeamSelSpan" ).hide( );
    //$( "#dlgAwayTd .dlgTeamDerivSelSpan" ).show( );
    //$( "#dlgAwayTd .dlgTeamDerivSelRank" ).show( );
	jQuery( "#dlgHomeTd .dlgTeamDerivSelSpan select" ).val( hd );
	jQuery( "#dlgHomeTd .dlgTeamDerivSelSpan select" ).each( function( index, element ) { changeTeamDeriv( this ) } );
	if( hd == 'F' ) {
	  jQuery( "#dlgHomeTd .LadderPosSel" ).val( hdr );
	} else {
	  jQuery( "#dlgHomeTd .WinLose" ).val( hdr );
	}
	jQuery( "#dlgAwayTd .dlgTeamDerivSelSpan select" ).val( ad );
	jQuery( "#dlgAwayTd .dlgTeamDerivSelSpan select" ).each( function( index, element ) { changeTeamDeriv( this ) } );
	if( ad == 'F' ) {
	  jQuery( "#dlgAwayTd .LadderPosSel" ).val( adr );
	} else {
	  jQuery( "#dlgAwayTd .WinLose" ).val( adr );
	}
  } else {
    jQuery( ".dlgTeamSelSpan" ).show( );
    jQuery( ".dlgTeamDerivSelSpan" ).hide( );
    jQuery( ".dlgTeamDerivSelRank" ).hide( );
    //$( "#dlgHomeTd .dlgTeamSelSpan" ).show( );
    //$( "#dlgHomeTd .dlgTeamDerivSelSpan" ).hide( );
    //$( "#dlgHomeTd .dlgTeamDerivSelRank" ).hide( );
    //$( "#dlgAwayTd .dlgTeamSelSpan" ).show( );
    //$( "#dlgAwayTd .dlgTeamDerivSelSpan" ).hide( );
    //$( "#dlgAwayTd .dlgTeamDerivSelRank" ).hide( );
  }
//window.alert( "HC0: " + $( "#dlgHomeTd" ).size( ) );
//window.alert( "HC1: " + $( "#dlgHomeTd .dlgTeamSelSpan" ).size( ) );
  if( hkey > 0 ) {
    jQuery( "#dlgHomeTd .dlgTeamSelSpan" ).find( "select" ).val( hkey );
  }
  if( ( akey < 0 ) && ( !ad ) ) {
    akey = 0;	// if an entered match has an akey of -1, it means a bye;
				// which is option 0.
    // ... or it could be a derived final
  }
  if( akey >= 0 ) {
    jQuery( "#dlgAwayTd .dlgTeamSelSpan" ).find( "select" ).val( akey );
    changeTeam( jQuery( "#dlgAwayTd select" )[0] );	// [0] turns it into DOM
  }
  jQuery( "#dlgVenueTd select" ).val( vkey );
  var tmpDate = new Date( sched );
  jQuery( "#dlgMatchdate" ).datepicker( 'setDate', new Date( tmpDate.getFullYear( ), tmpDate.getMonth( ), tmpDate.getDate( ) ) );
  var hh = tmpDate.getHours( );
  if( hh <= 9 ) {
    hh = new String( "0" + hh );
  }
  var mm = tmpDate.getMinutes( );
  if( mm <= 9 ) {
    mm = new String( "0" + mm );
  }
  jQuery( "#dlgMatchtime" ).val( hh + ":" + mm );
  document.roundnum = roundnum;
  document.matchnum = matchnum;
  jQuery( "#matchDialog" ).dialog( 'open' );
}

function rollOver( ) {
  jQuery( "#champDialog" ).dialog( 'open' );
}

function SendMatchResLibX( ) {
  window.alert("LibX");
}

window.XXfbAsyncInit = function( ) {
  FB.init( { appId: '',
			 cookie: true,
			 status: true,
			 xfbml: true,
			 oauth : true
		   } );
  FB.login( function( response ) {
    if( response.authResponse ) {
	  window.alert( "Successfully logged in" );
	  // user successfully logged in
	  loadPage( );
	} else {
	  // user cancelled login
	  window.alert( "You must be logged in" );
	}
  });
}

function setupInlineMatch( xData ) {
	console.log("sIF1");
	var sp = $("#scriptprefix").html();

	$.get(sp + "/xteam.xsl", function(teamXsl) {
		$( "#dlgHomeTd" ).getTransform(teamXsl, xData);
		$( "#dlgAwayTd" ).getTransform(teamXsl, xData);
		$( "#dlgAwayTd .dlgTeamSelSpan" ).find( "select" ).append( $( '<option>', { value: 0, text: 'Bye' } ) );

		$.get(sp + "/xvenue.xsl", function(venueXsl) {
			$( "#dlgVenueTd" ).getTransform(venueXsl, xData);
			$( "#venue" ).getTransform(venueXsl, xData);
		});
		$( "#hteam" ).getTransform(teamXsl, xData);
		$( "#ateam" ).getTransform(teamXsl, xData);
		$( "#ateam select" ).append( $( '<option>', { value: 0, text: 'Bye' } ) );
	});

	$( "#matchdate" ).datepicker( {
		dateFormat: "D, d M yy"
	} );
	if( $( ".displayrow" ).length > 0 ) {
		$( "#roundnum" ).val( $( ".roundnum:last" ).text( ) );
		$( "#matchnum" ).val( Number( $( ".matchnum:last" ).text( ) ) + 1 );
		//window.alert( new Date( $( ".textdate:last" ).text( ) ) );
		$( "#matchdate" ).datepicker( 'setDate', new Date( $( ".textdate:last" ).text( ) ) );
		$( "#matchtime" ).val( $( ".texttime:last" ).text( ) );
	} else {
		$( "#roundnum" ).val( 1 );
		$( "#matchnum" ).val( 1 );
	}
	console.log("sIF9");
}

function initFixt( xdata ) {
	document.xdata = xdata;

	$.get($("#scriptprefix").html() + "/xfixture.xsl", function(fixtXsl) {
		$( "#ChampFixture" ).getTransform(fixtXsl, $.xsl.serialize(xdata));
	});

	$.get( $("#scriptprefix").html() + "/xteam.php?showvenues=1&champ=" + $( '#ChampSel' ).val( ),
			function( xData ) {
				setupInlineMatch( xData );
		} );
}

function loadPage(defch = -1) {
	$( '#adminpage' ).text( "Now Loading..." );
	$.get($("#scriptprefix").html() + "/xstlib.xsl", function(compsxsl) {
			var defstr = "";
			if(defch > 0) {
				defstr = "&default=" + defch;
			}
			$.get($("#scriptprefix").html() + "/xstlib.php?listchamps=1" + defstr, function(compsxml) {
					$('#adminpage').getTransform(compsxsl, compsxml);
			});
		}, "xml");
}

function newRound( ) {
  jQuery( "#roundnum" ).val( Number( jQuery( ".roundnum:last" ).text( ) ) + 1 );
  jQuery( "#matchnum" ).val( 1 );

  var tmpDate = new Date( jQuery( ".textdate:last" ).text( ) );
  var tmp7Date = new Date( tmpDate.getFullYear( ), tmpDate.getMonth( ), tmpDate.getDate( ) + 7 );
  jQuery( "#matchdate" ).datepicker( 'setDate', tmp7Date );
  jQuery( "#matchtime" ).val( jQuery( ".texttime:last" ).text( ) );
}

function changeVenue( selCtrl ) {
  var selVal = selCtrl.options[selCtrl.selectedIndex].value;
  if( ( selVal != -1 ) && ( selVal != "" ) ) {
    var jvData = jQuery( document.venData );

    jQuery( "#teamDialog #groundAddrIH" ).getTransform( jQuery("#scriptprefix").html() + '/templates/favouritest/xven1.xsl', jQuery("#scriptprefix").html() + '/templates/favouritest/xven.xml' );
//    jQuery( "#teamDialog #groundAddrIH" ).getTransform( 
//	'<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"><xsl:template match="/">' +
//	'<xsl:for-each select="/stteam/venue[@key=\'' + selVal + '\']">' +
//	'<xsl:value-of select="addr"/>' +
//	'</xsl:for-each>' +
//	'</xsl:template></xsl:stylesheet>',
//      jQuery.xsl.serialize( document.venData ) );
    jQuery( "#teamDialog #groundAddr" ).val( jQuery( "teamDialog #groundAddrIH" ).val( ) );
    return;
  }
  jQuery( "#teamDialog #groundAddr" ).val( "" );
  window.alert( "not yet implemented - please contact the admin" );
}

function SaveMatch( ) {
  if( jQuery( "#hteam select" ).val( ) <= 0 ) {
    window.alert( "Please select a Home team" );
	jQuery( "#hteam select" ).focus( );
	return;
  }
  if( jQuery( "#ateam select" ).val( ) < 0 ) { // nb 0 is allowed, == bye
	window.alert( "Please select an Away team" );
	jQuery( "#ateam select" ).focus( );
	return;
  }
  if( jQuery( "#matchdate" ).val( ) == "" ) {
	window.alert( "Please select the match date" );
	jQuery( "#matchdate" ).datepicker( "show" );
  }
  if( jQuery( "#matchtime" ) == "" ) {
	window.alert( "Please select the match time" );
	return;
  }
  var sched = jQuery.datepicker.formatDate( "yy-mm-dd", jQuery( "#matchdate" ).datepicker( 'getDate' ) ) + " " + jQuery( "#matchtime" ).val( );
  $akey = jQuery( "#ateam :selected" ).val( );
  $venue = -1;
  if( $akey == 0 ) {
    $akey = -1; //BYE special values
  } else {
	$venue = jQuery( "#venue :selected" ).val( );
	if( $venue == "" ) {
	  window.alert( "Please select the venue" );
	  return;
	}
  }

  jQuery.post( jQuery("#scriptprefix").html() + "/xteam.php", {
      function: "NewMatch",
	  RNum: jQuery( "#roundnum" ).val( ),
	  ChKey: jQuery( '#ChampSel' ).val( ),
	  MatchNum: jQuery( "#matchnum" ).val( ),
	  HKey: jQuery( "#hteam :selected" ).val( ),
	  AKey: $akey,
	  Venue: $venue,
	  Sched: sched
	}, function( xdata, textStatus, jqXHR ) {
//window.alert( "returned: " + $.xsl.serialize( data ) );
	  initFixt( xdata );
	}, "xml" );
}

function onLoggedIn( chkey ) {
  jQuery.get( "https://www.thebrasstraps.com/scoretank/xstlib.php?isadmin=" + chkey, 
      function( xData ) {
        var jData = jQuery( xData );
        if( jData.find( "admin" ) ) {
          jQuery( ".scoreentry" ).removeAttr("disabled");
        }
        //window.alert( "returned: " + jQuery.xsl.serialize( xData ) );
      } );
}

function sendTestCB( data ) {
	console.log("sendTestCB, data returned: ");
	console.log(data);
}

function sendTest( userIdJwt ) {
	var queryParams = { "req" : "test", "userIdJwt" : userIdJwt };
	console.log("sendTest posting, userId = " + userIdJwt);
	jQuery.post("/scoretank/xindexxml.php", queryParams, sendTestCB, "json" );
//	jQuery.post("https://www.thebrasstraps.com/scoretank/templates/favouritest/xauthtest.php", queryParams, sendTestCB, "json" );
}

function SendMatchResLib( inputfld ) {
  var fldids = inputfld.id;
  var fldvals = inputfld.value;
  var queryParams = { "req" : "MRes", "fields" : fldids, "vals" : fldvals // < ?php
                // echo (', "fbSTAT" : "' . $_SESSION["fbSTATs"] . '"' );
         };
  jQuery.post( "/scoretank/jentry.php",
                          queryParams,
                          SendMatchResCB,
                          "json" );

  jQuery( '#status' + fldids ).text( "processing..." );
}

function SendMatchResLibBypass( inputfld ) {
  var fldids = inputfld.id;
  var fldvals = inputfld.value;
  var queryParams = { "req" : "MRes", "fields" : fldids, "vals" : fldvals // < ?php
                // echo (', "fbSTAT" : "' . $_SESSION["fbSTATs"] . '"' );
         };
console.log("posting");
  jQuery.post( "http://www.scoretank.com.au/fb/xentrybypass.php",
                          queryParams,
                          SendMatchResCB,
                          "json" );

  jQuery( '#status' + fldids ).text( "processing..." );
}

function SendMatchResCB( data ) {
	console.log("SendMatchResCB, data returned: ");
	console.log(data);
  if( data.inputflds ) {
    afields = data.inputflds.split( " " );
    var i = 0;
    for( i = 0; i < afields.length; i++ ) {
      jQuery( '#statusm' + afields[i] ).text( "processed" );
    }
  }
  if( data.error ) {
    if( data.message ) {
      alert("Result: " + data.message);
      //jQuery( "#fbdialog" ).text( data.message + ", " + data.error );
      //jQuery( "#fbdialog" ).dialog( 'open' );
    } else {
      alert("Error: " + data.error);
      //jQuery( "#fbdialog" ).text( data.error );
      //jQuery( "#fbdialog" ).dialog( 'open' );
    }
  } else {
  }
}
 
function loadStfbAuthPage( ) {
  jQuery( "#Displ" ).removeAttr("disabled");
  jQuery( "#xsubmit" ).removeAttr("disabled");
}

function sendAuthCB( data ) {
//window.alert( "calledback" );
  if( data.error ) {
    if( data.message ) {
      window.alert( data.message + ", " + data.error );
    } else {
      window.alert( data.error );
    }
  } else {
    window.alert( data.message );
    jQuery( '#proceed' ).attr( 'style', '' );
    jQuery( '#Displ' ).attr( 'disabled', true ); //      setDisabled( true );
    jQuery( '#xsubmit' ).attr( 'disabled', true );       // setDisabled( true );
  }
}

