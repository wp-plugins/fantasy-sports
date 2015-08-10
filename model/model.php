<?php
require_once("admin/RestClient.php");
class Model
{
    public function __construct() 
    {
        $this->selectField = null;
    }
    
    private function getRestClient($method, $url)
    {
        $ret = false;
        $ret = new RestClient($method, $url);
        return $ret;
    }
    
    public function selectField($params = array())
    {
        $this->selectField = $params;
    }
    
    protected function sendRequest($task, $params = null, $is_admin = true, $json = true)
    {
        $api_token = get_option('fanvictor_api_token');
        $params['v2'] = true;
        //parse url
        if($is_admin)
        {
            $url = get_option('fanvictor_api_url_admin')."/$task/".$api_token;
        }
        else 
        {
            $url = get_option('fanvictor_api_url')."/$task/".$api_token.'/'.get_current_user_id();
        }
        
        if(isset($this->selectField) && $this->selectField != null)
        {
            $params['field'] = $this->selectField;
        }

        //send request
        $client = $this->getRestClient('POST', $url);
        $data = $client->send($params);
        $this->selectField = null;
        if($json)
        {
            $data = json_decode($data, true);
        }
        return $data;
    }
    
    public static function parseImageSuffix($image = null, $suf = null)
    {
        $suffix = '_80';
        if($suf != null)
        {
            $suffix = $suf;
        }
        if($image != null)
        {
            $img = explode('.', $image);
            $img[count($img) - 2] = $img[count($img) - 2].$suffix.".".$img[count($img) - 1];
			unset($img[count($img) - 1]);
			array_values($img);
            $img = implode('.', $img);
            return $img;
        }
        return null;
    }
    
    public static function replaceSuffix($image = null, $suf = 'suf')
    {
        $suffix = '_80';
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
    
    public function deleteImage($sFileName = null)
    {
        if (!empty($sFileName))
        {
            $originalImagePath = FANVICTOR_IMAGE_DIR.$this->replaceSuffix($sFileName, '');
            $thumbImagePath = FANVICTOR_IMAGE_DIR.$this->replaceSuffix($sFileName);

            if(file_exists($originalImagePath))
            {
                unlink($originalImagePath);
            }
            if(file_exists($thumbImagePath))
            {
                unlink($thumbImagePath);
            }
        }
        return true;
    }
    
    public function uploadImage($resize = true)
    {
        if (!function_exists('wp_handle_upload')) 
        {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        $uploadedfile = $_FILES['image'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        if($movefile) 
        {
            if($resize)
            {
                $image = str_replace(FANVICTOR_IMAGE_URL, '', $this->parseImageSuffix($movefile['url'], '%s'));

                //resize 
                $img = wp_get_image_editor($movefile['file']);
                if (!is_wp_error($img)) 
                {
                    $img->resize(80,80, true );
                    $img->save($this->parseImageSuffix($movefile['file'], '_80'));
                }
            }
            else 
            {
                $image = str_replace(FANVICTOR_IMAGE_URL, '', $movefile['url']);;
            }
        }
        return $image;
    }
}
?>