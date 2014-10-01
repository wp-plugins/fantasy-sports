<?php
$Fanvictor_Organizations = new Fanvictor_Organizations();
class Fanvictor_Organizations
{
    private static $orgs;
    private static $url;
    private static $urladd;
    public function __construct() 
    {
        self::$orgs = new Organizations();
        self::$url = admin_url().'admin.php?page=manage-teams';
        self::$urladd = wp_get_referer();
    }
    
    public static function manageOrganizations()
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

        include FANVICTOR__PLUGIN_DIR.'class.table-organizations.php';
        $myListTable = new TableOrganizations();
        $myListTable->prepare_items(); 
        include FANVICTOR__PLUGIN_DIR_VIEW.'organizations/index.php';
    }
}
