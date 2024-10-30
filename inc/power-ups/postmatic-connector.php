<?php
/*
@Power-up Name: Postmatic Connector
@Power-up Class: WPPostmaticConnector
@Power-up Menu Text: 
@Power-up Menu Link: settings
@Power-up Slug: postmatic_connector
@Power-up URI: https://inboundrocket.co/features/email-service-provider-connectors/
@Power-up Description: Push your contacts to your Postmatic email lists.
@Power-up Icon: power-up-icon-postmatic-connector
@Power-up Icon Small: power-up-icon-postmatic-connector_small
@Power-up Sort: 1
@First Introduced: 1.2
@Power-up Tags: Newsletter, Connector, Email
@Auto Activate: No
@Permanently Enabled: No
@Hidden: No
@cURL Required: Yes
@Premium: No
@Options Name: inboundrocket_pm_options
*/
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

//=============================================
// Define Constants
//=============================================

if ( !defined('INBOUNDROCKET_POSTMATIC_CONNECTOR_PATH') )
    define('INBOUNDROCKET_POSTMATIC_CONNECTOR_PATH', INBOUNDROCKET_PATH . '/inc/power-ups/postmatic-connector');

if ( !defined('INBOUNDROCKET_POSTMATIC_CONNECTOR_PLUGIN_DIR') )
    define('INBOUNDROCKET_POSTMATIC_CONNECTOR_PLUGIN_DIR', INBOUNDROCKET_PLUGIN_DIR . '/inc/power-ups/postmatic-connector');

if ( !defined('INBOUNDROCKET_POSTMATIC_CONNECTOR_PLUGIN_SLUG') )
    define('INBOUNDROCKET_POSTMATIC_CONNECTOR_SLUG', basename(dirname(__FILE__)));

//=============================================
// Include Needed Files
//=============================================
require_once(INBOUNDROCKET_POSTMATIC_CONNECTOR_PLUGIN_DIR . '/admin/postmatic-connector-admin.php');

//=============================================
// WPPostmaticConnector Class
//=============================================
class WPPostmaticConnector extends WPInboundRocket {
    
    var $admin;
    var $options;

    /**
 @Class constructor
     */
    function __construct ( $activated )
    {
        //=============================================
        // Hooks & Filters 
        //=============================================

        if ( ! $activated )
            return false;
		
		global $inboundrocket_postmatic_connector_wp;
	    $inboundrocket_postmatic_connector_wp = $this;
	    
	    $this->admin = WPPostmaticConnectorAdmin::init();
        
	    add_action( 'wp_ajax_pm_delete_api_key', array( $this->admin, 'ir_pm_delete_api_key' ) );
    }

    public function admin_init ( )
    {
        // Register power up settings
		register_setting('inboundrocket_pm_options', 'inboundrocket_pm_options', array($this->admin, 'sanitize'));
		add_settings_section('ir_pm_section', 'Postmatic Options', '', 'inboundrocket_pm_options');
		add_settings_field('ir_pm_settings', __('Postmatic','inbound-rocket'), array($this->admin, 'ir_pm_input_fields'), 'inboundrocket_pm_options', 'ir_pm_section');							
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
    function push_contact_to_list ( $list_parameters = array() ) 
    {
	    $this->options = get_option('inboundrocket_pm_options');
	    
        if (class_exists('Prompt_Api') && class_exists( 'Prompt_Core' ) && Prompt_Core::$options->get( 'prompt_key' ))
        {
            $status = Prompt_Api::subscribe(array(
                "email_address" => $list_parameters['email'],
                "first_name" => $list_parameters['first_name'],
                "last_name" => $list_parameters['last_name']
                )
            );
            switch ( $status ) {
			  case Prompt_Api::INVALID_EMAIL:
			    inboundrocket_track_plugin_activity('Invalid Email', array('esp_connector' => 'postmatic'));
			  break;
			  case Prompt_Api::ALREADY_SUBSCRIBED:
			    inboundrocket_track_plugin_activity('Already subscribed - '.$list_parameters['email'], array('esp_connector' => 'postmatic'));
			  break;
			  case Prompt_Api::CONFIRMATION_SENT:
			    inboundrocket_track_plugin_activity('Contact Pushed to List - '.$list_parameters['email'], array('esp_connector' => 'postmatic'));
			  break;
			  case Prompt_Api::OPT_IN_SENT:
			    inboundrocket_track_plugin_activity('Opt-in Sent to '.$list_parameters['email'], array('esp_connector' => 'postmatic'));
			  break;
			}
            
            return TRUE;
        }

        return FALSE;
    }

    /**
 @Adds a multiple subcsribers to a specific list
     *
 @@param   string
 @@param   array
 @@return  int/bool        API status code OR false if api key not set
     */
    function bulk_push_contact_to_list ($list_id = '' /* NOT USED */, $contacts = '' ) 
    {
	    if (class_exists('Prompt_Api') && class_exists( 'Prompt_Core' ) && Prompt_Core::$options->get( 'prompt_key' ))
        {
			$batch_contacts = array();
            if ( count($contacts) )
            {
                foreach ( $contacts as $contact )
                {
                    $status = Prompt_Api::subscribe(array(
                        'email_address' => $contact->lead_email,
                        'first_name' => $contact->lead_first_name,
                        'last_name' => $contact->lead_last_name
                        )
                    );
                    switch ( $status ) {
					  case Prompt_Api::INVALID_EMAIL:
					    inboundrocket_track_plugin_activity('Invalid Email', array('esp_connector' => 'postmatic'));
					  break;
					  case Prompt_Api::ALREADY_SUBSCRIBED:
					    inboundrocket_track_plugin_activity('Already subscribed - '.$contact->lead_email, array('esp_connector' => 'postmatic'));
					  break;
					  case Prompt_Api::CONFIRMATION_SENT:
					    inboundrocket_track_plugin_activity('Contact Pushed to List - '.$contact->lead_email, array('esp_connector' => 'postmatic'));
					  break;
					  case Prompt_Api::OPT_IN_SENT:
					    inboundrocket_track_plugin_activity('Opt-in Sent to '.$contact->lead_email, array('esp_connector' => 'postmatic'));
					  break;
					}
                }
            }
            
            return TRUE;
        }

        return FALSE;
    }
}

//=============================================
// Postmatic Connector Init
//=============================================

global $inboundrocket_postmatic_connector_wp;

?>