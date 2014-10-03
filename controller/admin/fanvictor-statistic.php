<?php
$Fanvictor_Statistic = new Fanvictor_Statistic();
class Fanvictor_Statistic
{
    private static $payment;
    public function __construct() 
    {
        self::$payment = new Payment();
    }
    
    public static function manageStatistic()
    {
        //load css js
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/init_statistic.js');
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');

        include FANVICTOR__PLUGIN_DIR.'class.table-statistic.php';
        $myListTable = new TableStatistic();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null); 
        include FANVICTOR__PLUGIN_DIR_VIEW.'statistic/index.php';
    }
}
?>