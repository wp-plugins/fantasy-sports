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
        add_filter('the_content', array('MyLiveEntries', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        //wp_enqueue_script('mycontests.js', FANVICTOR__PLUGIN_URL_JS.'mycontests.js');
        wp_enqueue_script('fanvictor.js', FANVICTOR__PLUGIN_URL_JS.'fanvictor.js');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

    public static function addContent()
    {
        if(!in_the_loop())
        {
            return;
        }
        include FANVICTOR__PLUGIN_DIR_VIEW.'myliveentries.php';
    }
}
?>