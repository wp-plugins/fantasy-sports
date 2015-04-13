<?php
class FutureEvents
{
    private static $fanvictor;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
    }
	public static function process()
	{       
        add_action('wp_enqueue_scripts', array('FutureEvents', 'theme_name_scripts'));
        add_filter('the_content', array('FutureEvents', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('mycontests.js', FANVICTOR__PLUGIN_URL_JS.'mycontests.js');
        wp_enqueue_script('fanvictor.js', FANVICTOR__PLUGIN_URL_JS.'fanvictor.js');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
    }

    public static function addContent()
    {
        if(!in_the_loop())
        {
            return;
        }
        $sHeader = __("Future Events", FV_DOMAIN);
        $futureEvents = self::$fanvictor->getFutureEvents();
        include FANVICTOR__PLUGIN_DIR_VIEW.'futureevents.php';
    }
}
?>