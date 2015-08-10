<?php
class Statistics
{
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
        wp_enqueue_style('font-awesome.css', FANVICTOR__PLUGIN_URL_CSS.'font-awesome/css/font-awesome.css');
    }

	public static function addContent()
	{
		if(!in_the_loop()){
			return;
		}
		
		list($aSports, $aPools, $aTeams, $aPos , $aRounds, $allow_statistic)=self::$fanvictor->getStatData();

        $is_loggedin = (get_current_user_id() > 0) ? 1 : 0;
		include FANVICTOR__PLUGIN_DIR_VIEW.'statistics.php';
	}
}
?>