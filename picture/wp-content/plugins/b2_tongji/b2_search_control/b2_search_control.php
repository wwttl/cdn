<?php
use B2\Modules\Common\User;
if (!class_exists('tjb2search')) {
    class tjb2search{
        public function __construct(){
            add_action('wp_footer',function(){
                if(is_search()){
                ?>
                <div id="tjsearch"></div>
                <?php
                }
            });
            add_action('wp_enqueue_scripts', function(){
                if(is_search()){
                    wp_enqueue_script( 'tj_search',B2_TJ_URL.'b2_search_control/tj_search.js', array(), B2_TJ_VERSION , true );
                    $post_type = b2_get_search_type();
                    $type = isset($_GET['type']) && !empty($_GET['type']) ? esc_attr($_GET['type']) : 'post';
                    $key = get_search_query();
                    $setting_array = array(
                        'type' => $type,
                        'key' => $key,
                    );
                    wp_localize_script( 'tj_search', 'tj_search_data', $setting_array );
                }
            } ,99);
            add_action( 'rest_api_init', function(){
                register_rest_route('b2/v1','/tjsearch',array(
                    'methods'=>'post',
                    'callback'=>array(__CLASS__,'tj_search'),
                    'permission_callback' => '__return_true'
                ));
            });
        }
        
        public function tj_search($request){
            $res = self::tj_search_fun($request);
            if(isset($res['error'])){
                return new \WP_Error('tj_error',$res['error'],array('status'=>403));
            }else{
                return new \WP_REST_Response($res,200);
            } 
        }
        public function tj_search_fun($request){
            $type = $request['type'] ? esc_attr($request['type']) : '';
            $word = $request['key'] ? esc_attr($request['key']) : false;
            if(!$word) return array('error'=>__('无搜索词','b2'));
            $user_id = get_current_user_id();            
            if($word && !isCrawler()){
                if (!isset($_SESSION)) {
                    session_start();
                }
                $search_word = isset($_SESSION['search_word']) ? $_SESSION['search_word'] : array();
                if(!in_array($word,$search_word)){
                    $search_word[] = $word;
                    $_SESSION['search_word'] = $search_word;
                    global $wpdb;
                    $data = array(
                        'user' => $user_id,
                        'type' => $type,
                        'word' => esc_sql(esc_attr($word)),
                        'ip' => tj_get_user_ip(),
                    );
                    $table_name = $wpdb->prefix . 'Tj_Search';
                    $wpdb->insert( $table_name, $data);
                }
            }
            return 'success';
        }
    }
    new tjb2search();
}
add_action('cmb2_admin_init','add_Search_control_page','50');
function add_Search_control_page(){
    $login = new_cmb2_box(array(
        'id'           => 'b2_Search_control',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'b2_Search_control',
        'tab_group'    => 'b2_Search_options',
        'parent_slug'  => 'b2_tz_main_control',
        'menu_title'   => __('搜索管理','b2'),
        'display_cb'   => 'Search_function'
    ));
}
function Search_function() {
    $cardlisttable = new B2_Search_List_Table();
    $cardlisttable->prepare_items();
?>
  <div class="wrap">
	  <h1 class="wp-heading-inline">搜索管理</h1>
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
class B2_Search_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
              
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'id',    //singular name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );        
    }
  
    //默认的项目
    function column_default($item, $column_name){
        //var_dump($column_name);
        switch($column_name){
            case 'id':
            case 'date':
            case 'user':
            case 'type':
            case 'word':
            case 'ip':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }

    //编辑按钮
    function column_ID($item){
        //Build row actions
        $actions = array(
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s">删除</a>',$_REQUEST['page'],'delete',$item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s%2$s',
            $item['id'],
            $this->row_actions($actions)
        );
    }
    //批量操作回调
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            $item['id']                //The value of the checkbox should be the record's id
        );
    }
    function get_columns(){
        $columns = array(
            'cb'=> '<input type="checkbox" />', //选择框
            'id'=>'ID',
            'date'=>'搜索时间',
            'user'=>'搜索用户',
            'type'=>'搜索类型',
            'word'=>'搜索词',
            'ip'=>'IP',
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
        $actions = array(
            'delete'    => '删除'
        );
        return $actions;
    }
    function process_bulk_action() {
        if( 'delete'===$this->current_action() ) {
            global $wpdb; 
            $table_name = $wpdb->prefix . 'Tj_Search';
            if(is_array($_GET['id'])){
                foreach ($_GET['id'] as $key => $value) {
                    $wpdb->delete( $table_name, array( 'id' => $value ) );
                }
            }else{
                $wpdb->delete( $table_name, array( 'id' => $_GET['id'] ) );
            }
        }
    }
    function filter_data($data){
    	$type = array(
    		'post' => '文章',
    		'user' => '用户',
    		'shop' => '商品',
    		'document' => '帮助',
    		'' => '未定义',
    	);
        foreach($data as $key => $val){
            $user = User::get_user_public_data($val['user']);
            if(!isset($user["error"])){
            	$data[$key]['user'] = '<a href="'.$user['link'].'" target="_blank">'.$user['name'].'</a>';            	
            }else{
            		$data[$key]['user'] = '未登录';
            }
            $data[$key]['type'] = $type[$data[$key]['type']];
        }
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

        global $wpdb;
        $order = 'ORDER BY id DESC';
        $table_name = $wpdb->prefix . 'Tj_Search';
        $per_page = 10;
        $limit = $per_page;
        $paged = $current_page;
        $offset = ($paged-1)*$per_page;
        $pages = $wpdb->get_var( "SELECT count(*) FROM $table_name");
        $cards = $wpdb->get_results( "SELECT * FROM $table_name $order LIMIT $offset,$limit" ,ARRAY_A );
        $cards = $this->filter_data($cards);

        //排序方式
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id';
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
