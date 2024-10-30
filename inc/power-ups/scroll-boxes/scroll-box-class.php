<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

if( ! class_exists('Scroll_Box') ) {
	
class Scroll_Box{
	public $box;
  	public $opts;
	
  	public function __construct( $box ){	  
	  $this->box  = $box;
	  $this->opts = $this->set_box_options();	  
	  
	  add_action( 'wp_head', array( $this, 'output_css' ) );
	  add_action( 'wp_footer', array( $this, 'output_html' ) );
  	}

	/* initialize box options */
  	public function set_box_options(){
	  $default = array(
			  'css' => array(
						  'background_color' => '',
						  'text_color'	   	 => '',
						  'box_width' 	     => '',
						  'border_color' 	 => '',
						  'border_width' 	 => '',
						  'border_style' 	 => '',
						  'extra_css'		 => ''
					  ), 
				);
	  $option_value = get_post_meta($this->box->ID, INBOUNDROCKET_SCROLL_BOXES_META_KEY, true); 

	  $css_opts   = $option_value['css'];
	  
	  if( !isset( $css_opts ) )
	    $css_opts = $default['css'];
	  else
	    $css_opts = array_merge($default['css'], $css_opts);

	  $options['css'] = $css_opts;
	  $options['animation'] =  $option_value['animation'];
	  
	  return $options;
  	}
	
	public function box_css_classes(){	
	  $css_class = array( 'ir-sb', 'ir-sb-' . $this->box->ID );
	  $position  = $this->opts['css']['position'];
	  if('' !== $position){
		  $css_class[] = 'ir-sb-'.$position;
	  }
	  return implode(' ', $css_class);
	}

  	public function output_html(){
	  $box_post = get_post( $this->box->ID );
	  
	  $output = '';
	  $output .= '<div class="ir-sb-container">'.PHP_EOL;
	  $output .= '<div class="'. $this->box_css_classes().'">'.PHP_EOL;
	  $output .= '<a class="ir-sb-close" title="close">×</a>'.PHP_EOL;
	  $output .= apply_filters('the_content', $box_post->post_content).PHP_EOL;  
	  $output .= '</div>'.PHP_EOL;
	  $output .= '</div>';
	  
	  echo $output;
  	}
  
  	public function output_css(){
	  $css = $this->opts['css'];
	  
	  $output = '';
	  $output .= '<style type="text/css">'.PHP_EOL;
	  
	  $output .= sprintf('.ir-sb-%s {', $this->box->ID).PHP_EOL;
	  
	  if('' != $css['background_color']){
	    $output .= sprintf('background: %s !important;', $css['background_color']).PHP_EOL;
	  }
	  
	  if('' != $css['text_color']){
	  	$output .= sprintf('color: %s !important;', $css['text_color']).PHP_EOL;
	  }
	  
	  if('' != $css['box_width']){
		$output .= sprintf('width: %spx !important;', $css['box_width']).PHP_EOL;
	  }
	  	  
	  if('' === $css['border_width']){
		  $css['border_width'] = 0;
	  }
	  
	  if('' != $css['border_style']){		
		$output .= sprintf('border: %spx %s %s !important;', $css['border_width'], $css['border_style'], $css['border_color']).PHP_EOL;
	  }
	  $output .= '}'.PHP_EOL;
	  
	  $output .= sprintf('.ir-sb-%s .ir-sb-close{', $this->box->ID).PHP_EOL;
	  if('' != $css['border_color']){
		  $output .= sprintf('color: %s !important;', $css['border_color']).PHP_EOL;
	  }
	  $output .= '}'.PHP_EOL;
	  
	  if('' != $css['extra_css']){
		$output .= $css['extra_css'].PHP_EOL;
	  }
	  $output .= '</style>'.PHP_EOL;
	  
	  echo $output;
	}
}

}
?>