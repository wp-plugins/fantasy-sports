<?php
class MyLiveEntries
{
    private static $fanvictor;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
    }
	public static function process()
	{       
        add_action('wp_enqueue_scripts', array('MyLiveEntries', 'theme_name_scripts'));
        add_filter('template_include', array('MyLiveEntries', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('mycontests.js', FANVICTOR__PLUGIN_URL_JS.'mycontests.js');
        wp_enqueue_script('fanvictor.js', FANVICTOR__PLUGIN_URL_JS.'fanvictor.js');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

    public static function addContent()
    {
		if ( ($jsonData = self::$fanvictor->getLiveContests()) && ($jsonObject = json_decode($jsonData)) )
        {
            if ( isset($jsonObject->success) && $jsonObject->success )
			{
                $isLive = $jsonObject->isLive;
                $sHeader = __("My Live Entries");
			}
            else 
                $errorMessage = __('<br>Error getting my live entries');
        }
        else
        {
            $errorMessage = __('<br>Error occured could not get my live entries');
        }
        include FANVICTOR__PLUGIN_DIR_VIEW.'myliveentries.php';
    }
}
?>