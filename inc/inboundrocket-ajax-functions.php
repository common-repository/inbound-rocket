<?php

if ( !defined('INBOUNDROCKET_PLUGIN_VERSION') )
{
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

/**
 * Inserts a new page view for a lead in ir_pageviews
 *
 * @return	int
 */
function inboundrocket_log_pageview ()
{
	global $wpdb,$blog_id;
	
	if ( ! defined('DOING_AJAX') || ! DOING_AJAX ) die('access denied');

	if ( inboundrocket_ignore_logged_in_user() ) die('Ignored');
	
	$hash 		= sanitize_text_field($_POST['ir_id']);
	$title 		= sanitize_text_field($_POST['ir_title']);
	$url 		= sanitize_text_field($_POST['ir_url']);
	$source 	= ( isset($_POST['ir_referrer']) ? sanitize_text_field($_POST['ir_referrer']) : '' );
	$last_visit = ( isset($_POST['ir_last_visit']) ? sanitize_text_field($_POST['ir_last_visit']) : 0 );

	$wpdb->insert(
	    $wpdb->ir_pageviews,
	    array(
	        'lead_hashkey' 				=> $hash,
	        'pageview_title' 			=> $title,
	      	'pageview_url' 				=> $url,
	      	'pageview_source' 			=> $source,
	      	'pageview_session_start' 	=> ( !$last_visit ? 1 : 0 ),
	      	'blog_id'					=> ( isset($blog_id) ? $blog_id : 0 )
	    ),
	    array(
	        '%s', '%s', '%s', '%s', '%d', '%d'
	    )
	);
	
	unset($hash,$title,$url,$source,$last_visit);

	echo $wpdb->insert_id;
	exit;
}

add_action('wp_ajax_inboundrocket_log_pageview', 'inboundrocket_log_pageview'); // Call when user logged in
add_action('wp_ajax_nopriv_inboundrocket_log_pageview', 'inboundrocket_log_pageview'); // Call when user is not logged in

/**
 * Inserts a new lead into ir_leads on first visit
 *
 * @return	int
 */
function inboundrocket_insert_lead ()
{
	global $wpdb;

	if ( ! defined('DOING_AJAX') || ! DOING_AJAX ) die('access denied');
	
	if ( inboundrocket_ignore_logged_in_user() ) die('Ignored');
	
	$return = false;
	$hashkey 	= sanitize_text_field($_POST['ir_id']);
	
	$query = $wpdb->prepare("SELECT * FROM {$wpdb->ir_leads} WHERE hashkey = %s",$hashkey);
	$result = $wpdb->get_row($query);
	
	if(!empty($result->lead_id)){
		echo $result->lead_id;
		exit;
	}
		
	if(!isset($hashkey) || empty($hashkey)){
		$return = true;
		$length = 16;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';
		for ($p = 0; $p < $length; $p++) {
       		$string .= $characters[mt_rand(0, strlen($characters))];
    	}
    	$hashkey = $string;
    	setcookie("ir_hash", $hashkey, time()+3600, "/");
	}
	
	$ipaddress 	= $_SERVER['REMOTE_ADDR'];
	$source 	= ( isset($_POST['ir_referrer']) ? sanitize_text_field($_POST['ir_referrer']) : '' );
	
	$wpdb->insert(
	    $wpdb->ir_leads,
	    array(
	        'hashkey' 		=> $hashkey,
	        'lead_ip' 		=> $ipaddress,
	      	'lead_source' 	=> $source
	    ),
	    array(
	        '%s', '%s', '%s'
	    )
	);
	
	echo $wpdb->insert_id;
	exit;
}

add_action('wp_ajax_inboundrocket_insert_lead', 'inboundrocket_insert_lead'); // Call when user logged in
add_action('wp_ajax_nopriv_inboundrocket_insert_lead', 'inboundrocket_insert_lead'); // Call when user is not logged in

/**
 * Inserts a new form submisison into the ir_submissions table and ties to the submission to a row in ir_leads
 *
 * @return	int
 */
function inboundrocket_insert_form_submission()
{
	global $wpdb;
	
	if ( ! defined('DOING_AJAX') || ! DOING_AJAX ) die("access denied");
	
	if ( inboundrocket_ignore_logged_in_user() ) die('Ignored');

	$hashkey 				= sanitize_text_field($_POST['ir_id']);
	$submission_hash 		= sanitize_text_field($_POST['ir_submission_id']);
	$page_title 			= sanitize_text_field($_POST['ir_title']);
	$page_url 				= sanitize_text_field($_POST['ir_url']);
	$form_json 				= sanitize_text_field($_POST['ir_fields']);
	$email 					= sanitize_text_field($_POST['ir_email']);
	$first_name 			= sanitize_text_field($_POST['ir_first_name']);
	$last_name 				= sanitize_text_field($_POST['ir_last_name']);
	$phone 					= sanitize_text_field($_POST['ir_phone']);
	$form_selector_id 		= sanitize_text_field($_POST['ir_form_selector_id']);
	$form_selector_classes 	= sanitize_text_field($_POST['ir_form_selector_classes']);
	$options 				= get_option('inboundrocket_options');
	$ir_admin_email 		= ( isset($options['ir_email']) ) ? $options['ir_email'] : '';
	$contact_type 			= 'leads'; // used at bottom of function
	
	// Check to see if the form_hashkey exists, and if it does, don't run the insert or send the email
	$submission_hash_exists = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT form_hashkey FROM {$wpdb->ir_submissions} WHERE form_hashkey = %s AND form_deleted = 0", 
			$submission_hash
		)
	);
	
	if ( $submission_hash_exists )
	{
		// The form has been inserted successful so send back a trigger to clear the cached submission cookie on the front end
		return 2;
		exit;
	}
	
	// Get the contact row tied to hashkey
	$contact = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->ir_leads} WHERE hashkey = %s AND lead_deleted = 0", 
			$hashkey
		)
	);
	
	// Check if either of the names field are set and a value was filled out that's different than the existing name field
	$lead_first_name = ( isset($contact->lead_first_name) ? $contact->lead_first_name : '' );
	if ( strlen($first_name) && $lead_first_name != $first_name )
		$lead_first_name = $first_name;
	
	$lead_last_name = ( isset($contact->lead_last_name) ? $contact->lead_last_name : '' );
	if ( strlen($last_name) && $lead_last_name != $last_name )
		$lead_last_name = $last_name;
	
	// Check for existing contacts based on whether the email is present in the contacts table
	$existing_contacts = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT lead_email, hashkey, merged_hashkeys FROM {$wpdb->ir_leads} WHERE lead_email = %s AND hashkey != %s AND lead_deleted = 0", 
			$email, $hashkey
		)
	);
	
	// Setup the string for the existing hashkeys
	$existing_contact_hashkeys = ( isset($contact->merged_hashkeys) ? $contact->merged_hashkeys : '' );
	if ( count($existing_contacts) > 0 && $contact->merged_hashkeys )
		$existing_contact_hashkeys .= ',';
	
	// Do some merging if the email exists already in the contact table
	if ( count($existing_contacts) )
	{
		for ( $i = 0; $i < count($existing_contacts); $i++ )
		{
			// Start with the existing contact's hashkeys and create a string containg comma-deliminated hashes
			$existing_contact_hashkeys .= "'" . $existing_contacts[$i]->hashkey . "'";
			// Add any of those existing contact row's merged hashkeys
			if ( $existing_contacts[$i]->merged_hashkeys )
				$existing_contact_hashkeys .= "," . $existing_contacts[$i]->merged_hashkeys;
			// Add a comma delimiter 
			if ( $i != count($existing_contacts)-1 )
				$existing_contact_hashkeys .= ",";
		}
		
		// Remove duplicates from the array
		$existing_contact_hashkeys = implode(',', array_unique(explode(',', $existing_contact_hashkeys)));
		
		// Safety precaution - trim any trailing commas
		$existing_contact_hashkeys = rtrim($existing_contact_hashkeys, ',');
		
		// Update all the previous pageviews to the new hashkey
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->ir_pageviews} SET lead_hashkey = %s WHERE lead_hashkey IN ( $existing_contact_hashkeys )", 
				$hashkey
			)
		);
		
		// Update all the previous submissions to the new hashkey
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->ir_submissions} SET lead_hashkey = %s WHERE lead_hashkey IN ( $existing_contact_hashkeys )", 
				$hashkey
			)
		);
		
		// Update all the previous submissions to the new hashkey
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->ir_tag_relationships} SET contact_hashkey = %s WHERE contact_hashkey IN ( $existing_contact_hashkeys )", 
				$hashkey
			)
		);
		
		// "Delete" all the old leads from the leads table
		$wpdb->query("UPDATE {$wpdb->ir_leads} SET lead_deleted = 1 WHERE hashkey IN ( $existing_contact_hashkeys )");
	}
	
	// Prevent duplicate form submission entries by deleting existing submissions if it didn't finish the process before the web page refreshed
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$wpdb->ir_submissions} SET form_deleted = 1 WHERE form_hashkey = %s", 
			$submission_hash
		)
	);
	
	// Insert the form fields and hash into the submissions table
	$result = $wpdb->insert(
	    $wpdb->ir_submissions,
	    array(
	        'form_hashkey' 			=> $submission_hash,
	        'lead_hashkey' 			=> $hashkey,
	        'form_page_title' 		=> $page_title,
	        'form_page_url' 		=> $page_url,
	        'form_fields' 			=> $form_json,
	        'form_selector_id' 		=> $form_selector_id,
	        'form_selector_classes' => $form_selector_classes
	    ),
	    array(
	        '%s', '%s', '%s', '%s', '%s', '%s', '%s'
	    )
	);
	
	// Update the contact with the new email, new names, status and merged hashkeys
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$wpdb->ir_leads} SET lead_email = %s, lead_first_name = %s, lead_last_name = %s, merged_hashkeys = %s WHERE hashkey = %s", 
			$email, $lead_first_name, $lead_last_name, $existing_contact_hashkeys, $hashkey
		)
	);
	
	// Apply the tag relationship to contacts for form id rules
	if ( !empty($form_selector_id))
	{
		
		$tagged_lists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT tag_id, tag_synced_lists FROM {$wpdb->ir_tags} WHERE tag_form_selectors LIKE '%%%s%%' AND tag_deleted = 0",
				'#' . $form_selector_id
			)
		);
		
		if ( count($tagged_lists) )
		{
			foreach ( $tagged_lists as $list )
			{
				$tag_added = inboundrocket_apply_list_to_contact($list->tag_id, $hashkey, $submission_hash);
				$contact_type = 'id tagged contact';
				
				if ( ( $tag_added && $list->tag_synced_lists ) )
				{
					foreach ( unserialize($list->tag_synced_lists) as $synced_list )
					{
						// e.g. inboundrocket_campaign_monitor_connector_wp
						$inboundrocket_esp_wp = 'inboundrocket_' . $synced_list['esp'] . '_connector_wp';
						global ${$inboundrocket_esp_wp};
						if ( isset(${$inboundrocket_esp_wp}->activated) && ${$inboundrocket_esp_wp}->activated )
						{
							//@TODO Postmatic doesn't have a list ID
							$data = array(
								'list_id' => $synced_list['list_id'],
								'email' => $email,
								'first_name' => $first_name,
								'last_name' => $last_name,
								'phone' => $phone
							);
							$status = ${$inboundrocket_esp_wp}->push_contact_to_list($data);
							if($status){
								inboundrocket_track_plugin_activity('Contact Pushed to List', array('list_id' => $synced_list['list_id'],'email' => $email));
							} else {
								inboundrocket_track_plugin_activity('Contact NOT Pushed to List', array('list_id' => $synced_list['list_id'],'email' => $email));
							}
						}
					}
				}
			}
		}
	}
	
	// Apply the tag relationship to contacts for class rules
	$form_classes = '';
	if ( strpos($form_selector_classes, ',')===true  )
		$form_classes = explode(',', $form_selector_classes);
	else 
		$form_classes = array($form_selector_classes);
	if ( is_array($form_classes) && count($form_classes) > 0 )
	{
		foreach ( $form_classes as $class )
		{
			$q = $wpdb->prepare("SELECT tag_id, tag_synced_lists FROM {$wpdb->ir_tags} WHERE tag_form_selectors LIKE '%%%s%%' AND tag_deleted = 0", '.' . $class);
			$tagged_lists = $wpdb->get_results($q);
			if ( count($tagged_lists) )
			{
				foreach ( $tagged_lists as $list )
				{
					$tag_added = inboundrocket_apply_list_to_contact($list->tag_id, $hashkey, $submission_hash);
					$contact_type = 'class tagged contact';
				
					if ( $tag_added && $list->tag_synced_lists )
					{
						foreach ( unserialize($list->tag_synced_lists) as $synced_list )
						{
							// e.g. inboundrocket_constant_contact_connect_wp
							$inboundrocket_esp_wp = 'inboundrocket_' . $synced_list['esp'] . '_connector_wp';
							global ${$inboundrocket_esp_wp};
							//@TODO Postmatic doesn't have a list ID
							if ( isset(${$inboundrocket_esp_wp}->activated) && ${$inboundrocket_esp_wp}->activated )
							{
								$data = array(
									'list_id' => $synced_list['list_id'],	
									'email' => $email,
									'first_name' => $first_name,
									'last_name' => $last_name,
									'phone' => $phone,
								);
								${$inboundrocket_esp_wp}->push_contact_to_list($data);
							}
						}
					}
				}
			}
		}
	}
	
	$ir_emailer = new IR_Notifier();
	
	if ( $ir_admin_email )
		$ir_emailer->send_new_lead_email($hashkey); // Send the contact notification email
		
	if ( strstr($form_selector_id, 'welcome_bar-form') )
	{
		// Send the subscription confirmation kickback email
		/*$inboundrocket_subscribe_settings = get_option('inboundrocket_subscribe_options');
		if ( isset($inboundrocket_subscribe_settings['ir_subscribe_confirmation']) && $inboundrocket_subscribe_settings['ir_subscribe_confirmation'] )
			$ir_emailer->send_subscriber_confirmation_email($hashkey);*/
		$contact_type = 'subscribers';
	}
	else if ( strstr($form_selector_id, 'commentform') )
		$contact_type = 'commenters';
		
	echo $contact_type;
	die();
}

