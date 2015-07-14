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
        if(!empty($_GET['leagueID']))
        {
            self::exportUserPicks($_GET['leagueID']);
        }
        
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , FV_DOMAIN);
        }

        //load css js
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');
        
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
    
    private static function exportUserPicks($leagueID)
    {
        if($leagueID > 0)
        {
            $data = self::$fanvictor->showUserPicks($leagueID);
            $users = $data['picks'];
            $league = $data['league'];
            if($data['result'] == 0)
            {
                redirect(self::$url, __('This league does not exist.', FV_DOMAIN));
            }
            else 
            {
                ob_clean();
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename=data.csv');
                $file = fopen('php://output', 'w');
                
                fputcsv($file, array('League Name: '.$league['name']));
                fputcsv($file, array('Game Type: '.$league['gameType']));
                
                if($users != null)
                {
                    if($league['gameType'] == 'PLAYERDRAFT' && $league['is_team'] == 1)
                    {
                        fputcsv($file, array('Num', 'User', 'Entry Number', 'ID', 'Team Name', 'Pick Name'));
                    }
                    else if($league['gameType'] != 'PLAYERDRAFT') 
                    {
                        fputcsv($file, array('Num', 'User', 'Entry Number', 'ID', 'Fight Name', 'Pick Name'));
                    }
                    else 
                    {
                        fputcsv($file, array('Num', 'User', 'Entry Number', 'ID', 'Pick Name'));
                    }
                    foreach ($users as $ku => $user)
                    {
                        if($user['entries'] != null)
                        {
                            foreach ($user['entries'] as $entry)
                            {
                                if($entry['pick_items'] != null)
                                {
                                    foreach ($entry['pick_items'] as $k => $pick)
                                    {
                                        $num = $login_name = $entry_number = '';
                                        if($k == 0)
                                        {
                                            $num = $ku + 1;
                                            $login_name = $user['user_login'];
                                            $entry_number = $entry['entry_number'];
                                        }
                                        if($league['gameType'] == 'PLAYERDRAFT' && $league['is_team'] == 1)
                                        {
                                            fputcsv($file, array($num, $login_name, $entry_number, $pick['id'], $pick['team_name'], $pick['name']));
                                        }
                                        else if($league['gameType'] != 'PLAYERDRAFT') 
                                        {
                                            fputcsv($file, array($num, $login_name, $entry_number, $pick['id'], $pick['fight_name'], $pick['name']));
                                        }
                                        else 
                                        {
                                            fputcsv($file, array($num, $login_name, $entry_number, $pick['id'], $pick['name']));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else 
                {
                    fputcsv($file, array("No picks"));
                }

                fclose($file);
                exit;
            }
        }
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
        $iEditId = isset($_GET['id']) ? $_GET['id'] : null;
        $bIsEdit = $iEditId > 0 ? true : false;

        //add or update
		self::modify($bIsEdit);

        //pools and fights
        $aDatas = self::$fanvictor->loadCreateLeagueForm($iEditId);
        $aPools = htmlentities(json_encode($aDatas['pools']), ENT_QUOTES);
        $aFights = htmlentities(json_encode($aDatas['fights']), ENT_QUOTES);
        $aRounds = htmlentities(json_encode($aDatas['rounds']), ENT_QUOTES);
        $aSports = $aDatas['sports'];
        $aGameTypes = $aDatas['game_type'];
        $aPositions = json_encode($aDatas['player_positions']);
        $aForms = $aDatas['league'];
        $allowCustomSpread = $aDatas['allow_custom_spread'];
        
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