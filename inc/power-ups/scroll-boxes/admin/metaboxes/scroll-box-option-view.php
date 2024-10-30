<?php if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');
global $post;
$post_status = get_post_status ( $post->ID );
$rule_options = array(
	'' => __( "Select a condition", 'inbound-rocket' ),
	'everywhere' 	=> __( 'everywhere', 'inbound-rocket' ),
	'is_page' 		=> __( 'if page is', 'inbound-rocket' ),
	'is_not_page' 		=> __( 'if page is not', 'inbound-rocket' ),
	'is_post' 	=> __( 'if post is', 'inbound-rocket' ),
	'is_not_post' 	=> __( 'if post is not', 'inbound-rocket' ),
	'is_post_in_category' => __( 'if post is in category', 'inbound-rocket' ),
	'is_not_post_in_category' => __( 'if post is not in category', 'inbound-rocket' ),
	'is_post_type' 	=> __( 'if post type is', 'inbound-rocket' ),
	'is_not_post_type' 	=> __( 'if post type is not', 'inbound-rocket' ),
);

$irsb_box_positions = array(
	'' => __( "Select box position", 'inbound-rocket' ),
	'top-left' 		=> __( 'Top Left', 'inbound-rocket' ),
	'top-right' 	=> __( 'Top Right', 'inbound-rocket' ),
	'middle' 		=> __( 'Middle', 'inbound-rocket' ),
	'bottom-left' 	=> __( 'Bottom Left', 'inbound-rocket' ),
	'bottom-right' 	=> __( 'Bottom Right', 'inbound-rocket' ),	
);

