<?php
global $jal_db_version;
$jal_db_version = '1.0';

function jal_install()
{
    if(!get_option('fanvictor_done_installed',false))
    {
        //table fundhistory
        $sql = "`fundshistoryID` int(8) unsigned NOT NULL AUTO_INCREMENT,
                `userID` int(8) unsigned NOT NULL,
                `transactionID` varchar(255) DEFAULT NULL,
                `amount` decimal(10,2) NOT NULL,
                `operation` enum('ADD','DEDUCT') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                `type` enum('WIN','WITHDRAW','FUNDING','MAKE_BET','REFUND','CREDITS','DEPOSIT') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                `gateway` enum('PAYPAL') DEFAULT NULL,
                `reason` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
                `date` datetime NOT NULL,
                `leagueID` int(8) unsigned DEFAULT NULL,
                `new_balance` decimal(10,2) DEFAULT NULL,
                `cash_to_credit` int(11) DEFAULT NULL,
                `is_checkout` tinyint(4) DEFAULT '0',
                PRIMARY KEY (`fundshistoryID`),
                KEY `userID` (`userID`)";
        createTable($sql, "fundhistory");

        //table user_payment
        $sql = "`user_id` int(11) NOT NULL,
                `gateway` enum('PAYPAL') NOT NULL,
                `email` varchar(255) NOT NULL,
                `time_stamp` varchar(10) DEFAULT NULL,
                `time_update` varchar(10) DEFAULT NULL";
        createTable($sql, "user_payment");

        //table withdrawls
        $sql = "`withdrawlID` int(8) unsigned NOT NULL AUTO_INCREMENT,
                `userID` int(8) unsigned NOT NULL DEFAULT '0',
                `reason` text,
                `response_message` text,
                `requestDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                `processedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
                `real_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
                `credit_to_cash` int(11) NOT NULL DEFAULT '1',
                `new_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
                `status` enum('NEW','APPROVED','DECLINED') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NEW',
                `transactionID` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`withdrawlID`)";
        createTable($sql, "withdrawls");

        //table user_extended
        $sql = "`user_id` int(11) unsigned NOT NULL,
                `balance` decimal(10,2) NOT NULL DEFAULT '0.00'";
        createTable($sql, "user_extended");

        //insert options
        add_option('fanvictor_api_token', '');
        add_option('fanvictor_api_url', 'http://fanvictor.com/api');
        add_option('fanvictor_api_url_admin', 'http://fanvictor.com/api/admin');
        add_option('fanvictor_image_dir', 'uploads/fanvictor/');
        add_option('fanvictor_image_thumb_size', '80');
        add_option('fanvictor_entry_fee', array(2, 5, 15, 25, 50, 100));
        add_option('fanvictor_league_size', array(3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,25,50,100));
        add_option('fanvictor_winner_percent', '90');
        add_option('fanvictor_first_place_percent', '50');
        add_option('fanvictor_second_place_percent', '30');
        add_option('fanvictor_third_place_percent', '20');
        add_option('fanvictor_cash_to_credit', '1');
        add_option('fanvictor_credit_to_cash', '1');
        add_option('paypal_test', '0');
        add_option('paypal_email_account', '');

        //create frontend pages if not exist
        create_page();

        install_widget();
        
        update_option('fanvictor_done_installed',true);
    }
}

function jal_uninstall()
{
    dropTable("fundhistory");
    dropTable("user_payment");
    dropTable("withdrawls");
    dropTable("user_extended");
    
    //remove options
    delete_option('fanvictor_api_token');
    delete_option('fanvictor_api_url');
    delete_option('fanvictor_api_url_admin');
    delete_option('fanvictor_image_dir');
    delete_option('fanvictor_image_thumb_size');
    delete_option('fanvictor_entry_fee');
    delete_option('fanvictor_league_size');
    delete_option('fanvictor_winner_percent');
    delete_option('fanvictor_first_place_percent');
    delete_option('fanvictor_second_place_percent');
    delete_option('fanvictor_third_place_percent');
    delete_option('fanvictor_cash_to_credit');
    delete_option('fanvictor_credit_to_cash');
    delete_option('paypal_test');
    delete_option('paypal_email_account');
    
    //delete frontend pages if exist
	delete_menu("Fantasy");
	delete_menu("Create Contest");
    delete_menu("Add Funds");
    delete_menu("My Live Entries");
    delete_menu("My Upcoming Entries");
    delete_menu("My History Entries");
    delete_menu("My Funds");
    delete_menu("Future Events");
    delete_menu("Game Summary");
    delete_menu("Transactions");
    delete_menu("Withdrawal History");
    delete_menu("Submit Picks");
    delete_menu("Rankings");
    delete_menu("Notify Add Funds");
    delete_menu("Success Add Funds");
    delete_menu("Notify Withdrawls");
    delete_menu("Success Withdrawls");
	
    delete_page("Fantasy");
	delete_page("Create Contest");
    delete_page("Add Funds");
    delete_page("My Live Entries");
    delete_page("My Upcoming Entries");
    delete_page("My History Entries");
    delete_page("My Funds");
    delete_page("Future Events");
    delete_page("Game Summary");
    delete_page("Transactions");
    delete_page("Withdrawal History");
    delete_page("Submit Picks");
    delete_page("Rankings");
    delete_page("Notify Add Funds");
    delete_page("Success Add Funds");
    delete_page("Notify Withdrawls");
    delete_page("Success Withdrawls");

    uninstall_widget();
    delete_option('fanvictor_done_installed');
}

