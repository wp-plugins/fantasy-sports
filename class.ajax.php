<?php
$ajax = new Ajax();
class Ajax
{
    private static $fanvictor;
    private static $pools;
    private static $fighters;
    private static $orgs;
    private static $teams;
    private static $payment;
    private static $user;
    private static $statistic;
    private static $scoringcategory;
    private static $playerposition;
    private static $players;
    private static $coupon;
    
    public function __construct() 
    {
        
        add_action('init', array(&$this, 'init'));
    }
    
    public static function init()
    {
        self::$fanvictor = new Fanvictor();
        self::$pools = new Pools();
        self::$fighters = new Fighters();
        self::$orgs = new Organizations();
        self::$teams = new Teams();
        self::$payment = new Payment();
        self::$user = new User();
        self::$statistic = new Statistic();
        self::$scoringcategory = new ScoringCategory();
        self::$playerposition = new PlayerPosition();
        self::$players = new Players();
        self::$coupon = new FV_CouponModel();
        
        $funcs = array('loadCbOrgs', 'loadCbFighters', 'loadCbTeams', 'viewResult',
                       'updateResult', 'updatePoolComplete', 'activeOrgs', 'sendUserCredits',
                       'sendUserWithdrawls', 'loadPoolsByOrg', 'calculatePrizes', 'loadFights',
                       'LeagueResults', 'userpicks', 'sendInviteFriend', 'getNormalGameResult',
                       'addCredits', 'loadUserBalance', 'requestPayment', 'loadLeagueDetail',
                       'updateNewContests', 'showPoolStatisticDetail', 'viewPoolFixture', 'addMoneyByCoupon',
                       'loadUser', 'loadPoolInfo', 'viewPlayerDraftResult', 'updatePlayerDraftResult',
                       'loadUserResult', 'loadLeagueLobby', 'loadLeagueEntries', 'loadLeaguePrizes',
                       'loadLiveEntries', 'liveEntriesResult', 'loadContestScores', 'loadPlayerPoints',
                       'loadPlayerStatistics', 'loadPlayerNews', 'activeScoringCategory', 'reverseResult');
        foreach($funcs as $func)
        {
            add_action("wp_ajax_$func", array('Ajax', $func));
        }
		
		if(get_current_user_id() == 0 && isset($_POST['action']) && in_array($_POST['action'], $funcs))
        {
            $func = $_POST['action'];
            self::$func();
        }
    }
    
    public static function updateNewContests()
    {
        //leagues
        $aLeagues = self::$fanvictor->getLeagueLobby();
        $aLeagues = self::$fanvictor->parseLeagueData($aLeagues);
        exit(json_encode($aLeagues));
    }

    public static function LeagueResults()
    {
        $iLeagueId = $_POST['leagueId'];
        $isLive = isset($_POST['isLive']) ? $_POST['isLive'] : '';
        $sData = self::$fanvictor->leagueResults($iLeagueId, $isLive);
        exit(json_encode($sData));
    }
    
    public static function getNormalGameResult()
    {
        $sData = self::$fanvictor->getNormalGameResult($_POST['leagueID']);
        exit(json_encode($sData));
    }
    
    public static function loadLiveEntries()
    {
        $aLeagues = self::$fanvictor->getLiveEntries();
        $aLeagues = self::$fanvictor->parseLeagueData($aLeagues);
        exit(json_encode($aLeagues));
    }
    
    public static function userpicks()
    {
        $iLeagueId = $_POST['leagueId'];
        $sData = self::$fanvictor->getUserPicks($iLeagueId);
        exit($sData);
    }
    
    public static function sendInviteFriend()
    {
        $data = $_POST['val'];
        $friend_ids = isset($data['friend_ids']) ? $data['friend_ids'] : null;
        if(!array_filter($data['emails']) && $friend_ids == null)
        {
            exit(json_encode(array('notice' => __('You have not selected any friends to invite', FV_DOMAIN))));
        }
        else if($data['importleagueID'] == "" || $data['importleagueID'] == "0" )
        {
            exit(json_encode(array('notice' => __('Sorry the system detected a spam attempt please contact support', FV_DOMAIN))));
        }
        else if(!self::isValidInviteEmail($data['emails']))
        {
            exit(json_encode(array('notice' => __('Please enter a valid email address', FV_DOMAIN))));
        }
        else 
        {
            foreach($data['emails'] as $k => $item)
            {
                if($item == null)
                {
                    unset($data['emails'][$k]);
                }
            }
            array_values($data['emails']);
            $data['emails'] = implode(',', $data['emails']);
            $data['friend_ids'] = $friend_ids != null ? implode(',', $friend_ids) : null;
            $data = self::$fanvictor->inviteFriend($data);
            exit($data);
        }
    }
    
    private static function isValidInviteEmail($data)
    {
        foreach($data as $item)
        {
            if($item != null && !self::$payment->validEmail($item))
            {
                return false;
            }
        }
        return true;
    }

