<?php
if ( !defined('ABSPATH') ) exit;

if ( !defined('INBOUNDROCKET_PLUGIN_VERSION') ) 
{
    header('HTTP/1.0 403 Forbidden');
    die;
}

/**
 * Cleans up pageview records based on visits
 *
 * @param   int
 * @return  boolean 
 */
function inboundrocket_clean_db ( $visits, $days ) 
{
	global $wpdb;
	
    if ( empty($visits) || empty($days) )
        return false;
	
	if ( $visits=="0" || $days=="0" )
        return false;
	
	$wpdb->query("OPTIMIZE TABLE {$wpdb->ir_pageviews}");
	
	$total_users = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT lead_hashkey FROM {$wpdb->ir_pageviews} WHERE pageview_date <= DATE_ADD(CURDATE(), INTERVAL -%d DAY) AND lead_hashkey IS NOT NULL GROUP BY lead_hashkey DESC",
			$days
		)
	);
	
    $cleads = $wpdb->get_results("SELECT lead_hashkey FROM {$wpdb->ir_submissions} GROUP BY lead_hashkey DESC");
	$converted_leads = array();
	foreach($cleads as $leads){
		$converted_leads[] = $leads->lead_hashkey;
	}
	
	if($total_users>0){
		foreach($total_users as $user){
			
			$hashkey = $user->lead_hashkey;
			
			// skip empty
			if(empty($hashkey)) continue;
			
			// do not remove converted leads
			if(in_array($hashkey, $converted_leads)) continue;
			
			$u = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT count(*) as total_visits FROM {$wpdb->ir_pageviews} WHERE lead_hashkey = '%s' GROUP BY lead_hashkey ORDER BY total_visits DESC",
					$hashkey
				)
			);
			
			if(isset($u->total_visits)):
			if($u->total_visits <= $visits){
				$wpdb->query(
					$wpdb->prepare(
						"DELETE FROM {$wpdb->ir_pageviews} WHERE lead_hashkey = '%s';",
						$hashkey
					)
				);
				$wpdb->query("OPTIMIZE TABLE {$wpdb->ir_pageviews}");
				
				$wpdb->query(
					$wpdb->prepare(
						"DELETE FROM {$wpdb->ir_leads} WHERE hashkey = '%s';",
						$hashkey
					)
				);
				$wpdb->query("OPTIMIZE TABLE {$wpdb->ir_leads}");
			}
			endif;
		}
	} else {
		return false;
	}
	
    return true;
}

/**
 * Prints a number with a singular or plural label depending on number
 *
 * @param   int
 * @param   string
 * @param   string
 * @return  string 
 */
function inboundrocket_single_plural_label ( $number, $singular_label, $plural_label ) 
{
    //Set number = 0 when the variable is blank
    $number = ( !is_numeric($number) ? 0 : $number );

    return ( $number != 1 ? $number . " $plural_label" : $number . " $singular_label" );
}
	
/**
 * Install Evercookie from Inbound Rocket API
 *
 * @return  boolean
 */
