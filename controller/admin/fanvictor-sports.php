<?php
$Fanvictor_Sports = new Fanvictor_Sports();
class Fanvictor_Sports
{
    private static $sports;
    private static $url;
    private static $urladd;
    private static $urladdnew;
    public function __construct() 
    {
        self::$sports = new Sports();
        self::$url = admin_url().'admin.php?page=manage-sports';
        self::$urladd = wp_get_referer();
        self::$urladdnew = admin_url().'admin.php?page=add-sports';
    }
    
    public static function manageSports()
    {
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

        include FANVICTOR__PLUGIN_DIR_VIEW.'sports/class.table-sports.php';
        $myListTable = new TableSports();
        $myListTable->prepare_items(); 
        include FANVICTOR__PLUGIN_DIR_VIEW.'sports/index.php';
    }
    
    public static function addSports()
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
            $aForms = self::$sports->getSportById($iEditId);
            $aForms = self::$sports->parseSportsData($aForms);
            $aForms = $aForms[0];
		}
        else
        {
            $aForms = null;
        }

        //add or update
		self::modify($bIsEdit);

        $aSports = self::$sports->getSports();
        include FANVICTOR__PLUGIN_DIR_VIEW.'sports/add.php';
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
                if(self::$sports->isSportExist($aVals['id'])) //update
                {
                    if (self::$sports->update($aVals))
                    {
                        redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if (self::$sports->add($aVals))
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
				if (self::$sports->delete($iId))
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