    public static function loadPoolsByOrg()
    {
        $orgID = $_POST['orgID'];
        $aPools = self::$pools->getPools(null, $orgID, true, true);
        $resultPools = $resultFights = $sport = null;
        if($aPools != null)
        {
            foreach($aPools as $aPool)
            {
                $resultPools .= '<option value="'.$aPool['poolID'].'">'.$aPool['poolName'].'</option>';
                $sport = $aPool['type'];
            }
        }
        exit(json_encode(array('resultPools' => $resultPools, 'resultFights' => $resultFights, 'sport' => $sport)));
    }
    
    public static function loadFights()
    {
        $poolID = $_POST['poolID'];
        $aFights = self::$pools->getFights($poolID, null, true);
        $resultFights = null;
        if($aFights != null)
        {
            foreach($aFights as $aFight)
            {
                $resultFights .= '<input type="checkbox" checked="checked" name="fixture_'.$poolID.'_'.$aFight['fightID'].'" id="fixture_'.$poolID.'_'.$aFight['fightID'].'" value="'.$aFight['fightID'].'">'
                                .'<label for="fixture_'.$poolID.'_'.$aFight['fightID'].'">'.$aFight['name'].'</label><br/>';
            }
        }
        exit($resultFights);
    }
    
    public static function calculatePrizes()
    {
        $type = $_POST['type'];
        $structure = $_POST['structure'];
        $size = $_POST['size'];
        $entryFee = $_POST['entry_fee'];
        $prizes = self::$pools->calculatePrizes($type , $structure, $size, $entryFee);
        
        $result = '<table style="width:100%">'
                . '<tr><td style="text-align:left">Pos</td><td style="text-align:right">Prize</td></tr>';
        $count = 0;
        foreach($prizes as $prize)
        {
            $count++;
            $place = null;
            switch ($count)
            {
                case 1:
                    $place = '1st';
                    break;
                case 2:
                    $place = '2nd';
                    break;
                case 3:
                    $place = '3rd';
                    break;
            }
            $result .= '<tr><td style="text-align:left">'.$place.'</td><td style="text-align:right">$'.$prize.'</td></tr>';
        }
        $result .= '</table>';
        exit($result);
    }
    
    public static function viewPoolFixture()
    {
        $iPoolID = $_POST['iPoolID'];
        $aFights = self::$pools->getFights($iPoolID, null, true);
        $sResult = '';
        $count = 0;
        if($aFights != null)
        {
            $aPool = self::$pools->getPools($iPoolID, null, false, true);
            if($aPool['type'] == 'MMA' || $aPool['type'] == 'BOXING')
            {
                $teamOrFighterHeader = __("Fighter", FV_DOMAIN);
            }
            else 
            {
                $teamOrFighterHeader = __("Team", FV_DOMAIN);
            }
            $sResult .= '<table class="table table-striped table-bordered table-responsive table-condensed">';
            foreach($aFights as $aFight)
            {
                $spread1 = $aFight['team1_spread_points'];
                $spread2 = $aFight['team2_spread_points'];
                $sResult .= '<tr>
                                <td style="text-align:center">'.$spread1.' '.$aFight['name'].' '.$spread2.'</td>
                            </tr>';
            }
            $sResult .= '</table>';
        }
        else 
        {
            $sResult = '<center>'.__("No fixtures", FV_DOMAIN).'</center>';
        }
        exit($sResult);
    }
    
    //////////////////////////////v2//////////////////////////////
    public static function loadPoolInfo()
    {
        $aInfos = self::$fanvictor->getPoolInfo($_POST['leagueID']);
        exit(json_encode(array('league' => $aInfos['league'], 
                               'scorings' => $aInfos['scoringcats'], 
                               'fights' => $aInfos['fights'], 
                               'rounds' => $aInfos['rounds'], 
                               'entries' => $aInfos['entries'], 
                               'startDate' => $aInfos['pool']['startDate'])));
    }
    
    public static function loadLeagueEntries()
    {
        $leagueID = $_POST['leagueID'];
        $aDatas = self::$fanvictor->getEntries($leagueID);
        exit(json_encode($aDatas));
    }
    
    public static function loadLeaguePrizes()
    {
        $league = self::$fanvictor->getLeagueDetail($_POST['leagueID']);
        $league = $league[0];
        
        $structure = '';
        if($league['prize_structure'] == 'WINNER')
        {
            $structure = 'winnertakeall';
        }
		        else if($league['prize_structure'] == 'MULTI_PAYOUT')
        {
            $structure = 'multi_payout';
        }
        else 
        {
            $structure = 'top3';
        }
        $payouts = null;
        if(!empty($league['payouts']))
        {
            $payouts = json_decode($league['payouts'], true);
        }
        $prizes = self::$pools->calculatePrizes('' , $structure, $league['size'], $league['entry_fee'], $payouts);
        $aDatas = array();
        if($prizes != null)
        {
            foreach($prizes as $place => $prize)
            {
                $aDatas[] = array('place' => $place, 'prize' => $prize);
            }
        }
        else 
        {
            $aDatas[] = array('place' => '1st', 'prize' => 0);
        }
        exit(json_encode(array('prize' => $aDatas, 'note' => $league['note'])));
    }
    
