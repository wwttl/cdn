<?php
class WPJAM_Admin{
	public static function get_screen_id(){
		if(isset($_POST['screen_id'])){
			$screen_id	= $_POST['screen_id'];
		}elseif(isset($_POST['screen'])){
			$screen_id	= $_POST['screen'];
		}else{
			$ajax_action	= $_REQUEST['action'] ?? '';

			if($ajax_action == 'fetch-list'){
				$screen_id	= $_GET['list_args']['screen']['id'];
			}elseif($ajax_action == 'inline-save-tax'){
				$screen_id	= 'edit-'.sanitize_key($_POST['taxonomy']);
			}elseif(in_array($ajax_action, ['get-comments', 'replyto-comment'])){
				$screen_id	= 'edit-comments';
			}else{
				$screen_id	= false;
			}
		}

		if($screen_id){
			if('-network' === substr($screen_id, -8)){
				if(!defined('WP_NETWORK_ADMIN')){
					define('WP_NETWORK_ADMIN', true);
				}
			}elseif('-user' === substr($screen_id, -5)){
				if(!defined('WP_USER_ADMIN')){
					define('WP_USER_ADMIN', true);
				}
			}
		}

		return $screen_id;
	}

	public static function get_post_id(){
		if(isset($_GET['post'])){
			return (int)$_GET['post'];
		}elseif(isset($_POST['post_ID'])){
			return (int)$_POST['post_ID'];
		}else{
			return 0;
		}
	}

	public static function get_menu_hook($type='action'){
		if(is_network_admin()){
			$prefix	= 'network_';
		}elseif(is_user_admin()){
			$prefix	= 'user_';
		}else{
			$prefix	= '';
		}

		if($type == 'action'){
			return $prefix.'admin_menu';
		}else{
			return 'wpjam_'.$prefix.'pages';
		}
	}

	public static function get_referer(){
		$referer	= wp_get_original_referer() ?: wp_get_referer();
		$removable	= array_merge(wp_removable_query_args(), ['_wp_http_referer', 'action', 'action2', '_wpnonce']);

		return remove_query_arg($removable, $referer);
	}

	public static function get_setting($key='', $using_tab=false){
		$object = wpjam_get_current_var('plugin_page');

		if($object){
			$is_tab	= $object->function == 'tab';

			if(str_ends_with($key, '_name')){
				$using_tab	= $is_tab;
				$default	= $GLOBALS['plugin_page'];
			}else{
				$using_tab	= $using_tab ? $is_tab : false;
				$default	= null;
			}

			if($using_tab){
				$object	= wpjam_get_current_var('current_tab');
			}
		}

		if(!$object){
			return null;
		}

		return $key ? ($object->$key ?: $default) : $object->to_array();
	}

	protected static function get_nonce_action($key){
		$prefix	= $GLOBALS['plugin_page'] ?? $GLOBALS['current_screen']->id;

		return $prefix.'-'.$key;
	}

	public static function create_nonce($key){
		$action	= self::get_nonce_action($key);

		return wp_create_nonce($action);
	}

	public static function verify_nonce($key){
		$action	= self::get_nonce_action($key);
		$nonce	= wpjam_get_post_parameter('_ajax_nonce');

		return wp_verify_nonce($nonce, $action);
	}

	public static function tooltip($text, $tooltip){
		return '<div class="wpjam-tooltip">'.$text.'<div class="wpjam-tooltip-text">'.wpautop($tooltip).'</div></div>';
	}

	public static function add_screen_item($option, ...$args){
		$screen	= get_current_screen();

		if(!$screen){
			return;
		}

		$items	= $screen->get_option($option) ?: [];

		if(count($args) >= 2){
			$key	= $args[0];
			
			if(isset($items[$key])){
				return;	
			}

			$items[$key]	= $args[1];
		}else{
			$items[]		= $args[0];
		}

		$screen->add_option($option, $items);
	}

	public static function add_error($message='', $type='success'){
		if(is_wp_error($message)){
			$message	= $message->get_error_message();
			$type		= 'error';
		}

		if($message && $type){
			self::add_screen_item('admin_errors', ['message'=>$message, 'type'=>$type]);
		}
	}

	public static function init($plugin_page){
		$GLOBALS['plugin_page']	= $plugin_page;

		do_action('wpjam_admin_init');

		if($plugin_page){
			WPJAM_Menu_Page::render(false);
		}

		$screen_id	= self::get_screen_id();

		if($screen_id == 'upload'){
			$GLOBALS['hook_suffix']	= $screen_id;

			set_current_screen();
		}else{
			set_current_screen($screen_id);
		}
	}

