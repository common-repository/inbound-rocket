<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

/*  Register the ir-welcome-mat post type */
add_action('init', 'ir_wm_register_post_type');
function ir_wm_register_post_type(){	
	$args = array(
		'public' 		=> false,
		'labels'		=> array(
			'name'               => __( 'Welcome Mat', 'inbound-rocket' ),
			'singular_name'      => __( 'Welcome Mat', 'inbound-rocket' ),
			'add_new'            => __( 'Add New', 'inbound-rocket' ),
			'add_new_item'       => __( 'Add New Welcome Mat', 'inbound-rocket' ),
			'edit_item'          => __( 'Edit Welcome Mat', 'inbound-rocket' ),
			'new_item'           => __( 'New Welcome Mat', 'inbound-rocket' ),
			'all_items'          => __( 'All Welcome Mat', 'inbound-rocket' ),
			'view_item'          => __( 'View Welcome Mat', 'inbound-rocket' ),
			'search_items'       => __( 'Search Welcome Mats', 'inbound-rocket' ),
			'not_found'          => __( 'No Welcome Mats found', 'inbound-rocket' ),
			'not_found_in_trash' => __( 'No Welcome Mats found in Trash', 'inbound-rocket' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Welcome Mats', 'inbound-rocket' )
		),
		'publicly_queryable' => true,
		'show_ui' 			=> true,	
		'show_in_menu' 		=> false,		
		'query_var' 		=> true, //false
		'capability_type' 	=> 'post',
		'has_archive' 		=> true,
		'supports' 			=> array('title','editor','revisions'),
	);
	register_post_type( 'ir-welcome-mat', $args );
	
	ir_wm_sample_box();
}

add_action('init', 'ir_wm_rem_editor_from_post_type');
function ir_wm_rem_editor_from_post_type() {
    remove_post_type_support( 'ir-welcome-mat', 'editor' );
}

/* Register meta boxes */
add_action( 'add_meta_boxes_ir-welcome-mat', 'ir_wm_add_meta_boxes' );
function ir_wm_add_meta_boxes() {

	add_meta_box(
		'ir-welcome-mat-appearance-controls',
		__( 'Welcome Mat Design', 'inbound-rocket' ),
		'ir_wm_metabox_box_appearance',
		'ir-welcome-mat',
		'normal',
		'core'
	);

	add_meta_box(
		'ir-welcome-mat-fields-controls',
		__( 'Welcome Mat Fields', 'inbound-rocket' ),
		'ir_wm_metabox_box_fields',
		'ir-welcome-mat',
		'normal',
		'core'
	);

	add_meta_box(
		'ir-welcome-mat-options-controls',
		__( 'Welcome Mat Behavior', 'inbound-rocket' ),
		'ir_wm_metabox_box_option',
		'ir-welcome-mat',
		'normal',
		'core'
	);
}

function ir_wm_metabox_box_appearance( $post, $metabox ) {
	$option = array();
	$option = get_post_meta( $post->ID, INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY, true );
	
	if( !empty($option['css']) && empty( $option['css']['main_heading_text'] ))
	 	$option['css']['main_heading_text'] = 'We wanna send you something.';

	if( !empty($option['css']) && empty( $option['css']['main_heading_font'] ))
	 	$option['css']['main_heading_font'] = 'Open Sans,sans-serif:300,400,400italic,800';

	if( !empty($option['css']) && empty( $option['css']['main_heading_font_size'] ))
	 	$option['css']['main_heading_font_size'] = '50';

	if( !empty($option['css']) && empty( $option['css']['main_heading_font_weight'] ))
	 	$option['css']['main_heading_font_weight'] = '0';

	if( !empty($option['css']) && empty( $option['css']['main_heading_font_style'] ))
	 	$option['css']['main_heading_font_style'] = '0';

	if( !empty($option['css']) && empty( $option['css']['main_heading_font_align'] ))
	 	$option['css']['main_heading_font_align'] = 'center';

	if( !empty($option['css']) && empty( $option['css']['main_heading_font_color'] ))
	 	$option['css']['main_heading_font_color'] = '#000';

	if( !empty($option['css']) && empty( $option['css']['sub_heading_text'] ))
	 	$option['css']['sub_heading_text'] = 'If it\'s good, you can heart us forever.';

	if( !empty($option['css']) && empty( $option['css']['sub_heading_font'] ))
	 	$option['css']['sub_heading_font'] = 'Open Sans,sans-serif:300,400,400italic,800';

	if( !empty($option['css']) && empty( $option['css']['sub_heading_font_size'] ))
	 	$option['css']['sub_heading_font_size'] = '26';

	if( !empty($option['css']) && empty( $option['css']['sub_heading_font_weight'] ))
	 	$option['css']['sub_heading_font_weight'] = '0';

	if( !empty($option['css']) && empty( $option['css']['sub_heading_font_style'] ))
	 	$option['css']['sub_heading_font_style'] = '0';

	if( !empty($option['css']) && empty( $option['css']['sub_heading_font_align'] ))
	 	$option['css']['sub_heading_font_align'] = 'center';

	if( !empty($option['css']) && empty( $option['css']['sub_heading_font_color'] ))
	 	$option['css']['sub_heading_font_color'] = '#000';

	if( !empty($option['css']) && empty( $option['css']['background_color'] ))
	 	$option['css']['background_color'] = '#FFFFFF';
	 
	if( !empty($option['css']) && empty( $option['css']['background_image'] ))
	 	$option['css']['background_image'] = '';

	if( !empty($option['css']) && empty( $option['css']['submit_button_text'] ))
	 	$option['css']['submit_button_text'] = 'Okay';

	if( !empty($option['css']) && empty( $option['css']['submit_button_font'] ))
	 	$option['css']['submit_button_font'] = 'Open Sans,sans-serif:300,400,400italic,800';
	 
	if( !empty($option['css']) && empty( $option['css']['submit_button_font_size'] ))
	 	$option['css']['submit_button_font_size'] = '21';

	if( !empty($option['css']) && empty( $option['css']['submit_button_font_weight'] ))
	 	$option['css']['submit_button_font_weight'] = '0';

	if( !empty($option['css']) && empty( $option['css']['submit_button_font_style'] ))
	 	$option['css']['submit_button_font_style'] = '0';

	if( !empty($option['css']) && empty( $option['css']['submit_button_font_align'] ))
	 	$option['css']['submit_button_font_align'] = 'center';

	if( !empty($option['css']) && empty( $option['css']['submit_button_text_color'] ))
	 	$option['css']['submit_button_text_color'] = '#FFFFFF';

	if( !empty($option['css']) && empty( $option['css']['submit_button_text_hover_color'] ))
	 	$option['css']['submit_button_text_hover_color'] = '#F7F7F7';

	if( !empty($option['css']) && empty( $option['css']['submit_button_background_color'] ))
	 	$option['css']['submit_button_background_color'] = '#72AD53';

	if( !empty($option['css']) && empty( $option['css']['submit_button_hover_background_color'] ))
	 	$option['css']['submit_button_hover_background_color'] = '#57863F';

	if( !empty($option['css']) && empty( $option['css']['no_thanks_button_text'] ))
	 	$option['css']['no_thanks_button_text'] = 'Nope';
	 
	if( !empty($option['css']) && empty( $option['css']['no_thanks_button_font'] ))
	 	$option['css']['no_thanks_button_font'] = 'Open Sans,sans-serif:300,400,400italic,800';
	 
	if( !empty($option['css']) && empty( $option['css']['no_thanks_button_font_size'] ))
	 	$option['css']['no_thanks_button_font_size'] = '21';

	if( !empty($option['css']) && empty( $option['css']['no_thanks_button_font_weight'] ))
	 	$option['css']['no_thanks_button_font_weight'] = '0';

	if( !empty($option['css']) && empty( $option['css']['no_thanks_button_font_style'] ))
	 	$option['css']['no_thanks_button_font_style'] = '0';

	if( !empty($option['css']) && empty( $option['css']['no_thanks_button_font_align'] ))
	 	$option['css']['no_thanks_button_font_align'] = 'center';

	if( !empty($option['css']) && empty( $option['css']['no_thanks_button_text_color'] ))
	 	$option['css']['no_thanks_button_text_color'] = '#FFFFFF';

	if( !empty($option['css']) && empty( $option['css']['no_thanks_button_text_hover_color'] ))
	 	$option['css']['no_thanks_button_text_hover_color'] = '#F7F7F7';

	if( !empty($option['css']) && empty( $option['css']['no_thanks_button_background_color'] ))
	 	$option['css']['no_thanks_button_background_color'] = '#C3C5C5';

	if( !empty($option['css']) && empty( $option['css']['no_thanks_button_hover_background_color'] ))
	 	$option['css']['no_thanks_button_hover_background_color'] = '#A0A0A0';

	if( !empty($option['css']) && empty( $option['css']['arrow_icon'] ))
	 	$option['css']['arrow_icon'] = 'round-arrdown';

	if( !empty($option['css']) && empty( $option['css']['arrow_color'] ))
	 	$option['css']['arrow_color'] = '#72AD53';

	if( !empty($option['css']) && empty( $option['css']['submit_arrow_border_color'] ))
	 	$option['css']['submit_arrow_border_color'] = '#72AD53';

	if( !empty($option['css']) && empty( $option['css']['extra_css'] ))
	 	$option['css']['extra_css'] = '';

	wp_nonce_field( basename( __FILE__ ), 'inboundrocket_wm_options_nonce' );
	/* include form elements */
	require_once(INBOUNDROCKET_WELCOME_MAT_PLUGIN_DIR . '/admin/metaboxes/welcome-mat-appearance-view.php');
}

function ir_wm_metabox_box_fields($post, $metabox ) {
	$option = array();
	$option = get_post_meta( $post->ID, INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY, true );

	wp_nonce_field( basename( __FILE__ ), 'inboundrocket_wm_options_nonce' );
	/* include form elements */
	require_once(INBOUNDROCKET_WELCOME_MAT_PLUGIN_DIR . '/admin/metaboxes/welcome-mat-fields-view.php');
}

function ir_wm_metabox_box_option($post, $metabox ) {
	$option = array();
	$option = get_post_meta( $post->ID, INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY, true );
	
	if ( empty( $option['rules'] ) ) {
		$option = array('rules' => array());
		$option['rules'][] = array( 'condition' => 'everywhere', 'value' => '' );
	}
	
	if( empty( $option['toggle'] ) )
	 	$option['toggle'] = '0';

	 if( empty( $option['landing_toggle'] ) )
	 	$option['landing_toggle'] = '0';

	 if( empty( $option['redirect_url'] ) )
	 	$option['redirect_url'] = '';

	 if( empty( $option['form_variable'] ) )
	 	$option['form_variable'] = '';
	 
	if( empty( $option['css']['display_mode'] ) )
	 	$option['css']['display_mode'] = 'normal';

	if( empty( $option['rule_matches'] ) )
	 	$option['rule_matches'] = 'any';
	
	if( empty( $option['animation'] ) )
	 	$option['animation'] = 'fade';
	
	if( empty( $option['auto_show'] ) )
	 	$option['auto_show'] = '';
		
	if( empty( $option['auto_hide'] ) )
	 	$option['auto_hide'] = 0;

	if( empty( $option['hide_on_screen_size'] ) )
	 	$option['hide_on_screen_size'] = 0;
	
	if( empty( $option['css']['position']) )
		$option['css']['position'] = 'bottom-right';
		
	if( empty( $option['css']['text_align']) )
		$option['css']['text_align'] = 'left';
		
	if( empty( $option['css']['font_style']) )
		$option['css']['font_style'] = 'normal';
	
	if( empty( $option['auto_show_percentage'] ))
	 	$option['auto_show_percentage'] = 0;
		
	if( empty( $option['auto_show_element'] ))
	 	$option['auto_show_element'] = '';	

	if( empty( $option['cookie_exp'] ))
	 	$option['cookie_exp'] = 0;

 	if( empty( $option['cookie_exp_time'] ))
 		$option['cookie_exp_time'] = 'always-show';		
	
	if( empty( $option['test_mode'] ))
	 	$option['test_mode'] = 0;	

	/* include form elements */
	require_once(INBOUNDROCKET_WELCOME_MAT_PLUGIN_DIR . '/admin/metaboxes/welcome-mat-option-view.php');
}

add_action( 'save_post', 'ir_wm_save_box_options' );
function ir_wm_save_box_options( $post_id ) {
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['inboundrocket_wm_options_nonce'] ) || !wp_verify_nonce( $_POST['inboundrocket_wm_options_nonce'], basename( __FILE__ ) ) )
	  return $post_id;
	  
	if ( wp_is_post_revision( $post_id ) )
		return;

	$post = get_post($post_id);

	$post_type = get_post_type_object( $post->post_type );
		
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	//die(print_r( $_POST['ir-wm']));
	
	$new_meta_value = $_POST['ir-wm'];
	
	$meta_key 	= INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY;
	$meta_value = get_post_meta( $post_id, $meta_key, true );
	
	if ( $new_meta_value && '' == $meta_value )
	  add_post_meta( $post_id, $meta_key, $new_meta_value, true );
  
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
	  update_post_meta( $post_id, $meta_key, $new_meta_value );
  
	elseif ( '' == $new_meta_value && $meta_value )
	  delete_post_meta( $post_id, $meta_key, $meta_value );	
	
	else
		ir_wm_box_rules( $post_id );
}

