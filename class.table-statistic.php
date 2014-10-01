<?php
class TableStatistic extends WP_List_Table
{
    private static $pools;
    private static $statistic;
    function __construct()
    {
        self::$pools= new Pools();
        self::$statistic = new Statistic();
        global $status, $page;
        $this->data = $this->poolData = null;
        parent::__construct( array(
            'singular'  => __( 'book', 'mylisttable' ),     //singular name of the listed records
            'plural'    => __( 'books', 'mylisttable' ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

    function column_default( $item, $column_name ) 
    {
        switch( $column_name ) 
        { 
            case 'ID':
                return $item['poolID'];
            case 'name':
                return $item['poolName'];
            case 'startDate':
                return $item['startDate'];
            case 'status':
                return $item['status'];
            case 'detail':
                return '<a onclick="jQuery.admin.showPoolStatisticDetail('.$item['poolID'].', \'Event Statistics\')" href="#">View</a>';
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'ID' => __('ID', 'mylisttable'),
            'name' => __('Name', 'mylisttable'),
            'startDate' => __('Start Date', 'mylisttable'),
            'status' => __('Status', 'mylisttable'),
            'detail' => __('Detail', 'mylisttable'),
        );		
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'ID'  => array('ID',false),
            'name'  => array('poolName',false),
            'startDate'  => array('startDate',false),
            'status'  => array('status',false),
        );
        return $sortable_columns;
    }
    
    function usort_reorder( $a, $b ) 
    {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'poolID';
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
        list($total_items, $aPools) = self::$pools->getPoolsByFilter($aCond, 'poolID DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        $aPools = self::$statistic->parseListEvents($aPools);
        $this->poolData = $aPools;
        $this->data = $aPools['pools'];
        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $sortable = $this->get_sortable_columns();
        if($this->data != null)
        {
            usort( $this->data, array( &$this, 'usort_reorder' ) );
        }
        $this->_column_headers = array( $columns, $hidden, $sortable );
        
        //pagination
        $this->set_pagination_args( array(
            'total_items' => $total_items,                 
            'per_page'    => $item_per_page       
        ) );
        $this->items = $this->data;
    }
    
    function getData()
    {
        return $this->poolData;
    }
}
