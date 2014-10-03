<?php
include_once("paypal.php");
define('PAYPAL', 'PAYPAL');
class Payment
{
    function validEmail($email)
    {
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
        if (preg_match($regex, $email)) {
            return true;
        } else { 
            return false;
        } 
    }
    
    function isGatewayExist($data)
    {
        $gateway = $this->viewGateway();
        if(in_array($data, $gateway))
        {
            return true;
        }
        return false;
    }
    
    function viewGateway()
    {
        return array(PAYPAL);
    }
    
    function changeCashToCredit($iCash)
    {
        $money = 1;
        $credit = (int)get_option('fanvictor_credit_to_cash') < 1 ? 1 : get_option('fanvictor_credit_to_cash');
        return floor($iCash * $money / $credit);
    }
    
    function changeCreditToCash($iCredit)
    {
        $money = 1;
        $credit = (int)get_option('fanvictor_cash_to_credit') < 1 ? 1 : get_option('fanvictor_cash_to_credit');
        return floor($iCredit * $money / $credit);
    }
    
    function onlineTransaction($gateway = PAYPAL, $aSettings)
    {
        switch($gateway)
        {
            case PAYPAL:
                $paypal = new Paypal();
                return $paypal->parseData($aSettings);
                break;
            default :
                return false;
        }
    }
    
    function confirmPaypal()
    {
        //Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
        if(isset($_GET["token"]) && isset($_GET["PayerID"]))
        {
            //we will be using these two variables to execute the "DoExpressCheckoutPayment"
            //Note: we haven't received any payment yet.

            $token = $_GET["token"];
            $payer_id = $_GET["PayerID"];

            //get session variables
            $ItemName 			= $_SESSION['ItemName']; //Item Name
            $ItemPrice 			= $_SESSION['ItemPrice'] ; //Item Price
            $ItemNumber 		= $_SESSION['ItemNumber']; //Item Number
            $ItemDesc 			= $_SESSION['ItemDesc']; //Item Number
            $ItemQty 			= $_SESSION['ItemQty']; // Item Quantity
            $ItemTotalPrice 	= $_SESSION['ItemTotalPrice']; //(Item Price x Quantity = Total) Get total amount of product; 

            $padata = 	'&TOKEN='.urlencode($token).
                        '&PAYERID='.urlencode($payer_id).
                        '&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE").

                        //set item info here, otherwise we won't see product details later	
                        '&L_PAYMENTREQUEST_0_NAME0='.urlencode($ItemName).
                        '&L_PAYMENTREQUEST_0_NUMBER0='.urlencode($ItemNumber).
                        '&L_PAYMENTREQUEST_0_DESC0='.urlencode($ItemDesc).
                        '&L_PAYMENTREQUEST_0_AMT0='.urlencode($ItemPrice).
                        '&L_PAYMENTREQUEST_0_QTY0='. urlencode($ItemQty).
                        '&PAYMENTREQUEST_0_ITEMAMT='.urlencode($ItemTotalPrice).
                        '&PAYMENTREQUEST_0_AMT='.urlencode($ItemTotalPrice).
                        '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode('USD');
            
            $paypal = new MyPayPal(Phpfox::getParam('fanvictor.fanvictor_paypal_username'),
                                   Phpfox::getParam('fanvictor.fanvictor_paypal_password'),
                                   Phpfox::getParam('fanvictor.fanvictor_paypal_signature'),
                                   Phpfox::getParam('fanvictor.paypal_test'));
            return $paypal->confirm($padata);
        }
        return false;
    }
    
    ###########################
	#
	#       USER
	#
	###########################
    function getUserData($userID = null)
    {
        $user_id = (int)get_current_user_id();
        if((int)$userID > 0)
        {
            $user_id = $userID;
        }
        
        global $wpdb;
        $table_user = $wpdb->prefix.'users';
        $table_user_extended = $wpdb->prefix.'user_extended';
        $sCond = "WHERE u.ID = ".$user_id;
        $sql = "SELECT u.*, u.display_name as full_name, u.user_email as email, u.user_login as user_name, IFNULL(ue.balance, 0.00) as balance "
             . "FROM $table_user u "
             . "LEFT JOIN $table_user_extended ue ON ue.user_id = u.ID "
             . $sCond;
        $data = $wpdb->get_row($sql, ARRAY_A);
        return $data;
    }
    
    public function isUserExtendedExist($user_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'user_extended';
        $sCond = "WHERE user_id = ".(int)$user_id;
        $sql = "SELECT COUNT(*) "
             . "FROM $table_name "
             . $sCond;
        $data = $wpdb->get_var($sql);
        if($data == 1)
        {
            return true;
        }
        return false;
    }
    
