<?php
if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

//=============================================
// WPInboundRocketAdmin Class
//=============================================
class WPPostmaticConnectorAdmin extends WPInboundRocketAdmin {
    
    var $power_up_icon;
    var $options;
    var $nonce;
    
    public $authed = FALSE;
    public $invalid_key = FALSE;
    
    private static $_instance = null;
    
    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }
    
	/**
     * Class constructor
     */
    function __construct ()
    {
	    //=============================================
        // Hooks & Filters
        //=============================================
        
        $this->options = get_option('inboundrocket_pm_options');
        
        $this->authed = !empty( $this->options['ir_pm_api_key'] ) ? TRUE : FALSE;
        
        if ( $this->authed )
            $this->invalid_key = $this->ir_pm_check_invalid_api_key($this->options['ir_pm_api_key']);
    }
    
    public static function init(){
        if(self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;            
    }
	
	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    function sanitize ( $input )
    {
        $new_input = array();
        
        if( isset( $input['ir_pm_api_key'] ) )
        {
            $new_input['ir_pm_api_key'] = sanitize_text_field( $input['ir_pm_api_key'] );
			if ( $this->authed )
            	$this->invalid_key = $this->ir_pm_check_invalid_api_key($new_input['ir_pm_api_key']);
        }   
        if( isset( $input['ir_pm_list_id'] ) )
            $new_input['ir_pm_list_id'] = sanitize_text_field( $input['ir_pm_list_id'] );

        return $new_input;
    }
	
	function ir_pm_delete_api_key ( )
	{
		unset($this->options['ir_pm_api_key']);
		$this->authed = FALSE;
	}
		
    /**
     * Get synced list for the ESP from the WordPress database
     *
     * @return array/object    
     */
    function ir_get_synced_list_for_esp ( $esp_name, $output_type = 'OBJECT' )
    {
        global $wpdb;

        $q = $wpdb->prepare("SELECT * FROM {$wpdb->ir_tags} WHERE tag_synced_lists LIKE '%%%s%%' AND tag_deleted = 0", $esp_name);
        $synced_lists = $wpdb->get_results($q, $output_type);
		
		//die(print_r($synced_lists));
		
        return $synced_lists;
    }
    
    /**
     * Prints input form for settings page, if connected prints synced lists out for settings page in format  Lead List Name → ESP list
     */
  
	function ir_pm_input_fields () 
	{        
		if (class_exists('Prompt_Api')) {
			// Postmatic is installed
			
			if (class_exists( 'Prompt_Core' ) && Prompt_Core::$options->get( 'prompt_key' )) {
				// Postmatic is installed and has the correct API key
	            $synced_lists = $this->ir_get_synced_list_for_esp('postmatic');
	            $list_value_pairs = array();
	            $synced_list_count = 0;
	
	            echo '<table>';
	            foreach ( $synced_lists as $synced_list )
	            {
	                foreach ( stripslashes_deep(unserialize($synced_list->tag_synced_lists)) as $tag_synced_list )
	                {
	                    if ( $tag_synced_list['esp'] == 'postmatic' )
	                    {
	                        echo '<tr class="synced-list-row">';
	                            echo '<td class="synced-list-cell"><span class="icon-tag"></span> ' . $synced_list->tag_text . '</td>';
	                            echo '<td class="synced-list-cell"><span class="synced-list-arrow">&#8594;</span></td>';
	                            echo '<td class="synced-list-cell"><span class="icon-envelope"></span> ' . $tag_synced_list['list_name'] . '</td>';
	                            echo '<td class="synced-list-edit"><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists&action=edit_list&lead_list=' . $synced_list->tag_id . '">'.__('edit list','inbound-rocket').'</a></td>';
	                        echo '</tr>';
	
	                        $synced_list_count++;
	                    }
	                }
	            }
	            echo '</table>';
	
	            if ( ! $synced_list_count ) {
	                echo '<p>Postmatic '.__('connected successfully','inbound-rocket').'. <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists">'.__('Select a lead list to send contacts to','inbound-rocket').' Postmatic</a>.</p>';
	            } else {
	                echo '<p><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=inboundrocket_lead_lists">'.__('Edit your lead lists','inbound-rocket').'</a>';
	            }
	
			} else {
			echo '<p>'.__('It seems you need to complete your Postmatic configuration.', 'inbound-rocket').' <a target="_blank" href="http://docs.gopostmatic.com/article/115-how-to-request-and-enter-your-api-key">'.__('Get your API key','inbound-rocket').'</a> and activate <a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=postmatic">Postmatic</a>.</p>';
			}
		} else {
			echo '<p>'.__('In order for this power-up to work you will need to install','inbound-rocket').' <a href="' . get_bloginfo('wpurl') . '/wp-admin/plugin-install.php?tab=plugin-information&plugin=postmatic">Postmatic</a>.</p>';
		}
	}
	
}

?>
