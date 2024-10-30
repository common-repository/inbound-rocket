<?php
if ( !defined('ABSPATH') ) exit;

if ( !defined('INBOUNDROCKET_PLUGIN_VERSION') ) 
{
    header('HTTP/1.0 404 Not Found');
    die;
}

//=============================================
// Define Constants
//=============================================

if ( !defined('INBOUNDROCKET_ADMIN_PATH') )
    define('INBOUNDROCKET_ADMIN_PATH', untrailingslashit(__FILE__));
    
//=============================================
// Include Needed Files
//=============================================

require_once INBOUNDROCKET_PLUGIN_DIR . '/inc/inboundrocket-functions.php';

if ( !class_exists('IR_StatsDashboard') )
	require_once INBOUNDROCKET_PLUGIN_DIR . '/admin/inc/class-stats-dashboard.php';
	
if ( !class_exists('IR_List_Table') )
    require_once INBOUNDROCKET_PLUGIN_DIR . '/admin/inc/class-inboundrocket-list-table.php';
    
if ( !class_exists('IR_Contact') )
    require_once INBOUNDROCKET_PLUGIN_DIR . '/admin/inc/class-inboundrocket-contact.php';  
    
if ( !class_exists('IR_Lead_List_Table') )
    require_once INBOUNDROCKET_PLUGIN_DIR . '/admin/inc/class-inboundrocket-lead-list-table.php';

if ( !class_exists('IR_Lead_List_Editor') )
    require_once INBOUNDROCKET_PLUGIN_DIR . '/admin/inc/class-inboundrocket-lead-list-editor.php';

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

//=============================================
// InboundRocketAdmin Class
//=============================================
class WPInboundRocketAdmin {
	
	var $admin_power_ups;
	var $power_up_icon;
	var $stats_dashboard;
	var $action;
	var $esp_power_ups;
	
	public $activated_power_up_hooks = array();
	
	var $kses_html = array(
	    'a' => array(
	        'href' => array(),
	        'title' => array(),
	        'rel' => array(),
	        'class' => array(),
	        'id' => array()
	    ),
	    'b' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'blockquote' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'br' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'dd' => array(
		    'class' => array()
	    ),
	    'del' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'div' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'dl' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'dt' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'em' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'font' => array(
		    'size' => array(),
		    'face' => array(),
		    'class' => array(),
		    'id' => array()
	    ),
	    'h1' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'h2' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'h3' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'h4' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'h5' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'h6' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'hr' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'i' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'img' => array(
		    'title' => array(),
		    'src' => array(),
		    'alt' => array(),
		    'width' => array(),
		    'height' => array(),
		    'class' => array(),
		    'id' => array()
	    ),
	    'li' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'ol' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'p' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'center' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'pre' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'small' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'span' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'strong' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'sub' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'sup' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'table' => array(
		    'width' => array(),
		    'height' => array(),
		    'border' => array(),
		    'cellpadding' => array(),
		    'cellspacing' => array(),
		    'class' => array(),
		    'id' => array()
	    ),
	    'td' => array(
		    'class' => array()
	    ),
	    'th' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'thead' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'tr' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'tt' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'u' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'ul' => array(
		    'class' => array(),
		    'id' => array()
	    ),
	    'form' => array(
		    'name' => array(),
		  	'class' => array(),
		  	'id' => array(),
		  	'action' => array(),
		  	'enctype' => array(),
		  	'method' => array(),
		  	'target' => array()
	    ),
	    'input' => array(
		    'name' => array(),
		    'type' => array(),
		    'value' => array(),
		    'checked' => array(),
		    'placeholder' => array(),
		    'size' => array(),
		    'src' => array(),
		    'id' => array(),
		    'required' => array(),
			'class' => array()  
	    ),
	    'textarea' => array(
		    'id' => array(),
		    'name' => array(),
		    'class' => array(),
		    'required' => array(),
		    'maxlength' => array(),
		    'cols' => array(),
		    'rows' => array(),
		    'placeholder' => array()
	    ),
	    'select' => array(
		    'name' => array(),
		    'id' => array(),
			'class' => array()  
	    ),
	    'option' => array(
		    'value' => array(),
			'class' => array()  
	    ),
	    'button' => array(
		    'type' => array(),
		    'value' => array(),
		    'id' => array(),
			'class' => array()  
	    ),
	);
	public $protocals = array('http','https');
	
    protected $plugin_settings_tabs = array();
	
    /**
     * Class constructor
     */
    function __construct( $power_ups )
    {
        //=============================================
        // Hooks & Filters
        //=============================================
        $this->action = $this->inboundrocket_current_action();
        
        $this->admin_power_ups = $power_ups;
        
        $this->esp_power_ups = array(
	        'MailChimp'         => 'mailchimp_connector', 
	        'Constant Contact'  => 'constant_contact_connector', 
	        'AWeber'            => 'aweber_connector', 
	        'GetResponse'       => 'getresponse_connector', 
	        'MailPoet'          => 'mailpoet_connector', 
	        'Campaign Monitor'  => 'campaign_monitor_connector',
	        'Postmatic' 		=> 'postmatic_connector'
	    );
        
        if(is_admin()):
        
       	    add_action('admin_menu', array(&$this, 'inboundrocket_add_menu_items'));
        	add_action('admin_init', array(&$this, 'inboundrocket_settings_page'));
        	
        	add_action( 'wp_ajax_send_gdpr_info', array(&$this, 'inboundrocket_send_gdpr_info' ) );
        	
        	add_action('admin_print_styles', array(&$this, 'add_inboundrocket_admin_styles'));
			add_action('admin_enqueue_scripts', array(&$this, 'add_inboundrocket_admin_scripts'));
			
			add_filter('plugin_action_links', array(&$this, 'inboundrocket_plugin_settings_link'), 10, 2);
			add_filter('wp_privacy_personal_data_exporters', array(&$this, 'inboundrocket_register_exporter'), 10);
			
			if ( isset($_GET['page']) && $_GET['page'] === 'inboundrocket_stats' ) {
	            add_action('admin_footer', array(&$this, 'build_contacts_chart'));
	        }
        endif;
 	}	
 	
	/**
	 * Register the GDPR user data export hook
	 *
	 * @param object
	 *
	 */
	 
	function inboundrocket_register_exporter( $exporters ) {
	  $exporters['inboundrocket'] = array(
		'exporter_friendly_name' => __( 'Inbound Rocket Plugin' ),
		'callback' => array(&$this,'inboundrocket_export_data'),
	  );
	  return $exporters;
	}
	
