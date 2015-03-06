<?php
/**
 * Plugin Name: Fan Victor
 * Plugin URI: http://plugins.svn.wordpress.org/fantasy-sports/ 
 * Description: Create a fantasy sports website in minutes. Give your members the chance to compete in daily contests by predicting the outcomes of sporting events.  To get started: 1) Click the "Activate" link to the left of this description, 2) Sign up for a Fan Victor API key, and 3) Go to your FanVictor.com members page, and save your API key.
 * Version: 2.1.1
 * Author: Mega Website Services
 * Author URI: http://fanvictor.com
 * License: GPL2
 */

/*  Copyright 2014  Mega Website Services  (email : support@fanvictor.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

ob_start();

$upload_dir = wp_upload_dir();
define('FANVICTOR_VERSION', '2.1.1');
define('FANVICTOR__PLUGIN_URL', plugin_dir_url(__FILE__));
define('FANVICTOR__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FANVICTOR__PLUGIN_DIR_MODEL', FANVICTOR__PLUGIN_DIR.'model/');
define('FANVICTOR__PLUGIN_DIR_VIEW', FANVICTOR__PLUGIN_DIR.'views/');
define('FANVICTOR__PLUGIN_DIR_CONTROLLER', FANVICTOR__PLUGIN_DIR.'controller/');
define('FANVICTOR__PLUGIN_URL_IMAGE', FANVICTOR__PLUGIN_URL.'_inc/image/');
define('FANVICTOR__PLUGIN_URL_CSS', FANVICTOR__PLUGIN_URL.'_inc/css/');
define('FANVICTOR__PLUGIN_URL_JS', FANVICTOR__PLUGIN_URL.'_inc/jscript/');
define('FANVICTOR__PLUGIN_URL_AJAX', FANVICTOR__PLUGIN_URL.'fanvictor.php');
define('FANVICTOR_IMAGE_URL', $upload_dir['baseurl'].'/');
define('FANVICTOR_IMAGE_DIR', $upload_dir['basedir'].'/');
define('FANVICTOR_EMAIL_SUPPORT', 'support@fanvictor.com');

$permalink_structure = get_option('permalink_structure');
if($permalink_structure == '')
{
	$mypage = get_page_by_title('Create Contest');
	define('FANVICTOR_URL_CREATE_CONTEST', site_url().'/?page_id='.$mypage->ID);

	$mypage = get_page_by_title('Submit Picks');
	define('FANVICTOR_URL_SUBMIT_PICKS', site_url().'/?page_id='.$mypage->ID);

	$mypage = get_page_by_title('Add Funds');
	define('FANVICTOR_URL_ADD_FUNDS', site_url().'/?page_id='.$mypage->ID);

	$mypage = get_page_by_title('Notify Add Funds');
	define('FANVICTOR_URL_NOTIFY_ADD_FUNDS', site_url().'/?page_id='.$mypage->ID);

	$mypage = get_page_by_title('Success Add Funds');
	define('FANVICTOR_URL_SUCCESS_ADD_FUNDS', site_url().'/?page_id='.$mypage->ID);

	$mypage = get_page_by_title('Rankings');
	define('FANVICTOR_URL_RANKINGS', site_url().'/?page_id='.$mypage->ID);

	$mypage = get_page_by_title('Withdrawal History');
	define('FANVICTOR_URL_REQUEST_HISTORY', site_url().'/?page_id='.$mypage->ID);

	$mypage = get_page_by_title('Transactions');
	define('FANVICTOR_URL_TRANSACTIONS', site_url().'/?page_id='.$mypage->ID);

	$mypage = get_page_by_title('Success Withdrawls');
	define('FANVICTOR_URL_SUCCESS_WITHDRAWLS', site_url().'/?page_id='.$mypage->ID);

	$mypage = get_page_by_title('Notify Withdrawls');
	define('FANVICTOR_URL_NOTIFY_WITHDRAWLS', site_url().'/?page_id='.$mypage->ID);
    
    $mypage = get_page_by_title('Game');
	define('FANVICTOR_URL_GAME', site_url().'/?page_id='.$mypage->ID);
    
    $mypage = get_page_by_title('Entry');
	define('FANVICTOR_URL_ENTRY', site_url().'/?page_id='.$mypage->ID);
    
    $mypage = get_page_by_title('Contest');
	define('FANVICTOR_URL_CONTEST', site_url().'/?page_id='.$mypage->ID);
}
else 
{
	define('FANVICTOR_URL_CREATE_CONTEST', site_url().'/fantasy/create-contest/');
	define('FANVICTOR_URL_SUBMIT_PICKS', site_url().'/fantasy/submit-picks/');
    define('FANVICTOR_URL_GAME', site_url().'/fantasy/game/');
    define('FANVICTOR_URL_ENTRY', site_url().'/fantasy/entry/');
    define('FANVICTOR_URL_CONTEST', site_url().'/fantasy/contest/');
    define('FANVICTOR_URL_LOBBY', site_url().'/fantasy/lobby/');
	define('FANVICTOR_URL_ADD_FUNDS', site_url().'/fantasy/add-funds/');
	define('FANVICTOR_URL_NOTIFY_ADD_FUNDS', site_url().'/fantasy/notify-add-funds/');
	define('FANVICTOR_URL_SUCCESS_ADD_FUNDS', site_url().'/fantasy/success-add-funds/');
	define('FANVICTOR_URL_RANKINGS', site_url().'/fantasy/rankings/');
	define('FANVICTOR_URL_REQUEST_HISTORY', site_url().'/fantasy/withdrawal-history/');
	define('FANVICTOR_URL_TRANSACTIONS', site_url().'/fantasy/transactions/');
	define('FANVICTOR_URL_SUCCESS_WITHDRAWLS', site_url().'/fantasy/success-withdrawls/');
	define('FANVICTOR_URL_NOTIFY_WITHDRAWLS', site_url().'/fantasy/notify-withdrawls/');
}

function caption_shortcode( $atts, $content = null ) {
	return Lobby::show();
}
add_shortcode( 'fanvictor_lobby', 'caption_shortcode' );

//init
require_once(FANVICTOR__PLUGIN_DIR.'class.init.php');
register_activation_hook(__FILE__, array('FanvictorInit', 'active'));
register_deactivation_hook(__FILE__, array('FanvictorInit', 'deactivate'));
register_uninstall_hook(__FILE__, array('FanvictorInit', 'uninstall'));
add_action( 'plugins_loaded', array('FanvictorInit', 'upgrade'));
FanvictorInit::init_home_sidebar_area();

add_action('init', 'session_start');
?>