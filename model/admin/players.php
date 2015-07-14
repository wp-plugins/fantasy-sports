<?php
class Players extends Model
{
    public function isPlayersExist($id)
    {
        if($this->sendRequest("isPlayersExist", array('id' => $id)) == 1)
        {
            return true;
        }
        return false;
    }
    
    public function getPlayersImageName($id)
    {
        $data = $this->getPlayers($id);
        if($data != null)
        {
            return $data['image'];
        }
        return null;
    }
    
	public function getPlayers($id = null, $teamID = null, $all = false)
    {
        $params = array();
        if($id != null)
        {
            $params['id'] = $id;
        }
        if($teamID != null)
        {
            $params['teamID'] = $teamID;
        }
        if($all)
        {
            $params['all'] = true;
        }
        return $this->sendRequest("players", $params);
    }

    public function getPlayersByFilter($aConds, $sSort = 'id DESC', $iPage = '', $iLimit = '')
    {
        $params = array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit);
        $data = $this->sendRequest("playersByFilter", $params);
        return array($data['iCnt'], $data['aRows']);
    }
    
    public function getAddPlayer($player_id)
    {
        return $this->sendRequest("addPlayerFormData", array("player_id" => $player_id));
    }
    
    public function getPlayersName($id, $all = false)
    {
        $data = $this->getPlayers($id, null, $all);
        return $data['name'];
    }
    
    public function getIndicator()
    {
        return $this->sendRequest("indicator");
    }

    public function parsePlayersData($data = null, $is_arr = true)
    {
        if($data != null && $is_arr)
        {
            foreach($data as $k => $v)
            {
                if($v['siteID'] > 0)
                {
                    $data[$k]['full_image_path'] = FANVICTOR_IMAGE_URL.$this->replaceSuffix($v['image']);
                    $data[$k]['full_image_path_org'] = FANVICTOR_IMAGE_URL.$this->replaceSuffix($v['image'], '');
                }
                else 
                {
                    $data[$k]['full_image_path'] = $this->replaceSuffix($v['image']);
                    $data[$k]['full_image_path_org'] = $this->replaceSuffix($v['image'], '');
                }
            }
        }
        else if($data != null && !$is_arr)
        {
            if($data['siteID'] > 0)
            {
                $data['full_image_path'] = FANVICTOR_IMAGE_URL.$this->replaceSuffix($data['image']);
                $data['full_image_path_org'] = FANVICTOR_IMAGE_URL.$this->replaceSuffix($data['image'], '');
            }
            else 
            {
                $data['full_image_path'] = $this->replaceSuffix($data['image']);
                $data['full_image_path_org'] = $this->replaceSuffix($data['image'], '');
            }
        }
        return $data;
    }

    //////////////////////////////////////////add, update, delete//////////////////////////////////////////
    public function add($aVals)
    {
        $id = $this->sendRequest("addPlayer", $aVals, true ,false);
        if(!is_numeric($id) && $id != 'u1')
        {
            return $id;
        }

        //upload new image
        $image = $this->uploadImage();
        if($id == 'u1')
        {
            $id = $aVals['id'];
        }
        if($id > 0 && $image != null)
        {
            $this->updatePlayersImage($id, $image);
        }
        
        return $id;
    }

    public function updatePlayersImage($id, $image)
    {
        return $this->sendRequest("updatePlayers", array('id' => (int)$id, 'image' => $image));
    }
    
    private function parsePlayersDataForModify($aVals, $isUpdate = false)
    {
        $data = array('team_id' => $aVals['team_id'],
                      'org_id' => $aVals['org_id'],
                      'position_id' => $aVals['position_id'],
                      'name' => $aVals['name'],
                      'salary' => str_replace(',', '', $aVals['salary']),
                      'indicator_id' => $aVals['indicator_id']);
        if($isUpdate)
        {
            $data['id'] = $aVals['id'];
        }
        return $data;
    }
    
    public function delete($id)
    {
        $sFileName = $this->getPlayersImageName($id);
        $result = $this->sendRequest("deletePlayers", array('id' => $id));;
        if($result)
        {
            $this->deleteImage($sFileName);
            return true;
        }
        return false;
    }
}
?>