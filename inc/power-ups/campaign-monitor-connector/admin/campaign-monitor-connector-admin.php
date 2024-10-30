<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');
	
//=============================================
// WPCampaignMonitorConnectAdmin Class
//=============================================
class WPCampaignMonitorConnectorAdmin extends WPInboundRocketAdmin {
    
    var $power_up_settings_section = 'inboundrocket_campaign_monitor_connector_options_section';
    var $power_option_name = 'inboundrocket_campaign_monitor_connector_options';
    var $power_up_icon;
    var $options;
    var $nonce;
    var $authed = FALSE;
    var $invalid_key = FALSE;
    
    private static $_instance = null;
    
    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }

    /**
     * Class constructor
     */
    function __construct ( )
    {
        //=============================================
        // Hooks & Filters
        //=============================================
        $this->options = get_option('inboundrocket_cm_options');
        
        $this->nonce = wp_create_nonce( "ir_cm_nonce" );
	        	        
        $this->authed = ( isset($this->options['ir_cm_api_key']) && $this->options['ir_cm_api_key'] ? TRUE : FALSE );

        if ( $this->authed )
            $this->invalid_key = $this->ir_cm_check_invalid_api_key($this->options['ir_cm_api_key']);
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

        if ( isset( $input['ir_cm_api_key'] ) )
            $new_input['ir_cm_api_key'] = sanitize_text_field( $input['ir_cm_api_key'] );

        return $new_input;
    }

    /**
     * Prints API key input for settings page
     */
    function ir_cm_api_key_callback ()
    {
        $ir_cm_api_key = ( isset($this->options['ir_cm_api_key']) && $this->options['ir_cm_api_key'] ? $this->options['ir_cm_api_key'] : '' ); // Get header from options, or show default
        
        printf(
            '<input id="ir_cm_api_key" type="text" id="title" name="' . $this->power_option_name . '[ir_cm_api_key]" value="%s" style="width: 430px;"/>',
            $ir_cm_api_key
        );

        if ( ! isset($ir_cm_api_key) || ! $ir_cm_api_key || $this->invalid_key )
            echo '<p><a target="_blank" href="http://help.campaignmonitor.com/topic.aspx?t=206">'.__('Get your API key','inbound-rocket').'</a> '.__('from','inbound-rocket').' <a href="https://login.createsend.com/l" target="_blank">CampaignMonitor.com</a></p>';
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
     * @return array    
     */
    function ir_get_lists ()
    {
        $cm = new IR_Campaign_Monitor($this->options['ir_cm_api_key']);
        $lists = $cm->get_lists();
		
		//die(print_r($lists));
		
        if ( count($lists) )
        {
            foreach ( $lists as $list )
            {
                $list_obj = (Object)NULL;
                $list_obj->id = $list->ListID;
                $list_obj->name = $list->Name; // . ' (Client: ' . $list->ClientName . ')';

                $sanitized_lists[] = $list_obj;
            }
        }
        
        return $sanitized_lists;
    }

    /**
     * Use Campaign Monitor API key to try to grab corresponding user profile to check validity of key
     *
     * @param string
     * @return bool    
     */
    function ir_cm_check_invalid_api_key ( $api_key )
    {
        $cm = new IR_Campaign_Monitor($api_key);
        
        $invalid_key = FALSE;
		
        if ( !$cm->authorize() )
        {
            $invalid_key = TRUE;
        }

        return $invalid_key;
    }

    /**
     * Delete Campaign Monitor API key in settings
     *
     * @param string
     * @return bool    
     */
    function ir_cm_delete_api_key ( )
    {
	    $nonce = $_POST['ir_cm_nonce'];
		if( !wp_verify_nonce( $nonce,'ir_cm_nonce' ) || !current_user_can( 'manage_options' ) ) die('Busted!');
		
	    if ( isset($this->options['ir_cm_api_key']) )
        {
           delete_option( 'inboundrocket_cm_options' );
           
           unset($this->options['ir_cm_api_key']);
           unset($this->options['ir_cm_client_id']);
           echo 'success';
        } else {
	       echo 'ignore';
        }
        wp_die();
    }
    
    function ir_cm_action_javascript() { 
	    $this->nonce = wp_create_nonce( "ir_cm_nonce" );
    ?>
		<script type="text/javascript" >
		jQuery('#ir_cm_api_key_del').on('click',function(e){ 
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', 
			{ 
    			action: 'cm_delete_api_key', 
    			ir_mc_nonce: '<?php echo $this->nonce; ?>'
	        },
	        function(response) {
				console.log(response);
				if( response == 'success' ) location.href='/wp-admin/admin.php?page=inboundrocket_settings&tab=inboundrocket_cm_options';
			});
		});
		</script> <?php
	}    

    /**
     * Prints input form for settings page
     */
  
	function ir_cm_input_fields () 
	{        
		$ir_cm_api_key = ( isset($this->options['ir_cm_api_key']) && $this->options['ir_cm_api_key'] ? $this->options['ir_cm_api_key'] : '' ); // Get header from options, or show default
        
        echo '<input id="ir_cm_api_key" type="text" name="inboundrocket_cm_options[ir_cm_api_key]" value="'.$ir_cm_api_key.'" style="width: 430px; margin-right: 20px;"/>';
		if( $this->authed ) echo '<input type="button" name="ir_cm_api_key_del" id="ir_cm_api_key_del" class="button button-primary" value="Delete" />';

        if ( ! isset($ir_cm_api_key) || ! $ir_cm_api_key || $this->invalid_key )
            echo '<p><a target="_blank" href="http://help.campaignmonitor.com/topic.aspx?t=206">'.__('Get your API key','inbound-rocket').'</a> '.__('from','inbound-rocket').' <a href="https://login.createsend.com/l" target="_blank">CampaignMonitor.com</a></p>';
		if ( isset($this->options['ir_cm_api_key']) )
        {
            if ( $this->options['ir_cm_api_key'] && ! $this->invalid_key )

                $ir_cm_api_key = ( $this->options['ir_cm_api_key'] ? $this->options['ir_cm_api_key'] : '' ); // Get header from options, or show default
        
		        if ( isset($ir_cm_api_key ) )
		        {
		            $synced_lists = $this->ir_get_synced_list_for_esp('campaign_monitor');
		            $list_value_pairs = array();
		            $synced_list_count = 0;
		
		            echo '<table>';
		            foreach ( $synced_lists as $synced_list )
		            {
		                foreach ( stripslashes_deep(unserialize($synced_list->tag_synced_lists)) as $tag_synced_list )
		                {
		                    if ( $tag_synced_list['esp'] == 'campaign_monitor' )
		                    {
		                        echo '<tr class="synced-list-row">';
		                            echo '<td class="synced-list-cell"><span class="icon-tag"></span> ' . $synced_list->tag_text . '</td>';
		                            echo '<td class="synced-list-cell"><span class="synced-list-arrow">&#8594;</span></td>';
		                            echo '<td class="synced-list-cell"><span class="icon-envelope"></span> ' . $tag_synced_list['list_name'] . '</td>';
		                            echo '<td class="synced-list-edit"><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists&action=edit_list&lead_list=' . $synced_list->tag_id . '">edit list</a></td>';
		                        echo '</tr>';
		
		                        $synced_list_count++;
		                    }
		                }
		            }
		            echo '</table>';
		
		            if ( $synced_list_count ) {
		                echo '<p>Campaign Monitor '.__('connected successfully','inbound-rocket').'. <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists">'.__('Select a lead list to send contacts to','inbound-rocket').' Campaign Monitor</a>.</p>';
		            } else {
		                echo '<p><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists">'.__('Edit your lead lists','inbound-rocket').'</a> or <a href="https://login.createsend.com/l" target="_blank">'.__('Create a new list on','inbound-rocket').' CampaignMonitor.com</a></p>';
		            }
		        }    
        }	
	
	}
    
}

?>
