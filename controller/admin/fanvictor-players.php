<?php
$Fanvictor_Players = new Fanvictor_Players();
class Fanvictor_Players
{
    private static $fanvictor;
    private static $teams;
    private static $playerposition;
    private static $players;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
        self::$teams = new Teams();
        self::$playerposition = new PlayerPosition();
        self::$players = new Players();
        self::$url = admin_url().'admin.php?page=manage-players';
        self::$urladdnew = admin_url().'admin.php?page=add-players';
        self::$urladd = wp_get_referer();
    }
    
    public static function managePlayers()
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

        include FANVICTOR__PLUGIN_DIR_VIEW.'players/class.table-players.php';
        $myListTable = new TablePlayers();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null);
        include FANVICTOR__PLUGIN_DIR_VIEW.'players/index.php';
    }
    
    public static function addPlayers()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , FV_DOMAIN);
        }
        
        //load css js
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('players.js', FANVICTOR__PLUGIN_URL_JS.'admin/players.js');
        wp_enqueue_script('accounting.js', FANVICTOR__PLUGIN_URL_JS.'accounting.js');
        
        //edit data
        $bIsEdit = false;
		if (isset($_GET['id']) && $iEditId = $_GET['id'])
		{
            $bIsEdit = true;
            $aForms = self::$players->getplayers($iEditId);
            $aForms = self::$players->parsePlayersData($aForms);
            $aForms = $aForms[0];
		}
        else
        {
            $aForms = null;
            $aFights = array(null);
        }

        //add or update
		self::modify($bIsEdit);

        $aSports = self::$fanvictor->getListSports();
        $aTeams = self::$teams->getTeams(null, null, true, true);
        $aPositions = self::$playerposition->getPlayerPosition(null, null, true);
        $aIndicators = self::$players->getIndicator();
        include FANVICTOR__PLUGIN_DIR_VIEW.'players/add.php';
    }

    private static function validData($aVals)
    {
        $aPlayer = self::$players->getplayers($aVals['id']);
        if($aPlayer[0]['siteID'] > 0 || $aVals['id'] == '')
        {
            if(empty($aVals['name']))
            {
                redirect(self::$urladd, 'Provide a name');
            }
            else if(empty($aVals['salary']))
            {
                redirect(self::$urladd, 'Provide salary');
            }
        }
        return true;
    }
    
    private static function modify()
    {
        if (isset($_POST['val']) && $aVals = $_POST['val'])
		{
			if (self::validData($aVals))
			{
                if(self::$players->isPlayersExist($aVals['id'])) //update
                {
                    if (self::$players->update($aVals))
                    {
                        redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if (self::$players->add($aVals))
                    {
                        redirect(self::$urladd, 'Succesfully added');
                    }
                }
                redirect(self::$urladd, 'Something went wrong! Please try again.');
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
				if (self::$players->delete($iId))
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