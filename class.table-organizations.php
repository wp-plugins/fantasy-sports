<?php
class TableOrganizations extends WP_List_Table
{
    private static $orgs;
    function __construct()
    {
        $this->item_per_page = 10;
        self::$orgs = new Organizations();
        global $status, $page;
        $this->data = null;
        parent::__construct( array(
            'singular'  => __( 'book', 'mylisttable' ),     //singular name of the listed records
            'plural'    => __( 'books', 'mylisttable' ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

    function column_default( $item, $column_name ) 
    {
        return "";
    }

    function get_columns()
    {
        $columns = array(
            'name' => __('Name', 'mylisttable'),
            'active' => __('Active'),
        );
        return $columns;
    }
    

    function column_name($item) 
    {
        $actions = array(
        );
        $result = null;
        foreach($item['orgs'] as $item)
        {
            $active_display = $unactive_display = 'style="display:none"';
            if($item['is_active'] == 1)
            {
                $active_display = '';
            }
            else 
            {
                $unactive_display = '';
            }
            
            $result .= '<tr class="alternate">
                            <td class="name column-name">
                                <div style="padding-left: 50px"> |--'.$item['description'].' </div>
                            </td>
                            <td class="active column-active" id="setting'.$item['organizationID'].'">
                                <a class="active" '.$active_display.' title="Unactive" onclick="jQuery.admin.activeOrgsSetting('.$item['organizationID'].', 0)">
                                    <img class="active" src="'.FANVICTOR__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                                </a>
                                <a class="unactive" '.$unactive_display.' title="Activate" onclick="jQuery.admin.activeOrgsSetting('.$item['organizationID'].', 1)">
                                    <img src="'.FANVICTOR__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                                </a>
                            </td>
                        </tr>';
        }
        return sprintf('%1$s %2$s', $item['sport'], $this->row_actions($actions) ).$result;
    }

    function prepare_items() 
    {
        //get data
        $this->data = self::$orgs->getAllSportOrgs();
        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $sortable = array();
        /*if($this->data != null)
        {
            usort( $this->data, array( &$this, 'usort_reorder' ) );
        }*/
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $this->items = $this->data;
    }
}
?>