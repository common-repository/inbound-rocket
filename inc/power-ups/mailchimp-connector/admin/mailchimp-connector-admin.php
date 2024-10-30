<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

//=============================================
// WPInboundRocketAdmin Class
//=============================================
class WPMailChimpConnectorAdmin extends WPInboundRocketAdmin {
    
    var $power_up_icon;
    var $options;
    private $nonce;
    
    public $authed = FALSE;
    public $invalid_key = FALSE;
    
    private static $_instance = null;
    
    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }
    
	/**
     * Class constructor
     */
    function __construct()
    {
	    //=============================================
        // Hooks & Filters
        //=============================================
		
		$this->nonce = wp_create_nonce( "mc_nonce" );
        
        $this->options = get_option('inboundrocket_mc_options');
        
        $this->authed = !empty( $this->options['ir_mc_api_key'] ) ? TRUE : FALSE;
        
        if ( $this->authed )
            $this->invalid_key = $this->ir_mc_check_invalid_api_key($this->options['ir_mc_api_key']);
    }
    
    public static function init(){
        if(self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;            
    }
	
	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    function sanitize ( $input )
    {
        $new_input = array();
        
        if( isset( $input['ir_mc_api_key'] ) )
        {
            $new_input['ir_mc_api_key'] = sanitize_text_field( $input['ir_mc_api_key'] );
			if ( $this->authed )
            	$this->invalid_key = $this->ir_mc_check_invalid_api_key($new_input['ir_mc_api_key']);
        }   
        if( isset( $input['ir_mc_list_id'] ) )
            $new_input['ir_mc_list_id'] = sanitize_text_field( $input['ir_mc_list_id'] );

        return $new_input;
    }

    /**
     * Get synced list for the ESP from the WordPress database
     *
     * @return array/object    
     */
    function ir_get_synced_list_for_esp ( $esp_name, $output_type = 'OBJECT' )
    {
        global $wpdb;

        $q = $wpdb->prepare("SELECT * FROM $wpdb->ir_tags WHERE tag_synced_lists LIKE '%%%s%%' AND tag_deleted = 0", $esp_name);
        $synced_lists = $wpdb->get_results($q, $output_type);

        return $synced_lists;
    }

    /**
     * Format API-returned lists into parseable format on front end
     *
     * @return Array    
     */
    function ir_get_lists ( )
    {
		$data = array(
			'fields' => 'lists', // total_items, _links
			'count' => 25, // the number of lists to return, default - all
		);
		$api_key = $this->options['ir_mc_api_key'];
		$sanitized_lists = array();
		$url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/';
		$result = json_decode( $this->ir_mc_curl_connect( $url, 'GET', $api_key, $data) );
		//print_r( $result);
		 
		if( !empty($result->lists) ) {
			foreach( $result->lists as $list ){

				$list_obj = (Object)NULL;
                $list_obj->id = $list->id;
                $list_obj->name = $list->name;

                array_push($sanitized_lists, $list_obj);
				// you can also use $list->date_created, $list->stats->unsubscribe_count, $list->stats->cleaned_count or vizit MailChimp API Reference for more parameters (link is above)
			}
		} elseif ( is_int( $result->status ) ) { // full error glossary is here http://developer.mailchimp.com/documentation/mailchimp/guides/error-glossary/
			die( '<strong>' . $result->title . ':</strong> ' . $result->detail );
		}

        return $sanitized_lists;
    }
	
	/**
     * Connect to MailChimp API v3
     *
     * @param url string
	 * @param request_type (GET, POST) string
	 * @param api_key string
	 * @param data array
     * @return object    
     */
	function ir_mc_curl_connect( $url, $request_type, $api_key, $data = array() ) {
		if( $request_type == 'GET' )
			$url .= '?' . http_build_query($data);
	 
		$mch = curl_init();
		$headers = array(
			'Content-Type: application/json',
			'Authorization: Basic '.base64_encode( 'user:'. $api_key )
		);
		curl_setopt($mch, CURLOPT_URL, $url );
		curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($mch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
		curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
		curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
		curl_setopt($mch, CURLOPT_TIMEOUT, 10);
		curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection
	 
		if( $request_type != 'GET' ) {
			curl_setopt($mch, CURLOPT_POST, true);
			curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json
		}
	 
		return curl_exec($mch);
	}

    /**
     * Use MailChimp API key to try to grab corresponding user profile to check validity of key
     *
     * @param string
     * @return bool    
     */
    function ir_mc_check_invalid_api_key ( $api_key )
    {
        $data = array(
			// no data for validation
		);
		$api_key = $this->options['ir_mc_api_key'];
		$url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/';
		$result = json_decode( $this->ir_mc_curl_connect( $url, 'GET', $api_key, $data) );

        if ( !empty($result->account_id) )
            $invalid_key = FALSE;
        else
        {
            $invalid_key = TRUE;
        }

        return $invalid_key;
    }
    
    /**
     * Delete MailChimp API key in settings
     *
     * @param string
     * @return bool    
     */
    function ir_mc_delete_api_key ( )
    {
	    $nonce = $_POST['ir_mc_nonce'];
		if( !wp_verify_nonce( $nonce,'ir_mc_nonce' ) || !current_user_can( 'manage_options' ) ) die('Busted!');
		
	    if ( isset($this->options['ir_mc_api_key']) )
        {
           delete_option( 'inboundrocket_mc_options' );
           
           unset($this->options['ir_mc_api_key']);
           unset($this->options['ir_mc_list_id']);
           echo 'success';
        } else {
	       echo 'ignore';
        }
        wp_die();
    }
    
    function ir_mc_action_javascript() { 
	    $this->nonce = wp_create_nonce( "ir_mc_nonce" );
    ?>
		<script type="text/javascript" >
		jQuery('#ir_mc_api_key_del').on('click',function(e){ 
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', 
			{ 
    			action: 'mc_delete_api_key', 
    			ir_mc_nonce: '<?php echo $this->nonce; ?>'
	        },
	        function(response) {
				console.log(response);
				if( response == 'success' ) location.href='/wp-admin/admin.php?page=inboundrocket_settings&tab=inboundrocket_mc_options';
			});
		});
		</script> <?php
	}
    
    /**
     * Prints input form for settings page, if connected prints synced lists out for settings page in format  Lead List Name → ESP list
     */
  
	function ir_mc_input_fields () 
	{        
		$ir_mc_api_key = ( isset($this->options['ir_mc_api_key']) && $this->options['ir_mc_api_key'] ? $this->options['ir_mc_api_key'] : '' ); // Get header from options, or show default
        
        //echo '<form action="options.php" method="post">';
		//settings_fields('inboundrocket_mc_options');
        echo '<input id="ir_mc_api_key" type="text" name="inboundrocket_mc_options[ir_mc_api_key]" value="'.$ir_mc_api_key.'" style="width: 430px; margin-right: 20px;"/>';
        if( $this->authed ) echo '<input type="button" name="ir_mc_api_key_del" id="ir_mc_api_key_del" class="button button-primary" value="Delete" />';
        //echo '</form>';

        if ( ! isset($ir_mc_api_key) || ! $ir_mc_api_key || $this->invalid_key )
            echo '<p><a target="_blank" href="http://kb.mailchimp.com/accounts/management/about-api-keys#Find-or-Generate-Your-API-Key">'.__('Get your API key','inbound-rocket').'</a> '.__('from','inbound-rocket').' <a href="http://admin.mailchimp.com/account/api/" target="_blank">MailChimp.com</a></p>';

		if ( isset($this->options['ir_mc_api_key']) )
        {
            if ( $this->options['ir_mc_api_key'] && ! $this->invalid_key )

                $ir_mc_api_key = ( $this->options['ir_mc_api_key'] ? $this->options['ir_mc_api_key'] : '' ); // Get header from options, or show default
        
		        if ( isset($ir_mc_api_key ) )
		        {
		            $synced_lists = $this->ir_get_synced_list_for_esp('mailchimp');
		            $list_value_pairs = array();
		            $synced_list_count = 0;
		
		            echo '<table>';
		            foreach ( $synced_lists as $synced_list )
		            {
		                foreach ( stripslashes_deep(unserialize($synced_list->tag_synced_lists)) as $tag_synced_list )
		                {
		                    if ( $tag_synced_list['esp'] == 'mailchimp' )
		                    {
		                        echo '<tr class="synced-list-row">';
		                            echo '<td class="synced-list-cell"><span class="icon-tag"></span> ' . $synced_list->tag_text . '</td>';
		                            echo '<td class="synced-list-cell"><span class="synced-list-arrow">&#8594;</span></td>';
		                            echo '<td class="synced-list-cell"><span class="icon-envelope"></span> ' . $tag_synced_list['list_name'] . '</td>';
		                            echo '<td class="synced-list-edit"><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists&action=edit_list&lead_list=' . $synced_list->tag_id . '">'.__('edit list','inbound-rocket').'</a></td>';
		                        echo '</tr>';
		
		                        $synced_list_count++;
		                    }
		                }
		            }
		            echo '</table>';
		
		            if ( ! $synced_list_count ) {
		                echo '<p>MailChimp '.__('connected successfully','inbound-rocket').'. <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists">'.__('Select a lead list to send contacts to','inbound-rocket').' MailChimp</a>.</p>';
		            } else {
		                echo '<p><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists">'.__('Edit your lead lists','inbound-rocket').'</a> or <a href="http://admin.mailchimp.com/lists/new-list/" target="_blank">'.__('Create a new list on','inbound-rocket').' MailChimp.com</a></p>';
		            }
		        }    
        }	
	
	}
	
}

?>
