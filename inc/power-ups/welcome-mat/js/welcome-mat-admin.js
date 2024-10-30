jQuery( document ).ready(function($) {
  jQuery('#ir_wm_upload_image_button').click(function() {
    formfield = jQuery('#ir_wm_background_image').attr('name');
    tb_show('', 'media-upload.php?type=image&TB_iframe=true');
    return false;
  });
  window.send_to_editor = function(html) {
    imgurl = jQuery(html).attr('src');
    jQuery('#ir_wm_background_image').val(imgurl);
    tb_remove();
  }
  jQuery(".ir-wm-color-field").wpColorPicker();

	wmdDisplayAdditionalOptions();
	jQuery(".ir-wm-auto-show").change(function () {
		wmdDisplayAdditionalOptions();       
    });
	
	var optionControls = document.getElementById('ir-welcome-mat-options-controls');
	var $optionControls = jQuery(optionControls);
	//$optionControls.on('change', "#ir-wm-rule-condition", wmSetContextualHelpers);

	// Custom font dropdown
    jQuery('.font-link').click(function(event) {
      event.stopPropagation();
      jQuery(this).next('.font-items').slideToggle();
    });
    jQuery('.font-items li').click(function(event) {
      event.stopPropagation();
      var font_value = jQuery(this).attr('data-value');
      var font_name = jQuery(this).text();
      var font_style = jQuery(this).attr('style');
      jQuery(this).closest('.font-list').find('.font-link').attr('data-value', font_value).attr('style', font_style).text(font_name);
      jQuery(this).closest('.font-list').find('.sel_font_name').val(font_value);
      jQuery(this).parent().slideToggle();
    });
    jQuery('body').click(function() {
      jQuery('.font-list .font-items').slideUp();
    });

    jQuery('.sel_font_name').each(function() {
    	var sel_font_data_attr = jQuery(this).val();

    	jQuery(this).parent('.font-list').find('.font-items li').each(function(){
    		if(jQuery(this).attr('data-value') == sel_font_data_attr) {
    			var font_value = jQuery(this).attr('data-value');
			    var font_name = jQuery(this).text();
			    var font_style = jQuery(this).attr('style');
      			jQuery(this).closest('.font-list').find('.font-link').attr('data-value', font_value).attr('style', font_style).text(font_name);
    		}
    	});
    });


    // Popup
    jQuery('.close-btn').click(function() {
    	//alert('hgdhj');
    	// jQuery('.add_options_popup_body').hide();
    });

    jQuery('.field_required_display').change(function() {
      if (jQuery(this).is(':checked')) {
        jQuery(this).closest('td').find('.field_required').val("1");
      }
      else {
        jQuery(this).closest('td').find('.field_required').val("0");
      }
    });

    jQuery('.ir-wm-helper').blur(function(){
      var selConditions = jQuery(this.val());
      //alert(selConditions);
      jQuery(this).next('.ir-wm-rule-value').val(selConditions);
    });

    // Add new rule section....
    jQuery('.ir-wm-add-rule').click(function(){
      var $template = jQuery('#ir-wm-rule-row-add');

      $clone = $template
              .clone()
              .removeClass('hide')
              .removeAttr('id');
              //.insertBefore($template);

      jQuery('#rule-row').append($clone);
    });

    // Remove new rule section...
    jQuery('.ir-wm-remove-rule').click(function(){
      jQuery(this).closest('.ir-wm-rule-row').remove();
    });

    jQuery('.ir-wm-helper').keyup(function(){
       var condition = jQuery(this).closest('.ir-wm-rule-row').find('.ir-wm-rule-condition').val();
       var betterInput = jQuery(this);
       var $betterInput = jQuery(betterInput);
       // change placeholder for textual help
      switch(condition) {
        default:
          betterInput.placeholder = 'Enter a comma-separated list of values.';
          break;

        case '':
        case 'everywhere':
          valueInput.value = '';
          betterInput.style.display = 'none';
          break;

        case 'is_single':
        case 'is_post':
        case 'is_post_not':
          betterInput.placeholder = 'Enter a comma-separated list of post slugs or post ID\'s.';
          $betterInput.suggest(ajaxurl + "?action=ir_wm_autocomplete&type=post", {multiple:true, multipleSep: ","});
          break;

        case 'is_page':
        case 'is_page_not':
          betterInput.placeholder = 'Enter a comma-separated list of page slugs or page ID\'s.';
          $betterInput.suggest(ajaxurl + "?action=ir_wm_autocomplete&type=page", {multiple:true, multipleSep: ","});
          break;

        case 'is_post_type':
        case 'is_post_type_not':
          betterInput.placeholder = 'Enter a comma-separated list of post types.';
          $betterInput.suggest(ajaxurl + "?action=ir_wm_autocomplete&type=post_type", {multiple:true, multipleSep: ","});
          break;

        case 'is_url':
          betterInput.placeholder = 'Enter a comma-separated list of relative URL\'s, eg /contact/';
          break;

        case 'is_post_in_category':
        case 'is_post_not_in_category':
          $betterInput.suggest(ajaxurl + "?action=ir_wm_autocomplete&type=category", {multiple:true, multipleSep: ","});
          break;

        case 'manual':
          betterInput.placeholder = '';
          manualHintElement.style.display = '';
          break;
      }
    });

});
jQuery(document).keydown(function(e) {
    if (e.keyCode == 27) {
        jQuery('.add_options_popup').hide();
    }
  });

