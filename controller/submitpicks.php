<?php 
class Submitpicks
{ 
    private static $payment;
    private static $pools;
    private static $fanvictor;
    public function __construct() 
    {
        self::$payment = new Payment();
        self::$pools = new Pools();
        self::$fanvictor = new Fanvictor();
    }
    
	public static function process() 
	{   
        if(isset($_POST) && isset($_POST["SubmitPicks"]))
        {
            add_action( 'wp_enqueue_scripts', array('Submitpicks', 'theme_name_scripts') );
            add_filter('the_content', array('Submitpicks', 'submitPick'));
        }
        else 
        {
            add_action( 'wp_enqueue_scripts', array('Submitpicks', 'theme_name_scripts') );
            add_filter('the_content', array('Submitpicks', 'addContent'));
        }
	} 
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('submitpicks.js', FANVICTOR__PLUGIN_URL_JS.'submitpicks.js');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

    public static function addContent()
    {
        $leagueID = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? "/?num=".$_GET['num'] : '';
        //league
        $league = self::$fanvictor->getLeagueDetail($leagueID);
        $league = $league[0];
        
        //pool
        self::$pools->selectField(array('status'));
        $aPool = self::$pools->getPools($league['poolID'], null, false, true);
        
        if(strtolower($league['gameType']) == 'playerdraft')
        {
            redirect(FANVICTOR_URL_GAME.$leagueID.$entry_number, null, true);
        }
        else if(!self::$fanvictor->isNormalLeagueExist($leagueID))
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('League not found', FV_DOMAIN), true);
        }
        else if($aPool['status'] != 'NEW')//check league completed
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('This contest had completed', FV_DOMAIN), true);
        }
        else if(!self::$payment->isUserEnoughMoneyToJoin($league['entry_fee'], $leagueID))
        {
            redirect(FANVICTOR_URL_ADD_FUNDS, __('You do not have enough funds to enter. Please add funds', FV_DOMAIN));
        }
        else 
        {
            /*$htmlData = self::$fanvictor->getFights($leagueID);
            $errorMessage = '';
            if ( $htmlData == 'pool_expired' )
            {
                $errorMessage = __('Sorry the pool expired', FV_DOMAIN);
            }*/
            $aDatas = self::$fanvictor->getEnterNormalGameData($leagueID);
            $aLeague = $aDatas['league'];
            $aPool = $aDatas['pool'];
            $otherLeagues = $aDatas['otherLeagues'];
            $aFights = $aDatas['fights'];
            $aMethods = $aDatas['methods'];
            $creator = get_user_by("id", $aLeague['creator_userID']);
            include FANVICTOR__PLUGIN_DIR_VIEW.'submitpicks.php';
        }
    }
    
    public static function submitPick()
    {
        $leagueID = $_POST['leagueID'];
        //league
        $league = self::$fanvictor->getLeagueDetail($leagueID);
        $league = $league[0];
        
        //pool
        self::$pools->selectField(array('status'));
        $aPool = self::$pools->getPools($league['poolID'], null, false, true);
        
        if(strtolower($league['gameType']) == 'playerdraft')
        {
            redirect(FANVICTOR_URL_GAME.$leagueID, null, true);
        }
        else if(!self::$fanvictor->isNormalLeagueExist($leagueID))
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('League not found', FV_DOMAIN), true);
        }
        else if($aPool['status'] != 'NEW')//check league completed
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('This contest had completed', FV_DOMAIN), true);
        }
        else if(!self::$payment->isUserEnoughMoneyToJoin($league['entry_fee'], $leagueID))
        {
            redirect(FANVICTOR_URL_ADD_FUNDS, __('You do not have enough funds to enter. Please add funds', FV_DOMAIN));
        }
        else 
        {
            //league
            $elem_to_show = 'error_message';
            $showInvite = false;
            $errorMessage = '';
            $leagueID = 0;
            $poolID = 0;
            $leagueheader = '';
            $userID = get_current_user_id();
            $elem_to_show = 'league_history';
            $isLive = $league['is_live'];

            $error = false;
            $_POST['get_summary'] = 'NO';
            $allowMinutes = false;
            $canPlayPayed = false;		// whether user can play payed leagues
            $aFriends = self::$fanvictor->getAllPlayerInfo();
            $iTotalFriends = count($aFriends);
            sort($aFriends, SORT_ASC);
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
                $jsonObject = self::$fanvictor->postUserPicks($_POST);
                if ( $jsonObject != null )
                {
                    if ( isset($jsonObject['success']) && $jsonObject['success'] )
                    {
                        if(!self::$payment->isMakeBetForLeague($league['leagueID']))
                        {
                            //decrease user money
                            self::$payment->updateUserBalance($league['entry_fee'], true, $league['leagueID']);

                            //add to history
                            $aUser = self::$payment->getUserData();
                            self::$payment->addFundhistory($league['entry_fee'], $league['leagueID'], $aUser['balance'], 'MAKE_BET', 'DEDUCT');
                        }

                        // get ranking table for pool
                        if ( (int)$jsonObject['leagueID'] )
                        {
                            if ( isset($jsonObject['no_userpicks']) && $jsonObject['no_userpicks'] )
                                $showInvite = 'true';

                            $leagueID = $jsonObject['leagueID'];
                            $poolID = $jsonObject['poolID'];
                            $leagueheader = $jsonObject['leagueheader'];
                            $allowMinutes = $jsonObject['minutes'];
                        }
                        else
                        {
                            $errorMessage = __('No league ID is set.', FV_DOMAIN);
                            $error = true;
                        }
                    }
                    else
                    {
                        if ( isset($jsonObject['reason']) )
                        {
                            if ( 'pool_expired' == $jsonObject['reason'] )
                            {
                                $errorMessage = __('The pool has expired. Your picks have NOT been submitted.', FV_DOMAIN);
                            }
                            elseif ( 'pool_full' == $jsonObject['reason'] )
                            {
                                $errorMessage = __('The pool has maximum amount of users. Your picks have NOT been submitted.', FV_DOMAIN);
                            }
                            elseif ( 'not_enough_funds' == $jsonObject['reason'] )
                            {
                                $errorMessage = __('You do NOT have enough funds to enter. Please ADD FUNDS.', FV_DOMAIN);
                            }
                            elseif ( 'cannot_play_payed' == $jsonObject['reason'] )
                            {
                                $errorMessage = __("Sorry, we cannot process your request. You can't play payed leagues.", FV_DOMAIN);

                            }
                            elseif ( 'same_league_has_been_already_created' == $jsonObject['reason'] )
                            {
                                $errorMessage = __('The league is not created because you have already created the same league.', FV_DOMAIN);
                            }
                        }
                        elseif ( isset($jsonObject['msg']) )
                        {
                            $errorMessage = $jsonObject['msg']."<br>Your picks have NOT been submitted";
                        }
                    }
                }
                else
                {
                    $errorMessage = __('Error occured. Your picks have NOT been submitted.', FV_DOMAIN);
                }
            }
            $_SESSION['showInviteFriends'.$leagueID] = true;
            redirect(FANVICTOR_URL_RANKINGS.$leagueID, null, true);
        }
    }
} 
?>