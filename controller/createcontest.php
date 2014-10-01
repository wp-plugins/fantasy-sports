<?php
class Createcontest
{
    private static $orgs;
    public function __construct() 
    {
        self::$orgs = new Organizations();
    }
    
	public static function process()
	{    
        add_action( 'wp_enqueue_scripts', array('Createcontest', 'theme_name_scripts') );
        add_filter('template_include', array('Createcontest', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('createcontest.js', FANVICTOR__PLUGIN_URL_JS.'createcontest.js', 5);
        wp_enqueue_script('jquery.session.js', FANVICTOR__PLUGIN_URL_JS.'jquery.session.js',5);
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

    public static function addContent()
    {
        $aSports = self::$orgs->getAllSportOrgs(null, true);
        $aLeagueSizes = get_option('fanvictor_league_size');
        $aEntryFees = get_option('fanvictor_entry_fee');
        include FANVICTOR__PLUGIN_DIR_VIEW.'createcontest.php';
    }
}

?> 
