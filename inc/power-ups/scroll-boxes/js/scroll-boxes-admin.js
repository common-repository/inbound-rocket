jQuery( document ).ready(function($) {
	jQuery( '.ir-sb-color-field' ).wpColorPicker();
	
	displayAdditionalOptions();
	jQuery(".ir-sb-auto-show").change(function () {
		displayAdditionalOptions();       
    });
	
	jQuery('.ir-sb-helper').blur(function(){
      var selConditions = jQuery(this.val());
      //alert(selConditions);
      jQuery(this).next('.ir-sb-rule-value').val(selConditions);
    });

    // Add new rule section....
    jQuery('.ir-sb-add-rule').click(function(){
      var $template = jQuery('#ir-sb-rule-row-add');

      $clone = $template
        .clone()
        .removeClass('hide')
        .removeAttr('id');

      jQuery('.rule-row').append($clone);
    });

    // Remove new rule section...
    jQuery('.ir-sb-remove-rule').click(function(){
      jQuery(this).closest('.ir-sb-rule-row').remove();
    });

    jQuery('.ir-sb-helper').keyup(function(){
       var condition = jQuery(this).closest('.ir-sb-rule-row').find('.ir-sb-rule-condition').val();
       var betterInput = jQuery(this);
       var $betterInput = jQuery(betterInput);
       // change placeholder for textual help
      switch(condition) {
        default:
          betterInput.placeholder = 'Enter a comma-separated list of values.';
          break;

        case '':
        case 'everywhere':
          //valueInput.value = '';
          betterInput.style.display = 'none';
          break;

        case 'is_single':
        case 'is_post':
        case 'is_post_not':
          betterInput.placeholder = 'Enter a comma-separated list of post slugs or post ID\'s.';
          $betterInput.suggest(ajaxurl + "?action=ir_sb_autocomplete&type=post&nonce="+sbOptions.nonce, {multiple:true, multipleSep: ","});
          break;

        case 'is_page':
        case 'is_page_not':
          betterInput.placeholder = 'Enter a comma-separated list of page slugs or page ID\'s.';
          $betterInput.suggest(ajaxurl + "?action=ir_sb_autocomplete&type=page&nonce="+sbOptions.nonce, {multiple:true, multipleSep: ","});
          break;

        case 'is_post_type':
        case 'is_post_type_not':
          betterInput.placeholder = 'Enter a comma-separated list of post types.';
          $betterInput.suggest(ajaxurl + "?action=ir_sb_autocomplete&type=post_type&nonce="+sbOptions.nonce, {multiple:true, multipleSep: ","});
          break;

        case 'is_url':
          betterInput.placeholder = 'Enter a comma-separated list of relative URL\'s, eg /contact/';
          break;

        case 'is_post_in_category':
        case 'is_post_not_in_category':
          $betterInput.suggest(ajaxurl + "?action=ir_sb_autocomplete&type=category&nonce="+sbOptions.nonce, {multiple:true, multipleSep: ","});
          break;

        case 'manual':
          betterInput.placeholder = '';
          manualHintElement.style.display = '';
          break;
      }
    });
	jQuery('.ir-sb-rule-condition').on('change', sbSetContextualHelpers);
});

function sbSetContextualHelpers() {
	var context = jQuery('.ir-sb-rule-condition').parents('tr').get(0);
	var condition = jQuery('.ir-sb-rule-condition').val();
	var valueInput = context.querySelector('input.ir-sb-rule-value');
	var betterInput = valueInput.cloneNode(true);
	var $betterInput = jQuery(betterInput);
	
	

	// remove previously added helpers
	jQuery(context.querySelectorAll('.ir-sb-helper')).remove();

	// prepare better input
	betterInput.removeAttribute('name');
	betterInput.className += ' ir-sb-helper';
	valueInput.parentNode.insertBefore(betterInput, valueInput.nextSibling);
	betterInput.style.display = 'block';
	$betterInput.change(function() {
		valueInput.value = this.value; //.val(this.value);
	});

	valueInput.style.display = 'none';

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
			betterInput.placeholder = 'Enter a comma-separated list of post slugs or post ID\'s.';
			$betterInput.suggest(ajaxurl + "?action=ir_sb_autocomplete&type=post&nonce="+sbOptions.nonce, {multiple:true, multipleSep: ","});
			break;

		case 'is_page':
			betterInput.placeholder = 'Enter a comma-separated list of page slugs or page ID\'s.';
			$betterInput.suggest(ajaxurl + "?action=ir_sb_autocomplete&type=page&nonce="+sbOptions.nonce, {multiple:true, multipleSep: ","});
			break;

		case 'is_post_type':
			betterInput.placeholder = 'Enter a comma-separated list of post types.';
			$betterInput.suggest(ajaxurl + "?action=ir_sb_autocomplete&type=post_type&nonce="+sbOptions.nonce, {multiple:true, multipleSep: ","});
			break;

		case 'is_url':
			betterInput.placeholder = 'Enter a comma-separated list of relative URL\'s, eg /contact/';
			break;

		case 'is_post_in_category':
			$betterInput.suggest(ajaxurl + "?action=ir_sb_autocomplete&type=category&nonce="+sbOptions.nonce, {multiple:true, multipleSep: ","});
			break;

		case 'manual':
			betterInput.placeholder = '';
			manualHintElement.style.display = '';
			break;
	}		
}

