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
    
    public function getPlayersName($id, $all = false)
    {
        $data = $this->getPlayers($id, null, $all);
        return $data['name'];
    }
    
    public function getIndicator()
    {
        return $this->sendRequest("indicator");
    }

    public function parsePlayersData($data = null)
    {
        if($data != null)
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
        return $data;
    }

    //////////////////////////////////////////add, update, delete//////////////////////////////////////////
    public function add($aVals)
    {
        $id = $this->sendRequest("addPlayers", $this->parsePlayersDataForModify($aVals));

        //upload new image
        $image = $this->uploadImage();
        $this->updatePlayersImage($id, $image);
        
        if($id > 0)
        {
            return true;
        }
        return false;
    }

    public function update($aVals)
    {
        $result = $this->sendRequest("updatePlayers", $this->parsePlayersDataForModify($aVals, true));
        
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            //get current image name
            $sFileName = $this->getPlayersImageName($aVals['id']);

            //delete old image
            $this->deleteImage($sFileName);
            
            //upload new image
            $image = $this->uploadImage();
            $this->updatePlayersImage($aVals['id'], $image);
        }
        return $result;
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