    public static function loadLeagueDetail()
    {
        $league = self::$fanvictor->getLeagueDetail($_POST['leagueID']);
        if($league != null)
        {
            $league = $league[0];
        }
        exit(json_encode($league));
    }
    
    public static function updatePlayerDraftResult()
    {
        if(!self::$pools->updatePlayerDraftResult($_POST))
        {
            exit('<div class=\"error_message\">'.__('Something went wrong! Please try again', FV_DOMAIN).'</div>');
        }
        exit('Successfully updated');
    }
    
    public static function loadUserResult()
    {
        $aResults = self::$fanvictor->getPlayerPicksResult($_POST['leagueID'], $_POST['userID'], $_POST['entry_number']);
        exit(json_encode($aResults));
    }
    
    public static function loadLeagueLobby()
    {
        //leagues
        $aLeagues = self::$fanvictor->getLeagueLobby();
        $aLeagues = self::$fanvictor->parseLeagueData($aLeagues);
        exit(json_encode($aLeagues));
    }
    
    public static function liveEntriesResult()
    {
        self::$fanvictor->liveEntriesResult($_POST['poolID'], $_POST['leagueID']);
    }
    
    public static function loadContestScores()
    {
        //scores
        $aScores = self::$fanvictor->getScores($_POST['leagueID']);

        //cur user scores
        $currUserScore = null;
        if($aScores != null)
        {
            foreach($aScores as $k => $aScore)
            {
                $aScore[$k]['current'] = false;
                if($aScore['userID'] == get_current_user_id() && $aScore['entry_number'] == $_POST['entry_number'])
                {
                    $aScores[$k]['current'] = true;
                }
            }
        }
        exit(json_encode($aScores));
    }
    
    public static function loadPlayerStatistics()
    {
        $aStatistics = self::$fanvictor->getPlayerStatistics($_POST['orgID'], $_POST['playerID']);
        exit(json_encode($aStatistics));
    }
    
    public static function loadPlayerNews()
    {
        $news = self::$fanvictor->getPlayerNews($_POST['playerID']);
        exit(json_encode($news));
    }

    //////////////////
    ///   payment   //
    /////////////////
    public static function addCredits()
    {
        $credits = $_POST['credits'];
        $gateway = $_POST['gateway'];
		unset($_SESSION['paypal_complete']);
        if(!isset($_SESSION['is_transaction']))
        {
            if(!is_numeric($credits) || (int)$credits < 1)
            {
                exit(json_encode(array('notice' => __('Credits not valid', FV_DOMAIN))));
            }
            else if($credits < get_option('fanvictor_minimum_deposit'))
            {
                exit(json_encode(array('notice' => __('Credits must be greater than ').get_option('fanvictor_minimum_deposit', FV_DOMAIN))));
            }
            else if(!empty($_POST['coupon_code']) && 
                    !self::$coupon->isCouponCodeExist($_POST['coupon_code'], CP_ACTION_EXTRA_DEPOSIT))
            {
                exit(json_encode(array('notice' => __('This code does not exist', FV_DOMAIN))));
            }
            else if(!empty($_POST['coupon_code']) && 
                    self::$coupon->isCouponCodeUsed($_POST['coupon_code'], CP_ACTION_EXTRA_DEPOSIT))
            {
                exit(json_encode(array('notice' => __('This code has already used', FV_DOMAIN))));
            }
			else if(!empty($_POST['coupon_code']) && 
					self::$coupon->isCouponCodeLimit($_POST['coupon_code'], CP_ACTION_EXTRA_DEPOSIT))
            {
                exit(json_encode(array('notice' => __('This code has reached to limit', FV_DOMAIN))));
            }
            else if(!self::$payment->isGatewayExist($gateway))
            {
                exit(json_encode(array('notice' => __('Please select gateway', FV_DOMAIN))));
            }
            else
            {
                $money = self::$payment->changeCreditToCash($credits);
                $reason = '';
                if(!empty($_POST['coupon_code']))
                {
                    $coupon = self::$coupon->getCouponByCode($_POST['coupon_code'], CP_ACTION_EXTRA_DEPOSIT);
                    if($coupon != null)
                    {
                        $money += self::$coupon->getTotalDiscountValue($coupon->discount_type, $coupon->discount_value, $money);
                        $reason = __("Coupon code: ".$_POST['coupon_code'], FV_DOMAIN);
                        self::$coupon->addCouponUsed($coupon->id, get_current_user_id());
                    }
                }
                $iFundHitoryId = self::$payment->addFundhistory($money, 0, 0, 'DEPOSIT', 'ADD', null, $gateway, $reason, (int)get_option('fanvictor_cash_to_credit'));
                if((int)$iFundHitoryId > 0)
                {
                    $aSettings = array('paypal_email' => get_option('paypal_email_account'),
                                       'business' => get_option('paypal_email_account'),
                                       'item_name' => "Deposit ".$iFundHitoryId,
                                       'item_number' => 1,
                                       'amount' => $credits,
                                       'notify_url' => FANVICTOR_URL_NOTIFY_ADD_FUNDS,
                                       'return' => FANVICTOR_URL_SUCCESS_ADD_FUNDS,
                                       'cancel_return' => FANVICTOR_URL_ADD_FUNDS,
									   'custom' => get_current_user_id().'|'.$iFundHitoryId.'|'.$money);
                    $sUrl = self::$payment->onlineTransaction($gateway, $aSettings);
                    if($sUrl)
                    {
                    	unset($_SESSION['is_transaction']);
                    	if(strstr($sUrl, "//"))
                    		exit(json_encode(array('result' => $sUrl)));
                    	else
							exit(json_encode(array('notice' => $sUrl)));
                    }
                    else
                    {
                        self::$payment->deleteFundhistory($iFundHitoryId);
                        exit(json_encode(array('notice' => __('Something went wrong! Please try again.', FV_DOMAIN))));
                    }
                }
                else
                {
                    exit(json_encode(array('notice' => __('Something went wrong! Please try again.', FV_DOMAIN))));
                }
            }
        }
        else 
        {
            exit(json_encode(array('notice' => __('You are in transaction session. To start new session please refresh this page', FV_DOMAIN))));
        }
    }
    