add_action('wp_ajax_inboundrocket_insert_form_submission', 'inboundrocket_insert_form_submission');
add_action('wp_ajax_nopriv_inboundrocket_insert_form_submission', 'inboundrocket_insert_form_submission');

/**
 * Checks the lead status of the current visitor
 *
 */
function inboundrocket_check_visitor_status ()
{
	global $wpdb;
	
	if (!defined('DOING_AJAX')) die("Security");
	if (!wp_verify_nonce($_POST['ir_nonce'],'ir-nonce-verify')) die("Security");

	$hash 	= $_POST['ir_id'];

	// SELECT whether the hashkey is tied to the ir_tags list that is for the subscriber
	$results = $wpdb->get_var($wpdb->prepare("SELECT contact_hashkey FROM {$wpdb->ir_tag_relationships} ltr, {$wpdb->ir_tags} lt WHERE lt.tag_form_selectors LIKE '%%%s%%' AND lt.tag_id = ltr.tag_id AND ltr.contact_hashkey = %s AND lt.tag_deleted = 0", 'welcome_bar', $hash));

	if ( $results )
	{
		echo json_encode($results);
		die();
	}
	else
	{
		echo json_encode(FALSE);
		die();
	}
}

add_action('wp_ajax_inboundrocket_check_visitor_status', 'inboundrocket_check_visitor_status');
add_action('wp_ajax_nopriv_inboundrocket_check_visitor_status', 'inboundrocket_check_visitor_status');

