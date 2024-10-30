<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security'); 

//=============================================
// WPInboundRocketAdmin Class
//=============================================
class WPWelcomeBarAdmin extends WPInboundRocketAdmin {
    
    var $power_up_icon;
    var $options;
    
    private static $_instance = null;
    
    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }
    
    /**
     * Class constructor
     */
    function __construct()
    {
        //=============================================
        // Hooks & Filters
        //=============================================
        
        if ( is_admin() )
        {
            $this->options = get_option('inboundrocket_wb_options');
			
			add_action( 'admin_enqueue_scripts',  array($this, 'ir_wb_admin_scripts') );
        }
    }
    
    public static function init(){
        if(self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;            
    }
	
	/**
     * Load admin scripts for color picker
     *
     * @param string $hook
     */
	function ir_wb_admin_scripts($hook) {
		wp_enqueue_style( 'wp-color-picker' );
	}
    
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    function sanitize( $input )
    {
	    $new_input = array();
		
		if( isset( $input['ir_wb_cta_desktop'] ) )
            $new_input['ir_wb_cta_desktop'] = sanitize_text_field( $input['ir_wb_cta_desktop'] );

        if( isset( $input['ir_wb_cta_mobile'] ) )
            $new_input['ir_wb_cta_mobile'] = sanitize_text_field( $input['ir_wb_cta_mobile'] );

        if( isset( $input['ir_wb_email_placeholder'] ) )
            $new_input['ir_wb_email_placeholder'] = sanitize_text_field( $input['ir_wb_email_placeholder'] );

        if( isset( $input['ir_wb_button_text'] ) )
            $new_input['ir_wb_button_text'] = sanitize_text_field( $input['ir_wb_button_text'] );

        if( isset( $input['ir_wb_success_text'] ) )
            $new_input['ir_wb_success_text'] = sanitize_text_field( $input['ir_wb_success_text'] );

        if( isset( $input['ir_wb_interval'] ) )
            $new_input['ir_wb_interval'] = sanitize_text_field( $input['ir_wb_interval'] );
                        
        if( isset( $input['ir_wb_show_every'] ) )
            $new_input['ir_wb_show_every'] = sanitize_text_field( $input['ir_wb_show_every'] );
            
        if( isset( $input['ir_wb_color'] ) )
            $new_input['ir_wb_color'] = sanitize_text_field( $input['ir_wb_color'] );
		
        if( isset( $input['ir_wb_text_color'] ) )
            $new_input['ir_wb_text_color'] = sanitize_text_field( $input['ir_wb_text_color'] );
		
        if( isset( $input['ir_wb_button_color'] ) )
            $new_input['ir_wb_button_color'] = sanitize_text_field( $input['ir_wb_button_color'] );
		
        if( isset( $input['ir_wb_button_text_color'] ) )
            $new_input['ir_wb_button_text_color'] = sanitize_text_field( $input['ir_wb_button_text_color'] );
        
        if( isset( $input['ir_wb_hide'] ) )
            $new_input['ir_wb_hide'] = sanitize_text_field( $input['ir_wb_hide'] ); 
        
        if( isset( $input['ir_wb_show_on'] ) )
            $new_input['ir_wb_show_on'] = sanitize_text_field( $input['ir_wb_show_on'] ); 
            
        return $new_input;
    }
    
	/**
     * Prints input form for settings page
     */	
	function ir_wb_input_fields () {
								
		$ir_wb_cta_desktop = isset($this->options['ir_wb_cta_desktop']) ? esc_attr( $this->options['ir_wb_cta_desktop'] ) : '';
		$ir_wb_cta_mobile = isset($this->options['ir_wb_cta_mobile']) ? esc_attr( $this->options['ir_wb_cta_mobile'] ) : '';
		$ir_wb_email_placeholder = isset($this->options['ir_wb_email_placeholder']) ? esc_attr( $this->options['ir_wb_email_placeholder'] ) : '';
		$ir_wb_button_text = isset($this->options['ir_wb_button_text']) ? esc_attr( $this->options['ir_wb_button_text'] ) : '';
		$ir_wb_success_text = isset($this->options['ir_wb_success_text']) ? esc_attr( $this->options['ir_wb_success_text'] ) : '';
		
		$ir_wb_color = isset($this->options['ir_wb_color']) ? esc_attr( $this->options['ir_wb_color'] ) : '';
		$ir_wb_text_color = isset($this->options['ir_wb_text_color']) ? esc_attr( $this->options['ir_wb_text_color'] ) : '';
		$ir_wb_button_color = isset($this->options['ir_wb_button_color']) ? esc_attr( $this->options['ir_wb_button_color'] ) : '';
		$ir_wb_button_text_color = isset($this->options['ir_wb_button_text_color']) ? esc_attr( $this->options['ir_wb_button_text_color'] ) : '';
		
		$ir_wb_show_after = isset($this->options['ir_wb_show_after']) ? esc_attr($this->options['ir_wb_show_after']) : '0';
		$ir_wb_hide_after = isset($this->options['ir_wb_hide_after']) ? esc_attr($this->options['ir_wb_hide_after']) : '0';
			
		$ir_wb_show_on = isset($this->options['ir_wb_show_on']) ? esc_attr( $this->options['ir_wb_show_on'] ) : 'both';	
		
		$ir_wb_hide = isset($this->options['ir_wb_hide']) ? esc_attr( $this->options['ir_wb_hide'] ) : '';
		$ir_wb_goal = isset($this->options['ir_wb_goal']) ? esc_attr( $this->options['ir_wb_goal'] ) : 'both';
		
		if( empty( $ir_wb_color ) ) $ir_wb_color = 'black';
		if( empty( $ir_wb_text_color ) ) $ir_wb_text_color = 'white';
		if( empty( $ir_wb_button_color ) ) $ir_wb_button_color = 'grey';
		if( empty( $ir_wb_button_text_color ) ) $ir_wb_text_color = 'black';

		?>     
				<tr>
					<th><label for="ir_wb_show_on"><?php _e('Show Welcome Bar','inbound-rocket');?>:</label></th>
					<td>
						<select name="inboundrocket_wb_options[ir_wb_show_on]" id="ir_wb_show_on">
							<option value="both" <?php selected( $ir_wb_show_on, 'both' ); ?>><?php _e('on both desktop and mobile','inbound-rocket');?></option>
							<option value="desktop" <?php selected( $ir_wb_show_on, 'desktop' ); ?>><?php _e('on desktop only','inbound-rocket');?></option>
							<option value="mobile" <?php selected( $ir_wb_show_on, 'mobile' ); ?>><?php _e('on mobile only','inbound-rocket');?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th><label for="ir_wb_show_on"><?php _e('Select Your Goal','inbound-rocket');?>:</label></th>
					<td>
						<select name="inboundrocket_wb_options[ir_wb_goal]" id="ir_wb_goal">
							<option>Promote a Sale/Discount - Point visitors to your latest deal, bargain, or giveaway.</option>
							<option>Talk to Your Visitors - Encourage mobile visitors to give you a ring.</option>
							<option>Grow Your Mailing List - Convert one-time visits into lasting connections.</option>
							<option>Get Facebook Likes - Expand your social reach to maximize word of mouth.</option>
							<option>Other - Don't see your goal? Customize Welcome Bar to fit your needs.</option>
						</select>
					</td>
				</tr>
			</tbody>
			<tbody id="goal-other" style="background-color:#efefef;">
				<th><h3>Custom Welcome Bar</h3></th>
				<tr style="padding-top:30px;">
					<th><label for="ir_wb_cta_desktop"><?php _e('Call to Action (Desktop)','inbound-rocket');?>:</label></th>
					<td><input style="width:100%;" type="text" name="inboundrocket_wb_options[ir_wb_cta_desktop]" id="ir_wb_cta_desktop" value="<?=$ir_wb_cta_desktop;?>">
					<br /><span class="description"><?php _e('Customise the message and call to action for visitors on a desktop browser.','inbound-rocket');?></span></td>
				</tr>
				<tr>
					<th><label for="ir_wb_cta_mobile"><?php _e('Call to Action (Mobile)','inbound-rocket');?>:</label></th>
					<td><input style="width:100%;" type="text" name="inboundrocket_wb_options[ir_wb_cta_mobile]" id="ir_wb_cta_mobile" value="<?=$ir_wb_cta_mobile;?>">
					<br /><span class="description"><?php _e('Customise the message and call to action for visitors on a mobile browser.','inbound-rocket');?></span></td>
				</tr>
				<tr>
					<th><label for="ir_wb_email_placeholder"><?php _e('Email Address Placeholder Text','inbound-rocket');?>:</label></th>
					<td><input style="width:30%;" type="text" name="inboundrocket_wb_options[ir_wb_email_placeholder]" id="ir_wb_email_placeholder" value="<?=$ir_wb_email_placeholder;?>">
					<br /><span class="description"><?php _e('Customise the email input field placeholder text.','inbound-rocket');?></span></td>
				</tr>
				<tr>
					<th><label for="ir_wb_button_text"><?php _e('Button text','inbound-rocket');?>:</label></th>
					<td><input style="width:30%;" type="text" name="inboundrocket_wb_options[ir_wb_button_text]" id="ir_wb_button_text" value="<?=$ir_wb_button_text;?>">
					<br /><span class="description"><?php _e('Customise the message and call to action for visitors on a desktop browser.','inbound-rocket');?></span></td>
				</tr>
				<tr style="padding-bottom:30px;">
					<th><label for="ir_wb_success_text"><?php _e('Success text','inbound-rocket');?>:</label></th>
					<td><input style="width:100%;" type="text" name="inboundrocket_wb_options[ir_wb_success_text]" id="ir_wb_success_text" value="<?=$ir_wb_success_text;?>">
					<br /><span class="description"><?php _e('Customise the thank you message.','inbound-rocket');?></span></td>
				</tr>
			</tbody>
			<tbody>
				<tr style="border-top:1px solid #eee;">
					<th><label for="ir_wb_hide"><?php _e('Show welcome bar after','inbound-rocket');?>:</label></th>
					<td>
						<input type="text" name="inboundrocket_wb_options[ir_wb_show_after]" id="ir_wb_show_after" value="<?=$ir_wb_show_after;?>" /> <?php _e('seconds','inbound-rocket');?>
						<br /><span class="description"><?php _e('This will show the welcome bar after x amount of seconds. Setting to 0 will disable this setting.','inbound-rocket');?></span>
					</td>
				</tr>
				<tr>
					<th><label for="ir_wb_hide"><?php _e('Auto-hide welcome bar after','inbound-rocket');?>:</label></th>
					<td>
						<input type="text" name="inboundrocket_wb_options[ir_wb_hide_after]" id="ir_wb_hide_after" value="<?=$ir_wb_hide_after;?>" /> <?php _e('seconds','inbound-rocket');?>
						<br /><span class="description"><?php _e('This will hide the welcome bar after x amount of seconds. Setting to 0 will disable this setting.','inbound-rocket');?></span>
					</td>
				</tr>
				<tr>
					<th><label for="ir_wb_hide"><?php _e('Hide welcome bar after submission','inbound-rocket');?>:</label></th>
					<td>
						<input type="checkbox" name="inboundrocket_wb_options[ir_wb_hide]" id="ir_wb_hide" value="1" <?php checked('1', $ir_wb_hide); ?> />
						<br /><span class="description"><?php _e('This will not show the welcome bar once the user converts.','inbound-rocket');?></span>
					</td>
				</tr>
				<tr>
					<th><label for="ir_wb_color"><?php _e('Pick your welcome bar colors','inbound-rocket');?>:</label></th>
					<td>
						<table>
							<tr>
								<td><label for="color-picker">Background Color:</label></td>
								<td><input type="text" id="color-picker" class="color-picker" data-alpha="true" data-default-color="rgba(0,0,0,0.85)" name="inboundrocket_wb_options[ir_wb_color]" value="<?=$ir_wb_color;?>" /></td>
							</tr>
							<tr>
								<td><label for="color-picker2">Text Color:</label></td>
								<td><input type="text" id="color-picker2" class="color-picker2" data-alpha="true" data-default-color="rgba(0,0,0,0.85)" name="inboundrocket_wb_options[ir_wb_text_color]" value="<?=$ir_wb_text_color;?>" /></td>
							</tr>
							<tr>
								<td><label for="color-picker3">Button Background Color:</label></td>
								<td><input type="text" id="color-picker3" class="color-picker3" data-alpha="true" data-default-color="rgba(0,0,0,0.85)" name="inboundrocket_wb_options[ir_wb_button_color]" value="<?=$ir_wb_button_color;?>" /></td>
							</tr>
							<tr>
								<td><label for="color-picker4">Button Text Color:</label></td>
								<td><input type="text" id="color-picker4" class="color-picker4" data-alpha="true" data-default-color="rgba(0,0,0,0.85)" name="inboundrocket_wb_options[ir_wb_button_text_color]" value="<?=$ir_wb_button_text_color;?>" /></td>
							</tr>
						</table>
					</td>
				</tr>
				<script>
					(function ($) {
					  $(function () {
						$('.color-picker').wpColorPicker();
						$('.color-picker2').wpColorPicker();
						$('.color-picker3').wpColorPicker();
						$('.color-picker4').wpColorPicker();
					  });
					}(jQuery));
				</script>
<?php
	}

}
?>