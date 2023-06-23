<?php
add_action('cmb2_admin_init','add_paihang_a_control_page','50');
function add_paihang_a_control_page(){
    $login = new_cmb2_box(array(
        'id'           => 'b2_tz_main_control_3',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'b2_tz_main_control_3',
        'tab_group'    => 'b2_tz_main_options',
        'parent_slug'  => '/admin.php?page=b2_tz_main_control',
        'tab_title'    => __('文章下载次数排行','b2'),
        'menu_title'   => __('文章下载次数排行','b2'),
        'display_cb'   => 'b2_tz_main_control_3'
    ));
}
function b2_tz_main_control_3($cmb_options) {
	$tabs = tj_cb_options_page_tabs( $cmb_options );
    $cardlisttable = new B2_paihang_a_List_Table();
    $cardlisttable->prepare_items();
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
	  <h1 class="wp-heading-inline">近三天下载次数排行统计</h1>
	  <form id="movies-filter" method="get">
	    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    <?php 
	    	$cardlisttable->display() ?>
	  </form>
  </div>
<?php 
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class B2_paihang_a_List_Table extends WP_List_Table {
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
        	case 'post_id':
			case 'post_title':
			case 'i':
			case 'down_num':
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
        	'num'=>'下载排名',
        	'post_title'=>'文章标题',
        	'post_id'=>'文章ID',
			'i'=>'几号下载框',
			'down_num'=>'下载次数',
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
		$array = array();
		foreach($data as $key => $val){
		  foreach($val as $k => $v){
		    $array[$key.'_'.($k)] = $v;
		  }
		}
		arsort($array);
		$array_a = array();
		$i = 1;
		foreach($array as $key => $val){
		  $post_title = get_the_title($key);
		  $link = get_permalink($key);
		  $post_i = explode('_',$key);
		  $array_a[$key]['post_id'] = $post_i[0];
		  $array_a[$key]['post_title'] = '<a href="'.$link.'" target="_blank">'.$post_title.'</a>';
		  $array_a[$key]['num'] = $i;
		  $array_a[$key]['i'] = $post_i[1];
		  $array_a[$key]['down_num'] = $val;
		  $i++;
		}
		$data = ($array_a);
		return $data;
    }

    function prepare_items() {
        global $wpdb; 
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);      
        $this->process_bulk_action();
        $current_page = $this->get_pagenum();
		$time = new TZ_control();
		$time = ($time -> tz_get_time())["a"];
        global $wpdb;
        $table_name = $wpdb->prefix . 'TZ_download';
        $per_page = 10;
        $limit = $per_page;
        $paged = $current_page;
        $offset = ($paged-1)*$per_page;
        //$pages = $wpdb->get_var( "SELECT count(*) FROM $table_name");
        $cards = $wpdb->get_results( "SELECT * FROM $table_name WHERE TZ_date > '$time[3]'" ,ARRAY_A);
        $array_a = array();
        foreach($cards as $k => $v){
			$array_a[$v["TZ_post"]][$v["TZ_index"]]  = isset($array_a[$v["TZ_post"]][$v["TZ_index"]] ) ? $array_a[$v["TZ_post"]][$v["TZ_index"]] +1 : 1;
        }
        $cards = $array_a;
        $cards = $this->filter_data($cards);
		$pages = count($cards);
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