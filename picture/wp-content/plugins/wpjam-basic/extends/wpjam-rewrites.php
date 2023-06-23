<?php
/*
Name: Rewrite 优化
URI: https://blog.wpjam.com/m/wpjam-rewrite/
Description: Rewrites 扩展让可以优化现有 Rewrites 规则和添加额外的 Rewrite 规则。
Version: 1.0
*/
if(is_admin() && did_action('current_screen') && $GLOBALS['plugin_page'] = 'wpjam-rewrites'){
	class WPJAM_Rewrites_Admin{
		public static function get_primary_key(){
			return 'rewrite_id';
		}

		public static function get_all(){
			return get_option('rewrite_rules') ?: [];
		}

		public static function get_rewrites(){
			return wpjam_basic_get_setting('rewrites', []);
		}

		public static function update_rewrites($rewrites){
			wpjam_basic_update_setting('rewrites', array_values($rewrites));
			flush_rewrite_rules();
			return true;
		}

		public static function is_added($id){
			if($current	= self::get($id)){
				foreach(self::get_rewrites() as $i => $rewrite){
					if($rewrite['regex'] == $current['regex']){
						return $i;
					}
				}
			}

			return false;
		}

		public static function get($id){
			$rewrites	= self::get_all();
			$regex_arr	= array_keys($rewrites);
			$i			= $id-1;

			if($regex = $regex_arr[$i] ?? ''){
				return ['rewrite_id'=>$id, 'regex'=>$regex, 'query'=>$rewrites[$regex]];
			}

			return [];
		}

		public static function validate_data($data, $id=''){
			if(empty($data['regex']) || empty($data['query'])){
				return new WP_error('error', 'Rewrite 规则不能为空');
			}

			if(is_numeric($data['regex'])){
				return new WP_error('error', '无效的 Rewrite 规则');
			}

			$rewrites	= self::get_all();

			if($id){
				$current	= self::get($id);

				if(empty($current)){
					return new WP_error('error', '该 Rewrite 规则不存在');
				}elseif($current['regex'] != $data['regex'] && isset($rewrites[$data['regex']])){
					return new WP_error('error', '该 Rewrite 规则已使用');
				}
			}else{
				if(isset($rewrites[$data['regex']])){
					return new WP_error('error', '该 Rewrite 规则已存在');
				}
			}

			return $data;
		}

		public static function insert($data){
			$data	= self::validate_data($data);

			if(is_wp_error($data)){
				return $data;
			}

			$rewrites	= self::get_rewrites();
			$rewrites	= array_merge([$data], $rewrites);

			self::update_rewrites($rewrites);

			return 1;
		}

		public static function update($id, $data){
			$data	= self::validate_data($data, $id);

			if(is_wp_error($data)){
				return $data;
			}

			$i	= self::is_added($id);

			if($i !== false){
				$rewrites		= self::get_rewrites();
				$rewrites[$i]	= $data;

				return self::update_rewrites($rewrites);
			}

			return true;
		}

		public static function bulk_delete($ids){
			$rewrites	= self::get_rewrites();

			foreach($ids as $id){
				$current	= self::get($id);

				if(empty($current)){
					return new WP_error('error', '该 Rewrite 规则不存在');
				}

				$i	= self::is_added($id);

				if($i !== false){
					$rewrites	= array_except($rewrites, $i);
				}
			}

			return self::update_rewrites($rewrites);
		}
		
		public static function delete($id){
			$current	= self::get($id);

			if(empty($current)){
				return new WP_error('error', '该 Rewrite 规则不存在');
			}

			$i	= self::is_added($id);

			if($i !== false){
				$rewrites	= self::get_rewrites();
				$rewrites	= array_except($rewrites, $i);

				return self::update_rewrites($rewrites);
			}

			return true;
		}

		public static function reset(){
			wpjam_basic_delete_setting('rewrites');
			flush_rewrite_rules();
			return true;
		}

		public static function query_items($limit, $offset){
			$items		= [];
			$rewrite_id	= 0;

			foreach(self::get_all() as $regex => $query) {
				$rewrite_id++;
				$items[]	= compact('rewrite_id', 'regex', 'query');
			}

			return ['items'=>$items, 'total'=>count($items)];
		}

		public static function render_item($item){
			$item['regex']	= wpautop($item['regex']);
			$item['query']	= wpautop($item['query']);

			return $item;
		}

		public static function get_actions(){
			return [
				'add'		=> ['title'=>'新建',	'first'=>true,	'response'=>'list'],
				'edit'		=> ['title'=>'编辑'],
				'delete'	=> ['title'=>'删除',	'direct'=>true,	'bulk'=>true,		'response'=>'list'],
				'reset'		=> ['title'=>'重置',	'direct'=>true,	'confirm'=>true,	'overall'=>'true',	'response'=>'list'],
			];
		}

		public static function get_fields($action_key='', $id=0){
			return [
				'regex'		=> ['title'=>'正则',		'type'=>'text',	'show_admin_column'=>true],
				'query'		=> ['title'=>'查询',		'type'=>'text',	'show_admin_column'=>true],
			];
		}

		public static function get_list_table(){
			return [
				'plural'		=> 'rewrites',
				'singular'		=> 'rewrite',
				'model'			=> self::class,
				'capability'	=> 'manage_rewrites',
				'per_page'		=> 300
			];
		}
	}
}else{
	class WPJAM_Rewrite extends WPJAM_Option_Model{
		public static function cleanup(&$rules){
			$unuse_rewrite_keys = ['comment-page', 'feed=', 'attachment'];

			foreach ($unuse_rewrite_keys as $i=>$unuse_rewrite_key) {
				if(self::get_setting('remove_'.$unuse_rewrite_key.'_rewrite') == false){
					unset($unuse_rewrite_keys[$i]);
				}
			}

			if(self::get_setting('disable_post_embed')){
				$unuse_rewrite_keys[]	= '&embed=true';
			}

			if(self::get_setting('disable_trackbacks')){
				$unuse_rewrite_keys[]	= '&tb=1';
			}

			if($unuse_rewrite_keys){
				foreach ($rules as $key => $rule) {
					if($rule == 'index.php?&feed=$matches[1]'){
						continue;
					}

					foreach ($unuse_rewrite_keys as $unuse_rewrite_key) {
						if(strpos($key, $unuse_rewrite_key) !== false || strpos($rule, $unuse_rewrite_key) !== false){
							unset($rules[$key]);
						}
					}
				}
			}
		}

		public static function filter_attachment_link($link, $post_id){
			return wp_get_attachment_url($post_id);
		}

		public static function on_generate_rewrite_rules($wp_rewrite){
			self::cleanup($wp_rewrite->rules); 
			self::cleanup($wp_rewrite->extra_rules_top);

			$rewrites = self::get_setting('rewrites');

			if($rewrites){
				$wp_rewrite->rules = array_merge(array_column($rewrites, 'query', 'regex'), $wp_rewrite->rules);
			}
		}

		public static function map_meta_cap($user_id, $args){
			if($args && !empty($args[0]) && WPJAM_Rewrites_Admin::is_added($args[0]) === false){
				return ['do_not_allow'];
			}else{
				return is_multisite() ? ['manage_sites'] : ['manage_options'];
			}
		}

		public static function get_fields(){
			return [
				'remove_date_rewrite'			=> ['type'=>'checkbox',	'description'=>'移除日期 Rewrite 规则'],
				'remove_comment_rewrite'		=> ['type'=>'checkbox',	'description'=>'移除留言 Rewrite 规则'],
				'remove_comment-page_rewrite'	=> ['type'=>'checkbox',	'description'=>'移除留言分页 Rewrite 规则'],
				'remove_feed=_rewrite'			=> ['type'=>'checkbox',	'description'=>'移除分类 Feed Rewrite 规则'],
				'remove_attachment_rewrite'		=> ['type'=>'checkbox',	'description'=>'移除附件 Rewrite 规则']
			];
		}

		public static function get_tabs(){
			$tabs	= [];

			if(!is_multisite() || !is_network_admin()){
				$tabs['rules']	= [
					'title'			=> 'Rewrite 规则',
					'tab_file'		=> __FILE__,
					'capability'	=> 'manage_rewrites',
					'map_meta_cap'	=> [self::class, 'map_meta_cap'],
					'function'		=> 'list',
					'list_table'	=> 'WPJAM_Rewrites_Admin',
				];

				flush_rewrite_rules();
			}

			$tabs['optimize']	= [
				'title'			=> 'Rewrites 优化',
				'summary'		=> '如果你的网站没有使用以下页面，可以移除相关功能的的 Rewrites 规则以提高网站效率！',
				'function'		=> 'option',
				'option_name'	=> 'wpjam-basic',
			];

			return $tabs;
		}

		public static function get_menu_page(){
			return [
				'parent'		=> 'wpjam-basic',
				'menu_slug'		=> 'wpjam-rewrites',
				'menu_title'	=> 'Rewrites',
				'summary'		=> __FILE__,
				'function'		=> 'tab',
				'tabs'			=> [self::class, 'get_tabs']
			];	
		}

		public static function init(){
			if(self::get_setting('remove_date_rewrite')){
				remove_rewrite_tag('%year%');
				remove_rewrite_tag('%monthnum%');
				remove_rewrite_tag('%day%');
				remove_rewrite_tag('%hour%');
				remove_rewrite_tag('%minute%');
				remove_rewrite_tag('%second%');
			}
		}

		public static function add_hooks(){
			if(self::get_setting('remove_date_rewrite')){
				add_filter('date_rewrite_rules', '__return_empty_array');
			}

			if(self::get_setting('remove_attachment_rewrite')){
				add_filter('attachment_link',	[self::class, 'filter_attachment_link'], 10, 2);
			}

			if(self::get_setting('remove_comment_rewrite')){
				add_filter('comments_rewrite_rules', '__return_empty_array');
			}

			add_action('generate_rewrite_rules',	[self::class, 'on_generate_rewrite_rules']);
		}
	}

	wpjam_register_option('wpjam-basic', [
		'plugin_page'	=> 'wpjam-rewrites',
		'current_tab'	=> 'optimize',
		'site_default'	=> true,
		'model'			=> 'WPJAM_Rewrite',
	]);
}