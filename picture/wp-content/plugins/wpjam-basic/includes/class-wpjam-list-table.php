<?php
if(!class_exists('WP_List_Table')){
	include ABSPATH.'wp-admin/includes/class-wp-list-table.php';
}

class WPJAM_List_Table extends WP_List_Table{
	use WPJAM_Call_Trait;

	public function __construct($args=[]){
		$this->_args	= $args	= wp_parse_args($args, [
			'title'			=> '',
			'plural'		=> '',
			'singular'		=> '',
			'data_type'		=> 'model',
			'capability'	=> 'manage_options',
			'per_page'		=> 50
		]);

		$primary_key	= $this->get_primary_key_by_model();

		if($primary_key){
			$args['primary_key']	= $primary_key;
		}

		$GLOBALS['wpjam_list_table']	= $this;

		parent::__construct($this->parse_args($args));
	}

	public function __get($name){
		if(in_array($name, $this->compat_fields, true)){
			return $this->$name;
		}elseif(isset($this->_args[$name])){
			return $this->_args[$name];
		}
	}

	public function __isset($name){
		return $this->$name !== null;
	}

	public function __set($name, $value){
		if(in_array($name, $this->compat_fields, true)){
			return $this->$name	= $value;
		}else{
			$this->_args	= $this->_args ?? [];

			return $this->_args[$name]	= $value;
		}
	}

	public function __call($method, $args){
		if(in_array($method, $this->compat_methods, true)){
			return $this->$method(...$args);
		}elseif($method == 'get_query_id'){
			if($this->current_action()){
				return null;
			}

			return wpjam_get_parameter('id', ['sanitize_callback'=>'sanitize_text_field']);
		}elseif(str_ends_with($method, '_by_locale')){
			$method	= wpjam_remove_postfix($method, '_by_locale');

			return wpjam_call([$GLOBALS['wp_locale'], $method], ...$args);
		}elseif(str_ends_with($method, '_by_model')){
			$method	= wpjam_remove_postfix($method, '_by_model');

			if(method_exists($this->model, $method)){
				return wpjam_call([$this->model, $method], ...$args);
			}

			$fallback	= [
				'render_item'	=> 'item_callback',
				'get_subtitle'	=> 'subtitle',
				'get_views'		=> 'views',
				'query_items'	=> 'list',
			];

			if(isset($fallback[$method]) && method_exists($this->model, $fallback[$method])){
				return wpjam_call([$this->model, $fallback[$method]], ...$args);
			}

			if(in_array($method, [
				'render_item',
				'render_date'
			])){
				return $args[0];
			}elseif(in_array($method, [
				'get_subtitle',
				'get_views',
				'get_fields',
				'extra_tablenav',
				'before_single_row',
				'after_single_row',
			])){
				return null;
			}else{
				if(method_exists($this->model, '__callStatic')){
					$result	= wpjam_call([$this->model, $method], ...$args);
				}else{
					$result	= new WP_Error('undefined_method', [$this->model.'->'.$method.'()']);
				}

				if(is_wp_error($result)){
					if(in_array($method, [
						'get_filterable_fields',
						'get_searchable_fields',
						'get_primary_key',
						'col_left',
					])){
						return null;
					}
				}

				return $result;
			}
		}
	}

	protected function parse_args($args){
		$this->screen	= $args['screen'] = get_current_screen();
		$this->_args	= $this->_args ?? [];
		$this->_args	= array_merge($this->_args, $args);

		$this->add_screen_item('ajax', true);
		$this->add_screen_item('form_id', 'list_table_form');
		$this->add_screen_item('query_id', $this->get_query_id());
		$this->add_screen_item('left_key', $this->left_key);

		if(is_array($this->per_page)){
			add_screen_option('per_page', $this->per_page);
		}

		if($this->style){
			wp_add_inline_style('list-tables', $this->style);
		}

		$data_type = $this->data_type;

		if($data_type){
			add_screen_option('data_type', $data_type);

			if(in_array($data_type, ['post_type', 'taxonomy']) && $this->$data_type && !$this->screen->$data_type){
				$this->screen->$data_type	= $this->$data_type;
			}
		}

		$object		= wpjam_get_data_type_object($data_type);
		$meta_type	= $object ? $object->get_meta_type($args) : '';

		if($meta_type){
			add_screen_option('meta_type', $meta_type);
		}

		$args['views']	= [];

		foreach($this->get_objects('view') as $key => $object){
			$view	= $object->get_link();

			if($view && is_array($view)){
				$view	= $view['label'] ? $this->get_filter_link($view['filter'], $view['label'], $view['class']) : null;
			}

			if($view){
				$args['views'][$key]	= $view;
			}
		}

		$args['row_actions']	= $args['bulk_actions']	= $args['overall_actions']	= $next_actions = [];

		foreach($this->get_objects('action') as $key => $object){
			$object->primary_key	= $this->primary_key;
			$object->model			= $this->model;
			$object->capability		= $object->capability ?? $this->capability;
			$object->page_title		= $object->page_title ?? ($object->title ? wp_strip_all_tags($object->title.$this->title) : '');
			$object->data_type		= $data_type;

			if($data_type && $data_type != 'model'){
				$object->$data_type	= $this->$data_type ?: '';
			}

			if($object->overall){
				if(!$object->response){
					$object->response	= 'list';
				}

				$args['overall_actions'][]	= $key;
			}else{
				if(is_null($object->response)){
					$object->response	= $key;
				}

				if($object->bulk && $object->is_allowed()){
					$args['bulk_actions'][$key]	= $object;
				}

				if($object->next && $object->response == 'form'){
					$next_actions[$key]	= $object->next;
				}

				if($key == 'add'){
					if($this->layout == 'left'){
						$args['overall_actions'][]	= $key;
					}
				}else{
					if(is_null($object->row_action) || $object->row_action){
						$args['row_actions'][$key]	= $key;
					}
				}
			}
		}

		foreach($next_actions as $prev => $next){
			unset($args['row_actions'][$next]);

			$next_object	= $this->get_object($next);

			if($next_object && !$next_object->prev){
				$next_object->prev	= $prev;
			}
		}

		$this->add_screen_item('bulk_actions', $args['bulk_actions']);

		$args['columns']	= $args['sortable_columns'] = [];
		$filterable_fields	= $this->get_filterable_fields_by_model();

		foreach($this->get_objects('column') as $object){
			$key	= $object->name;

			if(is_null($object->filterable)){
				$object->filterable	= $filterable_fields && in_array($key, $filterable_fields);
			}

			$args['columns'][$key]	= $object->column_title ?? $object->title;

			if($object->sortable_column){
				$args['sortable_columns'][$key] = [$key, true];
			}

			$object->add_style();
		}

		add_shortcode('filter',		[$this, 'shortcode_callback']);
		add_shortcode('row_action',	[$this, 'shortcode_callback']);

		return $args;
	}

	protected function add_screen_item($key, $item){
		wpjam_add_screen_item('wpjam_list_table', $key, $item);
	}

