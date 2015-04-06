<?php
class Contest
{
    private static $orgs;
    private static $pools;
    private static $playerposition;
    private static $players;
    private static $teams;
    private static $fanvictor;
    public function __construct() 
    {
        self::$orgs = new Organizations();
        self::$pools = new Pools();
        self::$playerposition = new PlayerPosition();
        self::$players = new Players();
        self::$teams = new Teams();
        self::$fanvictor = new Fanvictor();
    }
    
	public static function process()
	{    
        add_action( 'wp_enqueue_scripts', array('Contest', 'theme_name_scripts') );
        add_filter('the_content', array('Contest', 'contest'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('playerdraft.js', FANVICTOR__PLUGIN_URL_JS.'playerdraft.js', 5);
        wp_enqueue_script('accounting.js', FANVICTOR__PLUGIN_URL_JS.'accounting.js', 5);
        wp_enqueue_script('fanvictor.js', FANVICTOR__PLUGIN_URL_JS.'fanvictor.js', 5);
        wp_enqueue_style('playerdraft.css', FANVICTOR__PLUGIN_URL_CSS.'playerdraft_contest.css');
    }

    public static function contest()
    {
        $leagueId = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? $_GET['num'] : 1;
        if(!self::$fanvictor->isPlayerDraftLeagueExist($leagueId))
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('Please create a new contest', FV_DOMAIN), true);
        }
        else 
        {
            $aData = self::$fanvictor->getContestResult($leagueId);
            $league = $aData['league'];
            $scoringCats = $aData['scoring_cat'];
            $bonus = $aData['bonus'];
            $aRounds = $aData['rounds'];
            
            //pool
            self::$pools->selectField(array('startDate'));
            $aPool = self::$pools->getPools($league['poolID'], null, false, true);

            //$league['startDate'] = $aPool['startDate'];
            $league = self::$fanvictor->parseLeagueData(array($league));
            $league = $league[0];

            //fight
            self::$pools->selectField(array('fightID', 'name', 'fighterID1', 'fighterID2', 'startDate', 'team1score', 'team2score'));
            $aFights = self::$pools->getFights($league['poolID'], explode(',', $league['fixtures']));
            $aFights = self::$pools->parseFightsDataDetail($aFights);

            include FANVICTOR__PLUGIN_DIR_VIEW.'contest.php';
        }
    }
}
?>