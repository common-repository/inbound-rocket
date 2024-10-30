<?php
/*
@Power-up Name: Campaign Monitor Connector
@Power-up Class: WPCampaignMonitorConnector
@Power-up Menu Text: 
@Power-up Slug: campaign_monitor_connector
@Power-up Menu Link: settings
@Power-up URI: https://inboundrocket.co/features/email-service-provider-connectors/
@Power-up Description: Push your contacts to your Campaign Monitor email lists.
@Power-up Icon: power-up-icon-campaign-monitor-connector
@Power-up Icon Small: power-up-icon-campaign-monitor-connector_small
@Power-up Sort: 1
@First Introduced: 1.1
@Power-up Tags: Newsletter, Connector, Email
@Auto Activate: No
@Permanently Enabled: No
@Hidden: No
@cURL Required: Yes
@Premium: No
@Options Name: inboundrocket_cm_options
*/
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

//=============================================
// Define Constants
//=============================================

if ( !defined('INBOUNDROCKET_CAMPAIGN_MONITOR_CONNECTOR_PATH') )
    define('INBOUNDROCKET_CAMPAIGN_MONITOR_CONNECTOR_PATH', INBOUNDROCKET_PATH . '/inc/power-ups/campaign-monitor-connector');

if ( !defined('INBOUNDROCKET_CAMPAIGN_MONITOR_CONNECTOR_PLUGIN_DIR') )
    define('INBOUNDROCKET_CAMPAIGN_MONITOR_CONNECTOR_PLUGIN_DIR', INBOUNDROCKET_PLUGIN_DIR . '/inc/power-ups/campaign-monitor-connector');

if ( !defined('INBOUNDROCKET_CAMPAIGN_MONITOR_CONNECT_PLUGIN_SLUG') )
    define('INBOUNDROCKET_CAMPAIGN_MONITOR_CONNECTOR_SLUG', basename(dirname(__FILE__)));

//=============================================
// Include Needed Files
//=============================================
require_once(INBOUNDROCKET_CAMPAIGN_MONITOR_CONNECTOR_PLUGIN_DIR . '/inc/ir_campaign_monitor.php');
require_once(INBOUNDROCKET_CAMPAIGN_MONITOR_CONNECTOR_PLUGIN_DIR . '/admin/campaign-monitor-connector-admin.php');

//=============================================
// WPCampaignMonitorConnector Class
//=============================================
class WPCampaignMonitorConnector extends WPInboundRocket {
    
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

        global $inboundrocket_campaign_monitor_connector_wp;
        $inboundrocket_campaign_monitor_connector_wp = $this;
    }

    public function admin_init ( )
    {
        $this->admin = WPCampaignMonitorConnectorAdmin::init();
        
        add_action( 'admin_footer', array( $this->admin, 'ir_cm_action_javascript' ) );
	    add_action( 'wp_ajax_cm_delete_api_key', array( $this->admin, 'ir_cm_delete_api_key' ) );
	    
	    // Register power up settings
		register_setting('inboundrocket_cm_options', 'inboundrocket_cm_options', array($this->admin, 'sanitize'));
		add_settings_section('ir_cm_section', '', '', 'inboundrocket_cm_options');
		add_settings_field('ir_cm_settings', __('Campaign Monitor API Key','inbound-rocket'), array($this->admin,'ir_cm_input_fields'), 'inboundrocket_cm_options', 'ir_cm_section');
    }

    function power_up_setup_callback ( )
    {
        $this->admin->power_up_setup_callback();
    }

    /**
 @Activate the power-up and add the defaults
     */
    function add_defaults ()
    {

    }

    /**
 @Adds a subcsriber to a specific list
     *
 @@param   array
 @@return  int/bool        API status code OR false if api key not set
     */
    function push_contact_to_list ( $list_parameters = array() ) 
    {
        $this->options = get_option('inboundrocket_cm_options');
        
        if ( isset($this->options['ir_cm_api_key']) && $this->options['ir_cm_api_key'] )
        {
            $cm = new IR_Campaign_Monitor($this->options['ir_cm_api_key']);
            
            $options = array( 
                'EmailAddress' => $list_parameters['email'], 
                'Name' => $list_parameters['first_name'] . ' ' . $list_parameters['last_name'],
                'Resubscribe' => FALSE,
                'RestartSubscriptionBasedAutoResponders' => FALSE
            );
            $r = $cm->call($list_parameters['list_id'], $options);

            if ( $r->http_status_code < 400 )
            {
	            inboundrocket_track_plugin_activity('Contact Added To WP Users', array('esp_connector' => 'campaign_monitor','email' => $list_parameters['email']));
                return TRUE;
            }
            else
            {
	            inboundrocket_track_plugin_activity('Contact NOT Added to WP Users', array('esp_connector' => 'campaign_monitor','email' => $list_parameters['email']));
	            return FALSE;
            }
                
        }

        return FALSE;
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
	        
	    $this->options = get_option('inboundrocket_cm_options');
	    
        if ( isset($this->options['ir_cm_api_key']) && $this->options['ir_cm_api_key'] && $list_id )
        {
            $cm = new IR_Campaign_Monitor($this->options['ir_cm_api_key']);

			$batch_contacts = array();
            
            foreach ( $contacts as $contact )
            {
	            if(in_multiarray(trim($contact->lead_email), $batch_contacts)) continue;
                array_push($batch_contacts, array(
                    'EmailAddress' => trim($contact->lead_email), 
					'Name' => $contact->lead_first_name . ' ' . $contact->lead_last_name
					)
                );
            }
           
			$r = $cm->bulk_call($list_id, $batch_contacts);
			
	        if ( $r->http_status_code < 400 )
	        {
	            inboundrocket_track_plugin_activity('Bulk Contacts Added to WP Users', array('esp_connector' => 'campaign_monitor', 'response' => $r->response));
	            return TRUE;
	        } else {
	            inboundrocket_track_plugin_activity('Bulk Contacts NOT Added to WP Users', array('esp_connector' => 'campaign_monitor', 'response' => $r->response));
	            return FALSE;
	        }
    	}
    
		return FALSE;
	}

}
//=============================================
// Campaign MOnitor Connector Init
//=============================================

global $inboundrocket_campaign_monitor_connector_wp;

?>