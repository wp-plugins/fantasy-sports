<?php
$Fanvictor_PlayerPosition = new Fanvictor_PlayerPosition();
class Fanvictor_PlayerPosition
{
    private static $fanvictor;
    private static $orgs;
    private static $playerposition;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
        self::$orgs = new Organizations();
        self::$playerposition = new PlayerPosition();
        self::$url = admin_url().'admin.php?page=manage-playerposition';
        self::$urladdnew = admin_url().'admin.php?page=add-playerposition';
        self::$urladd = wp_get_referer();
    }
    
    public static function managePlayerPosition()
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

        include FANVICTOR__PLUGIN_DIR_VIEW.'playerposition/class.table-playerposition.php';
        $myListTable = new TablePlayerPosition();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null);
        include FANVICTOR__PLUGIN_DIR_VIEW.'playerposition/index.php';
    }
    
    public static function addPlayerPosition()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , FV_DOMAIN);
        }
        
        //load css js
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        
        //edit data
        $bIsEdit = false;
		if (isset($_GET['id']) && $iEditId = $_GET['id'])
		{
            $bIsEdit = true;
            $aForms = self::$playerposition->getplayerposition($iEditId);
		}
        else
        {
            $aForms = null;
            $aFights = array(null);
        }

        //add or update
		self::modify($bIsEdit);

        $aSports = self::$fanvictor->getListSports();
        include FANVICTOR__PLUGIN_DIR_VIEW.'playerposition/add.php';
    }

    private static function validData($aVals)
    {
        if(empty($aVals['name']))
        {
            redirect(self::$urladd, 'Provide a name');
        }
        return true;
    }
    
    private static function modify()
    {
        if (isset($_POST['val']) && $aVals = $_POST['val'])
		{
			if (self::validData($aVals))
			{
                if(self::$playerposition->isPlayerPositionExist($aVals['id'])) //update
                {
                    if (self::$playerposition->update($aVals))
                    {
                        redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if (self::$playerposition->add($aVals))
                    {
                        redirect(self::$urladd, 'Succesfully added');
                    }
                }
                redirect(self::$urladd, 'There is something wrong! Please_try_again.');
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
				if (self::$playerposition->delete($iId))
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