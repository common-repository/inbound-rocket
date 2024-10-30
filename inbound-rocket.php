<?php
/*
Plugin Name: Inbound Rocket
Plugin URI: http://wordpress.org/extend/plugins/inbound-rocket/
Description: Introducing a new way of generating traffic and converting them into leads on WordPress. Inbound Rocket is an easy-to-use marketing automation plugin for WordPress. It features visitor activity tracking and the management of incoming leads to better understand your web visitors. It also offers great power-ups to help you get even more visitors and help them convert to leads, subscribers and customers.
Version: 1.5.5
Author: Inbound Rocket
Text Domain: inbound-rocket
Domain Path: /languages/
Author URI: https://inboundrocket.co/
License: GPLv2
*/

//=============================================
// Define Constants
//=============================================

if ( !defined('ABSPATH') ) exit;

if ( !defined('INBOUNDROCKET_PATH') )
    define('INBOUNDROCKET_PATH', untrailingslashit(plugins_url('', __FILE__ )));

if ( !defined('INBOUNDROCKET_PLUGIN_DIR') )
	define('INBOUNDROCKET_PLUGIN_DIR', untrailingslashit(dirname( __FILE__ )));
	
if ( !defined('INBOUNDROCKET_PLUGIN_SLUG') )
	define('INBOUNDROCKET_PLUGIN_SLUG', basename(dirname(__FILE__)));

if ( !defined('INBOUNDROCKET_DB_VERSION') )
	define('INBOUNDROCKET_DB_VERSION', '1.2.3');

if ( !defined('INBOUNDROCKET_PLUGIN_VERSION') )
	define('INBOUNDROCKET_PLUGIN_VERSION', '1.5.5');

if ( !defined('INBOUNDROCKET_ENABLE_DEBUG') )
	define('INBOUNDROCKET_ENABLE_DEBUG', FALSE);
	
if( INBOUNDROCKET_ENABLE_DEBUG ) {
	error_reporting (E_ALL);
	ini_set('display_errors', 1);
}

if ( !defined('INBOUNDROCKET_API') )
    define('INBOUNDROCKET_API', 'api.inboundrocket.co');
	
//=============================================
// Load Translation files
//=============================================    

load_plugin_textdomain( 'inbound-rocket', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );    

//=============================================
// Include Needed Files
//=============================================

// Admin
require_once(INBOUNDROCKET_PLUGIN_DIR . '/admin/inboundrocket-admin.php');

// API Connector
require_once(INBOUNDROCKET_PLUGIN_DIR . '/inc/class-inboundrocket-connector.php');

// General
require_once(INBOUNDROCKET_PLUGIN_DIR . '/inc/inboundrocket-functions.php');
require_once(INBOUNDROCKET_PLUGIN_DIR . '/inc/class-notifier.php');
require_once(INBOUNDROCKET_PLUGIN_DIR . '/inc/inboundrocket-ajax-functions.php');
require_once(INBOUNDROCKET_PLUGIN_DIR . '/inc/class-inboundrocket.php');

$ir_base_url = 'https://www.inboundrocket.co';

//=============================================
// Hooks & Filters
//=============================================

add_action( 'plugins_loaded', create_function( '', '$inboundrocket_wp = new WPInboundRocket;' ) );


if ( is_admin() ) 
{
	// Activate + install Inbound Rocket
	register_activation_hook( __FILE__, 'activate_inboundrocket');

	// Deactivate Inbound Rocket
	register_deactivation_hook( __FILE__, 'deactivate_inboundrocket');

	// Uninstall Inbound Rocket
	register_uninstall_hook(__FILE__, 'uninstall_inboundrocket');
	
	if(is_multisite()){
		// Activate on newly created wpmu blog
		add_action('wpmu_new_blog', 'activate_inboundrocket_on_new_blog', 10, 6);
	}
	
	$options = get_option('inboundrocket_options');
	
	$ir_show_widget = isset($options['ir_enable_dashboard_widget']) ? intval($options['ir_enable_dashboard_widget']) : 0;

	if ($ir_show_widget==1) {
		add_action( 'wp_dashboard_setup', 'ir_add_dashboard_widgets' );
    }
	
	if ( isset($options['ir_db_version']) )
    {
        if (version_compare($options['ir_db_version'], INBOUNDROCKET_DB_VERSION) < 0)
        {
            inboundrocket_db_install();
        }
    }
    
    // Redirect on activation of plugin for the first time for onboarding
	add_action('admin_init', 'inboundrocket_redirect');
}

