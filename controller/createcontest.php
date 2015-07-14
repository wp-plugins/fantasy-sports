<?php
class Createcontest
{
    private static $orgs;
    private static $pools;
    private static $payment;
    private static $fanvictor;
    public function __construct() 
    {
        self::$orgs = new Organizations();
        self::$pools = new Pools();
        self::$payment = new Payment();
        self::$fanvictor = new Fanvictor();
    }
    
	public static function process()
	{    
        if(isset($_POST) && isset($_POST["submitContest"]))
        {
            add_action( 'wp_enqueue_scripts', array('Createcontest', 'theme_name_scripts') );
            add_filter('the_content', array('Createcontest', 'submitPick'));
        }
        else 
        {
            add_action( 'wp_enqueue_scripts', array('Createcontest', 'theme_name_scripts') );
            add_filter('the_content', array('Createcontest', 'addContent'));
        }
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('createcontest.js', FANVICTOR__PLUGIN_URL_JS.'createcontest.js', 5);
        wp_enqueue_script('jquery.session.js', FANVICTOR__PLUGIN_URL_JS.'jquery.session.js',5);
        wp_enqueue_style('playerdraft.css', FANVICTOR__PLUGIN_URL_CSS.'playerdraft.css');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

    public static function addContent()
    {
        if(!in_the_loop())
        {
            return;
        }
        
        $aDatas = self::$fanvictor->loadCreateLeagueForm();
        $aPools = htmlentities(json_encode($aDatas['pools']), ENT_QUOTES);
        $aFights = htmlentities(json_encode($aDatas['fights']), ENT_QUOTES);
        $aRounds = htmlentities(json_encode($aDatas['rounds']), ENT_QUOTES);
        $aSports = $aDatas['sports'];
        $aGameTypes = $aDatas['game_type'];
        $aLeagueSizes = get_option('fanvictor_league_size');
        $aEntryFees = get_option('fanvictor_entry_fee');
        include FANVICTOR__PLUGIN_DIR_VIEW.'createcontest.php';
    }
}
?>