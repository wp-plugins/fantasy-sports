<?php
require_once("admin/RestClient.php");
define('DEVELOPMENT', false);
// for .htpasswd
define('DEV_USER', 'boss');
define('DEV_PASS', 'mma');

class Fanvictor 
{
    public function __construct() 
    {
        $this->api_token = get_option('fanvictor_api_token');
        $this->api_url = get_option('fanvictor_api_url');
        //$this->view_global = Phpfox::getParam('fanvictor.fanvictor_view_global');
        $this->view_global = false;
        $this->postUserInfo();
        //$this->percentCredits();
    }

	private function getRestClient($method, $url)
    {
        $ret = false;
        $ret = (defined(DEVELOPMENT) && DEVELOPMENT) ? new RestClient($method, $url) : new RestClient($method, $url, DEV_USER, DEV_PASS);
        return $ret;
    }
    
    private function postUserInfo()
    {
        global $wpdb;
        $table_name = $wpdb->prefix."users";
        $sCond = "WHERE ID = ".(int)get_current_user_id();
        $sql = "SELECT ID as user_id, user_login as user_name, user_nicename as full_name, user_email as email "
             . "FROM $table_name "
             . $sCond;
        $aUser = $wpdb->get_row($sql, ARRAY_A);
        $aUser = json_decode(json_encode($aUser), true);
        $url = $this->api_url."/userinfo/".$this->api_token."/".get_current_user_id();
        $client = $this->getRestClient("POST", $url);
        $data = $client->send($aUser);
    }
            
    function getToken()
	{
		return $this->api_token;
	}
    
    function getUserData($userID = null)
    {
        $user_id = (int)Phpfox::getUserId();
        if((int)$userID > 0)
        {
            $user_id = $userID;
        }
        $data = $this->database()->select('*')
            ->from(Phpfox::getT('user'))
            ->where('user_id = '.$user_id)
            ->execute('getSlaveRow');
        return $data;
    }
    
    function percentCredits()
    {
        $_POST["default_winnerpercent"] = Phpfox::getParam('fanvictor.fanvictor_winner_percent');
        $url = $this->api_url."/percentCredits/".$this->api_token."/".get_current_user_id();
        $client = $this->getRestClient("POST", $url);
        $data = $client->send($_POST);
    }

	public function getTransactionsummary()
    {
        $url = $this->api_url."/transactionsummary/".$this->api_token."/".get_current_user_id();
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
    }

	public function postWithdrawal()
    {
		$_POST["ip"] = $_SERVER['REMOTE_ADDR'];
        $url = $this->api_url."/withdrawal/".$this->api_token."/".get_current_user_id();
        $client = $this->getRestClient("POST", $url);
        $data = $client->send($_POST);
		return $data;
    }

	public function getBalance()
	{
		$jsonData = $this->getAccountinfo();
		$jsonObject = json_decode($jsonData);
                return $jsonObject->balance;
	}

	public function getAccountinfo()
    {
        $url = $this->api_url."/accountInfo/".$this->api_token."/".get_current_user_id()."/".$_SERVER['REMOTE_ADDR'];
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
    }

	public function getGamesummary()
	{
        $url = $this->api_url."/gamesummary/".$this->api_token."/".get_current_user_id()."/".$_SERVER['REMOTE_ADDR'];
		$client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;	
	}	

	public function getLeagueHeader($leagueID)
    {
        $url = $this->api_url."/leagueheader/".$this->api_token."/".$leagueID;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
    }

	public function getLiveContests()
    {
        $url = $this->api_url."/LiveLeagues/".$this->api_token."/".get_current_user_id();
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
    }

	public function getHistoryContests()
    {
        $url = $this->api_url."/historyLeagues/".$this->api_token."/".get_current_user_id()."/html";
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
    }

	public function getUpcomingContests()
	{
        $url = $this->api_url."/upcomingLeagues/".$this->api_token."/".get_current_user_id()."/html";
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
	}
    
    public function getFutureEvents()
	{
        $url = $this->api_url."/futureEvents/".$this->api_token."/".get_current_user_id();
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
	}

	public function leagueResults($leagueID, $isLive = false)
    {
        $url = $this->api_url."/leagueResults/".$this->api_token."/".get_current_user_id()."/".$leagueID."?isLive=$isLive";
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
    }	
    
    public function getLeagueDetail($leagueID)
    {
        $url = $this->api_url."/leagueDetail/".$this->api_token."/".get_current_user_id()."/".$leagueID;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return json_decode($data, true);
    }	
    
	public function postUserPicks($post="")
    {
        $imgSuf = get_option('fanvictor_image_thumb_size');
        $url = $this->api_url."/userpicks/".$this->api_token."/".get_current_user_id()."?imgurl=".FANVICTOR_IMAGE_URL."&imgsuf=".$imgSuf;
        $client = $this->getRestClient("POST", $url);
        $data = $client->send($post);
        return $data;
    }
    
    public function getUserPicks($leagueID)
    {
        $url = $this->api_url."/userpicks/".$this->api_token."/".get_current_user_id()."?fields=winnerID,methodID,minuteID,roundID,fightID&where=leagueID:".(int)$leagueID;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
    }
    
	public function getFights($poolID,$leagueID)
    {
        $imgSuf = get_option('fanvictor_image_thumb_size');
        $url = $this->api_url."/fights/".$this->api_token. "/".get_current_user_id()."/".$leagueID."?mode=html&imgurl=".FANVICTOR_IMAGE_URL."&imgsuf=".$imgSuf;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
		return $data;
	}
    
	public function getNewgames()
    {
        $url = $this->api_url."/leaguesinfo/".$this->api_token."/".get_current_user_id();
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
    }
    
