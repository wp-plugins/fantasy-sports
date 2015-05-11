<?php
$Fanvictor_PlayerNews = new Fanvictor_PlayerNews();
class Fanvictor_PlayerNews
{
    private static $fanvictor;
    private static $orgs;
    private static $players;
    private static $playernews;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
        self::$orgs = new Organizations();
        self::$players = new Players();
        self::$playernews = new PlayerNews();
        self::$url = admin_url().'admin.php?page=manage-playernews';
        self::$urladdnew = admin_url().'admin.php?page=add-playernews';
        self::$urladd = wp_get_referer();
    }
    
    public static function managePlayerNews()
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

        include FANVICTOR__PLUGIN_DIR_VIEW.'playernews/class.table-playernews.php';
        $myListTable = new TablePlayerNews();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null);
        include FANVICTOR__PLUGIN_DIR_VIEW.'playernews/index.php';
    }
    
    public static function addPlayerNews()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , FV_DOMAIN);
        }
        
        //load css js
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');
        wp_enqueue_script('playernews.js', FANVICTOR__PLUGIN_URL_JS.'admin/playernews.js');
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
        
        $bIsEdit = false;
        $id = !empty($_GET['id']) ? $_GET['id'] : 0;
        if($id > 0)
        {
            $bIsEdit = true;
        }
        
        //add or update
		self::modify($bIsEdit);
        
        //edit data
        $data = self::$playernews->getPlayerNewsForm($id);
        $aPlayers = $data['players'];
        $aForms = $data['player_news'];

        include FANVICTOR__PLUGIN_DIR_VIEW.'playernews/add.php';
    }

    private static function modify()
    {
        if (isset($_POST['val']) && $aVals = $_POST['val'])
		{
            $valid = self::$playernews->add($aVals);
            switch ($valid)
            {
                case 'i2':
                    redirect(self::$urladd, __('Please select player', FV_DOMAIN));
                    break;
                case 'i3':
                    redirect(self::$urladd, __('Provide date', FV_DOMAIN));
                    break;
                case 'i4':
                    redirect(self::$urladd, __('Provide title', FV_DOMAIN));
                    break;
                case 'i5':
                    redirect(self::$urladd, __('Provide content', FV_DOMAIN));
                    break;
                case 'u1':
                    redirect(self::$urladd, 'Succesfully updated');
                    break;
                case 'u0':
                    redirect(self::$urladd, 'There is something wrong! Please_try_again.');
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
				if (self::$playernews->delete($iId))
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