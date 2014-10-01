<?php
$Fanvictor_Pools = new Fanvictor_Pools();
class Fanvictor_Pools
{
    private static $pools;
    private static $orgs;
    private static $fighters;
    private static $url;
    private static $urladd;
    public function __construct() 
    {
        self::$pools = new Pools();
        self::$orgs = new Organizations();
        self::$fighters = new Fighters();
        self::$url = admin_url().'admin.php?page=manage-pools';
        self::$urladd = wp_get_referer();
    }
    
    public static function managePools()
    {
        //self::$pools->updateUserMoneyWon(1135);exit('aaa');
        //load css js
        wp_enqueue_style('admin.css', FANVICTOR__PLUGIN_URL_CSS.'admin.css');
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('fight.js', FANVICTOR__PLUGIN_URL_JS.'admin/fight.js');
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
        
        include FANVICTOR__PLUGIN_DIR.'class.table-pools.php';
        $myListTable = new TablePools();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null); 
        include FANVICTOR__PLUGIN_DIR_VIEW.'pools/index.php';
    }
    
    public static function addPools()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }
        
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
            $aForms = self::$pools->getPools($iEditId);
            $aFights = self::$pools->getFights($iEditId);
            $aFights = $aFights == null ? array(array()) : $aFights;
		}
        else
        {
            $aForms = null;
            $aFights = array(null);
        }

        //create valid
		//$oValidator = $this->createValid();
		
        //add or update
		self::modify($bIsEdit);
        
        $aSports = self::$orgs->getSport(null, true);
        $aPoolHours = self::$pools->getPoolHours();
        $aPoolMinutes = self::$pools->getPoolMinutes();
        $aRounds = self::$fighters->getRounds();
        
        include FANVICTOR__PLUGIN_DIR_VIEW.'pools/add.php';
    }

    private static function validData($aVals)
    {
        if(empty($aVals['poolName']))
        {
            redirect(self::$urladd, 'Provide a name');
        }
        else if(!self::$orgs->isOrgsExist($aVals['organization']))
        {
            redirect(self::$urladd, 'Please select organization');
        }
        else if(!self::$orgs->isSportExist($aVals['type']))
        {
            redirect(self::$urladd, 'Please select sport');
        }
        else if(empty($aVals['startDate']))
        {
            redirect(self::$urladd, 'Provide start date');
        }
        else if(empty($aVals['cutDate']))
        {
            redirect(self::$urladd, 'Provide cut date');
        }
        
        //valid fight
        foreach($aVals['fighterID1'] as $item)
        {
            if(empty($item))
            {
                redirect(self::$urladd, 'Please select fighter 1');
            }
        }
        foreach($aVals['fighterID2'] as $item)
        {
            if(empty($item))
            {
                redirect(self::$urladd, 'Please select fighter 2');
            }
        }
        foreach($aVals['fight_name'] as $item)
        {
            if(empty($item))
            {
                redirect(self::$urladd, 'Provide fixture name');
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
                if(self::$pools->isPoolExist($aVals['poolID'])) //update
                {
                    if (self::$pools->update($aVals))
                    {
                        redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if (self::$pools->add($aVals))
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
				if (self::$pools->delete($iId))
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
