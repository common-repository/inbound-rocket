<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

require_once(INBOUNDROCKET_AWEBER_CONNECTOR_PLUGIN_DIR . '/inc/aweber_api/aweber_api.php');

//=============================================
// WPAWeberConnectorAdmin Class
//=============================================
class WPAWeberConnectorAdmin extends WPInboundRocketAdmin {
        
	var $power_up_icon;
    var $options;
    private $nonce;
	
    private $appID = 'ab7c0b56';
	
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
		
        $this->nonce = wp_create_nonce( "aw_nonce" );
	    
		$this->options = get_option('inboundrocket_aw_options');
        
		$this->authed = !empty( $this->options['aw_auth_code'] ) ? TRUE : FALSE;
		
		if ( $this->authed )
            $this->invalid_key = FALSE;//$this->ir_aw_connect_to_api($this->options['aw_auth_code']);
    }
    
    public static function init(){
        if(self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;            
    }


    //=============================================
    // Settings Page
    //=============================================

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize ( $input )
    {
        $new_input = array();
				
		if( isset( $input['aw_auth_code'] ) )
			$new_input['aw_auth_code'] = sanitize_text_field( $input['aw_auth_code'] );
		
        return $new_input;
    }

    /**
     * Prints input form for settings page
     */
  
	function aw_input_fields () 
	{
		$this->options = get_option('inboundrocket_aw_options');
				
        $aw_auth_code = isset($this->options['aw_auth_code']) ? $this->options['aw_auth_code'] : '';
        
		echo '<input id="aw_auth_code" type="text" name="inboundrocket_aw_options[aw_auth_code]" value="'.$aw_auth_code.'" style="width: 430px; margin-right: 20px;" placeholder="Paste Authorization Code Here"/>';
		if( $this->authed ) echo '<input type="button" name="ir_aw_api_key_del" id="ir_aw_api_key_del" class="button button-primary" value="Delete" />';
		
		if ( $this->authed && !$this->invalid_key )
        {
			echo '<p>AWeber '.__('connected successfully','inbound-rocket').'! <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists">'.__('Select a lead list to send contacts to','inbound-rocket').' AWeber</a>.</p>';
		} else {
			echo '<p><a href="https://auth.aweber.com/1.0/oauth/authorize_app/'.$this->appID.'" target="_blank" id="authorize_aweber" class="button button-primary">Get Authorzation Code</a></p>';
			echo '<p><a target="_blank" href="https://help.aweber.com/hc/en-us/articles/204031226-How-Do-I-Authorize-an-App-">'.__('How to get your Authorization Code','inbound-rocket').'</a> '.__('from','inbound-rocket').' <a href="http://www.aweber.com" target="_blank">AWeber.com</a></p>';
			echo '<p><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists">'.__('Edit your lead lists','inbound-rocket').'</a> or <a href="https://www.aweber.com/users/newlist#about" target="_blank">'.__('Create a new list on','inbound-rocket').' AWeber.com</a> <small>(note: you must be logged into AWeber)</small></p>';
		}
            
        if ( $this->authed && !$this->invalid_key )
        {
            $synced_lists = $this->ir_get_synced_list_for_esp('aweber');
            $list_value_pairs = array();
            $synced_list_count = 0;

            echo '<table>';
            foreach ( $synced_lists as $synced_list )
            {
                foreach ( stripslashes_deep(unserialize($synced_list->tag_synced_lists)) as $tag_synced_list )
                {
                    if ( $tag_synced_list['esp'] == 'aweber' )
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
        }	 
		  
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
     * Format API-returned lists into parseable format on front end for inboundrocket to consume
     *
     * @return array    
     */
    function ir_get_lists ( )
    {		
		$sanitized_lists = array();
		
		try 
		{
			$aweber = $this->ir_get_aweber_instance();
		
			if ( ! is_object( $aweber ) || false === ( $creds = get_option( 'inboundrocket_aw_credentials' ) ) ) {
				return array();
			}
			
			$creds = get_option( 'inboundrocket_aw_credentials');
			
			$account = $aweber->getAccount( $creds['aw_ak'], $creds['aw_as'] );
			$account_id = $account->id;
			$lists = $account->lists;
			
			if( $account ) {
				foreach ( $lists->data['entries'] as $list )
				{
					$list_obj = (Object)NULL;
					$list_obj->id = $list['id'];
					$list_obj->name = $list['name'];

					array_push($sanitized_lists, $list_obj);
				}
			}
		}
		catch ( AWeberAPIException $exc )
		{
			inboundrocket_track_plugin_activity('AWeber Error: '.$exc->type.': '.$exc->message, array('esp_connector' => 'aweber', 'function' => 'ir_get_lists'));
		}

		return $sanitized_lists;
    }
	
	public function ir_get_aweber_instance() {
		$this->options = get_option( 'inboundrocket_aw_options' );
		$authorization_code = isset( $this->options['aw_auth_code'] ) ? trim( $this->options['aw_auth_code'] ) : '';
		$msg = '';
		if ( ! empty( $authorization_code ) ) {
			$error_code = "";
			if ( false !== get_option( 'inboundrocket_aw_credentials' ) ) {
				$creds = get_option( 'inboundrocket_aw_credentials' );
				$msg = $creds;
				try {
					$api = new AWeberAPI( $creds['aw_ck'], $creds['aw_cs'] );
				} catch( AWeberAPIException $exc ) {
					$api = false;
					inboundrocket_track_plugin_activity('AWeber Error 1: '.$exc->type.': '.$exc->message, array('esp_connector' => 'aweber', 'function' => 'ir_get_aweber_instance'));
				}
				return $api;
			} else {
				try {
					list( $consumer_key, $consumer_secret, $access_key, $access_secret ) = AWeberAPI::getDataFromAweberID( $authorization_code );
				} catch (AWeberAPIException $exc) {
					list( $consumer_key, $consumer_secret, $access_key, $access_secret ) = null;
					# make error messages customer friendly.
					$descr = $exc->message;
					$descr = preg_replace( '/http.*$/i', '', $descr );     # strip labs.aweber.com documentation url from error message
					$descr = preg_replace( '/[\.\!:]+.*$/i', '', $descr ); # strip anything following a . : or ! character
					$error_code = " ($descr)";
					inboundrocket_track_plugin_activity('AWeber Error 2: '.$exc->type.': '.$descr, array('esp_connector' => 'aweber', 'function' => 'ir_get_aweber_instance'));
				} catch ( AWeberOAuthDataMissing $exc ) {
					list( $consumer_key, $consumer_secret, $access_key, $access_secret ) = null;
					inboundrocket_track_plugin_activity('AWeber Error 3: '.$exc->type.': '.$exc->message, array('esp_connector' => 'aweber', 'function' => 'ir_get_aweber_instance'));
				} catch ( AWeberException $exc ) {
					list( $consumer_key, $consumer_secret, $access_key, $access_secret ) = null;
					inboundrocket_track_plugin_activity('AWeber Error 3: '.$exc->type.': '.$exc->message, array('esp_connector' => 'aweber', 'function' => 'ir_get_aweber_instance'));
				}
				if ( !$access_secret ) {
					$msg =  '<div id="aweber_access_token_failed" class="error">';
					$msg .= "Unable to connect to your AWeber Account$error_code:<br />";
					# show oauth_id if it failed and an api exception was not raised
					if ( empty( $error_code ) ) {
						$msg .= "Authorization code entered was: $authorization_code <br />";
					}
					$msg .= "Please make sure you entered the complete authorization code and try again.</div>";
				} else {
					$aw_creds = array(
						'aw_ck' => $consumer_key,
						'aw_cs' => $consumer_secret,
						'aw_ak' => $access_key,
						'aw_as' => $access_secret,
					);
				    inboundrocket_update_option( 'inboundrocket_aw_credentials', $aw_creds );
				 }
			}
		}
		$msg = isset( $msg ) ? $msg : 'NULL';
		inboundrocket_track_plugin_activity('AWeber Response: '.$msg, array('esp_connector' => 'aweber', 'function' => 'ir_get_aweber_instance'));
	}
	
	/**
     * Delete AWeber API key in settings
     *
     * @param string
     * @return bool    
     */
    function ir_aw_delete_api_key ( )
    {
	    $nonce = $_POST['ir_aw_nonce'];
		if( !wp_verify_nonce( $nonce,'ir_aw_nonce' ) || !current_user_can( 'manage_options' ) ) die('Busted!');
		
	    if ( isset($this->options['aw_auth_code']) )
        {
           delete_option( 'inboundrocket_aw_options' );
			$this->authed = FALSE;
			unset($this->options['aw_auth_code']);
			unset($this->options['aw_ck']);
			unset($this->options['aw_cs']);
			unset($this->options['aw_ak']);
			unset($this->options['aw_as']);
           echo 'success';
        } else {
	       echo 'ignore';
        }
        wp_die();
    }
    
    function ir_aw_action_javascript() { 
	    $this->nonce = wp_create_nonce( "ir_aw_nonce" );
    ?>
		<script type="text/javascript" >
		jQuery(document).ready(function(){
		jQuery('#ir_aw_api_key_del').on('click',function(e){ 
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', 
			{ 
    			action: 'aw_delete_api_key', 
    			ir_aw_nonce: '<?php echo $this->nonce; ?>'
	        },
	        function(response) {
				console.log(response);
				if( response == 'success' ) location.href='/wp-admin/admin.php?page=inboundrocket_settings&tab=inboundrocket_aw_options';
			});
		});
		});
		</script> <?php
	}
}