	public static function delete_notice(){
		$key = wpjam_get_data_parameter('notice_key');

		if($key){
			wpjam_user_notice()->delete($key);

			if(current_user_can('manage_options')){
				wpjam_admin_notice()->delete($key);
			}

			wpjam_send_json(['notice_key'=>$key]);
		}
	}

	public static function filter_admin_url($url, $path, $blog_id=null, $scheme='admin'){
		if($path && is_string($path) && str_starts_with($path, 'page=')){
			$url	= get_site_url($blog_id, 'wp-admin/', $scheme);
			$url	.= 'admin.php?'.$path;
		}

		return $url;
	}

	public static function on_admin_menu(){
		do_action('wpjam_admin_init');

		WPJAM_Menu_Page::render();
	}

	public static function on_admin_notices(){
		$errors	= get_screen_option('admin_errors') ?: [];

		foreach($errors as $error){
			echo '<div class="notice notice-'.$error['type'].' is-dismissible"><p>'.$error['message'].'</p></div>';
		}

		self::delete_notice();

		$modal		= '';
		$notices	= wpjam_user_notice()->data;

		if(current_user_can('manage_options')){
			$notices	= array_merge($notices, wpjam_admin_notice()->data);
		}

		if($notices){
			uasort($notices, function($n, $m){ return $m['time'] <=> $n['time']; });
		}

		foreach($notices as $key => $notice){
			$notice = wp_parse_args($notice, [
				'type'		=> 'info',
				'class'		=> 'is-dismissible',
				'admin_url'	=> '',
				'notice'	=> '',
				'title'		=> '',
				'modal'		=> 0,
			]);

			$admin_notice	= trim($notice['notice']);

			if($notice['admin_url']){
				$admin_notice	.= $notice['modal'] ? "\n\n" : ' ';
				$admin_notice	.= '<a style="text-decoration:none;" href="'.add_query_arg(['notice_key'=>$key], home_url($notice['admin_url'])).'">点击查看<span class="dashicons dashicons-arrow-right-alt"></span></a>';
			}

			$admin_notice	= wpautop($admin_notice).wpjam_get_page_button('delete_notice', ['data'=>['notice_key'=>$key]]);

			if($notice['modal']){
				if(empty($modal)){	// 弹窗每次只显示一条
					$modal	= $admin_notice;
					$title	= $notice['title'] ?: '消息';

					echo '<div id="notice_modal" class="hidden" data-title="'.esc_attr($title).'">'.$modal.'</div>';
				}
			}else{
				echo '<div class="notice notice-'.$notice['type'].' '.$notice['class'].'">'.$admin_notice.'</div>';
			}
		}
	}

	public static function on_admin_init(){
		$plugin_page	= $_POST['plugin_page'] ?? null;

		self::init($plugin_page);
	}

	public static function on_current_screen($screen=null){
		if(wpjam_get_current_var('plugin_page')){
			WPJAM_Menu_Page::load($screen);
		}else{
			WPJAM_Builtin_Page::load($screen);
		}
	}

