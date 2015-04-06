<?php
$Fanvictor_ScoringCategory = new Fanvictor_ScoringCategory();
class Fanvictor_ScoringCategory
{
    private static $fanvictor;
    private static $scoringcategory;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$fanvictor = new Fanvictor();
        self::$scoringcategory = new ScoringCategory();
        self::$url = admin_url().'admin.php?page=manage-scoringcategory';
        self::$urladdnew = admin_url().'admin.php?page=add-scoringcategory';
        self::$urladd = wp_get_referer();
    }
    
    public static function manageScoringCategory()
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

        include FANVICTOR__PLUGIN_DIR_VIEW.'scoringcategory/class.table-scoringcategory.php';
        $myListTable = new TableScoringCategory();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null);
        include FANVICTOR__PLUGIN_DIR_VIEW.'scoringcategory/index.php';
    }
    
    public static function addScoringCategory()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , FV_DOMAIN);
        }
        
        //load css js
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('scoringcat.js', FANVICTOR__PLUGIN_URL_JS.'admin/scoringcat.js');
        
        //edit data
        $bIsEdit = false;
		if (isset($_GET['id']) && $iEditId = $_GET['id'])
		{
            $bIsEdit = true;
            $aForms = self::$scoringcategory->getScoringCategory($iEditId);
		}
        else
        {
            $aForms = null;
            $aFights = array(null);
        }

        //add or update
		self::modify($bIsEdit);

        $aSports = self::$fanvictor->getListSports();
        $aScoringTypes = self::$scoringcategory->getScoringType();
        include FANVICTOR__PLUGIN_DIR_VIEW.'scoringcategory/add.php';
    }

    private static function validData($aVals)
    {
        $aScoringCat = self::$scoringcategory->getScoringCategory($aVals['id']);
        if($aScoringCat[0]['siteID'] > 0 || $aVals['id'] == '')
        {
            if(empty($aVals['name']))
            {
                redirect(self::$urladd, 'Provide a name');
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
                if(self::$scoringcategory->isScoringCategoryExist($aVals['id'])) //update
                {
                    if (self::$scoringcategory->update($aVals))
                    {
                        redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if (self::$scoringcategory->add($aVals))
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
				if (self::$scoringcategory->delete($iId))
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