    public static function requestPayment()
    {
        $aVals = $_POST['val'];
        $online = false;
        if(get_option('fanvictor_payout_method') == 'paypal')
        {
            $online = true;
        }
        $credits = $aVals['credits'];
        $reason = $aVals['reason'];
        if(!is_numeric($credits) || (int)$credits < 1)
        {
            exit(json_encode(array('notice' => 'Credits not valid')));
        }
        else if(!self::$payment->isAllowWithdraw($credits))
        {
            exit(json_encode(array('notice' => __('Credits must not exceed your available balance', FV_DOMAIN))));
        }
        else if($online && !self::$payment->isGatewayExist($aVals['gateway']))
        {
            exit(json_encode(array('notice' => __('Please select gateway', FV_DOMAIN))));
        }
        else if($online && empty($aVals['email']))
        {
            exit(json_encode(array('notice' => __('Please provide your email', FV_DOMAIN))));
        }
        else if(!$online && empty($aVals['name']))
        {
            exit(json_encode(array('notice' => __('Please provide your name', FV_DOMAIN))));
        }
        else if(!$online && empty($aVals['house']))
        {
            exit(json_encode(array('notice' => __('Please provide House/Deparment', FV_DOMAIN))));
        }
        else if(!$online && empty($aVals['street']))
        {
            exit(json_encode(array('notice' => __('Please provide street', FV_DOMAIN))));
        }
        else if(!$online && empty($aVals['city']))
        {
            exit(json_encode(array('notice' => __('Please provide city', FV_DOMAIN))));
        }
        else if(!$online && empty($aVals['state']))
        {
            exit(json_encode(array('notice' => __('Please provide state', FV_DOMAIN))));
        }
        else if(!$online && empty($aVals['country']))
        {
            exit(json_encode(array('notice' => __('Please provide country', FV_DOMAIN))));
        }
        else if(self::$payment->updateUserBalance($credits, true, 0))
        {
            //add withdrawl
            $aUser = self::$payment->getUserData();
            $withdrawlId = self::$payment->addWithdraw($credits, $reason, $aUser['ID'], $aUser['balance']);
            
            //update user info
            unset($aVals['credits']);
            unset($aVals['reason']);
            if(!self::$payment->isUserPaymentInfoExist($aVals))
            {
                self::$payment->addUserPaymentInfo($aVals);
            }
            else 
            {
                self::$payment->updateUserPaymentInfo($aVals);
            }
            
            //send email
            if(!$online)
            {
                self::sendRequestPaymentEmail($withdrawlId, $credits, $aVals);
            }
            exit(json_encode(array('result' => __('Your request has been sent', FV_DOMAIN), 'redirect' => FANVICTOR_URL_REQUEST_HISTORY)));
        }
        else
        {
            exit(json_encode(array('notice' => __('Something went wrong! Please try again.', FV_DOMAIN))));
        }
    }
    
    private static function sendRequestPaymentEmail($id, $credits, $aVal)
    {
        
        $current_user = wp_get_current_user();
        $to      = get_option('admin_email');
        $subject = 'Rquest Payment';
        $headers = 'From: '.get_option('blogname');
        require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/emailTemplates/withdrawl.php');
        try 
        {
            mail($to, $subject, $message, $headers);
        } catch (Exception $ex) {
            exit($ex->getMessage());
        }
    }

    public static function loadUserBalance()
    {
        $aUser = self::$payment->getUserData();
        exit($aUser['balance']);
    }