function inboundrocket_install_evercookie ()
{
	if(!is_dir(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/')){
		mkdir(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/');
	}
	
	if (!file_exists(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/evercookie/evercookie.php')) {
		$options = get_option('inboundrocket_options');
		$url = str_replace(array('https://','http://'),"",get_site_url());
		$ir_token = inboundrocket_get_token($url);
		$ir = new IR_Connect(INBOUNDROCKET_API);
		$resp = $ir->download_ec($ir_token);
		$access = $resp->access;
		$skip = false;
		if($access=='granted') {
			$payload = base64_decode($resp->payload);
			if(!file_put_contents(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/evercookie.zip', $payload)){
				$options['ir_evercookie_corrupted'] = 1;
				$options['ir_enable_evercookie'] = 0;
				$options['ir_enable_evercookie_status'] = 'disabled';
				$skip = true;
				inboundrocket_track_plugin_activity("Tracking Error: Evercookie not permitted.");	
			}
			$zip = new ZipArchive;
			if ($zip->open(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/evercookie.zip') === TRUE) {
				$zip->extractTo(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib');
				recurseChmod(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/evercookie', 0755, 0755);
				recurseRmdir(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/__MACOSX/');
				$zip->close();
				unlink(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/evercookie.zip');
				$options['ir_evercookie_corrupted'] = 0;
				$options['ir_enable_evercookie'] = 1;
				$options['ir_enable_evercookie_status'] = 'enabled';
				
				inboundrocket_track_plugin_activity("Tracking Method: Evercookie");	
			} else {
				$skip = true;
				$options['ir_evercookie_corrupted'] = 1;
				$options['ir_enable_evercookie'] = 0;
				$options['ir_enable_evercookie_status'] = 'disabled';
				inboundrocket_track_plugin_activity("Tracking Error: Evercookie zip missing");	
			}
		}
		if(inboundrocket_check_evercookie_integrity() && !$skip){
			$options['ir_enable_evercookie'] = 1;
			$options['ir_enable_evercookie_status'] = 'enabled';
		}
		inboundrocket_update_option('inboundrocket_options', $options);
	}
}

function inboundrocket_update_option( $option_name, $new_value )
{
	if ( get_option( $option_name ) !== false ) {
		// The option already exists, so we just update it.
		update_option( $option_name, $new_value );
	} else {
		// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
		$deprecated = null;
		$autoload = 'no';
		add_option( $option_name, $new_value, $deprecated, $autoload );
	}
}

/**
 * Remove Evercookie from Inbound Rocket
 *
 * @return  boolean
 */
function inboundrocket_remove_evercookie ()
{
	if (file_exists(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/evercookie/evercookie.php')) {
		recurseRmdir(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/evercookie/');
		inboundrocket_track_plugin_activity("Tracking Method: Evercookie disabled");
		inboundrocket_track_plugin_activity("Tracking Method: HTTP Cookies");		
	}
	$options = get_option('inboundrocket_options');
	$options['ir_evercookie_corrupted'] = 0;
	$options['ir_enable_evercookie'] = 0;
	$options['ir_enable_evercookie_status'] = 'disabled';
	inboundrocket_update_option('inboundrocket_options', $options);
}

/**
 * Check if all Evercookie files are there
 *
 * @return  boolean
 */
function inboundrocket_check_evercookie_integrity ()
{
	$options = get_option('inboundrocket_options');
	
	$ir_md5_file_array = array(
		"/inc/lib/evercookie/EvercookieCacheServlet.java" 														=> "9ec738ca90d1119310255e277958524a",
		"/inc/lib/evercookie/EvercookieEtagServlet.java"		 													=> "6c74f1e64e20c912228270737e0368ed",
		"/inc/lib/evercookie/EvercookiePngServlet.java" 															=> "c1d20de76c30cc169ad90eaf0115f572",
		"/inc/lib/evercookie/_cookie_name.php" 																	=> "4b30cae3f3fd751187a03c3c9d879775",
		"/inc/lib/evercookie/evercookie.fla" 																	=> "04ae36399853874e442d1d9342236fff",
		"/inc/lib/evercookie/evercookie.jar" 																	=> "46e6b02f61b1f1876faa31b3ff10065d",
		"/inc/lib/evercookie/evercookie.jnlp" 																	=> "fd17860bb0c10678b3a13187d096f0d8",
		"/inc/lib/evercookie/evercookie.php" 																	=> "d01f8391e9195876f1701c53ef2dd95f",
		"/inc/lib/evercookie/evercookie.swf" 																	=> "5dc1f3b459e096d8d45a1773c0901941",
		"/inc/lib/evercookie/evercookie.xap" 																	=> "1b5f71702a2d245719ef78322d47d295",
		"/inc/lib/evercookie/evercookie_cache.php" 																=> "c20464eb21351af01763e51a576d5c92",
		"/inc/lib/evercookie/evercookie_etag.php" 																=> "e89312a6cae1ac71cf900b42ecdfb11b",
		"/inc/lib/evercookie/evercookie_png.php" 																=> "cbbcdf7f275458757e09ed728f84d661",
		"/inc/lib/evercookie/evercookie_sl/evercookie/App.xaml" 													=> "da9cf0a12483aec98d289cb8ae43c138",
		"/inc/lib/evercookie/evercookie_sl/evercookie/App.xaml.cs" 												=> "0b3c37108cd4d43170c27c6a256224ef",
		"/inc/lib/evercookie/evercookie_sl/evercookie/Bin/Debug/AppManifest.xaml" 								=> "93ffe72c66be16e0912edeb13472c5f0",
		"/inc/lib/evercookie/evercookie_sl/evercookie/Bin/Debug/evercookie.dll" 									=> "010b62d25dac284fab172c7681530850",
		"/inc/lib/evercookie/evercookie_sl/evercookie/Bin/Debug/evercookie.pdb" 									=> "a2563f8debeb5a9519bec67b1cdff505",
		"/inc/lib/evercookie/evercookie_sl/evercookie/Bin/Debug/evercookie.xap" 									=> "1b5f71702a2d245719ef78322d47d295",
		"/inc/lib/evercookie/evercookie_sl/evercookie/Bin/Debug/evercookieTestPage.html" 						=> "aeef3299f108b7ffb6527edd7b45c776",
		"/inc/lib/evercookie/evercookie_sl/evercookie/evercookie.csproj" 										=> "63aadd9e6c3863da9ebf06f500131377",
		"/inc/lib/evercookie/evercookie_sl/evercookie/evercookie.csproj.user" 									=> "1412bcd5ce861315fb45484b91904731",
		"/inc/lib/evercookie/evercookie_sl/evercookie/MainPage.xaml" 											=> "4e5dab3bedf36ab72b4a4c05e6e763c5",
		"/inc/lib/evercookie/evercookie_sl/evercookie/MainPage.xaml.cs" 											=> "023b293466358a43db0292d4c90fe273",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/App.g.cs" 										=> "844c72c85df8c0c65bdada54c28232f3",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/App.g.i.cs" 										=> "844c72c85df8c0c65bdada54c28232f3",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/DesignTimeResolveAssemblyReferences.cache" 		=> "7940c9a6053b83df98aa7cada84ae0ee",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/DesignTimeResolveAssemblyReferencesInput.cache" 	=> "e39a6977815c9cb14b1136bae2bc12bf",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/evercookie.csproj.FileListAbsolute.txt" 			=> "54a94342cf4d0c91d303b145eddc360d",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/evercookie.dll" 									=> "010b62d25dac284fab172c7681530850",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/evercookie.g.resources" 							=> "3f5a40f8b9c8ec706afc32262ac9664f",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/evercookie.pdb" 									=> "a2563f8debeb5a9519bec67b1cdff505",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/MainPage.g.cs" 									=> "2ad368693f5aede7876ac6560d9e886e",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/MainPage.g.i.cs" 								=> "2ad368693f5aede7876ac6560d9e886e",
		"/inc/lib/evercookie/evercookie_sl/evercookie/obj/Debug/XapCacheFile.xml" 								=> "75fe3a972d01d456ae0e85de429c8b16",
		"/inc/lib/evercookie/evercookie_sl/evercookie/Properties/AppManifest.xml" 								=> "c286a47106cea314bc02fe96f343f9dd",
		"/inc/lib/evercookie/evercookie_sl/evercookie/Properties/AssemblyInfo.cs" 								=> "92726ec146fa0d7b5d20b732b059b794",
		"/inc/lib/evercookie/evercookie_sl/evercookie.sln" 														=> "2ff693c0206ec2c207814690c10c6b12",
		"/inc/lib/evercookie/evercookie_sl/evercookie.suo" 														=> "63e4a9dae51637b4ad3de8d13dac9b4f",
		"/inc/lib/evercookie/hsts_cookie.php" 																	=> "1967dbed29e7b0458388323e6839185b",
	);

	if(is_dir(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/evercookie/')){
		foreach($ir_md5_file_array as $file => $hash){
			if(file_exists(INBOUNDROCKET_PLUGIN_DIR.$file) && md5_file(INBOUNDROCKET_PLUGIN_DIR.$file)!=$hash){
				$options['ir_evercookie_corrupted'] = 1;
				$options['ir_enable_evercookie'] = 0;
				$options['ir_enable_evercookie_status'] = 'disabled';
				inboundrocket_update_option('inboundrocket_options', $options);
				return false;
			} elseif(!file_exists(INBOUNDROCKET_PLUGIN_DIR.$file)){
				$options['ir_evercookie_corrupted'] = 1;
				$options['ir_enable_evercookie'] = 0;
				$options['ir_enable_evercookie_status'] = 'disabled';
				inboundrocket_update_option('inboundrocket_options', $options);
				return false;
			}
		}
	} elseif(!file_exists(INBOUNDROCKET_PLUGIN_DIR.'/inc/lib/evercookie/evercookie.php')) {
		if(isset($options['ir_enable_evercookie']) && $options['ir_enable_evercookie']==1){
			$options['ir_evercookie_corrupted'] = 1;
			$options['ir_enable_evercookie'] = 0;
			$options['ir_enable_evercookie_status'] = 'disabled';
			inboundrocket_update_option('inboundrocket_options', $options);
		}
		return false;
	}
	return true;
}
	
/**
 * Get Inbound Rocket user
 *
 * @return  array
 */
function inboundrocket_get_current_user ()
{
    global $wp_version;
    
	if (version_compare($wp_version, '4.5', '>=')) {
		// version is 4.5 or higher
		$current_user = wp_get_current_user();
	} else { 
		$current_user = get_currentuserinfo();
	}
    
    $url = str_replace(array('https://','http://'),"",get_site_url());
	$ir_user_id = hash('sha1',$url.'ir_wp_hash2015');
	$ir_token = inboundrocket_get_token($url);
    $ir_options = get_option('inboundrocket_options');
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
	    $ip_address = $_SERVER['REMOTE_ADDR'];
	}
    
    $browser = $_SERVER['HTTP_USER_AGENT'];
    
    if ( isset($ir_options['ir_email']) ) {
        $ir_user_email = esc_attr($ir_options['ir_email']);
    } 
    else {
        $ir_user_email = $current_user->user_email;
    }
    
    $plugins = wp_get_active_and_valid_plugins();

    $inboundrocket_user = array(
	    'token' => $ir_token,
        'user_id' => $ir_user_id,
        'ip_address' => $ip_address,
        'browser' => $browser,
        'email' => $ir_user_email,
        'alias' => $current_user->display_name,
        'wp_url' => get_site_url(),
        'ir_version' => INBOUNDROCKET_PLUGIN_VERSION,
        'wp_version' => $wp_version,
        //'total_contacts' => get_total_contacts(),
        'wp_plugins' => $plugins,
    );

    return $inboundrocket_user;
}

/**
 * Gets the total number of contacts, comments and subscribers for above the table
 */
function get_total_contacts ()
{
    global $wpdb;

    if ( ! isset($wpdb->ir_leads) )
        return 0;

    $q = "SELECT COUNT(DISTINCT hashkey) AS total_contacts FROM {$wpdb->ir_leads} WHERE lead_email != '' AND lead_deleted = 0 AND hashkey != ''";

    $total_contacts = $wpdb->get_var($q);
    return $total_contacts;
}

/**
 * Register Inbound Rocket user
 *
 * @return  bool
 */
function inboundrocket_register_user()
{
    $inboundrocket_user = inboundrocket_get_current_user();

	if($inboundrocket_user['token']!=='Error'){
	    $data = array(
		    'site_token' => $inboundrocket_user['token'],
		    'email' => $inboundrocket_user['email'],
		    'alias' => $inboundrocket_user['alias'],
		    'ip_address' => $inboundrocket_user['ip_address'],
		    'browser' => $inboundrocket_user['browser'],
		    'wp_version' => $inboundrocket_user['wp_version'],
		    'ir_version' => $inboundrocket_user['ir_version'],
		    'wp_plugins' => $inboundrocket_user['wp_plugins']
	    );
	    $ir = new IR_Connect(INBOUNDROCKET_API);
	    $result = $ir->post($data, 'mixpanel/adduser');
	    
	    $options = get_option('inboundrocket_options');
	    
	    if(isset($options['ir_updates_subscription']) && $options['ir_updates_subscription']===1 && isset($options['ir_email'])):
		    if(strpos($options['ir_email'],",")===true){
			    $emails = explode(",", $options['ir_email']);
			    foreach($emails as $email){
			    	inboundrocket_mailchimp_subscribe( true, $email );
			    }
			    return true;
		    } else {
			    inboundrocket_mailchimp_subscribe( true, $options['ir_email'] );
			    return true;
		    }
		elseif(isset($options['ir_updates_subscription']) && $options['ir_updates_subscription']===0 && isset($options['ir_email'])):
			if(strpos($options['ir_email'],",")===true){
			    $emails = explode(",", $options['ir_email']);
			    foreach($emails as $email){
			    	inboundrocket_mailchimp_subscribe( false, $email );
			    }
			    return true;
		    } else {
			    inboundrocket_mailchimp_subscribe( false, $options['ir_email'] );
			    return true;
		    }
	    endif;
	    
	    if(isset($options['ir_updates_subscription']) && $options['ir_updates_subscription']=="1" && isset($inboundrocket_user['email'])):
	    	inboundrocket_mailchimp_subscribe( true );
			return true;
		elseif(isset($options['ir_updates_subscription']) && $options['ir_updates_subscription']=="0" && isset($inboundrocket_user['email'])):
			inboundrocket_mailchimp_subscribe( false );
			return true;
		endif;
	    
	    return true;
    } else {
	    return false;
    }
}


function inboundrocket_update_user()
{
    $inboundrocket_user = inboundrocket_get_current_user();

	if($inboundrocket_user['token']!=='Error'){
	    $data = array(
		    'site_token' => $inboundrocket_user['token'],
		    'email' => $inboundrocket_user['email'],
		    'alias' => $inboundrocket_user['alias'],
		    'ip_address' => $inboundrocket_user['ip_address'],
		    'browser' => $inboundrocket_user['browser'],
		    'wp_version' => $inboundrocket_user['wp_version'],
		    'ir_version' => $inboundrocket_user['ir_version'],
		    'wp_plugins' => $inboundrocket_user['wp_plugins']
	    );
	    $ir = new IR_Connect(INBOUNDROCKET_API);
	    $result = $ir->post($data, 'mixpanel/updateuser');
	    
	    inboundrocket_mailchimp_activate($inboundrocket_user['email']);
		return true;
	} else {
		return false;
	}
    
}

/**
 * Plugin Install update users parameters in MailChimp
 *
 * @return  bool
 *
 */
function inboundrocket_mailchimp_install($user_email)
{
	$inboundrocket_user = inboundrocket_get_current_user();
	
	// Sync to email to MailChimp
    $data = array(
	    'site_token' => $inboundrocket_user['token'],
	  	'method' => 'lists/subscribe',
	  	'parameters' => array(
	        "email"             => array('email' => $user_email),
	        "send_welcome"      => FALSE,
	        "email_type"        => 'html',
	        "update_existing"   => TRUE,
	        "replace_interests" => FALSE,
	        "double_optin"      => FALSE,
	        "merge_vars"        => array(
	        						'mc_location' => array(
	        							'anything'=>$inboundrocket_user['ip_address'])
										)
									)
	);
	
	$data['parameters']['merge_vars']['PREMIUM'] = 'Non-Premium'; // Non-Premium    or     Premium
	$data['parameters']['merge_vars']['SUBSCR_SRC'] = 'Plugin';
	$data['parameters']['merge_vars']['NEWSLETTER'] = 'Yes please!'; // Nope sorry :-/    or     Yes please!
	$data['parameters']['merge_vars']['PRODUCTUPD'] = 'Off course!'; // Rather not :-/    or     Off course!
    $data['parameters']['merge_vars']['VERSION'] = INBOUNDROCKET_PLUGIN_VERSION;
    $data['parameters']['merge_vars']['DEACTIVATE'] = 'No';
    $data['parameters']['merge_vars']['DELETED'] = 'No';
    $data['parameters']['merge_vars']['INSTALLED'] = 'Yes';
    $data['parameters']['merge_vars']['INSTALLURL'] = home_url();
    $data['parameters']['merge_vars']['INSTALLDAT'] = date('m/d/Y',strtotime("now"));
    
    $ir = new IR_Connect(INBOUNDROCKET_API);
    $result = $ir->post($data, 'mailchimp/parameters');

    inboundrocket_track_plugin_activity('Install Plugin - MailChimp update');
}

/**
 * Subscribe users in MailChimp
 *
 * @return  bool
 *
 */
function inboundrocket_mailchimp_subscribe($subscribe, $email=false)
{
	$inboundrocket_user = inboundrocket_get_current_user();
	
	$email = !$email ?  $inboundrocket_user['email'] : $email;
	
	// Sync to email to MailChimp
    $data = array(
		'site_token' => $inboundrocket_user['token'],
	  	'method' => 'lists/listid',
		'members' => array(
			'email'       		=> $email,
			'merge_fields'  	=> array(
				'mc_location' 	=> array(
					'anything' 	=> $inboundrocket_user['ip_address']
				)
			)
		)
	);
	
	if($subscribe){
		$data['members']['merge_fields']['PRODUCTUPD'] = 'Off course!';
	} elseif(!$subscribe){
		$data['members']['merge_fields']['PRODUCTUPD'] = 'Rather not :-/';
	}
	
	$data['members']['merge_fields']['VERSION'] = INBOUNDROCKET_PLUGIN_VERSION;
	
	$ir = new IR_Connect(INBOUNDROCKET_API);
    $result = $ir->post($data, 'mailchimp/parameters');
	
	inboundrocket_track_plugin_activity('Set Subscribe Parameters to "'.$data['members']['merge_fields']['PRODUCTUPD'].'"');
}

/**
 * Activated plugin update users parameters in MailChimp
 *
 * @return  bool
 *
 */
function inboundrocket_mailchimp_activate()
{
	$inboundrocket_user = inboundrocket_get_current_user();
	
	// Sync to email to MailChimp
    $data = array(
	    'site_token' => $inboundrocket_user['token'],
	  	'method' => 'lists/listid',
	  	'members' => array(
	        "email"             => $inboundrocket_user['email'],
	        "merge_fields"        => array(
	        						'mc_location' => array(
	        							'anything'=>$inboundrocket_user['ip_address'])
										)
									)
	);
	
	$data['members']['merge_fields']['VERSION'] = INBOUNDROCKET_PLUGIN_VERSION;
	$data['members']['merge_fields']['DEACTIVATE'] = 'No';
	
	$ir = new IR_Connect(INBOUNDROCKET_API);
    $result = $ir->post($data, 'mailchimp/parameters');
	
	inboundrocket_track_plugin_activity('Activated Plugin - MailChimp update');
}

/**
 * Deactivated plugin update users parameters in MailChimp
 *
 * @return  bool
 *
 */
function inboundrocket_mailchimp_deactivate()
{
	$inboundrocket_user = inboundrocket_get_current_user();
	
	// Sync to email to MailChimp
    $data = array(
	    'site_token' => $inboundrocket_user['token'],
	  	'method' => 'lists/listid',
	  	'members' => array(
	        "email"             => $inboundrocket_user['email'],
	        "merge_fields"        => array(
	        						'mc_location' => array(
	        							'anything'=>$inboundrocket_user['ip_address'])
										)
									)
	);
	
	$data['members']['merge_fields']['VERSION'] = INBOUNDROCKET_PLUGIN_VERSION;
	$data['members']['merge_fields']['DEACTIVATE'] = 'Yes';
	$data['members']['merge_fields']['DEACTDATE'] = date('m/d/Y',strtotime("now"));
	
	$ir = new IR_Connect(INBOUNDROCKET_API);
    $result = $ir->post($data, 'mailchimp/parameters');
	
	inboundrocket_track_plugin_activity('Deactivated Plugin - MailChimp update');
}

/**
 * Activated plugin update users parameters in MailChimp
 *
 * @return  bool
 *
 */
function inboundrocket_mailchimp_deleted()
{
	$inboundrocket_user = inboundrocket_get_current_user();
	
	// Sync to email to MailChimp
    $data = array(
	    'site_token' => $inboundrocket_user['token'],
	  	'method' => 'lists/listid',
		'subscribe' => 0,
	  	'members' => array(
	        "email"             => $inboundrocket_user['email'],
	        "merge_fields"        => array(
	        						'mc_location' => array(
	        							'anything'=>$inboundrocket_user['ip_address'])
										)
									)
	);
	
	$data['parameters']['merge_vars']['VERSION'] = INBOUNDROCKET_PLUGIN_VERSION;
	$data['parameters']['merge_vars']['DELETED'] = 'Yes';
	$data['parameters']['merge_vars']['INSTALLED'] = 'No';
	$data['parameters']['merge_vars']['DELETEDAT'] = date('m/d/Y',strtotime("now"));
	$data['parameters']['merge_vars']['PRODUCTUPD'] = 'Rather not :-/';
	
	$ir = new IR_Connect(INBOUNDROCKET_API);
    $result = $ir->post($data, 'mailchimp/parameters');
	
	inboundrocket_track_plugin_activity('Deleted Plugin - Update MailChimp Parameters');
}


/**
 * Marks user as deleted on MailChimp in MixPanel
 *
 * @return  bool
 *
 */
function inboundrocket_mark_deleted_user($user_email)
{
	$inboundrocket_user = inboundrocket_get_current_user();

	if($inboundrocket_user['token']!=='Error'){
	    $data = array(
		    'site_token' => $inboundrocket_user['token'],
		    'email' => $inboundrocket_user['email'],
		    'alias' => $inboundrocket_user['alias'],
		    'ip_address' => $inboundrocket_user['ip_address'],
		    'browser' => $inboundrocket_user['browser'],
		    'wp_version' => $inboundrocket_user['wp_version'],
		    'ir_version' => $inboundrocket_user['ir_version'],
		    'wp_plugins' => $inboundrocket_user['wp_plugins']
	    );
	    $ir = new IR_Connect(INBOUNDROCKET_API);
	    $result = $ir->post($data, 'mixpanel/updateuser');
		
		inboundrocket_mailchimp_deleted($user_email);
	
	    return $result;
		
	} else {
		return false;
	}
	
	
}

/**
 * Set Beta propertey on Inbound Rocket user in Mixpanel
 *
 * @return  bool
 */
function inboundrocket_set_beta_tester_property ( $beta_tester )
{
	$inboundrocket_user = inboundrocket_get_current_user();
	
	$properties = array(
		'$beta_tester' => $beta_tester,
	);
	
	if($inboundrocket_user['token']!=='Error'){
	    $data = array(
		    'email'			=> $inboundrocket_user['email'],
		    'site_token' 		=> $inboundrocket_user['token'],
			'ip_address' => $inboundrocket_user['ip_address'],
		    'browser' => $inboundrocket_user['browser'],
		    'wp_version' => $inboundrocket_user['wp_version'],
		    'ir_version' => $inboundrocket_user['ir_version'],
			'properties' => $properties
	    );
	    $ir = new IR_Connect(INBOUNDROCKET_API);
	    $result = $ir->post($data, 'mixpanel/properties');
		return true;
	} else {
		return false;
	}
}

/**
 * Set Premium property on Inbound Rocket user in Mixpanel
 *
 * @return  bool
 */
function inboundrocket_set_premium_user_property ( $premium_user )
{
    $inboundrocket_user = inboundrocket_get_current_user();

	$properties = array(
		'$premium_user' => $premium_user,
		'email' => $inboundrocket_user['email'],
		'$ip_address' => $inboundrocket_user['ip_address'],
		'$browser' => $inboundrocket_user['browser'],
		'$wp_version' => $inboundrocket_user['wp_version'],
		'$ir_version' => $inboundrocket_user['ir_version'],
	);

	if($inboundrocket_user['token']!=='Error'){
	    $data = array(
		    'token' 		=> $inboundrocket_user['token'],
		    'properties' 	=> $properties
	    );
	    $ir = new IR_Connect(INBOUNDROCKET_API);
	    $result = $ir->post($data, 'mixpanel/properties');
		return true;
	} else {
		return false;
	}
}

/**
 * Set the status property (activated, deactivated, bad url)
 *
 * @return  bool
 */
function inboundrocket_set_install_status ( $ir_status )
{
	$inboundrocket_user = inboundrocket_get_current_user();

	$properties = array(
		'$ir-status' => $ir_status,
		'$wp_version' => $inboundrocket_user['wp_version'],
	    '$ir_version' => $inboundrocket_user['ir_version'],
	);

	if($inboundrocket_user['token']!=='Error'){
	    $data = array(
		    'site_token' => $inboundrocket_user['token'],
		    'email' => $inboundrocket_user['email'],
			'ip_address' => $inboundrocket_user['ip_address'],
		    'browser' => $inboundrocket_user['browser'],
		    'properties' => $properties
	    );
	    
	    if ( $ir_status == 'activated' )
        	$data['last_activated'] = date('Y-m-d H:i:s'); 
		else
        	$data['last_deactivated'] = date('Y-m-d H:i:s');
        	
	    $ir = new IR_Connect(INBOUNDROCKET_API);
	    $result = $ir->post($data, 'mixpanel/properties');
		return true;
	} else {
		return false;
	}
}

/**
 * Send Mixpanel event when plugin is activated/deactivated
 *
 * @param   bool
 *
 * @return  bool
 */
function inboundrocket_track_plugin_registration_hook ( $activated )
{
    if ( $activated )
    {
        inboundrocket_track_plugin_activity("Activated Plugin");
        inboundrocket_set_install_status('activated');
    }
    else
    {
        inboundrocket_track_plugin_activity("Deactivated Plugin");
        inboundrocket_set_install_status('deactivated');
    }

    return TRUE;
}

/**
 * Returns the Authorization Token from the API
 *
 * @param   string
 *
 * @return  string
 */
function inboundrocket_get_token ( $url )
{
	$ir = new IR_Connect(INBOUNDROCKET_API);
	$url = base64_encode($url);
	$data = $ir->auth($url);
	return isset($data->token) ? $data->token : 'Error';
}

/**
 * Track plugin activity in MixPanel
 *
 * @param   string
 *
 * @return  array
 */
function inboundrocket_track_plugin_activity ( $activity_desc, $custom_properties = array() )
{   
	$inboundrocket_user = inboundrocket_get_current_user();
	
	if(!empty($inboundrocket_user['token'])){
	    $data = array(
		    'site_token' => $inboundrocket_user['token'],
		    'alias' => $inboundrocket_user['alias'],
		    'email' => $inboundrocket_user['email'],
		    'ip_address' => $inboundrocket_user['ip_address'],
		    'browser' => $inboundrocket_user['browser'],
		    'properties' => $custom_properties,
		    'activity' => $activity_desc,
		    'wp_version' => $inboundrocket_user['wp_version'],
		    'ir_version' => $inboundrocket_user['ir_version'],
		    'wp_plugins' => $inboundrocket_user['wp_plugins']
	    );
	    $ir = new IR_Connect(INBOUNDROCKET_API);
	    $result = $ir->post($data, 'mixpanel/activity');
	    return true;
    } else {
	    return false;
    }
}

/**
 * Logs a debug statement to /wp-content/debug.log
 *
 * @param   string
 */
function inboundrocket_log_debug ( $message )
{
    if ( WP_DEBUG === TRUE )
    {
        if ( is_array($message) || is_object($message) )
            error_log(print_r($message, TRUE));
        else 
            error_log($message);
    }
}

/**
 * Deletes an element or elements from an array
 *
 * @param   array
 * @param   wildcard
 * @return  array
 */
function inboundrocket_array_delete ( $array, $element )
{
    if ( !is_array($element) )
        $element = array($element);

    return array_diff($array, $element);
}

/**
 * Sorts the powerups into a predefined order in class-inboundrocket.php line 201
 *
 * @param   array
 * @param   array
 * @return  array
 */
function inboundrocket_sort_power_ups ( $power_ups, $ordered_power_ups ) 
{ 
    $ordered = array();
    $i = 0;
    foreach ( $ordered_power_ups as $key )
    {
        if ( in_array($key, $power_ups) )
        {
            array_push($ordered, $key);
            $i++;
        }
    }

    return $ordered;
}

/**
 * Deletes an element or elements from an array
 *
 * @param   array
 * @param   wildcard
 * @return  array
 */
function inboundrocket_get_value_by_key ( $key_value, $array )
{
    foreach ( $array as $key => $value )
    {
        if ( is_array($value) && $value['label'] == $key_value )
            return $value['value'];
    }

    return null;
}

/**
 * Encodes special HTML quote characters into utf-8 safe entities
 *
 * @param   string
 * @return  string
 */
function inboundrocket_user_encode_quotes ( $string ) 
{ 
    $string = str_replace(array("’", "‘", '&#039;', '“', '”'), array("'", "'", "'", '"', '"'), $string);
    return $string;
}

/**
 * Converts all carriage returns into HTML line breaks 
 *
 * @param   string
 * @return  string
 */
function inboundrocket_html_line_breaks ( $string ) 
{
    return stripslashes(str_replace('\n', '<br>', $string));
}

/**
 * Strip url get parameters off a url and return the base url
 *
 * @param   string
 * @return  string
 */
function inboundrocket_strip_params_from_url ( $url ) 
{ 
    $url_parts = parse_url($url);
    $base_url = ( isset($url_parts['host']) ? 'http://' . rtrim($url_parts['host'], '/') : '' ); 
    $base_url .= ( isset($url_parts['path']) ? '/' . ltrim($url_parts['path'], '/') : '' ); 
    
    if ( isset($url_parts['path'] ) )
        ltrim($url_parts['path'], '/');

    $base_url = urldecode(ltrim($base_url, '/'));

    return strtolower($base_url);
}

/**
 * Search an object by for a value and return the associated index key
 *
 * @param   object 
 * @param   string
 * @param   string
 * @return  key for array index if present, false otherwise
 */
function inboundrocket_search_object_by_value ( $haystack, $needle, $search_key )
{
   foreach ( $haystack as $key => $value )
   {
      if ( $value->$search_key === $needle )
         return $key;
   }

   return FALSE;
}

/**
 * Check if date is a weekend day
 *
 * @param   string
 * @return  bool
 */
function inboundrocket_is_weekend ( $date )
{
    return (date('N', strtotime($date)) >= 6);
}

/**
 * Tie a tag to a contact in ir_tag_relationships
 *
 * @param   int 
 * @param   int
 * @param   int
 * @return  bool    successful insert
 */
function inboundrocket_apply_list_to_contact ( $list_id, $contact_hashkey, $form_hashkey )
{
    global $wpdb;
	
    $exists = $wpdb->get_var(
	    $wpdb->prepare(
	    	"SELECT tag_id FROM {$wpdb->ir_tag_relationships} WHERE tag_id = %d AND contact_hashkey = %s",
	    	$list_id, $contact_hashkey
	    )
    );

    if ( ! $exists )
    {
	    $wpdb->query(
        	$wpdb->prepare(
        		"INSERT INTO {$wpdb->ir_tag_relationships} ( tag_id, contact_hashkey, form_hashkey ) VALUES ( %d, %s, %s )",
        		$list_id, $contact_hashkey, $form_hashkey
        	)
        );
        return true;
    }
    
    return false;
}

/**
 * Check multidimensional arrray for an existing value
 *
 * @param   string 
 * @param   array
 * @return  bool
 */
function inboundrocket_in_array_deep ( $needle, $haystack ) 
{
    if ( in_array($needle, $haystack) )
        return TRUE;

    foreach ( $haystack as $element ) 
    {
        if ( is_array($element) && inboundrocket_in_array_deep($needle, $element) )
            return TRUE;
    }

    return FALSE;
}

/**
 * Check multidimensional arrray for an existing value
 *
 * @param   string      needle 
 * @param   array       haystack
 * @return  string      key if found, null if not
 */
function inboundrocket_array_search_deep ( $needle, $array, $index ) 
{
    foreach ( $array as $key => $val ) 
    {
        if ( $val[$index] == $needle )
            return $key;
    }

   return NULL;
}

/**
 * Creates a list of filtered contacts into a comma separated string of hashkeys
 * 
 * @param object
 * @return string    sorted array
 */
function inboundrocket_merge_filtered_contacts ( $filtered_contacts, $all_contacts = array() )
{
    if ( ! count($all_contacts) )
        return $filtered_contacts;

    if ( count($filtered_contacts) )
    {
        foreach ( $all_contacts as $key => $contact )
        {
            if ( ! inboundrocket_in_array_deep($contact['lead_hashkey'], $filtered_contacts) )
                unset($all_contacts[$key]);
        }

        return $all_contacts;
    }
    else
        return FALSE;
}

/**
 * Creates a list of filtered contacts into a comma separated string of hashkeys
 * 
 * @param object
 * @return string    sorted array
 */
function inboundrocket_explode_filtered_contacts ( $contacts )
{
    if ( count($contacts) )
    {
        $contacts = array_values($contacts);

        $hashkeys = '';
        for ( $i = 0; $i < count($contacts); $i++ )
            $hashkeys .= "'" . $contacts[$i]['lead_hashkey'] . "'" . ( $i != (count($contacts) - 1) ? ', ' : '' );

        return $hashkeys;
    }
    else
        return FALSE;
}

/**
 * Calculates the hour difference between MySQL timestamps and the current local WordPress time
 * 
 */
function inboundrocket_set_mysql_timezone_offset ()
{
    global $wpdb;

    $mysql_timestamp = $wpdb->get_var("SELECT CURRENT_TIMESTAMP");
    $diff = strtotime($mysql_timestamp) - strtotime(current_time('mysql'));
    $hours = $diff / (60 * 60);

    $wpdb->db_hour_offset = $hours;
}

/**
 * Gets current URL with parameters
 * 
 */
function inboundrocket_get_current_url ( )
{
    return ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Gets current IP address
 * 
 */
function inboundrocket_get_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * Returns the user role for the current user
 * 
 */
function inboundrocket_get_user_role()
{
    global $wp_version;
        
    if (version_compare($wp_version, '4.5', '>=')) {
		// version is 4.5 or higher
		$current_user = wp_get_current_user();
	} else { 
		$current_user = get_currentuserinfo();
	}
	
	if(empty($current_user)) return;
	
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);

    return $user_role;
}

/**
 * Checks whether or not to ignore the logged in user in the Inbound Rocket tracking scripts
 * 
 */
function inboundrocket_ignore_logged_in_user()
{
    // ignore logged in users if defined in settings
    if ( is_user_logged_in() )
    {
	    $options = get_option('inboundrocket_options');
	    if ( array_key_exists('ir_do_not_track_' . inboundrocket_get_user_role(), $options) )
            return TRUE;
        else 
            return FALSE;
    }
    else
        return FALSE;
}

function inboundrocket_safe_social_profile_url($url)
{
    $url = str_replace('∖', '/', $url);
    return $url;
}

/**
 * Checks to see if an installation is Inbound Rocket Premium enabled
 * 
 * @return bool
 */
function inboundrocket_check_premium_user()
{
    $options = get_option('inboundrocket_options');
    if ( isset($options['premium']) && ($options['premium']=='1'))
        return TRUE;
    else
        return FALSE;
}

/**
 * Checks the first entry in the pageviews table 
 *
 */
function inboundrocket_check_first_pageview_data()
{
    global $wpdb;

    $q = "SELECT pageview_date FROM {$wpdb->ir_pageviews} ORDER BY pageview_date ASC LIMIT 1";
    $date = $wpdb->get_var($q);

    if ( $date )
    {
        if ( strtotime($date) < strtotime('-30 days') )
            return TRUE;
        else
            return FALSE;
    }
    else
    {
       return FALSE;
    }
}

/**
 * searches multidimensional CM array for existing emails
 *
 */
function in_multiarray($elem, $array)
{
	foreach($array as $arr){
		if($arr['EmailAddress']===$elem) return true;
	}
	return false;
}

/**
 * recursive directory removal
 *
 */
function recurseRmdir($dir) {
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (is_dir($dir."/".$object))
           recurseRmdir($dir."/".$object);
         else
           unlink($dir."/".$object); 
       } 
     }
     rmdir($dir); 
   } 
 }

/**
 * recursive directory permissions
 *
 */
function recurseChmod($dir, $dirPermissions, $filePermissions) {
      $dp = opendir($dir);
       while($file = readdir($dp)) {
         if (($file == ".") || ($file == ".."))
            continue;

        $fullPath = $dir."/".$file;

         if(is_dir($fullPath)) {
            chmod($fullPath, $dirPermissions);
            recurseChmod($fullPath, $dirPermissions, $filePermissions);
         } else {
            chmod($fullPath, $filePermissions);
         }

       }
     closedir($dp);
  }
?>