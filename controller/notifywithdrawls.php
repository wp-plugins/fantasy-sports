<?php
class Notifywithdrawls
{
    private static $payment;
    public function __construct() 
    {
        self::$payment = new Payment();
    }
    
	public function process()
	{
		$paypal = new MyPayPal(null, null, null, get_option('paypal_test'));
		$status = $paypal->callback();
		if($status == 'completed' || $status == "pending")
		{
			$custom = explode('|', $_POST['custom']);
			$aVals = array('status' => 'APPROVED', 
						   'response_message' => $custom[1],
						   'processedDate' => date('Y-m-d H:i:s'),
						   'transactionID' => $_POST['txn_id']);
			self::$payment->updateWithdraw($custom[0], $aVals);
		}
		else
		{
			redirect(admin_url().'admin.php?page=withdrawls');
		}
	}
}

?> 
