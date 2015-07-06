<?php
class Statistics
{
    private static $orgs;
    private static $pools;
    private static $payment;
    private static $fanvictor;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
    }
    
	public static function process()
	{
		add_action( 'wp_enqueue_scripts', array('Statistics', 'stat_scripts') );
        add_filter('the_content', array('Statistics', 'addContent'));
	}
    
    public static function stat_scripts()
    {
        wp_enqueue_script('stats.js', FANVICTOR__PLUGIN_URL_JS.'stats.js', 5);
        //wp_enqueue_script('jquery.session.js', FANVICTOR__PLUGIN_URL_JS.'jquery.session.js',5);
       // wp_enqueue_style('playerdraft.css', FANVICTOR__PLUGIN_URL_CSS.'playerdraft.css');
       // wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

	public static function addContent()
	{
		if(!in_the_loop()){
			return;
		}
		
		list($aSports, $aPools, $aPos , $aRounds)=self::$fanvictor->getStatData();
		
		include FANVICTOR__PLUGIN_DIR_VIEW.'statistics.php';
	}
}
?>