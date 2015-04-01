<?php
$message_subject = "$siteTitle league '" . $emailInfo['league_name'] . "' has completed";
$message_body = 'Congratulations ' . $emailInfo['username'] . '!
<br>
<br>
You won $' . $emailInfo['money'] . ' in league "' . $emailInfo['league_name'] . '" for coming in ' . $emailInfo['place'] . ' place.<br>
<br>
<br>
Daily '.$siteTitle.' Leagues<br>
<br>
Get back in another game here <a href="'.$website.'">'.$website.'</a>
<br><br>Good luck and thanks for playing!';
?>