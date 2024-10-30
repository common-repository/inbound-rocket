<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

/*  Register the ir-scroll-box post type */
add_action('init', 'irsb_register_post_type');
function irsb_register_post_type(){	
	$args = array(
		'public' 		=> false,
		'labels'		=> array(
			'name'               => __( 'Scroll Boxes', 'inbound-rocket' ),
			'singular_name'      => __( 'Scroll Box', 'inbound-rocket' ),
			'add_new'            => __( 'Add New', 'inbound-rocket' ),
			'add_new_item'       => __( 'Add New Box', 'inbound-rocket' ),
			'edit_item'          => __( 'Edit Box', 'inbound-rocket' ),
			'new_item'           => __( 'New Box', 'inbound-rocket' ),
			'all_items'          => __( 'All Boxes', 'inbound-rocket' ),
			'view_item'          => __( 'View Box', 'inbound-rocket' ),
			'search_items'       => __( 'Search Boxes', 'inbound-rocket' ),
			'not_found'          => __( 'No Boxes found', 'inbound-rocket' ),
			'not_found_in_trash' => __( 'No Boxes found in Trash', 'inbound-rocket' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Scroll Boxes', 'inbound-rocket' )
		),
		'publicly_queryable' => true,
		'show_ui' 			=> true,	
		'show_in_menu' 		=> false,		
		'query_var' 		=> true, //false
		'capability_type' 	=> 'post',
		'has_archive' 		=> true,
		'supports' 			=> array('title','editor','revisions'),
	);
	register_post_type( 'ir-scroll-box', $args );
	
	irsb_sample_box();
}

/* Register meta boxes */
add_action( 'add_meta_boxes', 'irsb_add_meta_boxes' );

function irsb_add_meta_boxes( $post_type ) {

	if ( $post_type !== 'ir-scroll-box' ) {
		return false;
	}

	add_meta_box(
		'ir-scroll-box-appearance-controls',
		__( 'Box Appearance', 'inbound-rocket' ),
		'irsb_metabox_box_appearance',
		'ir-scroll-box',
		'normal',
		'core'
	);

	add_meta_box(
		'ir-scroll-box-options-controls',
		__( 'Box Options', 'inbound-rocket' ),
		'irsb_metabox_box_option',
		'ir-scroll-box',
		'normal',
		'core'
	);
}

function irsb_metabox_box_appearance( $post, $metabox ) {
	$option = array();
	$option = get_post_meta( $post->ID, INBOUNDROCKET_SCROLL_BOXES_META_KEY, true );
	
	if( !empty($option['css']) && empty( $option['css']['background_color'] ))
	 	$option['css']['background_color'] = '#FFFFFF';
	 	
	if( !empty($option['css']) && empty( $option['css']['text_color'] ))
	 	$option['css']['text_color'] = '#020202';
	
	if( !empty($option['css']) && empty( $option['css']['box_width'] ))
	 	$option['css']['box_width'] = '';
		
	if( !empty($option['css']) && empty( $option['css']['border_width'] ))
	 	$option['css']['border_width'] = '';
	
	if( !empty($option['css']) && empty( $option['css']['border_color'] ))
	 	$option['css']['border_color'] = '#020202';
	
	if( !empty($option['css']) && empty( $option['css']['border_style'] ))
	 	$option['css']['border_style'] = '';
	
	if( !empty($option['css']) && empty( $option['css']['extra_css'] ))
	 	$option['css']['extra_css'] = '';

	wp_nonce_field( basename( __FILE__ ), 'inboundrocket_sb_options_nonce' );
	/* include form elements */
	require_once(INBOUNDROCKET_SCROLL_BOXES_PLUGIN_DIR . '/admin/metaboxes/scroll-box-appearance-view.php');
}

function irsb_metabox_box_option($post, $metabox ) {
	$option = array();
	$option = get_post_meta( $post->ID, INBOUNDROCKET_SCROLL_BOXES_META_KEY, true );
	
	if ( empty( $option['rules'] ) ) {
		$option = array('rules' => array());
		$option['rules'][] = array( 'condition' => '', 'value' => '' );
	}
	
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
	
	if( !empty($option['css']) && empty( $option['css']['position']) )
		$option['css']['position'] = 'bottom-right';
		
	if( !empty($option['css']) && empty( $option['css']['text_align']) )
		$option['css']['text_align'] = 'left';
		
	if( !empty($option['css']) && empty( $option['css']['font_style']) )
		$option['css']['font_style'] = 'normal';
	
	if( !empty($option['css']) && empty( $option['auto_show_percentage'] ))
	 	$option['auto_show_percentage'] = 0;
		
	if( empty( $option['auto_show_element'] ))
	 	$option['auto_show_element'] = '';	

	if( empty( $option['cookie_exp'] ))
	 	$option['cookie_exp'] = 0;	
	
	if( empty( $option['test_mode'] ))
	 	$option['test_mode'] = 0;	
	
	wp_nonce_field( basename( __FILE__ ), 'inboundrocket_sb_options_nonce' );
	
	/* include form elements */
	require_once(INBOUNDROCKET_SCROLL_BOXES_PLUGIN_DIR . '/admin/metaboxes/scroll-box-option-view.php');
}

add_action( 'save_post', 'irsb_save_box_options' );
function irsb_save_box_options( $post_id ) {
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['inboundrocket_sb_options_nonce'] ) || !wp_verify_nonce( $_POST['inboundrocket_sb_options_nonce'], basename( __FILE__ ) ) )
	  return $post_id;
	  
	if ( wp_is_post_revision( $post_id ) )
		return;

	$post = get_post($post_id);

	$post_type = get_post_type_object( $post->post_type );
		
  	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
	  return $post_id;

	$new_meta_value = $_POST['ir-sb'];//( isset( $_POST['ir-sb'] ) ? sanitize_html_class( $_POST['ir-sb'] ) : '' );

	$meta_key 	= INBOUNDROCKET_SCROLL_BOXES_META_KEY;
	$meta_value = get_post_meta( $post_id, $meta_key, true );
	
	if ( $new_meta_value && '' == $meta_value )
	  add_post_meta( $post_id, $meta_key, $new_meta_value, true );
  
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
	  update_post_meta( $post_id, $meta_key, $new_meta_value );
  
	elseif ( '' == $new_meta_value && $meta_value )
	  delete_post_meta( $post_id, $meta_key, $meta_value );	
	
	irsb_box_rules( $post_id );
}

