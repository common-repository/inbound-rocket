if (typeof ir_sb_box_js_options == 'object') {
	var boxesOptions = ir_sb_box_js_options;
		
	for (boxId in boxesOptions) {	
		var boxOptions = boxesOptions[boxId];
		
		var cookieName = 'ir_sb_'+boxId;
		var cookieExp  = boxOptions["cookie_exp"];
		var testMode   = ( parseInt(boxOptions["test_mode"]) == 1 ) ? true : false;
		
		var irsbBoxCookie = ( irsbGetCookie(cookieName) == '1' ) ? true : false;
		
		if( !irsbBoxCookie || testMode ){
		  //console.log(boxOptions);
		  loadTheBox( boxId, boxOptions["animation"], boxOptions["autoShow"], boxOptions["autoShowPercentage"], boxOptions["autoShowElement"], boxOptions["autoHide"], boxOptions["hideScreenSize"], cookieName, cookieExp, testMode);
		}
	}
}

function irsbSetCookie(name,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "expires=" + date.toUTCString() + ";";
    }
    document.cookie = name + "=1; " + expires + " path=/";
}

function irsbGetCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function loadTheBox(boxID, animation, autoShow, autoShowPercent, autoShowElement, autoHide, hideOnScreenSize, cookieName, cookieExp, testMode){
	
	var selector = '.ir-sb-' + boxID;
	var hideOnSc 		= parseInt(hideOnScreenSize);
	var autoShowPercent = parseInt(autoShowPercent);
	var windowWidth 	= jQuery(window).width();
	var windowHeight 	= jQuery(window).height();
	var documentHeight  = jQuery(document).height();
	
	
	//console.log(hideOnScreenSize+' - '+windowWidth);

	if( hideOnSc >= 0 && hideOnSc <= windowWidth ) {
	  if( autoShow === 'instant' && irsbGetCookie(cookieName) != '1' ){
		showBox(selector, animation);	
	  } else if( autoShow === 'percentage' && irsbGetCookie(cookieName) != '1' ){
		jQuery( window ).scroll(function() {
		  if( irsbGetCookie(cookieName) == '1' ) return false; 
		  
		  var scrollPercent = 100 * jQuery(window).scrollTop() / ( documentHeight - windowHeight );
		  if( Math.round(scrollPercent) >= autoShowPercent ){
			showBox(selector, animation);
		  }
  
		  if( autoHide == "1" && Math.round(scrollPercent) < autoShowPercent ){
			hideBox(selector, animation)
		  }
		});
	  } else if( autoShow === 'element' && irsbGetCookie(cookieName) != '1' ){
		jQuery(document).scroll(function(){
		  if(jQuery(this).scrollTop()>=jQuery( autoShowElement ).position().top){
			  if( irsbGetCookie(cookieName) == '1' ) return false; 
			  showBox(selector, animation);
		  }
		});
	  }

	  jQuery(selector +' .ir-sb-close').on('click', function(c){
		hideBox(selector, animation);
		if(cookieExp > 0 && !testMode){
		 irsbSetCookie(cookieName, cookieExp);
		}	
		
	  });	
	}
}

function showBox(selector, animation){
	if(animation === 'fade')
		jQuery( selector ).fadeIn( 2000 );
	  else if (animation === 'slide')
		jQuery( selector ).slideDown( 2000 );
}

function hideBox(selector, animation){
	if(animation === 'fade')
	  jQuery( selector ).fadeOut( 2000 );
	else if (animation === 'slide')
	  jQuery( selector ).slideUp( 2000 );
}