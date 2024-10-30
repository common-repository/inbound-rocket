<?php
if ( !defined('ABSPATH') ) exit;
if ( !defined('INBOUNDROCKET_PLUGIN_DIR') ) die('Security');

//=============================================
// Include Needed Files
//=============================================
include_once(INBOUNDROCKET_PLUGIN_DIR . '/admin/lib/Snowplow/RefererParser/Parser.php');
include_once(INBOUNDROCKET_PLUGIN_DIR . '/admin/lib/Snowplow/RefererParser/Referrer.php');
include_once(INBOUNDROCKET_PLUGIN_DIR . '/admin/lib/Snowplow/RefererParser/Medium.php');

//=============================================
// WPStatsDashboard Class
//=============================================
class IR_StatsDashboard {

    /**
     * Variables
     */
    var $total_visits = array();
    var $total_visits_last_30_days = 0;
    var $avg_visits_last_30_days = 0;
    var $total_contacts     = 0;
    var $total_new_contacts     = 0;
    var $best_day_ever      = 0;
    var $avg_contacts_last_90_days  = 0;
    var $total_contacts_last_30_days = 0;
    var $total_contacts_last_90_days = 0;
    var $total_returning_contacts = 0;
    var $max_source = 0;
    
    /**
     * Arrays
     */
    var $returning_contacts;
    var $new_contacts;
    var $most_popular_pages;
    var $new_shares;
    var $most_popular_referrer;
    var $this_month_daily_leads = array();
	var $last_month_daily_leads = array();
	var $x_axis_widget_labels;
    
    /**
     * Sources counts
     */
	var $organic_count 	= 0;
	var $referral_count = 0;
	var $social_count 	= 0;
	var $email_count 	= 0;
	var $paid_count 	= 0;
	var $direct_count 	= 0;

	var $x_axis_labels 	= '';
	var $column_colors 	= '';
	var $column_data 	= '';
	var $average_data 	= '';
	var $visits_data	= '';
	var $weekend_column_data = '';

	var $parser;

	function __construct ()
	{
		
		if(is_admin()):
			global $pagenow;
			
			$this->parser = new Parser();
		
			$screen = get_current_screen();
		
			if ( in_array( $screen->parent_base, array( 'inboundrocket_stats' ) ) ):
			
				$this->most_popular_referrer = $this->get_popular_referrer();
				
				$this->new_shares = $this->get_shares();
				$this->most_popular_pages = $this->get_popular_pages();
				
				$this->returning_contacts = $this->get_returning_contacts();
				$this->total_returning_contacts = count($this->returning_contacts);

				$this->new_contacts = $this->get_new_contacts();
				$this->total_new_contacts = count($this->new_contacts);
				
				$this->get_data_last_30_days_graph();
				$this->get_sources();
			
			endif;
			
			if ( ($pagenow == 'index.php' ) ) {
				if (INBOUNDROCKET_ENABLE_DEBUG==true) { wp_register_style('inboundrocket-widgets-css', INBOUNDROCKET_PATH . '/admin/inc/css/inboundrocket-widgets.css');    
	    		} else { wp_register_style('inboundrocket-widgets-css', INBOUNDROCKET_PATH . '/admin/inc/css/inboundrocket-widgets.min.css');}
				wp_enqueue_style('inboundrocket-widgets-css');
		
				wp_register_script('inboundrocket-charts-js', INBOUNDROCKET_PATH . '/admin/inc/js/Chart.bundle.min.js', array( 'jquery' ), FALSE, TRUE);
				wp_enqueue_script('inboundrocket-charts-js');
				
				add_action('admin_footer', array(&$this, 'build_widget_lead_chart'));
			}
			
			
			
		endif;

	}
	