if ( isset($options['inboundrocket_version']) )
{
    if (version_compare($options['inboundrocket_version'], INBOUNDROCKET_PLUGIN_VERSION) < 0)
    {
        if ( (isset($options['ir_enable_evercookie']) && $options['ir_enable_evercookie']==1) && (isset($options['ir_enable_evercookie_status']) && $options['ir_enable_evercookie_status']=='enabled') ){
        	inboundrocket_install_evercookie();
        	$options['inboundrocket_version'] = INBOUNDROCKET_PLUGIN_VERSION;
        	inboundrocket_update_option('inboundrocket_options', $options);
        }
    } elseif( (isset($options['ir_enable_evercookie']) && $options['ir_enable_evercookie']==1) && (isset($options['ir_enable_evercookie_status']) && $options['ir_enable_evercookie_status']=='enabled') ) {
        inboundrocket_check_evercookie_integrity();
    }
}


/**
* Add a widget to the dashboard if enabled by the user
*
*/
function ir_add_dashboard_widgets()
{
	global $wpdb;
	
	$options = get_option('inboundrocket_visitor_options');
	
	if ( !current_user_can('activate_plugins') || !isset($options['ir_enable_dashboard_widget']) ) {
        return;
    }
    
    $stats = new IR_StatsDashboard();

    $ir_custom_dashboard_widgets = array(
        'ir-lead-stats' => array(
            'title' => 'Inbound Rocket Lead Statistics',
            'callback' => array($stats,'display_lead_report_widget')
        )
    );

    foreach ($ir_custom_dashboard_widgets as $widget_id => $widget_options) {
        wp_add_dashboard_widget(
            $widget_id,
            $widget_options['title'],
            $widget_options['callback']
        );
    }
}

/**
* Remove widget on the dashboard if disabled by the user
*
*/
function ir_remove_dashboard_widget()
{
 	remove_meta_box('ir-lead-stats', 'dashboard', 'side');
}

/**
 * Activate the plugin
 */
function activate_inboundrocket($network_wide)
{
	if ( ! current_user_can( 'activate_plugins' ) )
        return;
        
	// Check activation on entire network or one blog
	if ( is_multisite() && $network_wide ) 
	{ 
		global $wpdb;
 
		// Get this so we can switch back to it later
		$current_blog = $wpdb->blogid;
		// For storing the list of activated blogs
		$activated = array();
 
		// Get all blogs in the network and activate plugin on each one
		$q = "SELECT blog_id FROM {$wpdb->blogs}";
		$blog_ids = $wpdb->get_col($q);
		foreach ( $blog_ids as $blog_id ) 
		{
			switch_to_blog($blog_id);
			add_inboundrocket_defaults();
			$activated[] = $blog_id;
			inboundrocket_track_plugin_registration_hook(TRUE);
		}
 
		// Store the array for a later function
		update_site_option('inboundrocket_activated', $activated);
		add_option('inboundrocket_do_onboard_redirect', TRUE);
	}
	else
	{
		inboundrocket_track_plugin_registration_hook(TRUE);
		add_inboundrocket_defaults();
		add_option('inboundrocket_do_onboard_redirect', TRUE);
	}
}

function inboundrocket_redirect()
{
	if (get_option('inboundrocket_do_onboard_redirect')) {
        delete_option('inboundrocket_do_onboard_redirect');
		wp_redirect(admin_url('admin.php?page=inboundrocket_settings'));
		exit;
	}
}

/**
 * Check inboundrocket installation and set options
 */
