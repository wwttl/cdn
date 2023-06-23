<?php
add_action('cmb2_admin_init','add_tz_main_control_h_page4','50');
function add_tz_main_control_h_page4(){
    $login = new_cmb2_box(array(
        'id'           => 'b2_tz_main_control_4',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'b2_tz_main_control_4',
        'tab_group'    => 'b2_tz_main_options',
        'parent_slug'  => '/admin.php?page=b2_tz_main_control',
        'tab_title'    => __('下载付费排行','b2'),
        'menu_title'   => __('下载付费排行','b2'),
        'display_cb'   => 'tz_main_function_4'
    ));
}
function tz_main_function_4($cmb_options) {
	$tabs = tj_cb_options_page_tabs( $cmb_options );
    $cardlisttable = new B2_paihang_a_List_Table4();
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
	  <h1 class="wp-heading-inline">今日下载付费排行</h1>
	  <form id="movies-filter" method="get">
	  	<ul class="subsubsub">
            <li class="all"><a href="<?php echo home_url('/wp-admin/admin.php?page=b2_tz_main_control_4&status=all'); ?>" class="<?php echo $status === 'all' ? 'current' : ''; ?>"><?php echo __('所有','b2'); ?></a> |</li>
            <li class="money"><a href="<?php echo home_url('/wp-admin/admin.php?page=b2_tz_main_control_4&status=m'); ?>" class="<?php echo $status === 'm' ? 'current' : ''; ?>"><?php echo __('现金资源','b2'); ?></a> |</li>
            <li class="credit"><a href="<?php echo home_url('/wp-admin/admin.php?page=b2_tz_main_control_4&status=c'); ?>" class="<?php echo $status === 'c' ? 'current' : ''; ?>"><?php echo __('积分资源','b2'); ?></a></li>
        </ul>
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
class B2_paihang_a_List_Table4 extends WP_List_Table {
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
        	case 'post_title':
        	case 'post_id':
			case 'post_i':
			case 'type':
			case 'pay_num':
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
        	'post_i'=>'几号资源',
			'type'=>'资源类型',
			'pay_num'=>'购买次数',
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
		    $array[$val["post_id"].'_'.($val["money_type"]).'_'.$val['order_key']] = isset($array[$val["post_id"].'_'.($val["money_type"]).'_'.$val['order_key']]) ? $array[$val["post_id"].'_'.($val["money_type"]).'_'.$val['order_key']] +1 : 1;
		}
		arsort($array);
		$array_a = array();
		$i = 1;
		foreach($array as $key => $val){
		  $post = explode('_',$key);
		  $post_title = get_the_title($post[0]);
		  $link = get_permalink($post[0]);
		  $array_a[$key]['post_id'] = $post[0];
		  $array_a[$key]['post_title'] = '<a href="'.$link.'" target="_blank">'.$post_title.'</a>';
		  $array_a[$key]['num'] = $i;
		  $array_a[$key]['post_i'] = $post[2]+1;
		  $array_a[$key]['type'] = $post[1]=='0' ? '现金资源' : '积分资源';
		  $array_a[$key]['pay_num'] = $val;
		  $i++;
		}
		$data = ($array_a);
		return $data;
    }

    function prepare_items() {
    	$status = isset($_GET["status"]) ? esc_sql($_GET["status"]) : 'all';
    	if (!empty($status) && $status != 'all') {
    		if($status=='m'){
    			$status = '0';
    		}elseif($status=='c'){
    			$status = '1';
    		}
            $query ="`money_type` = $status and ";
        }else{
        	$query = '';
        }
        global $wpdb; 
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);      
        $this->process_bulk_action();
        $current_page = $this->get_pagenum();
		$time = new TZ_control();
		$time = ($time -> tz_get_time())["a"][0];
        global $wpdb;
        $table_name = $wpdb->prefix . 'zrz_order';
        $per_page = 10;
        $limit = $per_page;
        $paged = $current_page;
        $offset = ($paged-1)*$per_page;
        $cards = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE {$query}order_type = 'x' and order_state = 'q' and order_date > '{$time}'" ,ARRAY_A);
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