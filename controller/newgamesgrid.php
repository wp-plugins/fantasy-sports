<?php
class Newgamesgrid extends WP_Widget
{    
    public function __construct() 
    {
		parent::__construct(
			'lobby_widget', // Base ID
			__('Lobby', 'text_domain'), // Name
			array( 'description' => __( 'Lobby Widget', 'text_domain' ), ) // Args
		);
	}
    
    public function widget( $args, $instance ) 
    {
        wp_enqueue_script('fanvictor.js', FANVICTOR__PLUGIN_URL_JS.'fanvictor.js');
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');
        wp_enqueue_script('lobby.js', FANVICTOR__PLUGIN_URL_JS.'lobby.js');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
        $sHeader = "Lobby";
        require_once(FANVICTOR__PLUGIN_DIR_VIEW."newgamesgrid.php");
	}
}
?>