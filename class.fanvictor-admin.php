<?php
class Fanvictor_Admin
{
    public function __construct() 
    {
        add_action( 'admin_head', array( &$this, 'admin_header' ) );
    }
    public static function init()
    {
        add_action('admin_menu', array('Fanvictor_Admin', 'loadMenuSetting'));
        add_action('admin_menu', array('Fanvictor_Admin', 'loadMenuBar'));
    }
    
    static function admin_header() {
        echo '<style type="text/css">';
        echo '.wp-list-table .column-ID, .wp-list-table .column-uID  { width: 60px; }';
        echo '.wp-list-table .column-payment_request_pending,'
           . '.wp-list-table .column-action { width: 200px; }';
        echo '.wp-list-table .column-balance , '
           . '.wp-list-table .column-startDate { width: 150px; }';
        echo '.wp-list-table .column-status{ width: 175px; }';
        echo '.wp-list-table .column-result, '
           . '.wp-list-table .column-image, '
           . '.wp-list-table .column-active, '
           . '.wp-list-table .column-edit, '
           . '.wp-list-table .column-detail { width: 50px; }';
        echo '.wp-list-table .column-new_balance, '
           . '.wp-list-table .column-amount, '
           . '.wp-list-table .column-playerdraft_result, '
           . '.wp-list-table .column-real_amount { width: 90px; } ';
        echo '</style>';
    }

    //setting
    public static function loadMenuSetting()
    {
        add_options_page( 'Fan Victor Settings', 'Fan Victor', 'manage_options', 'fanvictor', array( 'Fanvictor_Admin', 'options'));
        add_action('admin_init', array( 'Fanvictor_Admin', 'registerSettings'));
        wp_enqueue_script('option.js', FANVICTOR__PLUGIN_URL_JS.'admin/option.js');
    }
    
