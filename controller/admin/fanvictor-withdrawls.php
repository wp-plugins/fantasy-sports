<?php
$Fanvictor_Withdrawls = new Fanvictor_Withdrawls();
class Fanvictor_Withdrawls
{
    private static $payment;
    public function __construct() 
    {
        self::$payment = new Payment();
    }
    
    public static function manageWithdrawls()
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
        
        $aGateways = self::$payment->viewGateway();

        include FANVICTOR__PLUGIN_DIR_VIEW.'withdrawls/class.table-withdrawls.php';
        $myListTable = new TableWithdrawls();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null); 
        include FANVICTOR__PLUGIN_DIR_VIEW.'withdrawls/index.php';
    }
}
?>