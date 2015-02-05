<?php
if ( !isset($message_boxinvite) )
	$message_boxinvite = '';

$playerInfo['user_name'] = isset($playerInfo['user_name']) ? $playerInfo['user_name'] : $playerInfo['username'];

if ( $playerInfo['full_name'] )
	$message_body = $message_subject = "$playerInfo[full_name] ($playerInfo[user_name])";
else
	$message_body = $message_subject = "$playerInfo[email] ($playerInfo[user_name])";
$message_subject .= ' has sent you a '.$siteTitle.' Challenge!';
$message_body .= " has invited you to a Fantasy League on '$siteTitle'.

League Details:
League Name: ".$leagueInfo["name"]."
Based off: ".$leagueInfo["poolName"]." 
Date: ".$leagueInfo["startDate"]."
Size: ".$leagueInfo["size"]."
Entry : $".$leagueInfo["entry_fee"]."


Attached Message from ".$playerInfo["username"]." :
".$message_boxinvite."



Whats next?
Register or Login at $website.  Once you log in, you'll see this league listed in the lobby. Just click on that link and away you go.

What are you waiting for? Go get em'!

What is '.$siteTitle.'?
'.$siteTitle.' is a Fantasy league covering all major sports.  Leagues are created by '.$siteTitle.' players. You can join any public league or create you own private league and invite your friends.  Leagues are based off a real live events.  Leagues last just 1 day so there is instant fun and instant pay outs! Play for real money or play for FREE in our FREE leagues.

How does it work?
Simply register or login,  join a league and make your predictions on the outcome of each game. Earn points for each correct selection or how well your player does. The player with the most points at the end of the night wins!  Its simple and fun!  
";
?>