    public static function options() 
    {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) , FV_DOMAIN);
        }
        include FANVICTOR__PLUGIN_DIR_VIEW.'settings.php';
    }
    
    public static function registerSettings()
    { 
        register_setting('fanvictor-settings-group', 'fanvictor_api_token');
        register_setting('fanvictor-settings-group', 'fanvictor_api_url');
        register_setting('fanvictor-settings-group', 'fanvictor_api_url_admin');
        register_setting('fanvictor-settings-group', 'fanvictor_image_dir');
        register_setting('fanvictor-settings-group', 'fanvictor_image_thumb_size');
        register_setting('fanvictor-settings-group', 'fanvictor_entry_fee');
        register_setting('fanvictor-settings-group', 'fanvictor_league_size');
        register_setting('fanvictor-settings-group', 'fanvictor_winner_percent');
        register_setting('fanvictor-settings-group', 'fanvictor_first_place_percent');
        register_setting('fanvictor-settings-group', 'fanvictor_second_place_percent');
        register_setting('fanvictor-settings-group', 'fanvictor_third_place_percent');
        register_setting('fanvictor-settings-group', 'fanvictor_cash_to_credit');
        register_setting('fanvictor-settings-group', 'fanvictor_credit_to_cash');
        register_setting('fanvictor-settings-group', 'fanvictor_create_contest');
        register_setting('fanvictor-settings-group', 'fanvictor_payout_method');
        register_setting('fanvictor-settings-group', 'paypal_test');
        register_setting('fanvictor-settings-group', 'paypal_email_account');
        register_setting('fanvictor-settings-group', 'fanvictor_minimum_deposit');
        register_setting('fanvictor-settings-group', 'directpost_login');
    }
    
    //menu bar
    public static function loadMenuBar()
    {
        add_menu_page("Fan Victor Pages", "Fan Victor", '', 'fanvictor_page', '');

        $hook = add_submenu_page('fanvictor_page', __('Manage Sports', FV_DOMAIN), __('Manage Sports', FV_DOMAIN), 'manage_options', 'manage-sports', array('Fanvictor_Sports', 'manageSports'));
        add_action("load-$hook", array('Fanvictor_Admin', "sports_screen"));
        add_submenu_page('fanvictor_page', __('Add Sport', FV_DOMAIN), __('Add Sport', FV_DOMAIN), 'manage_options', 'add-sports', array('Fanvictor_Sports', 'addSports'));
        
        $hook = add_submenu_page('fanvictor_page', __('Manage Events', FV_DOMAIN), __('Manage Events', FV_DOMAIN), 'manage_options', 'manage-pools', array('Fanvictor_Pools', 'managePools'));
        add_action("load-$hook", array('Fanvictor_Admin', "pools_screen"));
        add_submenu_page('fanvictor_page', __('Add Events', FV_DOMAIN), __('Add Events', FV_DOMAIN), 'manage_options', 'add-pools', array('Fanvictor_Pools', 'addPools'));
        
        $hook = add_submenu_page('fanvictor_page', __('Manage Contests', FV_DOMAIN), __('Manage Contests', FV_DOMAIN), 'manage_options', 'manage-contests', array('Fanvictor_Contests', 'manageContests'));
        add_action("load-$hook", array('Fanvictor_Admin', "contests_screen"));
        add_submenu_page('fanvictor_page', __('Add Contests', FV_DOMAIN), __('Add Contests', FV_DOMAIN), 'manage_options', 'add-contests', array('Fanvictor_Contests', 'addContests'));
        
        $hook = add_submenu_page('fanvictor_page', __('Manage Fighters', FV_DOMAIN), __('Manage Fighters', FV_DOMAIN), 'manage_options', 'manage-fighters', array('Fanvictor_Fighters', 'manageFighters'));
        add_action("load-$hook", array('Fanvictor_Admin', "fighters_screen"));
        add_submenu_page('fanvictor_page', __('Add Fighters', FV_DOMAIN), __('Add Fighters', FV_DOMAIN), 'manage_options', 'add-fighters', array('Fanvictor_Fighters', 'addFighters'));
        
        $hook = add_submenu_page('fanvictor_page', __('Manage Teams', FV_DOMAIN), __('Manage Teams', FV_DOMAIN), 'manage_options', 'manage-teams', array('Fanvictor_Teams', 'manageTeams'));
        add_action("load-$hook", array('Fanvictor_Admin', "team_screen"));
        add_submenu_page('fanvictor_page', __('Add Teams', FV_DOMAIN), __('Add Teams', FV_DOMAIN), 'manage_options', 'add-teams', array('Fanvictor_Teams', 'addTeams'));
        
        $hook = add_submenu_page('fanvictor_page', __('Event Statistics', FV_DOMAIN), __('Event Statistics', FV_DOMAIN), 'manage_options', 'statistic', array('Fanvictor_Statistic', 'manageStatistic'));
        add_action("load-$hook", array('Fanvictor_Admin', "event_statistics_screen"));

        $hook = add_submenu_page('fanvictor_page', __('Manage Credits', FV_DOMAIN), __('Manage Credits', FV_DOMAIN), 'manage_options', 'credits', array('Fanvictor_Credits', 'manageCredits'));
        add_action("load-$hook", array('Fanvictor_Admin', "credits_screen"));
        
        $hook = add_submenu_page('fanvictor_page', __('Manage Withdrawls', FV_DOMAIN), __('Manage Withdrawls', FV_DOMAIN), 'manage_options', 'withdrawls', array('Fanvictor_Withdrawls', 'manageWithdrawls'));
        add_action("load-$hook", array('Fanvictor_Admin', "withdrawls_screen"));
        
        //v2
        $hook = add_submenu_page('fanvictor_page', __('Manage Player Position', FV_DOMAIN), __('Manage Player Position', FV_DOMAIN), 'manage_options', 'manage-playerposition', array('Fanvictor_PlayerPosition', 'managePlayerPosition'));
        add_action("load-$hook", array('Fanvictor_Admin', "playerposition_screen"));
        add_submenu_page('fanvictor_page', __('Add Player Position', FV_DOMAIN), __('Add Player Position', FV_DOMAIN), 'manage_options', 'add-playerposition', array('Fanvictor_PlayerPosition', 'addPlayerPosition'));
        
        $hook = add_submenu_page('fanvictor_page', __('Manage Scoring Category', FV_DOMAIN), __('Manage Scoring Category', FV_DOMAIN), 'manage_options', 'manage-scoringcategory', array('Fanvictor_ScoringCategory', 'manageScoringCategory'));
        add_action("load-$hook", array('Fanvictor_Admin', "scoringcategory_screen"));
        add_submenu_page('fanvictor_page', __('Add Scoring Category', FV_DOMAIN), __('Add Scoring Category', FV_DOMAIN), 'manage_options', 'add-scoringcategory', array('Fanvictor_ScoringCategory', 'addScoringCategory'));

        $hook = add_submenu_page('fanvictor_page', __('Manage Players', FV_DOMAIN), __('Manage Players', FV_DOMAIN), 'manage_options', 'manage-players', array('Fanvictor_Players', 'managePlayers'));
        add_action("load-$hook", array('Fanvictor_Admin', "players_screen"));
        add_submenu_page('fanvictor_page', __('Add Players', FV_DOMAIN), __('Add Players', FV_DOMAIN), 'manage_options', 'add-players', array('Fanvictor_Players', 'addPlayers'));
    
        $hook = add_submenu_page('fanvictor_page', __('Manage Transactions', FV_DOMAIN), __('Manage Transactions', FV_DOMAIN), 'manage_options', 'transactions', array('Fanvictor_Transactions', 'manageTransactions'));
        add_action("load-$hook", array('Fanvictor_Admin', "transactions_screen"));
    }
    
    static function sports_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_sports_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function pools_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_pools_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function team_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_team_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function fighters_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_fighters_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function event_statistics_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'event_statistics_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function credits_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'credits_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function withdrawls_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'withdrawls_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    //v2
    static function playerposition_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_playerposition_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function scoringcategory_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_scoringcategory_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function players_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_players_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function contests_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_contests_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function transactions_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'transactions_per_page'
        );
        add_screen_option( $option, $args );
    }
}
?>