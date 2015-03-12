<?php
class Leagues extends Model
{
    public function isLeagueExist($leagueID)
    {
        if($this->sendRequest("isLeagueExist", array('leagueID' => $leagueID)) == 1)
        {
            return true;
        }
        return false;
    }
    
    public function getLeaguesByFilter($aConds, $sSort = 'leagueID DESC', $iPage = '', $iLimit = '')
    {
        $params = array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit);
        $data = $this->sendRequest("leaguesByFilter", $params);
        return array($data['iCnt'], $data['aRows']);
    }
    
    public function parseLeagueData($datas = null)
    {
        if($datas != null)
        {
            foreach($datas as $k => $data)
            {
                $user = get_userdata($data['creator_userID']);
                $datas[$k]['creator'] = $user != null ? $user->user_login : null;
            }
        }
        return $datas;
    }

    public function delete($leagueID)
    {
        $result = $this->sendRequest("deleteLeague", array('leagueID' => $leagueID));
        if($result)
        {
            return true;
        }
        return false;
    }
}
?>