	protected function register_action($name, $args, $defaults=[]){
		return wpjam_register_list_table_action($name, wp_parse_args($args, $defaults));
	}

	protected function register_view($name, $view){
		return wpjam_register_list_table_view($name, $view);
	}

	protected function register_column($name, $field){
		if(!empty($field['show_admin_column'])){
			$field	= wpjam_strip_data_type($field);
			$field	= array_except($field, 'style');
			$field	= wp_parse_args($field, ['order'=>10.5]);

			return wpjam_register_list_table_column($name, $field);
		}
	}

	protected function get_objects($type='action'){
		if($type == 'action'){
			if($this->sortable){
				$sortable	= is_array($this->sortable) ? $this->sortable : ['items'=>' >tr'];
				$action		= array_pull($sortable, 'action', []);

				$this->register_action('move',	$action, ['direct'=>true,	'page_title'=>'拖动',	'dashicon'=>'move']);
				$this->register_action('up',	$action, ['direct'=>true,	'page_title'=>'向上移动',	'dashicon'=>'arrow-up-alt']);
				$this->register_action('down',	$action, ['direct'=>true,	'page_title'=>'向下移动',	'dashicon'=>'arrow-down-alt']);
				$this->add_screen_item('sortable', $sortable);
			}

			if(isset($this->actions)){
				$actions	= $this->actions;
			}elseif(method_exists($this->model, 'get_actions')){
				$actions	= call_user_func([$this->model, 'get_actions']);
			}else{
				$actions	= $this->_builtin ? [] : [
					'add'		=> ['title'=>'新建',	'dismiss'=>true],
					'edit'		=> ['title'=>'编辑'],
					'delete'	=> ['title'=>'删除',	'direct'=>true,	'confirm'=>true,	'bulk'=>true],
				];
			}

			foreach(array_wrap($actions) as $key => $action){
				$this->register_action($key, $action, ['order'=>10.5]);
			}

			$data_type	= $this->data_type;
			$meta_type	= get_screen_option('meta_type');

			if($meta_type){
				$args	= ['list_table'=>true];

				if($data_type && in_array($data_type, ['post_type', 'taxonomy'])){
					$args[$data_type]	= $this->$data_type;
				}

				foreach(wpjam_get_meta_options($meta_type, $args) as $name => $option){
					$action_name	= $option->action_name ?: 'set_'.$name;

					if(!$this->get_object($action_name)){
						$this->register_action($action_name, $option->parse_list_table_args());
					}
				}
			}
		}elseif($type == 'column'){
			$fields	= $this->get_fields_by_model() ?: [];

			foreach($fields as $key => $field){
				$this->register_column($key, $field);

				if(wpjam_get_fieldset_type($field) == 'single'){
					foreach($field['fields'] as $sub_key => $sub_field){
						$this->register_column($sub_key, $sub_field);
					}
				}
			}
		}elseif($type == 'view'){
			$views	= $this->get_views_by_model() ?: [];

			foreach($views as $key => $view){
				$this->register_view($key, $view);
			}
		}

		return wpjam_call(['WPJAM_List_Table_'.$type, 'get_registereds'], wpjam_slice_data_type($this->_args));
	}

	protected function get_object($name, $type='action'){
		return wpjam_call(['WPJAM_List_Table_'.$type, 'get'], $name, $this->_args);
	}

	public function shortcode_callback($attrs, $title, $tag){
		if($tag == 'filter'){
			$class	= array_pull($attrs, 'class', []);

			return $this->get_filter_link($attrs, $title, $class);
		}elseif($tag == 'row_action'){
			$name	= array_pull($attrs, 'name');

			if($title){
				$attrs['title']	= $title;
			}

			if(isset($attrs['data'])){
				$attrs['data']	= wp_parse_args($attrs['data']);
			}

			return $this->get_row_action($name, $attrs);
		}
	}

	protected function do_shortcode($content, $id){
		// remove_all_shortcodes();
		return do_shortcode(str_replace('[row_action ', '[row_action id="'.$id.'" ', $content));
	}

	protected function get_row_actions($id){
		$row_actions	= [];

		foreach($this->row_actions as $key){
			$row_actions[$key] = $this->get_row_action($key, ['id'=>$id]);
		}

		return array_filter($row_actions);
	}

	public function get_row_action($action, $args=[]){
		$object = $this->get_object($action);

		return $object ? $object->get_row_action($args, $this->layout) : '';
	}

	public function get_filter_link($filter, $label, $class=[]){
		$query_args	= $this->query_args ?: [];

		foreach($query_args as $query_arg){
			if(!array_key_exists($query_arg, $filter)){
				$filter[$query_arg]	= wpjam_get_data_parameter($query_arg);
			}
		}

		$filter		= $filter ?: new stdClass();
		$class		= (array)$class;
		$class[]	= 'list-table-filter';

		return wpjam_tag('a', [
			'title'		=> wp_strip_all_tags($label, true),
			'class'		=> $class,
			'data'		=> ['filter'=>$filter],
			'newline'	=> true,
		], $label);
	}

	public function get_single_row($id){
		return wpjam_ob_get_contents([$this, 'single_row'], $id);
	}

	public function single_row($raw_item){
		$raw_item	= $this->parse_item($raw_item);

		if(empty($raw_item)){
			return;
		}

		$this->before_single_row_by_model($raw_item);

		$item	= $this->render_item($raw_item);
		$attr	= [];

		$attr['class']	= !empty($item['class']) ? (array)$item['class'] : [];
		$attr['style']	= $item['style'] ?? '';

		if($this->primary_key){
			$id	= str_replace('.', '-', $raw_item[$this->primary_key]);

			$attr['id']		= $this->singular.'-'.$id;
			$attr['data']	= ['id'=>$id];

			if($this->multi_rows){
				$attr['class'][]	= 'tr-'.$id;
			}
		}

		$callback	= wpjam_parse_method($this, 'single_row_columns');

		echo wpjam_tag('tr', $attr, wpjam_ob_get_contents($callback, $item));

		$this->after_single_row_by_model($item, $raw_item);
	}

	protected function parse_item($item){
		if(!is_array($item)){
			$result	= $this->get_by_model($item);
			$item 	= is_wp_error($result) ? null : $result;
			$item	= $item ? (array)$item : $item;
		}

		return $item;
	}

	protected function render_item($raw_item){
		$item	= (array)$raw_item;

		if($this->primary_key){
			$id	= $item[$this->primary_key];

			$item['row_actions']	= $this->get_row_actions($id);

			if($this->primary_key == 'id'){
				$item['row_actions']['id']	= 'ID：'.$id;
			}
		}

		return $this->render_item_by_model($item);
	}

