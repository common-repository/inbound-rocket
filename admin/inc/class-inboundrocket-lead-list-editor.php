<?php
if ( !defined('ABSPATH') ) exit;
	
//=============================================
// IR_Contact Class
//=============================================
class IR_Lead_List_Editor {
	
	/**
	 * Variables
	 */
	var $lead_list_id;
	var $details;
	var $selectors;

	/**
	 * Class constructor
	 */
	function __construct ( $lead_list_id = FALSE ) 
	{
		if ( $lead_list_id )
			$this->lead_list_id = $lead_list_id;

		$this->selectors = $this->get_form_selectors();
	}

	/**
     * Get all the details for a list
     *
     * @param   int
     * @return  object
     */
	function get_tag_details ( $lead_list_id )
	{
		global $wpdb;
		$this->details =  $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->ir_tags} WHERE tag_id = %d", 
				$this->lead_list_id
			)
		);
	}

	/**
     * Get all the existing form selectors sorted by frequency
     *
     * @return  array
     */
	function get_form_selectors ( )
	{
		global $wpdb;
		$selectors = array();

		$classes = $wpdb->get_results("SELECT COUNT(form_selector_classes) AS freq, form_selector_classes FROM {$wpdb->ir_submissions} WHERE form_selector_classes != '' GROUP BY form_selector_classes ORDER BY freq DESC");
		
		if ( count($classes) )
		{
			foreach ( $classes as $class )
			{
				foreach ( explode(',', $class->form_selector_classes) as $class_selector )
				{
					if ( ! in_array('.' . $class_selector, $selectors) && $class_selector )
						array_push($selectors, '.' . $class_selector);
				}
			}
		}

		$ids = $wpdb->get_results("SELECT COUNT(form_selector_id) AS freq, form_selector_id FROM {$wpdb->ir_submissions} WHERE form_selector_id != '' GROUP BY form_selector_id ORDER BY freq DESC");

		if ( count($ids) )
		{
			foreach ( $ids as $id )
			{
				if ( ! in_array('#' . $id->form_selector_id, $selectors) && $id->form_selector_id )
					array_push($selectors, '#' . $id->form_selector_id);
			}
		}

		return $selectors;
	}

	/**
     * Add a new tag in the ir_tags table
     *
     * @param 	string
     * @param 	string
     * @param 	string
     * @return  int 		lead_list_id of last inserted tag
     */
	function add_list ( $tag_text, $tag_form_selectors, $tag_synced_lists )
	{
		global $wpdb;
		
		$tag_text = sanitize_text_field($tag_text);
		
		$tag_order = $wpdb->get_var("SELECT MAX(tag_order) FROM {$wpdb->ir_tags}");
		$tag_order = ( $tag_order ? $tag_order + 1 : 1 );
		$tag_slug = $this->generate_slug($tag_text);

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->ir_tags} 
				( tag_text, tag_slug, tag_form_selectors, tag_synced_lists, tag_order )
				VALUES ( %s, %s, %s, %s, %d )", 
				$tag_text, $tag_slug, $tag_form_selectors, $tag_synced_lists, $tag_order
			)
		);

		return $wpdb->insert_id;
	}

	/**
     * Update an existing tag in the ir_tags  table
     *
     * @param 	int
     * @param 	string
     * @param 	string
     * @param 	string 		serialized array(array('esp' => 'mailchimp', 'lead_list_id' => 'abc123'))
     * @return  bool
     */
	function save_list ( $lead_list_id, $tag_text, $tag_form_selectors, $tag_synced_lists )
	{
		global $wpdb;
		
		$tag_url = sanitize_text_field($tag_text);
		
		$tag_slug = $this->generate_slug($tag_url, $lead_list_id);

		$this->push_contacts_to_lead_list($lead_list_id, $tag_synced_lists);

		$result = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->ir_tags} 
				SET tag_text = %s, tag_slug = %s, tag_form_selectors = %s, tag_synced_lists = %s
				WHERE tag_id = %d", 
				$tag_text, $tag_slug, $tag_form_selectors, $tag_synced_lists, $lead_list_id
			)
		);

		// Add a method to loop through all the lists here and 

		return $result;
	}

	/**
     * Delete a tag from the ir_tags table
     *
     * @param 	int
     * @param 	string
     * @return  bool
     */
	function delete_list ( $lead_list_id )
	{
		global $wpdb;
		$result = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->ir_tags} SET tag_deleted = 1 WHERE tag_id = %d", 
				$lead_list_id
			)
		);
		return $result;
	}

	/**
     * Generates a slug based off a string. If slug exists, then appends -N until the slug is free
     *
     * @param 	string
     * @param 	int
     * @return  string
     */
	function generate_slug ( $tag_text, $lead_list_id = 0 )
	{
		global $wpdb;

		$tag_slug = sanitize_title($tag_text);
		$slug_used = TRUE;
		$slug_int_check = 0;

		while ( $slug_used )
		{
			// slug_int_check is set to 1, but we only want to use it if the slug exists, so kill it in the first iteration
			
			if ( $slug_int_check )
				$tag_slug_modified = $tag_slug . '-' . $slug_int_check;
			else
				$tag_slug_modified = $tag_slug;

			$slug_used = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT tag_slug FROM {$wpdb->ir_tags} WHERE tag_slug = %s " . ( $lead_list_id ? $wpdb->prepare(" AND tag_id != %d ", $lead_list_id) : '' ), 
					$tag_slug_modified
				)
			);

			if ( $slug_used )
				$slug_int_check++;
			else
				$tag_slug = $tag_slug_modified;
		}

		return $tag_slug;
	}

	/**
     * Gets all the contacts in a list given a lead_list_id
     *
     * @param 	int
     * @return  object
     */
	function get_contacts_in_tagged_list ( $lead_list_id = 0 )
	{
		global $wpdb;
		$contacts = $wpdb->get_results( 
			$wpdb->prepare(
				"SELECT contact_hashkey, lead_email, lead_first_name, lead_last_name FROM {$wpdb->ir_tag_relationships} ltr, {$wpdb->ir_leads} ll WHERE ltr.contact_hashkey = ll.hashkey AND ltr.tag_id = %d AND ltr.tag_relationship_deleted = 0", 
				$lead_list_id
			)
		);
		return $contacts;
	}

	/**
     * Push the contacts to a lead list
     *
     * @param 	int
     * @param 	string 		serialized array
     * @return  object
     */
	function push_contacts_to_lead_list ( $lead_list_id = 0, $tag_synced_lists = '' )
	{
		global $wpdb;

		if ( ! $tag_synced_lists )
		{
			$this->get_tag_details($lead_list_id);
			$tag_synced_lists = $this->details->tag_synced_lists;
		}
		
		$contacts = $this->get_contacts_in_tagged_list($lead_list_id);
		
		if ( count($contacts) && $tag_synced_lists )
		{
			$synced_lists = unserialize($tag_synced_lists);

			if ( count($synced_lists) )
			{
				foreach ( $synced_lists as $synced_list )
				{
					$power_up_slug = $synced_list['esp'] . '_connector';  // e.g mailchimp_connector
					if ( WPInboundRocket::is_power_up_active($power_up_slug) )
					{
						global ${'inboundrocket_' . $power_up_slug . '_wp'}; // e.g inboundrocket_mailchimp_connector_wp
						${'inboundrocket_' . $power_up_slug . '_wp'}->bulk_push_contact_to_list($synced_list['list_id'], $contacts);
					}
				}
			}
		}
	}
}
?>