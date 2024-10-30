<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

require_once(INBOUNDROCKET_WELCOME_MAT_PLUGIN_DIR . '/admin/welcome-mat-list-table.php');

//=============================================
// WPInboundRocketAdmin Class
//=============================================
class WPWelcomeMatAdmin extends WPInboundRocketAdmin {
    
    var $power_up_settings_section = 'inboundrocket_wm_options_section';
    var $power_option_name = 'inboundrocket_wm_options';
    var $options;
	var $nonce;
    
    private static $_instance = null;
    
    // Welcome Mat WP_List_Table object
	public $welcome_mat_obj;
    
    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }
    
    
    /**
     * Class constructor
     */
    function __construct( )
    {
	    //=============================================
        // Hooks & Filters
        //=============================================
		if ( is_admin() ){
			$this->options = get_option('inboundrocket_wm_options');
			
			add_action( "admin_enqueue_scripts", array($this, 'inboundrocket_wm_admin_scripts') );
			add_filter( "redirect_post_location", array($this, 'inboundrocket_wm_redirect_after_save') );

			add_action( "load-inbound-rocket_page_inboundrocket_settings", array( $this, 'screen_option' ) );
			add_filter( "set-screen-option", array( $this, 'set_screen' ) );
			
			add_action( 'current_screen', array( $this, 'this_screen' ) );
			
			add_action( 'wp_ajax_ir_wm_autocomplete', array( $this, 'get_auto_complete_result') );
		}
	}
	
	function this_screen()
	{
		$current_screen = get_current_screen();

		if( $current_screen->id === 'ir-welcome-mat' ) {
			add_action( "in_admin_header", array( $this, 'inboundrocket_wm_admin_header' ) );
			add_action( "in_admin_footer", array( $this, 'inboundrocket_wm_admin_footer' ) );
		}
	}
	
    public static function init(){
	    
	    if(self::$_instance == null){
	        self::$_instance = new self();
	    }
	    return self::$_instance;            
	}
	
	function inboundrocket_wm_admin_header() {
		$this->inboundrocket_header();
	}

	function inboundrocket_wm_admin_footer() {
		$this->inboundrocket_footer();
	}

	function inboundrocket_wm_redirect_after_save() {
		
		$screen = get_current_screen();
		
		if( $screen->id === "ir-welcome-mat" ) {
            $location = admin_url('admin.php?page=inboundrocket_settings&tab=inboundrocket_wm_options');
        } else {
	        $location = admin_url();
        }
        
        return $location;
    }

	function inboundrocket_wm_admin_scripts(){
		
		$this->inboundrocket_wm_admin_styles();
		
	    wp_enqueue_script('media-upload');
	    wp_enqueue_script('thickbox');
	    wp_enqueue_style('thickbox');
		if (INBOUNDROCKET_ENABLE_DEBUG==true) {					
			wp_enqueue_script( "inboundrocket_wm_admin_script", INBOUNDROCKET_WELCOME_MAT_PATH . '/js/welcome-mat-admin.js', array( 'jquery', 'wp-color-picker' ), true );
		} else {					
			wp_enqueue_script( "inboundrocket_wm_admin_script", INBOUNDROCKET_WELCOME_MAT_PATH . '/js/welcome-mat-admin.min.js', array( 'jquery', 'wp-color-picker' ), true );
		}
	}
	
	function inboundrocket_wm_admin_styles(){
		
		if (INBOUNDROCKET_ENABLE_DEBUG==true) {
			wp_register_style( 'inboundrocket_wm_admin_style', INBOUNDROCKET_WELCOME_MAT_PATH . '/css/welcome-mat-admin.css', false, '0.1' );
		} else {			
			wp_register_style( 'inboundrocket_wm_admin_style', INBOUNDROCKET_WELCOME_MAT_PATH . '/css/welcome-mat-admin.min.css', false, '0.1' );
		}
		
		wp_register_style('inboundrocket-admin-css', INBOUNDROCKET_PATH . '/admin/inc/css/inboundrocket-admin.min.css');
		wp_enqueue_style('inboundrocket-admin-css');
		
		/* CSS rules for Color Picker */ 
		wp_enqueue_style( 'wp-color-picker' );		
		
		wp_enqueue_style('inboundrocket_wm_admin_style');
	}

	//=============================================
    // Settings Page
    //=============================================

    /**
     * Creates settings options
     */
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}
	
	public function screen_option() {

		$args   = array(
			'label'   => 'Welcome Mat',
			'default' => 10,
			'option'  => 'welcome_mat_per_page'
		);
	
		add_screen_option( 'per_page', $args );
	
		$this->welcome_mat_obj = new Welcome_Mat_List_Table();
		
	}
	
	/**
     * Prints input form for settings page
     */	
	public function ir_wm_input_fields () {
		$ir_wm_test_mode = isset($this->options['ir_wm_test_mode']) ? esc_attr( $this->options['ir_wm_test_mode'] ) : 0;
		?><tr valign="top">
				  <th><label><?php _e( 'Enable test mode?', 'inbound-rocket' ); ?></label></th>
				  <td>
						<label><input type="radio" name="inboundrocket_wm_options[ir_wm_test_mode]" value="1" <?php checked( $ir_wm_test_mode, 1 ); ?> /> <?php _e( 'Yes', 'inbound-rocket' ); ?></label> &nbsp;            
						<label><input type="radio" name="inboundrocket_wm_options[ir_wm_test_mode]" value="0" <?php checked( $ir_wm_test_mode, 0 ); ?> /> <?php _e( 'No', 'inbound-rocket' ); ?></label> &nbsp;
						<p class="help"><?php _e( 'If test mode is enabled, all welcome mats will show up regardless of whether a cookie has been set.', 'inbound-rocket' ); ?></p>
				  </td>
		  	</tr>
	  	</table><?php
	  	submit_button('Save Settings');
	  	?></form>
	  	<br class="clear">
	  	<?php if(!empty($_GET['deleted']) && $_GET['deleted'] == 1): ?>
	  	<div class="notice notice-success is-dismissible">
        	<p><?php _e( 'Welcome Mat was deleted successfully.', 'inbound-rocket' ); ?></p>
    	</div>
    	<?php elseif(!empty($_GET['deleted']) && $_GET['deleted'] > 1): ?>
    	<div class="notice notice-success is-dismissible">
        	<p><?php _e( 'Welcome Mat with ID '. absint($_GET['deleted']).' was deleted successfully.', 'inbound-rocket' ); ?></p>
    	</div>
	  	<?php endif; ?>
	  	<table class="form-table">
				<tr valign="top">
		      <td colspan="2">
		        <h2><?php _e( 'Welcome Mat', 'inbound-rocket' ); ?></h2>
				        
						<a href="<?php echo admin_url('edit.php?post_status=trash&post_type=ir-welcome-mat'); ?>" class="page-title-action"><?php _e('Deleted Welcome Mats','inbound-rocket'); ?></a>
						<a href="<?php echo admin_url('post-new.php?post_type=ir-welcome-mat'); ?>" class="page-title-action"><?php _e('Add New','inbound-rocket'); ?></a>
				
						<div id="post-body" class="metabox-holder columns-2">
							<div id="post-body-content">
								<div class="meta-box-sortables ui-sortable">
									
									<form action="<?=admin_url('admin.php?page=inboundrocket_settings&tab=inboundrocket_wm_options');?>" method="post">
										<?php
										$this->welcome_mat_obj->prepare_items();
										$this->welcome_mat_obj->display(); 
										?>
									</form>
								</div>
							</div>
						</div>
				  </td>
				</tr><?php
	}

	function get_auto_complete_result() {
		global $wpdb;
		$type = $_GET['type'];
		$q = $wpdb->esc_like(stripslashes($_GET['q'])).'%';

		switch($type) {
			case 'post':
			case 'page':
				$results = $wpdb->get_results($wpdb->prepare("SELECT $wpdb->posts.* 
				    FROM $wpdb->posts
				    WHERE $wpdb->posts.post_status = 'publish' 
				    AND $wpdb->posts.post_title LIKE %s
				    AND $wpdb->posts.post_type = '%s'
				    AND $wpdb->posts.post_date < NOW()
				    ORDER BY $wpdb->posts.post_date DESC", $q, $type), ARRAY_A);
				foreach ($results as $r) {
					$items[] = addslashes($r['post_name']);
				}
				break;
			case 'post_type':
				$post_types = get_post_types();
				foreach ( $post_types  as $post_type ) {
				   $items[] = addslashes($post_type);
				}
				break;
			case 'category':
				$categories = get_terms( array() );
				foreach ( $categories  as $category ) {
				   $items[] = addslashes($category->slug);
				}
				break;
		}
		if(!empty($items)){
			foreach( $items as $i ) {
				echo $i."\n";
			}
		} else {
			echo 'no results found.';
		}
		wp_die();
	}
}
?>