<?php
$Fanvictor_Teams = new Fanvictor_Teams();
class Fanvictor_Teams
{
    private static $fanvictor;
    private static $teams;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
        self::$teams = new Teams();
        self::$url = admin_url().'admin.php?page=manage-teams';
        self::$urladdnew = admin_url().'admin.php?page=add-teams';
        self::$urladd = wp_get_referer();
    }
    
    public static function manageTeams()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') );
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

        include FANVICTOR__PLUGIN_DIR_VIEW.'teams/class.table-teams.php';
        $myListTable = new TableTeams();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null);
        include FANVICTOR__PLUGIN_DIR_VIEW.'teams/index.php';
    }
    
    public static function addTeams()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }
        
        //load css js
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        
        //edit data
        $bIsEdit = false;
		if (isset($_GET['id']) && $iEditId = $_GET['id'])
		{
            $bIsEdit = true;
            $aForms = self::$teams->getteams($iEditId);
		}
        else
        {
            $aForms = null;
            $aFights = array(null);
        }

        //add or update
		self::modify($bIsEdit);

        $aSports = self::$fanvictor->getListSports();
        include FANVICTOR__PLUGIN_DIR_VIEW.'teams/add.php';
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
                if(self::$teams->isTeamExist($aVals['teamID'])) //update
                {
                    if (self::$teams->update($aVals))
                    {
                        redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if (self::$teams->add($aVals))
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
				if (self::$teams->delete($iId))
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