function ir_wm_box_rules( $post_id ) {
	// only act on our own post type
	$post = get_post( $post_id );
	if ( $post instanceof WP_Post && $post->post_type !== 'ir-welcome-mat' ) {
		return;
	}
	$rules = array();
	// get all published boxes
	$all_boxes = get_posts(
		array(
			'post_type'   => 'ir-welcome-mat',
			'post_status' => 'publish',
			'posts_per_page' => -1
		)
	);
	
	if ( is_array( $all_boxes ) ) {
		foreach ( $all_boxes as $box ) {
			
			$box_meta = get_post_meta( $box->ID, INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY, true );
			
			if( empty($box_meta['rules_comparison']) ) $box_meta['rules_comparison'] = '';
			
			$rules[ $box->ID ]                = $box_meta['rules'];
			$rules[ $box->ID ]['comparison'] = $box_meta['rules_comparison'];
		}
	}
	$welcome_mat_options = get_option(INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY);
	if(!empty($rules)) $welcome_mat_options['rules'] = $rules;
	inboundrocket_update_option(INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY, $welcome_mat_options);
}

function filter_ir_wm_content( $content, $post_id ) {
  // Process content here
  return $content;
}
add_filter( 'content_edit_pre', 'filter_ir_wm_content', 10, 2 );

