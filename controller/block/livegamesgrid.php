<?php
class Livegamesgrid
{
    private static $fanvictor;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
    }
    
    public function process()
    {
        $result = array();
		if ( ($jsonData = self::$fanvictor->getLiveContests()) && ($jsonObject = json_decode($jsonData)) )
        {
            if ( isset($jsonObject->success) && $jsonObject->success )
			{
                $result['isLive'] = $jsonObject->isLive;
                $result['sHeader'] = __("Live contests");
			}
            else 
                $result['errorMessage'] = __('<br>Error getting live contests');
        }
        else
        {
            $result['errorMessage'] = __('<br>Error occured could not get live contests');
        }

        return $result;
    }
}
?>