    //////////////////
    ///   admin   ///
    /////////////////
    public static function activeOrgs()
    {
        $orgID = $_POST['id'];
        $active = $_POST['active'];
        if(self::$orgs->updateOrgsActive($orgID, $active))
        {
            exit(json_encode(array('result' => 'true')));
        }
        exit(json_encode(array('notice' => __('Something went wrong! Please try again.', FV_DOMAIN))));
    }
    
    public static function activeScoringCategory()
    {
        $id = $_POST['id'];
        $active = $_POST['active'];
        if(self::$scoringcategory->updateScoringCategoryActive($id, $active))
        {
            exit(json_encode(array('result' => 'true')));
        }
        exit(json_encode(array('notice' => __('Something went wrong! Please try again.', FV_DOMAIN))));
    }
    
    public static function loadCbOrgs()
    {
        $sport = $_POST['sport'];
        $sel = $_POST['sel'];
        $aOrgs = self::$orgs->getOrgs(null, $sport, true);
        $result = null;
        if($aOrgs != null)
        {
            foreach($aOrgs as $aOrg)
            {
                $select = null;
                if($aOrg['organizationID'] == $sel)
                {
                    $select = 'selected="true"';
                }
                $result .= '<option value="'.$aOrg['organizationID'].'" '.$select.'>'.$aOrg['description'].'</option>';
            }
        }
        exit($result);
    }
    
    public static function loadCbFighters()
    {
        $orgsID = $_POST['orgsID'];
        $aFighters = self::$fighters->getFighters(null, null, true);
        $result = '<option value="">--'.__('Please select fighter', FV_DOMAIN).'--</option>';
        if($aFighters != null)
        {
            foreach($aFighters as $aFighter)
            {
                $result .= '<option value="'.$aFighter['fighterID'].'">'.$aFighter['name'].'</option>';
            }
        }
        exit($result);
    }
    
    public static function loadCbTeams()
    {
        $orgsID = $_POST['orgsID'];
        $aTeams = self::$teams->getTeams(null, $orgsID, true);
        $result = '<option value="">--'.__('Please select team', FV_DOMAIN).'--</option>';
        if($aTeams != null)
        {
            foreach($aTeams as $aTeam)
            {
                $result .= '<option value="'.$aTeam['teamID'].'">'.$aTeam['name'].'</option>';
            }
        }
        exit($result);
    }
    
    public static function loadUser()
    {
        $user_id = $_POST['user_id'];
        $aUser = self::$user->getUser($user_id);
        exit(json_encode($aUser));
    }
    
    /////////////////////////////////payment/////////////////////////////////
    public static function sendUserCredits()
    {
        $task = $_POST['task'];
        $user_id = $_POST['user_id'];
        $credits = $_POST['credits'];
        $reason = $_POST['reason'];
        if(!is_numeric($credits) || (int)$credits < 1)
        {
            exit(json_encode(array('notice' => __('Credits not valid', FV_DOMAIN))));
        }
        else if($task == 'remove' && !self::$payment->isAllowWithdraw($credits, $user_id))
        {
            exit(json_encode(array('notice' => __('Credits must not exceed your available balance', FV_DOMAIN))));
        }
        else
        {
            switch($task)
            {
                case 'remove':
                    $decrease = true;
                    $operation = 'DEDUCT';
                    $msg = __('Successfully deducted', FV_DOMAIN);
                    break;
                default :
                    $decrease = false;
                    $operation = 'ADD';
                    $msg = __('Successfully added', FV_DOMAIN);
                    break;
            }
            if(self::$payment->updateUserBalance($credits, $decrease, 0, $user_id))
            {
                $aUser = self::$payment->getUserData($user_id);
                self::$payment->addFundhistory($credits, 0, $aUser['balance'], 'CREDITS', $operation, $user_id, null, $reason);
                exit(json_encode(array('result' => $msg)));
            }
            else
            {
                exit(json_encode(array('notice' => __('Something went wrong! Please try again.', FV_DOMAIN))));
            }
        }
    }
    
