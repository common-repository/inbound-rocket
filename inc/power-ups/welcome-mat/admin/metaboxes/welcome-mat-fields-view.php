<?php if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

global $post;
$post_status = get_post_status ( $post->ID );
$arrDefaultFieldStatus = array('auto-draft', 'draft');

$arrGroupFields = array('dropdown_list', 'checkbox_group', 'radio_group');
$irwm_field_types = array(
	'' => __( "Select a field type", 'inbound-rocket' ),
	'email_address' 	=> __( 'Email Address', 'inbound-rocket' ),
	'name' 	=> __( 'Name', 'inbound-rocket' ),
	'first_name' 	=> __( 'First name', 'inbound-rocket' ),
	'last_name' 	=> __( 'Last name', 'inbound-rocket' ),
	'company' 	=> __( 'Company', 'inbound-rocket' ),
	'zip_code' 	=> __( 'Zip code', 'inbound-rocket' ),
	'country_dropdown' 	=> __( 'Country dropdown', 'inbound-rocket' ),
	'website_url' 	=> __( 'Website URL', 'inbound-rocket' ),
	'phone_number' 	=> __( 'Phone number', 'inbound-rocket' ),
	'single_line_input_text' 	=> __( 'Single-line text input', 'inbound-rocket' ),
	'dropdown_list' 	=> __( 'Dropdown list', 'inbound-rocket' ),
	'checkbox_input' 	=> __( 'Checkbox input', 'inbound-rocket' ),
	'checkbox_group' 	=> __( 'Checkbox group', 'inbound-rocket' ),
	'radio_group' 	=> __( 'Radio group', 'inbound-rocket' ),
	'hidden_value' 	=> __( 'Hidden value', 'inbound-rocket' ),
);

$total_fields = 0;
if(!empty($option['css']['total_fields']))
	$total_fields = $option['css']['total_fields'];
