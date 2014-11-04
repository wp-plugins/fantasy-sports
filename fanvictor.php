<?php

/**

 * Plugin Name: Fan Victor

 * Plugin URI: http://plugins.svn.wordpress.org/fantasy-sports/ 

 * Description: Create a fantasy sports website in minutes. Give your members the chance to compete in daily contests by predicting the outcomes of sporting events.  To get started: 1) Click the "Activate" link to the left of this description, 2) Sign up for a Fan Victor API key, and 3) Go to your FanVictor.com members page, and save your API key.

 * Version: 1.0

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

define('FANVICTOR_VERSION', '3.0.2');

define('FANVICTOR__MINIMUM_WP_VERSION', '3.1');

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

}

else 

{

	define('FANVICTOR_URL_CREATE_CONTEST', site_url().'/fantasy/create-contest/');

	define('FANVICTOR_URL_SUBMIT_PICKS', site_url().'/fantasy/submit-picks/');

	define('FANVICTOR_URL_ADD_FUNDS', site_url().'/fantasy/add-funds/');

	define('FANVICTOR_URL_NOTIFY_ADD_FUNDS', site_url().'/fantasy/notify-add-funds/');

	define('FANVICTOR_URL_SUCCESS_ADD_FUNDS', site_url().'/fantasy/success-add-funds/');

	define('FANVICTOR_URL_RANKINGS', site_url().'/fantasy/rankings/');

	define('FANVICTOR_URL_REQUEST_HISTORY', site_url().'/fantasy/withdrawal-history/');

	define('FANVICTOR_URL_TRANSACTIONS', site_url().'/fantasy/transactions/');

	define('FANVICTOR_URL_SUCCESS_WITHDRAWLS', site_url().'/fantasy/success-withdrawls/');

	define('FANVICTOR_URL_NOTIFY_WITHDRAWLS', site_url().'/fantasy/notify-withdrawls/');

}



//create data, frontend pages plugin is actived

require_once(FANVICTOR__PLUGIN_DIR.'class.init.php');

register_activation_hook(__FILE__, 'jal_install');

register_uninstall_hook(__FILE__, 'jal_uninstall');

add_action('init', 'session_start');

$isUserLoggedIn = false;



if (is_admin()) 

{

    //model

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/pools.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/fighters.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/teams.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/organizations.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/user.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/statistic.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'payment.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'fanvictor.php');

	require_once(FANVICTOR__PLUGIN_DIR_MODEL.'mypaypal.php');

    

    //controller

    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-pools.php');

    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-fighters.php');

    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-teams.php');

    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-organizations.php');

    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-credits.php');

    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-withdrawls.php');

    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-statistic.php');

   

    //admin page

    require_once(FANVICTOR__PLUGIN_DIR.'class.fanvictor-admin.php');

	$fanvictor = new Fanvictor_Admin();

    $fanvictor->init();

    

    //ajax page

    require_once(FANVICTOR__PLUGIN_DIR.'class.ajax.php');

}

else

{    

    add_action('wp_enqueue_scripts','pluginname_ajaxurl'); 

    add_action('init','init_frontend'); 

    add_filter( 'wp_page_menu_args', 'fcs_page_menu_args');

}



function fcs_page_menu_args( $args ) 

{

    global $wpdb;

    $table_name = $wpdb->prefix."posts";

    $cond = "WHERE post_name LIKE '%submit-picks%' OR "

                . "post_name LIKE '%rankings%' OR "

                . "post_name LIKE '%notify-add-funds%' OR "

                . "post_name LIKE '%success-add-funds%' OR "

                . "post_name LIKE '%notify-withdrawls%' OR "

                . "post_name LIKE '%success-withdrawls%'";

    if(get_current_user_id() == 0)

    {

        $cond .= " OR post_name LIKE '%create-contest%' OR "

               . "post_name LIKE '%add-funds%' OR "

               . "post_name LIKE '%my-live-entries%' OR "

               . "post_name LIKE '%my-upcoming-entries%' OR "

               . "post_name LIKE '%my-history-entries%' OR "

               . "post_name LIKE '%my-funds%' OR "

               . "post_name LIKE '%game-summary%' OR "

               . "post_name LIKE '%transactions%' OR "

               . "post_name LIKE '%withdrawal-history%'";

    }

    $sql = "SELECT id "

         . "FROM $table_name "

         . $cond;

    $data = $wpdb->get_results($sql);

    $exclude = array();

    foreach($data as $item)

    {

        $exclude[] = $item->id;

    }

    if($exclude != null)

    {

        $exclude = implode(',', $exclude);

    }

    

	$args = array(

	'exclude'      => $exclude,

	'echo'         => 1,

    'menu_class' => 'nav-menu nav');

	return $args;

}



function init_frontend()

{

    //model

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'fanvictor.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/pools.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'payment.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/organizations.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/user.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'paypal.php');

    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'mypaypal.php');

         

	$page_title = null;

	if(isset($_GET['page_id']))

	{

		$page = get_post($_GET['page_id']);

		$page_title = $page->post_name;

	}

    

    if(($page_title != null && ($page_title == "notify-add-funds" || $page_title == "notify-withdrawls")) || 

	   (pageSegment(1) == "fantasy" && (pageSegment(2) == "notify-add-funds" || pageSegment(2) == "notify-withdrawls")))

    {

        $pagename = $page_title != null ? $page_title : pageSegment(2);

        call_page($pagename);

    }

    else if(get_current_user_id() > 0)

    {

        if($page_title != null || pageSegment(1) == "fantasy")

        {

            if($page_title == "fantasy" || (pageSegment(1) == "fantasy" && pageSegment(2) == ''))

            {

                redirect(FANVICTOR_URL_CREATE_CONTEST, null, true);

            }

            $pagename = $page_title != null ? $page_title : pageSegment(2);

            call_page($pagename);

        }

        else if(pageSegment(1) == "")

        {

            add_filter('the_content', 'addlobby');

        }

    }

    else 

    {

		if(pageSegment(2) == 'future-events')

		{

			$pagename = $page_title != null ? $page_title : pageSegment(2);

            call_page($pagename);

		}

		else 

		{

			if (($page_title != null || pageSegment(1) == "fantasy") && 

			   !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')))

			{

				redirect(wp_login_url());

			}

		}

	}

	if(get_current_user_id() == 0 && pageSegment(1) == "")

    {

        add_filter('the_content', 'addlobby');

    }

}



function addlobby($content)

{

	return home_sidebar().$content;

}



function pluginname_ajaxurl() 

{

    ?>

    <script type="text/javascript">

    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

    </script>

    <?php

}



function pageSegment($pos = 0)

{

    $siteUrl = explode('/', get_site_url().'/');

	$siteUrl = array_filter($siteUrl);

	$siteUrl = array_values($siteUrl);

	$offset = count($siteUrl) - 2;

    $url =  $_SERVER['REQUEST_URI'];

    $url = explode('/', $url);

    if(isset($url[$pos + $offset]))

    {

        return $url[$pos + $offset];

    }

    return null;

}



function redirect($url, $msg = null, $blank = false)

{

    if($msg != null && function_exists('add_settings_error'))

    {

        add_settings_error('general', 'settings-updated', __($msg), 'updated');

        set_transient('settings_errors', get_settings_errors(), 30);

    }

    else if($msg != null)

    {

        $_SESSION['msg'] = $msg;

    }

    if(!$blank)

    {

        $url = add_query_arg( 'settings-updated', 'true', $url);

    }

    wp_redirect($url);

    exit;

}



function getMessage()

{

    if(isset($_SESSION['msg']))

    {

        $msg = $_SESSION['msg'];

        unset($_SESSION['msg']);

        echo '<div class="public_message" style="display: block;">'.$msg.'</div>';

    }

}



function call_page($name)

{

    $name = trim($name);

    $name = str_replace('-', '', $name);

    if(file_exists(FANVICTOR__PLUGIN_DIR_CONTROLLER."$name.php"))

    {

        require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER."$name.php");

        $$name = new $name();

        add_action( 'wp_loaded', array($name, 'process'));

    }

}



function call_block($name)

{

    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER."block/$name.php");

    $block = new $name();

    $result = $block->process();

    if($result != null)

    {

        foreach($result as $k => $v)

        {

            $$k = $v;

        }

    }

    require_once(FANVICTOR__PLUGIN_DIR_VIEW."block/$name.php");

}

?>