    public static function sendUserWithdrawls()
    {
        $withdrawlID = $_POST['withdrawlID'];
        $action = isset($_POST['status']) ? $_POST['status'] : '';
        $gateway = isset($_POST['gateway']) ? $_POST['gateway'] : '';
        $response_message = $_POST['response_message'];
        if($action != 'APPROVED' && $action != 'DECLINED')
        {
            exit(json_encode(array('notice' => __('Please select action', FV_DOMAIN))));
        }
        else
        {
            $aWithdrawl = self::$payment->getWithdraw($withdrawlID);
            $aVals = array('status' => $action, 
                           'response_message' => $response_message,
                           'processedDate' => date('Y-m-d H:i:s'));
            if($action == 'DECLINED' && $aWithdrawl['status'] != 'DECLINED')
            {
                if(self::$payment->updateWithdraw($withdrawlID, $aVals) &&
                   self::$payment->updateUserBalance($aWithdrawl['amount'], false, 0, $aWithdrawl['userID']))
                {
                    exit(json_encode(array('result' => __('Successfully updated', FV_DOMAIN))));
                }
                else
                {
                    exit(json_encode(array('result' => __('Something went wrong! Please try again.', FV_DOMAIN))));
                }
            }
            else if($action == 'APPROVED' && $aWithdrawl['status'] != 'APPROVED')
            {
                if(get_option('fanvictor_payout_method') == 'paypal')
                {
                    $aUserPaymentInfo = self::$payment->getUserPaymentInfo($gateway, $aWithdrawl['userID']);
                    if(isset($aUserPaymentInfo['email']) && $aUserPaymentInfo['email'] != null)
                    {
                        $aSettings = array(
                            'paypal_email' => $aUserPaymentInfo['email'],
                            'business' => $aUserPaymentInfo['email'],
                            'item_name' => "withdraw ".$withdrawlID,
                            'item_number' => 1,
                            'amount' => $aWithdrawl['real_amount'],
                            'notify_url' => FANVICTOR_URL_NOTIFY_WITHDRAWLS,
                            'return' => FANVICTOR_URL_SUCCESS_WITHDRAWLS,
                            'cancel_return' => admin_url().'admin.php?page=withdrawls',
                            'custom' => $withdrawlID.'|'.$response_message);
                        $paypal = new Paypal();
                        $url = $paypal->parseData($aSettings);
                        exit(json_encode(array('redirect' => $url)));
                    }
                    else 
                    {
                        exit(json_encode(array('notice' => __('This user does not provide email for online transaction', FV_DOMAIN))));
                    }
                }
                else 
                {
                    $aVals = array('status' => 'APPROVED', 
                                    'response_message' => $response_message,
                                    'processedDate' => date('Y-m-d H:i:s'));
                    if(self::$payment->updateWithdraw($withdrawlID, $aVals))
                    {
                        exit(json_encode(array('result' => __('Successfully updated', FV_DOMAIN))));
                    }
                    else
                    {
                        exit(json_encode(array('result' => __('Something went wrong! Please try again.', FV_DOMAIN))));
                    }
                }
            }
            else
            {
                exit(json_encode(array('result' => __('Something went wrong! Please try again.', FV_DOMAIN))));
            }
        }
    }
    
    /////////////////////////////////view result/////////////////////////////////
    public static function viewResult()
    {
        $iPoolID = $_POST['iPoolID'];
        $aFights = self::$pools->getFights($iPoolID);

        $sResult = '';
        $count = 0;
        if($aFights != null)
        {
            $sResult .= '<div id="resultMessage"></div>
                        <form id="formResult">
                        <input type="hidden" name="val[poolID]" value="'.$iPoolID.'" />';
            foreach($aFights as $aFight)
            {
                $count++;
                $aPool = self::$pools->getPools($aFight['poolID'], null, false, true);
                if($aPool['is_team'] == 0)
                {
                    $sFighterName1 = self::$fighters->getFighterName($aFight['fighterID1'], true);
                    $sFighterName2 = self::$fighters->getFighterName($aFight['fighterID2'], true);
                    $teamOrFighterHeader = "Fighter";
                    $sHtmlType = self::viewFighterHtmlType($aFight['fightID'], $aFight['methodID'], $aFight['roundID'], $aFight['minuteID']);
                }
                else 
                {
                    $sFighterName1 = self::$teams->getTeamName($aFight['fighterID1'], true);
                    $sFighterName2 = self::$teams->getTeamName($aFight['fighterID2'], true);
                    $teamOrFighterHeader = "Team";
                    $sHtmlType = self::viewTeamHtmlType($aFight['fightID'], $aFight['team1score'], $aFight['team2score']);
                }
                
                $aFight['fighterID1'] == $aFight['winnerID'] ? $win1 = 'selected = "true"' : $win1 = null;
                $aFight['fighterID2'] == $aFight['winnerID'] ? $win2 = 'selected = "true"' : $win2 = null;
                $sResult .= '<div class="fight_container">
                                <div class="title_area">
                                    <div class="fight_number_title">'.$aFight['name'].'</div>
                                </div>
                                <table>
                                    <tr>
                                        <th>'.$teamOrFighterHeader.' 1</th>
                                        <th>'.$teamOrFighterHeader.' 2</th>
                                    </tr>
                                    <tr>
                                        <td>'.$sFighterName1.'</td>
                                        <td>'.$sFighterName2.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <div class="table">
                                                <div class="table_left">
                                                    '.__('Winner', FV_DOMAIN).':
                                                </div>
                                                <div class="table_right">
                                                    <select data-name="rounds" name="val[winnerID]['.$aFight['fightID'].']">
                                                        <option value="">Please select winner</option>
                                                        <option value="'.$aFight['fighterID1'].'" '.$win1.'>'.$sFighterName1.'</option>
                                                        <option value="'.$aFight['fighterID2'].'" '.$win2.'>'.$sFighterName2.'</option>
                                                    </select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    '.$sHtmlType.'
                                </table>
                            </div>';
            }
            $sResult .= '</form>';
        }
        exit($sResult);
    }
    