/**
 * Gets post and pages (name + title) for contacts filtering
 *
 * @return	json object
 */
function inboundrocket_get_posts_and_pages()
{
	global $wpdb;
	
	if (!defined('DOING_AJAX')) die("Security");
	if (!wp_verify_nonce($_POST['ir_nonce'],'ir-admin-nonce')) die("Security");

	$search_term = $_POST['search_term'];
	
	if($search_term=='any-page') { unset($_POST['search_term']); unset($search_term); }

    $wp_posts = $wpdb->get_results($wpdb->prepare("SELECT post_name as id, post_title as text FROM {$wpdb->prefix}posts WHERE post_status = 'publish' AND post_type IN ('post','page') AND ( post_name LIKE '%%%s%%' OR post_title LIKE '%%%s%%' ) GROUP BY post_name ORDER BY post_date DESC", $search_term, $search_term));

    if ( ! $_POST['search_term'] )
    {
    	$obj_any_page = (Object) null;
    	$obj_any_page->id = 'any-page';
    	$obj_any_page->text = 'any page';
    	array_unshift($wp_posts, $obj_any_page);
    }

    echo json_encode($wp_posts);
    die();
}

add_action('wp_ajax_inboundrocket_get_posts_and_pages', 'inboundrocket_get_posts_and_pages'); // Call when user logged in
add_action('wp_ajax_nopriv_inboundrocket_get_posts_and_pages', 'inboundrocket_get_posts_and_pages'); // Call when user is not logged in


