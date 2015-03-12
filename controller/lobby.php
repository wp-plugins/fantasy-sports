<?php
require_once(FANVICTOR__PLUGIN_DIR_MODEL.'model.php');
require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/fighters.php');
require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/teams.php');
require_once(FANVICTOR__PLUGIN_DIR_MODEL.'payment.php');
require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/pools.php');
require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/sports.php');
require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/scoringcategory.php');
require_once(FANVICTOR__PLUGIN_DIR_MODEL.'fanvictor.php');
class Lobby
{
    public static function show()
    {
        self::content();
    }
    
    private static function content()
    {
        wp_enqueue_script('playerdraft.js', FANVICTOR__PLUGIN_URL_JS.'playerdraft.js');
        wp_enqueue_script('lobby_page.js', FANVICTOR__PLUGIN_URL_JS.'lobby_page.js');
        wp_enqueue_script('countdown.min.js', FANVICTOR__PLUGIN_URL_JS.'countdown.min.js');
        wp_enqueue_script('tablesorter.js', FANVICTOR__PLUGIN_URL_JS.'tablesorter.js');
        wp_enqueue_script('accounting.js', FANVICTOR__PLUGIN_URL_JS.'accounting.js');
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');
        wp_enqueue_style('playerdraft.css', FANVICTOR__PLUGIN_URL_CSS.'playerdraft.css');
        wp_enqueue_style('lobby.css', FANVICTOR__PLUGIN_URL_CSS.'lobby.css');
        wp_enqueue_style('font-awesome.css', FANVICTOR__PLUGIN_URL_CSS.'font-awesome/css/font-awesome.css');
        
        $fanvictor = new Fanvictor();
        $aSports = $fanvictor->getListSports();

        include FANVICTOR__PLUGIN_DIR_VIEW.'lobby.php';
    }
}
?>