jQuery(document).keydown(function(e) {
    if (e.keyCode == 27) {
        jQuery('.add_options_popup').hide();
    }
  });

function closepopup(hide_val) {
	jQuery('.add_options_popup').hide();
}

function displayAdditionalOptions(){
	var radio_val = jQuery(".ir-sb-auto-show:checked").val();
	if (radio_val == "") {
		jQuery('#ir-sb-auto-show-options').css('display', 'none');
		jQuery('#ir-sb-custom-rules-options').css('display', 'table-row-group');
		jQuery('#ir-sb-box-rules').css('display', 'table-row-group');
		jQuery('#ir-sb-box-rules-button').css('display', 'table-row-group');
	} else if(radio_val == 'percentage' || radio_val == 'element' || radio_val == 'instant'){	
		jQuery('#ir-sb-auto-show-options').css('display', 'table-row-group');
		jQuery('#ir-sb-custom-rules-options').css('display', 'table-row-group');
		jQuery('#ir-sb-box-rules').css('display', 'table-row-group');
		jQuery('#ir-sb-box-rules-button').css('display', 'table-row-group');
	} else {
		jQuery('#ir-sb-auto-show-options').css('display', 'table-row-group');
		jQuery('#ir-sb-custom-rules-options').css('display', 'none');
		jQuery('#ir-sb-box-rules').css('display', 'none');
		jQuery('#ir-sb-box-rules-button').css('display', 'none');
	}
}

jQuery(window).load(function() {
	/*if( typeof( window.tinyMCE ) !== "object" || tinyMCE.get('content') === null ) {
		document.getElementById('notice-notinymce').style.display = 'block';
	}*/
	loadPreview();
	//showPreview();
});

function loadPreview(){
	
	var element =  document.getElementById('post_ID');
	if (typeof(element) === 'undefined' || element == null)
	{
		return;
	}
	
	var boxId = element.value || 0;

	// Only run if TinyMCE has actually inited
	if( typeof( window.tinyMCE ) !== "object" || tinyMCE.get('content') == null ) {
		return;
	}
	
	$editorFrame = jQuery("#content_ifr");
	$editor = $editorFrame.contents().find('html');
	$editor.css({
		'background': 'white'
	});
	
	$innerEditor = $editor.find('#tinymce');
	$innerEditor.addClass( 'ir-sb ir-sb-' + boxId );
		
	$innerEditor.get(0).style.cssText += ';padding: 25px !important;';	
	
	var editorContent = tinyMCE.get('content').getContent();
	if( editorContent !== ''){
	  var bg 	  = jQuery( "#ir-sb-background-color" ).val();
	  var color   = jQuery( "#ir-sb-text-color" ).val();
	  var width   = jQuery( "#ir-sb-width" ).val();
	  var bColor  = jQuery( "#ir-sb-border-color" ).val();
	  var bWidth  = jQuery( "#ir-sb-border-width" ).val();
	  var bStyle  = jQuery( "#ir-sb-border-style" ).val();
	  
	  $innerEditor.css({
		'margin': 0,
		'color': color, 
		'background-color': bg,
		'display': 'inline-block',
		'width': width,
		'position': 'relative',
		'border':  bWidth +'px '+ bStyle +' '+ bColor,
	  });
	  
	} else {
	
	  $innerEditor.css({
		'margin': 0,
		'color': 'black',
		'background': 'white',
		'display': 'inline-block',
		'width': 'auto',
		'min-width': '140px',
		'position': 'relative'
	  });
	}
	
	jQuery( "#ir-sb-background-color" ).on( "blur", function() {
		var bg = jQuery( this ).val();
		$innerEditor.css({ 'background-color': bg});
	});
	
	jQuery( "#ir-sb-text-color" ).on( "blur", function() {
		var color = jQuery( this ).val();
		$innerEditor.css({'color': color});
	});
	
	jQuery( "#ir-sb-border-color" ).on( "blur", function() {
		var bColor = jQuery( this ).val();
		$innerEditor.css({ 'border-color': bColor});
	});
	
	jQuery( "#ir-sb-width" ).on( "blur", function() {
		var width = jQuery( this ).val();
		$innerEditor.css({'width': width+'px'});
	});
	
	jQuery( "#ir-sb-border-width" ).on( "blur", function() {
		var bWidth = jQuery( this ).val();
		$innerEditor.css({'border-width': bWidth+'px'});
	});
	
	jQuery( "#ir-sb-border-style" ).on( "blur", function() {
		var bStyle = jQuery( this ).val();
		$innerEditor.css({'border-style': bStyle});
	});
	
	// create <style> element in <head>
	manualStyleEl = document.createElement('style');
	manualStyleEl.setAttribute('type','text/css');
	manualStyleEl.id = 'ir-sb-manual-css-style';
	
	var text = jQuery('#ir-sb-manual-css').val();	
	var manualStyle = document.createTextNode( text ); 
  	manualStyleEl.appendChild( manualStyle );	
	jQuery(manualStyleEl).appendTo($editor.find('head'));
}