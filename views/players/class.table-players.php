<?php
class TablePlayers extends WP_List_Table
{
    private static $players;
    private static $teams;
    private static $playerposition;
    function __construct()
    {
        self::$players = new Players();
        self::$teams = new Teams();
        self::$playerposition = new PlayerPosition();
        global $status, $page;
        $aResults = null;
        parent::__construct( array(
            'singular'  => __( 'book', 'mylisttable' , FV_DOMAIN),     //singular name of the listed records
            'plural'    => __( 'books', 'mylisttable' , FV_DOMAIN),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

    function column_default( $item, $column_name ) 
    {
        switch( $column_name ) 
        { 
            case 'image':
                return '<img width="35px" height="35px" alt="" src="'.$item['full_image_path'].'">';
            case 'name':
                return $item[ $column_name ];
            case 'salary':
                return number_format($item[ $column_name ]);
            case 'team':
                return $item['team_name'];
            case 'position':
                return $item['position_name'];
            case 'edit':
                return sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', 'add-players','edit',$item['id']);
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'image' => __('Image', FV_DOMAIN),
            'name' => __('Name', FV_DOMAIN),
            'salary' => __('Salary', FV_DOMAIN),
            'team' => __('Team', FV_DOMAIN),
            'position' => __('Position', FV_DOMAIN),
            'edit'    => '',
        );
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'name'  => array('name',false),
            'team'  => array('org_id',false),
            'position'  => array('position_id',false),
        );
        return $sortable_columns;
    }
    
    function usort_reorder( $a, $b ) 
    {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'DESC';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }

    function column_cb($item) 
    {
        if($item['siteID'] > 0)
        {
            return sprintf(
                '<input type="checkbox" name="id[]" value="%s" />', $item['id']
            );    
        }
        return '';
    }
    
    function prepare_items($keyword = null) 
    {
        $user_id = get_current_user_id();
        $screen = get_current_screen();
        
        // retrieve the "per_page" option
        $screen_option = $screen->get_option('per_page', 'option');
        
        //add page number to table usermeta
        if(isset($_POST['wp_screen_options']))
        {
            $screen_value = $_POST['wp_screen_options']['value'];
            $meta = get_user_meta($user_id, $screen_option);
            if($meta == null)
            {
                add_user_meta($user_id, $screen_option, $screen_value);
            }
            else 
            {
                update_user_meta($user_id, $screen_option, $screen_value);
            }
            header('Location:'.$_SERVER['REQUEST_URI']);
        }
        
        // retrieve the value of the option stored for the current user
        $item_per_page = get_user_meta(get_current_user_id(), $screen_option, true);
        
        if ( empty ( $item_per_page) || $item_per_page < 1 ) {
            // get the default value if none is set
            $item_per_page = $screen->get_option( 'per_page', 'default' );
        }
        
        //search
        $aCond = null;
        if($keyword != null)
        {
            $keyword = trim($keyword);
            $aCond = array("PLAYERS.name LIKE '%$keyword%'" => "");
        }

        //get data
        list($total_items, $aResults) = self::$players->getPlayersByFilter($aCond, 'id DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        $aResults = self::$players->parsePlayersData($aResults);
        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $sortable = $this->get_sortable_columns();
        if($aResults != null)
        {
            usort( $aResults, array( &$this, 'usort_reorder' ) );
        }
        $this->_column_headers = array( $columns, $hidden, $sortable );
        
        //pagination
        $this->set_pagination_args( array(
            'total_items' => $total_items,                 
            'per_page'    => $item_per_page       
        ) );
        $this->items = $aResults;
    }
}
?>