	/**
	* Create the function to output the contents of our Statistics Dashboard Widget.
	* @TODO create the content
 	*/
 	public function display_lead_report_widget() {
 		
 		global $wpdb;
 		
 		$all_time_leads = get_total_contacts();

        $url = site_url();

        $c_month = date('n') == 1 ? 12 : date('n');
        $previous_month = date('n') == 1 ? 12 : date('n') - 1;
        $previous_year = $previous_month == 12 ? date('Y') - 1 : date('Y');

        $start_current = date("Y-m-01"); // start of current month
        $end_current = date("Y-m-t", strtotime('last day of this month')); // end of current month

        //getting the previous month
        $previous_month_start = date("Y-m-01", strtotime("previous month"));
        $previous_month_end = date("Y-m-d", strtotime("-1 month"));

        $this_month = $this->ir_count_leads_by_time($start_current, $end_current);
        $last_month = $this->ir_count_leads_by_time($previous_month_start, $previous_month_end);

        $all_lead_text = ($all_time_leads == 1) ? "Lead" : "Leads";
        $leads_today = $this->ir_get_lead_count_from_today();
        $leads_today_text = ($leads_today == 1) ? "Lead" : "Leads";
        $month_comparasion = $this_month - $last_month;

        if ($month_comparasion < 0) {
            $month_class = 'ir-negative-leads';
            $sign = "";
            $sign_text = "decrease";
        } elseif ($month_comparasion === 0) {
            $month_class = 'ir-no-change';
            $sign = "";
            $sign_text = "No change ";
        } else {
            $month_class = 'ir-positive-leads';
            $sign = "+";
            $sign_text = "increase";
        }
		?>
		<div class="ir_leads_dashboard_widget">
			<canvas id="ir-leads-stats-graph" width="393" height="400"></canvas>
			<div id="ir-leads-stat-boxes">
                <div class='ir-leads-today'>
                    <a class="ir-data-block ir-widget-block" alt="<?php _e('Click to View Todays Leads', 'inbound-rocket'); ?>" href="">
                        <section>
                            <?php echo $leads_today; ?>
                            <br><?php echo $leads_today_text; ?>
                            <br><strong><?php _e('Today', 'inbound-rocket'); ?></strong>
                        </section>
                    </a>
                </div>
                <div class='ir-leads-this-month'>
                    <a class="ir-data-block ir-widget-block" alt="<?php _e('Click to View This Months Leads', 'inbound-rocket'); ?>" href="">
                        <section>
                            <?php echo $this_month; ?>
                            <br><?php echo $all_lead_text; ?>
                            <br><strong><?php _e('This Month', 'inbound-rocket'); ?></strong>
                        </section>
                    </a>
                </div>
                <div class='ir-leads-all-time'>
                    <a class="ir-data-block ir-widget-block" title="<?php _e('Click to View All Leads', 'inbound-rocket'); ?>" href="<?=admin_url('admin.php?page=inboundrocket_contacts');?>">
                        <section>
                            <?php echo $all_time_leads; ?>
                            <br><?php _e('Leads', 'inbound-rocket'); ?>
                            <strong><?php _e('All Time', 'inbound-rocket'); ?></strong>
                        </section>
                    </a>
                </div>
                <div class="ir-leads-change-box" style="text-align: center;">
                    <small class='<?php echo $month_class; ?>'><?php echo "<span>" . $sign . $month_comparasion . "</span> " . $sign_text; ?> <?php _e('since last month', 'inbound-rocket'); ?></small>
                </div>
                <?php if ($all_time_leads >= "1") { ?>
                <div id='leads-list'>
	                <?php
		            
		            
		            ?>
	                <h4 class='marketing-widget-header'><?php _e('Latest Leads', 'inbound-rocket'); ?><span class="toggle-lead-list"></span></h4>
	                <ul id='lead-ul'>
		            	<?php
							$amount = 5;
							
							foreach ( $this->get_latest_contacts ($amount) as $contact )
							{
								if(isset($contact->lead_first_name) && isset($contact->lead_last_name)){
									$lead_full_name = esc_html($contact->lead_first_name)." ".esc_html($contact->lead_last_name);
								} else {
									$lead_full_name = '';
								}
								
								$gravatar_hash = md5( strtolower( trim( $contact->lead_email ) ) );
			            ?>
						<li>
						<a class="post-edit-link" href="<?=admin_url('admin.php?page=inboundrocket_contacts&action=view&lead='.$contact->lead_id);?>"><img class="ir-widget-gravatar" alt="Avatar" src="https://www.gravatar.com/avatar/<?php echo $gravatar_hash;?>"><?php if (!empty($lead_full_name)) { echo esc_html( $lead_full_name ); } elseif (!empty($contact->lead_first_name)) { echo esc_html( $contact->lead_first_name )." "; } elseif (!empty($contact->lead_last_name)) { echo esc_html( $contact->lead_last_name )." "; } else { _e( 'No name provided', 'inbound-rocket' );}
							//echo !empty($lead_full_name) ? $lead_full_name : !empty($contact->lead_first_name) ? esc_html($contact->lead_first_name)." " : !empty($contact->lead_last_name) ? esc_html($contact->lead_last_name)." " : __( 'No name provided', 'inbound-rocket' );
							?></a> on <?php echo $contact->lead_date; ?> (<?php echo $contact->lead_email; ?>)
						</li>
						<?php } ?> 
	                </ul>
	            </div>
	            <?php } ?>
			</div>
		</div>
		<?php
	}
	
