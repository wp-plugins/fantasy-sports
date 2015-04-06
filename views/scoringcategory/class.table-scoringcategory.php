<?php
class TableScoringCategory extends WP_List_Table
{
    private static $scoringcategory;
    private static $sports;
    function __construct()
    {
        self::$scoringcategory = new ScoringCategory();
        self::$sports = new Sports();
        global $status, $page;
        $this->data = null;
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
            case 'name':
                return $item[ $column_name ];
            case 'points':
                return $item[ $column_name ];
            case 'type':
                return $item['scoring_type'];
            case 'org_id':
                return $item['sport_name'];
            case 'edit':
                return sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', 'add-scoringcategory','edit',$item['id']);
            case 'is_active':
                $active_display = $unactive_display = 'style="display:none"';
                if($item['is_active'] == 1)
                {
                    $active_display = '';
                }
                else 
                {
                    $unactive_display = '';
                }
                return '<a id="active'.$item['id'].'" '.$active_display.' title="Unactive" onclick="jQuery.admin.activeScoringCategorySetting('.$item['id'].', 0)">
                            <img class="active" src="'.FANVICTOR__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                        </a>
                        <a id="unactive'.$item['id'].'" '.$unactive_display.' title="Activate" onclick="jQuery.admin.activeScoringCategorySetting('.$item['id'].', 1)">
                            <img src="'.FANVICTOR__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                        </a>';
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'name' => __('Name', FV_DOMAIN),
            'points' => __('Point', FV_DOMAIN),
            'org_id' => __('Sport', FV_DOMAIN),
            'type' => __('Type', FV_DOMAIN),
            'is_active' => __('Active', FV_DOMAIN),
            'edit'    => '',
        );
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'name'  => array('name',false),
            'organization'  => array('org_id',false),
            'type'  => array('scoring_type',false),
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
        return null;
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
            $aCond = array("name LIKE '%$keyword%'" => "");
        }
        
        //get data
        list($total_items, $this->data) = self::$scoringcategory->getScoringCategoryByFilter($aCond, 'id DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        
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
}
?>