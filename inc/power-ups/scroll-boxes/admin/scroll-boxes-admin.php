<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

require_once(INBOUNDROCKET_SCROLL_BOXES_PLUGIN_DIR . '/admin/scroll-box-list-table.php');

//=============================================
// WPInboundRocketAdmin Class
//=============================================
class WPScrollBoxesAdmin extends WPInboundRocketAdmin {
    
    var $power_up_settings_section = 'inboundrocket_sb_options_section';
    var $power_option_name = 'inboundrocket_sb_options';
    var $options;
	var $nonce;
    
    private static $_instance = null;
    
    // Scroll Box WP_List_Table object
	public $scroll_boxes_obj;
    
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
			$this->options = get_option('inboundrocket_sb_options');
						
			add_action( "admin_enqueue_scripts", array($this, 'inboundrocket_sb_admin_scripts'), 10 );
			add_filter( "redirect_post_location", array($this, 'inboundrocket_sb_redirect_after_save'), 10, 2 );

			add_action( "load-inbound-rocket_page_inboundrocket_settings", array( $this, 'screen_option' ) );
			add_filter( "set-screen-option", array( $this, 'set_screen' ) );
			
			add_action( 'wp_ajax_ir_sb_autocomplete', array( $this, 'get_auto_complete_result') );
			
			$post_type = '';
			
			if(isset($_GET['post_type'])){
		    	$post_type = esc_attr($_GET['post_type']);
	    	} elseif(isset($_GET['post'])) {
		    	$post = get_post(absint($_GET['post']));
		    	$post_type = $post->post_type;
	    	}
			
