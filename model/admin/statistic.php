<?php
class Statistic extends Model
{
    public function __construct() 
    {
        global $wpdb;
        $this->_sTableFundhistory = $wpdb->prefix.'fundhistory';	
        $this->_sTableUser = $wpdb->prefix.'users';	
    }
    
    public function getProfit($aLeagues = null)
    {
        global $wpdb;
        $result = null;
        $accumProfit = 0;
        $accumPayOut = 0;
        $accumCash = 0;
        $sql = "SELECT * "
             . "FROM $this->_sTableFundhistory ";
        $aFundhistorys = $wpdb->get_results($sql);
        if($aFundhistorys != null)
        {
            $leagueWinId = array();
            foreach($aFundhistorys as $aFundhistory)
            {
                if($aFundhistory->type == 'WIN' && (!in_array($aFundhistory->leagueID, $leagueWinId) || $leagueWinId == null))
                {
                    $leagueWinId[] = $aFundhistory->leagueID;
                }
            }
            foreach($aFundhistorys as $aFundhistory)
            {
                if($aFundhistory->type == "MAKE_BET")
                {
                    $accumCash += $aFundhistory->amount;
                }
                if($aFundhistory->type == "MAKE_BET" && $leagueWinId != null && 
                   in_array($aFundhistory->leagueID, $leagueWinId))
                {
                    $accumProfit += $aFundhistory->site_profit;
                }
                if($aFundhistory->type == "WIN")
                {
                    $accumPayOut += $aFundhistory->amount;
                }
            }
        }
        return array('accumProfit' => '$'.$accumProfit, 
                            'accumPayOut' => '$'.$accumPayOut, 
                            'accumCash' => '$'.$accumCash);
    }
    
    public function viewLeagueDetail($poolID)
    {
        $aLeagues = $this->getLeagues($poolID);
        if($aLeagues != null)
        {
            foreach($aLeagues as $k2 => $aLeague)
            {
                $tc = $aLeague["entry_fee"] * $aLeague["size"];
                $aUserpicks = $this->getUserpicks($aLeague['leagueID']);
                $aLeagues[$k2]['entries'] = count($aUserpicks);

                $profit = 0;
                $payout = 0;		
                $cash = 0;

                if ( "YES" == $aLeague["awarded"] )
                {
                    $profit = $tc * .1;
                    $payout = $tc * .9;
                    $cash = $tc;
                }

                $aLeagues[$k2]['total_cash'] = '$'.$tc;
                $aLeagues[$k2]['profit'] = '$'.$profit;
            }
        }
        return $aLeagues;
    }
    
    public function eventStatistic($leagueID)
    {
        $aLeague = $this->sendRequest("eventStatistic", array("leagueID" => $leagueID));
        $accumProfit = 0;
        $accumPayOut = 0;
        $accumCash = 0;
        if($aLeague != null)
        {
            if(!empty($aLeague['pick']))
            {
                foreach($aLeague['pick'] as $k1 => $pick)
                {
                    $user = get_userdata($pick['userID']);
                    if($user != null)
                    {
                        $aLeague['pick'][$k1]['user_login'] = $user->user_login;
                    }
                }
            }
            $profit = 0;
            $payout = 0;		
            $cash = 0;
            $tc = $aLeague["entry_fee"] * $aLeague["size"];
            if ( "YES" == $aLeague["awarded"] )
            {
                $profit = $tc * (100 - $aLeague["winner_percent"]) / 100;
                $payout = $tc * $aLeague["winner_percent"] / 100;
                $cash = $tc;
            }

            $aLeague['total_cash'] = '$'.$tc;
            $aLeague['profit'] = '$'.$profit;

            $accumProfit += $profit;
            $accumPayOut += $payout;
            $accumCash += $cash;
        }
        return $aLeague;
    }
    
    public function getFundhistory($aConds, $sSort = 'f.fundshistoryID DESC', $iPage = '', $iLimit = '')
	{	
        if($aConds != null && is_array($aConds))
        {
            $aConds = implode('AND', $aConds);
        }
        global $wpdb;
        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT COUNT(*) "
             . "FROM $this->_sTableFundhistory f "
             . "INNER JOIN $this->_sTableUser u ON u.ID = f.userID "
             . $sCond;
        $iCnt = $wpdb->get_var($sql);

        $sql = "SELECT * "
             . "FROM $this->_sTableFundhistory f "
             . "INNER JOIN $this->_sTableUser u ON u.ID = f.userID "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit ";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        
		return array($iCnt, $aRows);
	}
}
?>