////////////////////////////widget////////////////////////////
function install_widget()
{
    $add_to_sidebar = 'fanvictor_home_sidebar';
    $widget_name = 'lobby_widget';
    $sidebar_options = get_option('sidebars_widgets');
    if(!isset($sidebar_options[$add_to_sidebar]))
    {
        $sidebar_options[$add_to_sidebar] = array('_multiwidget'=>1);
    }

    if(!is_array($homepagewidget))$homepagewidget = array();
    $count = count($homepagewidget)+1;
    // add first widget to sidebar:
    $sidebar_options[$add_to_sidebar][] = $widget_name.'-'.$count;
    $homepagewidget[$count] = array();
    $homepagewidget['_multiwidget'] = 1;
    $count++;

    update_option('sidebars_widgets',$sidebar_options);
    update_option('widget_'.$widget_name,$homepagewidget);
}

function uninstall_widget()
{
    delete_option('widget_lobby_widget');
    $add_to_sidebar = 'fanvictor_home_sidebar';
    $widget_name = 'lobby_widget';
    $sidebar_options = get_option('sidebars_widgets');
    if(isset($sidebar_options[$add_to_sidebar]))
    {
        unset($sidebar_options[$add_to_sidebar]);
        update_option('sidebars_widgets',$sidebar_options);
    }
}

function home_sidebar()
{
    echo '<div id="fanvictor_home_sidebar">';
    dynamic_sidebar('fanvictor_home_sidebar');;
    echo "</div>";
}

function init_home_sidebar_area()
{
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER."newgamesgrid.php");
    add_action( 'widgets_init', function(){
        register_sidebar( array(
            'name' => __('Fan Victor Home Sidebar'),
            'id' => 'fanvictor_home_sidebar',
        ));
        register_widget( 'Newgamesgrid' );
    });
}

////////////////////////////data tables////////////////////////////
function createTable($sql, $table_name)
{
    global $wpdb;
    global $jal_db_version;
    $table_name = $wpdb->prefix.$table_name;
    /*
     * We'll set the default character set and collation for this table.
     * If we don't do this, some characters could end up being converted 
     * to just ?'s when saved in our table.
     */
    $charset_collate = '';

    if (!empty( $wpdb->charset)) 
    {
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
    }

    if (!empty( $wpdb->collate)) 
    {
        $charset_collate .= "COLLATE {$wpdb->collate}";
    }
    
    $sql = "CREATE TABLE $table_name ($sql) $charset_collate;";
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    add_option( 'jal_db_version', $jal_db_version );
}

function dropTable($table_name)
{
    global $wpdb;
    $table_name = $wpdb->prefix.$table_name; 
    $sql = "DROP TABLE $table_name";

    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    $wpdb->query($sql);
}

