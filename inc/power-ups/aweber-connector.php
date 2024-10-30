<?php
/*
@Power-up Name: AWeber
@Power-up Class: WPAWeberConnector
@Power-up Menu Text: 
@Power-up Menu Link: settings
@Power-up Slug: aweber_connector
@Power-up URI: https://inboundrocket.co/features/email-service-provider-connectors
@Power-up Description: Push your contacts to your AWeber email lists.
@Power-up Icon: power-up-icon-aweber-connector
@Power-up Icon Small: power-up-icon-aweber-connector_small
@Power-up Sort: 1
@First Introduced: 1.5
@Power-up Tags: Newsletter, Connector, Email
@Auto Activate: No
@Permanently Enabled: No
@Hidden: No
@cURL Required: Yes
@Premium: No
@Options Name: inboundrocket_aw_options    
*/
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

//=============================================
// Define Constants
//=============================================

if ( !defined('INBOUNDROCKET_AWEBER_CONNECTOR_PATH') )
    define('INBOUNDROCKET_AWEBER_CONNECTOR_PATH', INBOUNDROCKET_PATH . '/inc/power-ups/aweber-connector');

if ( !defined('INBOUNDROCKET_AWEBER_CONNECTOR_PLUGIN_DIR') )
    define('INBOUNDROCKET_AWEBER_CONNECTOR_PLUGIN_DIR', INBOUNDROCKET_PLUGIN_DIR . '/inc/power-ups/aweber-connector');

if ( !defined('INBOUNDROCKET_AWEBER_CONNECTOR_PLUGIN_SLUG') )
    define('INBOUNDROCKET_AWEBER_CONNECTOR_PLUGIN_SLUG', basename(dirname(__FILE__)));

//=============================================
// Include Needed Files
//=============================================
require_once(INBOUNDROCKET_AWEBER_CONNECTOR_PLUGIN_DIR . '/admin/aweber-connector-admin.php');

//=============================================
// WPAWeberConnector Class
//=============================================
class WPAWeberConnector extends WPInboundRocket {
    
    var $admin;
    var $options;

    function __construct ( $activated )
    {
        //=============================================
        // Hooks & Filters 
        //=============================================

        if ( ! $activated )
            return false;

        global $inboundrocket_aweber_connector_wp;
        $inboundrocket_aweber_connector_wp = $this;
        
		$this->admin = WPAWeberConnectorAdmin::init();
		
		add_action( 'admin_footer', array( $this->admin, 'ir_aw_action_javascript' ) );
		add_action( 'wp_ajax_aw_delete_api_key', array( $this->admin, 'ir_aw_delete_api_key' ) );
    }
    
    public function admin_init ( )
    {
        // Register power up settings
		register_setting('inboundrocket_aw_options', 'inboundrocket_aw_options', array($this->admin, 'sanitize'));
		add_settings_section('ir_aw_section', '', '', 'inboundrocket_aw_options');
		add_settings_field('ir_aw_settings', __('aWeber Authorization Code','inbound-rocket'), array($this->admin, 'aw_input_fields'), 'inboundrocket_aw_options', 'ir_aw_section');						
    }

    function power_up_setup_callback ( )
    {
        $this->admin->power_up_setup_callback();
    }

    /**
 @Adds a subcsriber to a specific list
     *
 @@param   array
 @@return  int/bool
     */
    function push_contact_to_list ( $list_parameters = array() ) 
    {		
		$list_id = $list_parameters['list_id'];
		
		if (!$list_id)
			return FALSE;
		
		try 
		{
			$aweber = $this->admin->ir_get_aweber_instance();
		
			if ( ! is_object( $aweber ) || false === ( $creds = get_option( 'inboundrocket_aw_credentials' ) ) ) {
				return array();
			}
			
			$creds = get_option( 'inboundrocket_aw_credentials');
			
			$account = $aweber->getAccount( $creds['aw_ak'], $creds['aw_as'] );		
			$listURL = "/accounts/{$account->id}/lists/{$list_id}";
			$list = $account->loadFromUrl($listURL);
			
			$params = array(
				'email' => $list_parameters['email'],             
				'name' => $list_parameters['first_name'] .' '. $list_parameters['last_name']
			);
			$subscribers    = $list->subscribers;
			$new_subscriber = $subscribers->create( $params );
						 
			inboundrocket_track_plugin_activity('Contact '.$list_parameters['email'].' Pushed to List', array('esp_connector' => 'aweber','function' => 'push_contact_to_list'));

			return TRUE;
		} 
		catch(AWeberAPIException $exc) {
			inboundrocket_track_plugin_activity('AWeber Error: '.$exc->type.': '.$exc->message, array('esp_connector' => 'aweber','function' => 'push_contact_to_list'));
			return FALSE;
		}
    }

    /**
 @Bulk adds subcsribers to a specific list
     *
 @@param   string
 @@param   array
 @@return  int/bool        API status code OR false if api key not set
     */
    function bulk_push_contact_to_list ( $list_id = '', $contacts = '' ) 
    {
	    if ( count($contacts)<=0 ) return FALSE;
		
		$this->options = get_option('inboundrocket_aw_options');
	          	    
        if ( isset($this->options['aw_auth_code']) && $this->options['aw_auth_code'] && $list_id )
        {
			$success = 0;
			$failed = 0;
			foreach($contacts as $contact)
			{
				$push_contact_to_list = array();
				
				$push_contact_to_list['list_id'] = $list_id;
				$push_contact_to_list['first_name'] = $contact->lead_first_name;
				$push_contact_to_list['last_name'] = $contact->lead_last_name;
				$push_contact_to_list['email'] = $contact->lead_email;
				
				if($this->push_contact_to_list($push_contact_to_list)) {
				 // success
				 $success++;
				} else {
				 // error	
				 $failed++;
				}
			}
			inboundrocket_track_plugin_activity('Bulk Contacts Pushed to List - Success: '.$success.' : Error: '.$failed, array('esp_connector' => 'aweber'));
			return TRUE;
	        
		}
        return FALSE;
    }
}

//=============================================
// ESP Connector Init
//=============================================

global $inboundrocket_aweber_connector_wp;

?>