?>
<table class="form-table">	
	<tr valign="top">
		<th><label><?php _e( 'Box Position', 'inbound-rocket' ); ?></label></th>
		<td>
			<select id="ir-sb-position" name="ir-sb[css][position]" class="widefat">
			<?php foreach( $irsb_box_positions as $value => $position ) { ?>					
		    	<option value="<?=esc_attr($value)?>" <?php echo selected( empty($option['css']['position']) ? false : esc_attr($option['css']['position']), esc_attr($value) )?>><?=esc_attr($position) ?></option>
		    <?php } ?>
		  	</select>
		</td>
		<td colspan="2"></td>
	</tr>
    
	<tr valign="top">
		<th><label><?php _e( 'Animation', 'inbound-rocket' ); ?></label></th>
		<td colspan="3">
			<label><input type="radio" name="ir-sb[animation]" value="fade" <?php checked( empty($option['animation']) ? false : esc_attr($option['animation']), 'fade' ); ?> /> <?php _e( 'Fade In', 'inbound-rocket' ); ?></label> &nbsp;
			<label><input type="radio" name="ir-sb[animation]" value="slide" <?php checked( empty($option['animation']) ? false : esc_attr($option['animation']), 'slide' ); ?> /> <?php _e( 'Slide In', 'inbound-rocket' ); ?></label>
			<p class="help"><?php _e( 'Which animation type should be used to show the box?', 'inbound-rocket' ); ?></p>
		</td>
	</tr>

	<tr valign="top">
		<th><label><?php _e( 'Enable test mode?', 'inbound-rocket' ); ?></label></th>
		<td colspan="3">
			<label><input type="radio" name="ir-sb[test_mode]" value="1" <?php checked( empty($option['test_mode']) ? false : intval($option['test_mode']), 1 ); ?> /> <?php _e( 'Yes', 'inbound-rocket' ); ?></label> &nbsp;
			<label><input type="radio" name="ir-sb[test_mode]" value="0" <?php checked( empty($option['test_mode']) ? 0 : intval($option['test_mode']), 0 ); ?> /> <?php _e( 'No', 'inbound-rocket' ); ?></label> &nbsp;
			<p class="help"><?php _e( 'If test mode is enabled, this box will show up regardless of whether a cookie has been set.', 'inbound-rocket' ); ?></p>
		</td>
	</tr>
	
	<tr valign="top">
		<th><label for="ir-sb-cookie"><?php _e( 'Cookie expiration', 'inbound-rocket' ); ?></label></th>
		<td colspan="3">
			<input type="number" id="ir-sb-cookie" name="ir-sb[cookie_exp]" min="0" step="1" value="<?php echo empty($option['cookie_exp']) ? false : intval($option['cookie_exp']); ?>" /> days
			<p class="help"><?php _e( 'After closing the box, how many days should it stay hidden?', 'inbound-rocket' ); ?></p>
		</td>
	</tr>
	
	<tr valign="top">
		<th><label><?php _e( 'Show Box', 'inbound-rocket' ); ?></label></th>
		<td colspan="3">
	    	<label><input type="radio" class="ir-sb-auto-show" name="ir-sb[auto_show]" value="" <?php checked( empty($option['auto_show']) ? false : esc_attr($option['auto_show']), '' ); ?> /> <?php _e( 'Matching custom rules', 'inbound-rocket' ); ?></label><br />
			<label><input type="radio" class="ir-sb-auto-show" name="ir-sb[auto_show]" value="instant" <?php checked( empty($option['auto_show']) ? false : esc_attr($option['auto_show']), 'instant' ); ?> /> <?php _e( 'Immediately after loading the page.', 'inbound-rocket' ); ?></label><br />
			<label><input type="radio" class="ir-sb-auto-show" name="ir-sb[auto_show]" value="percentage" <?php checked( empty($option['auto_show']) ? false : esc_attr($option['auto_show']), 'percentage' ); ?> /> <?php printf( __( 'When at %s of page height', 'inbound-rocket' ), '<input type="number" name="ir-sb[auto_show_percentage]" min="0" max="100" value="' . empty($option['auto_show_percentage']) ? '' : esc_attr($option['auto_show_percentage']) . '" />%' ); ?></label><br />
			<label><input type="radio" class="ir-sb-auto-show" name="ir-sb[auto_show]" value="element" <?php checked( empty($option['auto_show']) ? false : esc_attr($option['auto_show']), 'element' ); ?> /> <?php printf( __( 'When at element %s', 'inbound-rocket' ), '<input type="text" name="ir-sb[auto_show_element]" value="' . empty($option['auto_show_element']) ? '' : esc_attr( $option['auto_show_element'] ) . '" placeholder="' . __( 'Example: #comments', 'inbound-rocket') .'" />' ); ?></label>
		</td>
	</tr>
    
    <tbody id="ir-sb-auto-show-options" style="display:table-row-group;">
		<tr valign="top">
			<th><label><?php _e( 'Auto-hide?', 'inbound-rocket' ); ?></label></th>
			<td colspan="2">
				<label><input type="radio" name="ir-sb[auto_hide]" value="1" <?php checked( empty($option['auto_hide']) ? false : intval($option['auto_hide']), 1 ); ?> /> <?php _e( 'Yes', 'inbound-rocket' ); ?></label> &nbsp;
				<label><input type="radio" name="ir-sb[auto_hide]" value="0" <?php checked( empty($option['auto_hide']) ? false : intval($option['auto_hide']), 0 ); ?> /> <?php _e( 'No', 'inbound-rocket' ); ?></label> &nbsp;
				<p class="help"><?php _e( 'Hide box again when visitors scroll back up?', 'inbound-rocket' ); ?></p>
    		</td>
  		</tr>
  
  		<tr valign="top">
  			<th><label><?php _e( 'Do not auto-show box on small screens?', 'inbound-rocket' ); ?></label></th>
  			<td colspan="2">
  				<p><?php printf( __( 'Do not auto-show on screens smaller than %s.', 'inbound-rocket' ), '<input type="number" min="0" name="ir-sb[hide_on_screen_size]" value="' . esc_attr( $option['hide_on_screen_size'] ) . '" style="max-width: 50px;" />px' ); ?></p>
  				<p class="help"><?php _e( 'Set to <code>0</code> if you <strong>do</strong> want to auto-show the box on small screens.', 'inbound-rocket' ); ?></p>
      		</td>
  		</tr>
   	</tbody>
   
   	<tbody id="ir-sb-custom-rules-options" style="display:table-row-group;">
   		<tr valign="top">
   			<th><label><?php _e( 'Custom Rules', 'inbound-rocket' ); ?></label></th>
   			<td colspan="3">
   				<?php _e( 'Request matches', 'inbound-rocket' ); ?>
   				<select name="ir-sb[rule_matches]">
   					<option value="any" <?php selected( empty($option['rule_matches']) ? false : esc_attr($option['rule_matches']), 'any' ); ?>><?php _e( 'any', 'inbound-rocket' ); ?></option>
   					<option value="all" <?php selected( empty($option['rule_matches']) ? false : esc_attr($option['rule_matches']), 'all' ); ?>><?php _e( 'all', 'inbound-rocket' ); ?></option>
        		</select>
				<?php _e( 'of the following conditions.', 'inbound-rocket' ); ?>
      		</td>
		</tr>
   	</tbody>
	
	<tbody class="rule-row" id="ir-sb-box-rules" style="display:table-row-group;"><?php
		$key = 0;
		if(!empty($option['rules'])):
		foreach( $option['rules'] as $rule ) {
			if( ! array_key_exists( 'condition', $rule ) ) { continue; }
			if(empty($rule['condition'])) { continue; }
			if(empty($rule['value'])) $rule['value'] = '';
			?><tr valign="top" class="ir-sb-rule-row ir-sb-rule-row-<?php echo $key; ?>">
			<th style="text-align: right; font-weight: normal;">
				<span class="ir-sb-close ir-sb-remove-rule"><span class="dashicons dashicons-dismiss"></span></span>
			</th>
			<td class="ir-sb-col-sm">
			<select class="widefat ir-sb-rule-condition" name="ir-sb[rules][<?php echo $key; ?>][condition]"><?php 
							foreach( $rule_options as $value => $label ) { 
								?><option value="<?php echo esc_attr($value)?>" <?php selected( esc_attr($rule['condition']), esc_attr($value) ); ?>><?php echo esc_attr($label)?></option><?php
							}
						?></select></td>
			<td colspan="2">
			<input class="widefat ir-sb-rule-value" name="ir-sb[rules][<?php echo intval($key); ?>][value]" type="text" value="<?php echo esc_attr( $rule['value'] ); ?>" placeholder="<?php _e( 'Leave empty for any or enter (comma-separated) names or ID\'s', 'inbound-rocket' ); ?>" style="<?php if( in_array( esc_attr($rule['condition']), array( '', 'everywhere' ) ) ) { echo 'display: none;'; } ?>" /></td>
			</tr><?php 
			$key++; 
		}
		endif;
		?>
		<tr id="ir-sb-rule-row-add" valign="top" class="ir-sb-rule-row hide">
			<th style="text-align: right; font-weight: normal;">
				<span class="ir-sb-close ir-sb-remove-rule"><span class="dashicons dashicons-dismiss"></span></span>
			</th>
			<td class="ir-sb-col-sm"><select class="widefat ir-sb-rule-condition" name="ir-sb[rules][<?php echo $key; ?>][condition]"><?php 
				foreach( $rule_options as $value => $label ) { 
					?><option value="<?=esc_attr($value)?>"><?=esc_attr($label)?></option><?php
				}
			?></select></td>
			<td colspan="2"><input class="widefat ir-sb-rule-value <?php echo $strClassRule; ?>" name="ir-sb[rules][<?php echo $key; ?>][value]" type="text" value="" placeholder="<?php _e( 'Leave empty for any or enter (comma-separated) names or ID\'s', 'inbound-rocket' ); ?>" style="<?php if( in_array( esc_attr($rule['condition']), array( '', 'everywhere' ) ) ) { echo 'display: none;'; } ?>" />
			</td>
		</tr>
	</tbody>
		
	<tbody id="ir-sb-box-rules-button" style="display:table-row-group;">
		<tr>
			<th></th>
			<td colspan="3"><button type="button" class="button ir-sb-add-rule"><?php _e( 'Add rule', 'inbound-rocket' ); ?></button></td>
		</tr>
	</tbody>	
</table>