	protected function get_column_value($id, $name, $value=null){
		$object	= $this->get_object($name, 'column');

		if($object){
			if(is_null($value)){
				if(method_exists($this->model, 'value_callback')){
					$value	= wpjam_value_callback([$this->model, 'value_callback'], $name, $id);
				}else{
					$value	= $object->default;
				}
			}

			$value	= $object->callback($id, $value);
		}

		if(is_array($value)){
			$wrap	= array_get($value, 'wrap');

			if(isset($value['row_action'])){
				$action	= array_get($value, 'row_action');
				$args	= array_get($value, 'args', []);
				$value	= $this->get_row_action($action, array_merge($args, ['id'=>$id]));
			}elseif(isset($value['filter'])){
				$filter	= array_get($value, 'filter', []);
				$label	= array_get($value, 'label');
				$class	= array_get($value, 'class', []);
				$value	= $this->get_filter_link($filter, $label, $class);
			}elseif(isset($value['items'])){
				$items	= array_get($value, 'items', []);
				$args	= array_get($value, 'args', []);
				$value	= $this->render_column_items($id, $items, $args);
			}else{
				trigger_error(var_export($value, true));
				$value	= '';
			}

			return $wrap ? wpjam_wrap($value, $wrap) : $value;
		}

		return $this->_builtin ? $value : $this->do_shortcode($value, $id);
	}

	public function column_default($item, $name){
		$value	= $item[$name] ?? null;
		$id		= $this->primary_key ? $item[$this->primary_key] : null;

		return $id ? $this->get_column_value($id, $name, $value) : $value;
	}

	public function column_cb($item){
		if($this->primary_key){
			$id	= $item[$this->primary_key];

			if($this->capability == 'read' || current_user_can($this->capability, $id)){
				$column	= $this->get_primary_column_name();
				$name	= isset($item[$column]) ? strip_tags($item[$column]) : $id;
				$cb_id	= 'cb-select-'.$id;
				$label	= wpjam_tag('label', ['for'=>$cb_id, 'class'=>'screen-reader-text'], '选择'.$name);
				$input	= wpjam_tag('input', ['type'=>'checkbox', 'name'=>'ids[]', 'value'=>$id, 'id'=>$cb_id]);

				return $label.$input;
			}
		}

		return wpjam_tag('span', ['dashicons', 'dashicons-minus']);
	}

	public function render_column_items($id, $items, $args=[]){
		$item_type	= $args['item_type'] ?? 'image';
		$item_key	= $args[$item_type.'_key'] ?? $item_type;
		$max_items	= $args['max_items'] ?? 0;
		$per_row	= $args['per_row'] ?? 0;
		$sortable	= $args['sortable'] ?? 0;
		$width		= $args['width'] ?? 60;
		$height		= $args['height'] ?? 60;
		$style		= (array)($args['style'] ?? []);

		$add_item	= $args['add_item'] ?? 'add_item';
		$edit_item	= $args['edit_item'] ?? 'edit_item';
		$move_item	= $args['move_item'] ?? 'move_item';
		$del_item	= $args['del_item'] ?? 'del_item';

		$rendered	= wpjam_tag();

		foreach($items as $i => $item){
			$color	= $item['color'] ?? null;
			$data	= compact('i');
			$args	= ['id'=>$id, 'data'=>$data];
			$attr	= ['id'=>'item_'.$i, 'data'=>$data, 'class'=>'item'];

			if($item_type == 'image'){
				$image	= $item[$item_key] ? wpjam_get_thumbnail($item[$item_key], $width*2, $height*2) : '';
				$image	= $image ? wpjam_tag('img', ['src'=>$image, 'width'=>$width, 'height'=>$height]) : ' ';
				$item	= $image.(!empty($item['title']) ? wpjam_tag('span', ['item-title'], $item['title']) : '');
				$attr	+= ['style'=>'width:'.$width.'px;'];
			}else{
				$item	= $item[$item_key] ?: ' ';
			}

			$item	= $this->get_row_action($move_item,	$args+[
				'class'		=> 'move-item '.$item_type,
				'style'		=> ['color'=>$color],
				'title'		=> $item
			]).wpjam_tag('span', ['row-actions'], $this->get_row_action($move_item, $args+[
				'class'		=> 'move-item',
				'dashicon'	=> 'move',
				'wrap'		=> wpjam_tag('span', [$move_item]),
			]).$this->get_row_action($edit_item, $args+[
				'dashicon'	=> 'edit',
				'wrap'		=> wpjam_tag('span', [$edit_item]),
			]).$this->get_row_action($del_item, $args+[
				'class'		=> 'del-icon',
				'dashicon'	=> 'no-alt',
				'wrap'		=> wpjam_tag('span', [$del_item])
			]));

			$rendered->append('div', $attr, $item);
		}

		if(!$max_items || count($items) <= $max_items){
			$add_args	= ['id'=>$id, 'class'=>'add-item item'];

			if($item_type == 'image'){
				$add_args	+= ['dashicon'=>'plus-alt2', 'style'=>'width:'.$width.'px; height:'.$height.'px;'];
			}else{
				$add_args	+= ['title'=>'新增'];
			}

			$rendered->append($this->get_row_action($add_item, $add_args));
		}

		if($per_row){
			$style['width']	= ($per_row * ($width+30)).'px';
		}

		$class	= ['items', $item_type.'-list', ($sortable ? 'sortable' : '')];

		return $rendered->wrap('div', ['class'=>$class, 'style'=>$style]);
	}

	public function get_list_table(){
		if(wp_doing_ajax()){
			$this->prepare_items();
		}

		return wpjam_ob_get_contents([$this, 'list_table']);
	}

	public function list_table(){
		$this->views();

		echo '<form action="#" id="list_table_form" method="POST">';

		if($this->is_searchable()){
			$this->search_box('搜索', 'wpjam');
			echo '<br class="clear" />';
		}

		$this->display();

		echo '</form>';
	}

