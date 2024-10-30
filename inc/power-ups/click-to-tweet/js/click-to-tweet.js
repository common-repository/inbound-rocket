jQuery(document).ready(function(){
 jQuery('.ctt-action').click(function(e){
	
	e.preventDefault();
	
	var sel = this;
	var href;
	var text;
	var postId;
	
	href = jQuery(this).attr('href');
	text = jQuery(this).data('text');
		
	var selectionContainer = jQuery(this).parents('article');
    if (selectionContainer.length) {
        var postIdentifier = selectionContainer.attr('id').match(/post-(\d+)/);
        if (postIdentifier) {
            postId = postIdentifier[1];
        }
    }    
    
    window.open(href);

	jQuery.ajax({
        url: inboundrocket_ctt_js.ajaxurl,
        type: 'post',
		async: false,
		dataType: 'json',
		data: {
	      nonce: inboundrocket_ctt_js.nextNonce,
          action: 'click_to_tweet_track',
          postid: postId,
          text: text,
          type: 'click-to-tweet'
        },
        success: function(result){
	     	if(result.status && result.status == 200){
	     		// debug
	     		// console.log('click-to-tweet success');
	     	} else {
		     	// debug
		     	// console.log('click-to-tweet failed');
	     	}
	    }
    });
 });
})