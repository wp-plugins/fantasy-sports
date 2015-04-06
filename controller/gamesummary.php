<?php
class GameSummary
{
    private static $fanvictor;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
    }
    
	public static function process()
	{       
        add_action('wp_enqueue_scripts', array('GameSummary', 'theme_name_scripts'));
        add_filter('the_content', array('GameSummary', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }
    
    public static function addContent()
    {
        $aPools = self::$fanvictor->getGamesummary();
		$htmlData = $aPools['html'];	
 		$sHeader = __("Game summary", FV_DOMAIN);
        include FANVICTOR__PLUGIN_DIR_VIEW.'gamesummary.php';
    }
}
?>