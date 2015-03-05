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
        $aPools = $aSports = $aFights = null;
        
        //pools and fights
        $aDatas = self::$fanvictor->getNewPools();
        if($aDatas['pools'] != null)
        {
            $aPools = htmlentities(json_encode($aDatas['pools']), ENT_QUOTES);
            $aFights = htmlentities(json_encode($aDatas['fights']), ENT_QUOTES);
            $aRounds = htmlentities(json_encode($aDatas['rounds']), ENT_QUOTES);

            //sports
            $aSports = self::$fanvictor->getListSports();
            if($aSports != null)
            {
                foreach($aSports as $k => $aSport)
                {
                    if(isset($aSport['child']))
                    {
                        foreach($aSport['child'] as $k2 => $org)
                        {
                            $total = 0;
                            if($aDatas['pools'] != null)
                            {
                                foreach($aDatas['pools'] as $aPool)
                                {
                                    if($aPool['organization'] == $org['id'])
                                    {
                                        $total++;
                                        break;
                                    }
                                }
                            }
                            if($total == 0)
                            {
                                unset($aSport['child'][$k2]);
                            }
                        }
                        $aSports[$k]['child'] = array_values($aSport['child']);
                        if($aSports[$k]['child'] == null)
                        {
                            unset($aSports[$k]);
                        }
                    }
                }
                $aSports = array_values($aSports);
            }

            //game type
            $aGameTypes = self::$orgs->getGameType();
            $aLeagueSizes = get_option('fanvictor_league_size');
            $aEntryFees = get_option('fanvictor_entry_fee');
        }
        include FANVICTOR__PLUGIN_DIR_VIEW.'createcontest.php';
    }
    
    public static function submitPick()
    {
        //check valid
        self::validData();
        
        //add
        $leagueID = self::$fanvictor->createLeague($_POST);

        if((int)$leagueID < 1)
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __("Something went wrong! Please try again"), true);
        }
        else if($_POST['game_type'] == 'playerdraft')
        {
            redirect(FANVICTOR_URL_GAME.$leagueID, null, true);
        }
        else 
        {
            redirect(FANVICTOR_URL_SUBMIT_PICKS.$leagueID, null, true);
        }
    }
    
    private static function validData()
    {
        $valid = self::$fanvictor->validCreateLeague($_POST['organizationID'], $_POST['poolID'], 
                                                     $_POST['game_type'], $_POST['leaguename'], 
                                                     isset($_POST['fightID']) ? $_POST['fightID'] : null);
        switch($valid)
        {
            case 2;
                redirect(FANVICTOR_URL_CREATE_CONTEST, __('Sport does not exist. Please try again.'), true);
                break;
            case 3;
                redirect(FANVICTOR_URL_CREATE_CONTEST, __('Date does not exist. Please try again.'), true);
                break;
            case 4;
                redirect(FANVICTOR_URL_CREATE_CONTEST, __('Fixture does not exist. Please try again.'), true);
                break;
            case 5;
                redirect(FANVICTOR_URL_CREATE_CONTEST, __('Please select at least a fixture.'), true);
                break;
            case 6;
                redirect(FANVICTOR_URL_CREATE_CONTEST, __('This game type does not exist.'), true);
                break;
            case 7;
                redirect(FANVICTOR_URL_CREATE_CONTEST, __('This sport does not support playerdraft type.'), true);
                break;
            case 8;
                redirect(FANVICTOR_URL_CREATE_CONTEST, __('Please enter league name'), true);
                break;
            case 9;
                redirect(FANVICTOR_URL_CREATE_CONTEST, __('Round does not exist. Please try again'), true);
                break;
            case 10;
                redirect(FANVICTOR_URL_CREATE_CONTEST, __('Please select at least a round'), true);
                break;
        }

        if(!in_array($_POST['leagueSize'], get_option('fanvictor_league_size')))
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('League size does not exist'), true);
        }
        else if($_POST['entry_fee'] > 0 && !in_array($_POST['entry_fee'], get_option('fanvictor_entry_fee')))
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('Entry fee does not exist'), true);
        }
    }
}
?>