    private static function viewFighterHtmlType($fightID, $methodID = null, $roundID = null, $minuteID = null)
    {
        $sResult =  self::viewMethodOfVictoryHtml($fightID, $methodID).
                    self::viewRoundHtml($fightID, $roundID).
                    self::viewMinuteHtml($fightID, $minuteID);
        return $sResult;
    }
    
    private static function viewTeamHtmlType($fightID, $team1score = 0, $team2score = 0)
    {
        $sResult =  '<tr>
                        <td colspan="6">
                            <div class="table">
                                <div class="table_left">
                                    '.__('Team Score', FV_DOMAIN).' 1:
                                </div>
                                <div class="table_right">
                                    <input type="text" size="10" name="val[team1score]['.$fightID.']" value="'.$team1score.'">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div class="table">
                                <div class="table_left">
                                    '.__('Team Score', FV_DOMAIN).' 2:
                                </div>
                                <div class="table_right">
                                    <input type="text" size="10" name="val[team2score]['.$fightID.']" value="'.$team2score.'">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </td>
                    </tr>';
        return $sResult;
    }

    private static function viewMethodOfVictoryHtml($fightID, $methodID = null)
    {
        $aMethods = self::$fighters->getMethods();
        $sResult = '<td colspan="6">
                                        <div class="table">
                                            <div class="table_left">
                                                '.__('Method of victory', FV_DOMAIN).':
                                            </div>
                                            <div class="table_right">
                                                <select data-name="rounds" name="val[methodID]['.$fightID.']">
                                                <option value="-1">Please select method</option>';
        foreach($aMethods as $aMethod)
        {
            $select = null;
            if($aMethod['methodID'] == $methodID)
            {
                $select = 'selected="true"';
            }
            $sResult .= '<option value="'.$aMethod['methodID'].'" '.$select.'>'.$aMethod['description'].'</option>';
        }
        $sResult .= '                     </select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </td>
                                </tr>';
        return $sResult;
    }
    
    private static function viewRoundHtml($fightID, $roundID = null)
    {
        $aRounds = self::$fighters->getRounds();
        $sResult = '<td colspan="6">
                                        <div class="table">
                                            <div class="table_left">
                                                '.__('Round', FV_DOMAIN).':
                                            </div>
                                            <div class="table_right">
                                                <select data-name="rounds" name="val[roundID]['.$fightID.']">
                                                <option value="-1">Please select round</option>';
        foreach($aRounds as $aRound)
        {
            $select = null;
            if($aRound == $roundID)
            {
                $select = 'selected="true"';
            }
            $sResult .= '<option value="'.$aRound.'" '.$select.'>'.$aRound.'</option>';
        }
        $sResult .= '                     </select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </td>
                                </tr>';
        return $sResult;
    }
    
    private static function viewMinuteHtml($fightID, $minuteID = null)
    {
        $aMinutes = self::$fighters->getMinutes();
        $sResult = '<td colspan="6">
                                        <div class="table">
                                            <div class="table_left">
                                                '.__('Minute', FV_DOMAIN).':
                                            </div>
                                            <div class="table_right">
                                                <select data-name="rounds" name="val[minuteID]['.$fightID.']">
                                                <option value="-1">Please select minute</option>';
        foreach($aMinutes as $aMinute)
        {
            $select = null;
            if($aMinute['minuteID'] == $minuteID)
            {
                $select = 'selected="true"';
            }
            $sResult .= '<option value="'.$aMinute['minuteID'].'" '.$select.'>'.$aMinute['description'].'</option>';
        }
        $sResult .= '                     </select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </td>
                                </tr>';
        return $sResult;
    }
    /////////////////////////////////end view result/////////////////////////////////
    
    /////////////////////////////////update complete/////////////////////////////////
    public static function updateResult()
    {
        $aVals = $_POST['val'];
        $aFights = self::$pools->getFights($aVals['poolID']);
        $success = true;
        foreach($aFights as $aFight)
        {
            $fightID = $aFight['fightID'];
            $data = array('poolID' => $aFight['poolID'],
                          'fightID' => $fightID,
                          'winnerID' => $aVals['winnerID'][$fightID],
                          'methodID' => isset($aVals['methodID'][$fightID]) ? $aVals['methodID'][$fightID] : '-1',
                          'roundID' => isset($aVals['roundID'][$fightID]) ? $aVals['roundID'][$fightID] : '-1',
                          'minuteID' => isset($aVals['minuteID'][$fightID]) ? $aVals['minuteID'][$fightID] : '-1',
                          'team1score' => isset($aVals['team1score'][$fightID]) ? $aVals['team1score'][$fightID] : 0,
                          'team2score' => isset($aVals['team2score'][$fightID]) ? $aVals['team2score'][$fightID] : 0);
            if(!self::$pools->updateFightResult($data))
            {
                $success = false;
            }
        }
        if(!$success)
        {
            exit('<div class=\"error_message\">'.__('Something went wrong! Please try again', FV_DOMAIN).'</div>');
        }
        exit('Successfully updated');
    }
    