function add_inboundrocket_defaults()
{
	global $wpdb;
		
	$options = get_option('inboundrocket_options');
	
	$email = get_bloginfo('admin_email');

	if ( !isset($options['ir_installed']) || $options['ir_installed'] != 1 || !is_array($options) )
	{
		$visitor_opt = array(
		  'ir_enable_evercookie'			=> 0,
		  'ir_email' 						=> $email,
		  'ir_enable_dashboard_widget'		=> 1,
		  'ir_auto_cleanup_days'			=> 0,
		  'ir_auto_cleanup_visits'			=> 0,
		  'ir_updates_subscription'			=> 0
		);
		
		$opt = array(			
			'ir_installed'					=> 1,
			'ir_enable_evercookie_status'	=>'disabled',
			'ir_evercookie_corrupted'		=> 0,
			'inboundrocket_version'			=> INBOUNDROCKET_PLUGIN_VERSION,
			'ir_db_version'					=> INBOUNDROCKET_DB_VERSION,
			'onboarding_step'				=> 1,
			'onboarding_complete'			=> 0,
			'converted_to_tags'				=> 0,
			'premium'						=> 0			
		);
		
		$email_opt = array(
			'ir_emails_welcome_send'	=> 0,
			'ir_emails_welcome_subject' => '',
			'ir_emails_welcome_content' => '',
			'ir_emails_welcome_type'	=> 'none',
			'ir_emails_welcome_from'	=> '',
			'ir_emails_welcome_name'	=> '',
			'ir_emails_welcome_return'	=> 0,
		);
		
		if ( is_multisite() )
		{
			update_site_option( 'inboundrocket_options', $opt );
			update_site_option( 'inboundrocket_visitor_options', $visitor_opt );
			update_site_option( 'inboundrocket_email_options', $email_opt );
		} else {
			inboundrocket_update_option( 'inboundrocket_options', $opt );	
			inboundrocket_update_option( 'inboundrocket_visitor_options', $visitor_opt );
			inboundrocket_update_option( 'inboundrocket_email_options', $email_opt );
		}
		
		inboundrocket_mailchimp_install($email); // Install mailchimp flags for user
        inboundrocket_db_install();
 			
		$wpdb->query("INSERT INTO {$wpdb->ir_tags}
		        ( tag_text, tag_slug, tag_form_selectors, tag_synced_lists, tag_order ) 
		    VALUES 
		    	('Leads', 'leads', '', '', 1),
		        ('Contacted', 'contacted', '', '', 2),
		        ('Customers', 'customers', '', '', 3),
		        ('Ambassadors', 'ambassadors', '', '', 4),
		        ('Commenters', 'commenters', '#commentform', '', 5),
		        ('Subscribers', 'subscribers', '#welcome_bar-form', '', 6)", "");
	} else {
		
		inboundrocket_db_install();
		inboundrocket_update_user();
	}
	
	$inboundrocket_active_power_ups = get_option('inboundrocket_active_power_ups');

	if ( !$inboundrocket_active_power_ups )
	{
		$auto_activate = array(
			'contacts'
		);
		inboundrocket_update_option('inboundrocket_active_power_ups', serialize($auto_activate));
	}
}

/**
 * Deactivate inboundrocket plugin hook
 */
function deactivate_inboundrocket( $network_wide )
{
	$inboundrocket_user = inboundrocket_get_current_user();
	
	if ( is_multisite() && $network_wide ) 
	{ 
		global $wpdb;
 
		// Get this so we can switch back to it later
		$current_blog = $wpdb->blogid;
 
		// Get all blogs in the network and activate plugin on each one
		$q = "SELECT blog_id FROM {$wpdb->blogs}";
		$blog_ids = $wpdb->get_col($q);
		foreach ( $blog_ids as $blog_id ) 
		{
			switch_to_blog($blog_id);
			inboundrocket_track_plugin_registration_hook(FALSE);
		}
		inboundrocket_mailchimp_deactivate(); // Deactivate mailchimp flag for user
 
		// Switch back to the current blog
		switch_to_blog($current_blog);
	}
	else
		inboundrocket_track_plugin_registration_hook(FALSE);
		inboundrocket_mailchimp_deactivate(); // Deactivate mailchimp flag for user
		
}

function activate_inboundrocket_on_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta )
{
	global $wpdb;
	
	if ( is_plugin_active_for_network(INBOUNDROCKET_PATH.'/inbound-rocket.php') )
	{
		$current_blog = $wpdb->blogid;
		switch_to_blog($blog_id);
		
		add_inboundrocket_defaults();
		
		switch_to_blog($current_blog);
	}
	
	
}

//=============================================
// Database functions
//=============================================

/**
 * Drops or uninstalls the inboundrocket tables
 */
