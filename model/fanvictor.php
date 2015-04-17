<?php
require_once("admin/RestClient.php");
class Fanvictor extends Model
{
    public function __construct() 
    {
        $this->pools = new Pools();
        $this->scoringcategory = new ScoringCategory();
        $this->postUserInfo();
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
        $aUser['ip'] = $_SERVER['REMOTE_ADDR'];
        $this->sendRequest("userInfo", $aUser, false);
    }

    function canPlay()
    {
        return $this->sendRequest("canPlay", null, false);;
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

	public function getGamesummary()
	{
        return $this->sendRequest("gameSummary", null, false);
	}	

	public function getLeagueHeader($leagueID)
    {
        return $this->sendRequest("leagueHeader", array('leagueID' => $leagueID), false);
    }

    public function getFutureEvents()
	{
        return $this->sendRequest("futureEvents", null, false);
	}
    
    public function getNormalGameResult($leagueID)
    {
        $aDatas = $this->sendRequest("getNormalGameResult", array('leagueID' => $leagueID), false);
        if($aDatas["users"] != null)
        {
            foreach($aDatas["users"] as $k => $user)
            {
                $info = get_user_by("id", $user["userID"]);
                $aDatas["users"][$k]["user_login"] = $info->data->user_login;
            }
        }
        return $aDatas;
    }
    
    public function getLeagueDetail($leagueID)
    {
        return $this->sendRequest("leagueDetail", array('leagueID' => $leagueID), false);
    }	
    
	public function postUserPicks($post="")
    {
        return $this->sendRequest("userpicks", $post, false);
    }
    
    public function getUserPicks($leagueID)
    {
        return $this->sendRequest("getuserpicks", array('leagueID' => $leagueID), false);
    }
    
	public function getFights($leagueID)
    {
        return $this->sendRequest("fights", array('leagueID' => $leagueID, 'mode' => 'html'), false, false);
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
        
        //league
        $this->selectField(array('name', 'size', 'entry_fee', 'poolID'));
		$leagueInfo = $this->getLeagueDetail($importleagueID);
        $leagueInfo = $leagueInfo[0];
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
            try 
            {
                if(!mail($email,$message_subject,$message_body,$headers))
                {
                    $success = false;
                }
            } 
            catch (Exception $ex) 
            {
                
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
    
    ////////////////////////////////v2////////////////////////////////////
    public function isLeagueExist($leagueID)
    {
        if($this->sendRequest("isLeagueExist", array('leagueID' => $leagueID), false, false) == 1)
        {
            return true;
        }
        return false;
    }
    
    public function isNormalLeagueExist($leagueID)
    {
        if($this->sendRequest("isNormalLeagueExist", array('leagueID' => $leagueID), false, false) == 1)
        {
            return true;
        }
        return false;
    }
    
    public function isPlayerDraftLeagueExist($leagueID)
    {
        if($this->sendRequest("isPlayerDraftLeagueExist", array('leagueID' => $leagueID), false, false) == 1)
        {
            return true;
        }
        return false;
    }
    
    public function isPlayerDraftLeagueFull($leagueID, $entry_number)
    {
        if($this->sendRequest("isPlayerDraftLeagueFull", array('leagueID' => $leagueID, 'entry_number' => $entry_number), false, false) == 1)
        {
            return true;
        }
        return false;
    }
    
    public function getListSports()
    {
        return $this->sendRequest("getListSports", null, false);
    }
    
    public function getLeagueLobby()
    {
        return $this->sendRequest("getLeagueLobby", null, false);
    }
    
    public function getUpcomingEntries()
    {
        return $this->sendRequest("getUpcomingEntries", null, false);
    }
    
    public function getHistoryEntries()
    {
        return $this->sendRequest("getHistoryEntries", null, false);
    }
    
    public function getLiveEntries()
    {
        return $this->sendRequest("getLiveEntries", null, false);
    }
    
    public function liveEntriesResult($poolID, $leagueID)
    {
        echo $this->sendRequest("liveEntriesResult", array('poolID' => $poolID, 'leagueID' => $leagueID), false, false);exit;
    }

    public function parseLeagueData($aLeagues)
    {
        if($aLeagues != null)
        {
            foreach($aLeagues as $k => $aLeague)
            {
                $aLeagues[$k]['today'] = false;
                if(isset($aLeague['startDate']) && strtotime(date('Y-m-d')) == strtotime($aLeague['startDate']))
                {
                    $aLeagues[$k]['today'] = true;
                }
                
                //icon
                if(!empty($aLeague['sport_siteID']) && $aLeague['sport_siteID'] > 0 && !empty($aLeagues[$k]['icon']))
                {
                    $aLeagues[$k]['icon'] = FANVICTOR_IMAGE_URL.$aLeague['icon'];
                }
                
                //creator
                $user = get_userdata($aLeague['creator_userID']);
                $aLeagues[$k]['creator_name'] = $user != null ? $user->user_login : null;
                
                //total prize for winners
                $structure = '';
                if($aLeague['prize_structure'] == 'WINNER')
                {
                    $structure = 'winnertakeall';
                }
                else 
                {
                    $structure = 'top3';
                }
                $prizes = $this->pools->calculatePrizes('' , $structure, $aLeague['size'], $aLeague['entry_fee']);
                $aLeagues[$k]['prizes'] = 0;
                foreach($prizes as $prize)
                {
                    $aLeagues[$k]['prizes'] += $prize;
                }
            }
        }
        return $aLeagues;
    }

    public function insertPlayerPicks($data)
    {
        $entry_number = $this->sendRequest("insertPlayerPicks", $data, false, false);
        if($entry_number > 0)
        {
            return $entry_number;
        }
        return false;
    }
    
    public function deletePlayerPicks($leagueID)
    {
        if($this->sendRequest("deletePlayerPicks", array('leagueID' => $leagueID), false, false))
        {
            return true;
        }
        return false;
    }
    
    public function getPlayerPicks($leagueID, $entry_number)
    {
        $data = $this->sendRequest("getPlayerPicks", array('leagueID' => $leagueID, 'entry_number' => $entry_number), false);
        return $data;
    }
    
    public function getPlayerPickEntries($leagueID)
    {
        $aDatas = $this->sendRequest("getPlayerPickEntries", array('leagueID' => $leagueID), false);
        return $this->parseUserData($aDatas);
    }
    
    public function getEntries($leagueID)
    {
        $aDatas = $this->sendRequest("getEntries", array('leagueID' => $leagueID), false);
        return $this->parseUserData($aDatas);
    }
    
    public function getScores($leagueID)
    {
        $aDatas = $this->sendRequest("getScores", array('leagueID' => $leagueID), false);
        return $this->parseUserData($aDatas);
    }
    
    private function parseUserData($aDatas = null)
    {
        if($aDatas != null)
        {
            foreach($aDatas as $k => $aData)
            {
                $user = get_userdata($aData['userID']);
                if($user != null)
                {
                    $aDatas[$k]['username'] = $user->user_login;
                    $aDatas[$k]['avatar'] = $this->get_avatar_url(get_avatar($aData['userID']));
                }
            }
        }
        return $aDatas;
    }
    
    public function get_avatar_url($get_avatar)
    {
        preg_match("/src=['\"](.*?)['\"]/i", $get_avatar, $matches);
		return $matches[1];
    }
    
    public function getPlayerPicksResult($leagueID, $userID, $entry_number)
    {
        return $this->sendRequest("getPlayerPicksResult", array('leagueID' => $leagueID, 'userID' => $userID, 'entry_number' => $entry_number), false);
    }
    
    public function getPlayerStatistics($orgID, $playerID)
    {
        return $this->sendRequest("getPlayerStatistics", array("orgID" => $orgID, "playerID" => $playerID), false);
    }

    public function getPoolInfo($leagueID)
    {
        $aDatas = $this->sendRequest("getPoolInfo", array('leagueID' => $leagueID), false);
        $aDatas['scoringcats']['playerdraft'] = $this->scoringcategory->groupScoringCategory($aDatas['scoringcats']['playerdraft']);
        $aDatas['entries'] = $this->parseUserData($aDatas['entries']);
        return $aDatas;
    }

    public function getNewPools()
    {
        return $this->sendRequest("getNewPools", null, false);
    }
    
    public function validCreateLeague($orgID, $poolID, $game_type, $name, $fightID, $roundID, $payouts_from = null, $payouts_to = null, $percentage = null)
    {
        return $this->sendRequest("validCreateLeague", array("orgID" => $orgID, 
                                                             "poolID" => $poolID, 
                                                             "game_type" => $game_type,
                                                             "name" => $name,
                                                             "fightID" => $fightID,
                                                             "roundID" => $roundID,     
                                                             "payouts_from" => $payouts_from,
                                                             "payouts_to" => $payouts_to,
                                                             "percentage" => $percentage), false, false);
    }
    
    public function createLeague($data)
    {
        $data['winner_percent'] = get_option('fanvictor_winner_percent');
        $data['first_percent'] = get_option('fanvictor_first_place_percent');
        $data['second_percent'] = get_option('fanvictor_second_place_percent');
        $data['third_percent'] = get_option('fanvictor_third_place_percent');
        return $this->sendRequest("createLeague", $data, false, false);
    }
    
    public function getEnterGameData($leagueID, $entry_number)
    {
        return $this->sendRequest("getEnterGameData", array("leagueID" => $leagueID, "entry_number" => $entry_number), false);
    }
    
    public function getEnterNormalGameData($leagueID)
    {
        return $this->sendRequest("getEnterNormalGameData", array("leagueID" => $leagueID), false);
    }
    
    public function getGameEntryData($leagueID, $entry_number)
    {
        return $this->sendRequest("getGameEntryData", array("leagueID" => $leagueID, "entry_number" => $entry_number), false);
    }
    
    public function validEnterPlayerdraft($leagueID, $playerIDs)
    {
        return $this->sendRequest("validEnterPlayerdraft", array("leagueID" => $leagueID, "playerIDs" => $playerIDs), false, false);
    }
    
    public function getContestResult($leagueID)
    {
        return $this->sendRequest("getContestResult", array("leagueID" => $leagueID), false);
    }
    
    public function getPlayerNews($playerID)
    {
        return $this->sendRequest("getPlayerNews", array("playerID" => $playerID), false);
    }
}
?>