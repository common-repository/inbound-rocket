/*
 * welcome-bar: Email is the best way to start a relationship with your visitors, educate them about your services, and turn your visitors into paying customers. Start collecting those emails right now.
 *
 * -- Requires jQuery --
 *
 * Author: Inbound Roket (@inboundrocket)
 */
 
var inboundrocket_debug_mode = inboundrocket_wb_js.ir_debug;
var evercookie = inboundrocket_wb_js.evercookie;
var ir_wb_show_on = inboundrocket_wb_js.ir_wb_show_on;
var ir_wb_hide = inboundrocket_wb_js.ir_wb_hide;

var wb_hide = Cookies.get('wb_hide');

if(wb_hide==1) ir_wb_show_on = 'hidden';
if(inboundrocket_debug_mode){ console.log("\nSHOW BAR ON: " + ir_wb_show_on); }

(function($) {

	switch(ir_wb_show_on) {
		case 'desktop':
		// START welcome-bar FOR DESKTOP
		var stub_showing = false;
		 
	    welcome_bar_show = function () { 
	        if(stub_showing) {
	          $('.welcome_bar-stub').slideUp('fast', function() {
	            $('.welcome_bar').show('bounce', { times:3, distance:15 }, 300);
	            if($('#wpadminbar').length > 0) $('.welcome_bar').css({"margin-top": "32px","z-index":"100"});
	            if($('#wpadminbar').length > 0) $('body').animate({"marginTop": "55px"}, 300); else $('body').animate({"marginTop": "20px"}, 300);
	          }); 
	        }
	        else {
	          $('.welcome_bar').show('bounce', { times:3, distance:15 }, 500);
	          if (ir_wb_show_on === 'mobile') {
	          	if($('#wpadminbar').length > 0) $('.welcome_bar').css({"margin-top": "32px","z-index":"100"});
			  	if($('#wpadminbar').length > 0) $('body').animate({"marginTop": "55px"}, 300); else $('body').animate({"marginTop": "20px"}, 300);
			  }
	        }
	    }
	 
		welcome_bar_hide = function () { 
	        $('.welcome_bar').slideUp('fast', function() {
	          $('.welcome_bar-stub').show('bounce', { times:3, distance:15 }, 100);
	          if($('#wpadminbar').length > 0) $('.welcome_bar-stub').css({"margin-top": "35px"});
	          stub_showing = true;
	        }); 
	 
	        if( $(window).width() > 1024 ) {
	          $('body').animate({"marginTop": "-20px"}, 250); // if width greater than 1024 pull up the body
	        }
	    }
	 
	    $(document).ready(function() {
	        window.setTimeout(function() {
	        	if(wb_hide==1 && ir_wb_hide=="1") welcome_bar_hide(); else welcome_bar_show();
				$('.close-notify').on('click',function(){welcome_bar_hide();});
	     }, 0); // change this from "0" to any number in milliseconds to delay welcome_bar from showing right away
	    });
		// END welcome_bar FOR DESKTOP
		break;
		
		case 'mobile':
		// START welcome_bar FOR MOBILE
	
		var mstub_showing = false;
		 
	    mwelcome_bar_show = function () { 
	        if(mstub_showing) {
	          $('.mwelcome_bar-stub').slideUp('fast', function() {
	            $('.mwelcome_bar').show('bounce', { times:3, distance:15 }, 300);
	            $('body').animate({"marginTop": "32px"}, 300);
	          }); 
	        }
	        else {
	          $('.mwelcome_bar').show('bounce', { times:3, distance:15 }, 500);
	          $('body').animate({"marginTop": "32px"}, 250);
	        }
	    }
	 
	    mwelcome_bar_hide = function () { 
	        $('.mwelcome_bar').slideUp('fast', function() {
	          $('.mwelcome_bar-stub').show('bounce', { times:3, distance:15 }, 100);
	          mstub_showing = true;
	        }); 
	 
	        if( $(window).width() > 1024 ) {
	          $('body').animate({"marginTop": "0px"}, 250); // if width greater than 1024 pull up the body
	        }
	    }
	 
	    $(document).ready(function() {
	        window.setTimeout(function() {
	            if(wb_hide==1 && ir_wb_hide=="1") mwelcome_bar_hide(); else mwelcome_bar_show()();
				$('.mclose-notify').on('click',function(){mwelcome_bar_hide();});
	     }, 0); // change this from "0" to any number in milliseconds to delay welcome_bar from showing right away
	    });
		// END welcome_bar FOR MOBILE
		break;
		
		default:
		// START welcome-bar FOR DESKTOP
		var stub_showing = false;
		 
	    welcome_bar_show = function () { 
	        if(stub_showing) {
	          $('.welcome_bar-stub').slideUp('fast', function() {
	            $('.welcome_bar').show('bounce', { times:3, distance:15 }, 300);
	            if($('#wpadminbar').length > 0) $('.welcome_bar').css({"margin-top": "32px","z-index":"100"});
	            if($('#wpadminbar').length > 0) $('body').animate({"marginTop": "55px"}, 300); else $('body').animate({"marginTop": "20px"}, 300);
	          }); 
	        }
	        else {
	          	$('.welcome_bar').show('bounce', { times:3, distance:15 }, 500);
	          	if($('#wpadminbar').length > 0) $('.welcome_bar').css({"margin-top": "32px","z-index":"100"});
			  	if($('#wpadminbar').length > 0) $('body').animate({"marginTop": "55px"}, 300); else $('body').animate({"marginTop": "20px"}, 300);
	        }
	    }
	 
		welcome_bar_show();
	   
		welcome_bar_hide = function () { 
	        $('.welcome_bar').slideUp('fast', function() {
	          $('.welcome_bar-stub').show('bounce', { times:3, distance:15 }, 100);
	          if($('#wpadminbar').length > 0) $('.welcome_bar-stub').css({"margin-top": "35px"});
	          stub_showing = true;
	        }); 
	 
	        if( $(window).width() > 1024 ) {
	          $('body').animate({"marginTop": "-20px"}, 250); // if width greater than 1024 pull up the body
	        }
	    }
	 
	    $(document).ready(function() {
	        window.setTimeout(function() {
	        if(wb_hide==1 && ir_wb_hide=="1") welcome_bar_hide(); else welcome_bar_show();
	        $('.close-notify').on('click',function(){welcome_bar_hide();});
	     }, 0); // change this from "0" to any number in milliseconds to delay welcome_bar from showing right away
	    });
		// END welcome_bar FOR DESKTOP
		
				// START welcome_bar FOR MOBILE
	
		var mstub_showing = false;
		 
	    mwelcome_bar_show = function () { 
	        if(mstub_showing) {
	          $('.mwelcome_bar-stub').slideUp('fast', function() {
	            $('.mwelcome_bar').show('bounce', { times:3, distance:15 }, 300);
	            $('body').animate({"marginTop": "32px"}, 300);
	          }); 
	        }
	        else {
	          $('.mwelcome_bar').show('bounce', { times:3, distance:15 }, 500);
	          $('body').animate({"marginTop": "32px"}, 250);
	        }
	    }
	 
	    mwelcome_bar_hide = function () { 
	        $('.mwelcome_bar').slideUp('fast', function() {
	          $('.mwelcome_bar-stub').show('bounce', { times:3, distance:15 }, 100);
	          mstub_showing = true;
	        }); 
	 
	        if( $(window).width() > 1024 ) {
	          $('body').animate({"marginTop": "0px"}, 250); // if width greater than 1024 pull up the body
	        }
	    }
	 
	    $(document).ready(function() {
	        window.setTimeout(function() {
	        if(wb_hide==1 && ir_wb_hide=="1") mwelcome_bar_hide(); else mwelcome_bar_show();
	        $('.mclose-notify').on('click',function(){mwelcome_bar_hide();});
	     }, 0); // change this from "0" to any number in milliseconds to delay welcome_bar from showing right away
	    });
		// END welcome_bar FOR MOBILE
		
	}
    
    function validateEmail(email) {
    	var re = /[^\s@]+@[^\s@]+\.[^\s@]+/;
    	return re.test(email);
	}
    
    //functions for signup form
	$("#welcome_bar-form,#mwelcome_bar-form").submit(function(e) {
		
	    e.preventDefault ? e.preventDefault() : e.returnValue = false;
	    
	    var form_selector_id        = ( $(this).attr('id') ? $(this).attr('id') : '' );
		var form_selector_classes   = ( $(this).classes() ? $(this).classes().join(',') : '' );
	    
	    if(inboundrocket_debug_mode){ console.log("\nWELCOME BAR FORM SUBMITTED:"); }
		
		$('button.welcome_bar-link').attr("disabled", "disabled");
		
		var ipaddress = $("input#ip_address").val();
		if(inboundrocket_debug_mode && ipaddress){ console.log("\nFIELD FOUND: #ip_address - "+ipaddress); }
		
	    var emailaddress = $("input#emailaddress").val();
	    if(inboundrocket_debug_mode && emailaddress){ console.log("\nFIELD FOUND: #emailaddress - "+emailaddress); }
	    
		var page_url = $("input#current_page").val();
		if(inboundrocket_debug_mode && page_url){ console.log("\nFIELD FOUND: #current_page - "+page_url); }
		
		var success = $('#welcome-span').data('success');
		var current_html = $('#welcome-span').html();

		if(!validateEmail(emailaddress)) {
			$('button.welcome_bar-link').removeAttr("disabled");
			if(inboundrocket_debug_mode){ console.log("\nFIELD VALIDATION: Failed - "+emailaddress); }
			return false;
		}
		
		var submission_hash = Math.random().toString(36).slice(2);
		
		var hashkey = Cookies.get('ir_hash');
		
		var data = {
			'action': 'welcome_bar-save_db',
			'hashkey' : hashkey,
			'wb_form_nonce' : inboundrocket_wb_js.nextNonce,
			'email': emailaddress,
			'page_url' : page_url,
			'submission_hash' : submission_hash,
			'form_selector_id' : form_selector_id,
			'form_selector_classes' : form_selector_classes,
			'ip_address' : ipaddress
		};
		if(inboundrocket_debug_mode){ console.log("\nFORM DATA: "); }
		if(inboundrocket_debug_mode){ console.log(data); }
		
	    $('#welcome-span').html('Processing...');
	    $.post(inboundrocket_wb_js.ajaxurl, data, function(response) {
		    if(inboundrocket_debug_mode){ console.log("\nFORM RESULT: "+response); }
			$('#welcome-span').html(success).fadeOut(5000, function(){
				Cookies.set('wb_hide', '1', { expires: 365, path: '' });
				$('#welcome-span').html(current_html).fadeIn();
				$("input#email").val("");
				$('button.welcome_bar-link').removeAttr("disabled");
				$('.welcome_bar').slideUp();
			});
		});
	});
  
})(jQuery);