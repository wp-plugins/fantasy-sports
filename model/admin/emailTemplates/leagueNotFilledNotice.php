<?php
	$message_subject = "Sorry, your league was not filled '" . $emailInfo['league_name']."'";
	$message_body = 'Hi, 

		<br><br>

		We are terribly sorry, but your league called '.$emailInfo['league_name']. ' was not filled in time. This means the game will be cancelled. Your funds have been credited back to your account.

<br><br>
We know how annoying this can be so here are some tips to avoid it happening in the future: 
<br><br>
1. Join nearly full leagues, ideally as the last player<br>
2. Invite friends to join leagues you are in<br>
3. Choose smaller leagues that do not have many spots to fill
<br><br>

Get back in another game here <a href="'.$website.'">'.$website.'</a>
<br><br>Good luck and thanks for playing!';

?>