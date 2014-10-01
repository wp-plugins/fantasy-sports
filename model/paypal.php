<?php
class Paypal
{
	function __construct()
	{
		$this->paypalmode = get_option('paypal_test') ? ".sandbox" : "";
		$this->port = get_option('paypal_test') ? 443 : 80;
		$this->url = "https://www".$this->paypalmode.".paypal.com/cgi-bin/webscr";
	}
	
    function parseData($values)
    {
        $aSettings = array('business' => $values['business'],
                            'cmd' => "_xclick",
                            'item_name' => $values['item_name'],
                            'item_number' => $values['item_number'],
                            'amount' => $values['amount'],
                            'currency_code' => 'USD',
                            'notify_url' => urlencode(stripslashes($values['notify_url'])),
                            'return' => urlencode(stripslashes($values['return'])),
                            'cancel_return' => urlencode(stripslashes($values['cancel_return'])),
							'custom' => $values['custom'],
                            'no_shipping' => '1',
                            'no_note' => '1');
        $dataString = null;
        foreach($aSettings as $k => $v)
        {
            $dataString[] = $k."=".$v;
        }
        $dataString = implode('&', $dataString);
        $url = $this->url."?".$dataString;
        return $url;
    }
    
    function callback()
    {
        if($_POST != null)
        {
            // Read the post from PayPal system and add 'cmd'
            $req = 'cmd=_notify-validate';

            // Loop through each of the variables posted by PayPal
            foreach ($_POST as $key => $value) 
            {
                $value = urlencode(stripslashes($value));
                $req .= "&$key=$value";
            }	

            $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "Host: www".$this->paypalmode.".paypal.com \r\n";
            $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
            $fp = fsockopen("ssl://www".$this->paypalmode.".paypal.com", $this->port, $error_no, $error_msg, 30);     
            fputs($fp, $header . $req);

            $bVerified = false;
            while (!feof($fp)) 
            {
                $res = fgets($fp, 1024);
                $res = strtoupper($res);
                if (strcmp($res, 'VERIFIED') == 0) 
                {  
                    $bVerified = true;
                    break;	
                }			
            }		
            fclose($fp);

            if ($bVerified === true)
            {
                if (isset($_POST['payment_status']))
                {
                    switch ($_POST['payment_status'])
                    {
                        case 'Completed':
                            $sStatus = 'completed';
                            break;
                        case 'Pending':
                            $sStatus = 'pending';
                            break;
                        case 'Refunded':
                        case 'Reversed':
                            $sStatus = 'cancel';
                            break;
                    }
                }
                return $sStatus;
            }
            else 
            {
                return 'failed';
            }
        }
        return 'failed';
    }
}
?>