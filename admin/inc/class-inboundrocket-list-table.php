<?php
if ( !defined('ABSPATH') ) exit;

//=============================================
// Include Needed Files
//=============================================
if ( !class_exists('WP_List_Table') )
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

//=============================================
// IR_List_Table Class
//=============================================
class IR_List_Table extends WP_List_Table {
    
    /**
     * Variables
     */
    public $data = array();
    private $current_view;
    public $view_label;
    private $view_count;
    private $views;
    private $total_contacts;
    private $total_filtered;
    public $tags;

    /**
     * Class constructor
     */
    function __construct () 
    {
        global $status, $page;
                
        //Set parent defaults
        parent::__construct(array(
            'singular'  => 'contact',
            'plural'    => 'contacts',
            'ajax'      => false
        ));
    }
    
    function print_submission_val ( $url_param )
	{
		if ( isset($_GET[$url_param]) )
		{
			return esc_url($_GET[$url_param]);
		}
		if ( isset($_POST[$url_param]) )
		{
			return sanitize_text_field($_POST[$url_param]);
		}
		return '';
	}

    /**
     * Prints text for no rows found in table
     */
    function no_items () 
    {
      _e('No contacts found.');
    }
    
    /**
     * Prints values for columns for which no column function has been defined
     *
     * @param   object
     * @param   string
     * @return  *           item value's type
     */
    function column_default ( $item, $column_name )
    {
        switch ( $column_name ) 
        {
            case 'email':
            case 'date':
            case 'last_visit':
            case 'submissions':
            case 'pageviews':
            case 'shares':
            case 'visits':
            case 'source':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }
    
    /**
     * Prints text for email column
     *
     * @param   object
     * @return  string
     */
    function column_email ( $item )
    {
        //Build row actions
        $actions = array(
            'view'    => sprintf('<div style="clear:both; padding-top: 4px;"></div><a href="?page=%s&action=%s&lead=%s">'. __('View', 'inbound-rocket').'</a>',esc_attr($_REQUEST['page']),'view',$item['ID']),
            'delete'  => sprintf('<a href="?page=%s&action=%s&lead=%s">'.__('Delete','inbound-rocket').'</a>',esc_attr($_REQUEST['page']),'delete',$item['ID'])
        );
        
        //Return the title contents
        return sprintf('%1$s<br/>%2$s',
            /*$1%s*/ $item['email'],
            /*$2%s*/ $this->row_actions($actions)
        );
    }
    
    /**
     * Prints checkbox column
     *
     * @param   object
     * @return  string
     */
    function column_cb ( $item )
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],
            /*$2%s*/ $item['ID']
        );
    }
    
    /**
     * Get all the columns for the list table
     *
     * @param   object
     * @param   string
     * @return  array           associative array of columns
     */
    function get_columns () 
    {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'email'         => __('Email','inbound-rocket'),
            'source'        => __('Original source','inbound-rocket'),
            'visits'        => __('Visits','inbound-rocket'),
            'pageviews'     => __('Page views','inbound-rocket'),
            'submissions'   => __('Forms','inbound-rocket'),
            'shares'		=> __('Shares','inbound-rocket'),
            'last_visit'    => __('Last visit','inbound-rocket'),
            'date'          => __('Created on','inbound-rocket')
        );
        return $columns;
    }
    
    /**
     * Defines sortable columns for table
     *
     * @param   object
     * @param   string
     * @return  array           associative array of columns
     */
    function get_sortable_columns () 
    {
        $sortable_columns = array(
            'email'         => array('email',false), // presorted if true
            'pageviews'     => array('pageviews',false),
            'visits'        => array('visits',false),
            'submissions'   => array('submissions',false),
            'shares'		=> array('shares',true),
            'date'          => array('date',true),
            'last_visit'    => array('last_visit',false),
            'source'        => array('source',false)
        );
        return $sortable_columns;
    }
    
    /**
     * Get the bulk actions
     *
     * @return  array           associative array of actions
     */
    function get_bulk_actions ()
    {
        $contact_type   = strtolower($this->view_label);
        $filtered       =  ( isset($_GET['filter_action']) ? __('filtered','inbound-rocket') : '' );
        $actions = array(
            'add_list_to_all'            => __('Add everyone tagged to a list','inbound-rocket').' "' . $filtered .' '. $contact_type . '"',
            'add_list_to_selected'       => __('Add selected to a list','inbound-rocket').' "' . $contact_type,
            'remove_list_from_all'       => __('Remove everyone tagged from a list','inbound-rocket').' "' . $filtered .' '. $contact_type . '"',
            'remove_list_from_selected'  => __('Remove selected from a list','inbound-rocket').' "' . $contact_type . '"',
            'delete_all'                 => __('Delete every one tagged','inbound-rocket').' "' . $contact_type . '" '.__('from Inbound Rocket','inbound-rocket'),
            'delete_selected'            => __('Delete selected','inbound-rocket').' ' . $contact_type . ' '.__('from Inbound Rocket','inbound-rocket')
        );

        return $actions;
    }
    
    /**
     * Process bulk actions for deleting
     */
    function process_bulk_action ()
    {
        global $wpdb;

        $ids_for_action = '';
        $hashes_for_action = '';

        //@TODO Fix the delete logic
        if ( strstr($this->current_action(), 'delete') )
        {
            if ( 'delete' === $this->current_action() )
            {
	            $ids_for_action = $_GET['lead'];
                
            } else if ( 'delete_all' === $this->current_action() ) {
                $contacts = $this->get_contacts();
                foreach ( $contacts as $contact )
                    $ids_for_action .= $contact['ID'] . ',';

                $ids_for_action = rtrim($ids_for_action, ',');
            }
			
			if( !empty($ids_for_action) )
			{
            	$q = $wpdb->prepare("SELECT hashkey FROM {$wpdb->ir_leads} WHERE lead_id IN ( " . $ids_for_action . " ) ", "");
            	$hashes = $wpdb->get_results($q);
			} else {
				$hashes = false;
			}
			
			
			
            if ( $hashes && count($hashes)>0 )
            {
                foreach ( $hashes as $hash )
                    $hashes_for_action .= "'" . $hash->hashkey . "',";

                $hashes_for_action = rtrim($hashes_for_action, ',');

                $q = $wpdb->prepare("UPDATE {$wpdb->ir_pageviews} SET pageview_deleted  = 1 WHERE lead_hashkey IN (" . $hashes_for_action . ") ", "");
                $delete_pageviews = $wpdb->query($q);

                $q = $wpdb->prepare("UPDATE {$wpdb->ir_submissions} SET form_deleted  = 1 WHERE lead_hashkey IN (" . $hashes_for_action . ") ", "");
                $delete_submissions = $wpdb->query($q);

                $q = $wpdb->prepare("UPDATE {$wpdb->ir_leads} SET lead_deleted  = 1 WHERE lead_id IN (" . $ids_for_action . ") ", "");
                $delete_leads = $wpdb->query($q);
                
                $q = $wpdb->prepare("UPDATE {$wpdb->ir_tag_relationships} SET tag_relationship_deleted = 1 WHERE contact_hashkey IN (" . $hashes_for_action . ") ", "");
                $delete_tags = $wpdb->query($q);
            }
        }
        
        if ( isset($_POST['bulk_edit_lists']) )
        {
            $q = $wpdb->prepare("SELECT tag_id FROM {$wpdb->ir_tags} WHERE tag_slug = %s", $_POST['bulk_selected_tag']);
            $list_id = $wpdb->get_var($q);

            if ( empty($_POST['inboundrocket_selected_contacts']) )
            {
                $contacts = $this->get_contacts();
                foreach ( $contacts as $contact )
                    $ids_for_action .= $contact['ID'] . ',';

                $ids_for_action = rtrim($ids_for_action, ',');
            }
            else
                $ids_for_action = $_POST['inboundrocket_selected_contacts'];

            $q = $wpdb->prepare("
                SELECT 
                    l.hashkey, l.lead_email,
                    ( SELECT ltr.tag_id FROM {$wpdb->ir_tag_relationships} ltr WHERE ltr.tag_id = %d AND ltr.contact_hashkey = l.hashkey GROUP BY ltr.contact_hashkey ) AS tag_set, ( SELECT tsl.tag_synced_lists FROM {$wpdv->ir_tags} tsl WHERE tsl.tag_id = %d ) AS tag_synced_lists 
                FROM 
                    {$wpdb->ir_leads} l
                WHERE 
                    l.lead_id IN ( " . $ids_for_action . " ) AND l.lead_deleted = 0 GROUP BY l.lead_id", $list_id, $list_id);

            $contacts = $wpdb->get_results($q);

            $insert_values          = '';
            $contacts_to_update     = '';

            if ( count($contacts) )
            {
                foreach ( $contacts as $contact )
                {
                    if ( $contact->tag_set === NULL )
                       $insert_values .= '(' . $list_id . ', "' . $contact->hashkey . '"),';
                    else
                        $contacts_to_update .= "'" . $contact->hashkey . "',";
                }
            }

            if ( $_POST['bulk_edit_list_action'] === 'add_list' )
            {
                if ( $insert_values )
                {
                    $q = "INSERT INTO {$wpdb->ir_tag_relationships} ( tag_id, contact_hashkey ) VALUES " . rtrim($insert_values, ',');
                    $wpdb->query($q);
                }

                if ( $contacts_to_update )
                {
                    // update the relationships for the contacts that exist already making sure to set all the tag_relationship_deleted = 0
                    $q = $wpdb->prepare("UPDATE {$wpdb->ir_tag_relationships} SET tag_relationship_deleted = 0 WHERE tag_id = %d AND contact_hashkey IN ( " . rtrim($contacts_to_update, ',')  . ") ", $list_id);
                    $wpdb->query($q);
                }

                // Bulk push all the email addresses for the tag to the MailChimp API
                $tagger = new IR_Lead_List_Editor($list_id);
                $tagger->push_contacts_to_lead_list($list_id);
            }
            else
            {
                if ( $contacts_to_update )
                {
                    // "Delete" the existing tags only
                    $q = $wpdb->prepare("UPDATE {$wpdb->ir_tag_relationships} SET tag_relationship_deleted = 1 WHERE tag_id = %d AND contact_hashkey IN ( " . rtrim($contacts_to_update, ',')  . ") ", $list_id);
                    $wpdb->query($q);
                }
            }
        }
    }

    /**
     * Get the leads for the contacts table based on $GET_['contact_type'] or $_GET['s'] (search)
     *
     * @return  array           associative array of all contacts
     */
    function get_contacts ()
    {
        /*** 
            == FILTER ARGS ==
            - filter_action (visited)      = visited a specific page url (filter_action) 
            - filter_action (submitted)    = submitted a form on specific page url (filter_action) 
            - filter_content               = content for filter_action
            - filter_form                  = selector id/class
            - num_pageviews                = visited at least #n pages
            - s                            = search query on lead_email/lead_source
        */

        global $wpdb;

        $mysql_search_filter        = '';
        $mysql_contact_type_filter  = '';
        $mysql_action_filter        = '';
        $filter_action_set          = FALSE;
        
        $contact_type = isset($_GET['contact_type']) ? esc_attr($_GET['contact_type']) : 'all';
        $filter_action = isset($_GET['filter_action']) ? esc_attr($_GET['filter_action']) : 'visited';
		$filter_content = isset($_GET['filter_content']) ? esc_attr($_GET['filter_content']) : 'any-page';
        $s = isset($_GET['s']) ? esc_attr($_GET['s']) : NULL;
		$filter_form = isset($_GET['filter_form']) ? esc_attr($_GET['filter_form']) : 'any-form';

        // search filter
        if ( !empty($s) )
        {
	        $wp_version = get_bloginfo('version');
            $escaped_query = '';
            if ( $wp_version >= 4 )
                $escaped_query = $wpdb->esc_like($s);
            else
                $escaped_query = @like_escape($s);

            $mysql_search_filter = $wpdb->prepare(" AND ( l.lead_email LIKE '%%%s%%' OR l.lead_source LIKE '%%%s%%' ) ", $escaped_query, $escaped_query);

            inboundrocket_track_plugin_activity('Filtered contacts - search term: '.$s);
        }
        
        $filtered_contacts = array();

        // contact type filter
        if ( !empty($contact_type) && $contact_type !== 'all' )
        {
            // Query for the list_id, then find all hashkeys with that tag ID tied to them. Use those hashkeys to modify the query
            $q = $wpdb->prepare("SELECT 
                    DISTINCT ltr.contact_hashkey as lead_hashkey 
                FROM 
                    {$wpdb->ir_tag_relationships} ltr, {$wpdb->ir_tags} lt 
                WHERE 
                    lt.tag_id = ltr.tag_id AND 
                    ltr.tag_relationship_deleted = 0 AND  
                    lt.tag_slug = %s GROUP BY ltr.contact_hashkey",  $contact_type);

            $filtered_contacts = $wpdb->get_results($q, 'ARRAY_A');
            $num_contacts = count($filtered_contacts);
        } else {
	        $q = "SELECT 
                    DISTINCT hashkey as lead_hashkey 
                FROM 
                    {$wpdb->ir_leads}
                WHERE
                	lead_email IS NOT NULL
                GROUP BY hashkey";

            $nonfiltered_contacts = $wpdb->get_results($q, 'ARRAY_A');
            $num_contacts = count($nonfiltered_contacts);
        }

        if ( !empty($filter_action) && $filter_action === 'visited' )
        {
            if ( !empty($filter_content) && $filter_content !== 'any-page' )
            {
                $q = $wpdb->prepare("SELECT lead_hashkey FROM {$wpdb->ir_pageviews} WHERE pageview_title LIKE '%%%s%%' GROUP BY lead_hashkey",  $filter_content);
                $filtered_contacts = inboundrocket_merge_filtered_contacts($wpdb->get_results($q, 'ARRAY_A'), $filtered_contacts);
                $filter_action_set = TRUE;
                
                inboundrocket_track_plugin_activity('Filtered contacts - action: '.$filter_action.', scope: '.$filter_content);
            }
        }
        
        // filter for a form submitted on a specific page
        if ( !empty($filter_action) && $filter_action === 'submitted' )
        {
            $filter_form_query = '';
            if ( !empty($filter_form) && $filter_form && $filter_form !== 'any-form' )
            {
                $query_filter_form = str_replace(array('#', '.'), '', $filter_form);
                $filter_form_query = $wpdb->prepare(" AND ( form_selector_id LIKE '%%%s%%' OR form_selector_classes LIKE '%%%s%%' )", $query_filter_form, $query_filter_form);
            }

            $q = $wpdb->prepare("SELECT lead_hashkey FROM {$wpdb->ir_submissions} WHERE form_page_title LIKE '%%%s%%' ", ( $filter_content != 'any-page' ? $filter_content : '' ));
            $q .= ( $filter_form_query ? $filter_form_query : '' );
            $q .= " GROUP BY lead_hashkey";
            
            $filtered_contacts = inboundrocket_merge_filtered_contacts($wpdb->get_results($q, 'ARRAY_A'), $filtered_contacts);
            $filter_action_set = TRUE;
            
            inboundrocket_track_plugin_activity('Filtered contacts - action: '.$filter_action.', form: '.$filter_form.', scope: '.$filter_content);
        }        

        $filtered_hashkeys = inboundrocket_explode_filtered_contacts($filtered_contacts);

        $mysql_action_filter = '';
        if ( $filter_action_set ) // If a filter action is set and there are no contacts, do a blank
            $mysql_action_filter = " AND l.hashkey IN ( " . ( $filtered_hashkeys ? $filtered_hashkeys : "''" ) . " ) ";
        else
            $mysql_action_filter = ( $filtered_hashkeys ? " AND l.hashkey IN ( " . $filtered_hashkeys . " ) " : '' ); // If a filter action isn't set, use the filtered hashkeys if they exist, else, don't include the statement
			
        // There's a filter and leads are in it
        if ( ( isset($contact_type) && ( $num_contacts || !$contact_type ) ) || !isset($contact_type) )
        {
	        $q =  $wpdb->prepare("
                SELECT 
                    l.lead_id AS lead_id,
                    COUNT(DISTINCT ls.share_id) AS lead_shares,
                    LOWER(DATE_SUB(l.lead_date, INTERVAL %d HOUR)) AS lead_date, l.lead_ip, l.lead_source, l.lead_email, l.hashkey, l.lead_first_name, l.lead_last_name,
                    COUNT(DISTINCT s.form_id) AS lead_form_submissions,
                    COUNT(DISTINCT p.pageview_id) AS lead_pageviews,
                    LOWER(DATE_SUB(MAX(p.pageview_date), INTERVAL %d HOUR)) AS last_visit,
                    ( SELECT COUNT(DISTINCT pageview_id) FROM {$wpdb->ir_pageviews} WHERE lead_hashkey = l.hashkey AND pageview_session_start = 1 AND pageview_deleted = 0 ) AS visits,
                    ( SELECT MIN(pageview_source) AS pageview_source FROM {$wpdb->ir_pageviews} WHERE lead_hashkey = l.hashkey AND pageview_session_start = 1 AND pageview_deleted = 0 ) AS pageview_source 
                FROM 
                    {$wpdb->ir_leads} l
                LEFT JOIN {$wpdb->ir_submissions} s ON l.hashkey = s.lead_hashkey
                LEFT JOIN {$wpdb->ir_pageviews} p ON l.hashkey = p.lead_hashkey
                LEFT JOIN {$wpdb->ir_shares} ls ON l.hashkey = ls.lead_hashkey 
                WHERE l.lead_email != '' AND l.lead_deleted = 0 AND l.hashkey != '' ", $wpdb->db_hour_offset, $wpdb->db_hour_offset);

            $q .= $mysql_contact_type_filter;
            $q .= ( $mysql_search_filter ? $mysql_search_filter : "" );
            $q .= ( $mysql_action_filter ? $mysql_action_filter : "" );
            $q .=  " GROUP BY l.hashkey";
            
            $leads = $wpdb->get_results($q);
        }
        else
        {
            $leads = array();
        }
        
        $all_leads = array();

        $contact_count = 0;
        
        $num_pageviews = isset($_GET['num_pageviews']) ? esc_attr($_GET['num_pageviews']) : '';
        $page = isset($_REQUEST['page']) ? esc_attr($_REQUEST['page']) : '';
        $paged = isset($_GET['paged']) ? esc_attr($_GET['paged']) : '';

        if ( count($leads) )
        {
            foreach ( $leads as $key => $lead ) 
            {
                // filter for number of page views and skipping lead if it doesn't meet the minimum
                if ( isset($filter_action) && $filter_action === 'num_pageviews' )
                {
                    if ( $lead->lead_pageviews < $filter_content )
                        continue;
                }

                $url = inboundrocket_strip_params_from_url($lead->lead_source);

                $redirect_url = '';
                if ( isset($contact_type) || isset($filter_action) || isset($filter_form) || isset($filter_content) || isset($num_pageviews) || isset($s) || isset($paged) )
                    $redirect_url = urlencode(inboundrocket_get_current_url());

                $lead_array = array(
                    'ID' => $lead->lead_id,
                    'hashkey' => $lead->hashkey,
                    'email' => sprintf('<a href="?page=%s&action=%s&lead=%s%s">' . "<img class='pull-left inboundrocket-contact-avatar inboundrocket-dynamic-avatar_" . substr($lead->lead_id, -1) . "' src='https://www.gravatar.com/avatar/" . md5( strtolower( trim( $lead->lead_email ) ) ) . "' width='50px' height='50px' style='margin-top: 2px; border-radius: 25px;'/> " . '</a>', $page, 'view', $lead->lead_id, ( $redirect_url ? '&redirect_to=' .  $redirect_url : '' )) .  sprintf('<a href="?page=%s&action=%s&lead=%s%s">%s' . $lead->lead_email . '</a>', $page, 'view', $lead->lead_id, ( $redirect_url ? '&redirect_to=' .  $redirect_url : '' ), ( strlen($lead->lead_first_name) || strlen($lead->lead_last_name)? '<b>' . $lead->lead_first_name . ' ' . $lead->lead_last_name . '</b><br>' : '' )),
                    'visits' => ( !isset($lead->visits) ? 1 : $lead->visits ),
                    'submissions' => $lead->lead_form_submissions,
                    'shares' => isset($lead->lead_shares) ? $lead->lead_shares : '0',
                    'pageviews' => isset($lead->lead_pageviews) ? $lead->lead_pageviews : '0',
                    'date' => date('Y-m-d g:ia', strtotime($lead->lead_date)),
                    'source' => ( $lead->pageview_source ? "<a title=\"".__('Visit page','inbound-rocket')."\" href=\"" . esc_url($lead->pageview_source) . "\" target=\"_blank\">" . inboundrocket_strip_params_from_url($lead->pageview_source) . "</a>" : __('Direct','inbound-rocket') ),
                    'last_visit' => date('Y-m-d g:ia', strtotime($lead->last_visit)),
                    'source' => ( $lead->lead_source ? "<a title=\"".__('Visit page','inbound-rocket')."\" href=\"" . esc_url($lead->lead_source) . "\" target=\"_blank\">" . inboundrocket_strip_params_from_url($lead->lead_source) . "</a>" : __('Direct','inbound-rocket') )
                );
                
                array_push($all_leads, $lead_array);
                $contact_count++;
            }
        }

        $this->total_filtered = count($all_leads);
		
        return $all_leads;
    }

    /**
     * Gets the total number of contacts, comments and subscribers for above the table
     */
    function get_total_contacts ()
    {
        global $wpdb;

        $q = "SELECT 
                COUNT(DISTINCT hashkey) AS total_contacts
            FROM 
                {$wpdb->ir_leads}
            WHERE
                lead_email != '' AND lead_deleted = 0 AND hashkey != '' ";

        $total_contacts = $wpdb->get_var($q);
        return $total_contacts;
    }
    
     /**
     * Gets the total number of shares for the contact
     */
    function get_total_shares ($hashkey)
    {
        global $wpdb;

        $q = $wpdb->prepare("SELECT 
                COUNT(ls.share_id) AS shares_count
            FROM 
                {$wpdb->ir_shares} ls
            WHERE 
            	ls.lead_hashkey = %s
            ORDER BY ls.share_date DESC", $hashkey);

        $total_contacts = $wpdb->get_var($q);
        return $total_contacts;
    }

    /**
     * Gets the current view based off $_GET['contact_type']
     *
     * @return  string
     */
    function get_view ()
    {
	    $current_contact_type = isset($_GET['contact_type']) ? esc_url($_GET['contact_type']) : 'contacts';
        return $current_contact_type;
    }

    /**
     * Get the contact tags
     *
     * @return  string
     */
    function get_lead_lists ()
    {
        global $wpdb;

        $q = "SELECT 
                lt.tag_text, lt.tag_slug, lt.tag_synced_lists, lt.tag_form_selectors, lt.tag_order, lt.tag_id,
                ( SELECT COUNT(DISTINCT contact_hashkey) FROM {$wpdb->ir_tag_relationships} ltr, {$wpdb->ir_leads} ll WHERE tag_id = lt.tag_id AND ltr.tag_relationship_deleted = 0 AND ltr.contact_hashkey != '' AND ll.hashkey = ltr.contact_hashkey AND ll.lead_deleted = 0 AND ll.hashkey != '' GROUP BY tag_id ) AS tag_count
            FROM 
                {$wpdb->ir_tags} lt
            WHERE
                lt.tag_deleted = 0
            GROUP BY lt.tag_slug 
            ORDER BY lt.tag_order ASC";

        return $wpdb->get_results($q);
    }

	/**
     * Prints contacts menu next to contacts table
     */
    function views ()
    {
        $this->tags = stripslashes_deep($this->get_lead_lists());
        
        $current = ( !empty($_GET['contact_type']) ? esc_url($_GET['contact_type']) : 'all' );
        
        $all_params = array( 'contact_type', 's', 'paged', '_wpnonce', '_wpreferrer', '_wp_http_referer', 'action', 'action2', 'filter_form', 'filter_action', 'filter_content', 'contact');
        
        $all_url = esc_url(remove_query_arg($all_params));

        $this->total_contacts = $this->get_total_contacts();

        echo "<h3 class='inboundrocket-contacts-total col-right'>";
            echo "<span class='icon-user'></span>" . $this->total_contacts .  " ".__('Total Contacts','inbound-rocket');
        echo "</h3>";

		if ( empty( $this->tags ) ) {
            echo "<h3 class='inboundrocket-contacts__tags-header col-left'>".__('No Lead Lists','inbound-rocket')."</h3>";
        }
        else {
            echo "<h3 class='inboundrocket-contacts__tags-header col-left'>".__('Lead Lists','inbound-rocket')."</h3>";
        }

        if ( $current == 'all' ) {
            $this->view_label = __('Contacts','inbound-rocket');
            $this->view_count = $this->total_contacts;
        }
		echo "<div class='col-clear'></div>";
        echo "<ul class='inboundrocket-contacts__type-picker'>";
            foreach ( $this->tags as $tag ) {
                
                if ( $current == $tag->tag_slug ) {
                    $currentTag = true;
                    $this->view_label = $tag->tag_text;
                    $this->view_count = $tag->tag_count;
                } else {
                    $currentTag = false;
                }

                echo "<li><a href='" . $all_url . "&contact_type=" . $tag->tag_slug . "' class='" . ( $currentTag ? 'current' :'' ) . "'><span class='icon-profile'></span>" . ( $tag->tag_count ? $tag->tag_count : '0' ) . " " . $tag->tag_text . "</a></li>";
            }
        echo "<li><a href='" . admin_url('admin.php?page=inboundrocket_lead_lists') . "' class='contact-edit-lists'>".__('Manage lead lists','inbound-rocket')."</a></li>";
        echo "</ul>";
    }

    /**
     * Prints contacts filter above contacts table
     */
    function filters()
    {
        $contact_type = isset($_GET['contact_type']) ? esc_attr($_GET['contact_type']) : 'all';
        $filter_action = isset($_GET['filter_action']) ? esc_attr($_GET['filter_action']) : 'visited';
		$filter_content = isset($_GET['filter_content']) ? esc_attr($_GET['filter_content']) : '';
		$filter_form = isset($_GET['filter_form']) ? esc_attr($_GET['filter_form']) : '';
        ?>
            <form id="inboundrocket-contacts-filter" class="inboundrocket-contacts__filter" method="GET">
                
                <h3 class="inboundrocket-contacts__filter-text">
                    <span class="inboundrocket-contacts__filter-count"><?php echo ( $this->total_filtered != $this->view_count ? '<span id="contact-count">' . $this->total_filtered . '</span>' . '/' : '' ) . '<span id="contact-count">' . ( $this->view_count ? $this->view_count : '0' ) . '</span>' . ' ' . strtolower($this->view_label); ?></span> who 
                    <select name="filter_action" id="filter_action">
                        <option value="visited" <?php echo ( $filter_action=='visited' ? 'selected' : '' ) ?> ><?php _e('viewed','inbound-rocket'); ?></option>
                        <option value="submitted" <?php echo ( $filter_action=='submitted' ? 'selected' : '' ) ?> ><?php _e('submitted','inbound-rocket'); ?></option>
                    </select>

                    <span id="form-filter-input" <?php echo ( ! isset($filter_form) || ( isset($filter_action) && $filter_action != 'submitted' ) ? 'style="display: none;"' : ''); ?>>
                        <select name="filter_form" id="filter_form" style="width:250px;">
	                        <?php 
		                        if(!empty($filter_form)){ 
			                        if($filter_content=='any-form') $filter_text = 'any form'; else $filter_text = $filter_form;
		                        	echo '<option value="'.$filter_form.'" selected="selected">'.$filter_text.'</option>';
		                        }
	                        ?>
                        </select> on 
                    </span>

                    <select name="filter_content" id="filter_content" style="width:250px;">
	                    <?php 
		                        if(!empty($filter_content)){ 
			                        if($filter_content=='any-page') $filter_text = 'any page'; else $filter_text = $filter_content;
		                        	echo '<option value="'.$filter_content.'" selected="selected">'.$filter_text.'</option>';
		                        }
	                        ?>
                    </select>
                    
                    <input type="submit" name="" id="inboundrocket-contacts-filter-button" class="button action" value="<?php _e('Apply','inbound-rocket'); ?>">

                    <?php if ( isset($filter_action) || isset($filter_content) ) : ?>
                        <a href="<?php echo admin_url('admin.php?page=inboundrocket_contacts') . ( isset($contact_type) ? '&contact_type=' . $contact_type : '' ); ?>" id="clear-filter"><?php _e('clear filter','inbound-rocket'); ?></a>
                    <?php endif; ?>
                </h3>

                <?php if ( isset($contact_type) ) : ?>
                    <input type="hidden" name="contact_type" value="<?php echo $contact_type; ?>"/>
                <?php endif; ?>

                <input type="hidden" name="page" value="inboundrocket_contacts"/>
            </form>
        <?php
    }

    /**
     * Gets + prepares the contacts for the list table
     */
    function prepare_items ()
    {
        $per_page = 10;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
                
        $orderby = ( !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'last_visit' );
        $order = ( !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'desc' );

        usort($this->data, array($this, 'usort_reorder'));

        $current_page = $this->get_pagenum();
        $total_items = count($this->data);
        $this->data = array_slice($this->data, (($current_page-1)*$per_page), $per_page);

        $this->items = $this->data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }

    /**
     * Sorting function for usort
     * 
     * @param array
     * @param array
     * @return array    sorted array
     */
    function usort_reorder ( $a, $b ) 
    {
        $orderby = ( !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'last_visit' );
        $order = ( !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'desc' );

        // Timestamp columns need to be convereted to integers to sort correctly
        $val_a = ( $orderby == 'last_visit' || $orderby == 'date' ? strtotime($a[$orderby]) : $a[$orderby] );
        $val_b = ( $orderby == 'last_visit' || $orderby == 'date' ? strtotime($b[$orderby]) : $b[$orderby] );

        if ( $val_a == $val_b )
            $result = 0;
        else if ( $val_a < $val_b )
            $result = -1;
        else
            $result = 1;

        return ( $order === 'asc' ? $result : -$result );
    }
}