    public function isUserEnoughMoneyToJoin($prize = 0, $leagueID = null)
    {
        if($this->isMakeBetForLeague($leagueID))
        {
            return true;
        }
        $user = $this->getUserData();
        if((int)$user['balance'] < $prize)
        {
            return false;
        }
        return true;
    }
    
    public function updateUserBalance($prize = 0, $decrease = false, $leagueID = 0, $user_id = null)
    {
        global $wpdb;
        
        $user = $this->getUserData($user_id);
        $deposit = $user['balance'] + $prize;
        if($decrease)
        {
            $deposit = $user['balance'] - $prize;
        }
        
        $values = array('user_id' => $user['ID'], 'balance' => $deposit);
        $table_name = $wpdb->prefix.'user_extended';
        if($this->isUserExtendedExist($user['ID']))
        {
            return $wpdb->update($table_name, $values, array('user_id' => $user['ID']));
        }
        else 
        {
            $result = $wpdb->insert($table_name, $values);
            if($result === false)
            {
                return false;
            }
            return true;
        }
    }
    
    public function isUserPaymentInfoExist($aVals)
    {
        global $wpdb;
        $sCond = "WHERE user_id = ".get_current_user_id()." AND gateway = '".$aVals['gateway']."'";
        $table_name = $wpdb->prefix."user_payment";
        $sql = "SELECT user_id "
             . "FROM $table_name "
             . $sCond;
        $aData = $wpdb->get_row($sql);
        if(count($aData) == 1)
        {
            return true;
        }
        return false;
    }
    
    function getUserPaymentInfo($gateway = PAYPAL, $user_id = null)
    {
        global $wpdb;
        $sCond = "WHERE up.user_id = ".(int)get_current_user_id()." AND up.gateway = '$gateway'";
        if((int)$user_id > 0)
        {
            $sCond = "WHERE up.user_id = ".(int)get_current_user_id();
        }
        $table_userpayment = $wpdb->prefix."user_payment";
        $table_user = $wpdb->prefix."users";
        $table_userextended = $wpdb->prefix."user_extended";
        $sql = "SELECT up.*, u.display_name as full_name, IFNULL(ue.balance, 0.00) as balance "
             . "FROM $table_userpayment up "
             . "INNER JOIN $table_user u ON up.user_id = u.ID "
             . "LEFT JOIN $table_userextended ue ON ue.user_id = u.ID "
             . $sCond;
        $data = $wpdb->get_row($sql);
        $data = json_decode(json_encode($data), true);
        return $data;
    }
    
    public function addUserPaymentInfo($aVals)
    {
        global $wpdb;
        $aVals['user_id'] = get_current_user_id();
        $aVals['time_stamp'] = current_time('timestamp');
        $aVals['time_update'] = current_time('timestamp');
        return $wpdb->insert($wpdb->prefix."user_payment", $aVals);
    }
    
    public function updateUserPaymentInfo($aVals)
    {
        global $wpdb;
        $aVals['time_update'] = current_time('timestamp');
        return $wpdb->update($wpdb->prefix."user_payment", $aVals, array('user_id' => get_current_user_id()));
    }
    
    ###########################
	#
	#       FUNDHISTORY
	#
	###########################
    public function isMakeBetForLeague($leagueID)
    {
        global $wpdb;
        $sCons = "WHERE userID = ".get_current_user_id()." AND leagueID = ".(int)$leagueID;
        $table_name = $wpdb->prefix.'fundhistory';
        $sql = "SELECT count(*) "
             . "FROM $table_name "
             . $sCons;
        $data = $wpdb->get_var($sql);
        if($data == 1)
        {
            return true;
        }
        return false;
    }
    
    public function isPaypalCompleted($fundshistoryID)
    {
        global $wpdb;
        $sCons = "WHERE userID = ".get_current_user_id()." AND transactionID != '' AND fundshistoryID = ".(int)$fundshistoryID;
        $table_name = $wpdb->prefix.'fundhistory';
        $sql = "SELECT count(*) "
             . "FROM $table_name "
             . $sCons;
        $data = $wpdb->get_var($sql);
        if($data == 1)
        {
            return true;
        }
        return false;
    }
    
    public function getFundhistory($aConds, $sSort = 'fundshistoryID DESC', $iPage = '', $iLimit = '')
	{	
        global $wpdb;
        $table_fundhistory = $wpdb->prefix."fundhistory";
        $aConds .= 'userID = '.(int)get_current_user_id();
        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT COUNT(*) "
             . "FROM $table_fundhistory "
             . $sCond;
        $iCnt = $wpdb->get_var($sql);

        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as date "
             . "FROM $table_fundhistory "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit ";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        
        return array($iCnt, $aRows);
	}
    
