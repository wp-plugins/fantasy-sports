<?php
class MyUpcomingEntries
{
    private static $fanvictor;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
    }
	public static function process()
	{       
        add_action('wp_enqueue_scripts', array('MyUpcomingEntries', 'theme_name_scripts'));
        add_filter('the_content', array('MyUpcomingEntries', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('mycontests.js', FANVICTOR__PLUGIN_URL_JS.'mycontests.js');
        wp_enqueue_script('fanvictor.js', FANVICTOR__PLUGIN_URL_JS.'fanvictor.js');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

    public static function addContent()
    {
		$jsonData = self::$fanvictor->getUpcomingContests();
       
		if (($jsonData = self::$fanvictor->getUpcomingContests()) && ($jsonObject = json_decode($jsonData)) )
        {
            if ( isset($jsonObject->success) && $jsonObject->success )
			{
                $upcomingContests = $jsonObject->html;
                $sHeader = __("My Upcoming Entries");
			}
            else 
                $errorMessage = __('<br>Error getting my upcoming entries');
        }
        else
        {
            $errorMessage = __('<br>Error occured could not get my upcoming entries');
        }
        include FANVICTOR__PLUGIN_DIR_VIEW.'myupcomingentries.php';
    }
}
?>