	/**
	 * GDPR User Data Export
	 *
	 * @param   string
	 * @param   int
	 * @return  array 
	 */
	function inboundrocket_export_data( $email_address, $page = 1 ) {
	  global $wpdb;
	 
	  $export_items = array();
	 
	  $user = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT * FROM {$wpdb->ir_leads} WHERE email = '%s';",
							$email_address
						)
					);
	  if(!empty($user)) {
		  $user_pageviews = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT * FROM {$wpdb->ir_pageviews} WHERE lead_hashkey = '%s'",
									$user->hashkey
								)
							);
							
		  $user_submissions = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT * FROM {$wpdb->ir_submissions} WHERE lead_hashkey = '%s'",
								$user->hashkey
							)
						);
	 
	 
		  $item_id = "inboundrocket-data-{$user->id}";
	 
		  // Core group IDs include 'comments', 'posts', etc.
		  // But you can add your own group IDs as needed
		  $group_id = 'inboundrocket';
	 
		  // Optional group label. Core provides these for core groups.
		  // If you define your own group, the first exporter to
		  // include a label will be used as the group label in the
		  // final exported report
		  $group_label = __( 'InboundRocket Data Export' );
	 
		  // Plugins can add as many items in the item data array as they want
		  $data = array(
			array(
			  'name' => __( 'User Data' ),
			  'value' => $user
			),
			array(
			  'name' => __( 'User Pageviews' ),
			  'value' => $user_pageviews
			),
			array(
			  'name' => __( 'User Submissions' ),
			  'value' => $user_submissions
			)
		  );
	 
		  $export_items[] = array(
			'group_id' => $group_id,
			'group_label' => $group_label,
			'item_id' => $item_id,
			'data' => $data,
		  );
	  }
	  $done = true;
	  return array(
		'data' => $export_items,
		'done' => $done,
	  );
	}
	
	//=============================================
    // Menus
    //=============================================
    /**
     * Adds Inbound Rocket menu to /wp-admin sidebar
     */
    function inboundrocket_add_menu_items()
    {
        $options = get_option('inboundrocket_visitor_options');

        global $submenu;
        global $wp_version;
        
        $capability = 'activate_plugins';
        if ( ! current_user_can('activate_plugins') )
        {
            if ( ! array_key_exists('ir_grant_access_to_' . inboundrocket_get_user_role(), $options ) )
                return FALSE;
            else
            {
                if ( current_user_can('manage_network') ) // super admin
                    $capability = 'manage_network';
                else if ( current_user_can('edit_pages') ) // editor
                    $capability = 'edit_pages';
                else if ( current_user_can('publish_posts') ) // author
                    $capability = 'publish_posts';
                else if ( current_user_can('edit_posts') ) // contributor
                    $capability = 'edit_posts';
                else if ( current_user_can('read') ) // subscriber
                    $capability = 'read';
            }
        }
        
        self::check_admin_action();
        
        if ( ini_get('allow_url_fopen') )
            $inboundrocket_icon = ($wp_version < 3.8 && !is_plugin_active('mp6/mp6.php') ? INBOUNDROCKET_PATH . '/img/inboundrocket-icon-32x32.png' : 'data:image/svg+xml;base64,' . base64_encode(@file_get_contents(INBOUNDROCKET_PATH . '/img/inboundrocket-svg-icon.svg')));
        else
        {

            $inboundrocket_icon = INBOUNDROCKET_PATH . '/img/inboundrocket-icon-32x32.png';
        }
		
		add_menu_page('Inbound Rocket', 'Inbound Rocket', $capability, 'inboundrocket_stats', array($this, 'inboundrocket_build_stats_page'),  $inboundrocket_icon , '25.10167');
		
		foreach ( $this->admin_power_ups as $power_up )
        {
            if ( $power_up->activated )
            {
                // Creates the menu icon for power-up if it's set. Overrides the main Inbound Rocket menu to hit the contacts power-up
                if ( $power_up->menu_text )
                    add_submenu_page('inboundrocket_stats', $power_up->menu_text, $power_up->menu_text, $capability, 'inboundrocket_' . $power_up->menu_link, array($power_up, 'power_up_setup_callback'));   
            }
        }
        

		add_submenu_page('inboundrocket_stats', __('Lead Lists', 'inbound-rocket'), __('Lead Lists', 'inbound-rocket'), $capability, 'inboundrocket_lead_lists', array(&$this, 'inboundrocket_build_lead_list_page'));
        add_submenu_page('inboundrocket_stats', __('Settings', 'inbound-rocket'), __('Settings', 'inbound-rocket'), 'activate_plugins', 'inboundrocket_settings', array(&$this, 'inboundrocket_plugin_options'));
        add_submenu_page('inboundrocket_stats', __('Power-ups', 'inbound-rocket'), __('Power-ups', 'inbound-rocket'), 'activate_plugins', 'inboundrocket_power_ups', array(&$this, 'inboundrocket_power_ups_page'));
        
		if ( ! inboundrocket_check_premium_user() )
			add_submenu_page('inboundrocket_stats', __('Upgrade to Premium', 'inbound-rocket'), __('Upgrade to Premium', 'inbound-rocket'), 'activate_plugins', 'inboundrocket_premium_upgrade', array(&$this, 'inboundrocket_premium_upgrade_page'));
        
        $submenu['inboundrocket_stats'][0][0] = __('Stats', 'inbound-rocket');
        
    }    
    
    //=============================================
    // Settings Page
    //=============================================

    /**
     * Adds setting link for Inbound Rocket to plugins management page 
     *
     * @param   array $links
     * @return  array
     */
    function inboundrocket_plugin_settings_link ( $links, $plugin_file )
    {
	    static $plugin;
		
		if (!isset($plugin)) $plugin = INBOUNDROCKET_PLUGIN_SLUG.'/'.INBOUNDROCKET_PLUGIN_SLUG.'.php';
		
		if ($plugin == $plugin_file) {
			$settings_link = '<a href="' . admin_url('admin.php?page=inboundrocket_settings&tab=inboundrocket_visitor_options') . '">' . __('Settings', 'inbound-rocket') . '</a>';
			array_unshift($links, $settings_link);
		}
        return $links;
    }    
     
     /**
     * Creates the stats page
     */
    function inboundrocket_build_stats_page ()
    {
        global $wp_version;
        
        $this->stats_dashboard = new IR_StatsDashboard();
        
        inboundrocket_track_plugin_activity("Loaded Stats Page");

        echo '<div id="inboundrocket" class="ir-stats wrap '. ( $wp_version < 3.8 && !is_plugin_active('mp6/mp6.php') ? 'pre-mp6' : ''). '">';
        
        	$this->inboundrocket_header('' . __('Inbound Rocket Stats', 'inbound-rocket') . ': ' . date('F j Y, g:ia', current_time('timestamp')), 'inboundrocket-stats__header');
        
			echo '<div class="inboundrocket-stats__top-container">';
            	echo $this->inboundrocket_postbox('inboundrocket-stats__chart', inboundrocket_single_plural_label(number_format($this->stats_dashboard->total_contacts_last_30_days), __('new contact', 'inbound-rocket'), __('new contacts', 'inbound-rocket')) . ' ' . __('last 30 days', 'inbound-rocket') . '', $this->inboundrocket_build_contacts_chart_stats());
			echo '</div>';

			echo '<div class="inboundrocket-stats__postbox_container">';
            	echo $this->inboundrocket_postbox('inboundrocket-stats__new-contacts', inboundrocket_single_plural_label(number_format($this->stats_dashboard->total_new_contacts), __('new contact', 'inbound-rocket'), __('new contacts', 'inbound-rocket')) . ' ' . __('today', 'inbound-rocket') . '', $this->inboundrocket_build_new_contacts_postbox());
				echo $this->inboundrocket_postbox('inboundrocket-stats__returning-contacts', inboundrocket_single_plural_label(number_format($this->stats_dashboard->total_returning_contacts), __('returning contact', 'inbound-rocket'), __('returning contacts', 'inbound-rocket')) . ' ' . __('today', 'inbound-rocket') . '', $this->inboundrocket_build_returning_contacts_postbox());
				echo $this->inboundrocket_postbox('inboundrocket-stats__most-popular-pages', __('Most popular pages today', 'inbound-rocket'), $this->inboundrocket_build_most_popular_pages_postbox());
			echo '</div>';

			echo '<div class="inboundrocket-stats__postbox_container">';
            	echo $this->inboundrocket_postbox('inboundrocket-stats__new-sources', __('New contact sources last 30 days', 'inbound-rocket'), $this->inboundrocket_build_sources_postbox());
				echo $this->inboundrocket_postbox('inboundrocket-stats__new-shares', __('New shares last 30 days', 'inbound-rocket'), $this->inboundrocket_build_new_shares_postbox());
				echo $this->inboundrocket_postbox('inboundrocket-stats__most-popular-referral', __('Top 5 referral sources', 'inbound-rocket'), $this->inboundrocket_build_most_popular_referrers_postbox());
			echo '</div>';
			echo '<div style="clear:both"></div>';
        $this->inboundrocket_footer();
        
        echo '</div>';
        
    }
    
    /*
	* Valid Email Address filter_var
	*/
	private function get_valid_email( $email ) {
  		return filter_var( $email, FILTER_VALIDATE_EMAIL );
	}
    
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize ( $input )
    {
        $new_input = array();
        
        // Settings
        
        if( isset( $input['premium'] ) )
            $new_input['premium'] = sanitize_text_field( $input['premium'] );
            
        if( isset( $input['ir_enable_dashboard_widget'] ) )
            $new_input['ir_enable_dashboard_widget'] = intval( $input['ir_enable_dashboard_widget'] );
		
		if( isset( $input['ir_auto_cleanup_visits'] ) )
			$new_input['ir_auto_cleanup_visits'] = intval( $input['ir_auto_cleanup_visits'] );
		
        if( isset( $input['ir_auto_cleanup_days'] ) )
            $new_input['ir_auto_cleanup_days'] = intval( $input['ir_auto_cleanup_days'] );
        
        if( isset( $input['ir_email'] ) && !empty($input['ir_email']) )
            
            $new_email_arr = array();
            if(isset($input['ir_email']) && strpos($input['ir_email'], ",")===true):
	            $emails_arr = explode( ",", sanitize_text_field($input['ir_email']) );
	            foreach($emails_arr as $email){
		           if($this->get_valid_email( $email )){
		            	$new_email_arr[] = $email;
		           } 
	            }
            endif;
            
            if(isset($new_email_arr[0])){
	            $new_input['ir_email'] = implode(',', $new_email_arr);
            } else {
	            $new_input['ir_email'] = isset($input['ir_email']) ? sanitize_text_field($input['ir_email']) : '';
            }
            
            
        if( isset( $input['ir_installed'] ) )
            $new_input['ir_installed'] = intval( $input['ir_installed'] );

        if( isset( $input['ir_db_version'] ) )
            $new_input['ir_db_version'] = sanitize_text_field( $input['ir_db_version'] );

        if( isset( $input['onboarding_step'] ) ) {
            $new_input['onboarding_step'] = intval( $input['onboarding_step'] );
            
			if($new_input['onboarding_step']==2){
	        	inboundrocket_register_user();
        	}
        
        }
        
        if( isset( $input['ir_evercookie_corrupted'] ) )
        	$new_input['ir_evercookie_corrupted'] = intval( $input['ir_evercookie_corrupted'] );
        	
        if( isset( $input['ir_enable_evercookie_status'] ) )
        	$new_input['ir_enable_evercookie_status'] = sanitize_text_field( $input['ir_enable_evercookie_status'] );
        
        if( isset( $input['ir_enable_evercookie'] ) )
	        $new_input['ir_enable_evercookie'] = intval( $input['ir_enable_evercookie'] );

        if( isset( $input['onboarding_complete'] ) )
            $new_input['onboarding_complete'] = intval( $input['onboarding_complete'] );
        
        if(isset( $new_input['onboarding_complete'] ) && $new_input['onboarding_complete']=='1'){
	        if(isset( $new_input['onboarding_complete'] ) && isset( $new_input['ir_enable_evercookie'] ) && $new_input['ir_enable_evercookie']=='1'){
				inboundrocket_install_evercookie();
	        } else {
		        inboundrocket_remove_evercookie();
	        }
        }

		if( isset( $input['converted_to_tags'] ) )
            $new_input['converted_to_tags'] = sanitize_text_field( $input['converted_to_tags'] );

        if( isset( $input['inboundrocket_version'] ) )
            $new_input['inboundrocket_version'] = sanitize_text_field( $input['inboundrocket_version'] );

        if( isset( $input['ir_updates_subscription'] ) )
            $new_input['ir_updates_subscription'] = sanitize_text_field( $input['ir_updates_subscription'] );
		
        if( isset( $input['beta_tester'] ) )
        {
            $new_input['beta_tester'] = sanitize_text_field( $input['beta_tester'] );
            inboundrocket_set_beta_tester_property(TRUE);
        }
        else
            inboundrocket_set_beta_tester_property(FALSE);

        $user_roles = get_editable_roles();
        if ( count($user_roles) )
        {
            foreach ( $user_roles as $key => $role )
            {
                $role_id_tracking = 'ir_do_not_track_' . $key;
                $role_id_access = 'ir_grant_access_to_' . $key;

                if ( isset( $input[$role_id_tracking] ) )
                    $new_input[$role_id_tracking] = sanitize_text_field( $input[$role_id_tracking] );

                if ( isset( $input[$role_id_access] ) )
                    $new_input[$role_id_access] = sanitize_text_field( $input[$role_id_access] );
            }
        }
      	
      	// Emails
        if( isset( $input['ir_emails_welcome_send'] ) )
            $new_input['ir_emails_welcome_send'] = intval( $input['ir_emails_welcome_send'] );
        
        if( isset( $input['ir_emails_welcome_return'] ) )
            $new_input['ir_emails_welcome_return'] = intval( $input['ir_emails_welcome_return'] );
        
        if( isset( $input['ir_emails_welcome_from'] ) )
            $new_input['ir_emails_welcome_from'] = sanitize_text_field( $input['ir_emails_welcome_from'] );
        
        if( isset( $input['ir_emails_welcome_name'] ) )
            $new_input['ir_emails_welcome_name'] = sanitize_text_field( $input['ir_emails_welcome_name'] );
        
        if( isset( $input['ir_emails_welcome_subject'] ) )
            $new_input['ir_emails_welcome_subject'] = sanitize_text_field( $input['ir_emails_welcome_subject'] );
        
        if( isset( $input['ir_emails_welcome_content'] ) )
            $new_input['ir_emails_welcome_content'] = wp_kses( $input['ir_emails_welcome_content'], $this->kses_html, $this->protocals );                  		
        
        if( isset( $input['ir_emails_welcome_type'] ) )
            $new_input['ir_emails_welcome_type'] = sanitize_text_field( $input['ir_emails_welcome_type'] );                  		
        
           
        return $new_input;
    }   
 
	/**
     * Creates the lead list page
    */
    function inboundrocket_build_lead_list_page ()
    {
        global $wp_version;
                
		if ( isset($_POST['tag_name']) )
        {
            $lead_list_id = ( isset($_POST['lead_list_id']) ? absint($_POST['lead_list_id']) : FALSE );
            $tagger = new IR_Lead_List_Editor($lead_list_id);
            
            $tag_name           = sanitize_text_field($_POST['tag_name']);
            $tag_form_selectors = '';
            $tag_synced_lists   = array();

            foreach ( $_POST as $name => $value )
            {
                // Create a comma deliniated list of selectors for tag_form_selectors
                if ( strstr($name, 'email_form_tags_') )
                {
                    $tag_selector = '';
                    if ( strstr($name, '_class') )
                        $tag_selector = str_replace('email_form_tags_class_', '.', $name);
                    else if ( strstr($name, '_id') )
                        $tag_selector = str_replace('email_form_tags_id_', '#', $name);

                    if ( $tag_selector )
                    {
                        if ( ! strstr($tag_form_selectors, $tag_selector) )
                            $tag_form_selectors .= $tag_selector . ',';
                    }
                } // Create a comma deliniated list of synced lists for tag_synced_lists
                else if ( strstr($name, 'email_connector_') )
                {
                    // Name comes through as email_connector_espslug_listid, so replace the beginning of each one with corresponding esp slug, which leaves just the list id
                    if ( strstr($name, '_mailchimp') )
                        $synced_list = array('esp' => 'mailchimp', 'list_id' => str_replace('email_connector_mailchimp_', '', $name), 'list_name' => $value);
                    else if ( strstr($name, '_constant_contact') )
                        $synced_list = array('esp' => 'constant_contact', 'list_id' => str_replace('email_connector_constant_contact_', '', $name), 'list_name' => $value);
                    else if ( strstr($name, '_aweber') )
                        $synced_list = array('esp' => 'aweber', 'list_id' => str_replace('email_connector_aweber_', '', $name), 'list_name' => $value);
                    else if ( strstr($name, '_campaign_monitor') )
                        $synced_list = array('esp' => 'campaign_monitor', 'list_id' => str_replace('email_connector_campaign_monitor_', '', $name), 'list_name' => $value);
                    else if ( strstr($name, '_getresponse') )
                        $synced_list = array('esp' => 'getresponse', 'list_id' => str_replace('email_connector_getresponse_', '', $name), 'list_name' => $value);
                    else if ( strstr($name, '_postmatic') )
                        $synced_list = array('esp' => 'postmatic', 'list_id' => str_replace('email_connector_postmatic_', '', $name), 'list_name' => $value);

                    array_push($tag_synced_lists, $synced_list);
                }
            }
            
            if ( $_POST['email_form_tags_custom'] )
            {
                if ( strstr($_POST['email_form_tags_custom'], ',') )
                {
                    foreach ( explode(',', sanitize_text_field($_POST['email_form_tags_custom'])) as $tag )
                    {
                        if ( ! strstr($tag_form_selectors, $tag) )
                            $tag_form_selectors .= $tag . ',';
                    }
                } else {
                    if ( ! strstr($tag_form_selectors, sanitize_text_field( $_POST['email_form_tags_custom']) ) )
                        $tag_form_selectors .= sanitize_text_field( $_POST['email_form_tags_custom'] ) . ',';
                }
            }
            
            // Sanitize the selectors by removing any spaces and any trailing commas
            $tag_form_selectors = rtrim(str_replace(' ', '', $tag_form_selectors), ',');
            
            if ( $lead_list_id )
            {
                $tagger->save_list(
                    $lead_list_id,
                    $tag_name,
                    $tag_form_selectors,
                    serialize($tag_synced_lists)
                );
            } else {
                $tagger->lead_list_id = $tagger->add_list(
                    $tag_name,
                    $tag_form_selectors,
                    serialize($tag_synced_lists)
                );
            }
        }

        echo '<div id="inboundrocket" class="wrap '. ( $wp_version < 3.8 && !is_plugin_active('mp6/mp6.php')  ? 'pre-mp6' : ''). '">';

            if ( $this->action == 'edit_list' || $this->action == 'add_list' ) {
                inboundrocket_track_plugin_activity("Loaded Lead List Editor");
                $this->inboundrocket_render_tag_editor();
            }
            else
            {
                inboundrocket_track_plugin_activity("Loaded Lead List");
                $this->inboundrocket_render_tag_list_page();
            }

            $this->inboundrocket_footer();

        echo '</div>';
    }

    /**
     * Creates list table for Contacts page
     *
     */
    function inboundrocket_render_tag_editor ()
    {
        ?>
        <div class="inboundrocket-contacts">
            <?php

                if ( $this->action == 'edit_list' || $this->action == 'add_list' )
                {
                    $lead_list_id = ( isset($_GET['lead_list']) ? sanitize_text_field($_GET['lead_list']) : FALSE);
                    $tagger = new IR_Lead_List_Editor($lead_list_id);
                }

                if ( $tagger->lead_list_id ) $tagger->get_tag_details($tagger->lead_list_id);
                
                $this->inboundrocket_header(( $this->action == 'edit_list' ? 'Edit a Lead List' : __('Add a Lead List', 'inbound-rocket') ), 'inboundrocket-contacts__header');
                
                echo '<a href="' . admin_url('admin.php?page=inboundrocket_lead_lists') .'">&larr; '. __('Manage Lead Lists', 'inbound-rocket') .'</a>';
            ?>

            <div class="">
                <form id="inboundrocket-tag-settings" action="<?php echo admin_url('admin.php?page=inboundrocket_lead_lists'); ?>" method="POST">

                    <table class="form-table"><tbody>
                        <tr>
                            <th scope="row"><label for="tag_name"><?php _e('Lead List name','inbound-rocket'); ?></label></th>
                            <td><input name="tag_name" type="text" id="tag_name" value="<?php echo ( isset($tagger->details->tag_text) ? esc_attr($tagger->details->tag_text) : '' ); ?>" class="regular-text" placeholder="<?php _e('Lead List name','inbound-rocket'); ?>"></td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Automatically tag contacts who fill out any of these forms','inbound-rocket'); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><span><?php _e('Automatically tag contacts who fill out any of these forms','inbound-rocket'); ?></span></legend>
                                    <?php 
                                        $tag_form_selectors = ( isset($tagger->details->tag_form_selectors) ? explode(',', str_replace(' ', '', $tagger->details->tag_form_selectors)) : '');
                                        
                                        foreach ( $tagger->selectors as $selector )
                                        {
                                            $html_id = 'email_form_tags_' . str_replace(array('#', '.'), array('id_', 'class_'), $selector); 
                                            $selector_set = FALSE;
                                            
                                            if ( isset($tagger->details->tag_form_selectors) && strstr($tagger->details->tag_form_selectors, $selector) )
                                            {
                                                $selector_set = TRUE;
                                                $key = array_search($selector, $tag_form_selectors);
                                                if ( $key !== FALSE )
                                                    unset($tag_form_selectors[$key]);
                                            }
                                            
                                            echo '<label for="' . $html_id . '">';
                                                echo '<input name="' . $html_id . '" type="checkbox" id="' . $html_id . '" value="" ' . ( $selector_set ? 'checked' : '' ) . '>';
                                                echo $selector;
                                            echo '</label><br>';
                                        }
                                    ?>
                                </fieldset>
                                <br>
                                <input name="email_form_tags_custom" type="text" value="<?php echo ( $tag_form_selectors ? implode(', ', $tag_form_selectors) : ''); ?>" class="regular-text" placeholder="#form-id, .form-class">
                                <p class="description"><?php _e('Include additional form\'s css selectors.','inbound-rocket'); ?></p>
                            </td>
                        </tr>

                        
                        <?php    
                            foreach ( $this->esp_power_ups as $power_up_name => $power_up_slug )
                            {
                                if ( WPInboundRocket::is_power_up_active($power_up_slug) )
                                {
                                    global ${'inboundrocket_' . $power_up_slug . '_wp'}; // e.g inboundrocket_mailchimp_connector_wp
                                    $esp_name = strtolower(str_replace('_connector', '', $power_up_slug)); // e.g. mailchimp

                                    if ( ${'inboundrocket_' . $power_up_slug . '_wp'}->admin->authed && !${'inboundrocket_' . $power_up_slug . '_wp'}->admin->invalid_key )
                                        $lists = ${'inboundrocket_' . $power_up_slug . '_wp'}->admin->ir_get_lists();
                                    
                                    $synced_lists = ( isset($tagger->details->tag_synced_lists) ? unserialize($tagger->details->tag_synced_lists) : '' );
									$synced = FALSE;
									
                                    echo '<tr>';
                                        echo '<th scope="row">';
                                        
                                        switch($power_up_name){
	                                        case 'AWeber':
	                                        	_e('Sync contacts to AWeber list','inbound-rocket'); 
	                                        break;
	                                        case 'Postmatic':
	                                        	_e('Push tagged contacts to Postmatic','inbound-rocket'); 
	                                        break;
	                                        case 'Campaign Monitor':
	                                        	_e('Sync contacts to Campaign Monitor list','inbound-rocket'); 
	                                        break;
	                                        case 'MailChimp':
	                                         	_e('Push tagged contacts to Mailchimp','inbound-rocket'); 
	                                        break;
	                                        default:
	                                        	_e(sprintf('Push tagged contacts to these %s lists',$power_up_name),'inbound-rocket');
                                        }
                                        
                                        echo'</th>';
                                        echo '<td>';
                                            echo '<fieldset>';
                                                echo '<legend class="screen-reader-text"><span>'.sprintf(__('Push tagged contacts to these %s email lists','inbound-rocket'),$power_up_name).'</span></legend>';
                                                $esp_name_readable = ucwords(str_replace('_', ' ', $esp_name));
                                                $esp_url = str_replace('_', '', $esp_name) . '.com';
																								
                                                switch ( $esp_name ) 
                                                {
                                                    case 'mailchimp' :
                                                        $esp_list_url = 'http://admin.mailchimp.com/lists/new-list/';
                                                        $settings_page_anchor_tab = 'inboundrocket_mc_options';
                                                        $invalid_key_message = sprintf(__('It looks like your %s API key is invalid','inbound-rocket'),$esp_name_readable).'...<br/><br/>';
                                                        $invalid_key_link = '<a target="_blank" href="http://kb.mailchimp.com/accounts/management/about-api-keys#Find-or-Generate-Your-API-Key">'.__('Get your API key','inbound-rocket').'</a> '.__('from','inbound-rocket').' <a href="http://admin.mailchimp.com/account/api/" target="_blank">MailChimp.com</a>';
                                                    break;

                                                    case 'constant_contact' :
                                                        $esp_list_url = 'https://login.constantcontact.com/login/login.sdo?goto=https://ui.constantcontact.com/rnavmap/distui/contacts';
                                                        $settings_page_anchor_tab = '#ir_cc_email';
                                                    break;

                                                    case 'aweber' :
                                                        $esp_list_url = 'https://www.aweber.com/users/newlist#about';
                                                        $settings_page_anchor_tab = 'inboundrocket_aw_options';
                                                        $invalid_key_message = sprintf(__('It looks like your %s Authorization Code is invalid','inbound-rocket'),$esp_name_readable).'...<br/><br/>';
                                                        $invalid_key_link = '<a target="_blank" href="https://help.aweber.com/hc/en-us/articles/204031226-How-Do-I-Authorize-an-App-">'.__('Get your Authorization Code','inbound-rocket').'</a> '.__('from','inbound-rocket').' <a href="https://auth.aweber.com/1.0/oauth/authorize_app/156b03fb" target="_blank">AWeber.com</a>';
                                                    break;

                                                    case 'campaign_monitor' :
                                                        $esp_list_url = 'https://login.createsend.com/l';
                                                        $settings_page_anchor_tab = 'inboundrocket_cm_options';
                                                        $invalid_key_message = sprintf(__('It looks like your %s API key is invalid','inbound-rocket'),$esp_name_readable).'...<br/><br/>';
                                                        $invalid_key_link = '<a target="_blank" href="http://help.campaignmonitor.com/topic.aspx?t=206">'.__('Get your API key','inbound-rocket').'</a> '.__('from','inbound-rocket').' <a href="https://login.createsend.com/l" target="_blank">CampaignMonitor.com</a>';
                                                    break;
                                                    
                                                    case 'getresponse' :
                                                        $esp_list_url = 'https://app.getresponse.com/create_campaign.html';
                                                        $settings_page_anchor_tab = 'inboundrocket_gr_options';
                                                        $invalid_key_message = sprintf(__('It looks like your %s API key is invalid','inbound-rocket'),$esp_name_readable).'...<br/><br/>';
                                                        $invalid_key_link = '<a target="_blank" href="http://support.getresponse.com/faq/where-i-find-api-key">'.__('Get your API key','inbound-rocket').'</a> '.__('from','inbound-rocket').' <a href="https://app.getresponse.com/account.html#api" target="_blank">GetResponse.com</a>';
                                                    break;
                                                    
                                                    case 'postmatic' :
                                                        $esp_list_url = 'https://app.gopostmatic.com/';
                                                        $settings_page_anchor_tab = 'inboundrocket_pm_options';
                                                        $invalid_key_message = sprintf(__('It looks like your %s API key is invalid','inbound-rocket'),$esp_name_readable).'...<br/><br/>';
                                                        $invalid_key_link = '<a target="_blank" href="http://docs.gopostmatic.com/article/115-how-to-request-and-enter-your-api-key">'.__('Get your API key','inbound-rocket').'</a> '.__('from','inbound-rocket').' <a href="https://app.gopostmatic.com/" target="_blank">GoPostmatic.com</a>';
                                                    break;

                                                    default:
                                                        $esp_list_url = '';
                                                        $settings_page_anchor_tab = '';
                                                    break;
                                                }

                                                if ( ! ${'inboundrocket_' . $power_up_slug . '_wp'}->admin->authed && $power_up_slug !='postmatic_connector')
                                                {
													echo sprintf(__('It looks like you haven\'t set up your %s integration yet','inbound-rocket'),$esp_name_readable).'...<br/><br/>';
                                                    echo '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_settings&tab=' . $settings_page_anchor_tab . '">'.sprintf(__('Set up your %s integration','inbound-rocket'),$esp_name_readable).'</a>';
                                                }
                                                else if ( isset(${'inboundrocket_' . $power_up_slug . '_wp'}->admin->invalid_key) && ${'inboundrocket_' . $power_up_slug . '_wp'}->admin->invalid_key )
                                                {
                                                    echo $invalid_key_message;
                                                    echo '<p>' . $invalid_key_link . ' '.__('then try copying and pasting it again in','inbound-rocket').' <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_settings&tab=' . $settings_page_anchor_tab . '">Inbound Rocket → Settings → '.$esp_name_readable.'</a></p>';
                                                }
                                                else if ($power_up_name == 'Postmatic')
                                                {
	                                                if ( $synced_lists )
                                                    {
                                                        // Search the synched lists for this tag for the list_id
                                                        //@TODO Postmatic doesn't have lists so I don't think we need this if loop for sync lists and can just only have the two echo's
                                                        $key = inboundrocket_array_search_deep('postmatic', $synced_lists, 'esp');

                                                        if ( isset($key) )
                                                        {
                                                            // Double check that the list is synced with the actual ESP
                                                            if ( $synced_lists[$key]['esp'] == $esp_name )
                                                                $synced = TRUE;
                                                        } else {
	                                                        $synced = FALSE;
                                                        }
                                                    }
                                                	echo '<label for="inboundrocket_' . $power_up_slug . '_wp">';
                                                    echo '<input name="email_connector_postmatic" type="checkbox" id="email_connector_postmatic" value="' . __('Push to Postmatic','inbound-rocket') . '" ' . ( $synced ? 'checked' : '' ) . '>';
                                                    _e('Push to Postmatic','inbound-rocket');
                                                echo '</label><br>';
                                                }
                                                else if ( !empty($lists) )
                                                {
													foreach ( $lists as $list )
                                                    {
                                                        $list_id = $list->id;

                                                        // Hack for constant contact's list id string (e.g. http://api.constantcontact.com/ws/customers/name%40website.com/lists/1234567890) 
                                                        if ( $power_up_name == 'Constant Contact' )
                                                            $list_id = end(explode('/', $list_id));


                                                        $html_id = 'email_connector_' . $esp_name . '_' . $list_id;
                                                        $synced = FALSE;

                                                        if ( $synced_lists )
                                                        {
                                                            
                                                            // Search the synched lists for this tag for the list_id
                                                            $key = inboundrocket_array_search_deep($list_id, $synced_lists, 'list_id');

                                                            if ( isset($key) )
                                                            {
                                                                // Double check that the list is synced with the actual ESP
                                                                if ( $synced_lists[$key]['esp'] == $esp_name )
                                                                    $synced = TRUE;
                                                            }
                                                        }

                                                        echo '<label for="' . $html_id  . '">';
                                                            echo '<input name="' . $html_id  . '" type="checkbox" id="' . $html_id  . '" value="' . $list->name . '" ' . ( $synced ? 'checked' : '' ) . '>';
                                                            echo $list->name;
                                                        echo '</label><br>';
                                                    }
                                                }
                                                else
                                                {
                                                    echo sprintf(__('It looks like you don\'t have any %s lists yet','inbound-rocket'),$esp_name_readable).'...<br/><br/>';
                                                    echo '<a href="' . $esp_list_url . '" target="_blank">'.sprintf(__('Create a list on %s','inbound-rocket'),esc_url($esp_url)). '</a>';
                                                }
                                            echo '</fieldset>';
                                        echo '</td>';
                                    echo '</tr>';
                                }
                            }
                        ?>
                        
                    </tbody></table>
                    <input type="hidden" name="lead_list_id" value="<?php echo esc_attr($lead_list_id); ?>" />
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','inbound-rocket'); ?>">
                    </p>
                </form>
            </div>

        </div>

        <?php
    }

    /**
     * Creates list table for Lead Lists page
     *
     */
    function inboundrocket_render_tag_list_page ()
    {
        global $wp_version;

        if ( $this->action == 'delete_list')
        {
            $lead_list_id = ( isset($_GET['lead_list']) ? $_GET['lead_list'] : FALSE);
            $tagger = new IR_Lead_List_Editor($lead_list_id);
            $tagger->delete_list($lead_list_id);
        }

        //Create an instance of our package class...
        $inboundrocketLeadListTable = new IR_Lead_List_Table();

        // Process any bulk actions before the contacts are grabbed from the database
        $inboundrocketLeadListTable->process_bulk_action();
        
        //Fetch, prepare, sort, and filter our data...
        $inboundrocketLeadListTable->data = $inboundrocketLeadListTable->get_lead_lists();
        $inboundrocketLeadListTable->prepare_items();

        ?>
        <div class="inboundrocket-contacts">

            <?php
                $this->inboundrocket_header(''.__('Manage Inbound Rocket Lead Lists', 'inbound-rocket') .' <a href="' . wp_nonce_url(admin_url('admin.php?page=inboundrocket_lead_lists&action=add_list')).'" class="add-new-h2">'.__('Add New', 'inbound-rocket') .'</a>', 'inboundrocket-contacts__header');
            ?>
            
            <div class="">
                    
                    <div class="inboundrocket-contacts__table">
                        <?php $inboundrocketLeadListTable->display();  ?>
                    </div>
                
            </div>

        </div>

        <?php
    }
    
    /**
     * Creates contacts chart content
     */
    function inboundrocket_build_contacts_chart_stats () 
    {
        $contacts_chart = "";

        //$contacts_chart .= "<div class='inboundrocket-stats__chart-container'>";
            $contacts_chart .= '<canvas id="contacts_chart"></canvas>';
        //$contacts_chart .= "</div>";
        $contacts_chart .= "<div class='inboundrocket__big-numbers-container'>";
            $contacts_chart .= "<div class='inboundrocket-stats__big-number'>";
                $contacts_chart .= "<label class='inboundrocket-stats__big-number-top-label'>".__('Today','inbound-rocket')."</label>";
                $contacts_chart .= "<h1  class='inboundrocket-stats__big-number-content'>" . number_format($this->stats_dashboard->total_new_contacts) . "</h1>";
                $contacts_chart .= "<label class='inboundrocket-stats__big-number-bottom-label'>".__('new','inbound-rocket')." " . ( $this->stats_dashboard->total_new_contacts != 1 ? __('contacts','inbound-rocket') : __('contact','inbound-rocket') ) . "</label>";
            $contacts_chart .= "</div>";
            $contacts_chart .= "<div class='inboundrocket-stats__big-number big-number--average'>";
                $contacts_chart .= "<label class='inboundrocket-stats__big-number-top-label'>".__('Avg last 90 days','inbound-rocket')."</label>";
                $contacts_chart .= "<h1  class='inboundrocket-stats__big-number-content'>" . number_format($this->stats_dashboard->avg_contacts_last_90_days) . "</h1>";
                $contacts_chart .= "<label class='inboundrocket-stats__big-number-bottom-label'>".__('new','inbound-rocket')." " . ( $this->stats_dashboard->avg_contacts_last_90_days != 1 ? __('contacts','inbound-rocket') : __('contact','inbound-rocket') ) . "</label>";
            $contacts_chart .= "</div>";
            $contacts_chart .= "<div class='inboundrocket-stats__big-number'>";
                $contacts_chart .= "<label class='inboundrocket-stats__big-number-top-label'>".__('Best day ever','inbound-rocket')."</label>";
                $contacts_chart .= "<h1  class='inboundrocket-stats__big-number-content'>" . number_format($this->stats_dashboard->best_day_ever) . "</h1>";
                $contacts_chart .= "<label class='inboundrocket-stats__big-number-bottom-label'>".__('new','inbound-rocket')." " . ( $this->stats_dashboard->best_day_ever != 1 ? __('contacts','inbound-rocket') : __('contact','inbound-rocket') ) . "</label>";
            $contacts_chart .= "</div>";
            $contacts_chart .= "<div class='inboundrocket-stats__big-number'>";
                $contacts_chart .= "<label class='inboundrocket-stats__big-number-top-label'>".__('All time','inbound-rocket')."</label>";
                $contacts_chart .= "<h1  class='inboundrocket-stats__big-number-content'>" . number_format($this->stats_dashboard->total_contacts) . "</h1>";
                $contacts_chart .= "<label class='inboundrocket-stats__big-number-bottom-label'>".__('total','inbound-rocket')." " . ( $this->stats_dashboard->total_contacts != 1 ? __('contacts','inbound-rocket') : __('contact','inbound-rocket') ) . "</label>";
            $contacts_chart .= "</div>";
        $contacts_chart .= "</div>";

        return $contacts_chart;
    }
    
     /**
     * Creates contacts chart content
     */
    function inboundrocket_build_new_contacts_postbox () 
    {
        $new_contacts_postbox = "";

        if ( count($this->stats_dashboard->new_contacts) )
        {
            $new_contacts_postbox .= '<table class="inboundrocket-postbox__table" style="table-layout: fixed;"><tbody>';
                $new_contacts_postbox .= '<tr>';
                    $new_contacts_postbox .= '<th>'.__('contact','inbound-rocket').'</th>';
                    $new_contacts_postbox .= '<th>'.__('pageviews','inbound-rocket').'</th>';
                    $new_contacts_postbox .= '<th>'.__('original source','inbound-rocket').'</th>';
                $new_contacts_postbox .= '</tr>';

                foreach ( $this->stats_dashboard->new_contacts as $contact )
                {
                   	$gravatar_hash = md5( strtolower( trim( $contact->lead_email ) ) ); 
                    $new_contacts_postbox .= '<tr>';
                        $new_contacts_postbox .= '<td class="">';
                            $new_contacts_postbox .= '<a href="'.admin_url('admin.php?page=inboundrocket_contacts&action=view&lead=' . $contact->lead_id . '&stats_dashboard=1').'"><img class="lazy pull-left inboundrocket-contact-avatar inboundrocket-dynamic-avatar_' . substr($contact->lead_id, -1) .'" src="http://www.gravatar.com/avatar/' . $gravatar_hash . '" width="35px" height="35px" style="border-radius: 50%"><b>' . $contact->lead_email . '</b></a>';
                        $new_contacts_postbox .= '</td>';
                        $new_contacts_postbox .= '<td class="">' . $contact->pageviews . '</td>';
                        $new_contacts_postbox .= '<td class="">' . $this->stats_dashboard->print_readable_source($this->stats_dashboard->check_lead_source($contact->lead_source, $contact->lead_origin_url)) . '</td>';
                    $new_contacts_postbox .= '</tr>';
                }

            $new_contacts_postbox .= '</tbody></table>';
        }
        else
            $new_contacts_postbox .= '<i>'.__('No new contacts today...','inbound-rocket').'</i>';

        return $new_contacts_postbox;
    }
    
    /**
     * Creates returning contacts chart content
     */
    function inboundrocket_build_returning_contacts_postbox () 
    {
        $returning_contacts_postbox = "";

        if ( count($this->stats_dashboard->returning_contacts) )
        {
            $returning_contacts_postbox .= '<table class="inboundrocket-postbox__table" style="table-layout: fixed;"><tbody>';
                $returning_contacts_postbox .= '<tr>';
                    $returning_contacts_postbox .= '<th>'.__('contact','inbound-rocket').'</th>';
                    $returning_contacts_postbox .= '<th>'.__('pageviews','inbound-rocket').'</th>';
                    $returning_contacts_postbox .= '<th>'.__('original source','inbound-rocket').'</th>';
                $returning_contacts_postbox .= '</tr>';

                foreach ( $this->stats_dashboard->returning_contacts as $contact )
                {
                    $gravatar_hash = md5( strtolower( trim( $contact->lead_email ) ) );
                    $returning_contacts_postbox .= '<tr>';
                        $returning_contacts_postbox .= '<td class="">';
                            $returning_contacts_postbox .= '<a href="'.admin_url('admin.php?page=inboundrocket_contacts&action=view&lead=' . $contact->lead_id . '&stats_dashboard=1').'"><img class="lazy pull-left inboundrocket-contact-avatar inboundrocket-dynamic-avatar_' . substr($contact->lead_id, -1) .'" src="http://www.gravatar.com/avatar/' . $gravatar_hash . '" width="35px" height="35px" style="border-radius: 50%"><b>' . $contact->lead_email . '</b></a>';
                        $returning_contacts_postbox .= '</td>';
                        $returning_contacts_postbox .= '<td class="">' . $contact->pageviews . '</td>';
                        $returning_contacts_postbox .= '<td class="">' . $this->stats_dashboard->print_readable_source($this->stats_dashboard->check_lead_source($contact->lead_source, $contact->lead_origin_url)) . '</td>';
                    $returning_contacts_postbox .= '</tr>';
                }

            $returning_contacts_postbox .= '</tbody></table>';
        }
        else
            $returning_contacts_postbox .= '<i>'.__('No returning contacts today...','inbound-rocket').'</i>';

        return $returning_contacts_postbox;
    }
    
    /**
     * Creates most popuplar pages chart content
     */
    function inboundrocket_build_most_popular_pages_postbox () 
    {
        $most_popular_pages_postbox = "";


        if ( count($this->stats_dashboard->most_popular_pages) )
        {
			$most_popular_pages_postbox .= '<table class="inboundrocket-postbox__table" style="table-layout: fixed;"><tbody>';
            $most_popular_pages_postbox .= '<tr>';
                $most_popular_pages_postbox .= '<th style="width:60%">'.__('Page','inbound-rocket').'</th>';
                $most_popular_pages_postbox .= '<th style="width:10%;text-align:center;">'.__('Count','inbound-rocket').'</th>';
                $most_popular_pages_postbox .= '<th style="width:30%;"></th>';
            $most_popular_pages_postbox .= '</tr>';
            
            foreach ( $this->stats_dashboard->most_popular_pages as $pages )
            {
	            
	        if(!empty($pages['total']))
	        {
	        	$percentage = ($pages['total'] * 100)/$pages['max_total'];
	        	if($percentage>100) $percentage = 100;
	        	
	        } else
	        	$percentage = 0;
	        	   
            $most_popular_pages_postbox .= '<tr>';
                $most_popular_pages_postbox .= '<td style="width:40%" class="">' . esc_attr($pages['page_title']) . '</td>';
                $most_popular_pages_postbox .= '<td style="width:10%;text-align:center;" class="">' . esc_attr($pages['total']) . '</td>';
                $most_popular_pages_postbox .= '<td style="width:50%;overflow:hidden;">';
                    $most_popular_pages_postbox .= '<div style="background: #f0f0f0; padding: 0px; height: 20px !important;"><div style="background: #f16b18; height: 100%; width: ' . $percentage . '%;">&nbsp;</div></div>';
                $most_popular_pages_postbox .= '</td>';
            $most_popular_pages_postbox .= '</tr>';
            
            }
            
            $most_popular_pages_postbox .= '</tbody></table>';
        }
        else
        	$most_popular_pages_postbox .= '<i>'.__('No most visited pages at this time...','inbound-rocket').'</i>';

        return $most_popular_pages_postbox;
	}

    /**
     * Creates new shares chart content
     */
    function inboundrocket_build_new_shares_postbox () 
    {
        $new_shares_postbox = "";

        if ( count($this->stats_dashboard->new_shares) )
        {
			$new_shares_postbox .= '<table class="inboundrocket-postbox__table" style="table-layout: fixed;"><tbody>';
            $new_shares_postbox .= '<tr>';
            $new_shares_postbox .= '<th style="width:15%;">'.__('Shared to','inbound-rocket').'</th>';
            $new_shares_postbox .= '<th style="text-align:center;width:10%;">'.__('Count','inbound-rocket').'</th>';
            $new_shares_postbox .= '<th style="width:75%;">'.__('Content','inbound-rocket').'</th>';
            $new_shares_postbox .= '</tr>';
            
            foreach ( $this->stats_dashboard->new_shares as $shares )
            {
	            $new_shares_postbox .= '<tr>';
                $new_shares_postbox .= '<td class="">' . $shares['shared_to'] . '</td>';
                $new_shares_postbox .= '<td style="text-align:center;" class="">' . $shares['shared_count'] . '</td>';
                $new_shares_postbox .= '<td class="">' . $shares['shared_content'] . '</td>';
				$new_shares_postbox .= '</tr>';	
            }
            
            $new_shares_postbox .= '</tbody></table>';
            
        } else
        	$new_shares_postbox .= '<i>'.__('No new shares at this time...','inbound-rocket').'</i>';
        return $new_shares_postbox;
	}

    /**
     * Creates popular referer chart content
     */
    function inboundrocket_build_most_popular_referrers_postbox () 
    {
        $most_popular_referrer_postbox = "";

        if ( count($this->stats_dashboard->most_popular_referrer) )
        {
	        $most_popular_referrer_postbox .= '<table class="inboundrocket-postbox__table" style="table-layout: fixed;"><tbody>';
            $most_popular_referrer_postbox .= '<tr>';
                $most_popular_referrer_postbox .= '<th width="60%">'.__('Referral','inbound-rocket').'</th>';
                $most_popular_referrer_postbox .= '<th width="10%">'.__('Count','inbound-rocket').'</th>';
                $most_popular_referrer_postbox .= '<th width="30%"></th>';
            $most_popular_referrer_postbox .= '</tr>';
            
            foreach ( $this->stats_dashboard->most_popular_referrer as $referral )
            {
	            
        	$percentage = round(($referral['referral_count']/$referral['referral_total']) * 100);
        	if($percentage>100) $percentage = 100;
	            
            $most_popular_referrer_postbox .= '<tr>';
                $most_popular_referrer_postbox .= '<td class="">' . $referral['referral_source'] . '</td>';
                $most_popular_referrer_postbox .= '<td class="">' . $referral['referral_count'] . '</td>';
                $most_popular_referrer_postbox .= '<td style="width:100%">';
                    $most_popular_referrer_postbox .= '<div style="background: #f0f0f0; padding: 0px; height: 20px !important;width:100%;"><div style="background: #f16b18; height: 100%; width: ' . $percentage . '%;">&nbsp;</div></div>';
                $most_popular_referrer_postbox .= '</td>';
            $most_popular_referrer_postbox .= '</tr>';
            
            }
            
            $most_popular_referrer_postbox .= '</tbody></table>';
        }
        else
        	$most_popular_referrer_postbox .= '<i>'.__('No referrers today...','inbound-rocket').'</i>';

        return $most_popular_referrer_postbox;
	}	 
    
    /**
     * Creates sources chart content
     */
    function inboundrocket_build_sources_postbox () 
    {
        $sources_postbox = "";

        $sources_postbox .= '<table class="inboundrocket-postbox__table" style="table-layout: fixed;"><tbody>';
            $sources_postbox .= '<tr>';
                $sources_postbox .= '<th width="150">'.__('source','inbound-rocket').'</th>';
                $sources_postbox .= '<th width="20">'.__('Contacts','inbound-rocket').'</th>';
                $sources_postbox .= '<th></th>';
            $sources_postbox .= '</tr>';
            $sources_postbox .= '<tr>';
                $sources_postbox .= '<td class="">'.__('Direct Traffic','inbound-rocket').'</td>';
                $sources_postbox .= '<td class="">' . $this->stats_dashboard->direct_count . '</td>';
                $sources_postbox .= '<td>';
                    $sources_postbox .= '<div style="background: #f0f0f0; padding: 0px; height: 20px !important;"><div style="background: #f16b18; height: 100%; width: ' . ( $this->stats_dashboard->max_source ? (($this->stats_dashboard->direct_count/$this->stats_dashboard->max_source)*100) : '0' ) . '%;">&nbsp;</div></div>';
                $sources_postbox .= '</td>';
            $sources_postbox .= '</tr>';
            $sources_postbox .= '<tr>';
                $sources_postbox .= '<td class="">'.__('Organic Search','inbound-rocket').'</td>';
                $sources_postbox .= '<td class="">' . $this->stats_dashboard->organic_count . '</td>';
                $sources_postbox .= '<td>';
                    $sources_postbox .= '<div style="background: #f0f0f0; padding: 0px; height: 20px !important;"><div style="background: #f16b18; height: 100%; width: ' . ( $this->stats_dashboard->max_source ? (($this->stats_dashboard->organic_count/$this->stats_dashboard->max_source)*100) : '0' ) . '%;">&nbsp;</div></div>';
                $sources_postbox .= '</td>';
            $sources_postbox .= '</tr>';
            $sources_postbox .= '<tr>';
                $sources_postbox .= '<td class="">'.__('Referrals','inbound-rocket').'</td>';
                $sources_postbox .= '<td class="">' . $this->stats_dashboard->referral_count . '</td>';
                $sources_postbox .= '<td>';
                    $sources_postbox .= '<div style="background: #f0f0f0; padding: 0px; height: 20px !important;"><div style="background: #f16b18; height: 100%; width: ' . ( $this->stats_dashboard->max_source ? (($this->stats_dashboard->referral_count/$this->stats_dashboard->max_source)*100) : '0' ) . '%;">&nbsp;</div></div>';
                $sources_postbox .= '</td>';
            $sources_postbox .= '</tr>';
            $sources_postbox .= '<tr>';
                $sources_postbox .= '<td class="">'.__('Social Media','inbound-rocket').'</td>';
                $sources_postbox .= '<td class="">' . $this->stats_dashboard->social_count . '</td>';
                $sources_postbox .= '<td>';
                    $sources_postbox .= '<div style="background: #f0f0f0; padding: 0px; height: 20px !important;"><div style="background: #f16b18; height: 100%; width: ' . ( $this->stats_dashboard->max_source ? (($this->stats_dashboard->social_count/$this->stats_dashboard->max_source)*100) : '0' ). '%;">&nbsp;</div></div>';
                $sources_postbox .= '</td>';
            $sources_postbox .= '</tr>';
            $sources_postbox .= '<tr>';
                $sources_postbox .= '<td class="">'.__('Email Marketing','inbound-rocket').'</td>';
                $sources_postbox .= '<td class="">' . $this->stats_dashboard->email_count . '</td>';
                $sources_postbox .= '<td>';
                    $sources_postbox .= '<div style="background: #f0f0f0; padding: 0px; height: 20px !important;"><div style="background: #f16b18; height: 100%; width: ' . ( $this->stats_dashboard->max_source ? (($this->stats_dashboard->email_count/$this->stats_dashboard->max_source)*100) : '0' ) . '%;">&nbsp;</div></div>';
                $sources_postbox .= '</td>';
            $sources_postbox .= '</tr>';
            $sources_postbox .= '<tr>';
                $sources_postbox .= '<td class="">'.__('Paid Search','inbound-rocket').'</td>';
                $sources_postbox .= '<td class="">' . $this->stats_dashboard->paid_count . '</td>';
                $sources_postbox .= '<td>';
                    $sources_postbox .= '<div style="background: #f0f0f0; padding: 0px; height: 20px !important;"><div style="background: #f16b18; height: 100%; width: ' . ( $this->stats_dashboard->max_source ? (($this->stats_dashboard->paid_count/$this->stats_dashboard->max_source)*100) : '0' ) . '%;">&nbsp;</div></div>';
                $sources_postbox .= '</td>';
            $sources_postbox .= '</tr>';
        $sources_postbox .= '</tbody></table>';

        return $sources_postbox;
    }
    
    /**
     * Prints checkbox for Inbound Rocket user subscription
     */
    function ir_subscription_callback()
    {
	    $options = get_option('inboundrocket_visitor_options');
	    ?>
	    <label for="ir_updates_subscription"><input type="checkbox" id="ir_updates_subscription" value="1" name="inboundrocket_visitor_options[ir_updates_subscription]" <?php checked( isset($options['ir_updates_subscription']) ? 1 : 0, 0 ); ?> /><?php _e('Keep me up to date with security and feature updates','inbound-rocket');?></label>
	    <?php
    }
    
    /**
     * Prints checkboxes for toggling Inbound Rocket access to specific user roles
     */
    function ir_grant_access_callback ()
    {
        $options = get_option('inboundrocket_visitor_options');
     
        $user_roles = get_editable_roles();
        
        // Show a disabled checkbox for administrative roles that always need to be enabled so users don't get locked out of the Inbound Rocket settings
        echo '<p><input id="ir_grant_access_to_administrator" type="checkbox" value="1" checked disabled/>';
        echo '<label for="ir_grant_access_to_administrator">'.__('Administrators','inbound-rocket').'</label></p>';

        if ( count($user_roles) )
        {
            foreach ( $user_roles as $key => $role )
            {
                $admin_role = FALSE;
                if ( isset($role['capabilities']['activate_plugins']) && $role['capabilities']['activate_plugins'] )
                    $admin_role = TRUE;

                $role_id = 'ir_grant_access_to_' . $key;

                if ( ! $admin_role )
                {
                    printf(
                        '<p><input id="' . $role_id . '" type="checkbox" name="inboundrocket_visitor_options[' . $role_id . ']" value="1"' . checked( isset($options[$role_id]) ? $options[$role_id] : 0, 1, FALSE ) . '/>' . 
                        '<label for="' . $role_id . '">' . $role['name'] . 's' . '</label></p>'
                    );
                }
            }
        }
    }
    
    function ir_emails_welcome_type_callback()
    {
	    $options = get_option('inboundrocket_email_options');
	    $type = isset($options['ir_emails_welcome_type']) ? $options['ir_emails_welcome_type'] : '';
	    ?>
	    <div style="display:flex;flex-direction:row;justify-content: flex-start;align-content: flex-start;align-items: stretch;">
	    <div>
	    <label for="ir_emails_welcome_type_php"><div style="display:flex;margin-right:15px;margin-bottom:10px;justify-content:center;align-items:center;height:80px;width:150px;padding:10px;background-color:#fff;border:1px solid #eee;border-radius:4px;"><img src="<?php echo INBOUNDROCKET_PATH . '/img/php.png'; ?>" alt="PHP" style="height:40px;width:auto;" /></div><input type="radio" id="ir_emails_welcome_type_php" value="php" name="inboundrocket_email_options[ir_emails_welcome_type]" <?php echo checked( $type, 'php' ) ?> /><?php _e('Default (none)','inbound-rocket');?></label>
	    </div>
	    <div>
		<label for="ir_emails_welcome_type_gmail"><div style="display:flex;margin-right:15px;margin-bottom:10px;justify-content:center;align-items:center;height:80px;width:150px;padding:10px;background-color:#fff;border:1px solid #eee;border-radius:4px;"><img src="<?php echo INBOUNDROCKET_PATH . '/img/gmail_soon.png'; ?>" alt="Gmail" style="height:40px;width:auto;" /></div><input type="radio" id="ir_emails_welcome_type_gmail" disabled="true" value="gmail" name="inboundrocket_email_options[ir_emails_welcome_type]" <?php echo checked( $type, 'gmail' ) ?> /><?php _e('Gmail (coming soon)','inbound-rocket');?></label>
		</div>
		<div>
	    <label for="ir_emails_welcome_type_other"><div style="display:flex;margin-bottom:10px;justify-content:center;align-items:center;height:80px;width:150px;padding:10px;background-color:#fff;border:1px solid #eee;border-radius:4px;"><img src="<?php echo INBOUNDROCKET_PATH . '/img/smtp_soon.png'; ?>" alt="SMTP" style="height:40px;width:auto;" /></div><input type="radio" id="ir_emails_welcome_type_other" value="smtp" name="inboundrocket_email_options[ir_emails_welcome_type]" disabled="true" <?php echo checked( $type, 'smtp' ) ?> /><?php _e('Other SMTP (coming soon)','inbound-rocket');?></label>
	    </div>
	    </div>
	    <?php
    }
    
    function ir_emails_welcome_from_callback()
    {
	     $options = get_option('inboundrocket_email_options');
	    $ir_emails_welcome_from = isset($options['ir_emails_welcome_from']) && $options['ir_emails_welcome_from'] ? sanitize_text_field($options['ir_emails_welcome_from']) : '';
     
        printf(
            '<input id="ir_emails_welcome_from" type="text" name="inboundrocket_email_options[ir_emails_welcome_from]" value="%s" style="width:70%%;" /><br/><span class="description">'.__('The email address which emails are sent from.','inbound-rocket').'<br>'.__('If left blank, the default value will use your WordPress admin email.','inbound-rocket').'</span>',
            !empty($ir_emails_welcome_from) ? $ir_emails_welcome_from : ''
        ); 
	}
	
	function ir_emails_welcome_name_callback()
    {
	     $options = get_option('inboundrocket_email_options');
	    $ir_emails_welcome_name = isset($options['ir_emails_welcome_name']) && $options['ir_emails_welcome_name'] ? sanitize_text_field($options['ir_emails_welcome_name']) : '';
     
        printf(
            '<input id="ir_emails_welcome_from" type="text" name="inboundrocket_email_options[ir_emails_welcome_name]" value="%s" style="width:70%%;" /><br/><span class="description">'.__('The name which emails are sent from.','inbound-rocket').'</span>',
            !empty($ir_emails_welcome_name) ? $ir_emails_welcome_name : ''
        ); 
	}
    
    function ir_emails_welcome_subject_callback()
    {
	     $options = get_option('inboundrocket_email_options');
	    $ir_emails_welcome_subject = isset($options['ir_emails_welcome_subject']) && $options['ir_emails_welcome_subject'] ? sanitize_text_field($options['ir_emails_welcome_subject']) : '';
     
        printf(
            '<input id="ir_emails_welcome_subject" type="text" name="inboundrocket_email_options[ir_emails_welcome_subject]" value="%s" style="width:70%%;" /><br/><span class="description">'.__('This is the subject of the welcome email, which will be send after someone signs-up on your website.','inbound-rocket').'</span>',
            !empty($ir_emails_welcome_subject) ? $ir_emails_welcome_subject : ''
        ); 
	}
	
	function ir_emails_welcome_content_callback()
    {
	    $options = get_option('inboundrocket_email_options');
	    $ir_emails_welcome_content = isset($options['ir_emails_welcome_content']) && $options['ir_emails_welcome_content'] ? wp_kses($options['ir_emails_welcome_content'], $this->kses_html, $this->protocals) : '';
		
		$initial_data='Thank You For Joining Our Mailing List.';
		
		if(empty($ir_emails_welcome_content)){
				$ir_emails_welcome_content = $initial_data;
		}
		
		$settings = array(
			'quicktags' => array('buttons' => 'em,strong,link',),
			'text_area_name'=>'inboundrocket_email_options[ir_emails_welcome_content]',//name you want for the textarea
			'quicktags' => true,
			'tinymce' => true
		);
		$id = 'ir_emails_welcome_content';//has to be lower case
		
        echo wp_editor( $ir_emails_welcome_content,$id,$settings ).'<script>jQuery(\'document\').ready(function(){jQuery(\'#ir_emails_welcome_content\').attr(\'name\',\'inboundrocket_email_options[ir_emails_welcome_content]\')});</script><br/><span class="description">'.__('This is the email which will be send after someone sign-ups on your website. It can either be plain text or you can copy paste your HTML code in here..','inbound-rocket').'</span>'; 
	}
    
    function ir_emails_welcome_send_callback()
    {
	    $options = get_option('inboundrocket_email_options');
	    ?>
	    <label for="ir_emails_welcome_send"><input type="checkbox" id="ir_emails_welcome_send" value="1" name="inboundrocket_email_options[ir_emails_welcome_send]" <?php echo checked( isset($options['ir_emails_welcome_send']) ? intval($options['ir_emails_welcome_send']) : 0, 1 ) ?> /><?php _e('Send a welcome email after someone becomes a contact','inbound-rocket');?><br><span class="description"><?php _e('Checking this box will auto send welcome emails to new contacts.','inbound-rocket');?></span></label>
	    <?php
    }
    
    function ir_emails_welcome_return_callback()
    {
	    $options = get_option('inboundrocket_email_options');
	    ?>
	    <label for="ir_emails_welcome_return"><input type="checkbox" id="ir_emails_welcome_return" value="1" name="inboundrocket_email_options[ir_emails_welcome_return]" <?php echo checked( isset($options['ir_emails_welcome_return']) ? intval($options['ir_emails_welcome_return']) : 0, 1 ) ?> /> <?php _e('Set the return-path to match the From Email','inbound-rocket');?><br><span class="description"><?php _e('Return Path indicates where non-delivery receipts or bounce messages - are to be sent.','inbound-rocket');?></span></label>
	    <?php
    }
    
    /**
     * Creates settings options
     */
    function inboundrocket_settings_page()
    {	
	    register_setting('inboundrocket_options', 'inboundrocket_options', array($this, 'sanitize'));
	    
	    register_setting('inboundrocket_visitor_options', 'inboundrocket_visitor_options', array($this, 'sanitize'));
		
		// Setup Visitor Tracking Tab
		$this->plugin_settings_tabs['inboundrocket_visitor_options'] = __('Visitor Tracking','inbound-rocket');
        add_settings_section('ir_settings_section', '', array($this, 'inboundrocket_options_section_heading'), 'inboundrocket_visitor_options');
        
        add_settings_field(
            'ir_email',
            __('Notification email','inbound-rocket'),
            array($this, 'ir_email_callback'),
            'inboundrocket_visitor_options',
            'ir_settings_section'
        );
                
        add_settings_field(
        	'ir_updates_subscription', 
        	__('Subscribe to updates','inbound-rocket'), 
        	array($this, 'ir_subscription_callback'), 
        	'inboundrocket_visitor_options', 
        	'ir_settings_section'
        );
                
        add_settings_field(
            'ir_enable_evercookie',
            __('Enable Evercookie Tracking','inbound-rocket'),
            array($this, 'ir_enable_evercookie_callback'),
            'inboundrocket_visitor_options',
            'ir_settings_section'
        );

        add_settings_field(
            'ir_enable_dashboard_widget',
            __('Enable Dashboard Widget','inbound-rocket'),
            array($this, 'ir_enable_dashboard_widget_callback'),
            'inboundrocket_visitor_options',
            'ir_settings_section'
        );

        add_settings_field(
            'ir_auto_cleanup_visits',
            __('Automatically clean-up unused records','inbound-rocket'),
            array($this, 'ir_auto_cleanup_visits_callback'),
            'inboundrocket_visitor_options',
            'ir_settings_section'
        );
        
        add_settings_field(
            'ir_auto_cleanup_days',
            '',
            array($this, 'ir_auto_cleanup_days_callback'),
            'inboundrocket_visitor_options',
            'ir_settings_section'
        );
             
        add_settings_field(
            'ir_do_not_track',
            __('Do not track logged in','inbound-rocket'),
            array($this, 'ir_do_not_track_callback'),
            'inboundrocket_visitor_options',
            'ir_settings_section'
        );
        
        add_settings_field(
            'ir_grant_access',
            __('Grant Inbound Rocket access to','inbound-rocket'),
            array($this, 'ir_grant_access_callback'),
            'inboundrocket_visitor_options',
            'ir_settings_section'
        );
		
		register_setting('inboundrocket_email_options', 'inboundrocket_email_options', array($this, 'sanitize'));
		
		// Setup Email Options Tab
		$this->plugin_settings_tabs['inboundrocket_email_options'] = __('Emails','inbound-rocket');
		add_settings_section('ir_emails_section', '', '', 'inboundrocket_email_options');
		add_settings_field(
			'ir_emails_welcome_from', 
			__('From Email','inbound-rocket'),
			array($this, 'ir_emails_welcome_from_callback'),
			'inboundrocket_email_options',
			'ir_emails_section'
		); 
		add_settings_field(
			'ir_emails_welcome_name', 
			__('From Name','inbound-rocket'),
			array($this, 'ir_emails_welcome_name_callback'),
			'inboundrocket_email_options',
			'ir_emails_section'
		);
		add_settings_field(
			'ir_emails_welcome_type', 
			__('Mailer','inbound-rocket'),
			array($this, 'ir_emails_welcome_type_callback'),
			'inboundrocket_email_options',
			'ir_emails_section'
		);
		add_settings_field(
			'ir_emails_welcome_return', 
			__('Return Path','inbound-rocket'),
			array($this, 'ir_emails_welcome_return_callback'),
			'inboundrocket_email_options',
			'ir_emails_section'
		);
		add_settings_field(
			'ir_emails_welcome_send', 
			__('Enable Welcome Emails','inbound-rocket'),
			array($this, 'ir_emails_welcome_send_callback'),
			'inboundrocket_email_options',
			'ir_emails_section'
		);
		add_settings_field(
			'ir_emails_welcome_subject', 
			__('Welcome Email Subject','inbound-rocket'),
			array($this, 'ir_emails_welcome_subject_callback'),
			'inboundrocket_email_options',
			'ir_emails_section'
		); 
		add_settings_field(
			'ir_emails_welcome_content', 
			__('Welcome Email Content','inbound-rocket'),
			array($this, 'ir_emails_welcome_content_callback'),
			'inboundrocket_email_options',
			'ir_emails_section'
		);
		
		$inboundrocket_active_power_ups = unserialize(get_option('inboundrocket_active_power_ups'));
        if (count($inboundrocket_active_power_ups) > 1 )
		{
			foreach($this->admin_power_ups as $power_up){
		        if($power_up->activated == 1 && $power_up->permanent != '1'){
			        $options_name = $power_up->options_name;
			        
			        $power_up->admin_init();
					        			        
			        switch($options_name){
				        case "inboundrocket_ctt_options":
				        
							$this->plugin_settings_tabs[$options_name] = __('Click To Tweet','inbound-rocket');
				        
				        break;
				        case "inboundrocket_ss_options":
				        
							$this->plugin_settings_tabs[$options_name] = __('Selection Sharer','inbound-rocket');							
				        
				        break;
				        case "inboundrocket_wb_options":
				        
				        	$this->plugin_settings_tabs[$options_name] = __('Welcome Bar','inbound-rocket');
				        
				        break;
				        case "inboundrocket_mc_options":
				        
				        	$this->plugin_settings_tabs[$options_name] = __('MailChimp','inbound-rocket');
						
				        break;
						case "inboundrocket_aw_options":
				        
				        	$this->plugin_settings_tabs[$options_name] = __('AWeber','inbound-rocket');
						
				        break;				        
						case "inboundrocket_cm_options":
				        
				        	$this->plugin_settings_tabs[$options_name] = __('Campaign Monitor','inbound-rocket');
						
				        break;
						case "inboundrocket_pm_options":
				        
				        	$this->plugin_settings_tabs[$options_name] = __('Postmatic','inbound-rocket');
						
				        break;
				        case "inboundrocket_ei_options":
				        
					        $this->plugin_settings_tabs[$options_name] = __('Exit Intent','inbound-rocket');
				        
				        break;			        
				        case "inboundrocket_sb_options":
				        
					        $this->plugin_settings_tabs[$options_name] = __('Scroll Boxes','inbound-rocket');
				        
				        break;
				        case "inboundrocket_wm_options":
				        
					        $this->plugin_settings_tabs[$options_name] = __('Welcome Mat','inbound-rocket');
				        
				        break;
				        case "inboundrocket_gdpr_options":
				        
					        $this->plugin_settings_tabs[$options_name] = __('GDPR Compliance','inbound-rocket');
				        
				        break;
				        case "inboundrocket_fb_options":
				        
					        $this->plugin_settings_tabs[$options_name] = __('Form Builder','inbound-rocket');
				        
				        break;
			        }
		        }
	        }
	        
		} else {
			
	        add_settings_section('ir_settings_section', ''.__('You have not activated any power-ups. Visit the','inbound-rocket').' <a href="'.admin_url('admin.php?page=inboundrocket_power_ups').'">'.__('power-ups page','inbound-rocket').'</a>, '. __('activate some today and start increasing conversions.','inbound-rocket').'', '', 'inboundrocket_options');
	        
	    }
	}
	
    function inboundrocket_options_section_heading ( )
    {
       $this->tracking_code_installed_message();   
    }

    function print_hidden_settings_fields()
    {
         // Hacky solution to solve the Settings API overwriting the default values
        $options = get_option('inboundrocket_options');
                
        printf(
            '<input id="ir_installed" type="hidden" name="inboundrocket_options[ir_installed]" value="%d" />',
            isset($options['ir_installed']) ? intval($options['ir_installed']) : 1
        );
        
        printf(
            '<input id="inboundrocket_version" type="hidden" name="inboundrocket_options[inboundrocket_version]" value="%s" />',
            isset($options['inboundrocket_version']) ? esc_attr($options['inboundrocket_version']) : INBOUNDROCKET_PLUGIN_VERSION
        );

        printf(
            '<input id="ir_db_version" type="hidden" name="inboundrocket_options[ir_db_version]" value="%s" />',
            isset($options['ir_db_version']) ? esc_attr($options['ir_db_version']) : INBOUNDROCKET_DB_VERSION
        );

		if(isset($options['onboarding_complete'])){

	        printf(
	            '<input id="onboarding_complete" type="hidden" name="inboundrocket_options[onboarding_complete]" value="%d" />',
	            isset($options['onboarding_complete']) ? intval($options['onboarding_complete']) : 0
	        );
        }
                
        printf(
            '<input id="ir_evercookie_corrupted" type="hidden" name="inboundrocket_options[ir_evercookie_corrupted]" value="%d" />',
            isset($options['ir_evercookie_corrupted']) ? intval($options['ir_evercookie_corrupted']) : 0
        );
    
        printf(
            '<input id="ir_enable_evercookie_status" type="hidden" name="inboundrocket_options[ir_enable_evercookie_status]" value="%s" />',
            isset($options['ir_enable_evercookie_status']) ? esc_attr($options['ir_enable_evercookie_status']) : (isset($options['ir_enable_evercookie']) && $options['ir_enable_evercookie']==1) ? 'enabled' : 'disabled'
        );
        
        printf(
            '<input id="converted_to_tags" type="hidden" name="inboundrocket_options[converted_to_tags]" value="%d" />',
            isset($options['converted_to_tags']) ? intval($options['converted_to_tags']) : 0
        );
        
        printf(
            '<input id="premium" type="hidden" name="inboundrocket_options[premium]" value="%d" />',
            isset($options['premium']) ? intval($options['premium']) : 0
        );

    }
    
    function has_leads ( )
    {
        global $wpdb;

        $q = "SELECT COUNT(hashkey) FROM {$wpdb->ir_leads} WHERE lead_deleted = 0 AND hashkey != '' AND lead_email != ''";
        $num_contacts = $wpdb->get_var($q);

        if ( $num_contacts > 0 )
        {
           return true;
        }
        else
        {
           return false;
        }
    }

    function tracking_code_installed_message ( )
    {
        global $wpdb;

        $num_contacts = $wpdb->get_var( "SELECT COUNT(hashkey) FROM {$wpdb->ir_leads} WHERE lead_deleted = 0 AND hashkey != '' AND lead_email != ''" );

        if ( $num_contacts > 0 )
        {
            echo '<div class="inboundrocket-section">';
                echo '<p style="color: #090; font-weight: bold;">'. __('Visitor tracking is installed and tracking visitors.','inbound-rocket').'</p>';
                echo '<p>'.__('The next time a visitor fills out a form on your WordPress site with an email address, Inbound Rocket will show you the contact\'s referral source, page view history and actions taken on the site.','inbound-rocket').'</p>';
            echo '</div>';
        }
        else
        {
            echo '<div class="inboundrocket-section">';
                echo '<p style="color: #ff7c00; font-weight: bold;">'.__('Inbound Rocket is setup and waiting for a form submission...','inbound-rocket').'</p>';
                echo '<p>'.__('Can\'t wait to see Inbound Rocket in action? Go fill out a form on your site to see your first contact.','inbound-rocket').'</p>';
            echo '</div>';
        }
    }


	/**
     * Prints enable evercookie checkbox for settings page
     */
    function ir_enable_evercookie_callback()
    {
        $options = get_option('inboundrocket_visitor_options');
        
        if ( isset($options['ir_evercookie_corrupted']) && $options['ir_evercookie_corrupted']==1 ) {
	        printf(
	            '<span class="description alert">' . __('Your host does not seem to support Evercookie tracking, Inbound Rocket automatically reverted back to normal HTTP Cookies for tracking. You can try again and enable Evercookie if you feel this is incorrect.','inbound-rocket'). '</span>'
            );
        }
        printf(
            '<p><input id="ir_enable_evercookie" type="checkbox" name="inboundrocket_visitor_options[ir_enable_evercookie]" value="1"' . checked( isset($options['ir_enable_evercookie']) ? $options['ir_enable_evercookie'] : 0, 1, FALSE ) . '/>' . 
            '<label for="ir_enable_evercookie">' . __('Enable Evercookie Tracking','inbound-rocket'). '</label></p>' .
            '<span class="description">' . __('Disabling Evercookie tracking will delete all Evercookie files from your server and enables common Cookies for tracking.','inbound-rocket'). '<br />' . __('By (re) enabling Evercookie, all necessary files will be automatically downloaded and placed in the Inbound Rocket plugin folder on your server.','inbound-rocket'). '</span>'
        );
    }

	/**
     * Prints enable dashboard widget checkbox for settings page
     */
    function ir_enable_dashboard_widget_callback()
    {
        $options = get_option('inboundrocket_visitor_options');
        printf(
            '<p><input id="ir_enable_dashboard_widget" type="checkbox" name="inboundrocket_visitor_options[ir_enable_dashboard_widget]" value="1"' . checked( isset($options['ir_enable_dashboard_widget']) ? esc_attr($options['ir_enable_dashboard_widget']) : 0, 1, FALSE ) . '/>' . 
            '<label for="ir_enable_dashboard_widget">' . __('Enable statistics widget on your dashboard','inbound-rocket'). '</label></p>' .
            '<span class="description">' . __('Disabling the dashboard widget will not show your statistics on the WordPress dashboard','inbound-rocket').'</span>'
        );
    }
    
	/**
     * Prints auto cleanup after X days for settings page
     * @TODO do an check if the value is set for the input box, if no value set than value is 0
     */
    function ir_auto_cleanup_visits_callback()
    {
        $options = get_option('inboundrocket_visitor_options');
        $ir_auto_cleanup_visits = !empty($options['ir_auto_cleanup_visits']) ? esc_attr($options['ir_auto_cleanup_visits']) : 0;
        echo
            'Remove users who have visited <select id="ir_auto_cleanup_visits" name="inboundrocket_visitor_options[ir_auto_cleanup_visits]"><option value="0" '.selected($ir_auto_cleanup_visits, 0, false).'>0</option><option value="1" '.selected($ir_auto_cleanup_visits, 1, false).'>1</option><option value="3" '.selected($ir_auto_cleanup_visits, 3, false).'>3</option><option value="5" '.selected($ir_auto_cleanup_visits, 5, false).'>5</option><option value="10" '.selected($ir_auto_cleanup_visits, 10, false).'>10</option><option value="15" '.selected($ir_auto_cleanup_visits, 15, false).'>15</option><option value="20" '.selected($ir_auto_cleanup_visits, 20, false).'>20</option><option value="25" '.selected($ir_auto_cleanup_visits, 25, false).'>25</option><option value="30" '.selected($ir_auto_cleanup_visits, 30, false).'>30</option></select> times or less ';
    }


	/**
     * Prints auto cleanup after X days for settings page
     * @TODO do an check if the value is set for the input box, if no value set than value is 0
     */
    function ir_auto_cleanup_days_callback()
    {
        $options = get_option('inboundrocket_visitor_options');
		$ir_auto_cleanup_days = !empty($options['ir_auto_cleanup_days']) ? esc_attr($options['ir_auto_cleanup_days']) : 0;
        echo
            'and did not return for <select id="ir_auto_cleanup_days" name="inboundrocket_visitor_options[ir_auto_cleanup_days]"><option value="0" '.selected($ir_auto_cleanup_days, 0, false).'>0</option><option value="3" '.selected($ir_auto_cleanup_days, 3, false).'>3</option><option value="5" '.selected($ir_auto_cleanup_days, 5, false).'>5</option><option value="10" '.selected($ir_auto_cleanup_days, 10, false).'>10</option><option value="15" '.selected($ir_auto_cleanup_days, 15, false).'>15</option><option value="20" '.selected($ir_auto_cleanup_days, 20, false).'>20</option><option value="25" '.selected($ir_auto_cleanup_days, 25, false).'>25</option><option value="30" '.selected($ir_auto_cleanup_days, 30, false).'>30</option><option value="45" '.selected($ir_auto_cleanup_days, 45, false).'>45</option><option value="60" '.selected($ir_auto_cleanup_days, 60, false).'>60</option><option value="90" '.selected($ir_auto_cleanup_days, 90, false).'>90</option><option value="120" '.selected($ir_auto_cleanup_days, 120, false).'>120</option></select> days.' . 
            '<br /><span class="description">' . __('Note: 0 values in either select box will disable auto clean.','inbound-rocket').'</span>' .
			'<br /><span class="description">' . __('Note: This will delete only non-converted user records from the database permanently.','inbound-rocket').'</span>';
    }

   /**
     * Prints do not track checkboxes for settings page
     */
    function ir_do_not_track_callback()
    {
        $options = get_option('inboundrocket_visitor_options');
        $user_roles = get_editable_roles();

        if ( count($user_roles) )
        {
            foreach ( $user_roles as $key => $role )
            {
                $role_id = 'ir_do_not_track_' . $key;
                printf(
                    '<p><input id="' . $role_id . '" type="checkbox" name="inboundrocket_visitor_options[' . $role_id . ']" value="1"' . checked( isset($options[$role_id]) ? esc_attr($options[$role_id]) : '0', 1, FALSE ) . '/>' . 
                    '<label for="' . $role_id . '">' . $role['name'] . 's' . '</label></p>'
                );
            }
        }
    }
    
   /**
     * Creates settings page
     */
    function inboundrocket_plugin_options()
    {
        $ir_options = get_option('inboundrocket_options');
        
        // Load onboarding for new plugin install
        if ( empty($ir_options['onboarding_complete']) || $ir_options['onboarding_complete'] == 0 ) {
            $this->inboundrocket_plugin_onboarding();
        } else {
	        inboundrocket_track_plugin_activity("Loaded Settings Page");
            $this->inboundrocket_plugin_settings();
        } 
    }

    /**
     * Creates premium page
     */
    function inboundrocket_premium_upgrade_page ()
    {
       global  $wp_version;
       
       inboundrocket_track_plugin_activity("Loaded Premium Page");

        echo '<div id="inboundrocket" class="ir-premium wrap '. ( $wp_version < 3.8 && !is_plugin_active('mp6/mp6.php') ? 'pre-mp6' : ''). '">';
        
        $this->inboundrocket_header('Inbound Rocket Premium (soon)');
        
        ?>
        <p><?php _e('Inbound Rocket Premium will give you all the goodies of Inbound Rocket and more','inbound-rocket');?>... </p>
        <p><?php _e('Currently your Inbound Rocket installation offers you already some great goodies','inbound-rocket');?>:</p>
            <div class="featurelist">   
                <div class="content">
                    <ul class="features">
                        <li><?php _e('Contacts Tracking','inbound-rocket');?></li>
                        <p><?php _e('Learn more about your visitors.','inbound-rocket');?></p>
                        <li><?php _e('Contacts Analytics','inbound-rocket');?></li>
                        <p><?php _e('Find out what content and traffic sources convert the best.','inbound-rocket');?></p>
                        <li><?php _e('Helpful power-ups','inbound-rocket');?></li>
                        <p><?php _e('Things like our Click To Tweet, Selection Sharer, and Welcome Bar functionality will help you get more traffic and convert your visitors more easily.','inbound-rocket');?></p>
                    </ul>
                </div>
            </div>
            <p><?php _e('However we got more in store for you, keep an eye out on','inbound-rocket');?> <a href='//inboundrocket.co/?utm_source=<?php echo $_SERVER['HTTP_HOST']; ?>&utm_medium=plugin-admin&utm_campaign=premium-upgrade' title='<?php _e('Not using Inbound Rocket yet? - You write. We\'ll turn them into leads','inbound-rocket');?>' target='_blank'><?php _e('our blog','inbound-rocket');?></a> <?php _e('or','inbound-rocket');?> <a href='https://twitter.com/inboundrocket' target='_blank' title='Inbound Rocket on Twitter'><?php _e('our Twitter','inbound-rocket');?></a> <?php _e('or','inbound-rocket');?> <a href='https://www.facebook.com/inboundrocket' target='_blank' title='Inbound Rocket on Facebook'>Facebook</a> <?php _e('to be amongst the first to find out!','inbound-rocket');?></p>
			<div style="clear:both"></div>
            
        <?php

        $this->inboundrocket_footer();

        //end wrap
        echo '</div>';

    }  

    /**
     * Creates onboarding settings page
     */
    function inboundrocket_plugin_onboarding ()
    {
        global  $wp_version;

        inboundrocket_track_plugin_activity("Loaded On-boarding Page");
        $ir_options = get_option('inboundrocket_options');
                
        echo '<div id="inboundrocket" class="ir-onboarding wrap '. ( $wp_version < 3.8 && !is_plugin_active('mp6/mp6.php') ? 'pre-mp6' : ''). '">';
    
        $this->inboundrocket_header('Inbound Rocket Plugin Setup','text-center');
        ?>
        <div class="onboarding-steps">
		<?php if ( ! isset($_GET['activate_powerup']) ) : ?>

            <?php if ( $ir_options['onboarding_step'] == 1 ) : ?>

                <?php inboundrocket_track_plugin_activity('Onboarding Step 2 - Choose your preferred way of tracking visitors'); ?>

                <ol class="onboarding-steps-names">
                    <li class="onboarding-step-name completed"><?php _e('Activate Inbound Rocket','inbound-rocket');?></li>
                    <li class="onboarding-step-name active"><?php _e('Setup up visitor tracking','inbound-rocket');?></li>
                    <li class="onboarding-step-name"><?php _e('Get Contact Reports','inbound-rocket');?></li>
                    <li class="onboarding-step-name"><?php _e('Activate Power-Ups','inbound-rocket');?></li>
                </ol>
                <div class="onboarding-step">
                    <h2 class="onboarding-step-title"><?php _e('How would you like us to track your visitors?','inbound-rocket');?></h2>
                    <div class="onboarding-step-content">
                        <p class="onboarding-step-description"><strong>Inbound Rocket</strong> <?php _e('can track your visitors in two different ways, using normal HTTP Cookies or using a more advanced technique using Evercookies.','inbound-rocket');?></p>
                        <p class="onboarding-step-description"><?php _e('Both of them have their advantages and disadvantages. If you\'re not sure which to choose, you can read our support article on <a href="https://inboundrocket.co/support/how-the-inbound-rocket-code-works/what-is-evercookie-and-why-do-you-need-it/" target="_blank">why you need Evercookies</a>, to help you make a better decision for your organization.','inbound-rocket');?></p>
                        <p class="onboarding-step-description"><?php _e('To track my visitors I would like to make use of:','inbound-rocket');?>
                        <form id="ir-onboarding-form" action="options.php" method="post">
	                        <input id="onboarding_step" type="hidden" name="inboundrocket_options[onboarding_step]" value="2" />
	                        <input id="ir_enable_dashboard_widget" type="hidden" name="inboundrocket_visitor_options[ir_enable_dashboard_widget]" value="1" />
	                        <input id="ir_auto_cleanup_days" type="hidden" name="inboundrocket_visitor_options[ir_auto_cleanup_days]" value="0" />
							<input id="ir_auto_cleanup_visits" type="hidden" name="inboundrocket_visitor_options[ir_auto_cleanup_visits]" value="0" />
							<input id="ir_updates_subscription" type="hidden" name="inboundrocket_visitor_options[ir_updates_subscription]" value="<?php if( isset($ir_options['ir_updates_subscription'] ) ) echo esc_attr($ir_options['ir_updates_subscription']);?>" />
							<input id="ir_email" type="hidden" name="inboundrocket_visitor_options[ir_email]" value="<?php if( isset($ir_options['ir_email'] ) ) echo esc_attr($ir_options['ir_email']);?>" />
	                        <?php
		                        $this->print_hidden_settings_fields();
		                    ?>
							<?php settings_fields('inboundrocket_options'); ?>
							<?php do_settings_sections(__FILE__); ?>
							<div>
								<select name="inboundrocket_visitor_options[ir_enable_evercookie]">
                            		<option value="0"><?php _e('HTTP Cookies','inbound-rocket');?></option>
                            		<option value="1"><?php _e('Evercookie','inbound-rocket');?></option>
                            	</select>
                            </div>
                            <input type="submit" name="submit" id="submit" class="button button-primary button-big" value="<?php esc_attr_e( __('Save tracking preferences','inbound-rocket')); ?>">
                        </form>
                    
                    
                    </div>
                </div>
          
            <?php elseif ( $ir_options['onboarding_step'] == 2 ) : ?>
                <?php inboundrocket_track_plugin_activity('Onboarding Step 3 - Get Contact Reports'); ?>

                <ol class="onboarding-steps-names">
                    <li class="onboarding-step-name completed"><?php _e('Activate Inbound Rocket','inbound-rocket');?></li>
                    <li class="onboarding-step-name completed"><?php _e('Setup up visitor tracking','inbound-rocket');?></li>
                    <li class="onboarding-step-name active"><?php _e('Get Contact Reports','inbound-rocket');?></li>
                    <li class="onboarding-step-name"><?php _e('Activate Power-Ups','inbound-rocket');?></li>
                </ol>
                <div class="onboarding-step">
                    <h2 class="onboarding-step-title"><?php _e('Where should we send your contact reports?','inbound-rocket');?></h2>
                    <div class="onboarding-step-content">
                        <p class="onboarding-step-description"><strong>Inbound Rocket</strong> <?php _e('will help you get to know your website visitors by sending you a report including traffic source and pageview history each time a visitor fills out a form.','inbound-rocket');?></p>
                        <form id="ir-onboarding-form" method="post" action="<?=admin_url('options.php');?>">
	                        <input id="onboarding_step" type="hidden" name="inboundrocket_options[onboarding_step]" value="3" />
		                    <input id="ir_enable_evercookie" type="hidden" name="inboundrocket_visitor_options[ir_enable_evercookie]" value="<?php if( isset($ir_options['ir_enable_evercookie'] ) ) echo esc_attr($ir_options['ir_enable_evercookie']);?>" />
		                    <input id="ir_evercookie_corrupted" type="hidden" name="inboundrocket_options[ir_evercookie_corrupted]" value="0" />
		                    <input id="ir_enable_evercookie_status" type="hidden" name="inboundrocket_options[ir_enable_evercookie_status]" value="<?php if($ir_options['ir_enable_evercookie']==1) echo 'enabled'; else echo 'disabled'; ?>" />
		                    <input id="ir_enable_dashboard_widget" type="hidden" name="inboundrocket_visitor_options[ir_enable_dashboard_widget]" value="1" />
		                    <input id="ir_auto_cleanup_days" type="hidden" name="inboundrocket_visitor_options[ir_auto_cleanup_days]" value="0" />
							<input id="ir_auto_cleanup_visits" type="hidden" name="inboundrocket_visitor_options[ir_auto_cleanup_visits]" value="0" />
							<input id="ir_updates_subscription" type="hidden" name="inboundrocket_visitor_options[ir_updates_subscription]" value="<?php if( isset($ir_options['ir_updates_subscription'] ) ) echo esc_attr($ir_options['ir_updates_subscription']);?>" />
							<input id="ir_email" type="hidden" name="inboundrocket_visitor_options[ir_email]" value="<?php if( isset($ir_options['ir_email'] ) ) echo esc_attr($ir_options['ir_email']);?>" />

	                        <?php
		                        $this->print_hidden_settings_fields();
		                    ?>
							<?php settings_fields('inboundrocket_options'); ?>
							<div>
                            <?php $this->ir_email_callback(); ?>
                            <?php if ( function_exists('curl_init') && function_exists('curl_setopt') ) : ?>
                                    <br />
                            <?php $this->ir_subscription_callback(); ?>
                            <?php endif; ?>
							</div>
                            <input type="submit" name="submit" id="submit" class="button button-primary button-big" value="<?php esc_attr_e( __('Save Email','inbound-rocket')); ?>">
                        </form>
                    </div>
                </div>

            <?php elseif ( $ir_options['onboarding_step'] == 3 ) : ?>
								
                <?php 
	                
	                inboundrocket_track_plugin_activity('Onboarding Step 4 - Activate Power-Ups');
	                $ir_options['onboarding_step'] = 4;
	                inboundrocket_update_option('inboundrocket_options', $ir_options); 
	                
                ?>

				<ol class="onboarding-steps-names">
                    <li class="onboarding-step-name completed"><?php _e('Activate Inbound Rocket','inbound-rocket');?></li>
                    <li class="onboarding-step-name completed"><?php _e('Setup up visitor tracking','inbound-rocket');?></li>
                    <li class="onboarding-step-name completed"><?php _e('Get Contact Reports','inbound-rocket');?></li>
                    <li class="onboarding-step-name active"><?php _e('Activate Power-Ups','inbound-rocket');?></li>
                </ol>
                <div class="onboarding-step">
                    <h2 class="onboarding-step-title"><?php _e('Grow your visitors and your contacts!','inbound-rocket');?><br><small><?php _e('We have developed some powerful tools which will help convert leads and grow visitors. Go ahead and enable some power-ups below:','inbound-rocket');?></small></h2>
                    <form id="ir-onboarding-form" method="post" action="<?=admin_url('options.php');?>">
	                    <input id="onboarding_step" type="hidden" name="inboundrocket_options[onboarding_step]" value="4" />
	                    <input id="ir_enable_evercookie" type="hidden" name="inboundrocket_visitor_options[ir_enable_evercookie]" value="<?php if( isset($ir_options['ir_enable_evercookie'] ) ) echo esc_attr($ir_options['ir_enable_evercookie']);?>" />
	                    <input id="ir_evercookie_corrupted" type="hidden" name="inboundrocket_options[ir_evercookie_corrupted]" value="0" />
		                <input id="ir_enable_evercookie_status" type="hidden" name="inboundrocket_options[ir_enable_evercookie_status]" value="<?php if($ir_options['ir_enable_evercookie']==1) echo 'enabled'; else echo 'disabled'; ?>" />
		                <input id="ir_enable_dashboard_widget" type="hidden" name="inboundrocket_visitor_options[ir_enable_dashboard_widget]" value="1" />
		                <input id="ir_auto_cleanup_days" type="hidden" name="inboundrocket_visitor_options[ir_auto_cleanup_days]" value="0" />
						<input id="ir_auto_cleanup_visits" type="hidden" name="inboundrocket_visitor_options[ir_auto_cleanup_visits]" value="0" />
							<input id="ir_updates_subscription" type="hidden" name="inboundrocket_visitor_options[ir_updates_subscription]" value="<?php if( isset($ir_options['ir_updates_subscription'] ) ) echo esc_attr($ir_options['ir_updates_subscription']);?>" />
							<input id="ir_email" type="hidden" name="inboundrocket_visitor_options[ir_email]" value="<?php if( isset($ir_options['ir_email'] ) ) echo esc_attr($ir_options['ir_email']);?>" />
                        <?php $this->print_hidden_settings_fields();  ?>
                        <div class="popup-options">
	                    	<h2 class="onboarding-step-title"><?php _e('Sharing Power Ups','inbound-rocket');?></h2>
							<label class="popup-option">
                            	<input type="checkbox" name="powerups" value="selection_sharer"><?php _e('Selection Sharer','inbound-rocket');?>
                                <img src="<?php echo INBOUNDROCKET_PATH ?>/img/power-ups/power-up-icon-selection-sharer@2x.png">
                                <p><?php _e('Medium like popover menu to share on Twitter, LinkedIn, Facebook or by email any text selected on the page.','inbound-rocket');?></p>
                            </label>
                            <label class="popup-option">
                                <input type="checkbox" name="powerups" value="click_to_tweet"><?php _e('Click To Tweet','inbound-rocket');?>
                                <img src="<?php echo INBOUNDROCKET_PATH ?>/img/power-ups/power-up-icon-click-to-tweet@2x.png">
                                <p><?php _e('Click-2-Tweet will enable your visitors to click important sections of your content and tweet it directly from your website.','inbound-rocket');?></p>
                            </label>
                        </div>
                        <div class="popup-options">
	                        <h2 class="onboarding-step-title"><?php _e('Lead Converting Power Ups','inbound-rocket');?></h2>
	                        <label class="popup-option">
                                <input type="checkbox" name="powerups" value="welcome_bar"><?php _e('Welcome Bar','inbound-rocket');?>
                                <img src="<?php echo INBOUNDROCKET_PATH ?>/img/power-ups/power-up-icon-welcome-bar@2x.png">
                                <p><?php _e('Welcome Bar sits beautifully at the top of your website, is an easy and non-intrusive way to ask people to join your email list.','inbound-rocket');?></p>
                            </label>
							<label class="popup-option">
                                <input type="checkbox" name="powerups" value="scroll_boxes"><?php _e('Scroll Boxes','inbound-rocket');?>
                                <img src="<?php echo INBOUNDROCKET_PATH ?>/img/power-ups/power-up-icon-scroll-boxes@2x.png">
                                <p><?php _e('Scroll Boxes: a polite way to ask your visitors for their email address as they finish reading your latest blog post or learning about your product.','inbound-rocket');?></p>
                            </label>
                            <label class="popup-option">
                                <input type="checkbox" name="powerups" value="welcome_mat"><?php _e('Welcome Mat','inbound-rocket');?>
                                <img src="<?php echo INBOUNDROCKET_PATH ?>/img/power-ups/power-up-icon-welcome-mat@2x.png">
                                <p><?php _e('Welcome Mat displays a full-screen call to action that shows when visitors land on your site. Encourage your visitors to join your email list, check out your popular blog post … the possibilities are endless!','inbound-rocket');?></p>
                            </label>
                        </div>
                        <div class="popup-options">
	                        <h2 class="onboarding-step-title"><?php _e('Mailing List Connectors','inbound-rocket');?></h2>                        
                            <label class="popup-option">
                                <input type="checkbox" name="powerups" value="mailchimp_connector"><?php _e('MailChimp Connector','inbound-rocket');?>
                                <img src="<?php echo INBOUNDROCKET_PATH ?>/img/power-ups/power-up-icon-mailchimp-connector@2x.png">
                                <p><?php _e('Push your contacts to your MailChimp email lists.','inbound-rocket');?></p>
                            </label>
                            <label class="popup-option">
                                <input type="checkbox" name="powerups" value="aweber_connector"><?php _e('AWeber Connector','inbound-rocket');?>
                                <img src="<?php echo INBOUNDROCKET_PATH ?>/img/power-ups/power-up-icon-aweber-connector@2x.png">
                                <p><?php _e('Push your contacts to your AWeber email lists.','inbound-rocket');?></p>
                            </label> 
                            <label class="popup-option">
                                <input type="checkbox" name="powerups" value="campaign_monitor_connector"><?php _e('Campaign Monitor Connector','inbound-rocket');?>
                                <img src="<?php echo INBOUNDROCKET_PATH ?>/img/power-ups/power-up-icon-campaign-monitor-connector@2x.png">
                                <p><?php _e('Push your contacts to your Campaign Monitor email lists.','inbound-rocket');?></p>
                            </label>
                            <label class="popup-option">
                                <input type="checkbox" name="powerups" value="postmatic_connector"><?php _e('Postmatic Connector','inbound-rocket');?>
                                <img src="<?php echo INBOUNDROCKET_PATH ?>/img/power-ups/power-up-icon-postmatic-connector@2x.png">
                                <p><?php _e('Push your contacts to your Postmatic email lists.','inbound-rocket');?></p>
                            </label>                                                        
                        </div>
                        <a id="btn-activate-subscribe" href="<?php echo admin_url('admin.php?page=inboundrocket_settings&inboundrocket_action=activate&redirect_to=' . urlencode(admin_url('admin.php?page=inboundrocket_settings&activate_powerup=true'))); ?>&power_up=selection_sharer" class="button button-primary button-big"><?php esc_attr_e(__('Activate these power-ups','inbound-rocket'));?></a>
                        <p><a href="<?php echo admin_url('admin.php?page=inboundrocket_settings&activate_powerup=false'); ?>"><?php _e('Don\'t activate power-ups right now','inbound-rocket');?></a></p>
                    </form>
                </div>
                
                <script type="text/javascript">
	                jQuery(document).ready(function($){
		                jQuery('input[name=powerups]').change(function() {
							var url = jQuery('#btn-activate-subscribe').attr('href');
							if( jQuery(this).is(":checked") ) {
								url = url + ','+jQuery(this).val();
								console.log(url);
								jQuery('#btn-activate-subscribe').prop('href',url);
							} else {
								var url = jQuery('#btn-activate-subscribe').attr('href');
								url = url.replace(','+jQuery(this).val(),'');
								url = url.replace(jQuery(this).val(),'');
								jQuery('#btn-activate-subscribe').prop('href',url);
							}
						});
					});
	            </script>
	            
	        <?php elseif ( $ir_options['onboarding_step'] == 4 ) : ?>
	        
	        <?php
	            $ir_options['onboarding_complete'] = 1;
	            inboundrocket_update_option('inboundrocket_options', $ir_options); 
	            
	            if($ir_options['ir_enable_evercookie']) {
		            inboundrocket_install_evercookie();
	            }
	            
	            inboundrocket_track_plugin_activity('Onboarding Complete');
            ?>

            <ol class="onboarding-steps-names">
                <li class="onboarding-step-name completed"><?php _e('Activate Inbound Rocket','inbound-rocket');?></li>
                <li class="onboarding-step-name completed"><?php _e('Setup up visitor tracking','inbound-rocket');?></li>
                <li class="onboarding-step-name completed"><?php _e('Get Contact Reports','inbound-rocket');?></li>
                <li class="onboarding-step-name completed"><?php _e('Activate Power-Ups','inbound-rocket');?></li>
            </ol>
            <div class="onboarding-step">
                <h2 class="onboarding-step-title"><?php _e('Setup Complete!','inbound-rocket');?><br><small><?php _e('Inbound Rocket is waiting for your first form submission.','inbound-rocket');?></small></h2>
                <div class="onboarding-step-content">
                    <p class="onboarding-step-description"><?php _e('Inbound Rocket is setup and waiting for a form submission.','inbound-rocket');?><br /> <?php _e('Once Inbound Rocket detects a form submission, a new contact will be added to your contacts list. We recommend filling out a form on your site to test that Inbound Rocket is working correctly.','inbound-rocket');?></p><p class="onboarding-step-description"><?php if (isset($_GET['activate_powerup']) && $_GET['activate_powerup']=='true') { ?><?php _e('Next to our basic form and visitor tracking the following power-ups are also activated','inbound-rocket');?>:<br /><?php _e('Press "Complete Setup" and configure your power-ups.','inbound-rocket');?><?php } ?></p>
                        <a href="<?php echo admin_url('admin.php?page=inboundrocket_settings'); ?>" class="button button-primary button-big"><?php esc_attr_e(__('Complete Setup','inbound-rocket')); ?></a>
                </div>
            </div>

            <?php endif; ?>

        <?php else : ?>
			
            <?php
	            $ir_options['onboarding_complete'] = 1;
	            inboundrocket_update_option('inboundrocket_options', $ir_options);
	            inboundrocket_track_plugin_activity('Onboarding Complete');
            ?>

            <ol class="onboarding-steps-names">
                <li class="onboarding-step-name completed"><?php _e('Activate Inbound Rocket','inbound-rocket');?></li>
                <li class="onboarding-step-name completed"><?php _e('Setup up visitor tracking','inbound-rocket');?></li>
                <li class="onboarding-step-name completed"><?php _e('Get Contact Reports','inbound-rocket');?></li>
                <li class="onboarding-step-name completed"><?php _e('Activate Power-Ups','inbound-rocket');?></li>
            </ol>
            <div class="onboarding-step">
                <h2 class="onboarding-step-title"><?php _e('Setup Complete!','inbound-rocket');?><br><small><?php _e('Inbound Rocket is waiting for your first form submission.','inbound-rocket');?></small></h2>
                <div class="onboarding-step-content">
                    <p class="onboarding-step-description"><?php _e('Inbound Rocket is setup and waiting for a form submission.','inbound-rocket');?><br /> <?php _e('Once Inbound Rocket detects a form submission, a new contact will be added to your contacts list. We recommend filling out a form on your site to test that Inbound Rocket is working correctly.','inbound-rocket');?></p><p class="onboarding-step-description"><?php if (isset($_GET['activate_powerup']) && $_GET['activate_powerup']=='true') { ?><?php _e('Next to our basic form and visitor tracking the following power-ups are also activated','inbound-rocket');?>:<br /><?php _e('Press "Complete Setup" and configure your power-ups.','inbound-rocket');?><?php } ?></p>
                        <a href="<?php echo admin_url('admin.php?page=inboundrocket_settings'); ?>" class="button button-primary button-big"><?php esc_attr_e(__('Complete Setup','inbound-rocket')); ?></a>
                </div>
            </div>

        <?php endif; ?>
        
        </div>

        
        <div class="onboarding-steps-help">
            <h4><?php _e('Any questions?','inbound-rocket');?></h4>
            <?php if ( isset($ir_options['premium']) && $ir_options['premium'] ) : ?>
                <p><?php _e('Send us a message and we’re happy to help you get set up.','inbound-rocket');?></p>
                <a class="button" href="mailto:help@inboundrocket.co?Subject=Hello" title="<?php _e('Contact Inbound Rocket Support.','inbound-rocket');?>"><?php _e('Mail with us','inbound-rocket');?></a>
            <?php else : ?>
                <p><?php _e('Leave us a message in the WordPress support forums. We\'re always happy to help you get set up and can answer any questions there.','inbound-rocket');?></p>
                <a class="button" href="https://wordpress.org/support/plugin/inbound-rocket" target="_blank"><?php _e('Go to Forums','inbound-rocket');?></a>
            <?php endif; ?>
        </div>
        

        <?php
        
        $this->inboundrocket_footer();

        //end wrap
        echo '</div>';
    }
	
	function plugin_options_tabs() {
	    $current_tab = isset( $_GET['tab'] ) ? esc_attr($_GET['tab']) : 'inboundrocket_options';
		
	    //screen_icon(); Depreciated v3.8
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
	        $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
	        echo '<a class="nav-tab ' . $active . '" href="'.admin_url('admin.php?page=inboundrocket_settings&tab=' . $tab_key).'">' . $tab_caption . '</a>';
	    }
	    echo '</h2>';
	}
	
    /**
     * Creates default settings page
     */
    function inboundrocket_plugin_settings()
    {
        global  $wp_version;
                
        echo '<div id="inboundrocket" class="ir-settings wrap '. ( $wp_version < 3.8 && !is_plugin_active('mp6/mp6.php') ? 'pre-mp6' : ''). '">';
        
        $this->inboundrocket_header(__('Inbound Rocket Settings','inbound-rocket'));
        
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'inboundrocket_visitor_options';

        $this->plugin_options_tabs();
        //@TODO if no module activated display different message
        
        ?>
	        <div id="loader-bg"></div>
	        <div id="saveResult"></div>
            <form method="post" action="<?=admin_url('options.php');?>" id="ir_form_options">
	        <?php 
		        $this->print_hidden_settings_fields();
                settings_fields($tab);
                do_settings_sections($tab);
                submit_button('Save Settings');
            ?>
            </form>
        <?php
        $this->inboundrocket_footer();

        //end wrap
        echo '</div>';
    }	

    /**
     * Prints email input for settings page
     */
    function ir_email_callback ()
    {
        $options = get_option('inboundrocket_visitor_options');
        $ir_email = empty($options['ir_email']) ? esc_attr(get_option('admin_email')) : esc_attr($options['ir_email']);
     
        printf(
            '<input id="ir_email" type="text" name="inboundrocket_visitor_options[ir_email]" value="%s" size="50"/><br/><span class="description">'. __('Separate multiple emails with commas. Leave blank to disable email notifications','inbound-rocket').'.</span>',
            $ir_email
        );    
    }	

    /**
     * Creates power-up page
     */
    function inboundrocket_power_ups_page ()
    {
        global  $wp_version;
        
        inboundrocket_track_plugin_activity("Viewed Power-ups Page");

        echo '<div id="inboundrocket" class="ir-settings wrap '. ( $wp_version < 3.8 && !is_plugin_active('mp6/mp6.php') ? 'pre-mp6' : ''). '">';
        
        $this->inboundrocket_header(__('Inbound Rocket Power-ups','inbound-rocket'));
        
        ?>
            <p><?php _e('Get the most out of your Inbound Rocket installation by installing one of the below power-ups.','inbound-rocket');?></p>
            
            <h2 class="powerup-title"><?php _e('Core Power Ups', 'inbound-rocket'); ?></h2>
            <ul class="powerup-list">
	            <?php $power_up_count = 0; ?>
                <?php foreach ( $this->admin_power_ups as $power_up ) : ?>
                    <?php 
                        // Skip displaying the power-up on the power-ups page if it's hidden
                        if ( $power_up->hidden )
                            continue;
                            
                        if ( $power_up_count == 1 ) :
                    ?>
					<!-- static content stats power-up - not a real power-up and this is a hack to put it second in the order -->
                        <li class="powerup activated">
                            <div class="img-container">
                                <img src="<?php echo INBOUNDROCKET_PATH; ?>/img/power-ups/power-up-icon-analytics@2x.png" height="120px" width="200px">
                            </div>
                            <h2><?php _e('Statistics','inbound-rocket');?></h2>
                            <p><?php _e('Get some nice graphs to show you how you are doing.','inbound-rocket');?></p>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=inboundrocket_stats')); ?>" class="button button-large"><?php _e('View Stats','inbound-rocket');?></a>
                        </li>
                        
                        <li class="powerup activated">
		                    <div class="img-container">
		                        <img src="<?php echo INBOUNDROCKET_PATH; ?>/img/power-ups/power-up-icon-ideas@2x.png" height="120px" width="200px">
		                    </div>
		                    <h2><?php _e('Suggestions?','inbound-rocket');?></h2>
		                    <p><?php _e('Have an idea for a power-up? We\'d love to hear it!','inbound-rocket');?></p>
		                    <a href="mailto:ideas@inboundrocket.co" target="_blank" class="button button-primary button-large"><?php _e('Give us a shout','inbound-rocket');?></a>
		                </li>
                    <?php
                        endif;
						
						
						switch($power_up->slug){
								case "selection_sharer":
									$abbrev = "ss";
								break;
								case "exit_intent":
									$abbrev = "ei";
								break;
								case "click_to_tweet":
									$abbrev = "ctt";
								break;
								case "welcome_bar":
									$abbrev = "wb";
								break;
								case "welcome_bar_pro":
									$abbrev = "wbp";
								break;
								case "welcome_mat":
									$abbrev = "wm";
								break;
								case "scroll_boxes":
									$abbrev = "sb";
								break;
								case "mailchimp_connector":
									$abbrev = "mc";
								break;
								case "campaign_monitor_connector":
									$abbrev = "cm";
								break;
								case "aweber_connector":
									$abbrev = "aw";
								break;
								case "postmatic_connector":
									$abbrev = "pm";
								break;
								case "gdpr_compliance":
									$abbrev = "gdpr";
								break;
								case "form_builder":
									$abbrev = "fm";
								break;
						}
					
					if( $power_up->slug == 'welcome_bar' || $power_up->slug == 'welcome_bar_pro') : ?>
            			</ul>
            			<h2 class="powerup-title"><?php _e('Lead Converting Power Ups','inbound-rocket'); ?></h2>
            			<ul class="powerup-list">
                    <?php elseif( $power_up->slug == 'selection_sharer') : ?>
            			</ul>
                    	<h2 class="powerup-title"><?php _e('Sharing Power Ups','inbound-rocket'); ?></h2>
                    	<ul class="powerup-list">
	                 <?php elseif( $power_up->slug == 'gdpr_compliance' || $power_up->slug == 'form_builder') : ?>
            			</ul>
                    	<h2 class="powerup-title"><?php _e('General','inbound-rocket'); ?></h2>
                    	<ul class="powerup-list">
                     <?php elseif( $power_up->slug == 'mailchimp_connector') : ?>
                        </ul>
                    	<h2 class="powerup-title"><?php _e('Mailing List Connectors','inbound-rocket'); ?></h2>
                    	<ul class="powerup-list">
                    <?php endif; ?>
                    
                             
					<li class="powerup <?php echo ( $power_up->activated ? 'activated' : ''); ?>">
	                    <div class="img-container">
	                        <?php if ( strstr($power_up->icon, 'dashicon') ) : ?>
	                            <span class="<?php echo $power_up->icon; ?>"></span>
	                        <?php else : ?>
	                            <img src="<?php echo INBOUNDROCKET_PATH . '/img/power-ups/' . $power_up->icon . '@2x.png'; ?>" height="120px" width="200px"/>
	                        <?php endif; ?>
	                    </div>
	                    <h2><?php echo $power_up->power_up_name; ?></h2>
	                    <p><?php echo $power_up->description; ?></p>
	                    <p><a href="<?php echo $power_up->link_uri; ?>" target="_blank"><?php _e('Learn more','inbound-rocket');?></a></p>  
	
	                    <?php if ( $power_up->activated ) : ?>
	                        <?php if ( ! $power_up->permanent ) : ?>
	                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=inboundrocket_power_ups&inboundrocket_action=deactivate&power_up=' . $power_up->slug)); ?>&redirect_to=<?=urlencode(admin_url("admin.php?page=inboundrocket_settings&tab=inboundrocket_".$abbrev."_options"));?>" class="button button-secondary button-large"><?php _e('Deactivate','inbound-rocket');?></a>
	                        <?php endif; ?>
	                    <?php else : ?>
	                        <?php if ( ( $power_up->curl_required && function_exists('curl_init') && function_exists('curl_setopt') ) || ! $power_up->curl_required ) : ?>
	                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=inboundrocket_power_ups&inboundrocket_action=activate&power_up=' . $power_up->slug)); ?>&redirect_to=<?=urlencode(admin_url("admin.php?page=inboundrocket_settings&tab=inboundrocket_".$abbrev."_options"));?>" class="button button-primary button-large"><?php _e('Activate','inbound-rocket');?></a>
	                        <?php else : ?>
	                            <p><a href="http://stackoverflow.com/questions/2939820/how-to-enable-curl-installed-ubuntu-lamp-stack" target="_blank"><?php _e('Install cURL','inbound-rocket');?></a> <?php _e('to use this power-up.','inbound-rocket');?></p>
	                        <?php endif; ?>
	                    <?php endif; ?>
	
	                    <?php if ( $power_up->activated || $power_up->permanent ) : ?>
	                        <?php if ( $power_up->menu_link == 'contacts' ) : ?>
	                            <a href="<?php echo admin_url('admin.php?page=inboundrocket_' . $power_up->menu_link); ?>" class="button button-secondary button-large"><?php _e('View Contacts','inbound-rocket');?></a>
	                            <a href="<?php echo admin_url('admin.php?page=inboundrocket_settings'); ?>" class="button button-secondary button-large"><?php _e('Configure','inbound-rocket');?></a>
	                        <?php else : ?>
	                            <a href="<?php echo admin_url('admin.php?page=inboundrocket_' . $power_up->menu_link.'&tab='.$power_up->options_name); ?>" class="button button-secondary button-large"><?php _e('Configure','inbound-rocket');?></a>
	                        <?php endif; ?>
	                    <?php endif; ?>
					</li>

                 <?php $power_up_count++; ?>
                <?php endforeach; ?>
               
            </ul>
			<div style="clear:both"></div>
        <?php
        $this->inboundrocket_footer();
        
        //end wrap
       echo '</div>';
    }

    function check_admin_action ( )
    {
        if ( isset( $_GET['inboundrocket_action'] ) ) 
        {	        
            switch ( $_GET['inboundrocket_action'] ) 
            {
                case 'activate' :

                    $power_up = esc_attr( $_GET['power_up'] );
                    
                    if( strpos($power_up, ',') !== false ){
	                
	                    $power_ups = explode(',',$power_up);
	                    foreach($power_ups as $power_up){
	                    	WPInboundRocket::activate_power_up( $power_up, FALSE );
	                    	inboundrocket_track_plugin_activity($power_up . " power-up activated");
	                    }
                    
                    } else {
                    
                    	WPInboundRocket::activate_power_up( $power_up, FALSE );
                    	inboundrocket_track_plugin_activity($power_up . " power-up activated");
                    
                    }
										
                    if ( !empty($_GET['redirect_to']) )
                        wp_redirect(urldecode($_GET['redirect_to']));
						
                    else
                        wp_redirect(admin_url('admin.php?page=inboundrocket_power_ups'));
                    
					exit;

                    break;

                case 'deactivate' :

                    $power_up = esc_attr( $_GET['power_up'] );
                    
                    if( strpos($power_up, ',') !== false ){
                    
                    	$power_ups = explode(',',$power_up);
	                    foreach($power_ups as $power_up){
	                    	WPInboundRocket::deactivate_power_up( $power_up, FALSE );
	                    	inboundrocket_track_plugin_activity($power_up . " power-up deactivated");
	                    }
                    	
					} else {
						
						WPInboundRocket::deactivate_power_up( $power_up, FALSE );
						inboundrocket_track_plugin_activity($power_up . " power-up deactivated");
						
					}
 
                    wp_redirect(admin_url('admin.php?page=inboundrocket_power_ups'));
                    exit;

                    break;
            }
        }
    }
	
	//=============================================
    // Admin Styles & Scripts
    //=============================================

    /**
     * Adds admin style sheets
     */
    function add_inboundrocket_admin_styles ()
    {
        $screen = get_current_screen();
        
        if ( in_array( $screen->id, array( 'inbound-rocket_page_inboundrocket_power_ups', 'toplevel_page_inboundrocket_stats', 'inbound-rocket_page_inboundrocket_settings', 'inbound-rocket_page_inboundrocket_contacts', 'inbound-rocket_page_inboundrocket_lead_lists', 'inbound-rocket_page_inboundrocket_premium_upgrade' ) ) ) {
	        if (INBOUNDROCKET_ENABLE_DEBUG==true) { wp_register_style('inboundrocket-admin-css', INBOUNDROCKET_PATH . '/admin/inc/css/inboundrocket-admin.css');    
	        } else { wp_register_style('inboundrocket-admin-css', INBOUNDROCKET_PATH . '/admin/inc/css/inboundrocket-admin.min.css');}
			wp_enqueue_style('inboundrocket-admin-css');
			
			if (INBOUNDROCKET_ENABLE_DEBUG==true) { wp_register_style('inboundrocket-select2', INBOUNDROCKET_PATH . '/inc/assets/css/select2.css');
			} else { wp_register_style('inboundrocket-select2', INBOUNDROCKET_PATH . '/inc/assets/css/select2.min.css'); }
			wp_enqueue_style('inboundrocket-select2');
			
			wp_enqueue_style('thickbox');
        }
    }
    
    /**
     * Adds admin javascript
    */
    
    function add_inboundrocket_admin_scripts ()
    {
        global $pagenow;

        if ( ($pagenow == 'admin.php' && isset($_GET['page']) && strstr($_GET['page'], 'inboundrocket')) ) 
        {
	        if($_GET['page'] != 'inboundrocket_settings') {
		        wp_register_script('inboundrocket-charts-js', INBOUNDROCKET_PATH . '/admin/inc/js/Chart.bundle.min.js', array( 'jquery' ), FALSE, TRUE);
				wp_enqueue_script('inboundrocket-charts-js');
	        }
	        if (INBOUNDROCKET_ENABLE_DEBUG==true) { 
		        wp_register_script('inboundrocket-select2-js', INBOUNDROCKET_PATH . '/admin/inc/js/select2.full.js', array( 'jquery' ), FALSE, TRUE);
		    } else { 
			    wp_register_script('inboundrocket-select2-js', INBOUNDROCKET_PATH . '/admin/inc/js/select2.full.min.js', array( 'jquery' ), FALSE, TRUE);
			}
	        wp_enqueue_script('inboundrocket-select2-js');

	        if (INBOUNDROCKET_ENABLE_DEBUG==true) { wp_register_script('inboundrocket-admin-js', INBOUNDROCKET_PATH . '/admin/inc/js/inboundrocket-admin.js', array ( 'jquery' ), FALSE, TRUE);
            } else { wp_register_script('inboundrocket-admin-js', INBOUNDROCKET_PATH . '/admin/inc/js/inboundrocket-admin.min.js', array ( 'jquery' ), FALSE, TRUE); }
            wp_enqueue_script('inboundrocket-admin-js');
            
            wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('jquery');
			
			wp_localize_script('inboundrocket-admin-js', 'ir_admin_ajax', array(
            	'ajax_url' => get_admin_url(NULL,'') . 'admin-ajax.php', 
            	'ir_nonce' => wp_create_nonce('ir-admin-nonce'),
            	'submitted'=>__('submitted','inbound-rocket'), 
            	'anyform'=>__('any form','inbound-rocket'), 
            	'addto'=>__('add to','inbound-rocket'),
            	'removefrom'=>__('remove from','inbound-rocket'),
            	'addlist'=>__('Add List','inbound-rocket')
            ));

        }
    }

    //=============================================
    // Internal Class Functions
    //=============================================  
    
     /**
     * Sends GDPR info to email address
     */
    function inboundrocket_send_gdpr_info() 
    {
	    global $wpdb;
	    
	 	if ( ! wp_verify_nonce($_POST['ir_nonce'], 'ir-nonce-verify') ) {
		 	die('Security check');
		} else {
			if(!empty($_POST['email'])) {
		    
			    $u_row = $wpdb->get_row( 
		        	$wpdb->prepare(
		        		"SELECT * FROM {$wpdb->ir_leads} WHERE lead_email = %d", 
		        		$_POST['email']
		        	) 
		        );
		        
		        $lead_hash = $u_row->lead_hash;
		        
		        $pv_rows = $wpdb->query( 
		        	$wpdb->prepare(
		        		"SELECT * FROM {$wpdb->ir_pageviews} WHERE lead_hashkey = %s", 
		        		$lead_hash
		        	)
		        );
		
		        $s_rows = $wpdb->query( 
		        	$wpdb->prepare(
		        		"SELECT * FROM {$wpdb->ir_submissions} WHERE lead_hashkey = %s", 
		        		$lead_hash
		        	)
		        );
		        
		        $personal_info['user_info'] = $u_row;
		        $personal_info['pageviews'] = $pv_rows;
		        $personal_info['submissions'] = $s_rows;
		    
		    	wp_mail( get_option('admin_email'), __('GDPR Data Export', 'inbound-rocket').': ' .$_POST['email'], json_encode($personal_info) );
				wp_send_json_success( __( 'success', 'inbound-rocket' ) );
		    }
		}
		exit;
    }
    
    
    /**
     * Creates postbox for admin
     *
     * @param string
     * @param string
     * @param string
     * @param bool
     * @return string   HTML for postbox
     */
    function inboundrocket_postbox ( $css_class, $title, $content, $handle = TRUE )
    {
        $postbox_wrap = "";
        
        $postbox_wrap .= '<div class="' . $css_class . ' inboundrocket-postbox">';
            $postbox_wrap .= '<h3 class="inboundrocket-postbox__header">' . esc_attr($title) . '</h3>';
            $postbox_wrap .= '<div class="inboundrocket-postbox__content">' . $content . '</div>';
        $postbox_wrap .= '</div>';

        return $postbox_wrap;
    }    
    /**
     * Prints the admin page title, icon and help notification
     *
     * @param string
     */
    function inboundrocket_header ( $page_title = '', $css_class = '' )
    {
        ?>
        <?php //screen_icon('inboundrocket'); Depreciated v3.8 ?>
        <div class="ir-content">
	        <div class="ir-frame">
		        <div class="header">
					<nav role="navigation" class="header-nav drawer-nav nav-horizontal">
						<ul class="main-nav">
							<li class="inboundrocket-logo"><a href="<?php echo admin_url('admin.php?page=inboundrocket_stats'); ?>" title="Inbound Rocket Stats"><span>Inbound Rocket</span></a></li>
						</ul>
					</nav>
				</div><!-- header -->
				<div class="clouds-sm"></div>
				<div class="wrapper">
		        <?php if(!empty($page_title)): ?><h2 class="<?php echo $css_class ?>"><?php echo $page_title ?></h2><?php endif; ?>
				<?php if ( 'ir-scroll-box' == get_post_type() ) { ?>
					<a href="/wp-admin/admin.php?page=inboundrocket_settings&tab=inboundrocket_sb_options" class="ir_settings_link">&laquo; <?php _e('Go back to','inbound-rocket'); ?>  scroll box <?php _e('settings','inbound-rocket'); ?></a>
				<?php } ?>
				<?php if ( 'ir-welcome-mat' == get_post_type() ) { ?>
					<a href="/wp-admin/admin.php?page=inboundrocket_settings&tab=inboundrocket_wm_options" class="ir_settings_link">&laquo; <?php _e('Go back to','inbound-rocket'); ?> welcome mat <?php _e('settings','inbound-rocket'); ?></a>
				<?php } ?>
		        <?php $options = get_option('inboundrocket_options'); ?>
		
		        <?php if ( isset($_GET['settings-updated']) && isset($options['onboarding_complete']) && $options['onboarding_complete']=='1' ) : ?>
		            <div id="message" class="updated">
		                <p><strong><?php _e('Settings saved', 'inbound-rocket'); ?>.</strong></p>
		            </div>
		        <?php endif;
    } 
    
    
    /*
	 * Prints the Footer
	 */
    function inboundrocket_footer ()
    {
        $ir_options = get_option('inboundrocket_options');
        ?>
			        </div><!-- .wrapper -->
			
			        <div class="footer">
				        <div class="fly"></div>
				        <nav class="primary nav-horizontal">
							<ul class="primary-footer">
								<li class="current"><a href="//inboundrocket.co/" target="_blank" title="Inbound Rocket">Inbound Rocket</a> <?php echo INBOUNDROCKET_PLUGIN_VERSION?></li>
							</ul>
						</nav><!-- .primary -->

						<nav class="secondary nav-horizontal">
							<ul class="secondary-footer">
								<?php if ( isset($ir_options['premium']) && $ir_options['premium'] ) : ?>
								<li>Need help? <a href="mailto:help@inboundrocket.co?Subject=Oops.." title="<?php _e('Contact Inbound Rocket Support.','inbound-rocket'); ?>" target="_blank"><?php _e('Contact Us','inbound-rocket'); ?></a></li>
								<?php else : ?>
								<li><a href="https://wordpress.org/support/plugin/inbound-rocket" target="_blank"><?php _e('Support forums', 'inbound-rocket'); ?></a></li>
								<?php endif; ?>
								<li><a href="//inboundrocket.co/blog/" title="<?php _e('Get product &amp; security updates','inbound-rocket'); ?>" target="_blank"><?php _e('Get product &amp; security updates','inbound-rocket'); ?></a></li>
								<li><a href="https://wordpress.org/support/view/plugin-reviews/inbound-rocket?rate=5#postform" title="<?php _e('Leave us a review','inbound-rocket'); ?>" target="_blank"><?php _e('Leave us a review','inbound-rocket'); ?></a></li>
							</ul>
						</nav><!-- .secondary -->
						
						<p class="sharing"><a href="https://twitter.com/inboundrocket" class="twitter-follow-button" data-show-count="false"><?php _e('Follow @inboundrocket','inbound-rocket'); ?></a>
			
			            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></p>
			        </div>
								
			</div><!-- .ir-frame -->
		</div><!-- .ir-content -->
        <?php
    }
    
    function build_contacts_chart ( )
    {
        ?>
        <script type="text/javascript">
            function create_weekends ()
            {
				// @TODO is this still needed?
            }

            function hide_weekends ()
            {
				// @TODO is this still needed?
            }

            function create_chart ()
            {
				Chart.defaults.global.defaultFontFamily = "Open Sans";
				Chart.defaults.global.defaultFontSize = 12;
				
				var leadStats = {
					labels: [<?php echo $this->stats_dashboard->x_axis_labels; ?>],
					datasets: [{
                        label: '<?php _e('Unique Visitors','inbound-rocket');?>',
                        data: [ <?php echo $this->stats_dashboard->visits_data; ?> ],
						backgroundColor: '#cdf3fb'
                    },
                    {
                        label: '<?php _e('Contacts','inbound-rocket');?>',
                        data: [ <?php echo $this->stats_dashboard->column_data; ?> ],
						backgroundColor: '#cdf3fb'
                    },
                    {
						type: 'line',
                        label: '<?php _e('Average','inbound-rocket');?>',
                        data: [ <?php echo $this->stats_dashboard->average_data; ?> ],
						lineTension: 0,
						fill: false,
						borderColor: '#f47d4a',
					    backgroundColor: 'transparent',
					    pointBackgroundColor: 'rgba(244,125,74,0.8)',
                    },
                    {
                        label: '<?php _e('Weekends','inbound-rocket');?>',
                        data: [ <?php echo $this->stats_dashboard->weekend_column_data; ?> ],
						fill: true,
						backgroundColor: '#f3f3f3'
                    }]
				}
				
				console.log(leadStats);
				
				var chartOptions = {
					title: {
						display: false
					},
					legend: {
						display: false
					},
					scales: {
					    xAxes: [{
					    	gridLines: {
					        	display: false,
								color: "black"
					    	},
							scaleLabel: {
					        	display: false
					    	}
					    }],
					    yAxes: [{
					    	gridLines: {
								color: "#aaa",
								borderDash: [2, 5]
					    	},
							scaleLabel: {
					        	display: false
					    	},
							categoryPercentage: 1.0,
							barPercentage: 1.0
					    }]
					}
				}
				var ctx = document.getElementById("contacts_chart");
				chart = new Chart(ctx, {
					type: 'bar',
					data: leadStats,
					options: chartOptions
				});
			}

        var $series;
        var chart;

        jQuery(document).ready( function ( e ) {
            create_chart();
            create_weekends();
        });

        var delay = (function(){
          var timer = 0;
          return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
          };
        })();

        // Takes care of figuring out the weekend widths based on the new column widths
        jQuery(window).resize(function() {
            hide_weekends();
            height = chart.height
            width = jQuery("#contacts_chart").width();
            chart.setSize(width, height);
            delay(function(){
                create_weekends();
            }, 500);
        });

	    </script>
		<?php
	}
    
    /**
     * GET and set url actions into readable strings
     * @return string if actions are set,   bool if no actions set
    */
    protected function inboundrocket_current_action()
    {
        if ( isset($_REQUEST['action']) && -1 != $_REQUEST['action'] )
            return esc_attr($_REQUEST['action']);

        return FALSE;
    }  
}
?>