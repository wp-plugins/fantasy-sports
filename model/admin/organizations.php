<?php
require_once("RestClient.php");
Class Organizations
{
	public function __construct()
	{	
        $this->pools = new Pools();
        $this->api_token = get_option('fanvictor_api_token');
        $this->api_url = get_option('fanvictor_api_url_admin');
        $this->urladd = 'admincp.fanvictor.organizations.add';
	}
    
    private function getRestClient($method, $url)
    {
        $ret = false;
        $ret = new RestClient($method, $url);
        return $ret;
    }
    
    public function isOrgsExist($orgID)
    {
        $url = $this->api_url."/isOrganizationExist/".$this->api_token."?orgID=".$orgID;
        $client = $this->getRestClient("GET", $url);
        return $client->send(false);
    }
    
    public function isSportExist($type)
    {
        $url = $this->api_url."/isSportExist/".$this->api_token."?type=".$type;
        $client = $this->getRestClient("GET", $url);
        return $client->send(false);
    }
    
    public function getOrgs($orgID = null, $sport = null, $setting = false)
    {
        $url = $this->api_url."/organization/".$this->api_token."?orgID=".$orgID;
        if($sport != null)
        {
            $url .= "&sport=".$sport;
        }
        if($setting)
        {
            $url .= "&setting=true";
        }
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
        return $data;
    }
    
    public function getSport($orgID = null, $setting = false)
    {
        $url = $this->api_url."/sport/".$this->api_token;
        if($setting)
        {
            $url .= "?setting=true";
        }
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
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


    public function updateOrgsActive($orgID, $is_active)
    {
        $data = array('organizationID' => $orgID,
                      'is_active' => $is_active);
        $url = $this->api_url."/updateOrgsActive/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $client->send($data);
        return true;
    }
}

?>