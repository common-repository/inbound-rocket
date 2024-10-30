jQuery(document).ready( function ( $ ) {
	
	$("#filter_action").select2();

	$('#filter_action').change( function ( e ) {
		var $this = $(this);

		if ( $this.val() == ir_admin_ajax.submitted )
		{
			$('#form-filter-input').show();
		}
		else
		{
			$('#form-filter-input').hide();
			$('#filter_form').select2("val", ir_admin_ajax.anyform);
		}
	});

	$('#view-all-do-not-track-selectors').click( function ( e ) {
		$('.form-do-not-track, #view-less-do-not-track-selectors').not('.do-not-track-always-show').show();
		$('#view-all-do-not-track-selectors').hide();
	});

	$('#view-less-do-not-track-selectors').click( function ( e ) {
		$('.form-do-not-track, #view-less-do-not-track-selectors').not('.do-not-track-always-show').hide();
		$('#view-all-do-not-track-selectors').show();
	});

	$('#filter_form').select2({
		debug: true,
        placeholder: "Select a form",
        allowClear: true,
        ajax: {
            url: ir_admin_ajax.ajax_url,
            dataType: 'json',
            type: 'POST',
            data: function (params) {
                return {
					ir_nonce: ir_admin_ajax.ir_nonce,
					action: 'inboundrocket_get_form_selectors', 
					search_term: params.term
                }
            },
            processResults: function (data, params) {
	         	//console.log(data);
	         	return {
			 		results: data
			 	}
	        }
        },
        formatResult: FormatResult,
        formatSelection: FormatSelection,
        escapeMarkup: function (m) { return m; }
	});
	
	$('#filter_content').select2({
		debug: true,
        placeholder: "Select a post or page",
        allowClear: true,
        ajax: {
            url: ir_admin_ajax.ajax_url,
            dataType: 'json',
            type: 'POST',
            data: function (params) {
                return {
					ir_nonce: ir_admin_ajax.ir_nonce,
					action: 'inboundrocket_get_posts_and_pages', 
					search_term: params.term
                }
            },
            processResults: function (data, params) {
	         	//console.log(data);
	         	return {
			 		results: data
			 	}
	        }
        },
        formatResult: FormatResult,
        formatSelection: FormatSelection,
        escapeMarkup: function (m) { return m; }
	});
	
	function FormatResult(item){
		var markup = "";
        if (item.text !== undefined) {
            markup += "<option value='" + item.id + "'>" + item.text + "</option>";
        }
        return markup;
	}
	
	function FormatSelection(item) {
        return item.text;
    }
	
	$('#inboundrocket-contact-status').change( function ( e ) {
		$('#inboundrocket-contact-status-button').addClass('button-primary');
	});

	$('input[name=popup-position]:radio').change( function ( e ) {
		$('#btn-activate-subscribe').attr('href', window.location.href + '&inboundrocket_action=activate&power_up=selection_sharer&redirect_to=' + encodeURIComponent(window.location.href + '&activate_popup=true&popup_position=' + $("input:radio[name='popup-position']:checked").val()));
	});

	$('#ir_subscribe_vex_class, #ir_subscribe_heading, #ir_subscribe_text, #ir_subscribe_btn_label, #ir_subscribe_name_fields:checkbox, #ir_subscribe_phone_field:checkbox').change( function ( e ) {
		var preview_link = $('#wp-admin-bar-view-site a.ab-item').attr('href') + '?preview-subscribe=1';

		preview_link += '&lis_heading=' + $('#ir_subscribe_heading').val();
		preview_link += '&lis_desc=' + $('#ir_subscribe_text').val();
		preview_link += '&lis_show_names=' + ( $('#ir_subscribe_name_fields').is(':checked') ? 1 : 0 );
		preview_link += '&lis_show_phone=' + ( $('#ir_subscribe_phone_field').is(':checked') ? 1 : 0 );
		preview_link += '&lis_btn_label=' + $('#ir_subscribe_btn_label').val();
		preview_link += '&lis_vex_class=' + $('#ir_subscribe_vex_class option:selected').val();

		$('#preview-popup-link').attr('href', preview_link);
	});
	
});

