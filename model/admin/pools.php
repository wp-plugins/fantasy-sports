<?php
require_once("RestClient.php");
class Pools
{
    public function __construct() 
    {
        $this->api_token = get_option('fanvictor_api_token');
        $this->api_url = get_option('fanvictor_api_url_admin');
        $this->urladd = 'admincp.fanvictor.pools.add';
        $this->payment = new Payment();
    }
    
    private function getRestClient($method, $url)
    {
        $ret = false;
        $ret = new RestClient($method, $url);
        return $ret;
    }
    
    //////////////////////////////////////////view//////////////////////////////////////////
    public function poolStatus()
    {
        return array('NEW', 'COMPLETE');
    }
    
    public function getPoolHours()
    {
        $data = array();
        for($i = 0; $i < 24; $i++)
        {
            if($i < 10)
            {
                $data[] = '0'.$i;
            }
            else 
            {
                $data[] = $i;
            }
        }
        return $data;
    }
    
    public function getPoolMinutes()
    {
        $data = array();
        for($i = 0; $i <= 55; $i++)
        {
            if($i < 10)
            {
                $data[] = '0'.$i;
            }
            else 
            {
                $data[] = $i;
            }
            $i+=4;
        }
        return $data;
    }
    
    public function isPoolExist($iPoolId = null)
    {
        if((int)$iPoolId > 0)
        {
            $data = $this->getPools((int)$iPoolId);
            if($data != null)
            {
                return true;
            }
        }
        return false;
    }
    