    public static function updatePoolComplete()
    {
        $iPoolID = $_POST['iPoolID'];
        $status = $_POST['status'];
        if(!self::$pools->isPoolExist($iPoolID))
        {
            exit(json_encode(array('notice' => __('This pool does not exist', FV_DOMAIN))));
        }
        else if($status != "NEW" && $status != "COMPLETE")
        {
            exit(json_encode(array('notice' => __('Please select status', FV_DOMAIN))));
        }
        else if(!self::$pools->isPoolResultsUpdated($iPoolID))
        {
            exit(json_encode(array('notice' => __('Please update pool result', FV_DOMAIN))));
        }
        else if($status == 'COMPLETE')
        {
            if(self::$pools->updatePoolComplete($iPoolID))
            {
                exit(json_encode(array('result' => 'Successfully updated')));
            }
            else 
            {
                exit(json_encode(array('notice' => __('Something went wrong! Please try again', FV_DOMAIN))));
            }
        }
        else 
        {
            self::$pools->updatePoolStatus($iPoolID, 'NEW');
            exit(json_encode(array('result' => __('Successfully updated', FV_DOMAIN))));
        }
    }
    
    public static function reverseResult()
    {
        $result = self::$pools->reverseResult($_POST['poolID']);
        switch($result)
        {
            case 2:
                exit(json_encode(array('notice' => __('Event does not exist', FV_DOMAIN))));
                break;
            case 1:
                exit(json_encode(array('result' => __('Successfully reversed', FV_DOMAIN))));
                break;;
            default :
                exit(json_encode(array('notice' => __('Something went wrong! Please try again', FV_DOMAIN))));
        }
    }
    /////////////////////////////////end update complete/////////////////////////////////
    
    public static function showPoolStatisticDetail()
    {
        $leagueID = $_POST['leagueID'];
        $aLeagues = self::$statistic->eventStatistic($leagueID);
        exit(json_encode($aLeagues));
    }
    
    /////////////////////////////////v2/////////////////////////////////
    public static function viewPlayerDraftResult()
    {
        $iPoolID = $_POST['iPoolID'];
        $data = self::$pools->viewPlayerDraftResult($iPoolID);
        exit(json_encode($data));
    }
    
    public static function loadPlayerPoints()
    {
        $poolID = $_POST['poolID'];
        $fightID = $_POST['fightID'];
        $roundID = $_POST['roundID'];
        $playerID = $_POST['playerID'];
        $page = $_POST['page'];
        
        //scoring category
        $item_per_page = 10;
        $aScorings = self::$scoringcategory->getPlayerStatsScoring($poolID, $fightID, $roundID, $playerID, $item_per_page, $page);
        $big = 999999999;
        $paging = paginate_links( array(
            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format' => '#',
            'current' => max($page, get_query_var('paged') ),
            'total' => ceil($aScorings['total'] / $item_per_page)
        ));
        $aScorings['paging'] = $paging;
        exit(json_encode($aScorings));
    }
    
	public static function addMoneyByCoupon()
    {
        if(empty($_POST['coupon_code']))
        {
            exit(json_encode(array('notice' => __('Please input coupon code', FV_DOMAIN))));
        }
        else if(!self::$coupon->isCouponCodeExist($_POST['coupon_code'], CP_ACTION_ADD_MONEY))
        {
            exit(json_encode(array('notice' => __('This code does not exist', FV_DOMAIN))));
        }
        else if(self::$coupon->isCouponCodeUsed($_POST['coupon_code'], CP_ACTION_ADD_MONEY))
        {
            exit(json_encode(array('notice' => __('This code has already used', FV_DOMAIN))));
        }
        else if(self::$coupon->isCouponCodeLimit($_POST['coupon_code'], CP_ACTION_ADD_MONEY))
        {
            exit(json_encode(array('notice' => __('This code has reached to limit', FV_DOMAIN))));
        }
        else 
        {
            $coupon = self::$coupon->getCouponByCode($_POST['coupon_code'], CP_ACTION_ADD_MONEY);
            if(!empty($coupon))
            {
                $user = self::$payment->getUserData();
                $discount_value = self::$coupon->getTotalDiscountValue($coupon->discount_type, $coupon->discount_value, $user['balance']);
                if(self::$payment->updateUserBalance($discount_value, false, 0, get_current_user_id()))
                {
                    self::$payment->addFundhistory($discount_value, 0, ($user['balance'] + $discount_value), "COUPON", "ADD", get_current_user_id(), null, null, null, "completed");
                    self::$coupon->addCouponUsed($coupon->id, get_current_user_id());
                    exit(json_encode(array('result' => __('Successfully added', FV_DOMAIN))));
                }
            }
            exit(json_encode(array('notice' => __('Something went wrong, please try again', FV_DOMAIN))));
        }
    }
}
?>