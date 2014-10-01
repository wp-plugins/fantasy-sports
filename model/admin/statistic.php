<?php
require_once("RestClient.php");
class Statistic
{
    public function __construct() 
    {
        $this->api_token = get_option('fanvictor_api_token');
        $this->api_url = get_option('fanvictor_api_url_admin');
        $this->pools = new Pools();
    }
    
    private function getRestClient($method, $url)
    {
        $ret = false;
        $ret = new RestClient($method, $url);
        return $ret;
    }
    
    public function getLeagues($poolID)
    {
        $url = $this->api_url."/leagues/".$this->api_token."?poolID=".$poolID;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
        return $data;
    }
    
    public function getUserpicks($leagueID)
    {
        $url = $this->api_url."/userpicks/".$this->api_token."?leagueID=".$leagueID;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
        return $data;
    }
    
    public function parseListEvents($aPools = null)
    {
        $result = null;
        if($aPools != null)
        {
            $accumProfit = 0;
            $accumPayOut = 0;
            $accumCash = 0;
            foreach($aPools as $k => $aPool)
            {
                $aLeagues = $this->getLeagues($aPool['poolID']);
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
                            $profit = $tc * (100 - $aLeague["winner_percent"]) / 100;
                            $payout = $tc * $aLeague["winner_percent"] / 100;
                            $cash = $tc;
                        }

                        $aLeagues[$k2]['total_cash'] = '$'.$tc;
                        $aLeagues[$k2]['profit'] = '$'.$profit;

                        $accumProfit += $profit;
                        $accumPayOut += $payout;
                        $accumCash += $cash;
                    }
                }
                $aPools[$k]['leagues'] = $aLeagues;
            }
            $result = array('pools' => $aPools, 
                            'accumProfit' => '$'.$accumProfit, 
                            'accumPayOut' => '$'.$accumPayOut, 
                            'accumCash' => '$'.$accumCash);
        }
        return $result;
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
}

?> 
