<?php
$Fanvictor_Contests = new Fanvictor_Contests();
class Fanvictor_Contests
{
    private static $orgs;
    private static $fanvictor;
    private static $leagues;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    private static $playerposition;
    private static $pools;
    public function __construct() 
    {
        self::$orgs = new Organizations();
        self::$fanvictor = new Fanvictor();
        self::$leagues = new Leagues();
        self::$playerposition = new PlayerPosition();
        self::$pools = new Pools();
        self::$url = admin_url().'admin.php?page=manage-contests';
        self::$urladdnew = admin_url().'admin.php?page=add-contests';
        self::$urladd = wp_get_referer();
    }
    
    public static function manageContests()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , FV_DOMAIN);
        }

        //load css js
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        
        //task action delete
        if(isset($_POST["task"]) && $task = $_POST["task"])
        {
            switch($task)
            {
                case "delete":
                    self::delete();
                    break;
            }
        }

        include FANVICTOR__PLUGIN_DIR_VIEW.'contests/class.table-contests.php';
        $myListTable = new TableContests();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null);
        include FANVICTOR__PLUGIN_DIR_VIEW.'contests/index.php';
    }
    
    public static function addContests()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , FV_DOMAIN);
        }
        
        //load css js
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('fight.js', FANVICTOR__PLUGIN_URL_JS.'admin/fight.js');
        wp_enqueue_script('createcontest.js', FANVICTOR__PLUGIN_URL_JS.'createcontest.js', 5);
        wp_enqueue_script('accounting.js', FANVICTOR__PLUGIN_URL_JS.'accounting.js');
        
        //edit data
        $bIsEdit = false;
		if (isset($_GET['id']) && $iEditId = $_GET['id'])
		{
            $bIsEdit = true;
            $aForms = self::$fanvictor->getLeagueDetail($iEditId);
            $aForms = $aForms[0];
		}
        else
        {
            $aForms = null;
        }

        //add or update
		self::modify($bIsEdit);

        //pools and fights
        $aDatas = self::$pools->getNewPools();
        $aPools = htmlentities(json_encode($aDatas['pools']), ENT_QUOTES);
        $aFights = htmlentities(json_encode($aDatas['fights']), ENT_QUOTES);
        $aRounds = htmlentities(json_encode($aDatas['rounds']), ENT_QUOTES);
        
        //sports
        $aSports = self::$fanvictor->getListSports();
        if($aSports != null)
        {
            foreach($aSports as $k => $aSport)
            {
                if(!empty($aSport['child']))
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
        
        //position
        $aPositions = self::$playerposition->getPlayerPosition();
        $aPositions = json_encode($aPositions);
        
        $aLeagueSizes = get_option('fanvictor_league_size');
        $aEntryFees = get_option('fanvictor_entry_fee');
        include FANVICTOR__PLUGIN_DIR_VIEW.'contests/add.php';
    }

    private static function validData($aVals)
    {
        $valid = self::$fanvictor->validCreateLeague($_POST['organizationID'], $_POST['poolID'], 
                                                     $_POST['game_type'], $_POST['leaguename'], 
                                                     isset($_POST['fightID']) ? $_POST['fightID'] : null,
                                                     $_POST['roundID'], 
                                                     isset($_POST['payouts_from']) ? $_POST['payouts_from'] : null,
                                                     isset($_POST['payouts_to']) ? $_POST['payouts_to'] : null,
                                                     isset($_POST['percentage']) ? $_POST['percentage'] : null);
        
        switch($valid)
        {
            case 2;
                redirect(self::$urladd, __('Sport does not exist. Please try again.', FV_DOMAIN));
                break;
            case 3;
                redirect(self::$urladd, __('Date does not exist. Please try again.', FV_DOMAIN));
                break;
            case 4;
                redirect(self::$urladd, __('Fixture does not exist. Please try again.', FV_DOMAIN));
                break;
            case 5;
                redirect(self::$urladd, __('Please select at least a fixture.', FV_DOMAIN));
                break;
            case 6;
                redirect(self::$urladd, __('This game type does not exist.', FV_DOMAIN));
                break;
            case 7;
                redirect(self::$urladd, __('This sport does not support playerdraft type.', FV_DOMAIN));
                break;
            case 8;
                redirect(self::$urladd, __('Please enter league name', FV_DOMAIN));
                break;
            case 9;
                redirect(self::$urladd, __('Round does not exist. Please try again', FV_DOMAIN));
                break;
            case 10;
                redirect(self::$urladd, __('Please select at least two rounds', FV_DOMAIN));
                break;
            case 11;
                redirect(self::$urladd, __('Invalid payouts', FV_DOMAIN));
                break;
        }
        
        if(!in_array($_POST['leagueSize'], get_option('fanvictor_league_size')))
        {
            redirect(self::$urladd, __('League size does not exist', FV_DOMAIN));
        }
        else if($_POST['entry_fee'] > 0 && !in_array($_POST['entry_fee'], get_option('fanvictor_entry_fee')))
        {
            redirect(self::$urladd, __('Entry fee does not exist', FV_DOMAIN));
        }
        return true;
    }
    
    private static function modify()
    {
        if (isset($_POST) && $aVals = $_POST)
		{
			if (self::validData($aVals))
			{
                if(self::$fanvictor->isLeagueExist($aVals['leagueID'])) //update
                {
                    if (self::$fanvictor->createLeague($_POST))
                    {
                        redirect(self::$urladd, __('Succesfully updated', FV_DOMAIN));
                    }
                }
                else //add
                {   
                    $leagueID = self::$fanvictor->createLeague($_POST);
                    if((int)$leagueID > 0)
                    {
                        redirect(self::$urladd, __('Succesfully added', FV_DOMAIN));
                    }
                }
                redirect(self::$urladd, __('Something went wrong! Please try again.', FV_DOMAIN));
			}
		}
    }
    
    private static function delete()
	{
        if ($aIds = $_POST['id'])
		{
			$iDeleted = 0;
			foreach ($aIds as $iId)
			{
				if (self::$leagues->delete($iId))
				{
					$iDeleted++;
				}
			}
			
			if ($iDeleted > 0)
			{
                redirect(self::$url, 'Succesfully deleted');
			}
		}
        redirect(self::$url);
	}
}
?>