jQuery(document).ready( function ( $ ) {
	
	var $bulk_opt_selected = $('.bulkactions select option[value="delete_selected"]');

	$('#inboundrocket-contacts input:checkbox').not('thead input:checkbox, tfoot input:checkbox').bind('change', function ( e  ){
		var cb_count = 0;
		var selected_vals = '';
		var $btn_selected = $('#inboundrocket-export-selected-leads');
		
		var $input_selected_vals = $('.inboundrocket-selected-contacts');
		var $cb_selected = $('#inboundrocket-contacts input:checkbox:checked').not('thead input:checkbox, tfoot input:checkbox');

		if ( $cb_selected.length > 0 )
		{
			$btn_selected.attr('disabled', false);
			$bulk_opt_selected.attr('disabled', false);

		}
		else
		{
			$btn_selected.attr('disabled', true);
			$bulk_opt_selected.attr('disabled', true);
		}

		$cb_selected.each( function ( e ) {
			selected_vals += $(this).val();
			
			if ( cb_count != ($cb_selected.length-1) )
				selected_vals += ',';

			cb_count++;
		});

		$input_selected_vals.val(selected_vals);
		$('.selected-contacts-count').text(cb_count);
	});

	$('#cb-select-all-1, #cb-select-all-2').bind('change', function ( e ) { 
		var cb_count = 0;
		var selected_vals = '';
		var $this = $(this);
		var $btn_selected = $('#inboundrocket-export-selected-leads');
		var $cb_selected = $('#inboundrocket-contacts input:checkbox').not('thead input:checkbox, tfoot input:checkbox');
		var $input_selected_vals = $('.inboundrocket-selected-contacts');

		$cb_selected.each( function ( e ) {
			selected_vals += $(this).val();
			
			if ( cb_count != ($cb_selected.length-1) )
				selected_vals += ',';

			cb_count++;
		});

		$input_selected_vals.val(selected_vals);

		if ( !$this.is(':checked') )
		{
			$btn_selected.attr('disabled', true);
			$bulk_opt_selected.attr('disabled', true);
			$('.selected-contacts-count').text($('#contact-count').text());
		}
		else
		{
			$btn_selected.attr('disabled', false);
			$bulk_opt_selected.attr('disabled', false);
		}

		$('.selected-contacts-count').text(cb_count);


	});

	$('.postbox .handlediv').bind('click', function ( e  ) {
		var $postbox = $(this).parent();
		
		if ( $postbox.hasClass('closed') )
		{
			$postbox.removeClass('closed');
		}
		else
		{
			$postbox.addClass('closed');
		}
	});

	$('.selected-contacts-count').text($('#contact-count').text());
	$bulk_opt_selected.attr('disabled', true);

	$('.bulkactions select').change(function ( e ) {
		var $this = $(this);
		var $contact_count = $('#contact-count').text();

		if ( $this.val() == 'add_list_to_all' || $this.val() == 'add_list_to_selected' )
		{
			$('#bulk-edit-lists h2').html($('#bulk-edit-lists h2').html().replace(ir_admin_ajax.removefrom, ir_admin_ajax.addto));
			$('#bulk-edit-button').val(ir_admin_ajax.addlist);
			$('#bulk-edit-tag-action').val('add_list');
			tb_show("", "#TB_inline?width=400&height=175&inlineId=bulk-edit-lists");
			
			// Reset the bulk actions so if the user closes the box it resets the values and nulls the apply button
			$('.bulkactions select').val('-1');
		}
		else if ( $this.val() == 'remove_list_from_all' || $this.val() == 'remove_list_from_selected' )
		{
			$('#bulk-edit-lists h2').html($('#bulk-edit-lists h2').html().replace(ir_admin_ajax.addto, ir_admin_ajax.removefrom));
			$('#bulk-edit-button').val('Remove List');
			$('#bulk-edit-tag-action').val('remove_list');
			tb_show("", "#TB_inline?width=400&height=175&inlineId=bulk-edit-lists");

			// Reset the bulk actions so if the user closes the box it resets the values and nulls the apply button
			$('.bulkactions select').val('-1');
		}
	});

	$('#contact-detail-read-more').click( function ( e ) {
		$('#company-detail-overview-short').hide();
		$('#company-detail-overview-full').show();
	});

	$('#contact-detail-read-less').click( function ( e ) {
		$('#company-detail-overview-full').hide();
		$('#company-detail-overview-short').show();
	});
});

jQuery(document).ready( function ( $ ) {
	$('#filter_action, #filter_content, #filter_form').change(function() {
		$('#inboundrocket-contacts-filter-button').addClass('button-primary');
	});
});