    public function parseFunhistoryData($aDatas = null)
    {
        if($aDatas != null)
        {
            foreach($aDatas as $k => $aData)
            {
                if($aData['operation'] == 'ADD')
                {
                    $aDatas[$k]['amount'] = "+".$aData['amount'];
                }
                else if($aData['operation'] == 'DEDUCT')
                {
                    $aDatas[$k]['amount'] = "-".$aData['amount'];
                }
            }
        }
        return $aDatas;
    }
    
    public function addFundhistory($prize = 0, $leagueID = 0, $newBalance = 0, $type, $operation, $user_id = null, $gateway = null, $reason = null, $changeRate = null)
    {
        global $wpdb;
        if($prize > 0)
        {
            $userID = (int)get_current_user_id();
            if((int)$user_id > 0)
            {
                $userID = $user_id;
            }
            $values = array('userID' => $userID, 
                            'amount' => $prize,
                            'operation' => $operation,
                            'type' => $type,
                            'new_balance' => $newBalance,
                            'gateway' => $gateway,
                            'reason' => $reason,
                            'cash_to_credit' => $changeRate,
                            'leagueID' => $leagueID,
                            'date' => date('Y-m-d H:i:s'));
            $table_name = $wpdb->prefix.'fundhistory';
            $wpdb->insert($table_name, $values);
            return $wpdb->insert_id;
        }
        return 0;
    }
    
    public function updateFundhistory($iId, $aValues, $user_id = null)
    {
        global $wpdb;
        $iUserId = get_current_user_id();
        if((int)$user_id > 0)
        {
            $iUserId = $user_id;
        }
        $user = $this->getUserData($iUserId);
        $aValues['new_balance'] = $user['balance'];
        return $wpdb->update($wpdb->prefix.'fundhistory', $aValues, array('fundshistoryID' => (int)$iId));
    }
    
    public function deleteFundhistory($iId)
    {
        return $this->database()->delete(Phpfox::getT('fundhistory'), 'fundshistoryID = '.(int)$iId);
    }
    
    ###########################
	#
	#       WITHDRAWLS
	#
	###########################
    public function isAllowWithdraw($amount = 0)
    {
        $aUser = $this->getUserData();
        if($aUser['balance'] >= $amount)
        {
            return true;
        }
        return false;
    }
    
    public function getWithdraw($iId = null)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'withdrawls';
        $sCond = "WHERE userID = ".get_current_user_id();
        if((int)$iId > 0)
        {
            $sCond = "WHERE withdrawlID = ".$iId;
        }
        $sql = "SELECT *, DATE_FORMAT(requestDate, '%Y-%m-%d') as requestDate, DATE_FORMAT(processedDate, '%Y-%m-%d') as processedDate "
             . "FROM $table_name "
             . $sCond." "
             . "ORDER BY withdrawlID DESC ";
        if((int)$iId > 0)
        {
            $data = $wpdb->get_row($sql);
        }
        else
        {
            $data = $wpdb->get_results($sql);
        }
        $data = json_decode(json_encode($data), true);
        return $data;
    }
    
    public function getListWithdraw($aConds, $sSort = 'withdrawlID DESC', $iPage = '', $iLimit = '')
	{	
        global $wpdb;
        $table_name = $wpdb->prefix."withdrawls";
        $aConds .= 'userID = '.(int)get_current_user_id();
        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT COUNT(*) "
             . "FROM $table_name "
             . $sCond;
        $iCnt = $wpdb->get_var($sql);

        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT *, DATE_FORMAT(requestDate, '%Y-%m-%d') as requestDate, DATE_FORMAT(processedDate, '%Y-%m-%d') as processedDate "
             . "FROM $table_name "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit ";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        
        return array($iCnt, $aRows);
	}
    
    public function addWithdraw($amount = 0, $reson = null, $user_id = null, $new_balance = 0)
    {
        global $wpdb;
        $userID = get_current_user_id();
        if((int)$user_id > 0)
        {
            $userID = $user_id;
        }
        $values = array('userID' => $userID, 
                        'amount' => $amount,
                        'real_amount' => $this->changeCashToCredit($amount),
                        'credit_to_cash' => get_option('fanvictor_credit_to_cash'), 
                        'new_balance' => $new_balance,
                        'reason' => $reson,
                        'requestDate' => date('Y-m-d H:i:s'));
        return $wpdb->insert($wpdb->prefix.'withdrawls', $values);
    }
    
    public function updateWithdraw($iId, $aValues)
    {
        global $wpdb;
        return $wpdb->update($wpdb->prefix."withdrawls", $aValues, array('withdrawlID' => (int)$iId));
    }
}
?>