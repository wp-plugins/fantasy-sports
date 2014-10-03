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
        add_filter('template_include', array('GameSummary', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }
    
    public static function addContent()
    {
        $jsonData = self::$fanvictor->getGamesummary();
		$jsonObject = json_decode($jsonData);
		$htmlData = $jsonObject->html;	
 		$sHeader = __("Game summary");
        include FANVICTOR__PLUGIN_DIR_VIEW.'gamesummary.php';
    }
}
?>