	public function ajax_response(){
		$referer	= wpjam_get_referer();

		if(!$referer){
			return new WP_Error('error', '非法请求');
		}

		$referer_parts	= parse_url($referer);

		if($referer_parts['host'] == $_SERVER['HTTP_HOST']){
			$_SERVER['REQUEST_URI']	= $referer_parts['path'];
		}

		$action_type	= wpjam_get_post_parameter('action_type');

		if($action_type == 'query_item'){
			$id	= wpjam_get_post_parameter('id',	['default'=>'']);

			return ['type'=>'add',	'id'=>$id, 'data'=>$this->get_single_row($id)];
		}elseif($action_type == 'query_items'){
			foreach(wpjam_get_data_parameter() as $key=>$value){
				$_REQUEST[$key]	= $value;
			}

			return ['data'=>$this->get_list_table(), 'type'=>'list'];
		}

		$list_action	= wpjam_get_post_parameter('list_action');
		$object			= $this->get_object($list_action);

		if(!$object){
			return new WP_Error('invalid_action');
		}

		$id		= wpjam_get_post_parameter('id',	['default'=>'']);
		$ids	= wpjam_get_post_parameter('ids',	['sanitize_callback'=>'wp_parse_args', 'default'=>[]]);
		$bulk	= wpjam_get_post_parameter('bulk',	['sanitize_callback'=>'intval']);

		if($action_type != 'form'){
			if(!$object->verify_nonce($id, $bulk)){
				return new WP_Error('invalid_nonce');
			}

			if($bulk === 2){
				$bulk = 0;
			}
		}

		$id_or_ids	= $bulk ? $ids : $id;

		if(!$object->is_allowed($id_or_ids)){
			return new WP_Error('access_denied');
		}

		$data	= wpjam_get_data_parameter();

		$response	= [
			'list_action'	=> $list_action,
			'page_title'	=> $object->page_title,
			'type'			=> $object->response,
			'layout'		=> $this->layout,
			'id'			=> $id,
			'bulk'			=> $bulk,
			'ids'			=> $ids
		];

		$form_args	= [
			'action_type'	=> $action_type,
			'response_type'	=> $object->response,
			'id'			=> $id,
			'bulk'			=> $bulk,
			'ids'			=> $ids,
			'data'			=> $data,
		];

		if($action_type == 'form'){
			return array_merge($response, [
				'type'	=> 'form',
				'form'	=> $object->get_form($form_args),
				'width'	=> $object->width ?: 720
			]);
		}elseif($action_type == 'direct'){
			if($bulk){
				$result	= $object->callback($ids);
			}else{
				if(in_array($list_action, ['move', 'up', 'down'])){
					$result	= $object->callback($id, $data);
				}else{
					$result	= $object->callback($id);

					if($list_action == 'duplicate'){
						$id = $result;
					}
				}
			}
		}elseif($action_type == 'submit'){
			$data	= $object->validate($id_or_ids, $data);

			if($object->response == 'form'){
				$form_args['data']	= $data;

				$result	= null;
			}else{
				$form_args['data']	= wpjam_get_post_parameter('defaults',	['sanitize_callback'=>'wp_parse_args', 'default'=>[]]);

				$submit_name	= wpjam_get_post_parameter('submit_name',	['default'=>$object->name]);
				$submit_text	= $object->get_submit_button($id, $submit_name);

				if(!$submit_text){
					return new WP_Error('invalid_submit_button');
				}

				$response['type']	= $submit_text['response'];

				$result	= $object->callback($id_or_ids, $data, $submit_name);
			}
		}

		$result_as_response	= is_array($result) && (
			isset($result['type']) || isset($result['bulk']) || isset($result['ids']) || isset($result['id']) || isset($result['items'])
		);

		if($result_as_response){
			$response	= array_merge($response, $result);

			$bulk	= $response['bulk'];
			$ids	= $response['ids'];
			$id		= $response['id'];
		}else{
			if(in_array($response['type'], ['add', 'duplicate']) || in_array($list_action, ['add', 'duplicate'])){
				if(is_array($result)){
					$dates	= $result['dates'] ?? $result;
					$date	= current($dates);
					$id		= is_array($date) ? ($date[$this->primary_key] ?? null) : null;

					if(is_null($id)){
						return new WP_Error('invalid_id');
					}
				}else{
					$id	= $result;
				}
			}
		}

		$data	= '';

		$form_required	= true;

		if($response['type'] == 'append'){
			return array_merge($response, ['data'=>$result, 'width'=>($object->width ?: 720)]);
		}elseif($response['type'] == 'redirect'){
			if(is_string($result)){
				$response['url']	= $result;
			}

			return $response;
		}elseif(in_array($response['type'], ['delete', 'move', 'up', 'down', 'form'])){
			if($this->layout == 'calendar'){
				$data	= $this->render_dates($result);
			}
		}elseif($response['type'] == 'items' && isset($response['items'])){
			foreach($response['items'] as $id => &$response_item){
				$response_item['id']	= $id;

				if($response_item['type'] == 'delete'){
					$form_required	= false;
				}elseif($response_item['type'] != 'append'){
					if(!is_blank($id)){
						$response_item['data']	= $this->get_single_row($id);
					}
				}
			}

			unset($response_item);
		}elseif($response['type'] == 'list'){
			if(in_array($list_action, ['add', 'duplicate'])){
				$response['id']	= $id;
			}

			$data	= $this->get_list_table();
		}else{
			if($bulk){
				$this->get_by_ids_by_model($ids);

				$data	= [];

				foreach($ids as $id){
					if(!is_blank($id)){
						$data[$id]	= $this->get_single_row($id);
					}
				}
			}else{
				if($this->layout == 'calendar'){
					$data	= $this->render_dates($result);
				}else{
					if(!$result_as_response && in_array($response['type'], ['add', 'duplicate'])){
						$response['id']	= $form_args['id'] = $id;
					}

					if(!is_blank($id)){
						$data	= $this->get_single_row($id);
					}
				}
			}
		}

		$response['data']	= $data;

		if($object->response != 'form'){
			if($result && is_array($result) && !empty($result['errmsg']) && $result['errmsg'] != 'ok'){ // 有些第三方接口返回 errmsg ： ok
				$response['errmsg'] = $result['errmsg'];
			}elseif($action_type == 'submit'){
				$response['errmsg'] = $submit_text['text'].'成功';
			}
		}

		if($action_type == 'submit'){
			if($response['type'] == 'delete'){
				$response['dismiss']	= true;
			}else{
				if($object->next){
					$response['next']		= $object->next;
					$response['page_title']	= $object->get_next_action()->page_title;

					if($response['type'] == 'form'){
						$response['errmsg']	= '';
					}
				}elseif($object->dismiss){
					$response['dismiss']	= true;
					$form_required			= false;
				}

				if($form_required){
					$response['form']	= $object->get_form($form_args);
				}
			}
		}

		return $response;
	}

	protected function parse_query_args($args){
		$filterable	= $this->get_filterable_fields_by_model();
		$query_vars	= $filterable ?: [];
		$query_vars	= array_merge($query_vars, ['orderby', 'order', 's']);

		foreach($query_vars as $query_var){
			$value	= wpjam_get_data_parameter($query_var);

			if(isset($value)){
				$args[$query_var]	= $value;
			}
		}

		return $args;
	}

	public function prepare_items(){
		foreach(['orderby', 'order'] as $key){
			$value	= wpjam_get_data_parameter($key);

			if($value){
				$_GET[$key] = $value;
			}
		}

		$per_page	= $this->get_per_page();
		$offset		= ($this->get_pagenum()-1) * $per_page;
		$args		= $this->parse_query_args(['number'=>$per_page, 'offset'=>$offset]);

		if(method_exists($this->model, 'query_data')){
			$result	= wpjam_try([$this, 'query_data_by_model'], $args);	// 6.3 放弃
		}else{
			if(method_exists($this->model, 'query_items') || method_exists($this->model, 'list')){
				$method		= method_exists($this->model, 'query_items') ? 'query_items' : 'list';
				$parameters	= wpjam_get_callback_parameters([$this->model, $method]);
			}else{
				$parameters	= null;
			}

			if($parameters && count($parameters) >= 2){
				$result	= wpjam_try([$this, 'query_items_by_model'], $per_page, $offset);
			}else{
				$result	= wpjam_try([$this, 'query_items_by_model'], $args);
			}
		}

		$this->items	= $result['items'] ?? [];
		$total_items	= $result['total'] ?? count($this->items);

		if($total_items){
			$this->set_pagination_args([
				'total_items'	=> $total_items,
				'per_page'		=> $per_page
			]);
		}
	}

