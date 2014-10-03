<?php

/***

$url = someurl
                $client = new RestClient("GET", $url);
                $data = $client->send(false);
                return json_decode($data);
****/
class RestClient
{
	var $ch;
	var $url;
	var $pwd;
	var $http_code = 0;
	var $headers = false;
	var $cookies;
	function __construct($method, $url, $user=false, $pass=false)
	{
		$this->url = $url;
		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$method = strtoupper($method);
		switch($method)
		{
			case 'GET':
				curl_setopt($ch, CURLOPT_HTTPGET, 1);
				break;
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, 1);
				break;
			default:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
				break;
		}
		if ( $user )
		{
			if ( $pass )
				curl_setopt($ch, CURLOPT_USERPWD, ($this->pwd="$user:$pass"));
			else
				curl_setopt($ch, CURLOPT_USERPWD, ($this->pwd=$user));
		}

		$this->method = $method;
		$this->ch = $ch;
	}

	function __destruct()
	{
		curl_close($this->ch);
	}

	function _header($data, $replace=true)
	{
		if ( !$replace && $this->headers[$data] )
			return false;
		$this->headers[$data] = true;

		return true;
	}
		
	function setCookie($cook)
	{
		curl_setopt($this->ch, CURLOPT_COOKIE, $cook);
	}	

	function cookieHandler($ch, $str)
	{
		if(!strncmp($str, "Set-Cookie:", 11))
		{
			header($str,false);
		}
		return strlen($str);
	}

	function send($post=false)
	{
		if ( $this->headers )
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, array_keys($this->headers));

		if($this->method=="POST"){
            if(is_array($post))
            {
                $post = http_build_query($post);
            }
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($this->ch, CURLOPT_POST, 1);
            curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, "RestClient::cookieHandler");
            $data = curl_exec($this->ch);
		}	
		if($this->method=="GET"){
            $data = wp_remote_get($this->url);
            $data = $data['body'];
		}
        $this->checkResultExist($data);
		if ( !strlen($data) )
			return false;

		return $data;
	}
    
    private function checkResultExist(&$data)
    {
        if($this->isJSON($data))
        {
            $data = json_decode($data, true);
            if(isset($data['serverMessage']) && $data['serverMessage'] == 'YES')
            {
                $pool = new Pools();
                $pool->updateUserMoneyWon();
            }
            unset($data['serverMessage']);
            $data = json_encode($data);
        }
    }
    
    function isJSON($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
?>