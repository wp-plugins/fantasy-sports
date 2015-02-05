<?php
$Fanvictor_Fighters = new Fanvictor_Fighters();
class Fanvictor_Fighters
{
    private static $fighters;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$fighters = new Fighters();
        self::$url = admin_url().'admin.php?page=manage-fighters';
        self::$urladdnew = admin_url().'admin.php?page=add-fighters';
        self::$urladd = wp_get_referer();
    }
    
    public static function manageFighters()
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
        
        include FANVICTOR__PLUGIN_DIR_VIEW.'fighters/class.table-fighters.php';
        $myListTable = new TableFighters();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null); 
        include FANVICTOR__PLUGIN_DIR_VIEW.'fighters/index.php';
    }
    
    public static function addFighters()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }
        
        //load css js
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        
        //load css js
        wp_enqueue_style('admin.css', FANVICTOR__PLUGIN_URL_CSS.'admin.css');
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('fight.js', FANVICTOR__PLUGIN_URL_JS.'admin/fight.js');
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');
        wp_enqueue_script('init_add.js', FANVICTOR__PLUGIN_URL_JS.'admin/init_add.js');
        
        //edit data
        $bIsEdit = false;
		if (isset($_GET['id']) && $iEditId = $_GET['id'])
		{
            $bIsEdit = true;
            $aForms = self::$fighters->getfighters($iEditId);
		}
        else
        {
            $aForms = null;
            $aFights = array(null);
        }

        //add or update
		self::modify($bIsEdit);

        include FANVICTOR__PLUGIN_DIR_VIEW.'fighters/add.php';
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
                if(self::$fighters->isFighterExist($aVals['fighterID'])) //update
                {
                    if (self::$fighters->update($aVals))
                    {
                        redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if (self::$fighters->add($aVals))
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
				if (self::$fighters->delete($iId))
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