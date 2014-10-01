<?php
class User
{
    public function __construct() 
    {
        global $wpdb;
        $this->api_token = get_option('fanvictor_api_token');
        $this->api_url = get_option('fanvictor_api_url_admin');
        $this->_sTable = $wpdb->prefix.'users';
        $this->_sTableUserExtended = $wpdb->prefix.'user_extended';
        $this->_sTableWithdrawls = $wpdb->prefix.'withdrawls';	
    }
    
    public function getUsers($aConds, $sSort = 'u.ID ASC', $iPage = '', $iLimit = '')
	{	
        if($aConds != null && is_array($aConds))
        {
            $aConds = implode('AND', $aConds);
        }
        global $wpdb;
        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT COUNT(*) "
             . "FROM $this->_sTable "
             . $sCond;
        $iCnt = $wpdb->get_var($sql);
        
        $sql = "SELECT u.*, IFNULL(ue.balance, 0.00) as balance "
             . "FROM $this->_sTable u "
             . "LEFT JOIN $this->_sTableUserExtended ue ON ue.user_id = u.ID "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        
		return array($iCnt, $aRows);
	}
    
    public function getUser($userID)
    {
        global $wpdb;
        $sCond = "WHERE u.ID = ".(int)$userID;
        $sql = "SELECT u.*, IFNULL(ue.balance, 0.00) as balance "
             . "FROM $this->_sTable u "
             . "LEFT JOIN $this->_sTableUserExtended ue ON ue.user_id = u.ID "
             . $sCond;
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        $aRows = $this->parseUsersData($aRows);
        return isset($aRows[0]) ? $aRows[0] : null;
    }
    
    public function getUsersWithdrawls($aConds, $sSort = 'u.user_id ASC', $iPage = '', $iLimit = '')
	{	
        if($aConds != null && is_array($aConds))
        {
            $aConds = implode('AND', $aConds);
        }
        global $wpdb;
        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT COUNT(*) "
             . "FROM $this->_sTable u "
             . "INNER JOIN $this->_sTableWithdrawls w ON u.ID = w.userID "
             . $sCond;
        $iCnt = $wpdb->get_var($sql);

        $sql = "SELECT *, DATE_FORMAT(w.requestDate, '%Y-%m-%d') as requestDate "
             . "FROM $this->_sTable u "
             . "INNER JOIN $this->_sTableWithdrawls w ON u.ID = w.userID "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit ";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        
		return array($iCnt, $aRows);
	}
    
    public function parseUsersData($aDatas = null)
    {
        if($aDatas != null)
        {
            foreach($aDatas as $k => $aData)
            {
                $aDatas[$k]['payment_request_pending'] = $this->getWithdrawlsTotal($aData['ID']);
            }
        }
        return $aDatas;
    }
    
    public function getWithdrawlsTotal($userID, $status = "NEW")
    {	
        global $wpdb;
        $sCond = "WHERE status = '$status' AND userID = $userID";
        $sql = "SELECT SUM(amount) as amount "
             . "FROM $this->_sTableWithdrawls "
             . $sCond;
        $aData = $wpdb->get_row($sql);
        return $aData->amount;
    }
}

?> 
