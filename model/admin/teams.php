<?php
require_once("RestClient.php");
class Teams
{
    public function __construct() 
    {
        $this->api_token = get_option('fanvictor_api_token');
        $this->api_url = get_option('fanvictor_api_url_admin');
        $this->urladd = 'admincp.fanvictor.teams.add';
    }
    
    private function getRestClient($method, $url)
    {
        $ret = false;
        $ret = new RestClient($method, $url);
        return $ret;
    }
    
    public function isTeamExist($teamID)
    {
        $url = $this->api_url."/isTeamExist/".$this->api_token."?teamID=".$teamID;
        $client = $this->getRestClient("GET", $url);
        if($client->send(false) == 1)
        {
            return true;
        }
        return false;
    }
    
	public function getTeams($teamID = null, $orgsID = null, $all = false)
    {
        $url = $this->api_url."/teams/".$this->api_token."?teamID=".$teamID;
        if((int)$orgsID > 0)
        {
            $url .= "&orgsID=".(int)$orgsID;
        }
        if($all)
        {
            $url .= "&all=true";
        }
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
        if((int)$teamID > 0)
        {
            $data = $this->parseTeamsData($data);
            $data = $data[0];
        }
        return $data;
    }
    
    public function getTeamImageName($teamID)
    {
        $data = $this->getTeams($teamID);
        if($data != null)
        {
            return $data['image'];
        }
        return null;
    }
    
    public function getTeamsByFilter($aConds, $sSort = 'teamID DESC', $iPage = '', $iLimit = '')
    {
        $url = $this->api_url."/teamsByFilter/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $data = $client->send(array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit));
        $data = json_decode($data, true);
        return array($data['iCnt'], $data['aRows']);
    }
    
    public function getTeamName($teamID, $all = false)
    {
        $data = $this->getTeams($teamID, null, $all);
        return $data['name'];
    }
    
    public function parseTeamsData($data = null)
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
    
    public static function parseImageSuffix($image = null, $suf = null)
    {
        $sufix = '-'.get_option('fanvictor_image_thumb_size');
        if($suf != null)
        {
            $sufix = $suf;
        }
        if($image != null)
        {
            $img = explode('.', $image);
            $img[count($img) - 2] = $img[count($img) - 2].$sufix.".".$img[count($img) - 1];
			unset($img[count($img) - 1]);
			array_values($img);
            $img = implode('.', $img);
            return $img;
        }
        return null;
    }
    
    public static function replaceSuffix($image = null, $suf = 'suf')
    {
        $suffix = '_'.get_option('fanvictor_image_thumb_size');
        if($suf != 'suf')
        {
            $suffix = $suf;
        }
        if($image != null)
        {
            $image = sprintf($image, $suffix);
        }
        return $image;
    }

    //////////////////////////////////////////add, update, delete//////////////////////////////////////////
    public function add($aVals)
    {
        $url = $this->api_url."/addTeams/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $teamID = $client->send($this->parseTeamsDataForModify($aVals));
        
        //upload new image
        $this->uploadImage($teamID);
        
        if($teamID > 0)
        {
            return true;
        }
        return false;
    }

    public function update($aVals)
    {
        $url = $this->api_url."/updateTeams/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $result = $client->send($this->parseTeamsDataForModify($aVals, true));
        
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            //get current image name
            $sFileName = $this->getTeamImageName($aVals['teamID']);

            //delete old image
            $this->deleteImage($sFileName);
            
            //upload new image
            $this->uploadImage($aVals['teamID']);
        }
        return $result;
    }
    
    public function updateTeamsImage($teamID, $image)
    {
        $url = $this->api_url."/updateTeams/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $client->send(array('teamID' => $teamID, 'image' => $image));
    }
    
    private function parseTeamsDataForModify($aVals, $isUpdate = false)
    {
        $data = array('organization_id' => $aVals['organization'],
                      'name' => $aVals['name'],
                      'nickName' => $aVals['nickName'],
                      'homepageLink' => $aVals['homepageLink'],
                      'cityname' => $aVals['cityname'],
                      'teamname' => $aVals['teamname'],
                      'record' => $aVals['record']);
        if($isUpdate)
        {
            $data['teamID'] = $aVals['teamID'];
        }
        return $data;
    }
    
    public function delete($teamID)
    {
        $sFileName = $this->getTeamImageName($teamID);
        $url = $this->api_url."/deleteTeams/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $result = $client->send(array('teamID' => $teamID));
        if($result)
        {
            $this->deleteImage($sFileName);
            return true;
        }
        return false;
    }
    
    private function uploadImage($iId)
    {
        if (!function_exists('wp_handle_upload')) 
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        $uploadedfile = $_FILES['image'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        if($movefile) 
        {
            $image = str_replace(FANVICTOR_IMAGE_URL, '', $this->parseImageSuffix($movefile['url'], '%s'));
            $this->updateTeamsImage($iId, $image);
            
            //resize 
            image_resize($movefile['file'], 
                        get_option('fanvictor_image_thumb_size'), 
                        get_option('fanvictor_image_thumb_size'), 
                        true, 
                        get_option('fanvictor_image_thumb_size'), 
                        FANVICTOR_IMAGE_URL, 
                        100);
            $newname = $this->parseImageSuffix($movefile['file'], '%s');
            rename(sprintf($newname, '-'.get_option('fanvictor_image_thumb_size')), sprintf($newname, '_'.get_option('fanvictor_image_thumb_size')));
        } 
    }
    
    public function deleteImage($sFileName = null)
    {
        if (!empty($sFileName))
        {
            $originalImagePath = FANVICTOR_IMAGE_DIR.$this->replaceSuffix($sFileName, '');
            $thumbImagePath = FANVICTOR_IMAGE_DIR.$this->replaceSuffix($sFileName);

            unlink($originalImagePath);
            unlink($thumbImagePath);
        }
        return true;
    }
}
?>