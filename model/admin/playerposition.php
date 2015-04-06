<?php
class PlayerPosition extends Model
{
    public function isPlayerPositionExist($id)
    {
        if($this->sendRequest("isPlayerPositionExist", array('id' => $id)) == 1)
        {
            return true;
        }
        return false;
    }
    
	public function getPlayerPosition($id = null, $orgsID = null)
    {
        $params = array();
        if((int)$id > 0)
        {
            $params['id'] = $id;
        }
        if((int)$orgsID > 0)
        {
            $params['orgsID'] = $orgsID;
        }
        $data = $this->sendRequest("playerposition", $params);
        if((int)$id > 0)
        {
            $data = $data[0];
        }
        return $data;
    }

    public function getPlayerPositionByFilter($aConds, $sSort = 'id DESC', $iPage = '', $iLimit = '')
    {
        $params = array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit);
        $data = $this->sendRequest("playerPositionByFilter", $params);
        return array($data['iCnt'], $data['aRows']);
    }
    
    public function getPlayerPositionName($id, $all = false)
    {
        $data = $this->getPlayerPosition($id, null, $all);
        return $data['name'];
    }

    //////////////////////////////////////////add, update, delete//////////////////////////////////////////
    public function add($aVals)
    {
        $id = $this->sendRequest("addPlayerPosition", $this->parsePlayerPositionDataForModify($aVals));
        if($id > 0)
        {
            return true;
        }
        return false;
    }

    public function update($aVals)
    {
        return $this->sendRequest("updatePlayerPosition", $this->parsePlayerPositionDataForModify($aVals, true));
    }
    
    public function updatePlayerPositionImage($id, $image)
    {
        return $this->sendRequest("updatePlayerPosition", array('id' => $id, 'image' => $image));
    }
    
    private function parsePlayerPositionDataForModify($aVals, $isUpdate = false)
    {
        $data = array('org_id' => $aVals['org_id'],
                      'name' => $aVals['name'],
                      'default_quantity' => $aVals['default_quantity']);
        if($isUpdate)
        {
            $data['id'] = $aVals['id'];
        }
        return $data;
    }
    
    public function delete($id)
    {
        $result = $this->sendRequest("deletePlayerPosition", array('id' => $id));
        if($result)
        {
            return true;
        }
        return false;
    }
}
?>