function closepopup(hide_val) {
	jQuery('.add_options_popup').hide();
}

// Display textbox based on rule selection....
function wmSetContextualHelpers(objEle, condition) {
  if(condition == 'everywhere' || condition == '') {
    jQuery(objEle).parent(".ir-wm-col-sm").find("input.ir-wm-rule-value").hide();
  }
  else {
    jQuery(objEle).parent(".ir-wm-col-sm").find("input.ir-wm-rule-value").show().addClass("ir-wm-helper");
  }

  var betterInput = jQuery(objEle).parent(".ir-wm-col-sm").find("input.ir-wm-rule-value");
  var $betterInput = jQuery(betterInput);


  // change placeholder for textual help
  switch(condition) {
    default:
      betterInput.placeholder = 'Enter a comma-separated list of values.';
      break;

    case '':
    case 'everywhere':
      valueInput.value = '';
      betterInput.style.display = 'none';
      break;

    case 'is_single':
    case 'is_post':
    case 'is_post_not':
      betterInput.placeholder = 'Enter a comma-separated list of post slugs or post ID\'s.';
      $betterInput.suggest(ajaxurl + "?action=ir_wm_autocomplete&type=post", {multiple:true, multipleSep: ","});
      break;

    case 'is_page':
    case 'is_page_not':
      betterInput.placeholder = 'Enter a comma-separated list of page slugs or page ID\'s.';
      $betterInput.suggest(ajaxurl + "?action=ir_wm_autocomplete&type=page", {multiple:true, multipleSep: ","});
      break;

    case 'is_post_type':
    case 'is_post_type_not':
      betterInput.placeholder = 'Enter a comma-separated list of post types.';
      $betterInput.suggest(ajaxurl + "?action=ir_wm_autocomplete&type=post_type", {multiple:true, multipleSep: ","});
      break;

    case 'is_url':
      betterInput.placeholder = 'Enter a comma-separated list of relative URL\'s, eg /contact/';
      break;

    case 'is_post_in_category':
    case 'is_post_not_in_category':
      $betterInput.suggest(ajaxurl + "?action=ir_wm_autocomplete&type=category", {multiple:true, multipleSep: ","});
      break;

    case 'manual':
      betterInput.placeholder = '';
      manualHintElement.style.display = '';
      break;
  }
}

function addslote(){
	var total_fields = jQuery("#total_fields").val();
	total_fields++;
	jQuery('#skeleton').find("#row_id").val(total_fields);
	jQuery("#total_fields").val(total_fields);
    var $template = jQuery('#skeleton');
    $clone = $template
            .clone()
            .removeClass('hide')
            .removeAttr('id')
            .insertBefore($template);

  jQuery('.field_required_display').change(function() {
      if (jQuery(this).is(':checked')) {
        jQuery(this).closest('td').find('.field_required').val("1");
      }
      else {
        jQuery(this).closest('td').find('.field_required').val("0");
      }
  }); 
}

