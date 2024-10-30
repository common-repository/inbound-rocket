jQuery(document).ready(function(){
	tinymce.PluginManager.add('ir_clicktotweet', function(editor, url) {
	    // Add a button that opens a window
	    editor.addButton('ir_clicktotweet', {
	        title: 'Inbound Rocket - Click To Tweet',
	        image: '/wp-content/plugins/inbound-rocket/inc/power-ups/click-to-tweet/img/twitter-little-bird-button.png',
	        onclick: function() {
	            // Open window
	            editor.windowManager.open({
	                title: 'Inbound Rocket - Click To Tweet',
	                body: [
	                    {type: 'textbox', name: 'clicktotweet', label: 'Enter your text'}
	                ],
	                onsubmit: function(e) {
	                    if (e != null && e != 'undefined' && e != '') {
	                    	editor.insertContent('[clicktotweet]' + e.data.clicktotweet + '[/clicktotweet]');
	                    }
	                }
	            });
	        }
	    });
	});
});