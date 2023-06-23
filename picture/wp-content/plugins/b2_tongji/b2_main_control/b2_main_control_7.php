<?php
use B2\Modules\Common\User;
add_action('cmb2_admin_init','add_tz_main_control_7h_page','50');
function add_tz_main_control_7h_page(){
    $login = new_cmb2_box(array(
        'id'           => 'b2_tz_main_control_7',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'b2_tz_main_control_7',
        'tab_group'    => 'b2_tz_main_options',
        'parent_slug'  => '/admin.php?page=b2_tz_main_control',
        'tab_title'    => __('用户积分付费排行','b2'),
        'menu_title'   => __('用户积分付费排行','b2'),
        'display_cb'   => 'tz_main_function_7'
    ));
}
function tz_main_function_7($cmb_options) {
	$tabs = tj_cb_options_page_tabs( $cmb_options );
    $cardlisttable = new B2_paihang_a_List_Table7();
    $cardlisttable->prepare_items();
    $status = isset($_GET["status"]) ? esc_sql($_GET["status"]) : 'all';
?>
	<style>
	.column-comments, .column-links, .column-posts, .widefat .num {
	    text-align: left;
	}
	</style>
	<div class="wrap p-3 option-<?php echo $cmb_options->option_key; ?>">
	    <?php if ( get_admin_page_title() ) : ?>
	        <h2><?php echo wp_kses_post( get_admin_page_title() ); ?></h2>
	    <?php endif; ?>
	    <h2 class="nav-tab-wrapper">
	        <?php foreach ( $tabs as $option_key => $tab_title ) : ?>
	            <a class="nav-tab<?php if ( isset( $_GET['page'] ) && $option_key === $_GET['page'] ) : ?> nav-tab-active<?php endif; ?>" href="<?php menu_page_url( $option_key ); ?>"><?php echo wp_kses_post( $tab_title ); ?></a>
	        <?php endforeach; ?>
	    </h2>
	  <h1 class="wp-heading-inline">用户积分付费排行</h1>
	  <form id="movies-filter" method="get">
	    <?php 
	    	$cardlisttable->display() ?>
	  </form>
  </div>
<?php 
}
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class B2_paihang_a_List_Table7 extends WP_List_Table {
	function __construct(){
        global $status, $page;
              
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'TZ_post',    //singular name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );        
    }


    //默认的项目
    function column_default($item, $column_name){
        //var_dump($column_name);
        switch($column_name){
        	case 'num':
        	case 'user':
			case 'money':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }
    //编辑按钮
    function column_ID($item){
        //Build row actions
        $actions = array(
        );
        
        //Return the title contents
        return $item;
    }
    //批量操作回调
    function column_cb($item){
        return $item;
    }
    function get_columns(){
        $columns = array(
        	'num'=>'排名',
        	'user'=>'用户',
        	'money'=>'已消费',
        );
        return $columns;
    }
    function get_sortable_columns() {
        $sortable_columns = array(
            //'id'     => array('id',false),     //true means it's already sorted
        );
        return $sortable_columns;
    }
    function get_bulk_actions() {
        $actions = array();
        return $actions;
    }
    function process_bulk_action() {
        
    }
    function filter_data($data){
        $i = 1;
		foreach($data as $key => $val){
          $data[$key]['num'] = $i;

          $TZ_user = User::get_user_public_data($val['user_id']);
          $data[$key]['user'] = '<a href="'.$TZ_user['link'].'" target="_blank">'.$TZ_user['name'].'</a>';
          
		  $data[$key]['money'] = $val['a'];
		  $i++;
		}
		return $data;
    }

    function prepare_items() {
    	$status = isset($_GET["status"]) ? esc_sql($_GET["status"]) : '1';
        $query ="`money_type` = $status";

        global $wpdb; 
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);      
        $this->process_bulk_action();
        $current_page = $this->get_pagenum();

        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_order';

        $per_page = 20;
        $limit = $per_page;
        $paged = $current_page;
        $offset = ($paged-1)*$per_page;

        $cards = $wpdb->get_results( "SELECT user_id,sum(BINARY(order_total)) as a FROM $table_name WHERE {$query} and order_state = 'q' group by user_id order by  a  desc" ,ARRAY_A);

        $cards = $this->filter_data($cards);
		$pages = count($cards);
		
		$cards = array_slice($cards,($current_page-1)*$per_page,$per_page);
        //排序方式
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'tz_id';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order==='desc') ? -$result : $result;
        }
        //usort($cards, 'usort_reorder');

        //总数
        $this->items = $cards;
        $this->set_pagination_args( array(
            'total_items' => $pages,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($pages/$limit)   
        ) );
    }
}