<?php if(!defined('ABSPATH') || !defined('INBOUNDROCKET_PATH')) die('Security');

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once(ABSPATH . 'wp-admin/includes/template.php' );

if( ! class_exists('Welcome_Mat_List_Table') ) {
	
	class Welcome_Mat_List_Table extends WP_List_Table {
		    
		function __construct() {
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => __( 'Welcome Mat', 'inbound-rocket' ),     //singular name of the listed records
            'plural'    => __( 'Welcome Mats', 'inbound-rocket' ),   //plural name of the listed records
            'ajax'      => false,        							//does this table support ajax?
            'screen'    => 'welcome-mat-list'        //hook suffix
        ) );
    }
    	
    public function column_default($item, $column_name) {
        switch($column_name){
			case 'box_id':
				return $item['ID'];
			case 'box_title':
    		case 'publish_date':
    			$format_date = date('F d, Y', strtotime($item['post_date']));
				return $format_date;
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
	
    function column_box_title($item) {
  		$delete_nonce = wp_create_nonce( 'irwm_delete_welcome_mat' );
  		$title = $item['post_title'];
  		$editlink = admin_url('post.php?post='.$item['ID'].'&action=edit');
		
		//Return the title contents
		return sprintf('<a href="%s"><strong>%s</strong></a>', $editlink, $title);
    }
    
    function column_wm($item) {
		$delete_nonce = wp_create_nonce( 'irwm_delete_welcome_mat' );
		$editlink = admin_url('post.php?post='.$item['ID'].'&action=edit');
		$actions = array(
			'edit'      => '<a href="'.$editlink.'">'.__('Edit', 'inbound-rocket') .'</a>',
			'delete'	=> sprintf('<a href="?page=%s&action=%s&tab=%s&welcome-mat=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', $_REQUEST['tab'], absint( $item['ID'] ), $delete_nonce )
		);
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" /> %s', $item['ID'], $this->row_actions($actions));
    }
	
    function get_columns() {
        $columns = array(
			'wm'   			=> '<input type="checkbox" />',
            'box_id'		=> __( 'ID', 'inbound-rocket' ),
            'box_title' 	=> __( 'Title', 'inbound-rocket'),
            'publish_date' 	=> __( 'Publish Date', 'inbound-rocket')
		);
        return $columns;
    }
	   
    function get_sortable_columns() {
        $sortable_columns = array(
            'box_id'		=> array('ID', false),
      		'box_title'		=> array('post_title', false),
      		'publish_date'	=> array('post_date', false)
        );
        return $sortable_columns;
    }
    
    public static function get_welcome_mat( $per_page = 10, $page_number = 1) {
	    global $wpdb;
	    $sql = "SELECT * FROM $wpdb->posts WHERE $wpdb->posts.post_type = 'ir-welcome-mat' AND $wpdb->posts.post_status IN ('publish','draft')";
	    if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
    		$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
  		}
  		$sql .= " LIMIT ".intval($per_page);
  		$sql .= ' OFFSET ' . (intval($page_number) - 1) * intval($per_page);
		
  		$result = $wpdb->get_results($sql, ARRAY_A);
  		return $result;
    }
	
  	public static function delete_welcome_mat( $id ) {
		if(!is_admin()) return false;
  		return wp_trash_post($id);	
  	}
  	
  	public static function record_count() {
  		global $wpdb;
  		$sql = "SELECT COUNT(*) FROM $wpdb->posts WHERE $wpdb->posts.post_type = 'ir-welcome-mat' AND $wpdb->posts.post_status IN ('publish','draft')";
  		return $wpdb->get_var( $sql );
  	}
  	
  	public function no_items() {
  		_e( 'No Welcome Mats found.', 'inbound-rocket' );
  	}
  	
    function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => 'Delete'
        );
        return $actions;
    }
  	
    public function process_bulk_action() {
  	    
      if( 'delete' == $this->current_action() ) {
  	    $nonce = esc_attr( $_REQUEST['_wpnonce'] );
  	    if ( ! wp_verify_nonce( $nonce, 'irwm_delete_welcome_mat' ) ) {
  			die( 'Access denied.' );
    		} else {
  			self::delete_welcome_mat( absint( $_GET['welcome-mat'] ) );
  			$deleted = absint( $_GET['welcome-mat'] );
  			echo '<script>document.location.href=\''.add_query_arg(array('deleted'=>$deleted),admin_url('admin.php?page=inboundrocket_settings&tab=inboundrocket_wm_options') ).'\'</script>';
  			exit;
    		}
      }
  	    	    
      if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {
  			$delete_ids = esc_sql( $_POST['bulk-delete'] );
  			$deleted = 0;
  		    foreach ( $delete_ids as $id ) {
  		      self::delete_welcome_mat( absint( $id ) );
  			  $deleted++;
  		    }
  		
  		    echo '<script>document.location.href=\''.add_query_arg(array('deleted'=>$deleted),admin_url('admin.php?page=inboundrocket_settings&tab=inboundrocket_wm_options') ).'\'</script>';
  			exit;
  		}
  	}

    public function prepare_items() {
      
      $this->_column_headers = $this->get_column_info();
      
      /* Process bulk action */
      $this->process_bulk_action();
      
      $per_page 	= $this->get_items_per_page( 'welcome_mat_per_page', 10 );
      $current_page = $this->get_pagenum();
      $total_items = self::record_count();
      $this->set_pagination_args( array(
    		'total_items' => $total_items, //WE have to calculate the total number of items
    		'per_page'    => $per_page //WE have to determine how many items to show on a page
    	) );
  	
  	  $this->items = self::get_welcome_mat( $per_page, $current_page );
    }
  }
} // end class exists check	