	public static function on_admin_enqueue_scripts(){
		$screen	= get_current_screen();

		if($screen->base == 'customize'){
			return;
		}elseif($screen->base == 'post'){
			wp_enqueue_media(['post'=>self::get_post_id()]);
		}else{
			wp_enqueue_media();
		}

		$ver	= get_plugin_data(WPJAM_BASIC_PLUGIN_FILE)['Version'];
		$static	= WPJAM_BASIC_PLUGIN_URL.'static';

		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');

		wp_enqueue_style('wpjam-style',		$static.'/style.css',	['wp-color-picker', 'editor-buttons'], $ver);
		wp_enqueue_script('wpjam-script',	$static.'/script.js',	['jquery', 'thickbox', 'wp-backbone', 'jquery-ui-sortable', 'jquery-ui-tooltip', 'jquery-ui-tabs', 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-ui-autocomplete', 'wp-color-picker'], $ver);
		wp_enqueue_script('wpjam-form',		$static.'/form.js',		['wpjam-script', 'mce-view'], $ver);

		$setting	= [
			'screen_base'	=> $screen->base,
			'screen_id'		=> $screen->id,
			'post_type'		=> $screen->post_type,
			'taxonomy'		=> $screen->taxonomy,
		];

		$params	= array_except($_REQUEST, wp_removable_query_args());
		$params	= array_except($params, ['page', 'tab', '_wp_http_referer', '_wpnonce']);
		$params	= array_filter($params, 'is_populated');

		if($GLOBALS['plugin_page']){
			$setting['plugin_page']	= $GLOBALS['plugin_page'];
			$setting['current_tab']	= $GLOBALS['current_tab'] ?? null;
			$setting['admin_url']	= $GLOBALS['current_admin_url'] ?? '';

			$query_data		= self::get_setting('query_data') ?: [];
			$_query_data	= self::get_setting('query_data', true) ?: [];
			$query_data		= array_merge($query_data, $_query_data);

			if($query_data){
				$params		= array_except($params, array_keys($query_data));

				$setting['query_data']	= array_map(function($query_item){
					return is_null($query_item) ? null : sanitize_textarea_field($query_item);
				}, $query_data);
			}
		}else{
			$setting['admin_url']	= set_url_scheme('http://'.$_SERVER['HTTP_HOST'].parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

			$args	= array_filter([
				'taxonomy'	=> $screen->taxonomy ? array_pull($params, 'taxonomy') : null,
				'post_type'	=> $screen->post_type ? array_pull($params, 'post_type') : null,
			]);

			if($args){
				$setting['admin_url']	= add_query_arg($args, $setting['admin_url']);
			}
		}

		if($params){
			if(isset($params['data'])){
				$params['data']	= urldecode($params['data']);
			}

			$params	= map_deep($params, 'sanitize_textarea_field');
		}

		$setting['params']	= $params ?: new stdClass();

		if(!empty($GLOBALS['wpjam_list_table'])){
			$setting['list_table']	= $screen->get_option('wpjam_list_table');
		}

		wp_localize_script('wpjam-script', 'wpjam_page_setting', $setting);
	}

	public static function on_admin_action_update(){
		// 为了实现多个页面使用通过 option 存储。这个可以放弃了，使用 AJAX + Redirect
		// 注册设置选项，选用的是：'admin_action_' . $_REQUEST['action'] hook，
		// 因为在这之前的 admin_init 检测 $plugin_page 的合法性

		$referer_origin	= parse_url(self::get_referer());

		if(!empty($referer_origin['query'])){
			$referer_args	= wp_parse_args($referer_origin['query']);

			if(!empty($referer_args['page'])){
				self::init($referer_args['page']);	// 实现多个页面使用同个 option 存储。
			}
		}
	}

	public static function on_plugins_loaded(){
		wpjam_register_page_action('delete_notice', [
			'button_text'	=> '删除',
			'tag'			=> 'span',
			'class'			=> 'hidden delete-notice',
			'callback'		=> [self::class, 'delete_notice'],
			'direct'		=> true,
		]);

		if($GLOBALS['pagenow'] == 'options.php'){
			add_action('admin_action_update',	[self::class, 'on_admin_action_update'], 9);
		}elseif(wp_doing_ajax()){
			if(self::get_screen_id()){
				add_action('admin_init',	[self::class, 'on_admin_init'], 9);

				wpjam_add_admin_ajax('wpjam-page-action',	['WPJAM_Admin_AJAX', 'page_action']);
				wpjam_add_admin_ajax('wpjam-upload',		['WPJAM_Admin_AJAX', 'upload']);
				wpjam_add_admin_ajax('wpjam-query',			['WPJAM_Admin_AJAX', 'query']);
			}
		}else{
			$menu_action	= self::get_menu_hook('action');

			add_action($menu_action,			[self::class, 'on_admin_menu'], 9);
			add_action('admin_notices',			[self::class, 'on_admin_notices']);
			add_action('admin_enqueue_scripts', [self::class, 'on_admin_enqueue_scripts'], 9);
			add_action('print_media_templates', ['WPJAM_Field',	'print_media_templates'], 9);

			add_filter('set-screen-option', function($status, $option, $value){
				trigger_error('filter::set-screen-option -- delete 2023-06-01');
				return isset($_GET['page']) ? $value : $status;
			}, 9, 3);
		}

		add_action('current_screen',	[self::class, 'on_current_screen'], 9);
		add_filter('admin_url',			[self::class, 'filter_admin_url'], 9, 4);
	}
}

class WPJAM_Admin_Load extends WPJAM_Register{
	public function is_available(...$args){
		if($this->type == 'plugin_page'){
			$plugin_page	= $args[0];
			$current_tab	= $args[1];

			if($this->plugin_page){
				if(is_callable($this->plugin_page)){
					return call_user_func($this->plugin_page, $plugin_page, $current_tab);
				}

				if(!wpjam_compare($plugin_page, (array)$this->plugin_page)){
					return false;
				}
			}

			if($this->current_tab){
				if(!$current_tab || !wpjam_compare($current_tab, (array)$this->current_tab)){
					return false;
				}
			}else{
				if($current_tab){
					return false;
				}
			}
		}elseif($this->type == 'builtin_page'){
			$screen	= $args[0];

			if($this->screen && is_callable($this->screen)){
				return call_user_func($this->screen, $screen);
			}

			foreach(['base', 'post_type', 'taxonomy'] as $key){
				if($this->$key && !wpjam_compare($screen->$key, (array)$this->$key)){
					return false;
				}
			}
		}

		return true;
	}

	public function load(...$args){
		if($this->is_available(...$args)){
			if($this->page_file){
				$files	= (array)$this->page_file;

				foreach($files as $file){
					if(is_file($file)){
						include $file;
					}
				}
			}

			if($this->callback){
				if(is_callable($this->callback)){
					return call_user_func_array($this->callback, $args);
				}
			}elseif($this->model){
				foreach(['load', $this->type.'_load'] as $method){
					if(method_exists($this->model, $method)){
						return call_user_func_array([$this->model, $method], $args);
					}
				}
			}
		}
	}

	protected static function get_config($key){
		if($key == 'orderby'){
			return true;
		}elseif($key == 'model'){
			return false;
		}
	}
}

class WPJAM_Admin_AJAX extends WPJAM_Args{
	public function callback(){
		$callback	= $this->callback;

		if(!$callback || !is_callable($callback)){
			wp_die('0', 400);
		}

		wpjam_send_json(wpjam_call($callback));
	}

	public static function add($name, $callback){
		$object	= new self(compact('callback'));

		add_action('wp_ajax_'.$name, [$object, 'callback']);
	}

	public static function page_action(){
		$action	= wpjam_get_post_parameter('page_action');
		$object	= WPJAM_Page_Action::get($action);

		if($object){
			return $object->callback();
		}

		wpjam_page_action_compact($action);
	}

	public static function upload(){
		$name	= wpjam_get_post_parameter('file_name');
		$nonce	= wpjam_get_post_parameter('_ajax_nonce');

		if(wp_verify_nonce($nonce, 'upload-'.$name)){
			return wpjam_upload($name, $relative=true);
		}else{
			return new WP_Error('invalid_nonce');
		}
	}

	public static function query(){
		$data_type	= wpjam_get_post_parameter('data_type');
		$object		= $data_type ? wpjam_get_data_type_object($data_type) : null;
		$items		= [];

		if($object){
			$args	= wpjam_get_post_parameter('query_args', ['default'=>[]]);
			$items	= $object->query_items($args) ?: [];

			if(is_wp_error($items)){
				$items	= [['label'=>$items->get_error_message(), 'value'=>$items->get_error_code()]];
			}
		}

		return ['items'=>$items];
	}
}

class WPJAM_Page_Action extends WPJAM_Register{
	protected function create_nonce(){
		return WPJAM_Admin::create_nonce($this->name);
	}

	protected function verify_nonce(){
		return WPJAM_Admin::verify_nonce($this->name);
	}

	public function parse_args(){
		return wp_parse_args($this->args, ['response'=>$this->name]);
	}

	public function is_allowed($type=''){
		$capability	= $this->capability ?? ($type ? 'manage_options' : 'read');

		return current_user_can($capability, $this->name);
	}

	public function callback(){
		$action_type	= wpjam_get_post_parameter('action_type');

		if($action_type == 'form'){
			$form	= $this->get_form();
			$width	= $this->width ?: 720;
			$modal	= $this->modal_id ?: 'tb_modal';
			$title	= wpjam_get_post_parameter('page_title');

			if(!$title){
				foreach(['page_title', 'button_text', 'submit_text'] as $key){
					if(!empty($this->$key) && !is_array($this->$key)){
						$title	= $this->$key;
						break;
					}
				}
			}

			return ['form'=>$form, 'width'=>$width, 'modal_id'=>$modal, 'page_title'=>$title];
		}

		if(!$this->verify_nonce()){
			return new WP_Error('invalid_nonce');
		}

		if(!$this->is_allowed($action_type)){
			return new WP_Error('access_denied');
		}

		$response	= ['type'=>$this->response];

		if($action_type == 'submit'){
			$submit_name	= wpjam_get_post_parameter('submit_name',	['default'=>$this->name]);
			$submit_button	= $this->get_submit_button($submit_name);

			if(!$submit_button){
				return new WP_Error('invalid_submit_button');
			}

			$callback	= $submit_button['callback'] ?: $this->callback;

			$response['type']	= $submit_button['response'];
		}else{
			$submit_name	= null;
			$callback		= $this->callback;
		}

		if(!$callback || !is_callable($callback)){
			return new WP_Error('invalid_callback');
		}

		if($this->validate){
			$data	= wpjam_get_data_parameter();
			$fields	= $this->get_fields();

			if($fields){
				$data	= wpjam_fields($fields)->validate($data);
			}

			$result	= wpjam_try($callback, $data, $this->name, $submit_name);
		}else{
			$result	= wpjam_try($callback, $this->name, $submit_name);
		}

		if(is_array($result)){
			$response	= array_merge($response, $result);
		}elseif($result === false || is_null($result)){
			$response	= new WP_Error('invalid_callback', ['返回错误']);
		}elseif($result !== true){
			if($this->response == 'redirect'){
				$response['url']	= $result;
			}else{
				$response['data']	= $result;
			}
		}

		return apply_filters('wpjam_ajax_response', $response);
	}

	public function get_button($args=[]){
		if(!$this->is_allowed()){
			return '';
		}

		$args	= array_merge($this->args, $args);
		$class	= $args['class'] ?? 'button-primary large';

		if(!empty($args['page_title'])){
			$title	= $args['page_title'];
		}else{
			$title	= $args['button_text'] ?? '保存';
		}

		$tag	= $args['tag'] ?? 'a';
		$attr	= [
			'title'	=> $title,
			'class'	=> $class.' wpjam-button',
			'style'	=> $args['style'] ?? '',
			'data'	=> [
				'action'	=> $this->name,
				'nonce'		=> $this->create_nonce(),
				'title'		=> $title,
				'data'		=> $args['data'] ?? [],
				'direct'	=> $args['direct'] ?? false,
				'confirm'	=> $args['confirm'] ?? false
			]
		];

		return wpjam_tag($tag, $attr, $args['button_text']);
	}

	public function get_fields(){
		$fields	= $this->fields;

		if($fields && is_callable($fields)){
			$fields	= wpjam_try($fields, $this->name);
		}

		return $fields ?: [];
	}

	public function get_data(){
		$data		= $this->data ?: [];
		$callback	= $this->data_callback;

		if($callback && is_callable($callback)){
			$_data	= wpjam_try($callback, $this->name, $this->get_fields());

			return array_merge($data, $_data);
		}

		return $data;
	}

	public function get_form(){
		if(!$this->is_allowed()){
			return '';
		}

		$button	= '';
		$fields	= $this->get_fields();
		$data	= $this->get_data();
		$fields	= wpjam_fields($fields, array_merge($this->args, ['data'=>$data, 'echo'=>false]));
		$form	= wpjam_tag('form', [
			'method'	=> 'post',
			'action'	=> '#',
			'id'		=> $this->form_id ?: 'wpjam_form',
			'data'		=> [
				'action'	=> $this->name,
				'nonce'		=> $this->create_nonce()
			]
		], $fields);

		foreach($this->get_submit_button() as $name => &$item){
			$button	.= get_submit_button($item['text'], $item['class'], $name, false);
		}

		if($button){
			$form->append('p', ['submit'], $button);
		}

		return $form;
	}

	protected function get_submit_button($name=null){
		if($name){
			$button	= $this->get_submit_button();

			return $button[$name] ?? [];
		}

		if(!is_null($this->submit_text)){
			$button	= $this->submit_text;

			if($button && is_callable($button)){
				$button	= wpjam_try($button, $this->name);
			}
		}else{
			$button = wp_strip_all_tags($this->page_title);
		}

		$button	= $button ?: [];
		$button	= is_array($button) ? $button : [$this->name=>$button];

		foreach($button as $name => &$item){
			$item	= is_array($item) ? $item : ['text'=>$item];
			$item	= wp_parse_args($item, ['response'=>$this->response, 'class'=>'primary', 'callback'=>'']);
		}

		return $button;
	}

	public static function get_nonce_action($key){	// 兼容
		return wpjam_get_nonce_action($key);
	}
}