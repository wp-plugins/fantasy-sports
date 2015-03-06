<?php
class ScoringCategory extends Model
{
    public function __construct() 
    {
        $this->api_token = get_option('fanvictor_api_token');
        $this->api_url = get_option('fanvictor_api_url_admin');
        $this->urladd = 'admincp.fanvictor.scoringcategory.add';
    }
    
    public function isScoringCategoryExist($id)
    {
        if($this->sendRequest("isScoringCategoryExist", array('id' => $id)) == 1)
        {
            return true;
        }
        return false;
    }
    
	public function getScoringCategory($id = null, $orgsID = null, $all = false)
    {
        $params = array();
        if($id != null)
        {
            $params['id'] = $id;
        }
        if((int)$orgsID > 0)
        {
            $params['orgsID'] = (int)$orgsID; 
        }
        if($all)
        {
            $params['orgsID'] = true;
        }
        $data = $this->sendRequest("scoringcategory", $params);
        if((int)$id > 0)
        {
            $data = $data[0];
        }
        return $data;
    }
    
    public function getPlayerStatsScoring($poolID, $fightID, $playerID, $item_per_page, $page)
    {
        return $this->sendRequest("playerStatsScoring", array('poolID' => $poolID, 
                                                              'fightID' => $fightID, 
                                                              'playerID' => $playerID,
                                                              'item_per_page' => $item_per_page,
                                                              'page' => $page));
    }

    public function getScoringCategoryByFilter($aConds, $sSort = 'id DESC', $iPage = '', $iLimit = '')
    {
        $params = array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit);
        $data = $this->sendRequest("scoringCategoryByFilter", $params);
        return array($data['iCnt'], $data['aRows']);
    }
    
    public function getScoringCategoryName($id, $all = false)
    {
        $data = $this->getScoringCategory($id, null, $all);
        return $data['name'];
    }
    
    public function getScoringType()
    {
        return  $this->sendRequest("getScoringType");
    }
    
    public function groupScoringCategory($datas)
    {
        if($datas != null)
        {
            //sort
            $mid = array();
            foreach ($datas as $key => $row) {
                $mid[$key]  = $row['scoring_type'];
            }
            array_multisort($mid, SORT_DESC, $datas);
            
            //group
            $grou = array();
            $groupName = 'none';
            $count = -1;
            foreach($datas as $k => $v)
            {
                if($groupName != $v['scoring_type'])
                {
                    $count++;
                    $group[$count]['name'] = $groupName = $v['scoring_type'];
                }
                $group[$count]['scoring_category'][] = $v;
            }
            return $group;
        }
        return null;
    }

    //////////////////////////////////////////add, update, delete//////////////////////////////////////////
    public function add($aVals)
    {
        $id = $this->sendRequest("addScoringCategory", $this->parseScoringCategoryDataForModify($aVals));
        if($id > 0)
        {
            return true;
        }
        return false;
    }

    public function update($aVals)
    {
        $result = $this->sendRequest("updateScoringCategory", $this->parseScoringCategoryDataForModify($aVals, true));
        return $result;
    }
    
    public function updateScoringCategoryActive($id, $is_active)
    {
        $data = array('id' => $id,
                      'is_active' => $is_active);
        $this->sendRequest("updateScoringCategoryActive", $data);
        return true;
    }
    
    private function parseScoringCategoryDataForModify($aVals, $isUpdate = false)
    {
        $data = array('org_id' => $aVals['org_id'],
                      'name' => $aVals['name'],
                      'points' => $aVals['points'],
                      'scoring_type' => $aVals['scoring_type']);
        if($isUpdate)
        {
            $data['id'] = $aVals['id'];
        }
        return $data;
    }
    
    public function delete($id)
    {
        $result = $this->sendRequest("deleteScoringCategory", array('id' => $id));
        if($result)
        {
            return true;
        }
        return false;
    }
}
?>