<?php if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');
global $post;
$post_status = get_post_status ( $post->ID );
$rule_options = array(
	'' => __( "Select a condition", 'inbound-rocket' ),
	'everywhere' 	=> __( 'everywhere', 'inbound-rocket' ),
	'is_page' 		=> __( 'if page is', 'inbound-rocket' ),
	'is_page_not' 		=> __( 'if page is not', 'inbound-rocket' ),
	'is_post' 	=> __( 'if post is', 'inbound-rocket' ),
	'is_post_not' 	=> __( 'if post is not', 'inbound-rocket' ),
	'is_post_in_category' => __( 'if post is in category', 'inbound-rocket' ),
	'is_post_not_in_category' => __( 'if post is not in category', 'inbound-rocket' ),
	'is_post_type' 	=> __( 'if post type is', 'inbound-rocket' ),	
	'is_post_type_not' 	=> __( 'if post type isn’t', 'inbound-rocket' ),	
);

$irwm_display_mode = array(
	'' => __( "Select a display mode", 'inbound-rocket' ),
	'normal' 	=> __( 'Normal', 'inbound-rocket' ),
	'embedded' 	=> __( 'Embedded', 'inbound-rocket' ),
);

$irwm_form_submit_type = array(
	'' => __( 'Don\'t pass form fields', 'inbound-rocket' ),
	'get' => __( 'Pass form fields as a GET variable', 'inbound-rocket' ),
	'post' => __( 'Pass form fields as a POST variable', 'inbound-rocket' )
);

$irwm_cookie_time = array(
	'always-show' => __( 'Always Show', 'inbound-rocket' ),
	'minutes' => __( 'Minutes', 'inbound-rocket' ),
	'hours' => __( 'Hours', 'inbound-rocket' ),
	'days' => __( 'Days', 'inbound-rocket' ),
	'months' => __( 'Months', 'inbound-rocket' ),
	'years' => __( 'Years', 'inbound-rocket' ),
);
?>