function uninstall_inboundrocket()
{	
	if ( ! current_user_can( 'activate_plugins' ) )
        return;
        	    
	global $wp_meta_boxes;
		
	$email_options = get_option('inboundrocket_visitor_options');
	$email = isset($email_options['ir_email']) ? $email_options['ir_email'] : get_bloginfo('admin_email');
	
	inboundrocket_mark_deleted_user($email);
	
	unset($wp_meta_boxes['dashboard']['side']['core']['ir-lead-stats']);
	
	// Delete Plugin Options
	if(is_multisite()){
		delete_site_option( 'inboundrocket_ctt_options' );
		delete_site_option( 'inboundrocket_is_options' );
		delete_site_option( 'inboundrocket_ss_options' );
		delete_site_option( 'inboundrocket_wb_options' );
		delete_site_option( 'inboundrocket_ei_options' );
		delete_site_option( 'inboundrocket_sb_options' );
		delete_site_option( 'inboundrocket_wm_options' );
		delete_site_option( 'inboundrocket_mc_options' );
		delete_site_option( 'inboundrocket_aw_options' );
		delete_site_option( 'inboundrocket_cm_options' );
		delete_site_option( 'inboundrocket_pm_options' );
		delete_site_option( 'inboundrocket_gdpr_options' );
		delete_site_option( 'inboundrocket_fb_options' );
		delete_site_option( 'inboundrocket_active_power_ups' );
	} else {
		delete_option( 'inboundrocket_ss_options' );
		delete_option( 'inboundrocket_ctt_options' );
		delete_option( 'inboundrocket_is_options' );
		delete_option( 'inboundrocket_wb_options' );
		delete_option( 'inboundrocket_ei_options' );
		delete_option( 'inboundrocket_sb_options' );
		delete_option( 'inboundrocket_wm_options' );		
		delete_option( 'inboundrocket_mc_options' );
		delete_option( 'inboundrocket_aw_options' );
		delete_option( 'inboundrocket_cm_options' );
		delete_option( 'inboundrocket_pm_options' );
		delete_option( 'inboundrocket_gdpr_options' );
		delete_option( 'inboundrocket_fb_options' );
		delete_option( 'inboundrocket_active_power_ups' );
	}
	
	// Unregister Settings
	unregister_setting('inboundrocket_visitor_options','inboundrocket_visitor_options');
	unregister_setting('inboundrocket_email_options','inboundrocket_email_options');
	unregister_setting('inboundrocket_options','inboundrocket_options');
	
	inboundrocket_unset_wpdb_tables();

	inboundrocket_track_plugin_activity("Plugin Uninstalled");
}

/**
 * Creates or updates the inboundrocket tables
 */