	function build_widget_lead_chart (){
		$this->get_data_this_month_versus_last_month();
	?>
		<script type="text/javascript">
			
			var chart;
			
			function create_lead_chart ()
			{
				Chart.defaults.global.defaultFontFamily = "Open Sans";
				Chart.defaults.global.defaultFontSize = 12;
				
				var leadStats = {
					labels: [1,'',3,'',5,'',7,'',9,'',11,'',13,'',15,'',17,'',19,'',21,'',23,'',25,'',27,'',29,'',31],
					datasets: [{
					    label: "This Month",
					    data: [<?php foreach ($this->this_month_daily_leads as $day => $cnt){ echo $cnt.','; } ?>],
					    lineTension: 0,
					    fill: false,
						borderColor: '#cdf3fb',
					    backgroundColor: 'transparent',
					    pointBackgroundColor: 'rgba(205,243,251,0.8)'
					},
					{
					    label: "Last Month",
					    data: [<?php foreach ($this->last_month_daily_leads as $day => $cnt){ echo $cnt.','; } ?>],
					    lineTension: 0,
					    fill: false,
					    borderColor: '#f47d4a',
					    backgroundColor: 'transparent',
					    pointBackgroundColor: 'rgba(244,125,74,0.8)',
					}]
				}
				
				var chartOptions = {
					title: {
						display: true,
						text: ['Lead Growth','Current Month vs. Last Month'],
					},
					legend: {
						display: true,
					    position: 'top',
					    labels: {
					    	boxWidth: 80,
							fontColor: '#aaa'
					    }
					},
					scales: {
					    xAxes: [{
					    	gridLines: {
					        	display: false
					    	},
							scaleLabel: {
					        	display: false
					    	}
					    }],
					    yAxes: [{
							ticks: {
								autoSkip: true,
								maxTicksLimit: 30,
								beginAtZero:true
							},
					    	gridLines: {
								color: "#aaa",
								borderDash: [2, 5],
					    	},
							scaleLabel: {
					        	display: false
					    	}
					    }]
					}
				}
				
				var canvas = document.getElementById('ir-leads-stats-graph').getContext('2d');
				chart = new Chart(canvas, {
					type: 'line',
					data: leadStats,
					options: chartOptions
				});
		}
		
		jQuery(document).ready( function ( e ) {
	            
            create_lead_chart();
			
			var delay = (function(){
	          var timer = 0;
	          return function(callback, ms){
	            clearTimeout (timer);
	            timer = setTimeout(callback, ms);
	          };
	        })();
	
	        // Takes care of figuring out the weekend widths based on the new column widths
	        jQuery(window).resize(function() {
	            height = chart.height
	            width = jQuery("#ir-leads-stats-graph").width();
	            chart.setSize(width, height);
	        });
        });
		</script>		
		
	<?php
	}
	
