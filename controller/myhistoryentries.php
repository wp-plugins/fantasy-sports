<?php
class MyHistoryEntries
{
    private static $fanvictor;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
    }
	public static function process()
	{       
        add_action('wp_enqueue_scripts', array('MyHistoryEntries', 'theme_name_scripts'));
        add_filter('template_include', array('MyHistoryEntries', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('mycontests.js', FANVICTOR__PLUGIN_URL_JS.'mycontests.js');
        wp_enqueue_script('fanvictor.js', FANVICTOR__PLUGIN_URL_JS.'fanvictor.js');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

    public static function addContent()
    {
		if ( ($jsonData = self::$fanvictor->getHistoryContests()) && ($jsonObject = json_decode($jsonData)) )
        {
            if ( isset($jsonObject->success) && $jsonObject->success )
			{
                $historyContests = $jsonObject->html;
                $sHeader = __("My History Entries");
			}
            else 
                $errorMessage = __('<br>Error getting my history entries');
        }
        else
        {
            $errorMessage = __('<br>Error occured error occured could not get my history entries');
        }
        include FANVICTOR__PLUGIN_DIR_VIEW.'myhistoryentries.php';
    }
}

?> 