	protected function get_bulk_actions(){
		return wp_list_pluck($this->bulk_actions, 'title');
	}

	public function get_subtitle(){
		$subtitle	= $this->get_subtitle_by_model();
		$search		= wpjam_get_data_parameter('s');

		if($search){
			$subtitle 	.= ' “'.esc_html($search).'”的搜索结果';
		}

		$subtitle	= $subtitle ? wpjam_tag('span', ['subtitle'], $subtitle) : '';

		if($this->layout != 'left'){
			$subtitle	= ' '.$this->get_row_action('add', ['class'=>'page-title-action', 'subtitle'=>true]).$subtitle;
		}

		return $subtitle;
	}

	protected function get_table_classes() {
		$classes = parent::get_table_classes();

		return $this->fixed ? $classes : array_diff($classes, ['fixed']);
	}

	public function get_singular(){
		return $this->singular;
	}

	protected function get_primary_column_name(){
		$name	= $this->primary_column;

		if($this->columns && (!$name || !isset($this->columns[$name]))){
			return array_key_first($this->columns);
		}

		return $name;
	}

	protected function handle_row_actions($item, $column_name, $primary){
		return ($primary === $column_name && !empty($item['row_actions'])) ? $this->row_actions($item['row_actions'], false) : '';
	}

	public function row_actions($actions, $always_visible=true){
		return parent::row_actions($actions, $always_visible);
	}

	public function get_per_page(){
		if($this->per_page && is_numeric($this->per_page)){
			return $this->per_page;
		}

		$option		= get_screen_option('per_page', 'option');
		$default	= get_screen_option('per_page', 'default') ?: 50;

		return $option ? $this->get_items_per_page($option, $default) : $default;
	}

	public function get_columns(){
		if($this->bulk_actions){
			return array_merge(['cb'=>'checkbox'], $this->columns);
		}

		return $this->columns;
	}

	public function get_sortable_columns(){
		return $this->sortable_columns;
	}

	public function get_views(){
		return $this->views;
	}

	public function is_searchable(){
		return $this->search ?? $this->get_searchable_fields_by_model();
	}

	public function extra_tablenav($which='top'){
		$this->extra_tablenav_by_model($which);

		do_action(wpjam_get_filter_name($this->plural, 'extra_tablenav'), $which);

		if($which == 'top'){
			$overall	= '';

			foreach($this->overall_actions as $action){
				$overall	.= $this->get_row_action($action, ['class'=>'button']);
			}

			echo $overall ? wpjam_tag('div', ['alignleft', 'actions', 'overallactions'], $overall) : '';
		}
	}

	public function current_action(){
		return wpjam_get_request_parameter('list_action', ['default'=>parent::current_action()]);
	}

	public function filter_parameter_default($default, $name){
		return $this->defaults[$name] ?? $default;
	}
}

class WPJAM_Left_List_Table extends WPJAM_List_Table{
	public function col_left(){
		$result	= $this->col_left_by_model();

		if($result && is_array($result)){
			$args	= wp_parse_args($result, [
				'total_items'	=> 0,
				'total_pages'	=> 0,
				'per_page'		=> 10,
			]);

			$total_pages	= $args['total_pages'] ?: ($args['per_page'] ? ceil($args['total_items']/$args['per_page']) : 0);

			if($total_pages){
				$pages	= [];

				foreach(['prev', 'text', 'next', 'goto'] as $key){
					$pages[$key]	= $this->get_left_page_link($key, $total_pages);
				}

				$class	= ['tablenav-pages'];
				$class	= $total_pages < 2 ? array_merge($class, ['one-page']) : $class;

				echo wpjam_tag('span', ['left-pagination-links'], join(' ', array_filter($pages)))->wrap('div', $class)->wrap('div', ['tablenav', 'bottom']);
			}
		}
	}

	public function ajax_response(){
		if(wpjam_get_post_parameter('action_type') == 'left'){
			return ['data'=>$this->get_list_table(), 'left'=>$this->get_col_left(), 'type'=>'left'];
		}

		return parent::ajax_response();
	}

	protected function get_left_page_link($type, $total){
		$current	= (int)wpjam_get_data_parameter('left_paged') ?: 1;

		if($type == 'text'){
			return wpjam_tag('span', ['current-page'], $current)
			->after(' / ')
			->after('span', ['total-pages'], number_format_i18n($total))
			->wrap('span', ['tablenav-paging-text']);
		}elseif($type == 'goto'){
			if($total < 2){
				return '';
			}

			return wpjam_tag('input', [
				'type'	=> 'text',
				'name'	=> 'paged',
				'value'	=> $current,
				'size'	=> strlen($total),
				'id'	=> 'left-current-page-selector',
				'class'	=> 'current-page',
				'aria-describedby'	=> 'table-paging',
			])->after('a', ['left-pagination', 'button', 'goto'], '&#10132;')
			->wrap('span', ['paging-input']);
		}elseif($type == 'prev'){
			$value	= 1;
			$paged	= max(1, $current - 1);
			$text	= '&lsaquo;';
			$reader	= __('Previous page');
		}else{
			$value	= $total;
			$paged	= min($value, $current + 1);
			$text	= '&rsaquo;';
			$reader	= __('Next page');
		}

		$attr	= ['aria-hidden'=>'true'];

		if($value == $current){
			$attr['class']	= ['tablenav-pages-navspan', 'button', 'disabled'];
		}

		$tag	= wpjam_tag('span', $attr, $text);

		if($value != $current){
			$tag->before('span', ['screen-reader-text'], $reader)->wrap('a', ['data'=>['left_paged'=>$paged], 'class'=>['left-pagination', 'button', $type.'-page']]);
		}

		return $tag;
	}

	public function get_col_left(){
		return wpjam_ob_get_contents([$this, 'col_left']);
	}
}

class WPJAM_Calendar_List_Table extends WPJAM_List_Table{
	public function __get($name){
		if($name == 'year'){
			$year	= (int)wpjam_get_data_parameter('year') ?: wpjam_date('Y');

			return max(min($year, 2200), 1970);
		}elseif($name == 'month'){
			$month	= (int)wpjam_get_data_parameter('month') ?: wpjam_date('m');

			return max(min($month, 12), 1);
		}
		
		return parent::__get($name);
	}

	public function prepare_items(){
		$args	= ['year'=>$this->year, 'month'=>$this->month, 'layout'=>$this->layout];
		$args	= $this->parse_query_args($args);

		$this->items	= wpjam_try([$this, 'query_items_by_model'], $args);
	}

