<?php
/*
@Power-up Name: Welcome Mat
@Power-up Class: WPWelcomeMat
@Power-up Menu Text: 
@Power-up Menu Link: settings
@Power-up Slug: welcome_mat
@Power-up URI: https://inboundrocket.co/features/welcome-mat
@Power-up Description: Welcome Mat displays a full-screen call to action that shows when visitors land on your site. Encourage your visitors to join your email list, check out your popular blog post … the possibilities are endless!
@Power-up Icon: power-up-icon-welcome-mat
@Power-up Icon Small: power-up-icon-welcome-mat_small
@Power-up Sort: 1
@First Introduced: 1.5
@Power-up Tags: Lead Converting
@Auto Activate: No
@Permanently Enabled: No
@Hidden: No
@cURL Required: No
@Premium: No
@Options Name: inboundrocket_wm_options    
*/
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

//=============================================
// Define Constants
//=============================================

if ( !defined('INBOUNDROCKET_WELCOME_MAT_PATH') )
    define('INBOUNDROCKET_WELCOME_MAT_PATH', INBOUNDROCKET_PATH . '/inc/power-ups/welcome-mat');

if ( !defined('INBOUNDROCKET_WELCOME_MAT_PLUGIN_DIR') )
    define('INBOUNDROCKET_WELCOME_MAT_PLUGIN_DIR', INBOUNDROCKET_PLUGIN_DIR . '/inc/power-ups/welcome-mat');

if ( !defined('INBOUNDROCKET_WELCOME_MAT_PLUGIN_SLUG') )
    define('INBOUNDROCKET_WELCOME_MAT_PLUGIN_SLUG', basename(dirname(__FILE__)));

if (!defined('INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY'))
	define('INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY', 'inboundrocket_wm_options');


//=============================================
// Include Needed Files
//=============================================

require_once(INBOUNDROCKET_WELCOME_MAT_PLUGIN_DIR . '/admin/welcome-mat-admin.php');
require_once(INBOUNDROCKET_WELCOME_MAT_PLUGIN_DIR . '/admin/welcome-mat-admin-functions.php');

//=============================================
// WPWelcomeMat Class
//=============================================
class WPWelcomeMat extends WPInboundRocket {
    
  var $admin;
  var $options;

  function __construct ( $activated )
  {
    //=============================================
    // Hooks & Filters 
    //=============================================

    if ( ! $activated )
        return false;

    global $inboundrocket_welcome_mat_wp;
    $inboundrocket_welcome_mat_wp = $this;
    
    // Register stylesheets
    add_filter( 'wp_enqueue_scripts', array( $this, 'inboundrocket_wm_styles' ), 0 );
        
    add_action( 'wp_loaded', array( $this, 'ir_wm_load_boxes' ) );      

    $this->admin = WPWelcomeMatAdmin::init();

    // load the actual output in the footer
    add_action('wp_footer', array( $this, 'ir_wm_add_welcome_mat'));   
  }

  public function admin_init ()
  {
      // Register power up settings
	  register_setting('inboundrocket_wm_options', 'inboundrocket_wm_options');
	  add_settings_section('ir_wm_section', '', '', 'inboundrocket_wm_options');
	  add_settings_field('ir_wm_settings', __('Welcome Mat Settings','inbound-rocket'), array($this->admin, 'ir_wm_input_fields'), 'inboundrocket_wm_options', 'ir_wm_section');          
  }

  function power_up_setup_callback ( )
  {
      $this->admin->power_up_setup_callback();
  }

  /**
   * Load the scripts and styles
   */
  function inboundrocket_wm_styles()
  {
    wp_enqueue_style( 'dashicons' );
    wp_enqueue_style( 'inboundrocket_wm_style' );
    wp_enqueue_script( 'jquery-ui-core');
    wp_enqueue_script( 'jquery-effects-bounce' );
    wp_enqueue_script( 'jquery-cookie-js', INBOUNDROCKET_WELCOME_MAT_PATH . "/js/js.cookie.js", array(), 0 );
  }

  function ir_wm_get_all_boxes( $matched_ids )
  {
    // query on ir-welcome-mat post type for matched ids
    $all_boxes = get_posts(
      array(
        'post_type'     => 'ir-welcome-mat',
        'post_status'     => 'publish',
        'post__in'      => $matched_ids,
        'posts_per_page'  => -1
      )
    );  
    return $all_boxes;
  }

  function ir_wm_get_filter_rules()
  {
    $rules = get_option('inboundrocket_wm_options');
      
    if( ! is_array( $rules ) ) {
      return array();
    }
    return $rules;
  }

  function ir_wm_compare_condition( $condition, $value )
  {
    $matched = false;
    $value = trim( $value );
    $value = array_map( 'trim', explode( ',', rtrim( trim( $value ), ',' ) ) );
    
    switch ( $condition ) {
      case 'everywhere';
        $matched = true;
        break;
  
      /*case 'is_url':
        $matched = match_patterns( $_SERVER['REQUEST_URI'], $value ); //@TODO
        break;
  
      case 'is_referer':
        if( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
          $referer = $_SERVER['HTTP_REFERER'];
          $matched = match_patterns( $referer, $value );
        }
        break;*/
  
      case 'is_post_type':
        $post_type =  get_post_type();
        if($post_type && is_array($value)) $matched = in_array( $post_type, $value ); else $matched = false;
        break;
  
      case 'is_single':
      case 'is_post':
        $matched = is_single( $value );
        break;
  
      case 'is_post_in_category':
        $matched = is_singular( 'post' ) && has_category( $value );
        break;
  
      case 'is_page':
        $matched = is_page( $value );       
        break;
    }
    return $matched;
  }

  function ir_wm_filter_boxes()
  {
    $matched_ids = array();
    $rules = $this->ir_wm_get_filter_rules();

    foreach( $rules as $box_id => $box_rules ) {
      $matched = false;
      $comparison = !empty( $box_rules['comparison'] ) ? $box_rules['comparison'] : 'any';
      
      if(!empty($box_rules['comparison'])) unset( $box_rules['comparison'] );
            
      if(!empty($box_rules) && is_array($box_rules)){
        foreach ( $box_rules as $rule ) {
          if( empty( $rule['condition'] ) ) {
            continue;
          }
          
          $matched = $this->ir_wm_compare_condition( $rule['condition'], $rule['value'] );
    
          // break out of loop if we've already matched
          if( $comparison === 'any' && $matched ) {
            break;
          }
          if( $comparison === 'all' && ! $matched ) {
            break;
          }
        }
      }
      if ( $matched ) {
        $matched_ids[] = $box_id;
      }   
    }
    return $matched_ids;
  }

  function ir_wm_load_boxes()
  { 
    $matched_ids = $this->ir_wm_filter_boxes();  
    
    // query on ir-welcome-mat post type for matched ids
    $all_boxes = $this->ir_wm_get_all_boxes($matched_ids);

    if(!empty($all_boxes)){
        
      $boxes_options = array();
      
      foreach( $all_boxes as $box ){
        
        $ir_wm_global_option = get_option('inboundrocket_wm_options');
        $test_mode_global    = !empty($ir_wm_global_option['ir_wm_test_mode']) ? $ir_wm_global_option['ir_wm_test_mode'] : 0;
                
        $box_options = get_post_meta($box->ID, INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY, true);   
        
        $box_test_mode = ( $test_mode_global == 1 ) ? 1 : $box_options['test_mode'];
        // array with box options
        $options = array(
          'id'         => $box->ID,
          'hideScreenSize'     => $box_options['hide_on_screen_size'],
          'test_mode'        => $box_test_mode,
        );
        $boxes_options[ $box->ID ] = $options;

        //new Scroll_Box($box);
      }
      wp_localize_script( 'inboundrocket_wm_script', 'ir_wm_box_js_options', $boxes_options );    
    }
  }

