<?php
$Fanvictor_Transactions = new Fanvictor_Transactions();
class Fanvictor_Transactions
{
    public static function manageTransactions()
    {
        //load css js
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', FANVICTOR__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('statistic.js', FANVICTOR__PLUGIN_URL_JS.'admin/statistic.js');
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');
       
        include FANVICTOR__PLUGIN_DIR_VIEW.'transactions/class.table-transactions.php';
        $myListTable = new TableTransactions();
        $myListTable->prepare_items(isset($_GET['s']) ? $_GET['s'] : null); 
        include FANVICTOR__PLUGIN_DIR_VIEW.'transactions/index.php';
    }
}
?>