	public function render_date($raw_item, $date){
		if(wp_is_numeric_array($raw_item)){
			foreach($raw_item as $key => &$_item){
				$_item	= $this->parse_item($_item);

				if(!$_item){
					unset($raw_item[$key]);
				}
			}
		}else{
			$raw_item	= $this->parse_item($raw_item);
		}

		$row_actions	= [];

		if(wpjam_is_assoc_array($raw_item)){
			$row_actions	= $this->get_row_actions($raw_item[$this->primary_key]);
		}else{
			$row_actions	= ['add'=>$this->get_row_action('add', ['data'=>['date'=>$date]])];
		}

		$links	= wpjam_tag('div', ['row-actions', 'alignright']);

		foreach($row_actions as $action => $link){
			$links->append('span', [$action], $link)->append(' ');
		}

		$item	= $this->render_date_by_model($raw_item, $date) ?: '';
		$day	= explode('-', $date)[2];
		$class	= $date == wpjam_date('Y-m-d') ? ['day', 'today'] :  ['day'];

		return $links->before('span', [$class], $day)
		->wrap('div', ['date-meta'])
		->after('div', ['date-content'], $item);
	}

	public function render_dates($result){
		$dates	= $result['dates'] ?? $result;
		$data	= [];

		foreach($dates as $date => $item){
			$data[$date]	= $this->render_date($item, $date);
		}

		return $data;
	}

	public function display(){
		$this->display_tablenav('top');

		$year	= $this->year;
		$month	= zeroise($this->month, 2);
		$m_ts	= mktime(0, 0, 0, $this->month, 1, $this->year);	// 每月开始的时间戳
		$days	= date('t', $m_ts);
		$start	= (int)get_option('start_of_week');
		$pad	= calendar_week_mod(date('w', $m_ts) - $start);
		$tr		= wpjam_tag('tr');

		for($wd_count = 0; $wd_count <= 6; $wd_count++){
			$weekday	= ($wd_count + $start) % 7;
			$name		= $this->get_weekday_by_locale($weekday);

			$tr->append('th', [
				'scope'	=> 'col',
				'class'	=> in_array($weekday, [0, 6]) ? 'weekend' : 'weekday',
				'title'	=> $name
			], $this->get_weekday_abbrev_by_locale($name));
		}

		$thead	= wpjam_tag('thead')->append(wp_clone($tr));
		$tfoot	= wpjam_tag('tfoot')->append(wp_clone($tr));
		$tbody	= wpjam_tag('tbody', ['id'=>'the-list', 'data'=>['wp-lists'=>'list:'.$this->singular]]);
		$tr		= wpjam_tag('tr');

		if($pad){
			$tr->append('td', ['colspan'=>$pad, 'class'=>'pad']);
		}

		for($day=1; $day<=$days; ++$day){
			$date	= $year.'-'.$month.'-'.zeroise($day, 2);
			$item	= $this->items[$date] ?? [];
			$item	= $this->render_date($item, $date);

			$tr->append('td', [
				'id'	=> 'date_'.$date,
				'class'	=> in_array($pad+$start, [0, 6, 7]) ? 'weekend' : 'weekday'
			], $item);

			$pad++;

			if($pad%7 == 0){
				$tbody->append($tr);

				$pad	= 0;
				$tr	= wpjam_tag('tr');
			}
		}

		if($pad){
			$tr->append('td', ['colspan'=>(7-$pad), 'class'=>'pad']);

			$tbody->append($tr);
		}

		echo $tbody->before($tfoot)->before($thead)->wrap('table', ['cellpadding'=>10, 'cellspacing'=>0, 'class'=>'widefat fixed']);

		$this->display_tablenav('bottom');
	}

	public function extra_tablenav($which='top'){
		if($which == 'top'){
			echo wpjam_tag('h2', [], sprintf(__('%1$s %2$d'), $this->get_month_by_locale($this->month), $this->year));
		}

		parent::extra_tablenav($which);
	}

	public function pagination($which){
		$pagination = wpjam_tag('span', ['pagination-links']);

		foreach(['prev', 'current', 'next'] as $type){
			$pagination->append($this->get_month_link($type));
		}

		echo $pagination->wrap('div', ['tablenav-pages']);
	}

	public function get_month_link($type=''){
		if($type == 'prev'){
			$text	= '&lsaquo;';
			$class	= 'prev-month';

			if($this->month == 1){
				$year	= $this->year - 1;
				$month	= 12;
			}else{
				$year	= $this->year;
				$month	= $this->month - 1;
			}
		}elseif($type == 'next'){
			$text	= '&rsaquo;';
			$class	= 'next-month';

			if($this->month == 12){
				$year	= $this->year + 1;
				$month	= 1;
			}else{
				$year	= $this->year;
				$month	= $this->month + 1;
			}
		}else{
			$text	= '今日';
			$class	= 'current-month';
			$year	= wpjam_date('Y');
			$month	= wpjam_date('m');
		}

		if($type){
			$reader	= sprintf(__('%1$s %2$d'), $this->get_month_by_locale($month), $year);
			$text	= wpjam_tag('span', ['aria-hidden'=>'true'], $text)->before('span', ['screen-reader-text'], $reader);
		}

		return $this->get_filter_link(['year'=>$year, 'month'=>$month], $text, $class.' button');
	}

	public function get_views(){
		return [];
	}

	public function get_bulk_actions(){
		return [];
	}

	public function is_searchable(){
		return false;
	}
}

class WPJAM_List_Table_Action extends WPJAM_Register{
	public function __call($method, $args){
		if($method == 'get_defaults' || $method == 'validate'){
			$id		= array_shift($args);
			$fields	= $this->get_fields($id, true);

			if(!$fields){
				return $args[1] ?? null;
			}

			return call_user_func_array([wpjam_fields($fields), $method], $args);
		}elseif(str_ends_with($method, '_nonce')){
			$id 	= $args[0];
			$bulk	= $args[1] ?? false;
			$bulk	= $bulk ?: ($id ? false : true);
			$key	= $bulk ? 'bulk_'.$this->name : $this->name.'-'.$id;

			if($method == 'verify_nonce'){
				return WPJAM_Admin::verify_nonce($key);
			}else{
				return WPJAM_Admin::create_nonce($key);
			}
		}else{
			if($method == 'get_next_action'){
				return self::get($this->next, $args);
			}elseif($method == 'get_prev_action'){
				return self::get($this->prev, $args);
			}
		}
	}

	public function jsonSerialize(){
		return array_filter($this->generate_data_attr(['bulk'=>true]));
	}

	public function get_data($id, $include_prev=false, $by_callback=false){
		if($include_prev || $by_callback){
			$callback	= $this->data_callback;

			if($callback && is_callable($callback)){
				$data 	= wpjam_try($callback, $id, $this->name);

				if(!$include_prev){
					return $data;
				}
			}else{
				if($include_prev){
					wpjam_exception('「'.$this->name.'」的 data_callback 无效', 'invalid_callback');
				}
			}
		}

		if($include_prev){
			$prev	= $this->get_prev_action();
			$prev	= $prev ? $prev->get_data($id, true) : [];

			return array_merge($prev, $data);
		}else{
			if(is_callable([$this->model, 'get'])){
				$data	= wpjam_try([$this->model, 'get'], $id);

				return $data ?: wpjam_exception('', 'invalid_id');
			}

			wpjam_exception([$this->model.'->get()'], 'undefined_method');
		}
	}