function inboundrocket_db_install()
{
	global $wpdb;
		
	$charset_collate = $wpdb->get_charset_collate();
		
	inboundrocket_set_wpdb_tables();
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
	$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->ir_leads} (
		  `lead_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `lead_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `hashkey` varchar(16) DEFAULT NULL,
		  `lead_ip` varchar(40) DEFAULT NULL,
		  `lead_source` text,
		  `lead_email` varchar(255) DEFAULT NULL,
		  `lead_first_name` varchar(255) NOT NULL,
  		  `lead_last_name` varchar(255) NOT NULL,
  		  `lead_phone` varchar(30) NOT NULL,
		  `lead_status` set('leads','contact','contacted','customers','ambassadors','commenters','subscribers') NOT NULL DEFAULT 'leads',
		  `merged_hashkeys` text,
		  `lead_deleted` int(1) NOT NULL DEFAULT '0',
		  `blog_id` int(11) unsigned NOT NULL,
		  `company_data` mediumtext NOT NULL,
  		  `social_data` mediumtext NOT NULL,
		  PRIMARY KEY (`lead_id`),
		  KEY `hashkey` (`hashkey`)
		) {$charset_collate};";
	dbDelta($sql);
	$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->ir_pageviews} (
		  `pageview_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `pageview_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `lead_hashkey` varchar(16) NOT NULL,
		  `pageview_title` varchar(255) NOT NULL,
		  `pageview_url` text NOT NULL,
		  `pageview_source` text NOT NULL,
		  `pageview_session_start` int(1) NOT NULL,
		  `pageview_deleted` int(1) NOT NULL DEFAULT '0',
		  `blog_id` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`pageview_id`),
		  KEY `lead_hashkey` (`lead_hashkey`)
		) {$charset_collate};";
	dbDelta($sql);
	$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->ir_submissions} (
		  `form_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `form_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `lead_hashkey` varchar(16) NOT NULL,
		  `form_page_title` varchar(255) NOT NULL,
		  `form_page_url` text NOT NULL,
		  `form_fields` text NOT NULL,
		  `form_selector_id` mediumtext NOT NULL,
		  `form_selector_classes` mediumtext NOT NULL,
		  `form_hashkey` varchar(16) NOT NULL,
		  `form_deleted` int(1) NOT NULL DEFAULT '0',
		  `blog_id` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`form_id`),
		  KEY `lead_hashkey` (`lead_hashkey`)
		) {$charset_collate};";
	dbDelta($sql);
	$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->ir_shares} (
		  `share_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `share_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `lead_hashkey` varchar(16) NOT NULL,
		  `share_type` set('contact','ss-twitter-text','is-twitter-image','click-to-tweet','ss-email-text','ss-email-image','ss-facebook-text','is-facebook-image','ss-linkedin-text','is-pinterest-image') NOT NULL,
		  `share_deleted` int(1) NOT NULL DEFAULT '0',
		  `share` text NOT NULL,
		  `post_id` int(11) NOT NULL,
		  `blog_id` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`share_id`),
		  KEY `lead_hashkey` (`lead_hashkey`)
		) {$charset_collate};";
	dbDelta($sql);
   	$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->ir_tags} (
		  `tag_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `tag_text` varchar(255) NOT NULL,
		  `tag_slug` varchar(255) NOT NULL,
		  `tag_form_selectors` mediumtext NOT NULL,
		  `tag_synced_lists` mediumtext NOT NULL,
		  `tag_order` int(11) unsigned NOT NULL,
		  `blog_id` int(11) unsigned NOT NULL,
		  `tag_deleted` int(1) NOT NULL,
		  PRIMARY KEY (`tag_id`)
		) {$charset_collate};";
	dbDelta($sql);
	$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->ir_tag_relationships} (
		  `tag_relationship_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `tag_id` int(11) unsigned NOT NULL,
		  `contact_hashkey` varchar(16) NOT NULL,
  		  `form_hashkey` varchar(16) NOT NULL,
		  `tag_relationship_deleted` int(1) unsigned NOT NULL,
		  `blog_id` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`tag_relationship_id`)
		) {$charset_collate};";
	dbDelta($sql);
	$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->ir_emails} (
		  `email_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `email_subject` varchar(255) NOT NULL,
		  `email_text_content` varchar(255) NOT NULL,
		  `email_html_content` mediumtext NOT NULL,
		  `email_deleted` int(1) NOT NULL,
		  PRIMARY KEY (`email_id`)
		) {$charset_collate};";
	dbDelta($sql);
	$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->ir_forms} (
		  `form_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `form_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `form_page_title` varchar(255) NOT NULL,
		  `form_page_url` text NOT NULL,
		  `form_fields` text NOT NULL,
		  `form_selector_id` mediumtext NOT NULL,
		  `form_selector_classes` mediumtext NOT NULL,
		  `form_hashkey` varchar(16) NOT NULL,
		  `form_deleted` int(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`form_id`),
		  KEY `lead_hashkey` (`lead_hashkey`)
		) {$charset_collate};";
	dbDelta($sql);

	$options = get_option('inboundrocket_options');
    $options['ir_db_version'] = INBOUNDROCKET_DB_VERSION;
	inboundrocket_update_option('inboundrocket_options', $options);
    
	inboundrocket_track_plugin_activity("Databases Updated");
}

/**
 * Sets the wpdb tables to the current blog
 * 
 */
function inboundrocket_set_wpdb_tables()
{
    global $wpdb;
    
    $wpdb->ir_leads       				= $wpdb->prefix . 'ir_leads';
    $wpdb->ir_pageviews       			= $wpdb->prefix . 'ir_pageviews';
    $wpdb->ir_submissions       		= $wpdb->prefix . 'ir_submissions';
    $wpdb->ir_shares       				= $wpdb->prefix . 'ir_shares'; 
    $wpdb->ir_tags 						= $wpdb->prefix . 'ir_tags';
    $wpdb->ir_tag_relationships			= $wpdb->prefix . 'ir_tag_relationships';
    $wpdb->ir_emails					= $wpdb->prefix . 'ir_emails';
}

/**
 * UnSets the wpdb tables to the current blog
 * 
 */
function inboundrocket_unset_wpdb_tables()
{
    global $wpdb;
    
    unset($wpdb->ir_leads);
    unset($wpdb->ir_pageviews);
    unset($wpdb->ir_submissions);
    unset($wpdb->ir_shares);
    unset($wpdb->ir_tags);
    unset($wpdb->ir_tag_relationships);
    unset($wpdb->ir_emails);
}
?>