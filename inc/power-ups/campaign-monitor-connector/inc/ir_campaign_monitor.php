<?php
/**
* Simple Campaign Monitor API wrapper
* 
*/
require_once dirname(__FILE__).'/csrest_general.php';
require_once dirname(__FILE__).'/csrest_clients.php';
require_once dirname(__FILE__).'/csrest_subscribers.php';

class IR_Campaign_Monitor
{
    private $api_key;
    private $client_id;

    /**
    * Create a new instance
    * @param string $api_key Your Campaign Monitor API key
    */
    function __construct($api_key)
    {
	    if(!session_id()) session_start();
	    
        $this->api_key = $api_key;
    }
	
	public function get_lists()
	{
		$apikey = $this->api_key;
		$rs = new CS_REST_Clients($_SESSION['ir_cm_client_id'],array('api_key'=>$apikey));
		$ret = $rs->get_lists();
		$ret = $ret->response;
		return $ret;
	}
	
	/**
    * Call an API method. Every request needs the API key, so that is added automatically -- you don't need to pass it in.
    * @param  array  $jsonargs   An array of arguments to pass to the method while using POST/PUT. Will be json encoded for you.
    * @return array              An array of response HTTP code and associative array of json decoded API response.
    */
    public function authorize()
    {	
	    $apikey = $this->api_key;
	    $rs = new CS_REST_General(array('api_key'=>$apikey));
	    $ret = $rs->get_clients();
	    if($ret->http_status_code > 300) {
		    return false;
	    }
	    $ret = $ret->response[0];
	    
	    if(isset($ret) && !empty($ret->ClientID)){
		    $this->client_id = $ret->ClientID;
	    }
	    
	    $_SESSION['ir_cm_client_id'] =  $this->client_id;
	    
        return !empty($_SESSION['ir_cm_client_id']) ? true : false;
    }
	
    /**
    * Call an API method. Every request needs the API key, so that is added automatically -- you don't need to pass it in.
    * @param  array  $jsonargs   An array of arguments to pass to the method while using POST/PUT. Will be json encoded for you.
    * @return array              An array of response HTTP code and associative array of json decoded API response.
    */
    public function call($list_id,$jsonargs = array())
    {
        $apikey = $this->api_key;
        $rs = new CS_REST_Subscribers(array('api_key'=>$apikey));
        $rs->set_list_id($list_id);
        $ret = $rs->add($jsonargs);
        $ret = $ret->response;
        return $ret;
    }
    
    /**
    * Call an API method. Every request needs the API key, so that is added automatically -- you don't need to pass it in.
    * @param  array  $jsonargs   An array of arguments to pass to the method while using POST/PUT. Will be json encoded for you.
    * @return array              An array of response HTTP code and associative array of json decoded API response.
    */
    public function bulk_call($list_id, $jsonargs = array(), $resubscribe=false)
    {
        $apikey = $this->api_key;
        $rs = new CS_REST_Subscribers(array('api_key'=>$apikey));
        $rs->set_list_id($list_id);
        $ret = $rs->import($jsonargs,$resubscribe);
        $ret = $ret->response;
        return $ret;
    }
    
    
    
}