	public function get_fields($id, $include_prev=false, $args=[]){
		if($this->direct){
			return [];
		}

		$fields	= $this->fields;

		if($fields && is_callable($fields)){
			$fields	= wpjam_try($fields, $id, $this->name);
		}

		$fields	= $fields ?: wpjam_try([$this->model, 'get_fields'], $this->name, $id);
		$fields	= is_array($fields) ? $fields : [];

		if($include_prev){
			$prev_action	= $this->get_prev_action();

			if($prev_action){
				$fields	= array_merge($fields, $prev_action->get_fields($id, true));
			}
		}

		if(method_exists($this->model, 'filter_fields')){
			$fields	= wpjam_try([$this->model, 'filter_fields'], $fields, $id, $this->name);
		}else{
			if(!in_array($this->name, ['add', 'duplicate'])){
				$primary_key	= $this->primary_key;

				if($primary_key && isset($fields[$primary_key])){
					$fields[$primary_key]['type']	= 'view';
				}
			}
		}

		return $args ? wpjam_fields($fields, $args) :$fields;
	}

	public function get_form($args=[]){
		$object			= $this;
		$action_type	= $args['action_type'];
		$prev_action	= null;

		if($action_type == 'submit' && $this->next){
			if($this->response == 'form'){
				$prev_action	= $this;
			}

			$object	= $this->get_next_action();
		}

		$bulk	= $args['bulk'];
		$id		= $bulk ? 0 : $args['id'];
		$id_arg	= $bulk ? $args['ids'] : $id;

		$fields_args	= ['id'=>$id, 'echo'=>false, 'data'=>$args['data']];

		if(!$bulk){
			if($id && ($action_type != 'submit' || $args['response_type'] != 'form')){
				$data	= $object->get_data($id, false, true);
				$data	= is_array($data) ? array_merge($args['data'], $data) : $data;

				$fields_args['data']	= $data;
			}

			$fields_args['meta_type']	= get_screen_option('meta_type');

			if($object->value_callback){
				$fields_args['value_callback']	= $object->value_callback;
			}elseif(method_exists($object->model, 'value_callback')){
				$fields_args['value_callback']	= [$object->model, 'value_callback'];
			}
		}

		$fields	= $object->get_fields($id_arg, false, $fields_args);
		$button	= '';

		$prev_action	= $prev_action ?: $object->get_prev_action();

		if($prev_action && !$bulk){
			$button	.= wpjam_tag('input', [
				'type'	=> 'button',
				'value'	=> '上一步',
				'class'	=> ['list-table-action', 'button','large'],
				'data'	=> $prev_action->generate_data_attr($args)
			]);

			if($action_type == 'form'){
				$args['data']	= array_merge($args['data'], $prev_action->get_data($id, true));
			}
		}

		if($object->next && $object->response == 'form'){
			$button	.= get_submit_button('下一步', 'primary', 'next', false);
		}else{
			foreach($object->get_submit_button($id) as $key => $item){
				$button	.= get_submit_button($item['text'], $item['class'], $key, false);
			}
		}

		$form	= wpjam_tag('form', [
			'method'	=> 'post',
			'action'	=> '#',
			'id'		=> 'list_table_action_form',
			'data'		=> $object->generate_data_attr(array_merge($args, ['type'=>'form']))
		], $fields);

		if($button){
			$form->append('p', ['submit'], $button);
		}

		return $form;
	}

	public function get_row_action($args=[], $layout=''){
		if($layout == 'calendar' && !$this->calendar){
			return '';
		}

		$args	= wp_parse_args($args, ['id'=>0, 'data'=>[], 'class'=>[], 'style'=>'', 'title'=>'']);

		if(!$this->show_if($args['id'])){
			return '';
		}

		if(!$this->is_allowed($args['id'])){
			$fallback	= array_get($args, 'fallback');

			return $fallback === true ? $args['title'] : (string)$fallback;
		}

		$tag	= $args['tag'] ?? 'a';
		$attr	= ['title'=>$this->page_title, 'style'=>$args['style'], 'class'=>(array)$args['class']];

		if($this->redirect){
			$tag	= 'a';

			$attr['href']		= str_replace('%id%', $args['id'], $this->redirect);
			$attr['class'][]	= 'list-table-redirect';
		}elseif($this->filter){
			$item	= (array)$this->get_data($args['id']);
			$data	= $this->data ?: [];
			$data	= array_merge($data, wp_array_slice_assoc($item, wp_parse_list($this->filter)));

			$attr['data']		= ['filter'=>wp_parse_args($args['data'], $data)];
			$attr['class'][]	= 'list-table-filter';
		}else{
			$attr['data']		= $this->generate_data_attr($args);
			$attr['class'][]	= in_array($this->response, ['move', 'move_item']) ? 'list-table-move-action' : 'list-table-action';
		}

		if(!empty($args['dashicon'])){
			$title	= wpjam_tag('span', ['dashicons dashicons-'.$args['dashicon']]);
		}elseif(!is_blank($args['title'])){
			$title	= $args['title'];
		}elseif($this->dashicon && empty($args['subtitle']) && ($layout == 'calendar' || !$this->title)){
			$title	= wpjam_tag('span', ['dashicons dashicons-'.$this->dashicon]);
		}else{
			$title	= $this->title ?: $this->page_title;
		}

		$action	= (string)wpjam_tag($tag, $attr, $title);
		$wrap	= array_get($args, 'wrap');

		return $wrap ? wpjam_wrap($action, $wrap, $this->name) : $action;
	}

	public function get_submit_button($id, $name=null){
		if($name){
			$button	= $this->get_submit_button($id);

			return $button[$name] ?? [];
		}

		if(!is_null($this->submit_text)){
			$button	= $this->submit_text;

			if($button && is_callable($button)){
				$button	= wpjam_try($button, $id, $this->name);
			}
		}else{
			$button = wp_strip_all_tags($this->title) ?: $this->page_title;
		}

		$button	= $button ?: [];
		$button	= is_array($button) ? $button : [$this->name=>$button];

		foreach($button as &$item){
			$item	= is_array($item) ? $item : ['text'=>$item];
			$item	= wp_parse_args($item, ['response'=>$this->response, 'class'=>'primary']);
		}

		return $button;
	}

