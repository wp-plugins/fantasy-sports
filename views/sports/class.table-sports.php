<?php
class TableSports extends WP_List_Table
{
    private static $sports;
    function __construct()
    {
        $this->item_per_page = 10;
        self::$sports = new Sports();
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
        return null;
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', FV_DOMAIN),
            'active' => __('Active', FV_DOMAIN),
			'reverse_points' => __('Reverse Point', FV_DOMAIN),
            'edit'    => '',
        );
        return $columns;
    }
    
    function column_cb($item) 
    {
        return '';
    }
    
    function column_name($itemSport) 
    {
        $delHtml = "";
        if(!isset($itemSport['child']) && $itemSport['siteID'] > 0)
        {
            $delHtml = '<input type="checkbox" value="'.$itemSport['id'].'" name="id[]">';
        }
        $editHtml = "";
        if($itemSport['siteID'] > 0)
        {
            $editHtml = '<a href="?page=add-sports&amp;action=edit&amp;id='.$itemSport['id'].'">Edit</a>';
        }
        $result =   '<tr class="alternate">
                        <th class="check-column" scope="row">
                            '.$delHtml.'
                        </th>
                        <td class="name column-name">
                            '.$itemSport['name'].'
                        </td>
                        <td></td>
						<td></td>
                        <td class="edit column-edit">
                            '.$editHtml.'
                        </td>
                    </tr>';
        if(isset($itemSport['child']) && $itemSport['child'] != null)
        {
            foreach($itemSport['child'] as $item)
            {
                $delHtml = "";
                if($item['siteID'] > 0)
                {
                    $delHtml = '<input type="checkbox" value="'.$itemSport['id'].'" name="id[]">';
                }
                $editHtml = "";
                if($item['siteID'] > 0)
                {
                    $editHtml = '<a href="?page=add-sports&amp;action=edit&amp;id='.$item['id'].'">Edit</a>';
                }
        
				//active
                $active_display = $unactive_display = 'style="display:none"';
                if($item['is_active'] == 1)
                {
                    $active_display = '';
                }
                else 
                {
                    $unactive_display = '';
                }
				
				//reverse point
                $rv_active_display = $rv_unactive_display = 'style="display:none"';
                if($item['reverse_points'] == 1)
                {
                    $rv_active_display = '';
                }
                else 
                {
                    $rv_unactive_display = '';
                }

                $result .= '<tr class="alternate">
                                <th class="check-column" scope="row">
                                    '.$delHtml.'
                                </th>
                                <td class="name column-name">
                                    <div style="padding-left: 30px"> |--'.$item['name'].' </div>
                                </td>
                                <td class="active column-active" id="setting'.$item['id'].'">
                                    <a class="active" '.$active_display.' title="Unactive" onclick="jQuery.admin.activeOrgsSetting('.$item['id'].', 0)">
                                        <img class="active" src="'.FANVICTOR__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                                    </a>
                                    <a class="unactive" '.$unactive_display.' title="Activate" onclick="jQuery.admin.activeOrgsSetting('.$item['id'].', 1)">
                                        <img src="'.FANVICTOR__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                                    </a>
                                </td>
								<td class="rv_active column-active" id="rv_setting'.$item['id'].'">
                                    <a class="active" '.$rv_active_display.' title="Unactive" onclick="jQuery.admin.reversePointOrgsSetting('.$item['id'].', 0)">
                                        <img class="active" src="'.FANVICTOR__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                                    </a>
                                    <a class="unactive" '.$rv_unactive_display.' title="Activate" onclick="jQuery.admin.reversePointOrgsSetting('.$item['id'].', 1)">
                                        <img src="'.FANVICTOR__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                                    </a>
                                </td>
                                <td class="edit column-edit">
                                    '.$editHtml.'
                                </td>
                            </tr>';
            }
        }
        return $result;
    }
    
    function prepare_items() 
    {
        //get data
        $aResults = self::$sports->getSports();
        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $sortable = array();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $this->items = $aResults;
    }
}
?>