////////////////////////////frontend pages////////////////////////////
function create_page()
{
    $parent_id = insert_page("Fantasy", 'publish', 20);
    $p1 = insert_page("Create Contest", 'publish', 1, $parent_id);
    $p2 = insert_page("Add Funds", 'publish', 2, $parent_id);
    $p3 = insert_page("My Live Entries", 'publish', 3, $parent_id);
    $p4 = insert_page("My Upcoming Entries", 'publish', 4, $parent_id);
    $p5 = insert_page("My History Entries", 'publish', 5, $parent_id);
    $p6 = insert_page("My Funds", 'publish', 6, $parent_id);
	$p7 = insert_page("Future Events", 'publish', 7, $parent_id);
    $p8 = insert_page("Game Summary", 'publish', 8, $parent_id);
    $p9 = insert_page("Transactions", 'publish', 9, $parent_id);
    $p10 =  insert_page("Withdrawal History", 'publish', 10, $parent_id);
    insert_page("Submit Picks", 'pending', 7, $parent_id);
    insert_page("Rankings", 'pending', 8, $parent_id);
    insert_page("Notify Add Funds", 'pending', 9, $parent_id);
    insert_page("Success Add Funds", 'pending', 10, $parent_id);
    insert_page("Notify Withdrawls", 'pending', 11, $parent_id);
    insert_page("Success Withdrawls", 'pending', 12, $parent_id);
    
    $locations = get_nav_menu_locations();
    $menu_id = $locations['primary'];
    if((int)$menu_id < 1)
    {
        $menu_id = $locations['primary-menu'];
    }
    
    $item_parent = insert_primary_menu(20);
    add_term_relationship($item_parent, $menu_id);
	add_menu_post_meta($item_parent, $parent_id, 0);
	
    $post_id = insert_primary_menu(1, $parent_id);
    add_term_relationship($post_id, $menu_id);
	add_menu_post_meta($post_id, $p1, $item_parent);
	
    $post_id = insert_primary_menu(2, $parent_id);
    add_term_relationship($post_id, $menu_id);
	add_menu_post_meta($post_id, $p2, $item_parent);
	
    $post_id = insert_primary_menu(3, $parent_id);
    add_term_relationship($post_id, $menu_id);
	add_menu_post_meta($post_id, $p3, $item_parent);
	
    $post_id = insert_primary_menu(4, $parent_id);
    add_term_relationship($post_id, $menu_id);
	add_menu_post_meta($post_id, $p4, $item_parent);
	
    $post_id = insert_primary_menu(5, $parent_id);
    add_term_relationship($post_id, $menu_id);
	add_menu_post_meta($post_id, $p5, $item_parent);
	
    $post_id = insert_primary_menu(6, $parent_id);
    add_term_relationship($post_id, $menu_id);
	add_menu_post_meta($post_id, $p6, $item_parent);
    
    $post_id = insert_primary_menu(7, $parent_id);
    add_term_relationship($post_id, $menu_id);
	add_menu_post_meta($post_id, $p7, $item_parent);
    
    $post_id = insert_primary_menu(8, $parent_id);
    add_term_relationship($post_id, $menu_id);
	add_menu_post_meta($post_id, $p8, $item_parent);
    
    $post_id = insert_primary_menu(9, $parent_id);
    add_term_relationship($post_id, $menu_id);
	add_menu_post_meta($post_id, $p9, $item_parent);
    
    $post_id = insert_primary_menu(10, $parent_id);
    add_term_relationship($post_id, $menu_id);
	add_menu_post_meta($post_id, $p10, $item_parent);
}

function insert_page($name, $status, $menu_order = 0, $parent = null)
{
    if(!get_page_by_title($name))
    {
        $my_post = array(
            'post_title'    => $name,
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_parent'   => $parent,
            'post_author'   => get_current_user_id(),
            'menu_order'    => $menu_order,
        );
        $id = wp_insert_post( $my_post );
        if($status == 'pending')
        {
            $my_post = array(
                'ID'            => $id,
                'post_status'   => 'pending',
            );
            wp_update_post($my_post);
        }
        return $id;
    }
    return null;
}

function insert_primary_menu($menu_order = 0, $parent = null)
{
    $my_post = array(
        'post_title'    => '',
        'post_status'   => 'publish',
        'post_type'     => 'nav_menu_item',
        'post_parent'   => $parent,
        'post_author'   => get_current_user_id(),
        'menu_order'    => $menu_order,
    );
    $id = wp_insert_post( $my_post );
    return $id;
}

function add_term_relationship($post_id, $term_id)
{
    global $wpdb;
    $table = $wpdb->prefix.'term_relationships';
    $sql = "INSERT INTO $table(object_id, term_taxonomy_id) VALUES($post_id, $term_id)";
    $wpdb->query($sql);
}

function add_menu_post_meta($post_id, $object_id, $item_parent)
{
	add_post_meta($post_id, '_menu_item_type', 'post_type');
	add_post_meta($post_id, '_menu_item_menu_item_parent', $item_parent);
	add_post_meta($post_id, '_menu_item_object_id', $object_id);
	add_post_meta($post_id, '_menu_item_object', 'page');
	add_post_meta($post_id, '_menu_item_target', '');
	add_post_meta($post_id, '_menu_item_classes', 'a:1:{i:0;s:0:"";}');
	add_post_meta($post_id, '_menu_item_xfn', '');
	add_post_meta($post_id, '_menu_item_url', '');
}

function delete_page($name, $status, $menu_order = 0, $parent = null)
{
    if(get_page_by_title($name))
    {
        global $wpdb;
        $wpdb->delete($wpdb->prefix."posts", array('post_title' => $name));
    }
}

function delete_menu($name)
{
	global $wpdb;
	$id = get_page_by_title($name)->ID;
	$table = $wpdb->prefix.'postmeta';
	$sql = "SELECT post_id FROM $table WHERE meta_key = '_menu_item_object_id' AND meta_value = $id";
	$post_id = $wpdb->get_var($sql);
	
	$wpdb->delete($wpdb->prefix."postmeta", array('post_id' => $post_id));
	$wpdb->delete($wpdb->prefix."term_relationships", array('object_id' => $post_id));
	$wpdb->delete($wpdb->prefix."posts", array('ID' => $post_id));
}

init_home_sidebar_area();