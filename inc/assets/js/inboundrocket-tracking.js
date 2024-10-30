/*
 *  Inbound Rocket Tracking JS
 *  Last Updated: 9/16/16
 *
 */
'use strict';

/*!
 * JavaScript Cookie v2.1.3
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
;(function (factory) {
	var registeredInModuleLoader = false;
	if (typeof define === 'function' && define.amd) {
		define(factory);
		registeredInModuleLoader = true;
	}
	if (typeof exports === 'object') {
		module.exports = factory();
		registeredInModuleLoader = true;
	}
	if (!registeredInModuleLoader) {
		var OldCookies = window.Cookies;
		var api = window.Cookies = factory();
		api.noConflict = function () {
			window.Cookies = OldCookies;
			return api;
		};
	}
}(function () {
	function extend () {
		var i = 0;
		var result = {};
		for (; i < arguments.length; i++) {
			var attributes = arguments[ i ];
			for (var key in attributes) {
				result[key] = attributes[key];
			}
		}
		return result;
	}

	function init (converter) {
		function api (key, value, attributes) {
			var result;
			if (typeof document === 'undefined') {
				return;
			}

			// Write

			if (arguments.length > 1) {
				attributes = extend({
					path: '/'
				}, api.defaults, attributes);

				if (typeof attributes.expires === 'number') {
					var expires = new Date();
					expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
					attributes.expires = expires;
				}

				try {
					result = JSON.stringify(value);
					if (/^[\{\[]/.test(result)) {
						value = result;
					}
				} catch (e) {}

				if (!converter.write) {
					value = encodeURIComponent(String(value))
						.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
				} else {
					value = converter.write(value, key);
				}

				key = encodeURIComponent(String(key));
				key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
				key = key.replace(/[\(\)]/g, escape);

				return (document.cookie = [
					key, '=', value,
					attributes.expires ? '; expires=' + attributes.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
					attributes.path ? '; path=' + attributes.path : '',
					attributes.domain ? '; domain=' + attributes.domain : '',
					attributes.secure ? '; secure' : ''
				].join(''));
			}

			// Read

			if (!key) {
				result = {};
			}

			// To prevent the for loop in the first place assign an empty array
			// in case there are no cookies at all. Also prevents odd result when
			// calling "get()"
			var cookies = document.cookie ? document.cookie.split('; ') : [];
			var rdecode = /(%[0-9A-Z]{2})+/g;
			var i = 0;

			for (; i < cookies.length; i++) {
				var parts = cookies[i].split('=');
				var cookie = parts.slice(1).join('=');

				if (cookie.charAt(0) === '"') {
					cookie = cookie.slice(1, -1);
				}

				try {
					var name = parts[0].replace(rdecode, decodeURIComponent);
					cookie = converter.read ?
						converter.read(cookie, name) : converter(cookie, name) ||
						cookie.replace(rdecode, decodeURIComponent);

					if (this.json) {
						try {
							cookie = JSON.parse(cookie);
						} catch (e) {}
					}

					if (key === name) {
						result = cookie;
						break;
					}

					if (!key) {
						result[name] = cookie;
					}
				} catch (e) {}
			}

			return result;
		}

		api.set = api;
		api.get = function (key) {
			return api.call(api, key);
		};
		api.getJSON = function () {
			return api.apply({
				json: true
			}, [].slice.call(arguments));
		};
		api.defaults = {};

		api.remove = function (key, attributes) {
			api(key, '', extend(attributes, {
				expires: -1
			}));
		};

		api.withConverter = init;

		return api;
	}

	return init(function () {});
}));











// Set variables
var page_title = jQuery(document).find("title").text();
var page_url = window.location.href;
var page_referrer = document.referrer;
var form_saved = false;
var ignore_form = false;
var hashkey;
var ir_last_visit;
var inboundrocket_debug_mode = inboundrocket_track_options.ir_debug;
var evercookie = inboundrocket_track_options.evercookie;

if( evercookie == 1 ){
	ec.get("ir_hash", function(value) {
		hashkey = value;
		run_activation(hashkey, jQuery);
	});
} else {
	hashkey = Cookies.get('ir_hash');
	run_activation(hashkey, jQuery);
}

function run_activation(hashkey, $){
  	
  	if(!hashkey || hashkey.length == 0){
        hashkey = Math.random().toString(36).slice(2);
		
		if(evercookie != 1) Cookies.set('ir_hash', hashkey, { expires: 30, path: '/' }); else ec.set("ir_hash", hashkey);
		
        if(inboundrocket_debug_mode){ console.log("New Hashkey: "+hashkey); }
        if(inboundrocket_debug_mode){ console.log("\nFUNCTION FIRED: inboundrocket_insert_lead()"); }
		inboundrocket_insert_lead(hashkey, page_referrer);
		inboundrocket_log_pageview(hashkey, page_title, page_url, page_referrer, Cookies.get('ir_last_visit'));
    }else{
        if(inboundrocket_debug_mode){ console.log("Hashkey Exists: "+hashkey); }
        if(inboundrocket_debug_mode){ console.log("\nFUNCTION FIRED: inboundrocket_insert_lead()"); }
        inboundrocket_insert_lead(hashkey, page_referrer);
        inboundrocket_log_pageview(hashkey, page_title, page_url, page_referrer, Cookies.get('ir_last_visit'));
    }
     
	$("form").bind("submit",function() {
     	if ( !($(this).attr('id') == 'welcome_bar-form') && !($(this).attr('id') == 'mwelcome_bar-form') && ! ( $(this).attr('id') == 'loginform' && $(this).attr('action').indexOf('wp-login.php') != -1 ) && ! ( $(this).attr('id') == 'lostpasswordform' && $(this).attr('action').indexOf('wp-login.php') != -1 ) ) {
	 		var $form = $(this);
	 		inboundrocket_submit_form($form, $, hashkey);
     	}
  	});
    
    var date = new Date();
    var current_time = date.getTime();
    date.setTime(date.getTime() + (60 * 60 * 1000));
    
    // The ir_last_visit has expired, so check to see if this is a stale contact that has been merged
    if ( !Cookies.get('ir_last_visit') )
    {
        inboundrocket_check_merged_contact(hashkey);
    }
	
    Cookies.set("ir_last_visit", current_time, {path: "/", domain: "", expires: date});			
}


function inboundrocket_check_merged_contact ( hash )
{
    jQuery.ajax({
        type: 'POST',
        url: inboundrocket_track_options.ajax_url,
        data: {
            "action": "inboundrocket_check_merged_contact", 
            "ir_id": hash
        },
        success: function(data){
            // Force override the current tracking with the merged value
            var json_data = jQuery.parseJSON(data);
            if ( json_data ){
				Cookies.set("ir_hash", json_data, {path: "/", domain: ""});
			}
        },
        error: function ( error_data ) {
            console.log(error_data);
        }
    });
}

// Check if submission cookie exists and re-insert if it does exist
function inboundrocket_check_submission()
{
	var ir_submission_cookie = Cookies.get("ir_submission");

    // The submission didn't officially finish before the page refresh, so try it again
    if ( ir_submission_cookie && ir_submission_cookie.length != 0 )
    {
        var submission_data = JSON.parse(ir_submission_cookie);
        inboundrocket_insert_form_submission(
            submission_data.submission_hash, 
            submission_data.hashkey, 
            submission_data.page_title, 
            submission_data.page_url, 
            submission_data.json_form_fields, 
            submission_data.lead_email, 
            submission_data.lead_first_name, 
            submission_data.lead_last_name, 
            submission_data.lead_phone, 
            submission_data.form_selector_id, 
            submission_data.form_selector_classes, 
            function ( data ) {
                // Form was submitted successfully before page reload. Delete cookie for this submission
                Cookies.remove('ir_submission');
            }
        );
    }
}

function inboundrocket_submit_form ( $form, $, hashkey )
{
    if ( inboundrocket_debug_mode )
    {
        console.log("\nFUNCTION FIRED: inboundrocket_submit_form()");
        console.log("FIELDS:\n-----------\n");
    }

    var $this = $form;

    var form_fields     = [];
    var lead_email      = '';
    var lead_first_name = '';
    var lead_last_name  = '';
    var lead_phone      = '';
    var form_selector_id        = ( $form.attr('id') ? $form.attr('id') : '' );
    var form_selector_classes   = ( $form.classes() ? $form.classes().join(',') : '' );

    // Excludes hidden input fields + submit inputs
    $this.find('input[type!="submit"], textarea').not('input[type="radio"], input[type="password"]').each( function ( index ) {  // Removed input[type="hidden"] from the list of not seen fields 12/2/15.

        var $element = $(this);
        var $value = $element.val();

        if ( !$element.is(':visible' ) )
            return true; 

        // Check if input has an attached lable using for= tag
        var $label = $("label[for='" + $element.attr('id') + "']").text();
        
        // Ninja Forms hack
        if ($label.length == 0) 
        {
            if ( $('#' + $element.attr('id') + "_label").length )
                $label = $('#' + $element.attr('id') + "_label").text();
        }

        // Check for label in same container immediately before input
        if ($label.length == 0) 
        {
            $label = $element.prev('label').not('.ir_used').addClass('ir_used').first().text();

            if ( !$label.length ) 
            {
                $label = $element.prevAll('b, strong, span').text(); // Find previous closest string
            }
        }

        // Check for label in same container immediately after input
        if ($label.length == 0) 
        {
            $label = $element.next('label').not('.ir_used').addClass('ir_used').first().text();

            if ( !$label.length ) 
            {
                $label = $element.nextAll('b, strong, span').text(); // Find next closest string
            }
        }

        // Checks the parent for a label or bold text
        if ($label.length == 0) 
        {
            $label = $element.parent().find('label, b, strong').not('.ir_used').first().text();
        }

        // Checks the parent's parent for a label or bold text
        if ($label.length == 0) 
        {
            if ( $.contains($this, $element.parent().parent()) )
            {
                $label = $element.parent().parent().find('label, b, strong').first().text();
            }
        }

        // Looks for closests p tag parent, and looks for label inside
        if ( $label.length == 0 ) 
        {
            var $p = $element.closest('p').not('.ir_used').addClass('ir_used');
            
            // This gets the text from the p tag parent if it exists
            if ( $p.length )
            {
                $label = $p.text();
                $label = $.trim($label.replace($value, "")); // Hack to exclude the textarea text from the label text
            }
        }

        // Check for placeholder attribute
        if ( $label.length == 0 )
        {
            if ( $element.attr('placeholder') !== undefined )
            {
                $label = $element.attr('placeholder').toString();
            }
        }

        if ( $label.length == 0 ) 
        {
            if ( $element.attr('name') !== undefined )
            {
                $label = $element.attr('name').toString();
            }
        }

        if ( $element.is(':checkbox') )
        {
            if ( $element.is(':checked')) 
            {
                $value = inboundrocket_track_options.lang_checked;
            }
            else
            {
                $value = inboundrocket_track_options.lang_not_checked;
            }
        }

        // Remove fakepath from input[type="file"]
        $value = $value.replace("C:\\fakepath\\", "");

        var $label_text = $.trim($label.replaceArray(["(", ")", "required", "Required", "*", ":"], [""]));
        var lower_label_text = $label_text.toLowerCase();

        if ( ! ignore_field($label_text, $value) )
            push_form_field($label_text, $value, form_fields);
        else
        {
            if ( inboundrocket_debug_mode )
                console.log('   - Skipping... label: ' + $label + ' value: ' + $value); 
        }
				
        // Set email
        if ( $value.indexOf('@') != -1 && $value.indexOf('.') != -1 )
            lead_email = $value;

        // Set first name 
        if ( ! lead_first_name )
        {
            if ( $element.attr('id') == 'inboundrocket-subscribe-fname' )
                lead_first_name = $value;
            else if ( lower_label_text == inboundrocket_track_options.lang_first || lower_label_text == inboundrocket_track_options.lang_first_name || lower_label_text == inboundrocket_track_options.lang_name || lower_label_text == inboundrocket_track_options.lang_your_name)
                lead_first_name = $value;
        }

        // Set last name
        if ( ! lead_last_name )
        {
            if ( $element.attr('id') == 'inboundrocket-subscribe-lname' )
                lead_last_name = $value;
            else if ( lower_label_text == inboundrocket_track_options.lang_last || lower_label_text == inboundrocket_track_options.lang_last_name || lower_label_text == inboundrocket_track_options.lang_your_last_name )
                lead_last_name = $value;
        }

        // Set phone number
        if ( ! lead_phone )
        {
            if ( $element.attr('id') == 'inboundrocket-subscribe-phone' )
                lead_phone = $value;
            else if ( lower_label_text == inboundrocket_track_options.lang_phone || lower_label_text == inboundrocket_track_options.lang_phone_number )
                lead_phone = $value;
        }
    });

    var radio_groups = [];
    var rbg_label_values = [];
    $this.find(":radio").each(function(){
        if ( $.inArray(this.name, radio_groups) == -1 )
            radio_groups.push(this.name);
            rbg_label_values.push($(this).val());
    });

    for ( var i = 0; i < radio_groups.length; i++ )
    {
        var $rbg = $("input:radio[name='" + radio_groups[i] + "']");
        var $rbg_value = $("input:radio[name='" + radio_groups[i] + "']:checked").val();

        if ( $this.find('.gfield').length ) // Hack for gravity forms
            $p = $rbg.closest('.gfield').not('.ir_used').addClass('ir_used');
        else if ( $this.find('.frm_form_field').length ) // Hack for Formidable
            $p = $rbg.closest('.frm_form_field').not('.ir_used').addClass('ir_used');
        else
            $p = $rbg.closest('div, p').not('.ir_used').addClass('ir_used');
        
        // This gets the text from the p tag parent if it exists
        if ( $p.length )
        {
            //$p.find('label, strong, span, b').html();
            $rbg_label = $p.text();
            $rbg_label = $.trim($rbg_label.replaceArray(rbg_label_values, [""]).replace($p.find('.gfield_description').text(), ''));
            // Remove .gfield_description from gravity forms
        }

        var rgb_selected = ( !$("input:radio[name='" + radio_groups[i] + "']:checked").val() ) ? 'not selected' : $("input:radio[name='" + radio_groups[i] + "']:checked").val();

        if ( ! ignore_field($rbg_label, rgb_selected) )
            push_form_field($rbg_label, rgb_selected, form_fields);
        else
        {
            if ( inboundrocket_debug_mode )
                console.log('Skipping... label: ' + $label + ' value: ' + $value);  
        }
    }

    $this.find('select').each( function ( ) {
        var $select = $(this);
        var $select_label = $("label[for='" + $select.attr('id') + "']").text();

        if ( !$select_label.length )
        {
            var select_values = [];
            $select.find("option").each(function(){
                if ( $.inArray($(this).val(), select_values) == -1 )
                    select_values.push($(this).val());
            });

            $p = $select.closest('div, p').not('.ir_used').addClass('ir_used');

            if ( $this.find('.gfield').length ) // Hack for gravity forms
                $p = $select.closest('.gfield').not('.ir_used').addClass('ir_used');
            else
            {   
                $p = $select.closest('div, p').addClass('ir_used');
            }

            if ( $p.length )
            {
                $select_label = $p.text();
                $select_label = $.trim($select_label.replaceArray(select_values, [""]).replace($p.find('.gfield_description').text(), ''));
            }
        }

        var select_value = '';
        if ( $select.val() instanceof Array )
        {
            var select_vals = $select.val();
            
            for ( i = 0; i < select_vals.length; i++ )
            {
                select_value += select_vals[i];
                if ( i != select_vals.length - 1 )
                    select_value += ', ';
            }
        }
        else
        {
            if ( $select.find('option:selected').text() )
                select_value = $select.find('option:selected').text();
            else
                select_value = $select.val();
        }

        if ( ! ignore_field($select_label, select_value) )
            push_form_field($select_label, select_value, form_fields);
        else
        {
            if ( inboundrocket_debug_mode )
                console.log('Skipping... label: ' + $label + ' value: ' + $value);  
        }
    });

    $this.find('.ir_used').removeClass('ir_used'); // Clean up added classes
	
    // Save submission into database if email is present and form is not ignore, send inboundrocket email, and submit form as usual
    if ( lead_email )
    {
        if ( inboundrocket_debug_mode )
            console.log("\nFOUND lead_email: " + lead_email + "\n");

        if ( ignore_form )
        {
            push_form_field(inboundrocket_track_options.lang_credit_card_submitted, inboundrocket_track_options.lang_payment_fields, form_fields);
        }

        var submission_hash = Math.random().toString(36).slice(2);

        var json_form_fields = JSON.stringify(form_fields);
				
        var form_submission = {
            "submission_hash":  submission_hash,
            "hashkey":          hashkey,
            "lead_email":       lead_email,
            "lead_first_name":  lead_first_name,
            "lead_last_name":   lead_last_name,
            "lead_phone":       lead_phone,
            "page_title":       page_title,
            "page_url":         page_url,
            "json_form_fields":         json_form_fields,
            "form_selector_id":         form_selector_id,
            "form_selector_classes":    form_selector_classes
        };

        if ( inboundrocket_debug_mode )
        {
            console.log("\nFORM SUBMISSION OBJECT:");
            console.log(form_submission);
        }

        Cookies.set("ir_submission", JSON.stringify(form_submission), {path: "/", domain: ""});

        inboundrocket_insert_form_submission(
            submission_hash, 
            hashkey, 
            page_title, 
            page_url, 
            json_form_fields, 
            lead_email, 
            lead_first_name, 
            lead_last_name, 
            lead_phone,
            form_selector_id,
            form_selector_classes, 
            function ( data ) {
                // Form was executed 100% successfully before page reload. Delete cookie for this submission
                Cookies.remove('ir_submission');
            }
        );
        
        if ( inboundrocket_debug_mode )
        {
        	console.log("\nFORM SUBMISSION SUCCESS");
		}
    }
    else // No lead - submit form as usual
    {
        form_saved = true;

        if ( inboundrocket_debug_mode )
            console.log("\ERROR: lead_email not found\n");
    }
}

// Inserts submissions into WP
function inboundrocket_insert_form_submission ( sub_hash, hash, page_title, page_url, json_fields, lead_email, lead_first_name, lead_last_name, lead_phone, form_selector_id, form_selector_classes, Callback )
{
    if ( inboundrocket_debug_mode )
        console.log("\nFUNCTION FIRED: inboundrocket_insert_form_submission()");

    jQuery.ajax({
        type: 'POST',
        url: inboundrocket_track_options.ajax_url,
        data: {
        	action: 		  "inboundrocket_insert_form_submission",
	        ir_nonce:         inboundrocket_track_options.ir_nonce,
	        ir_submission_id: sub_hash,
	        ir_id:            hash,
	        ir_title:         page_title,
	        ir_url:           page_url,
	        ir_fields:        json_fields,
	        ir_email:         lead_email,
	        ir_first_name:    lead_first_name,
	        ir_last_name:     lead_last_name,
	        ir_phone:         lead_phone,
	        ir_form_selector_id:  form_selector_id,
	        ir_form_selector_classes: form_selector_classes
    	},
        success: function(data){
            
            if ( inboundrocket_debug_mode )
                console.log('Contact Type: ' + data);
            
            if ( Callback )
                Callback(data);
        },
        error: function ( error_data ) {
            if ( inboundrocket_debug_mode ) {
                console.log('Form Submission Failed:');
                console.log(error_data);
            }
        },
        async: false
    });

}

// Insets lead into WP
function inboundrocket_insert_lead ( hash, page_referrer )
{
    jQuery.ajax({
        type: 'POST',
        url: inboundrocket_track_options.ajax_url,
        data: {
            "action": "inboundrocket_insert_lead",
            "ir_nonce": inboundrocket_track_options.ir_nonce,
            "ir_id": hash,
            "ir_referrer": page_referrer
        },
        success: function(data){
            if(inboundrocket_debug_mode) { console.log("Lead Insert: "+data); }
        },
        error: function ( error_data ) {
			console.log("Error: "+error_data);
        }
    });
}

// Logs page views
function inboundrocket_log_pageview ( hash, page_title, page_url, page_referrer, last_visit )
{
    jQuery.ajax({
        type: 'POST',
        url: inboundrocket_track_options.ajax_url,
        data: {
            "action": "inboundrocket_log_pageview",
            "ir_nonce": inboundrocket_track_options.ir_nonce,
            "ir_id": hash,
            "ir_title": page_title,
            "ir_url": page_url,
            "ir_referrer": page_referrer,
            "ir_last_visit": last_visit
        },
        success: function(data){
            if(inboundrocket_debug_mode) { console.log("Log Pageview: "+data); }
        },
        error: function ( error_data ) {
			console.log("Error: "+error_data);
        }
    });
}

// NOT BEING USED? - checks if the hashkey exists in the tag relationship lists
function inboundrocket_check_visitor_status ( hash, callback )
{
    jQuery.ajax({
        type: 'POST',
        url: inboundrocket_track_options.ajax_url,
        data: {
            "action": "inboundrocket_check_visitor_status",
            "ir_nonce": inboundrocket_track_options.ir_nonce,
            "ir_id": hash
        },
        success: function(data){
            // Force override the current tracking with the merged value
            var json_data = jQuery.parseJSON(data);

            if ( callback )
                callback(json_data);
        },
        error: function ( error_data ) {

        }
    });
}

function push_form_field ( label, value, form_fields )
{
    var field = {
        label: label,
        value: value
    };

    form_fields.push(field);

    if ( inboundrocket_debug_mode )
        console.log('   + Adding... [label:] ' + label + ' [value:] ' + value);
}

function ignore_field ( label, value )
{
    var bool_ignore_field = false;

    // Ignore any fields with labels that indicate a credit card field
    if ( label.toLowerCase().indexOf(inboundrocket_track_options.lang_credit_card) != -1 || label.toLowerCase().indexOf(inboundrocket_track_options.lang_card_number) != -1 )
        bool_ignore_field = true;

    if ( label.toLowerCase().indexOf(inboundrocket_track_options.lang_expiration) != -1 || label.toLowerCase().indexOf(inboundrocket_track_options.lang_expiry) != -1)
        bool_ignore_field = true;

    if ( label.toLowerCase() == inboundrocket_track_options.lang_month || label.toLowerCase() == 'mm' || label.toLowerCase() == 'yy' || label.toLowerCase() == 'yyyy' || label.toLowerCase() == inboundrocket_track_options.lang_year )
        bool_ignore_field = true;

    if ( label.toLowerCase().indexOf('cvv') != -1 || label.toLowerCase().indexOf('cvc') != -1 || label.toLowerCase().indexOf(inboundrocket_track_options.lang_secure_code) != -1 || label.toLowerCase().indexOf(inboundrocket_track_options.lang_security_code) != -1 )
        bool_ignore_field = true;

    if ( value.toLowerCase() == 'visa' || value.toLowerCase() == 'mastercard' || value.toLowerCase() == 'american express' || value.toLowerCase() == 'amex' || value.toLowerCase() == 'discover' )
        bool_ignore_field = true;

    // Check if value has integers, strip out spaces, then ignore anything with a credit card length (>16) or an expiration/cvv length (<5)
    var int_regex = new RegExp("/^[0-9]+$/"); 
    if ( int_regex.test(value) )
    {
        var value_no_spaces = value.replace(' ', '');

        if ( isInt(value_no_spaces) && value_no_spaces.length >= 16 )
            bool_ignore_field = true;
    }

    // Hack for the form parser sometimes rolling up all form fields into one massive label
    if ( label.length > 250 )
        bool_ignore_field = true;

    if ( bool_ignore_field )
    {
        if ( ! ignore_form )
            ignore_form = true;

        return true;
    }
    else
        return false;
}

String.prototype.replaceArray = function(find, replace) {
  var replaceString = this;
  for (var i = 0; i < find.length; i++) {
    if ( replace.length != 1 )
        replaceString = replaceString.replace(find[i], replace[i]); 
    else
        replaceString = replaceString.replace(find[i], replace[0]); 
  }
  return replaceString;
};

// Checks the version number of jQuery and compares to string
(function($){
  $.versioncompare = function(version1, version2){
    if ('undefined' === typeof version1) {
      throw new Error("$.versioncompare needs at least one parameter.");
    }
    version2 = version2 || $.fn.jquery;
    if (version1 == version2) {
      return 0;
    }
    var v1 = normalize(version1);
    var v2 = normalize(version2);
    var len = Math.max(v1.length, v2.length);
    for (var i = 0; i < len; i++) {
      v1[i] = v1[i] || 0;
      v2[i] = v2[i] || 0;
      if (v1[i] == v2[i]) {
        continue;
      }
      return v1[i] > v2[i] ? 1 : -1;
    }
    return 0;
  };
  function normalize(version){
    return $.map(version.split('.'), function(value){
      return parseInt(value, 10);
    });
  }
}(jQuery));

// Grabs the class names of the submitted form
(function ($) {
    $.fn.classes = function (callback) {
        var classes = [];
        $.each(this, function (i, v) {
            var splitClassName = v.className.split(/\s+/);
            for (var j in splitClassName) {
                var className = splitClassName[j];
                if (-1 === classes.indexOf(className)) {
                    classes.push(className);
                }
            }
        });
        if ('function' === typeof callback) {
            for (var i in classes) {
                callback(classes[i]);
            }
        }
        return classes;
    };
})(jQuery);

function isInt ( n ) {
    return typeof n== "number" && isFinite(n) && n%1===0;
}