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
        
        $funcs = array('loadCbOrgs', 'loadCbFighters', 'loadCbTeams', 'viewResult',
                       'updateResult', 'updatePoolComplete', 'activeOrgs', 'sendUserCredits',
                       'sendUserWithdrawls', 'loadPoolsByOrg', 'calculatePrizes', 'loadFights',
                       'LeagueResults', 'userpicks', 'LiveLeagues', 'sendInviteFriend',
                       'addCredits', 'loadUserBalance', 'requestPayment', 'accountInfo',
                       'updateNewContests', 'showLeagueDetails', 'showPoolStatisticDetail', 'viewPoolFixture',
                       'loadUser');
        foreach($funcs as $func)
        {
            add_action("wp_ajax_$func", array('Ajax', $func));
        }
		
		if(get_current_user_id() == 0 && isset($_POST['action']))
        {
            $func = $_POST['action'];
            self::$func();
        }
    }

	public static function showLeagueDetails()
	{
        $iLeagueId = $_POST['leagueId'];
        $sData = self::$fanvictor->showLeagueDetails($iLeagueId);

        exit($sData);
	}
    
    public static function updateNewContests()
    {
        $aGameGrids = self::$fanvictor->getNewgames();
        $aGameGrids = json_decode($aGameGrids);
        $sResult = null;
        if(isset($aGameGrids->flexgrid_leagues->rows))
        {
            $aGameGrids = $aGameGrids->flexgrid_leagues->rows;
            foreach($aGameGrids as $aGameGrid)
            {
                $img = null;
                if($aGameGrid->cell[11] != null)
                {
                    $img = Pools::replaceSuffix($aGameGrid->cell[11]);
                    $img = '<img src="'.FANVICTOR_IMAGE_URL.$img.'" width="30" />&nbsp;';
                }
                $sResult .= '<tr>
                                <td>'.$img.' '.$aGameGrid->cell[1].'</td>
                                <td>'.$aGameGrid->cell[10].'</td>
                                <td>'.$aGameGrid->cell[3].' / '.$aGameGrid->cell[4].'</td>
                                <td>'.$aGameGrid->cell[5].' / '.$aGameGrid->cell[6].'</td>
                                <td>'.$aGameGrid->cell[7].'</td>
                                <td style="text-align:center">'.$aGameGrid->cell[8].'</td>
                            </tr>';
            }
        }
        exit($sResult);
    }
    
    public static function LeagueResults()
    {
        $iLeagueId = $_POST['leagueId'];
        $isLive = isset($_POST['isLive']) ? $_POST['isLive'] : '';
        $sData = self::$fanvictor->leagueResults($iLeagueId, $isLive);
        exit($sData);
    }
    
    public static function LiveLeagues()
    {
        $sData = self::$fanvictor->getLiveContests();
        exit($sData);
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
            exit(json_encode(array('notice' => __('You have not selected any friends to invite'))));
        }
        else if($data['importleagueID'] == "" || $data['importleagueID'] == "0" )
        {
            exit(json_encode(array('notice' => __('Sorry the system detected a spam attempt please contact support'))));
        }
        else if(!self::isValidInviteEmail($data['emails']))
        {
            exit(json_encode(array('notice' => __('Please enter a valid email address'))));
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
        $poolID = $_POST['poolID'];
        $type = $_POST['type'];
        $structure = $_POST['structure'];
        $size = $_POST['size'];
        $entryFee = $_POST['entry_fee'];
        $prizes = self::$pools->calculatePrizes($poolID, $type , $structure, $size, $entryFee);
        
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
            $aPool = self::$pools->getPools($iPoolID);
            if($aPool['type'] == 'MMA' || $aPool['type'] == 'BOXING')
            {
                $teamOrFighterHeader = __("Fighter");
            }
            else 
            {
                $teamOrFighterHeader = __("Team");
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
            $sResult = '<center>'.__("No fixtures").'</center>';
        }
        exit($sResult);
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
                exit(json_encode(array('notice' => __('Credits not valid'))));
            }
            else if(!self::$payment->isGatewayExist($gateway))
            {
                exit(json_encode(array('notice' => __('Please select gateway'))));
            }
            else
            {
                $money = self::$payment->changeCreditToCash($credits);
                $iFundHitoryId = self::$payment->addFundhistory($money, 0, 0, 'DEPOSIT', 'ADD', null, $gateway, null, (int)get_option('fanvictor_cash_to_credit'));
                if((int)$iFundHitoryId > 0)
                {
                    $aSettings = array('paypal_email' => get_option('paypal_email_account'),
                                       'business' => get_option('paypal_email_account'),
                                       'item_name' => "Deposit ".$iFundHitoryId,
                                       'item_number' => 1,
                                       'amount' => $money,
                                       'notify_url' => FANVICTOR_URL_NOTIFY_ADD_FUNDS,
                                       'return' => FANVICTOR_URL_SUCCESS_ADD_FUNDS,
                                       'cancel_return' => FANVICTOR_URL_ADD_FUNDS,
									   'custom' => get_current_user_id().'|'.$iFundHitoryId.'|'.$credits);
                    $sUrl = self::$payment->onlineTransaction($gateway, $aSettings);
                    if($sUrl)
                    {
                        unset($_SESSION['is_transaction']);
                        exit(json_encode(array('result' => $sUrl)));
                    }
                    else
                    {
                        self::$payment->deleteFundhistory($iFundHitoryId);
                        exit(json_encode(array('notice' => __('Something went wrong! Please try again.'))));
                    }
                }
                else
                {
                    exit(json_encode(array('notice' => __('Something went wrong! Please try again.'))));
                }
            }
        }
        else 
        {
            exit(json_encode(array('notice' => __('You are in transaction session. To start new session please refresh this page'))));
        }
    }
    
    public static function requestPayment()
    {
        $credits = $_POST['credits'];
        $reason = $_POST['reason'];
        if(!is_numeric($credits) || (int)$credits < 1)
        {
            exit(json_encode(array('notice' => 'Credits not valid')));
        }
        else if(!self::$payment->isAllowWithdraw($credits))
        {
            exit(json_encode(array('notice' => __('Credits must not exceed your available balance'))));
        }
        else if(self::$payment->updateUserBalance($credits, true, 0))
        {
            $aUser = self::$payment->getUserData();
            self::$payment->addWithdraw($credits, $reason, $aUser['ID'], $aUser['balance']);
            exit(json_encode(array('result' => __('Your request has been sent'), 'redirect' => FANVICTOR_URL_REQUEST_HISTORY)));
        }
        else
        {
            exit(json_encode(array('notice' => __('Something went wrong! Please try again.'))));
        }
    }
    
    public static function accountInfo()
    {
        $aVals = $_POST['val'];

        //check valid
        if(!self::$payment->isGatewayExist($aVals['gateway']))
        {
            exit(json_encode(array('notice' => __('Please select gateway'))));
        }
        else if(empty($aVals['email']))
        {
            exit(json_encode(array('notice' => __('Provide your email'))));
        }
        else
        {
            if(!self::$payment->isUserPaymentInfoExist($aVals))
            {
                if(self::$payment->addUserPaymentInfo($aVals) === false)
                {
                    exit(json_encode(array('notice' => __('Something went wrong! Please try again.'))));
                }
                else
                {
                    exit(json_encode(array('result' => __('Successfully added'))));
                }
            }
            else 
            {
                if(self::$payment->updateUserPaymentInfo($aVals) === false)
                {
                    exit(json_encode(array('notice' => __('Something went wrong! Please try again.'))));
                }
                else
                {
                    exit(json_encode(array('result' => __('Successfully updated'))));
                }
            }
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
        exit(json_encode(array('notice' => __('Something went wrong! Please try again.'))));
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
        $result = '<option value="">--'.__('Please select fighter').'--</option>';
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
        $result = '<option value="">--'.__('Please select team').'--</option>';
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
            exit(json_encode(array('notice' => __('Credits not valid'))));
        }
        else if($task == 'remove' && !self::$payment->isAllowWithdraw($credits))
        {
            exit(json_encode(array('notice' => __('Credits must not exceed your available balance'))));
        }
        else
        {
            switch($task)
            {
                case 'remove':
                    $decrease = true;
                    $operation = 'DEDUCT';
                    $msg = __('Successfully deducted');
                    break;
                default :
                    $decrease = false;
                    $operation = 'ADD';
                    $msg = __('Successfully added');
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
                exit(json_encode(array('notice' => __('Something went wrong! Please try again.'))));
            }
        }
    }
    
    public static function sendUserWithdrawls()
    {
        $withdrawlID = $_POST['withdrawlID'];
        $action = isset($_POST['status']) ? $_POST['status'] : '';
        $gateway = $_POST['gateway'];
        $response_message = $_POST['response_message'];
        if($action != 'APPROVED' && $action != 'DECLINED')
        {
            exit(json_encode(array('notice' => __('Please select action'))));
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
                    exit(json_encode(array('result' => __('Successfully updated'))));
                }
                else
                {
                    exit(json_encode(array('result' => __('Something went wrong! Please try again.'))));
                }
            }
            else if($action == 'APPROVED' && $aWithdrawl['status'] != 'APPROVED')
            {
                $aUserPaymentInfo = self::$payment->getUserPaymentInfo($gateway, $aWithdrawl['userID']);
                if(isset($aUserPaymentInfo['email']) && $aUserPaymentInfo['email'] != null)
                {
                    $paypal = new MyPayPal(null, null, null, get_option('paypal_test'));
                    $aUserPaymentInfo['online'] = true;
                    $aUserPaymentInfo['name'] = "withdraw ".$withdrawlID;
                    $aUserPaymentInfo['amount'] = $aWithdrawl['real_amount'];
                    $aUserPaymentInfo['paypal_url'] = $paypal->getPaypalUrl();
                    $aUserPaymentInfo['cancel_url'] = admin_url().'admin.php?page=withdrawls';
                    $aUserPaymentInfo['return_url'] = FANVICTOR_URL_SUCCESS_WITHDRAWLS;
                    $aUserPaymentInfo['notify_url'] = FANVICTOR_URL_NOTIFY_WITHDRAWLS;
					$aUserPaymentInfo['custom'] = $withdrawlID.'|'.$response_message;
                    exit(json_encode($aUserPaymentInfo));
                }
                else 
                {
                    exit(json_encode(array('notice' => __('This user does not provide email for online transaction'))));
                }
            }
            else
            {
                exit(json_encode(array('result' => __('Something went wrong! Please try again.'))));
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
                $aPool = self::$pools->getPools($aFight['poolID']);
                if($aPool['type'] == 'MMA' || $aPool['type'] == 'BOXING')
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
                                                    '.__('Winner').':
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
                                    '.__('Team Score').' 1:
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
                                    '.__('Team Score').' 2:
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
                                                '.__('Method of victory').':
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
                                                '.__('Round').':
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
                                                '.__('Minute').':
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
            exit('<div class=\"error_message\">'.__('There are something wrong! Please try again').'</div>');
        }
        exit('Successfully updated');
    }
    
    public static function updatePoolComplete()
    {
        $iPoolID = $_POST['iPoolID'];
        $status = $_POST['status'];
        if(!self::$pools->isPoolExist($iPoolID))
        {
            exit(json_encode(array('notice' => __('This pool does not exist'))));
        }
        else if($status != "NEW" && $status != "COMPLETE")
        {
            exit(json_encode(array('notice' => __('Please select status'))));
        }
        else if(!self::$pools->isPoolResultsUpdated($iPoolID))
        {
            exit(json_encode(array('notice' => __('Please update pool result'))));
        }
        else if($status == 'COMPLETE')
        {
            if(self::$pools->updatePoolComplete($iPoolID))
            {
                self::$pools->updatePoolStatus($iPoolID, 'COMPLETE');
                exit(json_encode(array('result' => 'Successfully updated')));
            }
            else 
            {
                exit(json_encode(array('notice' => __('There are something wrong! Please try again'))));
            }
        }
        else 
        {
            self::$pools->updatePoolStatus($iPoolID, 'NEW');
            exit(json_encode(array('result' => __('Successfully updated'))));
        }
    }
    /////////////////////////////////end update complete/////////////////////////////////
    
    public static function showPoolStatisticDetail()
    {
        $poolID = $_POST['poolID'];
        $aLeagues = self::$statistic->viewLeagueDetail($poolID);
        $result = '';
        if($aLeagues != null)
        {
            $result .=  '<table class="wp-list-table widefat books">
                            <thead>
                                <tr>
                                    <th style="width: 30px">ID</th>	
                                    <th>'.__('League Name').'</th>	
                                    <th style="width: 40px">'.__('Class').'</th>
                                    <th style="width: 60px">'.__('Prizes').'</th>
                                    <th style="width: 60px">'.__('Awarded').'</th>
                                    <th style="width: 60px">'.__('Entry Fee').'</th>
                                    <th style="width: 40px">'.__('Size').'</th>
                                    <th style="width: 60px">'.__('Entries').'</th>
                                    <th style="width: 70px">'.__('Total Cash').'</th>
                                    <th style="width: 60px">'.__('Profit').'</th>
                                </tr>
                            </thead>
                            <tbody>';
            foreach($aLeagues as $iKey2 => $aLeague)
            {
                $result .= '<tr class="checkRow tr">
                                <td>
                                    '.$aLeague['leagueID'].'
                                </td>
                                <td>
                                    '.$aLeague['name'].'
                                </td>
                                <td>
                                    '.$aLeague['opponent'].'
                                </td>
                                <td>
                                    '.$aLeague['prize_structure'].'
                                </td>
                                <td>
                                    '.$aLeague['awarded'].'
                                </td>
                                <td>
                                    '.$aLeague['entry_fee'].'
                                </td>
                                <td>
                                    '.$aLeague['size'].'
                                </td>
                                <td>
                                    '.$aLeague['entries'].'
                                </td>
                                <td>
                                    '.$aLeague['total_cash'].'
                                </td>
                                <td>
                                    '.$aLeague['profit'].'
                                </td>
                            </tr>';
            }
            $result .= '</tbody></table>';
            exit(json_encode(array('result' => $result)));
        }
    }
}
?>