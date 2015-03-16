<?php
class Rankings
{
    private static $payment;
    private static $fanvictor;
    public function __construct() 
    {
        self::$payment = new Payment();
        self::$fanvictor = new Fanvictor();
    }
    
	public static function process()
	{  
        $leagueID = pageSegment(3);
        if(self::$fanvictor->isPlayerDraftLeagueExist($leagueID))
        {
            redirect(FANVICTOR_URL_CONTEST.$leagueID, null, true);
        }
        else 
        {
            add_action( 'wp_enqueue_scripts', array('Rankings', 'theme_name_scripts') );
            add_filter('the_content', array('Rankings', 'addContent'));
        }
	}
    
    public static function theme_name_scripts()
    {
       // wp_enqueue_script('leagues.class.js', FANVICTOR__PLUGIN_URL_JS.'leagues.class.js');
        wp_enqueue_script('rankings.js', FANVICTOR__PLUGIN_URL_JS.'rankings.js');
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
    }

    public static function addContent()
    {
        $leagueID = pageSegment(3);
        if($leagueID > 0)
        {
            $leagueID = pageSegment(3);
        }
        
        if(!self::$fanvictor->isNormalLeagueExist($leagueID))
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('Contest not found'));
        }
        else 
        {
            $aDatas = self::$fanvictor->getLeagueHeader($leagueID);
            $aLeague = $aDatas['league'];
            $aPool = $aDatas['pool'];
            $creator = get_user_by("id", $aLeague['creator_userID']);
            
            //friend
            $aFriends = self::$fanvictor->getAllPlayerInfo();
            sort($aFriends, SORT_ASC);
            usort($aFriends, function($a, $b){
                $a = strtolower($a['full_name'] ? $a['full_name'] : $a['user_name']);
                $b = strtolower($b['full_name'] ? $b['full_name'] : $b['user_name']);
                return strcmp($a, $b);
            });
            
            //allow show popup
            $showInviteFriends = false;
            if(isset($_SESSION['showInviteFriends'.$leagueID]) && $aPool['status'] == "NEW")
            {
                unset($_SESSION['showInviteFriends'.$leagueID]);
                $showInviteFriends = true;
            }

            include FANVICTOR__PLUGIN_DIR_VIEW.'rankings.php';
        }
    }
}
?>