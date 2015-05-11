<?php
class TableStatistic extends WP_List_Table
{
    private static $leagues;
    private static $statistic;
    function __construct()
    {
        self::$leagues = new Leagues();
        self::$statistic = new Statistic();
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
            case 'ID':
                return $item['leagueID'];
            case 'contest':
                return $item[ 'name' ];
            case 'gameType':
                return $item[ $column_name ];
            case 'poolName':
                return $item[ $column_name ];
            case 'startDate':
                return $item[ $column_name ];
            case 'creator':
                return $item[ $column_name ];
            case 'status':
                return $item[ $column_name ];
            case 'detail':
                return '<a onclick="return jQuery.statistic.showPoolStatisticDetail('.$item['leagueID'].', \''.$item['name'].'\')" href="#">'.__('View', FV_DOMAIN).'</a>';
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'ID' => __('ID', FV_DOMAIN),
            'contest' => __('Name', FV_DOMAIN),
            'gameType' => __('Game Type', FV_DOMAIN),
            'poolName' => __('Event', FV_DOMAIN),
            'startDate' => __('Start Date', FV_DOMAIN),
            'creator' => __('Creator', FV_DOMAIN),
            'status' => __('Status', FV_DOMAIN),
            'detail' => __('Detail', FV_DOMAIN),
        );		
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'name'  => array('name',false),
        );
        return $sortable_columns;
    }
    
    function usort_reorder( $a, $b ) 
    {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'leagueID';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'ASC';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }
    
    function column_name($item) 
    {
        $actions = array(
        );

        return sprintf('%1$s %2$s', $item['poolName'], $this->row_actions($actions) );
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
            $aCond = array("poolName LIKE '%%%$keyword%%'");
        }
        
        //get data
        list($total_items, $aResults) = self::$leagues->getLeaguesByFilter($aCond, 'leagueID DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        $aResults = self::$leagues->parseLeagueData($aResults);
                
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
    
    function getData()
    {
        return self::$statistic->getProfit($aResults);
    }
}
?>