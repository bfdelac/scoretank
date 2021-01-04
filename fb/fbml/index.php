<?php
// Copyright 2007 Facebook Corp.  All Rights Reserved. 
// 
// Application: ScoreTank
// File: 'index.php' 
//   This is a sample skeleton for your application. 
// 

require_once 'facebook.php';

$appapikey = '103424ce8ec89f93620faeb04713764c';
$appsecret = '6a1cc9469dec99e4e067876e56dbd473';
$facebook = new Facebook($appapikey, $appsecret);
$user_id = $facebook->require_login();


?>
<H1>ScoreTank</H1>

ScoreTank on Facebook is in operation but many features are still in development.<P/>

You can view ScoreTank's main site at <A href='http://www.scoretank.com.au'>scoretank.com.au</A> and find your team's page.  You can then bookmark the link back to the ScoreTank Facebook page.<P/>

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