/**
 * Gets form css selectors (name + title) for contacts filtering
 *
 * @return	json object
 */
function inboundrocket_get_form_selectors()
{
	global $wpdb;
	
	if (!defined('DOING_AJAX')) die("Security");
	if (!wp_verify_nonce($_POST['ir_nonce'],'ir-admin-nonce')) die("Security");

	$search_term = $_POST['search_term'];
	
	if($search_term=='any-form') { unset($_POST['search_term']); unset($search_term); }

    $wp_posts = $wpdb->get_results($wpdb->prepare("SELECT tag_form_selectors as id, tag_form_selectors as text FROM {$wpdb->prefix}ir_tags WHERE tag_form_selectors != '' AND tag_deleted = '0' AND ( tag_form_selectors LIKE '%%%s%%' ) GROUP BY tag_form_selectors ORDER BY tag_order DESC", $search_term));

    if ( ! $_POST['search_term'] )
    {
    	$obj_any_page = (Object) null;
    	$obj_any_page->id = 'any-form';
    	$obj_any_page->text = 'any form';
    	array_unshift($wp_posts, $obj_any_page);
    }

    echo json_encode($wp_posts);
    die();
}

add_action('wp_ajax_inboundrocket_get_form_selectors', 'inboundrocket_get_form_selectors'); // Call when user logged in
add_action('wp_ajax_nopriv_inboundrocket_get_form_selectors', 'inboundrocket_get_form_selectors'); // Call when user is not logged in

?>