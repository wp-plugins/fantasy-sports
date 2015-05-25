<?php
class PlayerNews extends Model
{
    public function getPlayerNewsByFilter($aConds, $sSort = 'id DESC', $iPage = '', $iLimit = '', $keyword = '')
    {
        $params = array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit, 'keyword' => $keyword);
        $data = $this->sendRequest("playerNewsByFilter", $params);
        return array($data['iCnt'], $data['aRows']);
    }
    
    public function getPlayerNewsForm($id)
    {
        return $this->sendRequest("getPlayerNewsForm", array("id" => $id));
    }
    
    //////////////////////////////////////////add, update, delete//////////////////////////////////////////
    public function add($aVals)
    {
        return $this->sendRequest("addPlayerNews", $aVals, true ,false);
    }

    public function delete($id)
    {
        $result = $this->sendRequest("deletePlayerNews", array('id' => $id));
        if($result)
        {
            return true;
        }
        return false;
    }
}
?>