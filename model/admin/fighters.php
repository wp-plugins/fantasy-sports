<?php
class Fighters extends Model
{
    public function getMethods($methodID = null)
    {
        $params = array();
        if((int)$methodID > 0)
        {
            $params['methodID'] = $methodID;
        }
        $data = $this->sendRequest("methods", $params);
        return $data;
    }
    
    public function getRounds()
    {
        $data = array();
        for($i = 1; $i <= 12; $i++)
        {
            $data[] = $i;
        }
        return $data;
    }
    
    public function getMinutes($minuteID = null)
    {
        $params = array();
        if((int)$minuteID > 0)
        {
            $params['minuteID'] = $minuteID;
        }
        $data = $this->sendRequest("minutes", $params);
        return $data;
    }
    
    public function isFighterExist($fighterID)
    {
        if($this->sendRequest("isFighterExist", array('fighterID' => $fighterID)) == 1)
        {
            return true;
        }
        return false;
    }

	public function getFighters($fighterID = null, $orgsID = null)
    {
        $params = array();
        if($fighterID != null)
        {
            $params['fighterID'] = $fighterID;
        }
        if((int)$orgsID > 0)
        {
            $params['orgsID'] = $orgsID;
        }
        $data = $this->sendRequest("fighters", $params);
        if(!is_array($fighterID) && (int)$fighterID > 0)
        {
            $data = $this->parseFightersData($data);
            $data = $data[0];
        }
        return $data;
    }
    
    public function getFighterImageName($fighterID)
    {
        $data = $this->getFighters($fighterID);
        if($data != null)
        {
            return $data['image'];
        }
        return null;
    }
    
    public function getFightersByFilter($aConds, $sSort = 'fighterID DESC', $iPage = '', $iLimit = '')
    {
        $params = array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit);
        $data = $this->sendRequest("fightersByFilter", $params);
        return array($data['iCnt'], $data['aRows']);
    }
    
    public function getFighterName($fighterID, $all = false)
    {
        $data = $this->getFighters($fighterID, null, $all);
        return $data['name'];
    }
    
    public function parseFightersData($data = null)
    {
        if($data != null)
        {
            foreach($data as $k => $v)
            {
                $data[$k]['full_image_path'] = FANVICTOR_IMAGE_URL.$this->replaceSuffix($v['image']);
            }
        }
        return $data;
    }
    
    //////////////////////////////////////////add, update, delete//////////////////////////////////////////
    public function add($aVals)
    {
        $fighterID = $this->sendRequest("addFighters", $this->parseFightersDataForModify($aVals));
        //upload new image
        $image = $this->uploadImage();
        $this->updateFightersImage($fighterID, $image);
        
        if($fighterID > 0)
        {
            return true;
        }
        return false;
    }

    public function update($aVals)
    {
        $result = $this->sendRequest("updateFighters", $this->parseFightersDataForModify($aVals, true));
        
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            //get current image name
            $sFileName = $this->getFighterImageName($aVals['fighterID']);

            //delete old image
            $this->deleteImage($sFileName);
            
            //upload new image
            $image = $this->uploadImage();
            $this->updateFightersImage($aVals['fighterID'], $image);
        }
        return $result;
    }
    
    public function updateFightersImage($fighterID, $image)
    {
        return $this->sendRequest("updateFighters", array('fighterID' => (int)$fighterID, 'image' => $image));
    }
    
    private function parseFightersDataForModify($aVals, $isUpdate = false)
    {
        $data = array('name' => $aVals['name'],
                      'nickName' => $aVals['nickName'],
                      'age' => $aVals['age'],
                      'fightCamp' => $aVals['fightCamp'],
                      'strengths' => $aVals['strengths'],
                      'homepageLink' => $aVals['homepageLink'],
                      'height' => $aVals['height'],
                      'weight' => $aVals['weight'],
                      'record' => $aVals['record']);
        if($isUpdate)
        {
            $data['fighterID'] = $aVals['fighterID'];
        }
        return $data;
    }
    
    public function delete($fighterID)
    {
        $sFileName = $this->getFighterImageName($fighterID);
        $result = $this->sendRequest("deleteFighters", array('fighterID' => $fighterID));
        if($result)
        {
            $this->deleteImage($sFileName);
            return true;
        }
        return false;
    }
}
?>