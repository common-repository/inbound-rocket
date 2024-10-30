<?php
/**
 *
 *  InboundRocket Connector
 *  Last modified: 9/7/15
 *
 */
 
if( !class_exists('IR_Connect') ) {
	class IR_Connect {
		
		var $type = 'live'; //@TODO change to 'live'
		var $url = '';
		
		function __construct($url)
		{
			$url = str_replace(array('http://','https://','/'), array('','',''), $url);
			$this->url = 'http://'.$url;
		}
		
		function auth($url)
		{
			$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $this->url.'/auth/'.$url,
			    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']
			));
			$resp = curl_exec($curl);
			curl_close($curl);
			return json_decode($resp);
		}
		
		function download_ec($token)
		{
			$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $this->url.'/download/ec/'.$token,
			    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']
			));
			$resp = curl_exec($curl);
			curl_close($curl);
			return json_decode($resp);
		}
		
		function post($post_array = array(), $method)
		{
			$post_array['type'] = $this->type;
			$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $this->url.'/'.$method,
			    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			    CURLOPT_POST => 1,
			    CURLOPT_POSTFIELDS => http_build_query($post_array)
			));
			$resp = curl_exec($curl);
			curl_close($curl);
			return json_decode($resp);
		}
	}
}