  function ir_wm_get_welcome_mat_rules($currentPageId) {
    $ir_wm_rules = array();
    $global_rules = $this->ir_wm_get_filter_rules();
    if(isset($global_rules['ir_wm_test_mode']) && $global_rules['ir_wm_test_mode'] == 1) {
      $args = array('post_type' => 'ir-welcome-mat',
                  'post_status' => 'publish',
                  'posts_per_page' => 1,
                  'orderby' => 'date',
                  'order' => 'DESC');
      $WMPosts = new WP_Query( $args );
      if($WMPosts->found_posts) {
        $ir_wm_rules['global_test_mode'] = $WMPosts->posts[0]->ID;
      }
    } else {
      $currentPageTitle = get_the_title($currentPageId);
      $currentPostType = get_post_type($currentPageId);
      $post_slug = get_post_field( 'post_name', get_post($currentPageId) );
      $arrCategories = wp_get_post_categories( $currentPageId );
      $args = array('post_type' => 'ir-welcome-mat',
                  'post_status' => 'publish',
                  'posts_per_page' => -1,
                  'orderby' => 'date',
                  'order' => 'DESC');
      $WMPosts = new WP_Query( $args );
      if($WMPosts->found_posts) {
        foreach($WMPosts->posts as $WMpost) {
          $wm_cookie = array();
          if(isset($_COOKIE['ir_wm_' . $currentPageId . "_" . $WMpost->ID])) {
            $wm_cookie = array($_COOKIE['ir_wm_' . $currentPageId . "_" . $WMpost->ID]);
          }

          $box_options = get_post_meta($WMpost->ID, INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY, true);
          $rule_matches = $box_options['rule_matches'];
          $test_mode = $box_options['test_mode'];
          switch ($rule_matches) {
            case "any":
              if(isset($box_options['rules']) /* && !empty($box_options['rules']) */) {
                foreach($box_options['rules'] as $rules) {
                  if((isset($rules['condition']) && $rules['condition'] != '')) {
                    $data = array_map('trim', explode(",", $rules['value']));
                    switch($rules['condition']) {
                      case 'is_post':
                      case 'is_page':
                        if(in_array($currentPageId, $data) || in_array($post_slug, $data)) {
                          if(!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie) || (!isset($ir_wm_rules[$rules['condition']]) && $test_mode)) {
                            $ir_wm_rules[$rules['condition']] = $WMpost->ID;
                          }
                        }
                        break;
                      case 'is_post_not':
                      case 'is_page_not':
                        if(!in_array($currentPageId, $data) || !in_array($post_slug, $data)) {
                          if(!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie) || (!isset($ir_wm_rules[$rules['condition']]) && $test_mode)) {
                            $ir_wm_rules[$rules['condition']] = $WMpost->ID;
                          }
                        }
                        break;
                      case 'is_post_type':
                        if(in_array($currentPostType, $data)) {
                          if((!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie)) || (!isset($ir_wm_rules[$rules['condition']]) && $test_mode)) {
                            $ir_wm_rules[$rules['condition']] = $WMpost->ID;
                          }
                        }
                        break;
                      case 'is_post_type_not':
                        if(!in_array($currentPostType, $data)) {
                          if((!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie)) || (!isset($ir_wm_rules[$rules['condition']]) && $test_mode)) {
                            $ir_wm_rules[$rules['condition']] = $WMpost->ID;
                          }
                        }
                        break;
                      case 'is_post_in_category':
                        foreach ($arrCategories as $catIndex => $currentCatId) {
                          if(in_array($currentCatId, $data)) {
                            if((!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie)) || (!isset($ir_wm_rules[$rules['condition']]) && $test_mode)) {
                              $ir_wm_rules[$rules['condition']] = $WMpost->ID;
                            }
                          }
                        }
                        break;
                      case 'is_post_not_in_category':
                        $flagCategoryNotIn = 0;
                        foreach ($arrCategories as $catIndex => $currentCatId) {
                          if(!in_array($currentCatId, $data)) {
                            $flagCategoryNotIn++;
                          }
                        }
                        if(count($arrCategories) == $flagCategoryNotIn) {
                          if((!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie)) || (!isset($ir_wm_rules[$rules['condition']]) && $test_mode)) {
                            $ir_wm_rules[$rules['condition']] = $WMpost->ID;
                          }
                        }
                        break;
                      case 'everywhere':
                        default :
                        if(!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie)) {
                          if(empty($rules['condition'])) {
                            $ir_wm_rules['everywhere'] = $WMpost->ID;
                          }
                          else {
                            $ir_wm_rules[$rules['condition']] = $WMpost->ID;
                          }
                        }
                        break;
                    }
                  }
                }
              }
              break;
            case "all":
            default:
              if(isset($box_options['rules']) /* && !empty($box_options['rules']) */) {
                $totalRules = count($box_options['rules']);
                $totalRules--;
                $cntCorrectRule = 0;
                foreach($box_options['rules'] as $rules) {
                  if((isset($rules['condition']) && $rules['condition'] != '')) {
                    $data = array_map('trim', explode(",", $rules['value']));
                    switch($rules['condition']) {
                      case 'is_post':
                      case 'is_page':
                        if(in_array($currentPageId, $data) || in_array($post_slug, $data)) {
                          if(!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie) || (!isset($ir_wm_rules[$rules['condition']]) && $test_mode)) {
                            //$ir_wm_rules[$rules['condition']] = $WMpost->ID;
                            $cntCorrectRule++;
                          }
                        }
                        break;
                      case 'is_post_not':
                      case 'is_page_not':
                        if(!in_array($currentPageId, $data) || !in_array($post_slug, $data)) {
                          if(!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie) || (!isset($ir_wm_rules[$rules['condition']]) && $test_mode)) {
                            //$ir_wm_rules[$rules['condition']] = $WMpost->ID;
                            $cntCorrectRule++;
                          }
                        }
                        break;
                      case 'is_post_type':
                        if(in_array($currentPostType, $data)) {
                          if((!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie)) || (!isset($ir_wm_rules[$rules['condition']]) && $test_mode)) {
                            //$ir_wm_rules[$rules['condition']] = $WMpost->ID;
                            $cntCorrectRule++;
                          }
                        }
                        break;
                      case 'is_post_type_not':
                        if(!in_array($currentPostType, $data)) {
                          if((!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie)) || (!isset($ir_wm_rules[$rules['condition']])  && $test_mode)) {
                            //$ir_wm_rules[$rules['condition']] = $WMpost->ID;
                            $cntCorrectRule++;
                          }
                        }
                        break;
                      case 'is_post_in_category':
                        foreach ($arrCategories as $catIndex => $currentCatId) {
                          if(in_array($currentCatId, $data)) {
                            if((!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie)) || (!isset($ir_wm_rules[$rules['condition']])  && $test_mode)) {
                              //$ir_wm_rules[$rules['condition']] = $WMpost->ID;
                              $cntCorrectRule++;
                            }
                          }
                        }
                        break;
                      case 'is_post_not_in_category':
                        $flagCategoryNotIn = 0;
                        foreach ($arrCategories as $catIndex => $currentCatId) {
                          if(!in_array($currentCatId, $data)) {
                            $flagCategoryNotIn++;
                          }
                        }
                        if(count($arrCategories) == $flagCategoryNotIn) {
                          if((!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie)) || (!isset($ir_wm_rules[$rules['condition']])  && $test_mode)) {
                            //$ir_wm_rules[$rules['condition']] = $WMpost->ID;
                            $cntCorrectRule++;
                          }
                        }
                        break;
                      case 'everywhere':
                        default :
                        if(!isset($ir_wm_rules[$rules['condition']]) && !in_array($WMpost->ID, $wm_cookie)) {
                          if(empty($rules['condition'])) {
                            //$ir_wm_rules['everywhere'] = $WMpost->ID;
                            $cntCorrectRule++;
                          }
                          else {
                            //$ir_wm_rules[$rules['condition']] = $WMpost->ID;
                            $cntCorrectRule++;
                          }
                        }
                        break;
                    }
                  }
                }
                if($totalRules == $cntCorrectRule) {
                  $ir_wm_rules['all'] = $WMpost->ID;
                }
              }
              break;
          }
        }
      }
    }
    return $ir_wm_rules;
  }

  function ir_wm_get_welcome_mat_matching_id($result){
      if(is_array($result) && !empty($result)){
        $temp_post_id = $this->ir_wm_get_welcome_mat_id($result);
        if($temp_post_id){
          $box_options = get_post_meta($temp_post_id, INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY, true);
        }
        else{
          return 0;
        }
      }
      else{
        return 0;
      }
  }
  function ir_wm_get_welcome_mat_id($result){
    if(isset($result['global_test_mode']) && $result['global_test_mode'] != ''){
      return $result['global_test_mode'];
    } 
    else if(isset($result['all']) && $result['all'] != ''){
      return $result['all'];
    }
    else if(isset($result['is_page']) && $result['is_page'] != ''){
      return $result['is_page'];
    }
    else if(isset($result['is_page_not']) && $result['is_page_not'] != ''){
      return $result['is_page_not'];
    }
    else if(isset($result['is_post']) && $result['is_post'] != ''){
      return $result['is_post'];
    }
    else if(isset($result['is_post_not']) && $result['is_post_not'] != ''){
      return $result['is_post_not'];
    }
    else if(isset($result['is_post_in_category']) && $result['is_post_in_category'] != ''){
      return $result['is_post_in_category'];
    }
    else if(isset($result['is_post_not_in_category']) && $result['is_post_not_in_category'] != ''){
      return $result['is_post_not_in_category'];
    }
    else if(isset($result['is_post_type']) && $result['is_post_type'] != ''){
      return $result['is_post_type'];
    }
    else if(isset($result['is_post_type_not']) && $result['is_post_type_not'] != ''){
      return $result['is_post_type_not'];
    }
    else if(isset($result['everywhere']) && $result['everywhere'] != ''){
      return $result['everywhere'];
    }
    return 0;
  }

  function ir_wm_get_welcome_mat_post() {
    if(isset($_GET['preview']) && $_GET['preview'] == true) {
      $post_id = $_GET['p'];
    }
    else if(!empty($_GET['preview_id'])) {
      $post_id = $_GET['preview_id'];
    }
    else {
      $current_page_id = get_the_id();
      $result = $this->ir_wm_get_welcome_mat_rules($current_page_id);
      #echo "<pre>";print_r($result); exit;
      #$post_id = $this->ir_wm_get_welcome_mat_matching_id($result);
      $post_id = $this->ir_wm_get_welcome_mat_id($result);

    }
    $ir_wm_query = get_post($post_id);
    return $ir_wm_query;
  }

  function ir_wm_add_welcome_mat() {
    $strContent = '';
    $welcome_mat = $this->ir_wm_get_welcome_mat_post();
    if(!empty($welcome_mat->ID) && ($welcome_mat->post_type == 'ir-welcome-mat')) {
      $welcome_mat_id = $welcome_mat->ID;
      $options = get_post_meta( $welcome_mat->ID, INBOUNDROCKET_WELCOME_MAT_BOXES_META_KEY, true );
      $test_mode = $options['test_mode'];
      $hide_on_screen_size = $options['hide_on_screen_size'];

      // Test mode is "Yes" OR cookie "ir_wm_opted_out" is not set....
      if(!isset($_COOKIE['ir_wm_opted_out']) || $test_mode) {
        $unique_cookie_id = "ir_wm_" . $welcome_mat_id;

        $option = $options['css'];

        $cookie_exp_time = $options['cookie_exp_time'];
        $cookie_exp = 0;
        if($cookie_exp_time != 'always-show'){
          $cookie_exp = $options['cookie_exp'];
        }
        switch ($cookie_exp_time) {
          case 'minutes':
            $expire_time = time() + $cookie_exp * MINUTE_IN_SECONDS;
            break;
          case 'hours':
            $expire_time = time() + $cookie_exp * HOUR_IN_SECONDS;
            break;
          case 'days':
            $expire_time = time() + $cookie_exp * DAY_IN_SECONDS;
            break;
          case 'months':
            $expire_time = time() + $cookie_exp * MONTH_IN_SECONDS;
            break;
          case 'years':
            $expire_time = time() + $cookie_exp * YEAR_IN_SECONDS;
            break;
          case 'always-show':
            $expire_time = time() - (15 * 60);
            break;
        }

        $opt_choice = array();
        if(!empty($options['opt_popup']))
          $opt_choice = $options['opt_popup'];

        $redirect_url = '#';
        if(!empty($options['redirect_url']))
          $redirect_url = esc_url($options['redirect_url']);

        $method = '';
        if(!empty($options['form_variable']))
          $method = esc_attr($options['form_variable']);

        if(!empty($option['main_heading_font'])) list($main_heading_font_family, $main_heading_font_weight_size) = explode(":", $option['main_heading_font']); else $main_heading_font_family = 'Arial, san-serif';
        if(!empty($option['sub_heading_font'])) list($sub_heading_font_family, $sub_heading_font_weight_size) = explode(":", $option['sub_heading_font']);  else $sub_heading_font_family = 'Arial, san-serif';
        if(!empty($option['submit_button_font'])) list($submit_button_font_family, $submit_button_font_weight_size) = explode(":", $option['submit_button_font']);  else $submit_button_font_family = 'Arial, san-serif';
        if(!empty($option['no_thanks_button_font'])) list($no_thanks_button_font_family, $no_thanks_button_font_weight_size) = explode(":", $option['no_thanks_button_font']);  else $no_thanks_button_font_family = 'Arial, san-serif';

        $main_heading_font_weight = 'normal';
        if(!empty($option['main_heading_font_weight']))
          $main_heading_font_weight = esc_attr($option['main_heading_font_weight']);

        $main_heading_font_style = 'normal';
        if(!empty($option['main_heading_font_style']))
          $main_heading_font_style = esc_attr($option['main_heading_font_style']);
        
        $main_heading_font_align = 'left';
        if(!empty($option['main_heading_font_align']))
          $main_heading_font_align = esc_attr($option['main_heading_font_align']);
        
        $sub_heading_font_weight = 'normal';
        if(!empty($option['sub_heading_font_weight']))
          $sub_heading_font_weight = esc_attr($option['sub_heading_font_weight']);
		
		$sub_heading_font_align = 'left';
        if(!empty($option['sub_heading_font_align']))
          $sub_heading_font_align = esc_attr($option['sub_heading_font_align']);
		
        $sub_heading_font_style = 'normal';
        if(!empty($option['sub_heading_font_style']))
          $sub_heading_font_style = esc_attr($option['sub_heading_font_style']);

        $submit_button_font_weight = 'normal';
        if(!empty($option['submit_button_font_weight']))
          $submit_button_font_weight = esc_attr($option['submit_button_font_weight']);
          
        $submit_button_font_align = 'left';
        if(!empty($option['submit_button_font_align']))
          $submit_button_font_align = esc_attr($option['submit_button_font_align']);

        $submit_button_font_style = 'normal';
        if(!empty($option['submit_button_font_style']))
          $submit_button_font_style = esc_attr($option['submit_button_font_style']);

        $no_thanks_button_font_weight = 'normal';
        if(!empty($option['no_thanks_button_font_weight']))
          $no_thanks_button_font_weight = esc_attr($option['no_thanks_button_font_weight']);

        $no_thanks_button_font_style = 'normal';
        if(!empty($option['no_thanks_button_font_style']))
          $no_thanks_button_font_style = esc_attr($option['no_thanks_button_font_style']);

		$no_thanks_button_font_align = 'left';
        if(!empty($option['no_thanks_button_font_align']))
          $no_thanks_button_font_align = esc_attr($option['no_thanks_button_font_align']);
		
        $background_image_color = '';
        if(!empty($option['background_color']))
          $background_image_color = 'background-color: ' . esc_attr($option['background_color']) . ';';

        if(!empty($option['background_image'])) {
          $background_image_color = 'background-image: url("' . esc_attr($option['background_image']) . '");
                                  background-repeat: no-repeat;
                                  background-size: 100%;';
        }

        $extra_css = '';
        if(!empty($option['extra_css']))
          $extra_css =  esc_html($option['extra_css']);

        // Do not show if opted out - (Toggle "ON" and this mat WON'T show to someone who has already opted out of this app.)
        $toggle = '';
        $no_thanks_opted_out_class = '';
        if(!empty($options['toggle'])) {
          $toggle = esc_attr($options['toggle']);
          $no_thanks_opted_out_class = 'opted_out_cookie';
        }
        

        // Enable instant landing page - Removes visitors ability to scroll down. (Increases conversion 2x!)
        $landing_toggle = '';
        if(!empty($options['landing_toggle']))
          $landing_toggle = esc_attr($options['landing_toggle']);

        // Display Mode - Set how the mat is displayed on the page 
        $display_mode = '';
        if(!empty($option['display_mode']))
          $display_mode = esc_attr($option['display_mode']);

        $popup_close_style = "ts-link-remove";
        if($display_mode == 'embedded')
          $popup_close_style = "ts-link-hide";
          
        if($redirect_url!="#") {
	     	$action_target = 'target="_blank"';
	    } else {
		    $action_target = '';
		}
		
        $strContent .= '<script type="text/javascript">

                        jQuery(window).load(function() {
                          var cookie_exp_time = "' . $cookie_exp_time . '";
                          var cookie_exp = "' . $cookie_exp . '";
                        
                          switch (cookie_exp_time) {
                            case "minutes":
                              expire_time = new Date(new Date().getTime() + cookie_exp * 60 * 1000);
                              break;
                            case "hours":
                              expire_time = new Date(new Date().getTime() + (cookie_exp * 60) * 60 * 1000);
                              break;
                            case "days":
                              expire_time = new Date(new Date().getTime() + (cookie_exp * 24) * 60 * 60 * 1000);
                              break;
                            case "months":
                              expire_time = new Date(new Date().getTime() + (cookie_exp * 30) * 24 * 60 * 60 * 1000);
                              break;
                            case "years":
                              expire_time = new Date(new Date().getTime() + (cookie_exp * 365) * 24 * 60 * 60 * 1000);
                              break;
                            default:
                              expire_time = new Date(new Date().getTime());
                              break;
                          }
                          
                          if ( !Cookies.get("' . $unique_cookie_id . '") )
                            Cookies.set("' . $unique_cookie_id . '", "' . $welcome_mat_id . '", {path: "/", domain: "", expires: expire_time});

                          // Option: Show top screen at top-screen
						  setTimeout(function() {
                          	jQuery(".top-screen").removeClass("hide-at-top");
                          }, 300);
                          
                          // Close window on yes submit
                          jQuery("#wm-yes-submit").on("click",function(){
	                          var window_height = jQuery(window).height();
                              jQuery("html, body").animate({
                                scrollTop: window_height
                              }, 800);
                              
                              Cookies.set("ir_wm_opted_out", "' . $welcome_mat_id . '", {path: "/", domain: "", expires: expire_time });
                              
                              return false;
	                      });
                            
                          var window_h = jQuery(window).height();
                          var window_w = jQuery(window).width();
                          jQuery("body").addClass("show");';

                          // Option: Do not auto-show box on small screens? (i.e 500px )
                          if($hide_on_screen_size > 0) {
                            $strContent .= 'if(window_w < ' . $hide_on_screen_size . ') {
                              jQuery("body").addClass("cookie-hide");
                            }';
                          }
                          
                          // Enable instant landing page  - Removes visitors ability to scroll down. (Increases conversion 2x!)
                          if(!empty($landing_toggle) && $display_mode == 'normal') {
                            // Option: Fixed Top screen
                            $strContent .= 'jQuery("body").addClass("fixed");';
                          }

                          // Option: Cookie data
                          //$strContent .= 'jQuery("body").addClass("cookie-hide");';

			$strContent .= 'jQuery("html, body").animate({
                              scrollTop: 0
                            }, 1);
                            setTimeout(function() {
                              jQuery("body.ts-body-remove").addClass("show").removeClass("remove");
                            }, 2);


                            if (jQuery(".top-screen .ts-scroll-down.icon-round a").hasClass("ts-link-remove")) {
                              // Option1: Remove Top screen (class "ts-body-remove")
                              jQuery("body").addClass("ts-body-remove");
                            }
                            
                            jQuery(window).scroll(function() {
                              var scrolled = jQuery(window).scrollTop();
                              if(scrolled >= window_h) {
                                jQuery("body.ts-body-remove").removeClass("show").addClass("remove");
                              } else {
                                // else code
                              }
                            });

                            // Option1: Remove Top screen (class "ts-link-remove")
                            jQuery(".top-screen .ts-link-remove").click(function() {

                              // Set cookie for Opted out option...
                              if(jQuery(this).hasClass("opted_out_cookie")) {
                                Cookies.set("ir_wm_opted_out", "' . $welcome_mat_id . '", {path: "/", domain: "", expires: expire_time });
                              }

                              jQuery("body").removeClass("fixed"); 
                              setTimeout(function() {
                                jQuery("body").addClass("remove");
                                jQuery("body").removeClass("show");
                              }, 800);

                              var window_height = jQuery(window).height();
                              jQuery("html, body").animate({
                                scrollTop: window_height
                              }, 800);
                              return false;
                            });

                            // Option2: Hide Top screen ("ts-link-hide")
                            jQuery(".top-screen .ts-link-hide").click(function() {

                              // Set cookie for Opted out option...
                              if(jQuery(this).hasClass("opted_out_cookie")) {
                                Cookies.set("ir_wm_opted_out", "' . $welcome_mat_id . '", {path: "/", domain: "", expires: expire_time });
                              }

                              jQuery("body").addClass("hide");
                              setTimeout(function() {
                                jQuery("body").removeClass("show").addClass("move-top");;
                              }, 1000);

                              var window_height = jQuery(window).height();
                              jQuery("html, body").animate({
                                scrollTop: window_height
                              }, 800);
                              return false;
                            });
                            // End
                          });
                        </script>
                        <style type="text/css">
                          .show {
                            padding-top: 100vh;
                            /*overflow: hidden;*/
                          }

                          .top-screen {
                            position: absolute;
                            top: 0;
                            left: 0;
                            z-index: 9999999999;
                            display: inline-block;
                            height: 100vh;
                            width: 100%;
                            ' . $background_image_color . '
                              -webkit-transition: all 1s ease 0s;
                                 -moz-transition: all 1s ease 0s;
                                      transition: all 1s ease 0s;
                          }
                          .top-screen.hide-at-top { top: -100vh; }
                          .remove .top-screen {
                            /*top: -100vh;*/
                            display: none;
                          }
                          .hide {
                            padding-top: 100vh;
                          }
                          .hide .top-screen {
                            /*top: -100vh;*/ /* For Embedded option */
                          }
                          .move-top .top-screen {
                            position: absolute;
                            top: 0;
                          }
                          .top-screen .body-wrap {
                            position: relative;
                            display: table-cell;
                            height: 100vh;
                            width: 100vw;
                            text-align: center;
                            vertical-align: middle;
                          }
                          .top-screen .body-wrap2 {
                            display: inline-block;
                            height: -webkit-calc(100vh - 160px);
                            height:    -moz-calc(100vh - 160px);
                            height:         calc(100vh - 160px);
                            width: 100%;
                            overflow-y: auto;
                            overflow-x: hidden;
                            padding: 0;
                          }
                          .top-screen .body {
                            display: inline-block;
                            width: auto;
                            padding: 0 15px;
                          }
                          .top-screen .body .ts-title {
                            display: inline-block;
                            width: 100%;
                          }
                          .top-screen .body .ts-title h2 {
                            margin: 0;
                            color: ' . esc_attr($option['main_heading_font_color']) . ';
                            font-size: ' . esc_attr($option['main_heading_font_size']) . 'px;
                            line-height: normal;
                            font-weight: ' . $main_heading_font_weight . ';
                            font-style: ' . $main_heading_font_style . ';
                            font-family: ' . $main_heading_font_family . ';
                            text-align: ' . $main_heading_font_align . ';
                          }
                          .top-screen .body .ts-info-text {
                            color: ' . esc_attr($option['sub_heading_font_color']) . ';
                            font-size: ' . esc_attr($option['sub_heading_font_size']) . 'px;
                            line-height: normal;
                            font-weight: ' . $sub_heading_font_weight . ';
                            font-style: ' . $sub_heading_font_style . ';
                            font-family: ' . $sub_heading_font_family . ';
                            text-align: ' . $sub_heading_font_align . ';
                          }
                          .top-screen .body form {
                            display: inline-block;
                            /*width: 300px;*/
                            width: 100%;
                            max-width: 600px;
                            padding: 0;
                            margin: 0;
                          }
                          .top-screen .body .ts-form-fields input[type=\'text\'],
                          .top-screen .body .ts-form-fields input[type=\'email\'],
						  .top-screen .body .ts-form-fields input[type=\'url\'],
                          .top-screen .body .ts-form-fields input[type=\'password\'],
                          .top-screen .body .ts-form-fields input[type=\'tel\'],
                          .top-screen .body .ts-form-fields input[type=\'number\'],
                          .top-screen .body .ts-form-fields input[type=\'checkbox\'],
                          .top-screen .body .ts-form-fields select,
                          .top-screen .body .ts-form-fields textarea {
                            display: inline-block;
                            width: 100%;
                            height: 2.6em;
                            margin: 0 0 12px;
                            padding: 0 15px;
                            font-size: 16px;
                            line-height: normal;
                            font-weight: normal;
                            font-family: ' . $main_heading_font_family . ';
                            margin-bottom: 0.5em;
                            -webkit-border-radius: 5px 5px 5px 5px;
                            border-radius: 5px 5px 5px 5px;
                             -webkit-box-shadow: 0 0 0 0;
                              -moz-box-shadow: 0 0 0 0;
                              box-shadow: 0 0 0 0;
                            border: 1px solid #a0964e;
                          }
                          .top-screen .body .ts-form-action {
                            display: inline-block;
                            width: 100%;
                          }
                          .top-screen .body .ts-form-action input[type=\'button\'],
                          .top-screen .body .ts-form-action input[type=\'submit\'],
                          .top-screen .body .ts-form-action buttom,
                          .top-screen .body .ts-form-action .btn {
                            float: left;
                            width: -webkit-calc(50% - 5px);
                            width:    -moz-calc(50% - 5px);
                            width:         calc(50% - 5px);
                            padding: 12px 5px;
                            background-color: ' . esc_attr($option['submit_button_background_color']) . ';
                            border: ' . esc_attr($option['submit_button_background_color']) . ';
                            color: ' . esc_attr($option['submit_button_text_color']) . ';
                            font-size: ' . esc_attr($option['submit_button_font_size']) . 'px;
                            line-height: normal;
                            font-weight: ' . $submit_button_font_weight . ';
                            font-style: ' . $submit_button_font_style . ';
                            font-family: ' . $submit_button_font_family . ';
                            text-align: ' . $submit_button_font_align . ';
                            -webkit-border-radius: 5px;
                               -moz-border-radius: 5px;
                                    border-radius: 5px;
                            -webkit-transition: all 250ms ease-in-out;
                            -moz-transition: all 250ms ease-in-out;
                              -o-transition: all 250ms ease-in-out;
                                -ms-transition: all 250ms ease-in-out;
                                  transition: all 250ms ease-in-out;
                            text-align: ' . $submit_button_font_align . ';
                          }

                          .top-screen .body .ts-form-action .btn:hover{
                            background-color: ' . esc_attr($option['submit_button_hover_background_color']) . ';
                            color: ' . esc_attr($option['submit_button_text_hover_color']) . '; 
                          }

                          .top-screen .body .ts-form-action .btn.btn-cancel {
                            padding: 12px 5px;
                            background-color: ' . esc_attr($option['no_thanks_button_background_color']) . ';
                            border: 1px solid ' . esc_attr($option['no_thanks_button_background_color']) . ';
                            color: ' . esc_attr($option['no_thanks_button_text_color']) . ';
                            font-size: ' . esc_attr($option['no_thanks_button_font_size']) . 'px;
                            line-height: normal;
                            font-weight: ' . $no_thanks_button_font_weight . ';
                            font-style: ' . $no_thanks_button_font_style . ';
                            font-family: ' . $no_thanks_button_font_family . ';
                            text-align: ' . $no_thanks_button_font_align . ';
                          }
                          .top-screen .body .ts-form-action .btn:last-child {
                            float: right;
                          }

                          .top-screen .body .ts-form-action .btn.btn-cancel:hover{
                            background-color: ' . esc_attr($option['no_thanks_button_hover_background_color']) . ';
                            color: ' . esc_attr($option['no_thanks_button_text_hover_color']) . ';
                          }

                          .top-screen .ts-powered-by {
                            position: absolute;
                            top: auto;
                            bottom: 0;
                            left: 0;
                            margin: 0 0 30px 30px;
                          }
                          .top-screen .ts-powered-by p {
                            margin: 0;
                            font-size: 12px;
                            font-family: Arial;
                            text-transform: uppercase;
                          }
                          .top-screen .ts-scroll-down {
                            position: absolute;
                            top: auto;
                            bottom: 0;
                            left: 50%;
                            height: 48px;
                            width: 48px;
                            margin: 0 0 18px -24px;
                          }
                          .top-screen .ts-scroll-down a {
                            position: relative;
                            display: inline-block;
                            height: 48px;
                            width: 48px;
                            margin: 0;
                            border: 4px solid ' . esc_attr($option['submit_arrow_border_color']) . ';
                            font-family: "Dashicons";
                          }
                          .top-screen .ts-scroll-down.icon-round a {
                            -webkit-border-radius: 100px;
                               -moz-border-radius: 100px;
                                    border-radius: 100px;
                          }
                          .top-screen .ts-scroll-down a::before {
                            position: absolute;
                            top: 0;
                            left: 0;
                            display: inline-block;
                            height: 100%;
                            width: 100%;
                            margin: 8px 0 0 0;
                            padding: 0;
                            color: ' . esc_attr($option['arrow_color']) . ';
                            font-size: 20px;
                            font-style: normal;
                            font-family: "Dashicons";
                            text-align: center;
                            vertical-align: middle;
                            content: "\f347";
                          }
                          .top-screen .ts-scroll-down.icon-cross a::before {
                            content: "\f158";
                          }
                          /*@media screen and (max-width: 600px) {
                            .top-screen .ts-scroll-down {
                              right: 0;
                              left: auto;
                              margin-right: 30px;
                            }
                          }*/
                          @media (max-width: 992px) {
                            .top-screen .body-wrap {
                             /* height: -webkit-calc(100vh - 106px);
                              height:    -moz-calc(100vh - 106px);
                              height:         calc(100vh - 106px);*/
                              height: auto;
                              padding-top: 20px;
                            }
                            .top-screen .body-wrap2 {
                              height: 100%;
                              max-height: -webkit-calc(100vh - 120px);
                              max-height:    -moz-calc(100vh - 120px);
                              max-height:         calc(100vh - 120px);
                            }
                            .top-screen .ts-powered-by {
                                position: relative;
                                width: 100%;
                                margin-left: 0;
                                /*margin-bottom: 80px;*/
                                text-align: center;
                            }
                          }

                          .irwm-form-field {
                            display: inline-block;
                            width: 100%;
                            margin-bottom: 15px;
                          }
                          .irwm-form-field .field_title,
                          .irwm-form-field .field_control {
                            float: left;
                            width: 100%;
                            /* padding: 0 10px; */
                            text-align: left;
                          }
                          .field_control label {
                            font-size: 16px;
                            line-height: normal;
                            font-weight: normal;
                            font-family: ' . $main_heading_font_family . ';
                          }
                          .irwm-form-field .field_title {
                            text-align: left;
                            padding-top: 7px;
                            font-size: 16px;
                            line-height: normal;
                            font-weight: normal;
                            font-family: ' . $main_heading_font_family . ';
                          }
                          .irwm-form-field .checkbox_group input,
                          .irwm-form-field .checkbox_group label,
                          .irwm-form-field .radio_group input,
                          .irwm-form-field .radio_group label  {
                            display: inline-block;
                          }

                          /* For Cookie data */
                          .cookie-hide { padding-top: 0 !important; }
                          .cookie-hide .top-screen { display: none !important; }
                          /* End */

                          /* For fixed position of Top screen */
                          .fixed {
                            overflow: hidden;
                          }
                          .fixed .top-screen {
                            position: fixed;
                          }
                          .inboundrocket-wm-branding {
	                        color: ' . esc_attr($option['main_heading_font_color']) . ';
	                        opacity: 0.5;
	                        filter:alpha(opacity=50); /* For IE8 and earlier */
	                      }
                          /* End */
                          '
                          . $extra_css .
                        '</style>';
		
		$strContent .= '<div class="top-screen hide-at-top">
                <div class="body-wrap">
                  <div class="body-wrap2">
                    <div class="body">
                      <div class="ts-title">
                        <h2> ' . __(esc_html($option['main_heading_text']), 'inbound-rocket' ) . '</h2>
                      </div>
                      <div class="ts-info-text">
                        <p> ' . __(esc_html($option['sub_heading_text']), 'inbound-rocket' )  . '</p>
                      </div>
                      <form id="welcome-mat_' . $welcome_mat_id . '" method="' . $method . '" action="' . $redirect_url . '" '.$action_target.'>
                        <div class="ts-form-fields">';
                          $total_fields = $option['total_fields'];
						  for ($iCnt = 0; $iCnt <= $total_fields; $iCnt++) {
                            if(!empty($option['field_name'][$iCnt])) {
                              $field_type = esc_attr($option['field_type'][$iCnt]);
                              $field_name = esc_attr($option['field_name'][$iCnt]);
                              if(!empty($field_name)) {
                                $field_required = '';
                                if(!empty($option['field_required'][$iCnt])) {
                                  $field_required = ' required';
                                }
                                $field_variable_name = esc_attr($option['field_variable_name'][$iCnt]);
                                $strContent .= '<div class="irwm-form-field">';
                                  // @OTODO why is there a label for a hidden value?
                                  if($field_type == 'checkbox_input' || $field_type == 'hidden_value') {
                                    // $strContent .= '<label class="field_title">'. $field_name .'</label>';
                                  }
                                  else {
                                    // $strContent .= '<label class="field_title">' . __($field_name, 'inbound-rocket' ) . '</label>';
                                  }
                                  $strContent .= '<span class="field_control">';
                                    switch ($field_type) {
                                      case 'country_dropdown':
                                        $countries = $this->get_countries();
                                        $strContent .= '<select id="'. $field_variable_name . '" name="' . $field_variable_name . '"' . $field_required . '>';
                                        foreach($countries as $key => $value) {
                                          $strContent .= '<option value="' . $key . '" title="' . __(esc_attr($value), 'inbound-rocket' ) . '">' . __(esc_html($value), 'inbound-rocket' ) . '</option>';
                                        }
                                        $strContent .= '</select>';
                                        break;
                                      case 'checkbox_group':
                                        if(!empty($opt_choice['field_name'][$iCnt])) {
                                          foreach ($opt_choice['field_name'][$iCnt] as $index => $opt_title) {
                                            if(!empty($opt_choice['field_value'][$iCnt][$index])) {
                                              $opt_value = $opt_choice['field_value'][$iCnt][$index];
                                              $strContent .= '<div class="checkbox_group">
                                                <input type="checkbox" id="' . $field_variable_name . $iCnt .  $index . '" name="' . $field_variable_name . '[]" value="' . esc_attr($opt_value) . '"' . $field_required . ' />
                                                <label for="' . $field_variable_name . $iCnt . $index . '">' . __(esc_html($opt_title), 'inbound-rocket' ) . '</label>
                                              </div>';
                                            }
                                          }
                                        }
                                        break;
                                      case 'radio_group':
                                        if(!empty($opt_choice['field_name'][$iCnt])) {
                                          $radioCnt = 0;
                                          foreach ($opt_choice['field_name'][$iCnt] as $index => $opt_title) {
                                            if(!empty($opt_choice['field_value'][$iCnt][$index])) {
                                              $strChecked = '';
                                              if($radioCnt == 0) {
                                                $strChecked = ' checked="checked"';
                                              }
                                              $opt_value = $opt_choice['field_value'][$iCnt][$index];
                                              $strContent .= '<div class="radio_group">
                                                <input type="radio" id="' . $field_variable_name . $iCnt . $index . '" name="' . $field_variable_name . '[]" value="' . esc_attr($opt_value) . '"' . $field_required . $strChecked . '/>
                                                <label for="' . $field_variable_name . $iCnt . $index . '">' . __(esc_html($opt_title), 'inbound-rocket' ) . '</label>
                                              </div>';
                                              $radioCnt++;
                                            }
                                          }
                                        }
                                        break;
                                      case 'dropdown_list':
                                        if(!empty($opt_choice['field_name'][$iCnt])) {
                                          $strContent .= '<select id="' . $field_variable_name . $iCnt . $index . '" name="' . $field_variable_name . '"' . $field_required . '>';
                                            foreach ($opt_choice['field_name'][$iCnt] as $index => $opt_title) {
                                              if(!empty($opt_choice['field_value'][$iCnt][$index])) {
                                                $opt_value = $opt_choice['field_value'][$iCnt][$index];
                                                $strContent .= '<option value="' . esc_attr($opt_value) . '">' . __(esc_html($opt_title), 'inbound-rocket' ) . '</option>';
                                              }
                                            }
                                          $strContent .= '</select>';
                                        }
                                        break;
                                      case 'email_address':
                                        $strContent .= '<input type="email" id="' . $field_variable_name . '" name="' . $field_variable_name . '" placeholder="' . __($field_name, 'inbound-rocket' ) . '"' . $field_required . ' />';
                                        break;
                                      case 'website_url':
                                        $strContent .= '<input type="url" id="' . $field_variable_name . '" name="' . $field_variable_name . '" placeholder="' . __($field_name, 'inbound-rocket' ) . '"' . $field_required . ' pattern="https?://.+"/>';
                                        break;
                                      case 'phone_number':
                                        $strContent .= '<input type="text" id="' . $field_variable_name . '" name="' . $field_variable_name . '" placeholder="' . __($field_name, 'inbound-rocket' ) . '"' . $field_required . ' pattern="[0-9]{10}" />';
                                        break;
                                      case 'checkbox_input':
                                        $strContent .= '<input type="checkbox" id="' . $field_variable_name . '" name="' . $field_variable_name . '" placeholder="' . __($field_name, 'inbound-rocket' ) . '"' . $field_required . ' />';
                                         $strContent .= '<label for="' . $field_variable_name . '">' . __($field_name, 'inbound-rocket' ) . '</label>';
                                        break;
                                      case 'hidden_value':
                                        $strContent .= '<input type="hidden" name="' . __($field_name, 'inbound-rocket' ) . '" id="' . __($field_name, 'inbound-rocket' ) . '" value="' . $field_variable_name . '"' . $field_required . ' />';
                                        break;
                                      default:
                                        $strContent .= '<input type="text" id="' . $field_variable_name . '" name="' . $field_variable_name . '" placeholder="' . __($field_name, 'inbound-rocket' ) . '"' . $field_required . ' />';
                                        break;
                                    }
                                  $strContent .= '</span>
                                </div>';
                              }
                            }
                          }

                        $submit_button_type = "submit";
                        $submit_button_close_class = '';
                        if(($redirect_url == '#')) {
                          $submit_button_type = "button";
                          $submit_button_close_class = $popup_close_style;
                        }

                        $strContent .= '</div>
                        <div class="ts-form-action">';
                        if($method == '') {
                          $strContent .= '<a href="' . $redirect_url . '" id="wm-yes-submit" class="btn">' . __($option['submit_button_text'], 'inbound-rocket' ) . '</a>';
                        }
                        else {
                          $strContent .= '<input type="' . $submit_button_type . '" class="btn ' . $submit_button_close_class . '" value="' . __(esc_html($option['submit_button_text']), 'inbound-rocket' ) . '">';
                        }
                        $strContent .= '<input type="button" class="btn btn-cancel ' . $popup_close_style . ' ' . $no_thanks_opted_out_class . '" value="' . __(esc_html($option['no_thanks_button_text']), 'inbound-rocket' ) . '">
                        </div>
                      </form>
                    </div>
                  </div> 
                </div>';
            if ( ! inboundrocket_check_premium_user() ) { 
            $strContent .= '<div class="ts-powered-by">
               <p class="inboundrocket-wm-branding">'. __('Powered by', 'inbound-rocket' ) .' Inbound Rocket</p>
            </div>';
          }
          switch ($option['arrow_icon']) {
            case 'round-arrdown':
              $arrow_icon = 'icon-round';
              break;
            case 'square-arrdow':
              $arrow_icon = '';
              break;
            case 'round-cross':
              $arrow_icon = 'icon-round icon-cross';
              break;
            case 'square-cross':
              $arrow_icon = 'icon-cross';
              break;
            default:
              # code...
              $arrow_icon = '';
              break;
          }
          $strContent .= '<div class="ts-scroll-down ' . $arrow_icon . '">
            <a href="#" class="' . $popup_close_style . '">&nbsp;</a>
          </div>
        </div>';
      }
    }
    echo $strContent;
  }

  function get_countries() {
      return array("AF" => "Afghanistan",
                "AX" => "Åland Islands",
                "AL" => "Albania",
                "DZ" => "Algeria",
                "AS" => "American Samoa",
                "AD" => "Andorra",
                "AO" => "Angola",
                "AI" => "Anguilla",
                "AQ" => "Antarctica",
                "AG" => "Antigua and Barbuda",
                "AR" => "Argentina",
                "AM" => "Armenia",
                "AW" => "Aruba",
                "AU" => "Australia",
                "AT" => "Austria",
                "AZ" => "Azerbaijan",
                "BS" => "Bahamas",
                "BH" => "Bahrain",
                "BD" => "Bangladesh",
                "BB" => "Barbados",
                "BY" => "Belarus",
                "BE" => "Belgium",
                "BZ" => "Belize",
                "BJ" => "Benin",
                "BM" => "Bermuda",
                "BT" => "Bhutan",
                "BO" => "Bolivia",
                "BA" => "Bosnia and Herzegovina",
                "BW" => "Botswana",
                "BV" => "Bouvet Island",
                "BR" => "Brazil",
                "IO" => "British Indian Ocean Territory",
                "BN" => "Brunei Darussalam",
                "BG" => "Bulgaria",
                "BF" => "Burkina Faso",
                "BI" => "Burundi",
                "KH" => "Cambodia",
                "CM" => "Cameroon",
                "CA" => "Canada",
                "CV" => "Cape Verde",
                "KY" => "Cayman Islands",
                "CF" => "Central African Republic",
                "TD" => "Chad",
                "CL" => "Chile",
                "CN" => "China",
                "CX" => "Christmas Island",
                "CC" => "Cocos (Keeling) Islands",
                "CO" => "Colombia",
                "KM" => "Comoros",
                "CG" => "Congo",
                "CD" => "Congo, The Democratic Republic of The",
                "CK" => "Cook Islands",
                "CR" => "Costa Rica",
                "CI" => "Cote D'ivoire",
                "HR" => "Croatia",
                "CU" => "Cuba",
                "CY" => "Cyprus",
                "CZ" => "Czech Republic",
                "DK" => "Denmark",
                "DJ" => "Djibouti",
                "DM" => "Dominica",
                "DO" => "Dominican Republic",
                "EC" => "Ecuador",
                "EG" => "Egypt",
                "SV" => "El Salvador",
                "GQ" => "Equatorial Guinea",
                "ER" => "Eritrea",
                "EE" => "Estonia",
                "ET" => "Ethiopia",
                "FK" => "Falkland Islands (Malvinas)",
                "FO" => "Faroe Islands",
                "FJ" => "Fiji",
                "FI" => "Finland",
                "FR" => "France",
                "GF" => "French Guiana",
                "PF" => "French Polynesia",
                "TF" => "French Southern Territories",
                "GA" => "Gabon",
                "GM" => "Gambia",
                "GE" => "Georgia",
                "DE" => "Germany",
                "GH" => "Ghana",
                "GI" => "Gibraltar",
                "GR" => "Greece",
                "GL" => "Greenland",
                "GD" => "Grenada",
                "GP" => "Guadeloupe",
                "GU" => "Guam",
                "GT" => "Guatemala",
                "GG" => "Guernsey",
                "GN" => "Guinea",
                "GW" => "Guinea-bissau",
                "GY" => "Guyana",
                "HT" => "Haiti",
                "HM" => "Heard Island and Mcdonald Islands",
                "VA" => "Holy See (Vatican City State)",
                "HN" => "Honduras",
                "HK" => "Hong Kong",
                "HU" => "Hungary",
                "IS" => "Iceland",
                "IN" => "India",
                "ID" => "Indonesia",
                "IR" => "Iran, Islamic Republic of",
                "IQ" => "Iraq",
                "IE" => "Ireland",
                "IM" => "Isle of Man",
                "IL" => "Israel",
                "IT" => "Italy",
                "JM" => "Jamaica",
                "JP" => "Japan",
                "JE" => "Jersey",
                "JO" => "Jordan",
                "KZ" => "Kazakhstan",
                "KE" => "Kenya",
                "KI" => "Kiribati",
                "KP" => "Korea, Democratic People's Republic of",
                "KR" => "Korea, Republic of",
                "KW" => "Kuwait",
                "KG" => "Kyrgyzstan",
                "LA" => "Lao People's Democratic Republic",
                "LV" => "Latvia",
                "LB" => "Lebanon",
                "LS" => "Lesotho",
                "LR" => "Liberia",
                "LY" => "Libyan Arab Jamahiriya",
                "LI" => "Liechtenstein",
                "LT" => "Lithuania",
                "LU" => "Luxembourg",
                "MO" => "Macao",
                "MK" => "Macedonia, The Former Yugoslav Republic of",
                "MG" => "Madagascar",
                "MW" => "Malawi",
                "MY" => "Malaysia",
                "MV" => "Maldives",
                "ML" => "Mali",
                "MT" => "Malta",
                "MH" => "Marshall Islands",
                "MQ" => "Martinique",
                "MR" => "Mauritania",
                "MU" => "Mauritius",
                "YT" => "Mayotte",
                "MX" => "Mexico",
                "FM" => "Micronesia, Federated States of",
                "MD" => "Moldova, Republic of",
                "MC" => "Monaco",
                "MN" => "Mongolia",
                "ME" => "Montenegro",
                "MS" => "Montserrat",
                "MA" => "Morocco",
                "MZ" => "Mozambique",
                "MM" => "Myanmar",
                "NA" => "Namibia",
                "NR" => "Nauru",
                "NP" => "Nepal",
                "NL" => "Netherlands",
                "AN" => "Netherlands Antilles",
                "NC" => "New Caledonia",
                "NZ" => "New Zealand",
                "NI" => "Nicaragua",
                "NE" => "Niger",
                "NG" => "Nigeria",
                "NU" => "Niue",
                "NF" => "Norfolk Island",
                "MP" => "Northern Mariana Islands",
                "NO" => "Norway",
                "OM" => "Oman",
                "PK" => "Pakistan",
                "PW" => "Palau",
                "PS" => "Palestinian Territory, Occupied",
                "PA" => "Panama",
                "PG" => "Papua New Guinea",
                "PY" => "Paraguay",
                "PE" => "Peru",
                "PH" => "Philippines",
                "PN" => "Pitcairn",
                "PL" => "Poland",
                "PT" => "Portugal",
                "PR" => "Puerto Rico",
                "QA" => "Qatar",
                "RE" => "Reunion",
                "RO" => "Romania",
                "RU" => "Russian Federation",
                "RW" => "Rwanda",
                "SH" => "Saint Helena",
                "KN" => "Saint Kitts and Nevis",
                "LC" => "Saint Lucia",
                "PM" => "Saint Pierre and Miquelon",
                "VC" => "Saint Vincent and The Grenadines",
                "WS" => "Samoa",
                "SM" => "San Marino",
                "ST" => "Sao Tome and Principe",
                "SA" => "Saudi Arabia",
                "SN" => "Senegal",
                "RS" => "Serbia",
                "SC" => "Seychelles",
                "SL" => "Sierra Leone",
                "SG" => "Singapore",
                "SK" => "Slovakia",
                "SI" => "Slovenia",
                "SB" => "Solomon Islands",
                "SO" => "Somalia",
                "ZA" => "South Africa",
                "GS" => "South Georgia and The South Sandwich Islands",
                "ES" => "Spain",
                "LK" => "Sri Lanka",
                "SD" => "Sudan",
                "SR" => "Suriname",
                "SJ" => "Svalbard and Jan Mayen",
                "SZ" => "Swaziland",
                "SE" => "Sweden",
                "CH" => "Switzerland",
                "SY" => "Syrian Arab Republic",
                "TW" => "Taiwan, Province of China",
                "TJ" => "Tajikistan",
                "TZ" => "Tanzania, United Republic of",
                "TH" => "Thailand",
                "TL" => "Timor-leste",
                "TG" => "Togo",
                "TK" => "Tokelau",
                "TO" => "Tonga",
                "TT" => "Trinidad and Tobago",
                "TN" => "Tunisia",
                "TR" => "Turkey",
                "TM" => "Turkmenistan",
                "TC" => "Turks and Caicos Islands",
                "TV" => "Tuvalu",
                "UG" => "Uganda",
                "UA" => "Ukraine",
                "AE" => "United Arab Emirates",
                "GB" => "United Kingdom",
                "US" => "United States",
                "UM" => "United States Minor Outlying Islands",
                "UY" => "Uruguay",
                "UZ" => "Uzbekistan",
                "VU" => "Vanuatu",
                "VE" => "Venezuela",
                "VN" => "Viet Nam",
                "VG" => "Virgin Islands, British",
                "VI" => "Virgin Islands, U.S.",
                "WF" => "Wallis and Futuna",
                "EH" => "Western Sahara",
                "YE" => "Yemen",
                "ZM" => "Zambia",
                "ZW" => "Zimbabwe");
  }

}

//=============================================
// Welcome Mat Connector Init
//=============================================

global $inboundrocket_welcome_mat_wp;

?>