// Function for add text field inside popup
function addOptSlot(savePopOpt){
   var $template =  jQuery(savePopOpt).parent().parent('.add_options_popup').find('#select_option_skeleton'),
    $clone = $template
            .clone()
            .removeClass('hide')
            .removeAttr('id')
            .insertBefore($template);
      
}

// Show/hide "Add Option" link based on dropdown value selection
function addSubOptions(objSel, selVal) {
	if(selVal == 'dropdown_list' || selVal == 'checkbox_group' || selVal == 'radio_group'){
		jQuery(objSel).closest('.form-fields').find('a.add_options').removeClass('hide');
	}
	else {
		jQuery(objSel).closest('.form-fields').find('a.add_options').addClass('hide');
	}
  if(selVal == 'checkbox_input'){
    jQuery(objSel).closest('.form-fields').find('.add_checkbox_input').removeClass('hide');
  }
  else {
    jQuery(objSel).closest('.form-fields').find('.add_checkbox_input').addClass('hide');
  }
  if(selVal == 'hidden_value'){
    jQuery(objSel).closest('.form-fields').find('.add_hidden_value').removeClass('hide');
  }
  else {
    jQuery(objSel).closest('.form-fields').find('.add_hidden_value').addClass('hide');
  }

  var field_name = jQuery(objSel).find('option:selected').text();
  jQuery(objSel).closest('.form-fields').find('#ir-wm-field-name').attr( 'value', field_name );
  jQuery(objSel).closest('.form-fields').find('#ir-wm-field-variable-name').attr( 'value', selVal );
}

// Function to open optPopup
function optPopup(popOpt) {
	var objTr = jQuery(popOpt).closest('.form-fields');
	var objRowIdTxt = objTr.find("#row_id");
	var row_id = objRowIdTxt.attr('value');
	var objNameTxt = jQuery(popOpt).closest('.add_options_popup').find("#select_option_ir-wm-field-name");
	var objVariableNameTxt = jQuery(popOpt).next('.add_options_popup').find("#select_option_ir-wm-field-variable-name");
	objNameTxt.attr('name', 'ir-wm[opt_popup][field_name][' + row_id + '][]');
	objVariableNameTxt.attr('name', 'ir-wm[opt_popup][field_value][' + row_id + '][]');
	jQuery(popOpt).next('.add_options_popup').slideDown('1800');
}

function saveOptData(saveOpt) {
	jQuery(saveOpt).parent().parent().slideUp('1000');
}

function wmdDisplayAdditionalOptions(){
	var radio_val = jQuery(".ir-wm-auto-show:checked").val();
	if (radio_val == "") {
		jQuery('#ir-wm-auto-show-options').css('display', 'none');
		jQuery('#ir-wm-custom-rules-options').css('display', 'inline-block');
		jQuery('#ir-wm-box-rules').css('display', 'inline-block');
		jQuery('#ir-wm-box-rules-button').css('display', 'inline-block');
	} else if(radio_val == 'percentage' || radio_val == 'element' || radio_val == 'instant'){	
		jQuery('#ir-wm-auto-show-options').css('display', 'inline-block');
		jQuery('#ir-wm-custom-rules-options').css('display', 'inline-block');
		jQuery('#ir-wm-box-rules').css('display', 'inline-block');
		jQuery('#ir-wm-box-rules-button').css('display', 'inline-block');
	} else {
		jQuery('#ir-wm-auto-show-options').css('display', 'inline-block');
		jQuery('#ir-wm-custom-rules-options').css('display', 'none');
		jQuery('#ir-wm-box-rules').css('display', 'none');
		jQuery('#ir-wm-box-rules-button').css('display', 'none');
	}
}

function form_val( form_sel ) {
	if( jQuery( form_sel ).val() != '' ) {
		jQuery(' div#wm_post_variable ').addClass('hide');
		//jQuery(' div#wm_post_variable ').removeClass('hide');
	}
	else {
		jQuery(' div#wm_post_variable ').addClass('hide');
	}
}

function cookie_time( cookie_val ) {
	if( jQuery( cookie_val ).val() == 'always-show' ) {
		jQuery(' input#ir-wm-cookie ').attr('disabled', 'disabled');
	}
	else {
		jQuery(' input#ir-wm-cookie ').removeAttr('disabled');
	}
}
