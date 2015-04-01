<?php
Class Sports extends Model
{
	public function __construct()
	{	
        $this->api_token = get_option('fanvictor_api_token');
        $this->api_url = get_option('fanvictor_api_url_admin');
        $this->urladd = 'admincp.fanvictor.sports.add';
	}
    
    public function isSportExist($id)
    {
        return $this->sendRequest("isSportExist", array("id" => $id));
    }
    
    public function getSports()
    {
        return $this->sendRequest("getSports", null);
    }
    
    public function getSportById($id)
    {
        return $this->sendRequest("getSportById", array("id" => $id));
    }
    
    public function parseSportsData($data = null)
    {
        if($data != null)
        {
            foreach($data as $k => $v)
            {
                $data[$k]['full_image_path'] = FANVICTOR_IMAGE_URL.$v['image'];
            }
        }
        return $data;
    }
    
    public function getSportImageName($id)
    {
        $data = $this->getSportById($id);
        if($data != null)
        {
            return $data[0]['image'];
        }
        return null;
    }
    
    //////////////////////////////////////////add, update, delete//////////////////////////////////////////
    public function add($aVals)
    {
        $id = $this->sendRequest("addSport", $this->parseSportsDataForModify($aVals));
        
        //upload new image
        $image = $this->uploadImage(false);
        $this->updateSportsImage($id, $image);
        
        if($id > 0)
        {
            return true;
        }
        return false;
    }

    public function update($aVals)
    {
        $result = $this->sendRequest("updateSport", $this->parseSportsDataForModify($aVals, true));
        
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            //get current image name
            $sFileName = $this->getSportImageName($aVals['id']);

            //delete old image
            $this->deleteImage($sFileName);
            
            //upload new image
            $image = $this->uploadImage(false);
            $this->updateSportsImage($aVals['id'], $image);
        }
        return $result;
    }
    
    public function updateSportsImage($id, $image)
    {
        return $this->sendRequest("updateSport", array('id' => (int)$id, 'image' => $image));
    }
    
    private function parseSportsDataForModify($aVals, $isUpdate = false)
    {
        $data = array('parent_id' => $aVals['parent_id'],
                      'name' => $aVals['name'],
                      'is_playerdraft' => isset($aVals['is_playerdraft']) ? $aVals['is_playerdraft'] : 0,
                      'is_team' => isset($aVals['is_team']) ? $aVals['is_team'] : 0);
        if($isUpdate)
        {
            $data['id'] = $aVals['id'];
        }
        return $data;
    }
    
    public function delete($id)
    {
        $sFileName = $this->getSportImageName($id);
        $result = $this->sendRequest("deleteSport", array('id' => $id));
        if($result)
        {
            $this->deleteImage($sFileName);
            return true;
        }
        return false;
    }
}
?>