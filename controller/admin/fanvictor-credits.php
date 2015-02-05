<?php
$Fanvictor_Credits = new Fanvictor_Credits();
class Fanvictor_Credits
{
    private static $orgs;
    private static $teams;
    public function __construct() 
    {
        self::$orgs = new Organizations();
        self::$teams = new Teams();
    }
    
    public static function manageCredits()
    {
        //load css js
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
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

        include FANVICTOR__PLUGIN_DIR_VIEW.'credits/class.table-credits.php';
        $myListTable = new TableCredits();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null); 
        include FANVICTOR__PLUGIN_DIR_VIEW.'credits/index.php';
    }
}
?>