			if($post_type == 'ir-scroll-box') {
				add_action( "in_admin_header", array( $this, 'inboundrocket_sb_admin_header' ) );
				add_action( "in_admin_footer", array( $this, 'inboundrocket_sb_admin_footer' ) );
			}
			

		}
	}
	
    public static function init(){
	    
	    if(self::$_instance == null){
	        self::$_instance = new self();
	    }
	    return self::$_instance;            
	}
	
	function inboundrocket_sb_admin_header() {
		$this->inboundrocket_header();
	}

	function inboundrocket_sb_admin_footer() {
		$this->inboundrocket_footer();
	}

	function inboundrocket_sb_redirect_after_save( $location, $post_id ) {
		
		if ( isset( $_POST['save'] ) || isset( $_POST['publish'] ) ) {		
			if ( $_POST['post_type'] == "ir-scroll-box" ) {
	            wp_redirect(admin_url('admin.php?page=inboundrocket_settings&tab=inboundrocket_sb_options'));
	            exit;
	        }
        }
        return $location;
    }

	function inboundrocket_sb_admin_scripts(){
		
		$this->inboundrocket_sb_admin_styles();
		
		wp_enqueue_script( 'media-upload' );
	    wp_enqueue_script( 'thickbox' );
	    wp_enqueue_style( 'thickbox' );
	    
		if (INBOUNDROCKET_ENABLE_DEBUG==true) {					
			wp_enqueue_script( "inboundrocket_sb_admin_script", INBOUNDROCKET_SCROLL_BOXES_PATH . '/js/scroll-boxes-admin.js', array( 'jquery', 'wp-color-picker' ), true );
		} else {					
			wp_enqueue_script( "inboundrocket_sb_admin_script", INBOUNDROCKET_SCROLL_BOXES_PATH . '/js/scroll-boxes-admin.min.js', array( 'jquery', 'wp-color-picker' ), true );
		}
		
		wp_localize_script( 'inboundrocket_sb_admin_script', 'sbOptions', array('nonce' => wp_create_nonce('ir_sb_nonce')) );

		
		
	}
	
	function inboundrocket_sb_admin_styles(){
		
		if (INBOUNDROCKET_ENABLE_DEBUG==true) {
			wp_register_style( 'inboundrocket_sb_admin_style', INBOUNDROCKET_SCROLL_BOXES_PATH . '/css/scroll-boxes-admin.css', false, '0.1' );
		} else {			
			wp_register_style( 'inboundrocket_sb_admin_style', INBOUNDROCKET_SCROLL_BOXES_PATH . '/css/scroll-boxes-admin.min.css', false, '0.1' );
		}
		
		wp_register_style('inboundrocket-admin-css', INBOUNDROCKET_PATH . '/admin/inc/css/inboundrocket-admin.min.css');
		wp_enqueue_style('inboundrocket-admin-css');
		
		/* CSS rules for Color Picker */ 
		wp_enqueue_style( 'wp-color-picker' );		
		
		wp_enqueue_style('inboundrocket_sb_admin_style');
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
			'label'   => 'Scroll Boxes',
			'default' => 10,
			'option'  => 'scroll_boxes_per_page'
		);
	
		add_screen_option( 'per_page', $args );
	
		$this->scroll_boxes_obj = new Scroll_Boxes_List_Table();
		
	}
	
	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    function sanitize ( $input )
    {
        $new_input = array();
		
		if( isset( $input['ir_sb_test_mode'] ) )
			$new_input['ir_sb_test_mode'] = sanitize_text_field( $input['ir_sb_test_mode'] );
		
		return $new_input;
	}
	
	/**
     * Prints input form for settings page
     */	
	public function ir_sb_input_fields () {
		if(0) {
			print("<pre>");
			print_r($this->options);
		}		
		$ir_sb_test_mode = isset($this->options['ir_sb_test_mode']) ? esc_attr( $this->options['ir_sb_test_mode'] ) : 0;
		?><tr valign="top">
				  <th><label><?php _e( 'Enable test mode?', 'inbound-rocket' ); ?></label></th>
				  <td>
						<label><input type="radio" name="inboundrocket_sb_options[ir_sb_test_mode]" value="1" <?php checked( $ir_sb_test_mode, 1 ); ?> /> <?php _e( 'Yes', 'inbound-rocket' ); ?></label> &nbsp;            
						<label><input type="radio" name="inboundrocket_sb_options[ir_sb_test_mode]" value="0" <?php checked( $ir_sb_test_mode, 0 ); ?> /> <?php _e( 'No', 'inbound-rocket' ); ?></label> &nbsp;
						<p class="help"><?php _e( 'If test mode is enabled, all scroll boxes will show up regardless of whether a cookie has been set.', 'inbound-rocket' ); ?></p>
				  </td>
			</tr>
	  	</table><?php
	  	submit_button('Save Settings');
		?></form>
	  	<br class="clear">
	  	<?php if(!empty($_GET['deleted']) && $_GET['deleted'] == 1): ?>
	  	<div class="notice notice-success is-dismissible">
        	<p><?php _e( 'Scroll box was deleted successfully.', 'inbound-rocket' ); ?></p>
    	</div>
    	<?php elseif(!empty($_GET['deleted']) && $_GET['deleted'] > 1): ?>
    	<div class="notice notice-success is-dismissible">
        	<p><?php _e( 'Scroll Box with ID '. absint($_GET['deleted']).' was deleted successfully.', 'inbound-rocket' ); ?></p>
    	</div>
	  	<?php endif; ?>
	  	<table class="form-table">
		<tr valign="top">
          <td colspan="2">
	        <h2><?php _e( 'Scroll Boxes', 'inbound-rocket' ); ?></h2>
	        
			<a href="<?php echo admin_url('edit.php?post_status=trash&post_type=ir-scroll-box'); ?>" class="page-title-action"><?php _e('Deleted Boxes','inbound-rocket'); ?></a>
			<a href="<?php echo admin_url('post-new.php?post_type=ir-scroll-box'); ?>" class="page-title-action"><?php _e('Add New','inbound-rocket'); ?></a>
			
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						
						<form action="<?=admin_url('admin.php?page=inboundrocket_settings&tab=inboundrocket_sb_options');?>" method="post">
							<?php
							$this->scroll_boxes_obj->prepare_items();
							$this->scroll_boxes_obj->display(); ?>
						</form>
					</div>
				</div>
			</div>
		  </td>
		</tr>
<?php
	}
	
	function get_auto_complete_result() {
		global $wpdb;
		$type = $_GET['type'];
		$q = $wpdb->esc_like(stripslashes($_GET['q'])).'%';
		
		if(!wp_verify_nonce('ir_sb_nonce',$_GET['nonce']))
			die('Hack Attempt');

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