function ir_wm_sample_box() {

	$boxes = get_posts(
		array(
			'post_type'   => 'ir-welcome-mat',
			'post_status' => array( 'publish', 'draft' )
		)
	);

	if( ! empty( $boxes ) ) {
		return false;
	}

	$box_id = wp_insert_post(
		//@TODO make title and content translatable
		array(
			'post_type' 	=> 'ir-welcome-mat',
			'post_title' 	=> "Sample Welcome Mat (can't delete)",
			'post_content' 	=> "<h4>Hello world.</h4><p>This is a sample welcome mat box, with some sample content in it.</p>",
			'post_status' 	=> 'draft',
		)
	);

	// set box options
	$box_options = array(
		'css' => array(
			'background_color'  => '#edf9ff',
			'text_color' 		=> '#000',
			'text_align'		=> 'left',
			'font_style'		=> 'normal',
			'box_width' 		=> '340',
			'border_color' 		=> '#ff7c00',
			'border_width' 		=> '4',
			'border_style' 		=> 'dashed',
			'position' 			=> 'bottom-right',
			'extra_css' 		=> '',
			'main_heading_text' => '',
			'main_heading_font_size' => '10',
			'main_heading_font_weight' => '0',
			'main_heading_font_style' => '0',
			'main_heading_font_align' => 'left',
			'main_heading_font_color' => '#000',
			'sub_heading_text' => '',
			'sub_heading_font_size' => '10',
			'sub_heading_font_weight' => '0',
			'sub_heading_font_style' => '0',
			'sub_heading_font_align' => 'left',
			'sub_heading_font_color' => '',
			'background_color' => '#FFFFFF',
			'background_image' => '',
			'submit_button_text' => 'Submit',
			'submit_button_font_size' => '10',
			'submit_button_font_weight' => '0',
			'submit_button_font_style' => '0',
			'submit_button_font_align' => 'left',
			'submit_button_text_color' => '#FFFFFF',
			'submit_button_background_color' => '#999',
			'submit_button_hover_background_color' => '#111',
			'arrow_icon' => 'round-arrdown',
			'arrow_color' => '#999',
			'submit_arrow_border_color' => '#000',
			'display_mode'=> 'normal'
		),
		'rules' => '',
		'rule_matches' 	=> 'any',
		'auto_show' 	=> 'instant',
		'auto_hide' 	=> '0',
		'test_mode' 	=> '1',
		'cookie_exp' 	=> '1',
		'cookie_exp_time' => 'always-show',
		'toggle' 	=> '0',
		'landing_toggle' => '0',
		'redirect_url' => '',
		'form_variable' => ''
	);

	update_post_meta( $box_id, INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY, $box_options );
}
?>