	public function callback($id=0, $data=null, $submit_name=''){
		$bulk		= is_array($id);
		$cb_key		= $bulk ? 'bulk_callback' : 'callback';
		$callback	= $this->$cb_key;

		if($submit_name){
			$submit_text	= $this->get_submit_button($id, $submit_name);

			if(!empty($submit_text[$cb_key])){
				$callback	= $submit_text[$cb_key];
			}
		}

		if($bulk){
			if(!$callback && method_exists($this->model, 'bulk_'.$this->name)){
				$callback	= [$this->model, 'bulk_'.$this->name];
			}

			if($callback){
				if(!is_callable($callback)){
					wpjam_exception('', 'invalid_callback');
				}

				$result	= wpjam_try($callback, $id, $data, $this->name, $submit_name);

				if(is_null($result)){
					wpjam_exception(['没有正确返回'], 'invalid_callback');
				}

				return $result;
			}else{
				$return	= wpjam_array();

				foreach($id as $_id){
					$result	= $this->callback($_id, $data, $submit_name);

					if(is_array($result)){
						$return->merge($result);
					}
				}

				return $return->get(null) ?: $result;
			}
		}else{
			if($callback){
				if(!is_callable($callback)){
					wpjam_exception('', 'invalid_callback');
				}

				$args	= [$id, $data];

				if($this->overall){
					$args	= [$data];
				}elseif($this->response == 'add' && !is_null($data)){
					$parameters	= wpjam_get_callback_parameters($callback);

					if(count($parameters) == 1 || $parameters[0]->name == 'data'){
						$args	= [$data];
					}
				}

				$args	= array_merge($args, [$this->name, $submit_name]);
				$result	= wpjam_try($callback, ...$args);

				if(is_null($result)){
					wpjam_exception(['没有正确返回'], 'invalid_callback');
				}

				return $result;
			}else{
				$method	= $this->name;

				if($method == 'add'){
					$method	= 'insert';
				}elseif($method == 'edit'){
					$method	= 'update';
				}elseif(in_array($method, ['up', 'down'], true)){
					$method	= 'move';
				}elseif($method == 'duplicate' && !is_null($data)){
					$method	= 'insert';
				}

				$defaults	= $this->get_defaults($id);
				$callback	= [$this->model, $method];

				if($this->overall || $method == 'insert' || $this->response == 'add'){
					if(is_callable([$this->model, $method])){
						$args	= [$data];
					}else{
						$callback	= null;
					}
				}else{
					if(method_exists($this->model, $method)){
						$args		= [$id, $data];
						$callback 	= wpjam_parse_method($this->model, $method, $args, true);
					}elseif(!$this->meta_type && method_exists($this->model, '__callStatic')){
						$args		= [$id, $data];
					}elseif(method_exists($this->model, 'update_callback')){
						$args		= [$id, $data, $defaults];
						$callback	= wpjam_parse_method($this->model, 'update_callback', $args, true);
					}else{
						$meta_type	= get_screen_option('meta_type');

						if($meta_type){
							$args		= [$meta_type, $id, $data, $defaults];
							$callback	= 'wpjam_update_metadata';
						}else{
							$callback	= null;
						}
					}
				}

				if(!$callback){
					wpjam_exception([$this->name, '回调函数'], 'undefined_method');
				}

				$result	= wpjam_try($callback, ...$args);

				return is_null($result) ? true : $result;
			}
		}
	}

	public function generate_data_attr($args=[]){
		$args	= wp_parse_args($args, ['type'=>'button', 'id'=>0, 'data'=>[], 'bulk'=>false, 'ids'=>[]]);
		$data	= $this->data ?: [];
		$attr	= [
			'action'	=> $this->name,
			'nonce'		=> $this->create_nonce($args['id'], $args['bulk']),
			'data'		=> wp_parse_args($args['data'], $data),
		];

		if($args['bulk']){
			$attr['bulk']	= $this->bulk;
			$attr['ids']	= $args['ids'];
			$attr['data']	= $attr['data'] ? http_build_query($attr['data']) : '';
			$attr['title']	= $this->title;
		}else{
			$attr['id']		= $args['id'];
		}

		if($args['type'] == 'button'){
			$attr['direct']		= $this->direct;
			$attr['confirm']	= $this->confirm;
		}else{
			$attr['next']		= $this->next;
		}

		return $attr;
	}

	protected function show_if($id){
		try{
			$show_if	= $this->show_if;

			if($show_if){
				if(is_callable($show_if)){
					return wpjam_try($show_if, $id, $this->name);
				}elseif(is_array($show_if) && $id){
					return wpjam_show_if($this->get_data($id), $show_if);
				}
			}

			return true;
		}catch(Exception $e){
			return false;
		}
	}

	public function is_allowed($id=0){
		if($this->capability == 'read'){
			return true;
		}

		foreach((array)$id as $_id){
			if(!current_user_can($this->capability, $_id, $this->name)){
				return false;
			}
		}

		return true;
	}

	protected static function get_config($key){
		if(in_array($key, ['data_type', 'orderby'])){
			return true;
		}
	}
}

class WPJAM_List_Table_Column extends WPJAM_Register{
	public function parse_args(){
		return wp_parse_args($this->args, ['type'=>'view', 'show_admin_column'=>true]);
	}

	public function callback($id, $value){
		$callback	= $this->column_callback ?: $this->callback;

		if($callback && is_callable($callback)){
			return wpjam_call($callback, $id, $this->name, $value);
		}

		if($this->options){
			$options	= wpjam_parse_options($this->options);
			$value		= (array)$value;

			foreach($value as &$item){
				$option	= $options[$item] ?? $item;
				$item	= $this->filterable ? '[filter '.$this->name.'="'.$item.'"]'.$option.'[/filter]' : $option;
			}

			return implode(',', $value);
		}else{
			return $this->filterable ? '[filter '.$this->name.'="'.$value.'"]'.$value.'[/filter]' : $value;
		}
	}

	public function add_style(){
		$style	= $this->column_style ?: $this->style;

		if($style){
			if(!preg_match('/\{([^\}]*)\}/', $style)){
				$style	= '.manage-column.column-'.$this->name.'{ '.$style.' }';
			}

			wp_add_inline_style('list-tables', $style);
		}
	}

	protected static function get_config($key){
		if(in_array($key, ['data_type', 'orderby'])){
			return true;
		}
	}
}

class WPJAM_List_Table_View extends WPJAM_Register{
	public function get_link(){
		if($this->view){
			return $this->view;
		}

		$callback	= $this->callback;

		if($callback && is_callable($callback)){
			$result	= wpjam_call($callback, $this->name);

			if(is_wp_error($result)){
				return null;
			}elseif(!is_array($result)){
				return $result;
			}

			$this->update_args($result);
		}

		if($this->label){
			if(is_numeric($this->count)){
				$this->label	.= wpjam_tag('span', ['count'], '（'.$this->count.'）');
			}

			$this->filter	= $this->filter ?? [];
			$this->class	= $this->class ?? $this->parse_class();

			return $this->get_args();
		}

		return null;
	}

	protected function parse_class(){
		foreach($this->filter as $key => $value){
			$current	= wpjam_get_data_parameter($key);

			if((is_null($value) && !is_null($current)) || $current != $value){
				// if($this->name == 'shop-log'){
				// 	var_dump($key);
				// 	var_dump($value);
				// 	var_dump($current);
				// }
				return '';
			}
		}

		return 'current';
	}

	protected static function get_config($key){
		if(in_array($key, ['data_type', 'orderby'])){
			return true;
		}
	}
}