<?php
class FV_CouponModel
{
    public function __construct() 
    {
        $this->action_type = array(array("name" => "Add Money", "value" => CP_ACTION_ADD_MONEY),
                                   array("name" => "Extra Deposit", "value" => CP_ACTION_EXTRA_DEPOSIT));
        $this->discount_type = array(array("name" => "Percent", "value" => CP_DISCOUNT_PERCENT),
                                   array("name" => "Price", "value" => CP_DISCOUNT_PRICE));
    }
    
    public function isActionTypeExist($val)
    {
        foreach($this->action_type as $item)
        {
            if($item['value'] == $val)
            {
                return true;
            }
        }
        return false;
    }
    
    public function isDiscountTypeExist($val)
    {
        foreach($this->discount_type as $item)
        {
            if($item['value'] == $val)
            {
                return true;
            }
        }
        return false;
    }
    
    public function isCouponExist($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'coupon';
        $sCond = "WHERE id = '".(int)$id."'";
        $sql = "SELECT COUNT(*) "
             . "FROM $table_name "
             . $sCond;
        $data = $wpdb->get_var($sql);
        if($data == 1)
        {
            return true;
        }
        return false;
    }
    
    public function isCouponCodeExist($coupon_code, $action_type)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'coupon';
        $sCond = "WHERE UNIX_TIMESTAMP(NOW()) >= UNIX_TIMESTAMP(start_date) AND "
                     . "UNIX_TIMESTAMP(NOW()) <= UNIX_TIMESTAMP(expiry_date) AND "
                     . "coupon_code = '$coupon_code' AND "
                     . "action_type = '$action_type' ";
        $sql = "SELECT COUNT(*) "
             . "FROM $table_name "
             . $sCond;
        $data = $wpdb->get_var($sql);
        if($data > 0)
        {
            return true;
        }
        return false;
    }
    
    public function isCouponCodeUsed($coupon_code, $action_type)
    {
        global $wpdb;
        $coupon = $this->getCouponByCode($coupon_code, $action_type);
        if($coupon != null)
        {
            $table_name = $wpdb->prefix.'coupon_used';
            $sCond = "WHERE coupon_id = '".$coupon->id."' AND "
                         . "user_id = '". get_current_user_id()."' ";
            $sql = "SELECT COUNT(*) "
                 . "FROM $table_name "
                 . $sCond;
            $used = $wpdb->get_var($sql);
        }
        
        if(($coupon->coupon_time > 0) && ($used >= $coupon->coupon_time))
        {
            return true;
        }
        return false;
    }
    
    public function isCouponCodeLimit($coupon_code, $action_type)
    {
        global $wpdb;
        $coupon = $this->getCouponByCode($coupon_code, $action_type);
        if($coupon != null)
        {
            $table_name = $wpdb->prefix.'coupon_used';
            $sCond = "WHERE coupon_id = '".$coupon->id."'";
            $sql = "SELECT COUNT(*) "
                 . "FROM $table_name "
                 . $sCond;
            $total = $wpdb->get_var($sql);
        }
        
        if($total > 0 && $coupon->num_of_redemption > 0 && $total >= $coupon->num_of_redemption)
        {
            return true;
        }
        return false;
    }
    
    public function isHasCoupon($action_type)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'coupon';
        $sql = "SHOW TABLES LIKE '$table_name'";
        $data = $wpdb->get_var($sql);
        if(empty($data))
        {
            return false;
        }
        
        $sCond = "WHERE ((UNIX_TIMESTAMP(NOW()) >= UNIX_TIMESTAMP(start_date) AND "
                     . "UNIX_TIMESTAMP(NOW()) <= UNIX_TIMESTAMP(expiry_date)) OR no_expiry = 1) AND "
                     . "is_active = 1 AND "
                     . "action_type = '$action_type' ";
        $sql = "SELECT COUNT(*) "
             . "FROM $table_name "
             . $sCond;
        $data = $wpdb->get_var($sql);
        if($data > 0)
        {
            return true;
        }
        return false;
    }
    
    public function getCouponList($aConds, $sSort = null, $iPage = '', $iLimit = '')
    {
        switch($sSort)
        {
            case 'name':
                $sSort = 'name ASC';
                break;
            default :
                $sSort = 'id ASC';
        }
        global $wpdb;
        $tableName = $wpdb->prefix.'coupon';	
        if($aConds != null && is_array($aConds))
        {
            $aConds = implode('AND', $aConds);
        }
        global $wpdb;
        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT COUNT(*) "
             . "FROM $tableName "
             . $sCond;
        $iCnt = $wpdb->get_var($sql);

        $sql = "SELECT * "
             . "FROM $tableName "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit ";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
		return array($iCnt, $aRows);
    }
    
    public function getCoupon($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'coupon';
        $sCond = "WHERE id = '".(int)$id."'";
        $sql = "SELECT * "
             . "FROM $table_name "
             . $sCond;
        $data = $wpdb->get_results($sql);
        if($data != null)
        {
            $data = json_decode(json_encode($data[0]), true);
            return $data;
        }
        return null;
    }
    
    public function getCouponByCode($coupon_code, $action_type)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'coupon';
        $sCond = "WHERE UNIX_TIMESTAMP(NOW()) >= UNIX_TIMESTAMP(start_date) AND "
                     . "UNIX_TIMESTAMP(NOW()) <= UNIX_TIMESTAMP(expiry_date) AND "
                     . "coupon_code = '$coupon_code' AND "
                     . "action_type = '$action_type'";
        $sql = "SELECT * "
             . "FROM $table_name "
             . $sCond
             . "ORDER BY id DESC LIMIT 1";
        $data = $wpdb->get_results($sql);
        if($data != null)
        {
            return $data[0];
        }
        return null;
    }
    
    public function getTotalDiscountValue($discount_type, $discount_value, $money = 0)
    {
        switch($discount_type)
        {
            case CP_DISCOUNT_PERCENT:
                return round($money * $discount_value / 100, 2);
                break;
            case CP_DISCOUNT_PRICE:
                return $discount_value;
                break;
        }
        return 0;
    }
    
    public function addCouponUsed($coupon_id, $user_id)
    {
        global $wpdb;
        return $wpdb->insert($wpdb->prefix.'coupon_used', array('coupon_id' => $coupon_id, 
                                                                'user_id' => $user_id,
                                                                'created' => date('Y-m-d H:i:s')));
    }
}
?>