<?php
// Copyright 2007 Facebook Corp.  All Rights Reserved. 
// 
// Application: ScoreTank
// File: 'index.php' 
//   This is a sample skeleton for your application. 
// 

include 'stpage.php';
require_once 'facebook.php';


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<title>ScoreTank Index Page</title>
  </head>
  <body>
    <div id="fb-root"></div>
	<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
	<script type="text/javascript">
       FB.init({
         appId  : '<?=$fbconfig['appid']?>',
         status : true, // check login status
         cookie : true, // enable cookies to allow the server to access the session
	     xfbml  : true  // parse XFBML
	   });

	</script>

    <H1>ScoreTank</H1>

ScoreTank on Facebook is in operation but many features are still in development.<P/>

You can view ScoreTank's main site at <A target='_blank' href='http://www.scoretank.com.au'>scoretank.com.au</A> and find your team's page.  You can then bookmark the link back to the ScoreTank Facebook page.<P/>

ScoreTank on Facebook will have new features built on the Facebook infrastructure, such as
    <ul>
        <li>the ability to register yourself as a team member or fan, and thereby seeing other team members and having your team's matches in your events;</li>
        <li>improved graphic design, particularly on mobile devices</li>
        <li>the ability to upload photos, comments etc</li>
        <li>improved competition administration</li>
    </ul>

ScoreTank on Facebook uses the same fixtures and results database as the main ScoreTank website.
<p/>Privacy: ScoreTank will store your user ID to enable registration of team members, administrators etc., but it will ask you first and give you the option of whether or not you want to display these on the ScoreTank on Facebook webpages.
  ScoreTank will not divulge your user ID to any other party.

  </body>
</html>

