<?php
class Historygamesgrid
{
    private static $fanvictor;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
    }
    
    public function process()
    { 
        $result = array();
		if ( ($jsonData = self::$fanvictor->getHistoryContests()) && ($jsonObject = json_decode($jsonData)) )
        {
            if ( isset($jsonObject->success) && $jsonObject->success )
			{
                $result['historyContests'] = $jsonObject->html;
                $result['sHeader'] = __("Contest history");
			}
            else 
                $result['errorMessage'] = __('<br>Error getting contest history');
        }
        else
        {
            $result['errorMessage'] = __('<br>Error occured error occured could not get contest history');
        }
        return $result;
    }
}
?>