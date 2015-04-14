<?php
class MyPayPal {
    
    public function __construct($username = null, $password = null, $signature = null, $isTest = false) 
    {
        $isTest == true ? $this->PayPalMode = 'sandbox' : $this->PayPalMode = 'live';
        $this->port = ($this->PayPalMode=='sandbox') ? 443 : 80;
        $this->paypalmode = ($this->PayPalMode=='sandbox') ? '.sandbox' : '';
        $this->PayPalApiUsername 		= $username; //PayPal API Username
        $this->PayPalApiPassword 		= $password; //Paypal API password
        $this->PayPalApiSignature 	= $signature; //Paypal API Signature
    }
    
    public function getPaypalUrl()
    {
        return 'https://www'.$this->paypalmode.'.paypal.com/cgi-bin/webscr';
    }
    
	function PPHttpPost($methodName_, $nvpStr_) 
    {
			// Set up your API credentials, PayPal end point, and API version.
        
			$API_UserName = urlencode($this->PayPalApiUsername);
			$API_Password = urlencode($this->PayPalApiPassword);
			$API_Signature = urlencode($this->PayPalApiSignature);

			$API_Endpoint = "https://api-3t".$this->paypalmode.".paypal.com/nvp";
			$version = urlencode('109.0');
		
			// Set the curl parameters.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
			// Turn off the server and peer verification (TrustManager Concept).
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
		
			// Set the API operation, version, and API signature in the request.
			$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
		
			// Set the request as a POST FIELD for curl.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		
			// Get response from the server.
			$httpResponse = curl_exec($ch);
		
			if(!$httpResponse) {
				exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
			}
		
			// Extract the response details.
			$httpResponseAr = explode("&", $httpResponse);
		
			$httpParsedResponseAr = array();
			foreach ($httpResponseAr as $i => $value) {
				$tmpAr = explode("=", $value);
				if(sizeof($tmpAr) > 1) {
					$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
				}
			}
		
			if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
				exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
			}
		
		return $httpParsedResponseAr;
	}
    
    function doCheckout($padata)
    {
		$httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $padata);
		
		//Respond according to message we receive from Paypal
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
		{
            //Redirect user to PayPal store with Token received.
            $paypalurl ='https://www'.$this->paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
            return $paypalurl;
			 
		}else{
			//Show error message
			/*echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
			echo '<pre>';
			print_r($httpParsedResponseAr);
			echo '</pre>';*/
            return false;
		}
    }
	
    function confirm($padata)
    {
        //We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
        $httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $padata);

        //Check if everything went ok..
        if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
        {
            /*if('Completed' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
            {
                echo '<div style="color:green">Payment Received! Your product will be sent to you very soon!</div>';
            }
            elseif('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
            {
                echo '<div style="color:red">Transaction Complete, but payment is still pending! '.
                'You need to manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
            }*/

            // we can retrive transection details using either GetTransactionDetails or GetExpressCheckoutDetails
            // GetTransactionDetails requires a Transaction ID, and GetExpressCheckoutDetails requires Token returned by SetExpressCheckOut
            $padata = 	'&TOKEN='.urlencode($_GET["token"]);
            $httpParsedResponseAr = $this->PPHttpPost('GetExpressCheckoutDetails', $padata);

            if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
            {
                /*echo '<br /><b>Stuff to store in database :</b><br /><pre>';
                echo '<pre>';
                print_r($httpParsedResponseAr);
                echo '</pre>';*/
                return $httpParsedResponseAr['PAYMENTREQUESTINFO_0_TRANSACTIONID'];
            } else  {
                /*echo '<div style="color:red"><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
                echo '<pre>';
                print_r($httpParsedResponseAr);
                echo '</pre>';*/
                return false;
            }

        }else{
                /*echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
                echo '<pre>';
                print_r($httpParsedResponseAr);
                echo '</pre>';*/
            return false;
        }
    }
    
    function callback()
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
}
?>