    public function isCompleteUserWin($leagueID)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'fundhistory';
        $sCond = "WHERE leagueID = $leagueID AND operation = 'ADD' AND type= 'WIN'";
        $sql = "SELECT COUNT(*) "
             . "FROM $table_name "
             . $sCond;
        $data = $wpdb->get_var($sql);
        if($data > 0)
        {
            return true;
        }
        return false;
    }
    
    public function isPoolResultsUpdated($iPoolId)
    {
        $aFights = $this->getFights($iPoolId, null, true);
        foreach($aFights as $aFight)
        {
            if($aFight['winnerID'] != $aFight['fighterID1'] && $aFight['winnerID'] != $aFight['fighterID2'])
            {
                return false;
            }
        }
        return true;
    }

    public function getPools($iPoolId = null, $orgID = null, $isNew = false, $all = false)
    {
        $url = $this->api_url."/pools/".$this->api_token."?poolID=".$iPoolId;
        if((int)$orgID > 0)
        {
            $url .= '&orgID='.$orgID;
        }
        if($isNew)
        {
            $url .= '&isNew=true';
        }
        if($all)
        {
            $url .= '&all=true';
        }
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
        if((int)$iPoolId > 0)
        {
            $data = $this->parsePoolsData($data);
            $data = $data[0];
        }
        return $data;
    }
    
    public function getTotalCurrentPools($orgID = null, $all = false)
    {
        $url = $this->api_url."/totalCurrentPools/".$this->api_token;
        $cond = null;
        if((int)$orgID > 0)
        {
            $cond[] = 'orgID='.$orgID;
        }
        if($all)
        {
            $cond[] = 'all=true';
        }
        if($cond != null)
        {
            $url .= '?'.implode('&', $cond);
        }
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
        return $data;
    }
    
    public function getPoolImageName($iPoolId)
    {
        $data = $this->getPools($iPoolId);
        if($data != null)
        {
            return $data['image'];
        }
        return null;
    }
    
    public function getPoolsByFilter($aConds, $sSort = 'poolID DESC', $iPage = '', $iLimit = '')
    {
        $url = $this->api_url."/poolsByFilter/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $data = $client->send(array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit));
        $data = json_decode($data, true);
        return array($data['iCnt'], $data['aRows']);
    }

    public function getFights($poolID, $fightID = null, $all = false)
    {
        $url = $this->api_url."/fights/".$this->api_token."?poolID=".$poolID."&fightID=".$fightID;
        if($all)
        {
            $url .= '&all=true';
        }
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
        $data = $this->parseFightsData($data);
        return $data;
    }
    
    public function getLeagues($poolID)
    {
        $url = $this->api_url."/leagues/".$this->api_token."?poolID=".$poolID;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send(false);
        $data = json_decode($data, true);
        return $data;
    }
    
    public function parsePoolsData($data = null)
    {
        if($data != null)
        {
            foreach($data as $k => $v)
            {
                $data[$k]['full_image_path'] = FANVICTOR_IMAGE_URL.$this->replaceSuffix($v['image']);
                $data[$k]['result'] = null;

                //parse time
                $data[$k]['startHour'] = date('H', strtotime($v['startDate'])); 
                $data[$k]['startMinute'] = date('i', strtotime($v['startDate'])); 
                $data[$k]['cutHour'] = date('H', strtotime($v['cutDate'])); 
                $data[$k]['cutMinute'] = date('i', strtotime($v['cutDate'])); 
                $data[$k]['startDate'] = date('Y-m-d', strtotime($v['startDate'])); 
                $data[$k]['cutDate'] = date('Y-m-d', strtotime($v['cutDate'])); 
            }
        }
        return $data;
    }
    
    public function parseFightsData($data = null)
    {
        if($data != null)
        {
            $count = 0;
            foreach($data as $k => $v)
            {
                $count++;
                $data[$k]['count'] = $count;
            }
        }
        return $data;
    }
    
    public function calculatePrizes($poolID, $type, $structure, $size, $entryFee)
    {
        //default percent
        $winnerPercent = get_option('fanvictor_winner_percent');
        $firstPercent = get_option('fanvictor_first_place_percent');
        $secondPercent = get_option('fanvictor_second_place_percent');
        $thirdPercent = get_option('fanvictor_third_place_percent');
        
        $result = array();
        if($type == 'head2head')
        {
            $size = 2;
            $structure = "winnertakeall";
        }
        if((int)$entryFee > 0)
        {
            $prize = $size * $entryFee * $winnerPercent / 100;
            switch($structure)
            {
                case "winnertakeall":
                    $result[] = floor($prize);
                    break;
                case "top3":
                    $result[] = $this->addInsufficientZeroToMoneyFormat(round($prize * $firstPercent / 100, 2));//1st
                    $result[] = $this->addInsufficientZeroToMoneyFormat(round($prize * $secondPercent / 100, 2));//2nd
                    $result[] = $this->addInsufficientZeroToMoneyFormat(round($prize * $thirdPercent / 100, 2));//3th
                    break;
                /*default :
                    break;*/
            }
        }
        return $result;
    }
    
    private function addInsufficientZeroToMoneyFormat($str)
    {
        if ( substr($str, -2, 1) == '.' )
        {
            $str .= '0';
        }
        return $str;
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
    
    //////////////////////////////////////////add, update, delete pools//////////////////////////////////////////
    public function add($aVals)
    {
        $url = $this->api_url."/addPools/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $poolID = $client->send($this->parsePoolsDataForModify($aVals));

        //upload new image
        $this->uploadImage($poolID);
        
        //add livepool
        if(isset($aVals['live_pool']) && $aVals['live_pool'] == 1)
        {
            $this->addLivePool($poolID);
        }
        else
        {
            $this->deleteLivePool($poolID);
        }
        
        //insert fight
        if((int)$poolID > 0)
        {
            $this->addFights($aVals, $poolID);
            return true;
        }
        return false;
    }
    
    public function addLivePool($poolID)
    {
        $url = $this->api_url."/addLivePool/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        return $client->send(array('poolID' => $poolID));
    }
    
    public function deleteLivePool($poolID)
    {
        $url = $this->api_url."/deleteLivePool/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        return $client->send(array('poolID' => $poolID));
    }
    
    public function addFights($aVals, $poolID)
    {
        foreach($aVals['fight'] as $index)
        {
            $data = $this->parseFightsDataForModify($aVals, $index, $poolID);
            $url = $this->api_url."/addFights/".$this->api_token;
            $client = $this->getRestClient("POST", $url);
            $client->send($data);
        }
    }
    
    public function update($aVals)
    {
        $url = $this->api_url."/updatePools/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $result = $client->send($this->parsePoolsDataForModify($aVals, true));
        
        //add livepool
        if(isset($aVals['live_pool']) && $aVals['live_pool'] == 1)
        {
            $this->addLivePool($aVals['poolID']);
        }
        else
        {
            $this->deleteLivePool($aVals['poolID']);
        }
        
        //if new image uploaded, delete old image
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            //get current image name
            $sFileName = $this->getPoolImageName($aVals['poolID']);

            //delete old image
            $this->deleteImage($sFileName);
            
            //upload new image
            $this->uploadImage($aVals['poolID']);
        }

        //update fights
        if($result)
        {
            $this->updateFights($aVals, $aVals['poolID']);
            return true;
        }
        return false;
    }
    
    public function updatePoolsImage($poolID, $image)
    {
        $url = $this->api_url."/updatePools/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $client->send(array('poolID' => $poolID, 'image' => $image));
    }

    public function updatePoolStatus($poolID, $status = 'NEW')
    {
        $data = array('poolID' => $poolID,
                      'status' => $status);
        $url = $this->api_url."/updatePools/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        return $client->send($data);
    }
    
    public function updatePoolComplete($poolID)
    {
        $data = array('poolID' => $poolID);
        $url = $this->api_url."/updatePoolComplete/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $data = $client->send($data);
        
        //update money for winners
        $this->updateUserMoneyWon();
        
        return true;
    }
    
    public function updateUserMoneyWon()
    {
        $url = $this->api_url."/userWonHistory/".$this->api_token;
        $client = $this->getRestClient("GET", $url);
        $data = $client->send();
        $aDatas = json_decode($data, true);
        
        if($aDatas != null)
        {
            $myUser = $this->payment->getUserData(get_current_user_id());
            $myEmail = $myUser['email'];
            $success = true;
            foreach($aDatas as $aData)
            {
                if($this->payment->updateUserBalance($aData['amount'], false, $aData['leagueID'], $aData['userID']))
                {
                    $aUser = $this->payment->getUserData($aData['userID']);
                    $this->payment->addFundhistory($aData['amount'], $aData['leagueID'], $aUser['balance'], $aData['type'], $aData['operation'], $aData['userID'], null, $aData['comment']);

                    //send email
                    $email = $aUser['email'];
                    $website = 'http://'.$_SERVER['SERVER_NAME'];
                    $siteTitle = get_option('blogname');
                    $place = '';
                    switch($aData['rank'])
                    {
                        case 1:
                            $place = '1st';
                            break;
                        case 2:
                            $place = '2nd';
                            break;
                        case 3:
                            $place = '3th';
                            break;
                    }
                    $headers  = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                    $headers .= 'To: ' . $email . "\r\n";
                    $headers .= "From: $myEmail". "\r\n";
                    //$headers .= 'Bcc: ' . $myEmail . "\r\n";
                    $emailInfo = array('league_name' => $aData['league_name'],
                                       'username' => $aUser['user_name'],
                                       'money' => $aData['amount'],
                                       'place' => $place);
                    
                    if($aData['type'] == "WIN")
                    {
                        include 'emailTemplates/wonLeague.php';
                    }
                    else if($aData['type'] == "REFUND")
                    {
                        include 'emailTemplates/leagueNotFilledNotice.php';
                    }
                    mail($email, $message_subject, $message_body, $headers);
                    exit('aaaaa');
                }
                else
                {
                    $success = false;
                }
            }
        }
    }

    public function updateFights($aVals, $poolID)
    {
        $newFightIds = array();
        //load current fight
        $curFight = $this->getFights($poolID);
        $curFightID = array();
        if($curFight != null)
        {
            foreach($curFight as $item)
            {
                $curFightID[] = $item['fightID'];
            }
        }

        //parse fight to update, add new or delete
        foreach($aVals['fight'] as $index)
        {
            $fightID = $aVals['fightID'][$index];
            if((int)$fightID > 0 && in_array($fightID, $curFightID)) //update
            {
                $data = $this->parseFightsDataForModify($aVals, $index, $poolID, true);
                $url = $this->api_url."/updateFights/".$this->api_token;
                $client = $this->getRestClient("POST", $url);
                $client->send($data);
                
                //clear updated fight
                if(($key = array_search($fightID, $curFightID)) !== false)
                {
                    unset($curFightID[$key]);
                    array_values($curFightID);
                }
            }
            else //add
            {
                $data = $this->parseFightsDataForModify($aVals, $index, $poolID);
                $url = $this->api_url."/addFights/".$this->api_token;
                $client = $this->getRestClient("POST", $url);
                $newFightIds[] = $client->send($data);
            }
        }
        
        //update new fixture for league
        if($newFightIds != null)
        {
            $newFightIds = implode(',', $newFightIds);
            $aLeagues = $this->getLeagues($poolID);
            if($aLeagues != null)
            {
                foreach($aLeagues as $aLeague)
                {
                    $fixtures = $aLeague['fixtures'].','.$newFightIds;
                    $data = array('leagueID' => $aLeague['leagueID'], 'fixtures' => $fixtures); 
                    $url = $this->api_url."/updateLeague/".$this->api_token;
                    $client = $this->getRestClient("POST", $url);
                    $data = $client->send($data);
                }
            }
        }
        
        //delete
        foreach($curFightID as $item)
        {
            $data = array('poolID' => $poolID, 'fightID' => $item);
            $url = $this->api_url."/deleteFights/".$this->api_token;
            $client = $this->getRestClient("POST", $url);
            $client->send($data);
        }
    }
    
    public function updateFightResult($data)
    {
        $url = $this->api_url."/updateFights/".$this->api_token;
        $client = $this->getRestClient("POST", $url);
        $result = $client->send($data);
        if($result)
        {
            return true;
        }
        return false;
    }
    
    private function parsePoolsDataForModify($aVals, $isUpdate = false)
    {
        $data = array('poolName' => str_replace("\'", "'", $aVals['poolName']),
                      'startDate' => $aVals['startDate'].' '.$aVals['startHour'].':'.$aVals['startMinute'].':00',
                      'cutDate' => $aVals['cutDate'].' '.$aVals['cutHour'].':'.$aVals['cutMinute'].':00',
                      'organization' => $aVals['organization'],
                      'type' => $aVals['type']);
        if($isUpdate)
        {
            $data['poolID'] = $aVals['poolID'];
        }
        return $data;
    }
    
    private function parseFightsDataForModify($aVals, $index, $poolID, $isUpdate = false)
    {
        $data = array('poolID' => $poolID,
                    'fighterID1' => $aVals['fighterID1'][$index],
                    'fighterID2' => $aVals['fighterID2'][$index],
                    'name' => $aVals['fight_name'][$index],
                    'champFight' => isset($aVals['champFight'][$index]) && $aVals['champFight'][$index] == 1 ? 'YES' : 'NO',
                    'amateurFight' => isset($aVals['amateurFight'][$index]) && $aVals['amateurFight'][$index] == 1 ? 'YES' : 'NO',
                    'mainFight' => isset($aVals['mainFight'][$index]) && $aVals['mainFight'][$index] == 1 ? 'YES' : 'NO',
                    'prelimFight' => isset($aVals['prelimFight'][$index]) && $aVals['prelimFight'][$index] == 1 ? 'YES' : 'NO',
                    'rounds' => $aVals['rounds'][$index],
                    'fightOrder' => $index);
        if($isUpdate)
        {
            $data['fightID'] = $aVals['fightID'][$index];
        }
        return $data;
    }

    public function delete($poolId)
    {
        $sFileName = $this->getPoolImageName($poolId);
        $url = $this->api_url."/deletePools/".$this->api_token."?poolID = ".$poolId;
        $client = $this->getRestClient("POST", $url);
        $result = $client->send(array('poolID' => $poolId));
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
            $this->updatePoolsImage($iId, $image);
            
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
