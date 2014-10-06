<?php
class Rankings
{
    private static $payment;
    private static $fanvictor;
    public function __construct() 
    {
        self::$payment = new Payment();
        self::$fanvictor = new Fanvictor();
    }
    
	public static function process()
	{        
        add_action( 'wp_enqueue_scripts', array('Rankings', 'theme_name_scripts') );
        add_filter('the_content', array('Rankings', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('leagues.class.js', FANVICTOR__PLUGIN_URL_JS.'leagues.class.js');
        wp_enqueue_script('rankings.js', FANVICTOR__PLUGIN_URL_JS.'rankings.js');
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
    }

    public static function addContent()
    {
        $elem_to_show = 'error_message';
        $showInvite = false;
        $errorMessage = '';
        $leagueID = 0;
        $poolID = 0;
        $leagueheader = '';
        $userID = get_current_user_id();
        $elem_to_show = 'league_history';
        
        if ( isset($_REQUEST['live']) && 1 == $_REQUEST['live'] )
            $isLive = 1;
		else
            $isLive = 0;
		
		$error = false;
		$_POST['get_summary'] = 'NO';
		$allowMinutes = false;
		$canPlayPayed = false;		// whether user can play payed leagues
        $aFriends = self::$fanvictor->getAllPlayerInfo();
        $iTotalFriends = count($aFriends);
		sort($aFriends, SORT_STRING);
		usort($aFriends, function($a, $b){
			$a = strtolower($a['full_name'] ? $a['full_name'] : $a['user_name']);
			$b = strtolower($b['full_name'] ? $b['full_name'] : $b['user_name']);
			return strcmp($a, $b);
		});
		 
		$string = "";
		foreach ( $aFriends as $buddy )
		{
			# <input type="checkbox" name="vehicle" value="Bike" /> I have a bike<br />
			$full_name = $buddy["full_name"] ? $buddy["full_name"] : $buddy["user_name"];
			$string .= '<label><input type="checkbox" checked name="val[friend_ids][]" value="'.$buddy["ID"].'"> '.htmlspecialchars($full_name)."</label><br>";
		}

        $myString = $string;

        if ( isset($_REQUEST['poolID']) && $_REQUEST['poolID'] )	// modify or update
        {
            //league
            $league = self::$fanvictor->getLeagueDetail($_POST['leagueID']);
            $valid = self::validData($league[0]['entry_fee'], $league[0]['leagueID']);
            if($valid === true)
            {
                if ( ($jsonData = self::$fanvictor->postUserPicks($_POST)) && ($jsonObject = json_decode($jsonData)) )
                {
                    if ( isset($jsonObject->success) && $jsonObject->success )
                    {
                        if(!self::$payment->isMakeBetForLeague($league[0]['leagueID']))
                        {
                            //decrease user money
                            self::$payment->updateUserBalance($league[0]['entry_fee'], true, $league[0]['leagueID']);

                            //add to history
                            $aUser = self::$payment->getUserData();
                            self::$payment->addFundhistory($league[0]['entry_fee'], $league[0]['leagueID'], $aUser['balance'], 'MAKE_BET', 'DEDUCT');
                        }

                        // get ranking table for pool
                        if ( (int)$jsonObject->leagueID )
                        {
                            if ( isset($jsonObject->no_userpicks) && $jsonObject->no_userpicks )
                                $showInvite = 'true';

                            $leagueID = $jsonObject->leagueID;
                            $poolID = $jsonObject->poolID;
                            $leagueheader = $jsonObject->leagueheader;
                            $allowMinutes = $jsonObject->minutes;
                        }
                        else
                        {
                            $errorMessage = __('No league ID is set.');
                            $error = true;
                        }
                    }
                    else
                    {
                        if ( isset($jsonObject->reason) )
                        {
                            if ( 'pool_expired' == $jsonObject->reason )
                            {
                                $errorMessage = __('The pool has expired. Your picks have NOT been submitted.');
                            }
                            elseif ( 'pool_full' == $jsonObject->reason )
                            {
                                $errorMessage = __('The pool has maximum amount of users. Your picks have NOT been submitted.');
                            }
                            elseif ( 'not_enough_funds' == $jsonObject->reason )
                            {
                                $errorMessage = __('You do NOT have enough funds to enter. Please ADD FUNDS.');
                            }
                            elseif ( 'cannot_play_payed' == $jsonObject->reason )
                            {
                                $errorMessage = __("Sorry, we cannot process your request. You can't play payed leagues.");

                            }
                            elseif ( 'same_league_has_been_already_created' == $jsonObject->reason )
                            {
                                $errorMessage = __('The league is not created because you have already created the same league.');
                            }
                        }
                        elseif ( isset($jsonObject->msg) )
                        {
                            $errorMessage = $jsonObject->msg."<br>Your picks have NOT been submitted";
                        }
                    }
                }
                else
                {
                    $errorMessage = __('Error occured. Your picks have NOT been submitted.');
                }
            }
            else 
            {
                $errorMessage = $valid;
            }
        }
        else
        {
            // just see the results
            if ( isset($_REQUEST['leagueID']) )
            {
                $leagueID = $_REQUEST['leagueID'];
                $userID = get_current_user_id();
                $elem_to_show = 'league_history';
            }
            else
            {
                $errorMessage = 'No league ID.';
                $error = true;
            }
        }

        if ( !$error && !$allowMinutes )
        {
            if ( isset($_POST['leagueID']) && $_POST['leagueID'] && ($jsonData = self::$fanvictor->getLeagueHeader($_POST['leagueID'])) && ($jsonObject = json_decode($jsonData)) )
            {
                $leagueheader = $jsonObject->leagueheader;
            }
            else
            {
                $errorMessage = __('Internal error occured (AM type).');
                $error = true;
            }
        }

        $allowMinutes = (($allowMinutes == 'NO') ? 0 : 1);
        include FANVICTOR__PLUGIN_DIR_VIEW.'rankings.php';
    }
    
    private static function validData($entryFee = 0, $leagueID = null)
    {
        if(!self::$payment->isUserEnoughMoneyToJoin($entryFee, $leagueID))
        {
            $errorMessage = __('You do NOT have enough funds to enter. Please click <a href="'.FANVICTOR_URL_ADD_FUNDS.'">here</a> to ADD FUNDS.');
            return $errorMessage;
        }
        return true;
    }
}
?>