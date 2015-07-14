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
        
        
        $bIsEdit = !empty($_GET['id']) ? true : false;
        $iEditId = !empty($_GET['id']) ? $_GET['id'] : 0;
        
        //add or update
		self::modify();

        //data
        $data = self::$players->getAddPlayer($iEditId);
        $aForms = $data['player'];
        $aForms = self::$players->parsePlayersData($aForms, false);
        $aSports = $data['sports'];
        $aTeams = $data['teams'];
        $aPositions = $data['player_positions'];
        $aIndicators = self::$players->getIndicator();
        include FANVICTOR__PLUGIN_DIR_VIEW.'players/add.php';
    }
    
    private static function modify()
    {
        if (isset($_POST['val']) && $aVals = $_POST['val'])
		{
            $valid = self::$players->add($aVals);
            switch ($valid)
            {
                case 'v1':
                    var_dump($valid);exit;
                    redirect(self::$urladd, __('Please select an organization ', FV_DOMAIN));
                    break;
                case 'v2':
                    redirect(self::$urladd, __('Please select a team', FV_DOMAIN));
                    break;
                case 'v3':
                    redirect(self::$urladd, __('Please select a position ', FV_DOMAIN));
                    break;
                case 'v4':
                    redirect(self::$urladd, __('Provide name', FV_DOMAIN));
                    break;
                case 'v5':
                    redirect(self::$urladd, __('Provide salary', FV_DOMAIN));
                    break;
                case 'u1':
                    redirect(self::$urladd, __('Succesfully updated', FV_DOMAIN));
                    break;
                case 'u1':
                    redirect(self::$urladd, __('Something went wrong! Please try again.', FV_DOMAIN));
                    break;
            }
            redirect(self::$urladd, 'Succesfully added');
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