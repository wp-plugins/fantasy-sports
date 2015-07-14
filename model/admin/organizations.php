<?php
Class Organizations extends Model
{
	public function __construct()
	{	
        $this->pools = new Pools();
        $this->api_token = get_option('fanvictor_api_token');
        $this->api_url = get_option('fanvictor_api_url_admin');
        $this->urladd = 'admincp.fanvictor.organizations.add';
	}
    
    public function isOrgsExist($orgID)
    {
        return $this->sendRequest("isOrganizationExist", array("orgID" => $orgID));
    }
    
    public function isGameTypeExist($value)
    {
        return $this->sendRequest("isGameTypeExist", array("value" => $value));
    }
    
    public function isSportExist($type)
    {
        return $this->sendRequest("isSportExist", array("type" => $type));
    }
    
    public function getOrgs($orgID = null, $sport = null, $setting = false, $playerdraft = false)
    {
        $params = array();
        if((int)$orgID > 0)
        {
            $params['orgID'] = $orgID;
        }
        if($sport != null)
        {
            $params['sport'] = $sport;
        }
        if($setting)
        {
            $params['setting'] = true;
        }
        if($playerdraft)
        {
            $params['playerdraft'] = true;
        }
        return $this->sendRequest("organization", $params);
    }
    
    public function getSport($orgID = null, $setting = false)
    {
        $params = array();
        if($setting)
        {
            $params['setting'] = true;
        }
        $data = $this->sendRequest("sport", $params);
        return $data;
    }
    
    public function getAllSportOrgs($orgID = null, $setting = false)
    {
        $aResults = array();
        $aSports = $this->getSport($orgID, $setting);
        $allOrgs = $this->getOrgs();
        $allTotalCurrentPools = $this->pools->getTotalCurrentPools(null, true);
        if($aSports != null)
        {
            foreach($aSports as $aSport)
            {
                $aOrgs = $this->parseOrgsOfSport($allOrgs, $aSport);
                if($aOrgs != null)
                {
                    foreach($aOrgs as $k => $aOrg)
                    {
                        //$aOrgs[$k]['total_pools'] = $this->pools->getTotalCurrentPools($aOrg['organizationID'], true);
                        $aOrgs[$k]['total_pools'] = $this->parseTotalPoolsOfOrg($allTotalCurrentPools, $aOrg['organizationID']);
                    }
                }
                $aResults[] = array('sport' => $aSport, 'orgs' => $aOrgs);
            }
        }
        return $aResults;
    }
    
    private function parseOrgsOfSport($aOrgs = null, $aSport)
    {
        $result = array();
        if($aOrgs != null)
        {
            foreach($aOrgs as $aOrg)
            {
                if($aOrg['sport'] == $aSport)
                {
                    $result[] = $aOrg;
                }
            }
        }
        return $result;
    }
    
    private function parseTotalPoolsOfOrg($data = null, $orgID)
    {
        if($data != null)
        {
            foreach($data as $data)
            {
                if($data['organization'] == $orgID)
                {
                    return $data['total'];
                }
            }
        }
        return 0;
    }
    
    public function getGameType()
    {
        return $this->sendRequest("getGameType");
    }

    public function updateOrgsActive($orgID, $is_active)
    {
        $data = array('organizationID' => $orgID,
                      'is_active' => $is_active);
        $this->sendRequest("updateOrgsActive", $data);
        return true;
    }
	
	public function updateOrgsReversePoint($orgID, $reverse_points)
    {
        $data = array('organizationID' => $orgID,
                      'reverse_points' => $reverse_points);
        $this->sendRequest("updateOrgsReversePoint", $data);
        return true;
    }
}
?>