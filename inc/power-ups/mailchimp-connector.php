<?php
/*
@Power-up Name: MailChimp Connector
@Power-up Class: WPMailChimpConnector
@Power-up Menu Text:
@Power-up Menu Link: settings
@Power-up Slug: mailchimp_connector
@Power-up URI: https://inboundrocket.co/features/email-service-provider-connectors/
@Power-up Description: Push your contacts to your MailChimp email lists.
@Power-up Icon: power-up-icon-mailchimp-connector
@Power-up Icon Small: power-up-icon-mailchimp-connector_small
@Power-up Sort: 1
@First Introduced: 1.1
@Power-up Tags: Newsletter, Connector, Email
@Auto Activate: No
@Permanently Enabled: No
@Hidden: No
@cURL Required: Yes
@Premium: No
@Options Name: inboundrocket_mc_options
*/

if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

//=============================================
// Define Constants
//=============================================

if ( !defined('INBOUNDROCKET_MAILCHIMP_CONNECTOR_PATH') )
    define('INBOUNDROCKET_MAILCHIMP_CONNECTOR_PATH', INBOUNDROCKET_PATH . '/inc/power-ups/mailchimp-connector');

if ( !defined('INBOUNDROCKET_MAILCHIMP_CONNECTOR_PLUGIN_DIR') )
    define('INBOUNDROCKET_MAILCHIMP_CONNECTOR_PLUGIN_DIR', INBOUNDROCKET_PLUGIN_DIR . '/inc/power-ups/mailchimp-connector');

if ( !defined('INBOUNDROCKET_MAILCHIMP_CONNECTOR_PLUGIN_SLUG') )
    define('INBOUNDROCKET_MAILCHIMP_CONNECTOR_SLUG', basename(dirname(__FILE__)));

//=============================================
// Include Needed Files
//=============================================
require_once(INBOUNDROCKET_MAILCHIMP_CONNECTOR_PLUGIN_DIR . '/admin/mailchimp-connector-admin.php');

//=============================================
// WPMailChimpConnector Class
//=============================================
class WPMailChimpConnector extends WPInboundRocket {
    
    var $admin;
    var $options;

    function __construct ( $activated )
    {
        //=============================================
        // Hooks & Filters 
        //=============================================

        if ( ! $activated )
            return false;
		
		global $inboundrocket_mailchimp_connector_wp;
	    $inboundrocket_mailchimp_connector_wp = $this;
	    
	    $this->admin = WPMailChimpConnectorAdmin::init();
        
        add_action( 'admin_footer', array( $this->admin, 'ir_mc_action_javascript' ) );
	    add_action( 'wp_ajax_mc_delete_api_key', array( $this->admin, 'ir_mc_delete_api_key' ) );
    }

    public function admin_init ( )
    {
        // Register power up settings
		register_setting('inboundrocket_mc_options', 'inboundrocket_mc_options', array($this->admin, 'sanitize'));
		add_settings_section('ir_mc_section', '', '', 'inboundrocket_mc_options');
		add_settings_field('ir_mc_settings', __('MailChimp API Key','inbound-rocket'), array($this->admin,'ir_mc_input_fields'), 'inboundrocket_mc_options', 'ir_mc_section');							
    }

    function power_up_setup_callback ( )
    {
        $this->admin->power_up_setup_callback();
    }

    /**
 @Adds a subcsriber to a specific list
     *
 @@param   array
 @@return  int/bool        API status code OR false if api key not set
     */
    function push_contact_to_list ( $list_parameters = array(), $status = 'subscribed' ) 
    {
	    $this->options = get_option('inboundrocket_mc_options');
	    
        if ( isset($this->options['ir_mc_api_key']) && $this->options['ir_mc_api_key'] && $list_parameters['list_id'] )
        {
			$data = array(
				'members' => array(
					'email_address' => $list_parameters['email'],
					'status'        => $status,
					'merge_fields'  => $list_parameters['merge_fields'],	
				)
			);
			$api_key = $this->options['ir_mc_api_key'];
			
			$url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/'.$list_parameters['list_id'].'/';
			$result = json_decode( $this->admin->ir_mc_curl_connect( $url, 'POST', $api_key, $data) );
			
            if(isset($result->error_count) && $result->error_count > 0) {
            
	            inboundrocket_track_plugin_activity('Contact NOT Pushed to List: '.$list_parameters['list_id'], array('esp_connector' => 'mailchimp', 'debug' => $result->errors));
	            
	            return FALSE;
            }
            
            inboundrocket_track_plugin_activity('Contact Pushed to List: '.$list_parameters['list_id'], array('esp_connector' => 'mailchimp'));

            return TRUE;
        }

        return FALSE;
    }

    /**
 @Adds a subcsriber to a specific list
     *
 @@param   string
 @@param   array
 @@return  int/bool        API status code OR false if api key not set
     */
    function bulk_push_contact_to_list ( $list_id = '', $contacts = '' ) 
    {
	    $this->options = get_option('inboundrocket_mc_options');
	    
        if ( isset($this->options['ir_mc_api_key']) && $this->options['ir_mc_api_key'] && $list_id )
        {
            $api_key = $this->options['ir_mc_api_key'];
			$url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/'.$list_id.'/';
			$data = array(
				'members' => array()
			);
            if ( count($contacts) )
            {
                foreach ( $contacts as $contact )
                {
                    array_push($data['members'], array(
                        'email' => array('email' => $contact->lead_email),
                        'status' => 'subscribe',
                        'merge_vars' => array(
                            'EMAIL' => $contact->lead_email,
                            'FNAME' => $contact->lead_first_name,
                            'LNAME' => $contact->lead_last_name
                        ))
                    );
                }
            }
            $result = json_decode( $this->admin->ir_mc_curl_connect( $url, 'POST', $api_key, $data) );
            
            if( isset($result->error_count) && $result->error_count > 0){
	
				inboundrocket_track_plugin_activity('Bulk Contacts NOT Pushed to List: '.$list_id, array('esp_connector' => 'mailchimp','debug'=>$result->errors));
	            return FALSE;
	            
            } else {
            
	            inboundrocket_track_plugin_activity('Bulk Contacts Pushed to List: '.$list_id, array('esp_connector' => 'mailchimp'));
	            return $result->total_created;
			
			}

            
        }

        return FALSE;
    }
}

//=============================================
// MailChimp Connector Init
//=============================================

global $inboundrocket_mailchimp_connector_wp;

?>