<?php
require_once("RestClient.php");
class Fighters
{
    public function __construct() 
    {
        $this->api_token = get_option('fanvictor_api_token');
        $this->api_url = get_option('fanvictor_api_url_admin');
        $this->urladd = 'admincp.fanvictor.fighters.add';
    }
    
    private function getRestClient($method, $url)
    {
        $ret = false;
        $ret = new RestClient($method, $url);
        return $ret;
    }
    
    public function getMethods($methodID = null)
    {
        $url = $this->api_url."/methods/".$this->api_token."?methodID=".$methodID;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
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
        $url = $this->api_url."/minutes/".$this->api_token."?minuteID=".$minuteID;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
        return $data;
    }
    
    public function isFighterExist($fighterID)
    {
        $url = $this->api_url."/isFighterExist/".$this->api_token."?fighterID=".$fighterID;
        $client = $this->getRestClient("GET", $url);
        if($client->send(false) == 1)
        {
            return true;
        }
        return false;
    }

	public function getFighters($fighterID = null, $orgsID = null, $all = false)
    {
        $url = $this->api_url."/fighters/".$this->api_token."?fighterID=".$fighterID;
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
        if((int)$fighterID > 0)
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
        $url = $this->api_url."/fightersByFilter/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $data = $client->send(array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit));
        $data = json_decode($data, true);
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
            $img[count($img) - 1] = $sufix.".".$img[count($img) - 1];
            $img = implode('', $img);
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
        $url = $this->api_url."/addFighters/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $fighterID = $client->send($this->parseFightersDataForModify($aVals));
        
        //upload new image
        $this->uploadImage($fighterID);
        
        if($fighterID > 0)
        {
            return true;
        }
        return false;
    }

    public function update($aVals)
    {
        $url = $this->api_url."/updateFighters/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $result = $client->send($this->parseFightersDataForModify($aVals, true));
        
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            //get current image name
            $sFileName = $this->getFighterImageName($aVals['fighterID']);

            //delete old image
            $this->deleteImage($sFileName);
            
            //upload new image
            $this->uploadImage($aVals['fighterID']);
        }
        return $result;
    }
    
    public function updateFightersImage($fighterID, $image)
    {
        $url = $this->api_url."/updateFighters/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $client->send(array('fighterID' => $fighterID, 'image' => $image));
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
        $url = $this->api_url."/deleteFighters/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $result = $client->send(array('fighterID' => $fighterID));
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
            $this->updateFightersImage($iId, $image);
            
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