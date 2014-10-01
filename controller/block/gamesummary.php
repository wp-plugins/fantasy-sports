<?php
class Gamesummary
{    
    private static $fanvictor;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
    }
    
	public function process()
	{
        $result = array();
		$jsonData = self::$fanvictor->getGamesummary();
		$jsonObject = json_decode($jsonData);
		$result['htmlData'] = $jsonObject->html;	
 		$result['sHeader'] = __("Game summary");
        
		return $result; 		
	}
}
 
?> 