?><table class="form-table">
	<tr valign="top">
		<th><label><?php _e( 'Field Type', 'inbound-rocket' ); ?></label></th>
		<th><label><?php _e( 'Field Name', 'inbound-rocket' ); ?></label></th>
		<th><label><?php _e( 'Required', 'inbound-rocket' ); ?></label></th>
		<th><label><?php _e( 'Variable Name', 'inbound-rocket' ); ?></label></th>
	</tr><?php
	// Display email address field by default in "Add" case only....
	if(in_array($post_status, $arrDefaultFieldStatus) && $total_fields <= 1) {
		?><tr valign="top" class="form-fields">
			<td>
				<select id="ir-wm-field-type" name="ir-wm[css][field_type][]" class="widefat" onChange="addSubOptions(this, this.value);">
			    	<option value="email_address"><?php _e( 'Email Address', 'inbound-rocket' ); ?></option>
			  	</select>
			  	<input type="hidden" id="ir-wm-field-type" name="ir-wm[css][field_type][]" value="email_address" />
			</td>
			<td>
				<input type="text" id="ir-wm-field-name" name="ir-wm[css][field_name][]" value="<?php _e( 'Your email address', 'inbound-rocket' ); ?>" />
			</td>
			<td>
				<label class="switch">
					<input type="checkbox" name="ir-wm[css][field_required_display][]" value="0"  class="field_required_display"  />
					<div class="slider round"></div>
				</label>
				<input type="hidden" name="ir-wm[css][field_required][]" value="0"  checked="checked" class="field_required" />
				<input type="hidden" id="row_id" name="row_id[]" value="0"  />
			</td>
			<td>
				<input type="text" id="ir-wm-field-variable-name" name="ir-wm[css][field_variable_name][]" value="email" />
			</td>
			<td>
				<a href="javascript:;" onclick="this.closest('.form-fields').remove();" class="remove_field dashicons dashicons-trash">&nbsp;</a>
			</td>
		</tr><?php 
		$total_fields++;
	}
	if($total_fields > 0) {
		for ($i = 0; $i <= $total_fields; $i++) {
			if(!empty($option['css']['field_type'][$i])) {
				$row_id = $i; //($i + 1);
				if(! isset( $option['css']['field_required'][$i] )) {
					$option['css']['field_required'][$i] = 0;
				}
				?><tr valign="top" class="form-fields">
					<td>
						<select id="ir-wm-field-type[<?php echo $i; ?>]" name="ir-wm[css][field_type][]" class="widefat" onChange="addSubOptions(this, this.value);">
						<?php foreach( $irwm_field_types as $value => $position ) { ?>					
					    	<option value="<?php echo $value; ?>" <?php echo selected( $option['css']['field_type'][$i], $value )?>><?=$position?></option>
					    <?php } ?>
					  	</select>
					</td>
					<td>
						<input type="text" id="ir-wm-field-name[<?php echo $i; ?>]" name="ir-wm[css][field_name][]" value="<?php echo esc_attr($option['css']['field_name'][$i]); ?>" />
					</td>
					<td>
						<label class="switch">
							<input type="checkbox" name="ir-wm[css][field_required_display][]" value="1" <?php checked( $option['css']['field_required'][$i], 1 ); ?> class="field_required_display" />
						  	<div class="slider round"></div>
						</label>
						<input type="hidden" name="ir-wm[css][field_required][]" class="field_required" value="<?php echo $option['css']['field_required'][$i]; ?>" />
						<input type="hidden" id="row_id" name="row_id[]" value="<?php echo $row_id; ?>"  />
					</td>
					<td>
						<input type="text" id="ir-wm-field-variable-name" name="ir-wm[css][field_variable_name][]" value="<?php echo esc_attr($option['css']['field_variable_name'][$i]); ?>" /><?php
							
							$field_group_fields_css = 'hide';
							if( in_array($option['css']['field_type'][$i], $arrGroupFields)) {
								$field_group_fields_css = '';
							}
								#print('<pre>' . $row_id);
								#print_r($option['opt_popup']['field_name'][$row_id]);

								if(isset($option['opt_popup']['field_name'][$row_id]) || 1) {
									if(isset($option['opt_popup']['field_name'][$row_id])) {
										$iCnt = 1;
										$arrCustomOptionNames = array();
										foreach($option['opt_popup']['field_name'][$row_id] as $index => $arrInfo) {
											$arrCustomOptionNames[$row_id][$iCnt++] = $arrInfo;
										}

										$iCnt = 1;
										$arrCustomOptionValues = array();
										foreach($option['opt_popup']['field_value'][$row_id] as $index => $arrInfo) {
											$arrCustomOptionValues[$row_id][$iCnt++] = $arrInfo;
										}
									}
									?><a href="javascript:;" onclick="optPopup(this);" class="add_options <?php echo $field_group_fields_css; ?>"><?php _e( 'Add Options', 'inbound-rocket'); ?></a>
									<div class="add_options_popup hide">
										<div class="add_options_popup_overlay"></div>
										<div class="add_options_popup_body">
											<input type="button" id="opt_popup_add_field" name="opt_popup_add_field" value="<?php _e( 'Add', 'inbound-rocket' ); ?>" onclick="addOptSlot(this);">
											<input type="button" id="opt_popup_add_data" name="opt_popup_add_data" value="<?php _e( 'Save', 'inbound-rocket' ); ?>" onclick="saveOptData(this);">
											<div class="close-btn" onclick="closepopup(this);">X</div>
											<div class="optpopup_wrap">
												<table id="optpopuptsb" style="width: 460px; margin: 0 auto">
													<tr>
														<th><?php _e( 'Name', 'inbound-rocket'); ?></th>
														<th><?php _e( 'Value', 'inbound-rocket'); ?></th>
														<th>&nbsp;</th>
													</tr><?php
													$totalOpt = 0;
													if(!empty($arrCustomOptionNames[$row_id])) {
														$totalOpt = count($arrCustomOptionNames[$row_id]);
														if($totalOpt > 0) {
															for ($j=0; $j < $totalOpt; $j++) {
																if(!empty($arrCustomOptionNames[$row_id][$j])) {
																	?><tr valign="top" id="select_option_skeleton[<?php echo $j; ?>]" class="select_option_form_fields">
																		<td>
																			<input type="text" id="select_option_ir-wm-field-name" name="ir-wm[opt_popup][field_name][<?php echo $row_id; ?>][<?php echo $j; ?>]" value="<?php echo $arrCustomOptionNames[$row_id][$j]; ?>" />
																		</td>
																		<td>
																			<input type="text" id="select_option_ir-wm-field-variable-name" name="ir-wm[opt_popup][field_value][<?php echo $row_id; ?>][<?php echo $j; ?>]" value="<?php echo $arrCustomOptionValues[$row_id][$j]; ?>" /><br/>
																		</td>
																		<td>
																			<a href="javascript:;" onclick="this.closest('.select_option_form_fields').remove();" class="remove_field dashicons dashicons-trash">&nbsp;</a>
																		</td>
																	</tr><?php
																}
															}
														}
													}
													?><tr valign="top" id="select_option_skeleton" class="select_option_form_fields hide">
														<td>
															<input type="text" id="select_option_ir-wm-field-name" name="ir-wm[opt_popup][field_name][<?php echo $row_id; ?>][]" value="" />
														</td>
														<td>
															<input type="text" id="select_option_ir-wm-field-variable-name" name="ir-wm[opt_popup][field_value][<?php echo $row_id; ?>][]" value="" /><br/>
														</td>
														<td>
															<a href="javascript:;" onclick="this.closest('.select_option_form_fields').remove();" class="remove_field dashicons dashicons-trash">&nbsp;</a>
														</td>
													</tr><?php
												?></table>
											</div>
										</div>
									</div><?php 
								}
							
				
							$field_checkbox_input_css = 'hide';
							if(empty($option['css']['field_checkbox_input'][$i])) {
								$option['css']['field_checkbox_input'][$i] = '';
							}
							if($option['css']['field_type'][$i] == 'checkbox_input') {
								$field_checkbox_input_css = '';
							}
							?><input type="text" id="checkbox_input_ir-wm-field-name" name="ir-wm[css][field_checkbox_input][<?php echo $row_id; ?>]" value="<?php echo esc_attr($option['css']['field_checkbox_input'][$i]); ?>" class="add_checkbox_input <?php echo $field_checkbox_input_css; ?>" placeholder="Field Value (eg: 'yes')" /><?php
							
							if(empty($option['css']['field_hidden_value'][$i])) {
								$option['css']['field_hidden_value'][$i] = '';
							}
							$field_hidden_value_css = 'hide';
							if($option['css']['field_type'][$i] == 'hidden_value') {
								$field_hidden_value_css = '';
							}
							?><input type="text" id="hidden_value_ir-wm-field-name" name="ir-wm[css][field_hidden_value][<?php echo $row_id; ?>]" value="<?php echo esc_attr($option['css']['field_hidden_value'][$i]); ?>" class="add_hidden_value <?php echo $field_hidden_value_css; ?>"  placeholder="Field Value" /><?php
							
					?></td>
					<td>
						<a href="javascript:;" onclick="this.closest('.form-fields').remove();" class="remove_field dashicons dashicons-trash">&nbsp;</a>
					</td>
				</tr><?php
			}
		}
	}
	?>
	<tr valign="top" id="skeleton" class="form-fields hide">
		<td>
			<select id="ir-wm-field-type" name="ir-wm[css][field_type][]" class="widefat" onChange="addSubOptions(this, this.value);">
			<?php foreach( $irwm_field_types as $value => $position ) { ?>					
		    	<option value="<?=$value?>"><?=$position?></option>
		    <?php } ?>
		  	</select>
		</td>
		<td>
			<input type="text" id="ir-wm-field-name" name="ir-wm[css][field_name][]" value="" />
		</td>
		<td>
			<label class="switch">
				<input type="checkbox" name="ir-wm[css][field_required_display][]" value="0" class="field_required_display" />
			  	<div class="slider round"></div>
			</label>
			<input type="hidden" name="ir-wm[css][field_required][]" value="0" class="field_required" />
			<input type="hidden" id="row_id" name="row_id[]" value="0"  />
		</td>
		<td>
			<input type="text" id="ir-wm-field-variable-name" name="ir-wm[css][field_variable_name][]" value="" /><br/>
			<a href="javascript:;" onclick="optPopup(this);" class="add_options hide"><?php _e( 'Add Options', 'inbound-rocket'); ?></a>
			<div class="add_options_popup hide">
				<div class="add_options_popup_overlay"></div>
				<div class="add_options_popup_body">
					<input type="button" id="opt_popup_add_field" name="opt_popup_add_field" value="<?php _e( 'Add', 'inbound-rocket' ); ?>" onclick="addOptSlot(this);">
					<input type="button" id="opt_popup_add_data" name="opt_popup_add_data" value="<?php _e( 'Save', 'inbound-rocket' ); ?>" onclick="saveOptData(this);">
					<div class="close-btn" onclick="closepopup(this);">X</div>
					<div class="optpopup_wrap">
						<table id="optpopuptsb" style="width: 460px; margin: 0 auto">
							<tr>
								<th><?php _e( 'Name', 'inbound-rocket'); ?></th>
								<th><?php _e( 'Value', 'inbound-rocket'); ?></th>
								<th>&nbsp;</th>
							</tr>
							<tr valign="top" id="select_option_skeleton" class="select_option_form_fields hide">
								<td>
									<input type="text" id="select_option_ir-wm-field-name" name="ir-wm[opt_popup][field_name][][]" value="" />
								</td>
								<td>
									<input type="text" id="select_option_ir-wm-field-variable-name" name="ir-wm[opt_popup][field_value][][]" value="" /><br/>
								</td>
								<td>
									<a href="javascript:;" onclick="this.closest('.select_option_form_fields').remove();" class="remove_field dashicons dashicons-trash">&nbsp;</a>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<input type="text" id="checkbox_input_ir-wm-field-name" name="ir-wm[css][field_checkbox_input][]" placeholder="Field Value (eg: 'Yes')" class="add_checkbox_input hide" value="" />
			<input type="text" id="hidden_value_ir-wm-field-name" name="ir-wm[css][field_hidden_value][]" placeholder="Hidden Value (eg: 'Yes')" class="add_hidden_value hide" value="" />
		</td>
		<td>
			<a href="javascript:;" onclick="this.closest('.form-fields').remove();" class="remove_field dashicons dashicons-trash">&nbsp;</a>
		</td>
	</tr>
</table>
<input type="hidden" id="total_fields" name="ir-wm[css][total_fields]" value="<?php echo $total_fields; ?>"  />
<input type="button" id="btn_add_field" class="button" name="btn_add_field" value="<?php _e( 'Add Field', 'inbound-rocket' ); ?>" onclick="addslote();" />