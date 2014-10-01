<?php
class Upcominggamesgrid
{
    private static $fanvictor;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
    }

    public function process()
    {    
        $jsonData = self::$fanvictor->getUpcomingContests();
        $result = array();
       
		if (($jsonData = self::$fanvictor->getUpcomingContests()) && ($jsonObject = json_decode($jsonData)) )
        {
            if ( isset($jsonObject->success) && $jsonObject->success )
			{
                $result['upcomingContests'] = $jsonObject->html;
                $result['sHeader'] = __("Future Events");
			}
            else 
                $result['errorMessage'] = __('<br>Error getting upcoming contests');
        }
        else
        {
            $result['errorMessage'] = __('<br>Error occured could not get upcoming contests');
        }
        
        return $result;
    }
}
?>