	function ir_count_leads_by_time($start_current, $end_current) {
        global $wpdb;

		if ( ! isset($wpdb->ir_leads) )
	        return 0;

        $numposts = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT hashkey) AS total_contacts
				FROM 
                	{$wpdb->ir_leads}
				WHERE
                	lead_email != '' AND lead_deleted = 0 AND hashkey != ''
				AND 
				lead_date BETWEEN %s AND %s", $start_current, $end_current
            )
        );

        return $numposts;
    }

	function ir_get_lead_count_from_today() {
        global $wpdb;

		if ( ! isset($wpdb->ir_leads) )
	        return 0;

        $wordpress_date_time = $timezone_format = _x('Y-m-d', 'timezone date format');
        $wordpress_date_time = date_i18n($timezone_format);
        $wordpress_date = $timezone_day = _x('d', 'timezone date format');
        $wordpress_date = date_i18n($timezone_day);

        $today = $wordpress_date_time; // Corrected timezone
        $tomorrow = date("Y-m-d", strtotime("+2 day")); // Hack to look 2 days ahead

        $numposts = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT hashkey) AS total_contacts
				FROM 
                	{$wpdb->ir_leads}
				WHERE
                	lead_email != '' AND lead_deleted = 0 AND hashkey != ''
				AND 
				lead_date BETWEEN %s AND %s", $today, $tomorrow
            )
        );

        return $numposts;
    }
    
    function get_data_this_month_versus_last_month()
    {
	    global $wpdb;
	    
	    // Getting leads per day of the current month
	    $results = $wpdb->get_results("SELECT DATE_FORMAT(lead_date, '%Y-%m-%d') as date_field,COUNT(hashkey) as val FROM {$wpdb->ir_leads} WHERE YEAR(lead_date) = YEAR(CURRENT_DATE) AND MONTH(lead_date) = MONTH(CURRENT_DATE) AND lead_deleted = 0 AND lead_email != '' AND hashkey != '' GROUP BY date_field");
		
		$number = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
				
		for ( $i = 1; $i <= $number; $i++){
			$this->this_month_daily_leads[$i] = 0;
			foreach($results as $row){
				$day = sprintf("%02d", $i);
				$month = sprintf("%02d", date('m'));
				if($row->date_field==date('Y').'-'.$month.'-'.$day) {
					$this->this_month_daily_leads[$i] = $row->val;
					break;
				} 
			}
		}
		
		// Getting leads per day of the previous month
		$results = $wpdb->get_results("SELECT DATE_FORMAT(lead_date, '%Y-%m-%d') as date_field,COUNT(hashkey) as val FROM {$wpdb->ir_leads} WHERE YEAR(lead_date) = YEAR(CURRENT_DATE) AND MONTH(lead_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND lead_deleted = 0 AND lead_email != '' AND hashkey != '' GROUP BY date_field");
		
		//die(print_r($results));
		
		if(date('m')-1==0) {
			$month = 12;
			$year = date('Y')-1; 
		} else {
			$month = date('m')-1;
			$year = date('Y');
		}
		
		$number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
				
		for ( $i = 1; $i <= $number; $i++){
			$this->last_month_daily_leads[$i] = 0;
			foreach($results as $row){
				$day = sprintf("%02d", $i);
				$month = sprintf("%02d", $month);
				if($row->date_field==date('Y').'-'.$month.'-'.$day) {
					$this->last_month_daily_leads[$i] = $row->val;
					break;
				} 
			}
		}
    }    
	
	function get_data_last_30_days_graph ()
	{
		global $wpdb;
				
		$q = "SELECT 
                COUNT(DISTINCT lead_hashkey) AS total_visits
            FROM 
                {$wpdb->ir_pageviews}
            WHERE
                pageview_deleted = 0 AND lead_hashkey != '';";
		
		$this->total_visits = $wpdb->get_var($q);
		
		$q = "SELECT 
                COUNT(DISTINCT hashkey) AS total_contacts
            FROM 
                {$wpdb->ir_leads}
            WHERE
                lead_email != '' AND lead_deleted = 0 AND hashkey != '';";

        $this->total_contacts = $wpdb->get_var($q);

        $q = "SELECT DATE(lead_date) as lead_date, COUNT(DISTINCT hashkey) contacts FROM {$wpdb->ir_leads} WHERE lead_email != '' AND hashkey != '' AND lead_deleted = 0 GROUP BY DATE(lead_date)";
		$contacts = $wpdb->get_results($q);
		
		$q = "SELECT DATE(pageview_date) as pageview_date, COUNT(DISTINCT lead_hashkey) pageviews FROM {$wpdb->ir_pageviews} WHERE lead_hashkey != '' AND pageview_deleted = 0 GROUP BY DATE(pageview_date)";
		$visits = $wpdb->get_results($q);

		for ( $i = count($contacts)-1; $i >= 0; $i-- )
		{
			$this->best_day_ever = ( $contacts[$i]->contacts && $contacts[$i]->contacts > $this->best_day_ever ? $contacts[$i]->contacts : $this->best_day_ever);
		}

        for ( $i = 30; $i >= 0; $i-- )
        {
            $array_key = inboundrocket_search_object_by_value($contacts, date('Y-m-d', strtotime('-'. $i .' days')), 'lead_date');
            $this->total_contacts_last_30_days += ( $array_key ? $contacts[$array_key]->contacts : 0);
            
            $array_key = inboundrocket_search_object_by_value($visits, date('Y-m-d', strtotime('-'. $i .' days')), 'pageview_date');
            $this->total_visits_last_30_days += ( $array_key ? $visits[$array_key]->pageviews : 0);
        }
        
        for ( $i = 90; $i >= 0; $i-- )
        {
            $array_key = inboundrocket_search_object_by_value($contacts, date('Y-m-d', strtotime('-'. $i .' days')), 'lead_date');
            $this->total_contacts_last_90_days += ( $array_key ? $contacts[$array_key]->contacts : 0);
        }
		
		$this->avg_contacts_last_90_days = intval(floor($this->total_contacts_last_90_days/90));
        $this->avg_contacts_last_30_days = (intval(floor($this->total_contacts_last_30_days/30)) > 0) ? intval(floor($this->total_contacts_last_30_days/30)) : 1;
        
        
        
        $this->avg_visits_last_30_days = intval(floor($this->total_visits_last_30_days/30));
        
		for ( $i = 31; $i >= 0; $i-- )
		{
			// x axis labels
			$this->x_axis_labels .= "'" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "'". ( $i != 0 ? "," : "" );

			// colors for chart columns
			$array_key = inboundrocket_search_object_by_value($contacts, date('Y-m-d', strtotime('-'. $i .' days')), 'lead_date');
			
			if($array_key && $contacts[$array_key]->contacts > $this->avg_contacts_last_30_days){
				//$this->column_data .= "{ name: '" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "', color: '#00CAF0', y: " . ( $array_key ? $contacts[$array_key]->contacts : '0' ) . " }" . ( $i != 0 ? ", " : "" );
				$this->column_data .= "{ x: '" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "', y: " . ( $array_key ? $contacts[$array_key]->contacts : '0' ) . " }" . ( $i != 0 ? ", " : "" );
			} else {
				//$this->column_data .= "{ name: '" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "', color: '#00CAF0', y: " . ( $array_key ? $contacts[$array_key]->contacts : '0' ) . " }" . ( $i != 0 ? ", " : "" );
				$this->column_data .= "{ x: '" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "', y: " . ( $array_key ? $contacts[$array_key]->contacts : '0' ) . " }" . ( $i != 0 ? ", " : "" );
			}
			

            // weekend background column points
			if ( inboundrocket_is_weekend(date('Y-m-d', strtotime('-'. $i .' days'))) )
				//$this->weekend_column_data .= "{ name: '" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "', color: 'rgba(0,0,0,.05)', y: " . ( $array_key ? $contacts[$array_key]->contacts : '0' ) . " }" . ( $i != 0 ? ", " : "" );
				$this->weekend_column_data .= "{ x: '" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "', y: 1 }" . ( $i != 0 ? ", " : "" );
			else
				//$this->weekend_column_data .= "{ name: '" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "', color: 'rgba(0,0,0,0)', y: " . ( $array_key ? $contacts[$array_key]->contacts : '0' ) . " }" . ( $i != 0 ? ", " : "" );
				$this->weekend_column_data .= "{ x: '" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "', y: 0 }" . ( $i != 0 ? ", " : "" );			
				
			$array_key = inboundrocket_search_object_by_value($visits, date('Y-m-d', strtotime('-'. $i .' days')), 'pageview_date');
			//$this->visits_data .= "{ name: '" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "', color: '#CCF3FC', y: " . ( $array_key ? $visits[$array_key]->pageviews : '0' ) . " }" . ( $i != 0 ? "," : "");
			$this->visits_data .= "{ x: '" . strtoupper(date('M j', strtotime('-'. $i .' days'))) . "', y: " . ( $array_key ? $visits[$array_key]->pageviews : '0' ) . " }" . ( $i != 0 ? "," : "");
			
            // average line
            if ( $this->avg_contacts_last_30_days )
            {
                $this->average_data .= $this->avg_contacts_last_30_days . ( $i != 0 ? "," : "");
            }

		}
	}

	function get_sources ()
	{
		global $wpdb;

		$q = "SELECT hashkey lh,
			( SELECT MIN(pageview_source) AS pageview_source FROM {$wpdb->ir_pageviews} WHERE lead_hashkey = lh AND pageview_session_start = 1 AND pageview_deleted = 0 ) AS lead_source,
			( SELECT MIN(pageview_url) AS pageview_url FROM {$wpdb->ir_pageviews} WHERE lead_hashkey = lh AND pageview_session_start = 1 AND pageview_deleted = 0 ) AS lead_origin_url 
		 FROM 
		 	{$wpdb->ir_leads}
		 WHERE 
		 	lead_date BETWEEN CURDATE() - INTERVAL 30 DAY AND NOW() AND lead_email != ''";
	
		$contacts = $wpdb->get_results($q);

		foreach ( $contacts as $contact ) 
		{
			$source = $this->check_lead_source($contact->lead_source, $contact->lead_origin_url);

			switch ( $source )
		    {
		    	case 'search' :
		    		$this->organic_count++;
		    	break;

		    	case 'social' :
		    		$this->social_count++;
		    	break;
		    
		    	case 'email' :
		    		$this->email_count++;
		    	break;

		    	case 'referral' :
		    		$this->referral_count++;
		    	break;

		    	case 'paid' :
		    		$this->paid_count++;
		    	break;

		    	case 'direct' :
		    		$this->direct_count++;
		    	break;
		    }
		}

		$this->max_source = max(array($this->organic_count, $this->referral_count, $this->social_count, $this->email_count, $this->paid_count, $this->direct_count));
	}
	
	function get_shares()
	{
		global $wpdb;
		
		$shares = array();
		
		$q = "SELECT a.share_id,a.share_type,a.share,a.share_date,COUNT(a.share) as total FROM {$wpdb->ir_shares} a WHERE a.share_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() GROUP BY a.share HAVING COUNT(a.share) >= 1 ORDER BY a.share_id DESC;";
		
		$ss = $wpdb->get_results($q);
		
		if($ss):
		foreach($ss as $s){
			$arr = array(
				'shared_count' => $s->total,
				'shared_to' => $s->share_type,
				'shared_content' => $s->share,
				'id' => $s->share_id
			);
			$shares[] = $arr;
		}
		endif;
		
		return $shares;
	}
	
	function get_popular_pages()
	{
		global $wpdb;
		
		$popular_pages = array();
		
		$q = "SELECT pageview_date,pageview_title,pageview_url,COUNT(pageview_title) as cnt FROM {$wpdb->ir_pageviews} WHERE pageview_date >= '".date('Y-m-d')." 00:00:00' GROUP BY pageview_title HAVING COUNT(pageview_title) >= 2 ORDER BY cnt DESC;";
	
		$pp = $wpdb->get_results($q);
		
		$max_total = count($pp);
		
		if($pp):
		foreach($pp as $p){
			$arr = array(
				'page_title' => str_replace(site_url(),'',$p->pageview_url),
				'page_name' => $p->pageview_title,
				'total' => $p->cnt,
				'max_total' => $max_total	
			);
			$popular_pages[] = $arr;
		}
		endif;
	
		return $popular_pages;

	}	
	
	function get_popular_referrer()
	{
		global $wpdb;

		$popular_referrer = array();

		$q = "
			SELECT DISTINCT lead_hashkey,
				pageview_source, 
				count(*) as cnt 
			FROM 
				{$wpdb->ir_pageviews}
			WHERE 
				pageview_source != '' AND
				pageview_deleted <= 0
			GROUP BY 
				pageview_source
			ORDER BY 
				cnt DESC;";
		
		$pr = $wpdb->get_results($q);
		
		$count = 1;
		$total = 0;
		
		if($pr):
		foreach($pr as $r){
			$cnt = $r->cnt;
			$total += $cnt;
		}
		
		foreach($pr as $r){
			$cnt = $r->cnt;
			if($cnt > 1 && $count <= 5):
				$arr = array(
					'referral_source' => str_replace(site_url(),'',$r->pageview_source),
					'referral_count' => $cnt,
					'referral_total' => $total
				);
				$popular_referrer[] = $arr;
			endif;
			$count++;
		}
		endif;
		
		return $popular_referrer;
	}	
	
	function get_latest_contacts ($amount)
	{
		global $wpdb;

		$q = $wpdb->prepare("
			SELECT DISTINCT lead_hashkey lh,
				lead_id, 
				lead_email,
				ll.lead_first_name, 
				ll.lead_last_name, 
				ll.lead_date,
				( SELECT COUNT(*) FROM $wpdb->ir_pageviews WHERE lead_hashkey = lh ) as pageviews
			FROM 
				$wpdb->ir_leads ll
			JOIN 
				$wpdb->ir_pageviews lpv on lpv.lead_hashkey = ll.hashkey AND lpv.pageview_deleted = 0
			WHERE
				 ll.lead_email != '' AND ll.lead_deleted = 0
			ORDER BY ll.lead_date DESC
			LIMIT %d", $amount);

		return $wpdb->get_results($q, OBJECT);	
	}

		
	function get_new_contacts ()
	{
		global $wpdb;

		$q = "
			SELECT DISTINCT lead_hashkey lh,
				lead_id, 
				lead_email, 
				( SELECT COUNT(*) FROM $wpdb->ir_pageviews WHERE lead_hashkey = lh ) as pageviews, 
				( SELECT MIN(pageview_source) AS pageview_source FROM $wpdb->ir_pageviews WHERE lead_hashkey = lh AND pageview_session_start = 1 AND pageview_deleted = 0 ) AS lead_source,
				( SELECT MIN(pageview_url) AS pageview_url FROM $wpdb->ir_pageviews WHERE lead_hashkey = lh AND pageview_session_start = 1 AND pageview_deleted = 0 ) AS lead_origin_url 
			FROM 
				$wpdb->ir_leads ll, $wpdb->ir_pageviews lpv
			WHERE 
				lead_date >= CURRENT_DATE() AND 
				ll.hashkey = lpv.lead_hashkey AND 
				pageview_deleted = 0 AND lead_email != '' AND lead_deleted = 0;";

		return $wpdb->get_results($q);	
	}

	function get_returning_contacts ()
	{
		global $wpdb;

		$q = "
			SELECT 
				DISTINCT lead_hashkey lh,
				lead_id, 
				lead_email, 
				( SELECT COUNT(*) FROM $wpdb->ir_pageviews WHERE lead_hashkey = lh ) as pageviews,
				( SELECT MIN(pageview_source) AS pageview_source FROM $wpdb->ir_pageviews WHERE lead_hashkey = lh AND pageview_session_start = 1 AND pageview_deleted = 0 ) AS lead_source,
				( SELECT MIN(pageview_url) AS pageview_url FROM $wpdb->ir_pageviews WHERE lead_hashkey = lh AND pageview_session_start = 1 AND pageview_deleted = 0 ) AS lead_origin_url 
			FROM 
				$wpdb->ir_leads ll, $wpdb->ir_pageviews lpv
			WHERE 
				ll.lead_date <= CURRENT_DATE() AND 
				pageview_date = CURRENT_DATE() AND 
				ll.hashkey = lpv.lead_hashkey AND 
				pageview_deleted = 0 AND lead_email != '' AND lead_deleted = 0 ";

		return $wpdb->get_results($q);
	}	

	function check_lead_source ( $source, $origin_url = '' )
	{
		if ( $source )
		{
			$decoded_source = urldecode($source);

			if ( stristr(strtolower($decoded_source), 'utm_medium=cpc') || stristr(strtolower($decoded_source), 'utm_medium=ppc') || stristr(strtolower($decoded_source), 'aclk') || stristr(strtolower($decoded_source), 'gclid') )
				return 'paid';

			if ( stristr($source, 'utm_') )
			{
				$url = $source;
				$url_parts = parse_url($url);
				parse_str($url_parts['query'], $path_parts);

				if ( isset($path_parts['adurl']) )
					return 'paid';

				if ( isset($path_parts['utm_medium']) )
				{
					if ( strtolower($path_parts['utm_medium']) == 'cpc' || strtolower($path_parts['utm_medium']) == 'ppc' || strtolower($path_parts['utm_medium']) == 'ysm' || strtolower($path_parts['utm_medium']) == 'msn-ppc' || strtolower($path_parts['utm_medium']) == 'yahoo-ppc' || strtolower($path_parts['utm_medium']) == 'bing-ppc' )
						return 'paid';

					if ( strtolower($path_parts['utm_medium']) == 'social' || strtolower($path_parts['utm_medium']) == 'facebook' || strtolower($path_parts['utm_medium']) == 'twitter' || strtolower($path_parts['utm_medium']) == 'linkedin' )
						return 'social';

					if ( strtolower($path_parts['utm_medium']) == 'email' || strtolower($path_parts['utm_medium']) == 'e-mail' || strtolower($path_parts['utm_medium']) == 'newsletter' )
						return 'email';
				}

				if ( isset($path_parts['utm_source']) )
				{
					if ( stristr(strtolower($path_parts['utm_source']), 'email') || stristr(strtolower($path_parts['utm_source']), 'e-mail') || stristr(strtolower($path_parts['utm_source']), 'mailchimp') || stristr(strtolower($path_parts['utm_source']), 'constantcontact') || stristr(strtolower($path_parts['utm_source']), 'aweber') || stristr(strtolower($path_parts['utm_source']), 'icontact') || stristr(strtolower($path_parts['utm_source']), 'verticalresponse') || stristr(strtolower($path_parts['utm_source']), 'getresponse') ) 
						return 'email';
				}
			}

			$referer = $this->parser->parse(
			     $source
			);

			if ( $referer->isKnown() )
				return $referer->getMedium();
			else
			    return 'referral';
		}
		else
		{
			$decoded_origin_url = urldecode($origin_url);

			if ( stristr(strtolower($decoded_origin_url), 'utm_medium=cpc') || stristr(strtolower($decoded_origin_url), 'utm_medium=ppc') || stristr(strtolower($decoded_origin_url), 'aclk') || stristr(strtolower($decoded_origin_url), 'gclid') )
				return 'paid';

			if ( stristr($decoded_origin_url, 'utm_') )
			{
				$url = $decoded_origin_url;
				$url_parts = parse_url($url);
				parse_str($url_parts['query'], $path_parts);

				if ( isset($path_parts['adurl']) )
					return 'paid';

				if ( isset($path_parts['utm_medium']) )
				{
					if ( strtolower($path_parts['utm_medium']) == 'cpc' || strtolower($path_parts['utm_medium']) == 'ppc' || strtolower($path_parts['utm_medium']) == 'ysm' || strtolower($path_parts['utm_medium']) == 'msn-ppc' || strtolower($path_parts['utm_medium']) == 'yahoo-ppc' || strtolower($path_parts['utm_medium']) == 'bing-ppc' )
						return 'paid';

					if ( strtolower($path_parts['utm_medium']) == 'social' || strtolower($path_parts['utm_medium']) == 'facebook' || strtolower($path_parts['utm_medium']) == 'twitter' || strtolower($path_parts['utm_medium']) == 'linkedin' )
						return 'social';

					if ( strtolower($path_parts['utm_medium']) == 'email' || strtolower($path_parts['utm_medium']) == 'e-mail' || strtolower($path_parts['utm_medium']) == 'newsletter' )
						return 'email';
				}

				if ( isset($path_parts['utm_source']) )
				{
					if ( stristr(strtolower($path_parts['utm_source']), 'email')  || stristr(strtolower($path_parts['utm_source']), 'e-mail') || stristr(strtolower($path_parts['utm_source']), 'mailchimp') || stristr(strtolower($path_parts['utm_source']), 'constantcontact') || stristr(strtolower($path_parts['utm_source']), 'aweber') || stristr(strtolower($path_parts['utm_source']), 'icontact') || stristr(strtolower($path_parts['utm_source']), 'verticalresponse') || stristr(strtolower($path_parts['utm_source']), 'getresponse') ) 
						return 'email';
				}
			}

			return 'direct';
		}
	}

	function print_readable_source ( $source )
	{
		switch ( $source )
	    {
	    	case 'search' :
	    		return 'Organic Search';
	    	break;

	    	case 'social' :
	    		return 'Social Media';
	    	break;
	    
	    	case 'email' :
	    		return 'Email Marketing';
	    	break;

	    	case 'referral' :
	    		return 'Referral';
	    	break;

	    	case 'paid' :
	    		return 'Paid';
	    	break;

	    	case 'direct' :
	    		return 'Direct';
	    	break;
	    }
	}

}
?>