function irsb_box_rules( $post_id ) {
	// only act on our own post type
	$post = get_post( $post_id );
	if ( $post instanceof WP_Post && $post->post_type !== 'ir-scroll-box' ) {
		return;
	}

	// get all published boxes
	$all_boxes = get_posts(
		array(
			'post_type'   => 'ir-scroll-box',
			'post_status' => 'publish',
			'posts_per_page' => -1
		)
	);
	
	if ( is_array( $all_boxes ) ) {
		foreach ( $all_boxes as $box ) {
			
			$box_meta = get_post_meta( $box->ID, INBOUNDROCKET_SCROLL_BOXES_META_KEY, true );
			
			if( empty($box_meta['rules_comparison']) ) $box_meta['rules_comparison'] = '';
			
			$rules[ $box->ID ]                = $box_meta['rules'];
			$rules[ $box->ID ]['comparison'] = $box_meta['rules_comparison'];
		}
	}
	$scrollbox_options = get_option(INBOUNDROCKET_SCROLL_BOXES_META_KEY);
	if(!empty($rules)) $scrollbox_options['rules'] = $rules;
	inboundrocket_update_option(INBOUNDROCKET_SCROLL_BOXES_META_KEY, $scrollbox_options);
}

function filter_function_name( $content, $post_id ) {
  // Process content here
  return $content;
}
add_filter( 'content_edit_pre', 'filter_function_name', 10, 2 );

function irsb_sample_box() {

	$boxes = get_posts(
		array(
			'post_type'   => 'ir-scroll-box',
			'post_status' => array( 'publish', 'draft' )
		)
	);

	if( ! empty( $boxes ) ) {
		return false;
	}

	$box_id = wp_insert_post(
		//@TODO make title and content translatable
		array(
			'post_type' 	=> 'ir-scroll-box',
			'post_title' 	=> "Sample Box (can't delete)",
			'post_content' 	=> "<h4>Hello world.</h4><p>This is a sample box, with some sample content in it.</p>",
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
			'extra_css' 		=> ''
		),
		'rule_matches' 	=> 'any',
		'auto_show' 	=> 'instant',
		'auto_hide' 	=> 0,
		'test_mode' 	=> 1
	);

	update_post_meta( $box_id, INBOUNDROCKET_SCROLL_BOXES_META_KEY, $box_options );
}
?>