    public function showLeagueDetails($iLeagueId)
    {
        $url = $this->api_url."/LeagueDetails/".$this->api_token."/?leagueID=".(int)$iLeagueId;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
    }

    public function getFixtures()
    {
        $url = $this->api_url."/fixtures/".$this->api_token."/";
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        return $data;
    }
	
	public function postLeague($post)
    {
        $post['ip'] = $_SERVER['REMOTE_ADDR'];
        $post['winner_percent'] = get_option('fanvictor_winner_percent');
        $post['first_percent'] = get_option('fanvictor_first_place_percent');
        $post['second_percent'] = get_option('fanvictor_second_place_percent');
        $post['third_percent'] = get_option('fanvictor_third_place_percent');
        $url = $this->api_url."/league/".$this->api_token."/".get_current_user_id();
        $client = $this->getRestClient("POST", $url);
        $data = $client->send($post);
        return $data;
    }
    
    public function inviteFriend($data)
    {
        if (!empty($data['message_boxinvite']))
			$message_boxinvite = mysql_real_escape_string($data['message_boxinvite']);	

        global $wpdb;
		$contacts=array();
		$trueContacts=array();
		$contacts = explode(",", $data["emails"]);
		$importleagueID = $data["importleagueID"];
        $inFriends = null;
		if ( $friendIds = trim($data['friend_ids']) )
		{
			$inFriends = mysql_real_escape_string($friendIds);
            $table_name = $wpdb->prefix."users";
            $sCond = "WHERE ID IN ($inFriends)";
            $sql = "SELECT user_email as email "
                 . "FROM $table_name "
                 . $sCond;
            $result = $wpdb->get_results($sql);
            $result = json_decode(json_encode($result), true);
            foreach($result as $item)
            {
                $contacts[] = $item['email'];
            }
		}
		
		// we can't send invite to ourselves, so let's get username and email
        $table_name = $wpdb->prefix."users";
        $sCond = "WHERE ID = ".(int)get_current_user_id();
        $sql = "SELECT user_login, user_email as email "
             . "FROM $table_name "
             . $sCond;
        $result = $wpdb->get_row($sql);
        $result = json_decode(json_encode($result), true);
        $myUsername = $result['user_login'];
        $myEmail = $result['email'];
		
		// check if value is an email address. If not
		// then get mmavictor username

		foreach ( $contacts as $contact )
		{
			$contact = trim($contact);
			if ( $contact == $myUsername || $contact == $myEmail )
				continue;
			
			$pos = strpos($contact, '@');
			if ($pos === false)
			{
				// go get the email of the user
				if ( $mmavictorInfo = $this->getPlayerInfoByUsername($contact) )
					array_push($trueContacts,strtolower(trim($mmavictorInfo["email"])));
			}
			else
				array_push($trueContacts,strtolower(trim($contact)));
		}
		
		if ( count($trueContacts) == 0 )
			return array("message" =>"You haven't selected any contacts to invite !");
			
		$trueContacts = array_unique($trueContacts);
		$playerInfo = $this->getPlayerInfo(get_current_user_id());
		$leagueInfo = $this->getLeagueInfo($importleagueID);
        $website = 'http://'.$_SERVER['SERVER_NAME'];
        $siteTitle = get_option('blogname');

		require_once('admin/emailTemplates/invite.php');
		$message=array('subject'=>$message_subject,
			'body'=>$message_body,
			'attachment'=>"\n\rAttached message: \n\r".$message_boxinvite);

		$message_footer="\r\n\r\nThanks for playing and Good Luck!"
				."\r\n\r\nGet into the game here $website";
		//$message_subject=$name.$message['subject'];
		$message_subject = $message['subject'];
		$message_body = $message['body'] . $message_footer;
		$headers="From: ".$myEmail;
        $success = true;
        
		foreach ($trueContacts as $email)
		{
			if(!mail($email,$message_subject,$message_body,$headers))
            {
                $success = false;
            }
		}
        $message= "Invites Sent!";
        if(!$success)
        {
            $message = 'Something went wrong! Please try again.';
            return json_encode(array("notice" => $message));
        }
		return json_encode(array("message" => $message));
    }
    
    private function getPlayerInfoByUsername($username = null)
	{
        if($username != null)
        {
            $result = $this->database()->select('*')
                ->from(Phpfox::getT('user'))
                ->where("username = '$username'")
                ->execute('getSlaveRow');
            return $result;
        }
        return null;
	}
    
    private function getLeagueInfo($leagueID)
    {
        $url = $this->api_url."/leagueInfo/".$this->api_token."/".get_current_user_id()."?leagueID=".$leagueID;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
        return $data;
    }
    
    private function getPlayerInfo($user_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix."users";
        $sCond = "WHERE ID = ".(int)$user_id;
        $sql = "SELECT *, user_email as email, display_name as full_name "
             . "FROM $table_name "
             . $sCond;
        $result = $wpdb->get_row($sql);
        $result = json_decode(json_encode($result), true);
        $result['pubKey'] = $result['firstName'] = $result['lastName'] = '';
        $result['username'] = $result['user_login'];
        return $result;
    }
    
    public function getAllPlayerInfo()
    {
        global $wpdb;
        $table_name = $wpdb->prefix."users";
        $sCond = "WHERE ID != ".get_current_user_id();
        $sql = "SELECT *, user_email as email, display_name as full_name "
             . "FROM $table_name "
             . $sCond;
        $result = $wpdb->get_results($sql);
        $result = json_decode(json_encode($result), true);

        return $result;
    }
}

?> 
