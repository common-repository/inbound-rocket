<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

//=============================================
// WPInboundRocketAdmin Class
//=============================================
class WPSelectionSharerAdmin extends WPInboundRocketAdmin {
    
    var $power_up_settings_section = 'inboundrocket_ss_options';
    var $options;
    
    private static $_instance = null;
    
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
            $this->options = get_option('inboundrocket_ss_options');
            
            add_action('admin_enqueue_scripts', array($this,'ir_ss_enqueue_script'));
        }
    }
    
    public static function init(){
        if(self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;            
    }
    
    /**
     * Enqueue Power-Up Scripts
     */
    function ir_ss_enqueue_script()
	{
		if (INBOUNDROCKET_ENABLE_DEBUG==true) { 
			wp_register_script('inboundrocket-ir-admin-js', INBOUNDROCKET_SELECTION_SHARER_PATH . '/admin/js/selection-sharer-admin.js', array ( 'wp-color-picker' ), FALSE, TRUE);
		} else {
			wp_register_script('inboundrocket-ir-admin-js', INBOUNDROCKET_SELECTION_SHARER_PATH . '/admin/js/selection-sharer-admin.min.js', array ( 'wp-color-picker' ), FALSE, TRUE);
		}
		wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('inboundrocket-ir-admin-js');
	}

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    function sanitize ( $input )
    {
        $new_input = array();

		if( isset( $input['ir_ss_twitter_username'] ) )
            $new_input['ir_ss_twitter_username'] = sanitize_text_field( str_replace('@','',$input['ir_ss_twitter_username']) );
			
        if( isset( $input['ir_ss_tweet_suffix'] ) )
            $new_input['ir_ss_tweet_suffix'] = sanitize_text_field( $input['ir_ss_tweet_suffix'] );

        if( isset( $input['ir_ss_drop_suffix'] ) )
            $new_input['ir_ss_drop_suffix'] = intval( $input['ir_ss_drop_suffix'] );
        
		if( isset( $input['ir_ss_bg_color'] ) )
            $new_input['ir_ss_bg_color'] = sanitize_text_field( $input['ir_ss_bg_color'] );
		
        if( isset( $input['ir_ss_short_url_service'] ) )
            $new_input['ir_ss_short_url_service'] = sanitize_text_field( $input['ir_ss_short_url_service'] );

        if( isset( $input['ir_ss_campaign_variables'] ) )
            $new_input['ir_ss_campaign_variables'] = intval( $input['ir_ss_campaign_variables'] );

        if( isset( $input['ir_ss_awesm_api_key'] ) )
            $new_input['ir_ss_awesm_api_key'] = isset($input['ir_ss_awesm_api_key']) ? sanitize_text_field( $input['ir_ss_awesm_api_key'] ) : '';
            
        if( isset( $input['ir_ss_bitly_login'] ) )
            $new_input['ir_ss_bitly_login'] = isset($input['ir_ss_bitly_login']) ? sanitize_text_field( $input['ir_ss_bitly_login'] ) : '';

        if( isset( $input['ir_ss_bitly_api_key'] ) )
            $new_input['ir_ss_bitly_api_key'] = isset($input['ir_ss_bitly_api_key']) ? sanitize_text_field( $input['ir_ss_bitly_api_key'] ) : '';
            
        if( isset( $input['ir_ss_enable_frontend'] ) )
            $new_input['ir_ss_enable_frontend'] = intval( $input['ir_ss_enable_frontend'] );
		
		if( isset( $input['ir_ss_email_subject'] ) )
            $new_input['ir_ss_email_subject'] = sanitize_text_field( $input['ir_ss_email_subject'] );
            
        if( isset( $input['ir_ss_fbappid'] ) )
            $new_input['ir_ss_fbappid'] = intval( $input['ir_ss_fbappid'] );    
		
		if( isset( $input['ir_ss_enable_fb'] ) )
            $new_input['ir_ss_enable_fb'] = intval( $input['ir_ss_enable_fb'] );

		if( isset( $input['ir_ss_enable_li'] ) )
            $new_input['ir_ss_enable_li'] = intval( $input['ir_ss_enable_li'] );
		
		if( isset( $input['ir_ss_enable_tw'] ) )
            $new_input['ir_ss_enable_tw'] = intval( $input['ir_ss_enable_tw'] );

		if( isset( $input['ir_ss_enable_em'] ) )
            $new_input['ir_ss_enable_em'] = intval( $input['ir_ss_enable_em'] ); 
            
        return $new_input;
    }

	/**
     * Prints input form for settings page
     */	
	function ir_ss_input_fields () {
		
		$options = get_option('inboundrocket_ss_options');
		
		$ir_ss_twitter_username = isset($options['ir_ss_twitter_username']) ? esc_attr( $options['ir_ss_twitter_username'] ) : '';
		$ir_ss_bg_color = isset($options['ir_ss_bg_color']) ? esc_attr( $options['ir_ss_bg_color'] ) : '';
		$ir_ss_tweet_suffix = isset($options['ir_ss_tweet_suffix']) ? esc_attr( $options['ir_ss_tweet_suffix'] ) : '';
		$ir_ss_awesm_api_key = isset($options['ir_ss_awesm_api_key']) ? esc_attr( $options['ir_ss_awesm_api_key'] ) : '';
		$ir_ss_awesm_tool = isset($options['ir_ss_awesm_tool']) ? esc_attr( $options['ir_ss_awesm_tool'] ) : '';
		$ir_ss_awesm_channel = isset($options['ir_ss_awesm_channel']) ? esc_attr( $options['ir_ss_awesm_channel'] ) : '';
		$ir_ss_bitly_login = isset($options['ir_ss_bitly_login']) ? esc_attr( $options['ir_ss_bitly_login'] ) : '';
		$ir_ss_bitly_api_key = isset($options['ir_ss_bitly_api_key']) ? esc_attr( $options['ir_ss_bitly_api_key'] ) : '';
		$ir_ss_enable_frontend = isset($options['ir_ss_enable_frontend']) ? intval( $options['ir_ss_enable_frontend'] ) : '0';
		$ir_ss_fbappid = isset($options['ir_ss_fbappid']) ? esc_attr( $options['ir_ss_fbappid'] ) : '';
		
		$ir_ss_email_subject = isset($options['ir_ss_email_subject']) ? esc_attr( $options['ir_ss_email_subject'] ) : '';
		
		$ir_ss_enable_fb = isset($options['ir_ss_enable_fb']) ? esc_attr( $options['ir_ss_enable_fb'] ) : '1';
		$ir_ss_enable_li = isset($options['ir_ss_enable_li']) ? esc_attr( $options['ir_ss_enable_li'] ) : '1';
		$ir_ss_enable_tw = isset($options['ir_ss_enable_tw']) ? esc_attr( $options['ir_ss_enable_tw'] ) : '1';
		$ir_ss_enable_em = isset($options['ir_ss_enable_em']) ? esc_attr( $options['ir_ss_enable_em'] ) : '1';
		
		$ir_ss_campaign_variables = isset($options['ir_ss_campaign_variables']) ? '1' : '0';
		$ir_ss_drop_suffix = isset($options['ir_ss_drop_suffix']) ? '1' : '0';
		$ir_ss_short_url_service = isset($options['ir_ss_short_url_service']) ? esc_attr( $options['ir_ss_short_url_service'] ) : 'googl';
		?>
	<tr>
		<th colspan="2"><? _e('Hide or Show the Selection shares on the frontend.','inbound-rocket');?></th>
	</tr>	
	<tr>
		<th><label for="ir_ss_enable_frontend"><?php _e('Hide/Show shared text','inbound-rocket');?>:</label></th>
		<td><input type="checkbox" name="inboundrocket_ss_options[ir_ss_enable_frontend]" id="ir_ss_enable_frontend" value="1" <?php checked('1', $ir_ss_enable_frontend, true); ?> /> <span class="description"><?php _e('If you prefer not to show the previously shared text uncheck this box.','inbound-rocket');?></span></td>
	</tr>
	<tr>
		<th><label for="ir_wb_color"><?php _e('Background Color','inbound-rocket');?>:</label></th>
		<td> <input type="text" data-default-color="#effeff" name="inboundrocket_ss_options[ir_ss_bg_color]" class="ss-color-picker" value="<?=$ir_ss_bg_color;?>" /></td>
	</tr>
	<tr>
		<th><label for="ir_ss_api_service_awesm"><?php _e('Short URL service','inbound-rocket');?>:</label></th>
		<td><input type="radio" name="inboundrocket_ss_options[ir_ss_short_url_service]" id="ir_ss_api_service_awesm" value="awesm" <?php checked('awesm', $ir_ss_short_url_service); ?> /> <label for="ir_ss_api_service_awesm">Awe.sm</label> &nbsp;&nbsp;&nbsp;
			<input type="radio" name="inboundrocket_ss_options[ir_ss_short_url_service]" id="ir_ss_service_bitly" value="bitly" <?php checked('bitly', $ir_ss_short_url_service); ?> /> <label for="ir_ss_service_bitly">Bit.ly</label></td>
	</tr>
	<tr class="awesm">
		<th><h4>Awe.sm <?php _e('credentials','inbound-rocket');?></h4></th>
	</tr>
	<tr class="awesm">
		<th><label for="ir_ss_awesm_api_key">Awe.sm <?php _e('API key','inbound-rocket');?>:</label></th>
		<td><input type="text" name="inboundrocket_ss_options[ir_ss_awesm_api_key]" id="ir_ss_awesm_api_key" value="<?=$ir_ss_awesm_api_key;?>" class="regular-text" /></td>
	</tr>
	<tr class="awesm">
		<th><label for="ir_ss_awesm_tool">Awe.sm <?php _e('Tool key','inbound-rocket');?>:</label></th>
		<td><input type="text" name="inboundrocket_ss_options[ir_ss_awesm_tool]" id="ir_ss_awesm_tool" value="<?=$ir_ss_awesm_tool;?>" class="regular-text" /></td>
	</tr>
	<tr class="awesm">
		<th><label for="ir_ss_awesm_channel">Awe.sm <?php _e('Channel','inbound-rocket');?></label></th>
		<td><input type="text" name="inboundrocket_ss_options[ir_ss_awesm_channel]" id="ir_ss_awesm_channel" value="<?=$ir_ss_awesm_channel;?>" class="regular-text" /></td>
	</tr>
	<tr class="bitly">
		<th><h4>Bit.ly <?php _e('credentials','inbound-rocket');?></h4></th>
	</tr>
	<tr class="bitly">
		<th><label for="ir_ss_bitly_login">Bit.ly <?php _e('Login name','inbound-rocket');?>:</label></th>
		<td><input type="text" name="inboundrocket_ss_options[ir_ss_bitly_login]" id="ir_ss_bitly_login" value="<?=$ir_ss_bitly_login;?>" class="regular-text" /></td>
	</tr>			
	<tr class="bitly">
		<th><label for="ir_ss_bitly_login">Bit.ly <?php _e('API key:','inbound-rocket');?></label></th>
		<td><input type="text" name="inboundrocket_ss_options[ir_ss_bitly_api_key]" id="ir_ss_bitly_api_key" value="<?=$ir_ss_bitly_api_key;?>" class="regular-text"></td>
	</tr>
	<?php if(isset($ir_ss_short_url_service)): 
		switch($ir_ss_short_url_service) {
		case "bitly":
			echo "<script>jQuery('.awesm').hide();</script>";
		break;
		case "awesm":
			echo "<script>jQuery('.bitly').hide();</script>";
		break;
		default:
			echo "<script>jQuery('.bitly,.awesm').hide();</script>";
		}
		endif;
	?>
	<tr>
		<th><label for="ir_ss_campaign_variables"><?php _e('Campaign variables','inbound-rocket');?>:</label></th>
		<td><input type="checkbox" name="inboundrocket_ss_options[ir_ss_campaign_variables]" id="ir_ss_campaign_variables" value="1" <?php checked($ir_ss_campaign_variables,'1', true); ?> /> <span class="description"><?php _e('Add Google Analytics campaign variables to the permalink for tracking.','inbound-rocket');?></span></td>
	</tr>
	<tr>
		<th colspan="2"><h3><? _e('Email Settings','inbound-rocket');?></h3></th>
	</tr>
	<tr>
		<th><label for"ir_ss_enable_em"><?php _e('Enable Email Share','inbound-rocket');?>:</label></th>
		<td><input type="checkbox" name="inboundrocket_ss_options[ir_ss_enable_em]" id="ir_ss_enable_em" value="1" <?php checked('1', $ir_ss_enable_em, true); ?> /></td>
	</tr>
	<tr>
		<th><label for"ir_ss_email_subject"><?php _e('Email Subject','inbound-rocket');?>:</label></th>
		<td><input type="text" name="inboundrocket_ss_options[ir_ss_email_subject]" id="ir_ss_email_subject" style="width:375px !important;" value="<?=$ir_ss_email_subject;?>" /></td>
	</tr>
	<tr>
		<td colspan="2"><i><? _e('Note: The email subject has a few available shortcodes: [selection] to show the selected text or [title] to show the current page title.','inbound-rocket');?></i></td>
	</tr>
	<tr>
		<th colspan="2"><h3><? _e('LinkedIn Settings','inbound-rocket');?></h3></th>
	</tr>
	<tr>
		<th><label for"ir_ss_enable_li"><?php _e('Enable LinkedIn Share','inbound-rocket');?>:</label></th>
		<td><input type="checkbox" name="inboundrocket_ss_options[ir_ss_enable_li]" id="ir_ss_enable_li" value="1" <?php checked('1', $ir_ss_enable_li, true); ?> /></td>
	</tr>
	<tr>
		<th colspan="2"><h3><? _e('Facebook Settings','inbound-rocket');?></h3></th>
	</tr>
	<tr>
		<th><label for"ir_ss_enable_fb"><?php _e('Enable Facebook Share','inbound-rocket');?>:</label></th>
		<td><input type="checkbox" name="inboundrocket_ss_options[ir_ss_enable_fb]" id="ir_ss_enable_fb" value="1" <?php checked('1', $ir_ss_enable_fb, true); ?> /></td>
	</tr>
	<tr>
		<td colspan="2"><i><? _e('Note: for Facebook Sharing to be able to work you will need to have a Facebook App ID. Inbound Rocket can automatically include your fb:app_id meta tag information in your theme if you enter it below. To get or create your Facebook App ID click','inbound-rocket');?><a href="https://developers.facebook.com/" target="_blank"> <? _e('here','inbound-rocket');?></a></i></td>
	</tr>
	<tr>
		<th><label for"ir_ss_fbappid"><?php _e('Facebook App ID','inbound-rocket');?>:</label></th>
		<td><input type="text" name="inboundrocket_ss_options[ir_ss_fbappid]" id="ir_ss_fbappid" value="<?=$ir_ss_fbappid;?>" /><br /><span class="description"><?php _e('Enter your Facebook App ID to add Facebook sharing.','inbound-rocket');?></span></td>
	</tr>
	<tr>
		<th colspan="2"><h3><? _e('Twitter Settings','inbound-rocket');?></h3></th>
	</tr>
	<tr>
		<th><label for"ir_ss_enable_tw"><?php _e('Enable Twitter Share','inbound-rocket');?>:</label></th>
		<td><input type="checkbox" name="inboundrocket_ss_options[ir_ss_enable_tw]" id="ir_ss_enable_tw" value="1" <?php checked('1', $ir_ss_enable_tw, true); ?> /></td>
	</tr>
	<tr>
		<th><label for="ir_ss_twitter_username"><?php _e('Twitter username','inbound-rocket');?>:</label></th>
		<td><input type="text" name="inboundrocket_ss_options[ir_ss_twitter_username]" id="ir_ss_twitter_username" value="<?=$ir_ss_twitter_username;?>" /><br /><span class="description"><?php _e('Enter your Twitter handle to add "via @yourhandle" to your tweets. Do not include the @ symbol.','inbound-rocket');?></span></td>
	</tr>
	<tr>
		<th><label for="ir_ss_tweet_suffix"><?php _e('Tweet suffix','inbound-rocket');?>:</label></th>
		<td><input type="text" name="inboundrocket_ss_options[ir_ss_tweet_suffix]" id="ir_ss_tweet_suffix" value="<?=$ir_ss_tweet_suffix;?>" />
		<br /><span class="description"><?php _e('You can suffix your automated tweets with extra hashtags, like','inbound-rocket');?> <code><?php _e('#hashtag','inbound-rocket');?></code></span></td>
	</tr>
	<tr>
		<th><label for="ir_ss_drop_suffix"><?php _e('Drop suffix','inbound-rocket');?>:</label></th>
		<td><input type="checkbox" name="inboundrocket_ss_options[ir_ss_drop_suffix]" id="ir_ss_drop_suffix" value="1" <?php checked('1', $ir_ss_drop_suffix, true); ?> /> <span class="description"><?php _e('Drop the suffix if the tweet exceeds 140 characters.','inbound-rocket');?></span></td>
	</tr>
	<script>
		(function ($) {
		  $(function () {
			$('.color-picker').wpColorPicker();
		  });
		}(jQuery));
	</script>
	<?php
	}	

}
?>