<div class="form-table">
	<div class="frm-row">
		<div class="lbl-row"><label><?php _e( 'Enable test mode?', 'inbound-rocket' ); ?></label></div>
		<div>
			<label><input type="radio" name="ir-wm[test_mode]" value="1" <?php checked( empty($option['test_mode']) ? false : esc_attr($option['test_mode']), 1 ); ?> /> <?php _e( 'Yes', 'inbound-rocket' ); ?></label>
			<label><input type="radio" name="ir-wm[test_mode]" value="0" <?php checked( empty($option['test_mode']) ? 0 : esc_attr($option['test_mode']), 0 ); ?> /> <?php _e( 'No', 'inbound-rocket' ); ?></label>
			<p class="help"><?php _e( 'If test mode is enabled, this Welcome Mat will show up regardless of whether a cookie has been set.', 'inbound-rocket' ); ?></p>
		</div>
	</div>
	
	<div class="frm-row">
		<div class="lbl-row"><label for="ir-wm-cookie"><?php _e( 'Frequency', 'inbound-rocket' ); ?></label></div>
		<div class="cookie-expiration-section">
			<input type="number" id="ir-wm-cookie" name="ir-wm[cookie_exp]" min="0" step="1" value="<?php echo empty($option['cookie_exp']) ? false : esc_attr($option['cookie_exp']); ?>" <?php if( $option['cookie_exp_time'] == 'always-show' ) { echo 'disabled="disabled"'; } ?>/>

			<select name="ir-wm[cookie_exp_time]" onchange="cookie_time(this);">
			<?php foreach( $irwm_cookie_time as $irwm_cookie_value => $irwm_cookie_option ) { ?>					
	    		<option value="<?=esc_attr($irwm_cookie_value)?>" <?php echo selected( empty($option['cookie_exp_time']) ? false : esc_attr($option['cookie_exp_time']), esc_attr($irwm_cookie_value) )?>><?=esc_attr($irwm_cookie_option)?></option>
	    	<?php } ?>
			</select>
		</div>
		<p class="help"><?php _e( 'Do NOT show welcome mat to the same visitor again until this much time has passed.', 'inbound-rocket' ); ?></p>
		
	</div>
 
	<div class="frm-row">
		<div class="lbl-row"><label><?php _e( 'Do not show if opted out', 'inbound-rocket' ); ?></label></div>
		<div>
			<label class="switch">
			  <input type="checkbox" name="ir-wm[toggle]" value="1" <?php checked( empty($option['toggle']) ? false : esc_attr($option['toggle']), 1 ); ?> />
			  <div class="slider round"></div>
			</label>
			<p class="help"><?php _e( '(Toggle "ON" and this mat WON\'T show to someone who has already opted out of this app.)', 'inbound-rocket' ); ?></p>
		</div>
	</div>

	<div class="frm-row">
		<div class="lbl-row">
			<label><?php _e( 'Enable instant landing page', 'inbound-rocket' ); ?></label>
		</div>
		<div>
			<label class="switch">
			  <input type="checkbox" name="ir-wm[landing_toggle]" value="1" <?php checked( empty($option['landing_toggle']) ? false : esc_attr($option['landing_toggle']), 1 ); ?> /> <?php //_e( 'Yes', 'inbound-rocket' ); ?>
			  <div class="slider round"></div>
			</label>
			<p class="help"><?php _e( 'Removes visitors ability to scroll down. (Increases conversion 2x!)', 'inbound-rocket' ); ?></p>
		</div>
	</div>


	<div class="frm-row">
		<div class="lbl-row"><label><?php _e( 'Display Mode', 'inbound-rocket' ); ?></label></div>
		<div>
			<select id="ir-wm-display-mode" name="ir-wm[css][display_mode]" class="widefat">
			<?php foreach( $irwm_display_mode as $value => $position ) { ?>					
		    	<option value="<?=esc_attr($value)?>" <?php echo selected( empty($option['css']['display_mode']) ? false : esc_attr($option['css']['display_mode']), $value )?>><?=esc_attr($position)?></option>
		    <?php } ?>
		  	</select>
			<p class="help"><?php _e( 'Set how the mat is displayed on the page', 'inbound-rocket' ); ?></p>
		</div>
	</div>
  
	<div class="frm-row">
		<div class="lbl-row"><label><?php _e( 'Do not auto-show box on small screens?', 'inbound-rocket' ); ?></label></div>
		<div>
			<small><?php printf( __( 'Do not auto-show on screens smaller than %s.', 'inbound-rocket' ), '<input type="number" min="0" name="ir-wm[hide_on_screen_size]" value="' . empty($option['hide_on_screen_size']) ? '' : esc_attr($option['hide_on_screen_size']) . '" style="max-width: 60px;" />px' ); ?></small>
			<p class="help"><?php _e( 'Set to <code>0</code> if you <strong>do</strong> want to auto-show the box on small screens.', 'inbound-rocket' ); ?></p>
		</div>
	</div>
   
   	<div class="frm-row">
   		<div>
   			<div class="lbl-row"><label><?php _e( 'Custom Rules', 'inbound-rocket' ); ?></label></div>
   			<div>
   				<?php _e( 'Request matches', 'inbound-rocket' ); ?>
   				<select name="ir-wm[rule_matches]">
   					<option value="any" <?php selected( empty($option['rule_matches']) ? false : esc_attr($option['rule_matches']), 'any' ); ?>><?php _e( 'any', 'inbound-rocket' ); ?></option>
   					<option value="all" <?php selected( empty($option['rule_matches']) ? false : esc_attr($option['rule_matches']), 'all' ); ?>><?php _e( 'all', 'inbound-rocket' ); ?></option>
        		</select>
				<?php _e( 'of the following conditions.', 'inbound-rocket' ); ?>
      		</div>
		</div>
   	</div>
	
	<div id="rule-row" class="frm-row">
		<div class="ir-wm-rule-row"><?php
		$key = 0;
		if(!empty($option['rules'])):
		foreach( $option['rules'] as $rule ) {
			if( ! array_key_exists( 'condition', $rule ) ) { continue; }
			if(empty($rule['condition'])) { continue; }
			if(empty($rule['value'])) $rule['value'] = '';
			?><div class="irwm-rule-row">
					<div class="ir-wm-col-sm">
						<span class="ir-wm-close ir-wm-remove-rule"><span class="dashicons dashicons-dismiss"></span></span>
						<select id="ir-wm-rule-condition" class="widefat ir-wm-rule-condition" name="ir-wm[rules][<?php echo $key; ?>][condition]" onchange="wmSetContextualHelpers(this, this.value)"><?php 
							foreach( $rule_options as $value => $label ) { 
								?><option value="<?php echo esc_attr($value)?>" <?php selected( esc_attr($rule['condition']), esc_attr($value) ); ?>><?php echo esc_attr($label)?></option><?php
							}
						?></select><?php
						$strClassRule = '';
						if($post_status == 'publish') {
							$strClassRule = 'hide';
							?><input class="widefat ir-wm-rule-value <?php if( !in_array( $rule['condition'], array( '', 'everywhere' ) ) ) { echo 'ir-wm-helper'; } ?>" type="text" name="ir-wm[rules][<?php echo $key; ?>][value]" value="<?php echo esc_attr( $rule['value'] ); ?>" placeholder="<?php _e( 'Leave empty for any or enter (comma-separated) names or ID\'s', 'inbound-rocket' ); ?>" style="<?php if( in_array( $rule['condition'], array( '', 'everywhere' ) ) ) { echo 'display: none;'; } ?>" /><?php
						}
						else {
						?><input class="widefat ir-wm-rule-value <?php echo $strClassRule; ?>" name="ir-wm[rules][<?php echo $key; ?>][value]" type="text" value="<?php echo esc_attr( $rule['value'] ); ?>" placeholder="<?php _e( 'Leave empty for any or enter (comma-separated) names or ID\'s', 'inbound-rocket' ); ?>" style="<?php if( in_array( $rule['condition'], array( '', 'everywhere' ) ) ) { echo 'display: none;'; } ?>" /><?php
						}
					?></div>
				</div><?php 
			$key++; 
		}
		endif;
		?></div>
		<div id="irwm-rule-row-add" class="irwm-rule-row hide">
				<div class="ir-wm-col-sm">
					<span class="ir-wm-close ir-wm-remove-rule"><span class="dashicons dashicons-dismiss"></span></span>
					<select id="ir-wm-rule-condition" class="widefat ir-wm-rule-condition" name="ir-wm[rules][<?php echo $key; ?>][condition]" onchange="wmSetContextualHelpers(this, this.value)"><?php 
						foreach( $rule_options as $value => $label ) { 
							?><option value="<?=esc_attr($value)?>"><?=esc_attr($label)?></option><?php
						}
					?></select>
					<input class="widefat ir-wm-rule-value <?php echo $strClassRule; ?>" name="ir-wm[rules][<?php echo $key; ?>][value]" type="text" value="" placeholder="<?php _e( 'Leave empty for any or enter (comma-separated) names or ID\'s', 'inbound-rocket' ); ?>" style="<?php if( in_array( esc_attr($rule['condition']), array( '', 'everywhere' ) ) ) { echo 'display: none;'; } ?>" />
				</div>
			</div>
	</div>
	
	<div class="frm-row">
		<div>
			<div></div>
			<div><button type="button" class="button ir-wm-add-rule"><?php _e( 'Add rule', 'inbound-rocket' ); ?></button></div>
		</div>
	</div>	

	<div class="frm-row">
		<div>
			<div class="lbl-row"><label><?php _e( 'After Success Redirect URL', 'inbound-rocket' ); ?></label></div>
			<small><?php _e( 'After a successful subscribe, redirect the user to the specified URL', 'inbound-rocket' ); ?></small></br>
			<small><?php _e( '(Your URL must contain "http://" or "https://". Example: http://www.yourwebsite.com/thankyou)', 'inbound-rocket' ); ?></small>
		</div>
		<div>
		  	<input type="text" class="widefat" name="ir-wm[redirect_url]" value="<?php echo empty($option['redirect_url']) ? false : esc_url($option['redirect_url']); ?>" placeholder="Subscribe Redirect URL">
		</div>
	</div>

	<div class="frm-row">
		<div>
			<div class="lbl-row"><label><?php _e( 'Pass forms field to Success Redirect URL', 'inbound-rocket' ); ?></label></div>
			<small><?php _e( 'Need to do something  with the form  fields on the Success page? Pass it to the Success Redirect URL.', 'inbound-rocket' ); ?></small>
		</div>
		<div>
		  	<select name="ir-wm[form_variable]" onchange="form_val(this);">
		  		<?php foreach( $irwm_form_submit_type as $irwm_form_submit_value => $irwm_form_submit_option ) { ?>					
		    		<option value="<?=esc_attr($irwm_form_submit_value)?>" <?php echo selected( empty($option['form_variable']) ? false : esc_attr($option['form_variable']), esc_attr($irwm_form_submit_value) )?>><?=esc_attr($irwm_form_submit_option)?></option>
		    <?php } ?>
		  	</select>
		</div>
	</div>


	<div id="wm_post_variable" class="hide <?php if( empty( $option['form_variable'] ) ) echo 'hide'; ?>">
		<div>
			<label><?php _e( 'POST/GET Variable Name', 'inbound-rocket' ); ?></label></br>
			<small><?php _e( 'Need to do something  with the form  fields on the Success page? Pass it to the Success Redirect URL.', 'inbound-rocket' ); ?></small>
		</div>
	</div>
</div>