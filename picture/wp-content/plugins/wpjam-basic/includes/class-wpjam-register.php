<?php
trait WPJAM_Call_Trait{
	protected static $_closures	= [];

	public static function add_dynamic_method($method, Closure $closure){
		if(is_callable($closure)){
			$name	= strtolower(get_called_class());

			self::$_closures[$name][$method]	= $closure;
		}
	}

	public static function remove_dynamic_method($method){
		$name	= strtolower(get_called_class());

		unset(self::$_closures[$name]);
	}

	protected static function get_dynamic_method($method){
		$called	= get_called_class();
		$names	= array_values(class_parents($called));

		array_unshift($names, $called);

		foreach($names as $name){
			$name	= strtolower($name);

			if(isset(self::$_closures[$name][$method])){
				return self::$_closures[$name][$method];
			}
		}
	}

	protected function call_dynamic_method($method, ...$args){
		$closure	= is_closure($method) ? $method : self::get_dynamic_method($method);
		$callback	= $closure ? $closure->bindTo($this, get_called_class()) : null;

		return $callback ? call_user_func_array($callback, $args) : null;
	}

	public function call($method, ...$args){
		try{
			if(!is_closure($method) && method_exists($this, $method)){
				return call_user_func_array([$this, $method], $args);
			}else{
				return $this->call_dynamic_method($method, ...$args);
			}
		}catch(WPJAM_Exception $e){
			return $e->get_wp_error();
		}catch(Exception $e){
			return new WP_Error($e->getCode(), $e->getMessage());
		}
	}

	public function try($method, ...$args){
		if(is_callable([$this, $method])){
			try{
				$result	= call_user_func_array([$this, $method], $args);

				if(is_wp_error($result)){
					wpjam_exception($result);
				}

				return $result;
			}catch(Exception $e){
				throw $e;
			}
		}

		trigger_error(get_called_class().':'.$method, true);
	}

	public function map($value, $method, ...$args){
		if($value && is_array($value)){
			foreach($value as &$item){
				$item	= $this->try($method, $item, ...$args);
			}
		}

		return $value;
	}
}

trait WPJAM_Items_Trait{
	public function get_items(){
		$items	= $this->_items;

		return is_array($items) ? $items : [];
	}

	public function update_items($items){
		$this->_items	= $items;

		return $this;
	}

	public function get_item_keys(){
		return array_keys($this->get_items());
	}

	public function item_exists($key){
		return array_key_exists($key, $this->get_items());
	}

	public function get_item($key){
		$items	= $this->get_items();

		return $items[$key] ?? null;
	}

	public function add_item(...$args){
		if(count($args) == 2){
			$key	= $args[0];
			$item	= $args[1];

			if($this->item_exists($key)){
				return new WP_Error('invalid_item_key', '「'.$key.'」已存在，无法添加');
			}

			return $this->set_item($key, $item, 'add');
		}else{
			$item	= $args[0];
			$result	= $this->validate_item($item, null, 'add');

			if(is_wp_error($result)){
				return $result;
			}

			$items		= $this->get_items();
			$items[]	= $this->sanitize_item($item, null, 'add');

			return $this->update_items($items);
		}
	}

	public function edit_item($key, $item){
		if(!$this->item_exists($key)){
			return new WP_Error('invalid_item_key',  '「'.$key.'」不存在，无法编辑');
		}

		return $this->set_item($key, $item, 'edit');
	}

	public function replace_item($key, $item){
		if(!$this->item_exists($key)){
			return new WP_Error('invalid_item_key', '「'.$key.'」不存在，无法编辑');
		}

		return $this->set_item($key, $item, 'replace');
	}

	public function set_item($key, $item, $action='set'){
		$result	= $this->validate_item($item, $key, $action);

		if(is_wp_error($result)){
			return $result;
		}

		$items			= $this->get_items();
		$items[$key]	= $this->sanitize_item($item, $key, $action);

		return $this->update_items($items);
	}

	public function delete_item($key){
		if(!$this->item_exists($key)){
			return new WP_Error('invalid_item_key', '「'.$key.'」不存在，无法删除');
		}

		$result	= $this->validate_item(null, $key, 'delete');

		if(is_wp_error($result)){
			return $result;
		}

		$items	= $this->get_items();
		$items	= array_except($items, $key);
		$result = $this->update_items($items);

		if(!is_wp_error($result)){
			$this->after_delete_item($key);
		}

		return $result;
	}

	public function del_item($key){
		return $this->delete_item($key);
	}

	public function move_item($orders){
		$new_items	= [];
		$items		= $this->get_items();

		foreach($orders as $i){
			if(isset($items[$i])){
				$new_items[]	= array_pull($items, $i);
			}
		}

		return $this->update_items(array_merge($new_items, $items));
	}

	protected function validate_item($item=null, $key=null, $action=''){
		return true;
	}

	protected function sanitize_item($item, $id=null){
		return $item;
	}

	protected function after_delete_item($key){
	}
}

class WPJAM_Args implements ArrayAccess, IteratorAggregate, JsonSerializable{
	use WPJAM_Call_Trait;

	protected $args;
	protected $_archives	= [];

	public function __construct($args=[]){
		$this->args	= $args;
	}

	public function __get($key){
		return $this->offsetGet($key);
	}

	public function __set($key, $value){
		$this->offsetSet($key, $value);
	}

	public function __isset($key){
		return $this->offsetExists($key);
	}

	public function __unset($key){
		$this->offsetUnset($key);
	}

	#[ReturnTypeWillChange]
	public function offsetGet($key){
		$args	= $this->get_args();
		$value	= $args[$key] ?? null;

		if(is_null($value) && $key == 'args'){
			return $args;
		}

		return $value;
	}

	#[ReturnTypeWillChange]
	public function offsetSet($key, $value){
		$this->filter_args();

		if(is_null($key)){
			$this->args[]		= $value;
		}else{
			$this->args[$key]	= $value;
		}
	}

	#[ReturnTypeWillChange]
	public function offsetExists($key){
		return array_key_exists($key, $this->get_args());
	}

	#[ReturnTypeWillChange]
	public function offsetUnset($key){
		$this->filter_args();

		unset($this->args[$key]);
	}

	#[ReturnTypeWillChange]
	public function getIterator(){
		return new ArrayIterator($this->get_args());
	}

	#[ReturnTypeWillChange]
	public function jsonSerialize(){
		return $this->get_args();
	}

	public function invoke(...$args){
		$invoke	= $this->invoke;

		if($invoke){
			return $this->call_dynamic_method($invoke, ...$args);
		}
	}

	protected function error($errcode, $errmsg){
		return new WP_Error($errcode, $errmsg);
	}

	protected function filter_args(){
		return $this->args = $this->args ?: [];
	}

	public function get_args(){
		return $this->filter_args();
	}

	public function set_args($args){
		$this->args	= $args;

		return $this;
	}

	public function update_args($args){
		foreach($args as $key => $value){
			$this->offsetSet($key, $value);
		}

		return $this;
	}

	public function get_archives(){
		return $this->_archives;
	}

	public function archive(){
		array_push($this->_archives, $this->get_args());

		return $this;
	}

	public function restore(){
		if($this->_archives){
			$this->args	= array_pop($this->_archives);
		}

		return $this;
	}

	public function sandbox($callback, ...$args){
		$this->archive();

		$result	= call_user_func_array($callback, $args);

		$this->restore();

		return $result;
	}

	public function get_arg($key, $default=null){
		return array_get($this->get_args(), $key, $default);
	}

	public function update_arg($key, $value=null){
		$this->filter_args();

		array_set($this->args, $key, $value);

		return $this;
	}

	public function delete_arg($key){
		$this->args	= array_except($this->get_args(), $key);

		return $this;
	}

	public function pull($key, $default=null){
		$value	= $this->get_arg($key, $default);

		$this->delete_arg($key);

		return $value;
	}

	public function pulls($keys){
		$data	= wp_array_slice_assoc($this->get_args(), $keys);

		$this->delete_arg($keys);

		return $data;
	}

	public function filter_parameter_default($default, $name){
		return $this->defaults[$name] ?? $default;
	}
}

class WPJAM_Register extends WPJAM_Args{
	use WPJAM_Items_Trait;

	protected $name;
	protected $_group;
	protected $_filtered	= false;

	public function __construct($name, $args=[], $group=''){
		$this->name		= $name;
		$this->_group	= $group = self::parse_group($group);

		if($this->is_active() || !empty($args['active'])){
			$args	= self::preprocess_args($args, $this);
		}

		$this->args	= $args;
	}

	#[ReturnTypeWillChange]
	public function offsetGet($key){
		if($key == 'name'){
			return $this->name;
		}else{
			return parent::offsetGet($key);
		}
	}

	#[ReturnTypeWillChange]
	public function offsetSet($key, $value){
		if($key != 'name'){
			parent::offsetSet($key, $value);
		}
	}

	#[ReturnTypeWillChange]
	public function offsetExists($key){
		if($key == 'name'){
			return true;
		}

		return parent::offsetExists($key);
	}

	protected function parse_method($method, $type=null, $args=null){
		if($type == 'model'){
			$model	= $args ? array_get($args, 'model') : $this->model;

			if($model && method_exists($model, $method)){
				return [$model, $method];
			}
		}elseif($type == 'property'){
			if($this->$method && is_callable($this->$method)){
				return $this->$method;
			}
		}else{
			foreach(['model', 'property'] as $type){
				$called = $this->parse_method($method, $type);

				if($called){
					return $called;
				}
			}
		}
	}

	protected function method_exists($method, $type=null){
		return $this->parse_method($method, $type) ? true : false;
	}

	protected function call_method($method, ...$args){
		$called	= $this->parse_method($method);

		if($called){
			return call_user_func_array($called, $args);
		}

		if(str_starts_with($method, 'filter_')){
			return $args[0] ?? null;
		}
	}

	protected function try_method($method, ...$args){
		try{
			$result	= $this->call_method($method, ...$args);

			if(is_wp_error($result)){
				wpjam_exception($result);
			}

			return $result;
		}catch(Exception $e){
			throw $e;
		}
	}

	protected function parse_args(){	// 子类实现
		return $this->args;
	}

	protected function filter_args(){
		if(!$this->_filtered){
			$this->_filtered	= true;

			$args	= $this->parse_args();
			$args	= is_null($args) ? $this->args : $args;
			$filter	= $this->get_filter();

			if($filter){
				$args	= apply_filters($filter, $args, $this->name);
			}

			$this->args	= $args;
		}

		return $this->args;
	}

	protected function get_filter(){
		$class	= strtolower(get_called_class());

		if($class == 'wpjam_register'){
			return 'wpjam_'.$this->_group.'_args';
		}else{
			return $class.'_args';
		}
	}

	public function get_arg($key='', $default=null){
		$value	= parent::get_arg($key, $default);

		if(is_null($value) && $this->model && $key && is_string($key) && strpos($key, '.') === false){
			$value	= $this->parse_method('get_'.$key, 'model');
		}

		return $value;
	}

	public function get_item_arg($item_key, $key, $default=null){
		$item	= $this->get_item($item_key);

		if($item){
			if(isset($item[$key])){
				return $item[$key];
			}

			if(static::get_config('item_arg') == 'model'){
				return $this->parse_method('get_'.$key, 'model', $item);
			}
		}

		return $default;
	}

	public function to_array(){
		return $this->get_args();
	}

	public function is_active(){
		return true;
	}

	// match($args=[], $operator='AND')
	// match($key, $value)
	public function match(...$args){
		$args[0]	= $args[0] ?? [];

		if(is_array($args[0])){
			if(!$args[0]){
				return true;
			}

			$operator	= $args[1] ?? 'AND';
			$match_args	= [];

			foreach($args[0] as $key => $value){
				if(is_string($value) && str_starts_with($value, 'match:')){
					$key	= $key.':'.wpjam_remove_prefix($value, 'match:');
					$value	= function($key){ return $this->match($key, null); };
				}
				
				$match_args[$key]	= $value;
			}

			return wpjam_match($this, $match_args, $operator);
		}else{
			$key	= $args[0];
			$value	= $args[1] ?? null;
			$null	= $args[2] ?? true;

			if(is_null($value) && strpos($key, ':')){
				$parts	= explode(':', $key);
				$key	= array_shift($parts);
				$value	= implode(':', $parts);
			}

			if($null && is_null($this->$key)){
				return true;
			}

			if(is_callable($this->$key)){
				if(wpjam_call($this->$key, $value, $this)){
					return true;
				}
			}else{
				if(wpjam_compare($value, (array)$this->$key)){
					return true;
				}
			}

			return false;
		}
	}

	public function data_type($slice){
		if($this->data_type){
			$data_type	= $slice['data_type'] ?? '';

			if($data_type != $this->data_type){
				return false;
			}

			if($this->$data_type){
				$type_value	= $slice[$data_type] ?? '';

				if(!$this->match($data_type, $type_value)){
					return false;
				}
			}
		}

		return true;
	}

	public function add_menu_page($item_key=''){
		$cb_args	= [$this->name];

		if($item_key){
			$menu_page	= $this->get_item_arg($item_key, 'menu_page');
			$cb_args[]	= $item_key;
		}else{
			$menu_page	= $this->from_item ? null : $this->get_arg('menu_page');
		}

		if($menu_page){
			if(is_callable($menu_page)){
				$menu_page	= call_user_func_array($menu_page, $cb_args);
			}

			if($menu_page){
				if(wp_is_numeric_array($menu_page)){
					wpjam_add_menu_page($menu_page);
				}elseif(isset($menu_page['plugin_page']) && isset($menu_page['tab_slug'])){
					if(isset($GLOBALS['plugin_page']) && $GLOBALS['plugin_page'] == $menu_page['plugin_page']){
						wpjam_add_menu_page($menu_page);
					}
				}else{
					wpjam_add_menu_page(wp_parse_args($menu_page, ['menu_slug'=>$this->name]));
				}
			}
		}

		if(!$item_key && static::get_config('item_arg')){
			foreach($this->get_item_keys() as $item_key){
				$this->add_menu_page($item_key);
			}
		}
	}

	public function admin_load($item_key=''){
		$cb_args	= [$this->name];

		if($item_key){
			$admin_load	= $this->get_item_arg($item_key, 'admin_load');
			$cb_args[]	= $item_key;
		}else{
			$admin_load	= $this->from_item ? null : $this->get_arg('admin_load');
		}

		if($admin_load){
			if(is_callable($admin_load)){
				$admin_load	= call_user_func_array($admin_load, $cb_args);
			}

			if($admin_load){
				wpjam_add_admin_load($admin_load);
			}
		}

		if(!$item_key && static::get_config('item_arg')){
			foreach($this->get_item_keys() as $item_key){
				$this->admin_load($item_key);
			}
		}
	}

	protected static $_registereds	= [];
	protected static $_hooked		= [];

	protected static function get_config($key){
		return null;
	}

	protected static function validate_name($name){
		if(empty($name)){
			trigger_error(self::class.'的注册 name 为空');
			return;
		}elseif(is_numeric($name)){
			trigger_error(self::class.'的注册 name「'.$name.'」'.'为纯数字');
			return;
		}elseif(!is_string($name)){
			trigger_error(self::class.'的注册 name「'.var_export($name, true).'」不为字符串');
			return;
		}

		return $name;
	}

	protected static function parse_group($group=null){
		if($group){
			return strtolower($group);
		}else{
			$group	= wpjam_remove_prefix(strtolower(get_called_class()), 'wpjam_');

			return $group == 'register' ? '' : $group;
		}
	}

	public static function preprocess_args($args){
		$model_config	= static::get_config('model');
		$model_config	= $model_config ?? true;

		$model	= $model_config ? array_get($args, 'model') : null;
		$hooks	= array_pull($args, 'hooks');
		$init	= array_pull($args, 'init');

		if($model || $hooks || $init){
			$file	= array_pull($args, 'file');

			if($file && is_file($file)){
				include_once $file;
			}
		}

		if($model && is_subclass_of($model, 'WPJAM_Register')){
			$model_class	= is_object($model) ? get_class($model) : $model;
			trigger_error('「'.$model_class.'」是 WPJAM_Register 子类');
		}

		if($model_config === 'object'){
			if(!$model){
				trigger_error('model 不存在');
			}

			if(!is_object($model)){
				if(!class_exists($model)){
					trigger_error('model 无效');
				}

				$model = $args['model']	= new $model($args);
			}
		}

		if($model){
			if($hooks === true || is_null($hooks)){
				if(method_exists($model, 'add_hooks')){
					$hooks	= [$model, 'add_hooks'];
				}
			}

			if($init === true || (is_null($init) && static::get_config('init'))){
				if(method_exists($model, 'init')){
					$init	= [$model, 'init'];
				}
			}
		}

		if($init && $init !== true){
			wpjam_load('init', $init);
		}

		if($hooks && $hooks !== true){
			wpjam_hooks($hooks);
		}

		$group	= self::parse_group();

		if($group && empty(self::$_hooked[$group])){
			self::$_hooked[$group]	= true;

			if(static::get_config('register_json')){
				add_action('wpjam_api', [get_called_class(), 'on_register_json']);
			}

			if(is_admin()){
				if(static::get_config('menu_page') || static::get_config('admin_load')){
					add_action('wpjam_admin_init', [get_called_class(), 'on_admin_init']);
				}
			}
		}

		return $args;
	}

	public static function register_by_group($group, ...$args){
		$group			= self::parse_group($group);
		$registereds	= self::$_registereds[$group] ?? [];

		if(is_object($args[0])){
			$args	= $args[0];
			$name	= $args->name;
		}elseif(is_array($args[0])){
			$args	= $args[0];
			$name	= '__'.count($registereds);
		}else{
			$name	= self::validate_name($args[0]);
			$args	= $args[1] ?? [];

			if(is_null($name)){
				return;
			}
		}

		if(is_object($args)){
			$object	= $args;
		}else{
			if(!empty($args['admin']) && !is_admin()){
				return;
			}

			$object	= new static($name, $args, $group);
			$name	= self::sanitize_name($name, $args);
		}

		if(isset($registereds[$name])){
			trigger_error($group.'「'.$name.'」已经注册。');
		}

		$orderby	= static::get_config('orderby');

		if($orderby){
			$orderby	= $orderby === true ? 'order' : $orderby;
			$current	= $object->$orderby = $object->$orderby ?? 10;
			$order		= static::get_config('order');
			$order		= $order ? strtoupper($order) : 'DESC';
			$sorted		= [];

			foreach($registereds as $_name => $_registered){
				if(!isset($sorted[$name])){
					$value	= $current - $_registered->$orderby;
					$value	= $order == 'DESC' ? $value : (0 - $value);

					if($value > 0){
						$sorted[$name]	= $object;
					}
				}

				$sorted[$_name]	= $_registered;
			}

			$sorted[$name]	= $object;

			self::$_registereds[$group]	= $sorted;
		}else{
			self::$_registereds[$group][$name]	= $object;
		}

		$registered	= static::get_config('registered');

		if($registered && method_exists($object, $registered)){
			if($registered == 'init'){
				wpjam_load('init', [$object, 'init']);
			}else{
				call_user_func([$object, $registered]);
			}
		}

		return $object;
	}

	public static function unregister_by_group($group, $name, $args=[]){
		$group	= self::parse_group($group);
		$name	= self::sanitize_name($name, $args);

		if(isset(self::$_registereds[$group][$name])){
			unset(self::$_registereds[$group][$name]);
		}
	}

	public static function get_by_group($group=null, $name=null, $args=[], $operator='AND'){
		if($name){
			if($args && static::get_config('data_type')){
				$objects	= self::get_by_group($group, null, wpjam_slice_data_type($args), $operator);
			}else{
				$objects	= self::get_by_group($group);
			}

			if(static::get_config('data_type') && !isset($args['data_type'])){
				$objects	= wp_filter_object_list($objects, ['name'=>$name]);

				if($objects && count($objects) == 1){
					return current($objects);
				}
			}else{
				if(isset($objects[$name])){
					return $objects[$name];
				}
			}

			return null;
		}

		$group		= self::parse_group($group);
		$objects	= self::$_registereds[$group] ?? [];

		if($args){
			if(static::get_config('data_type')){
				$data_type	= !empty($args['data_type']);
				$slice		= wpjam_slice_data_type($args, true);
			}else{
				$data_type	= false;
			}

			$filtered	= [];

			foreach($objects as $name => $object){
				if(static::get_config('data_type') && !$object->data_type($slice)){
					continue;
				}

				if($object->match($args, $operator)){
					if($data_type){
						$filtered[$object->name]	= $object;
					}else{
						$filtered[$name]	= $object;
					}
				}
			}

			return $filtered;
		}

		return $objects;
	}

	public static function register(...$args){
		return self::register_by_group(null, ...$args);
	}

	public static function registers($items){
		foreach($items as $name => $args){
			if(!self::get_by_group(null, $name, $args)){
				self::register($name, $args);
			}
		}
	}

	public static function unregister($name){
		self::unregister_by_group(null, $name);
	}

	public static function get_registereds($args=[], $output='objects', $operator='and'){
		$defaults	= static::get_config('defaults');

		if($defaults){
			self::registers($defaults);
		}

		$objects	= self::get_by_group(null, null, $args, $operator);

		if($output == 'names'){
			return array_keys($objects);
		}elseif(in_array($output, ['args', 'settings'])){
			return array_map(function($registered){
				return $registered->to_array();
			}, $objects);
		}else{
			return $objects;
		}
	}

	public static function get_by(...$args){
		if($args){
			$args	= is_array($args[0]) ? $args[0] : [$args[0] => $args[1]];
		}

		return self::get_registereds($args);
	}

	public static function get_by_model($model, $top=''){
		while($model && strcasecmp($model, $top) !== 0){
			foreach(self::get_registereds() as $object){
				if($object->model && is_string($object->model) && strcasecmp($object->model, $model) === 0){
					return $object;
				}

				if(static::get_config('item_arg') == 'model'){
					foreach($object->get_items() as $item){
						if(!empty($item['model']) && is_string($item['model']) && strcasecmp($item['model'], $model) === 0){
							return $object;
						}
					}
				}
			}

			$model	= get_parent_class($model);
		}

		return null;
	}

	public static function get_options_fields($args=[]){
		$args	= wp_parse_args($args, [
			'name'				=> self::parse_group(),
			'title'				=> '',
			'title_field'		=> 'title',
			'show_option_none'	=> __('&mdash; Select &mdash;'),
			'option_none_value'	=> '',
		]);

		$name		= $args['name'];
		$fields		= [$name => ['title'=>$args['title'], 'type'=>'select']];
		$options	= wp_list_pluck(self::get_registereds(), $args['title_field']);

		if($args['show_option_none']){
			$options	= array_merge([$args['option_none_value'] => $args['show_option_none']], $options);
		}

		$custom_fields	= static::get_config('custom_fields');

		if($custom_fields){
			$options['custom']	= '自定义';

			foreach($custom_fields as $field_key => $custom_field){
				$fields[$field_key]	= array_merge($custom_field, ['show_if'=>['key'=>$name, 'value'=>'custom']]);
			} 
		}

		$fields[$name]['options']	= $options;

		return $fields;
	}

	public static function get_setting_fields(){
		$fields	= [];

		foreach(self::get_registereds() as $name => $object){
			if(is_null($object->active)){
				$field	= $object->field ?: [];

				$fields[$name]	= wp_parse_args($field, [
					'title'			=> $object->title,
					'type'			=> 'checkbox',
					'description'	=> $object->description ?: '开启'.$object->title
				]);
			}
		}

		return $fields;
	}

	public static function get($name, $args=[]){
		if($name){
			$object = self::get_by_group(null, $name, $args);

			if(!$object){
				if($name == 'custom'){
					$custom_args	= static::get_config('custom_args');

					if(is_array($custom_args)){
						$object	= self::register($name, $custom_args);
					}
				}else{
					$defaults	= static::get_config('defaults');
					$default	= $defaults[$name] ?? null;

					if(is_array($default)){
						$object	= self::register($name, $default);
					}
				}
			}

			return $object;
		}

		return null;
	}

	public static function exists($name){
		return self::get($name) ? true : false;
	}

	protected static function sanitize_name($name, $args){
		if(static::get_config('data_type') && !empty($args['data_type'])){
			return $name.'__'.md5(maybe_serialize(wpjam_slice_data_type($args)));
		}

		return $name;
	}

	public static function get_active($key=null, ...$args){
		$return	= [];

		foreach(self::get_registereds() as $name => $object){
			$active	= $object->active ?? $object->is_active();

			if($active){
				if($key){
					$value	= $object->get_arg($key);

					if(is_callable($value)){
						$value	= call_user_func_array($value, $args);
					}

					if(!is_null($value)){
						$return[$name]	= $value;
					}
				}else{
					$return[$name]	= $object;
				}
			}
		}

		return $return;
	}

	public static function call_active($method, ...$args){
		if(str_starts_with($method, 'filter_')){
			$type	= 'filter_';
		}elseif(str_starts_with($method, 'get_')){
			$return	= [];
			$type	= 'get_';
		}else{
			$type	= '';
		}

		foreach(self::get_active() as $object){
			$result	= $object->call_method($method, ...$args);

			if(is_wp_error($result)){
				return $result;
			}

			if($type == 'filter_'){
				$args[0]	= $result;
			}elseif($type == 'get_'){
				if($result && is_array($result)){
					$return	= array_merge($return, $result);
				}
			}
		}

		if($type == 'filter_'){
			return $args[0];
		}elseif($type == 'get_'){
			return $return;
		}
	}

	public static function on_admin_init(){
		foreach(self::get_active() as $object){
			if(static::get_config('menu_page')){
				$object->add_menu_page();
			}

			if(static::get_config('admin_load')){
				$object->admin_load();
			}
		}
	}

	public static function on_register_json($json){
		return self::call_active('register_json', $json);
	}

	protected static function get_model($args){	// 兼容
		$file	= array_pull($args, 'file');

		if($file && is_file($file)){
			include_once $file;
		}

		return $args['model'] ?? null;
	}
}

class WPJAM_Meta_Type extends WPJAM_Register{
	public function __construct($name, $args=[]){
		$name	= sanitize_key($name);
		$args	= wp_parse_args($args, [
			'table_name'	=> $name.'meta',
			'table'			=> $GLOBALS['wpdb']->prefix.$name.'meta',
		]);

		if(!isset($GLOBALS['wpdb']->{$args['table_name']})){
			$GLOBALS['wpdb']->{$args['table_name']} = $args['table'];
		}

		parent::__construct($name, $args);
	}

	public function __call($method, $args){
		if(str_ends_with($method, '_meta')){
			$method	= str_replace('_meta', '_data', $method);
		}elseif(str_contains($method, '_meta')){
			$method	= str_replace('_meta', '', $method);
		}else{
			return;
		}

		if(method_exists($this, $method)){
			return call_user_func_array([$this, $method], $args);
		}
	}

	public function register_lazyloader(){
		return wpjam_register_lazyloader($this->name.'_meta', [
			'filter'	=> 'get_'.$this->name.'_metadata',
			'callback'	=> [$this, 'update_cache']
		]);
	}

	public function lazyload_data($ids){
		wpjam_lazyload($this->name.'_meta', $ids);
	}

	public function get_options($args=[]){
		$args		= array_merge($args, ['meta_type'=>$this->name]);
		$objects	= [];

		foreach(WPJAM_Meta_Option::get_by($args) as $option){
			$objects[$option->name]	= $option;
		}

		return $objects;
	}

	public function get_option($name){
		return WPJAM_Meta_Option::get($this->name.':'.$name);
	}

	public function register_option($name, $args){
		$args	= array_merge($args, ['meta_type'=>$this->name]);
		$object	= new WPJAM_Meta_Option($name, $args);

		return WPJAM_Meta_Option::register($this->name.':'.$name, $object);
	}

	public function unregister_option($name){
		return WPJAM_Meta_Option::unregister($this->name.':'.$name);
	}

	public function get_table(){
		return _get_meta_table($this->name);
	}

	public function get_column(){
		return $this->name.'_id';
	}

	public function get_data($id, $key='', $single=false){
		return get_metadata($this->name, $id, $key, $single);
	}

	public function get_data_with_default($id, ...$args){
		if(!$args){
			return $this->get_data($id);
		}

		if(is_array($args[0])){
			$data	= [];

			if($id && $args[0]){
				foreach($this->parse_defaults($args[0]) as $key => $default){
					$data[$key]	= $this->get_data_with_default($id, $key, $default);
				}
			}

			return $data;
		}else{
			if($id && $args[0]){
				if($args[0] == 'meta_input'){
					$data	= $this->get_data($id);

					foreach($data as $key => &$value){
						$value	= maybe_unserialize($value[0]);
					}

					return $data;
				}

				if(metadata_exists($this->name, $id, $args[0])){
					return $this->get_data($id, $args[0], true);
				}
			}

			return $args[1] ?? null;
		}
	}

	public function add_data($id, $key, $value, $unique=false){
		return add_metadata($this->name, $id, $key, wp_slash($value), $unique);
	}

	public function update_data($id, $key, $value, $prev_value=''){
		return update_metadata($this->name, $id, $key, wp_slash($value), $prev_value);
	}

	public function update_data_with_default($id, ...$args){
		if(is_array($args[0])){
			$data	= $args[0];

			if(wpjam_is_assoc_array($data)){
				if((isset($args[1]) && is_array($args[1]))){
					$defaults	= $this->parse_defaults($args[1]);
				}else{
					$defaults	= array_fill_keys(array_keys($data), null);
				}

				if(isset($data['meta_input']) && wpjam_is_assoc_array($data['meta_input'])){
					$this->update_data_with_default($id, array_pull($data, 'meta_input'), array_pull($defaults, 'meta_input'));
				}

				foreach($data as $key => $value){
					$this->update_data_with_default($id, $key, $value, array_pull($defaults, $key));
				}
			}

			return true;
		}else{
			$key		= $args[0];
			$value		= $args[1];
			$default	= $args[2] ?? null;

			if(is_array($value)){
				if($value && (!is_array($default) || array_diff_assoc($default, $value))){
					return $this->update_data($id, $key, $value);
				}
			}else{
				if(isset($value) && ((is_null($default) && $value) || (!is_null($default) && $value != $default))){
					return $this->update_data($id, $key, $value);
				}
			}

			return $this->delete_data($id, $key);
		}
	}

	public function delete_data($id, $key, $value=''){
		return delete_metadata($this->name, $id, $key, $value);
	}

	public function delete_orphan_data($primary_table=null, $primary_key=null){
		$primary_table	= $primary_table ?: $this->primary_table;
		$primary_key	= $primary_key ?: ($this->primary_key ?: 'id');

		if($primary_table && $primary_key){
			$wpdb	= $GLOBALS['wpdb'];
			$table	= $this->get_table();
			$column	= $this->get_column();
			$mids	= $wpdb->get_col("SELECT m.meta_id FROM {$table} m LEFT JOIN {$primary_table} t ON t.{$primary_key} = m.{$column} WHERE t.{$primary_key} IS NULL") ?: [];

			foreach($mids as $mid){
				$this->delete_by_mid($mid);
			}
		}
	}

	public function delete_by_key($key, $value=''){
		return delete_metadata($this->name, null, $key, $value, true);
	}

	public function delete_by_mid($mid){
		return delete_metadata_by_mid($this->name, $mid);
	}

	public function delete_by_id($id){
		$wpdb	= $GLOBALS['wpdb'];
		$table	= $this->get_table();
		$column	= $this->get_column();
		$mids	= $wpdb->get_col($wpdb->prepare("SELECT meta_id FROM {$table} WHERE {$column} = %d ", $id));
		
		foreach($mids as $mid){
			$this->delete_by_mid($mid);
		}
	}

	public function get_by_key(...$args){
		global $wpdb;

		if(empty($args)){
			return [];
		}

		if(is_array($args[0])){
			$key	= $args[0]['meta_key'] ?? ($args[0]['key'] ?? '');
			$value	= $args[0]['meta_value'] ?? ($args[0]['value'] ?? '');
		}else{
			$key	= $args[0];
			$value	= $args[1] ?? null;
		}

		$where	= [];

		if($key){
			$where[]	= $wpdb->prepare('meta_key=%s', $key);
		}

		if(!is_null($value)){
			$where[]	= $wpdb->prepare('meta_value=%s', maybe_serialize($value));
		}

		if(!$where){
			return [];
		}

		$where	= implode(' AND ', $where);
		$table	= $this->get_table();
		$data	= $wpdb->get_results("SELECT * FROM {$table} WHERE {$where}", ARRAY_A) ?: [];

		foreach($data as &$item){
			$item['meta_value']	= maybe_unserialize($item['meta_value']);
		}

		return $data;
	}

	public function update_cache($ids){
		if($ids){
			update_meta_cache($this->name, $ids);
		}
	}

	public function create_table(){
		$table	= $this->get_table();

		if($GLOBALS['wpdb']->get_var("show tables like '{$table}'") != $table){
			$column	= $this->name.'_id';

			$GLOBALS['wpdb']->query("CREATE TABLE {$table} (
				meta_id bigint(20) unsigned NOT NULL auto_increment,
				{$column} bigint(20) unsigned NOT NULL default '0',
				meta_key varchar(255) default NULL,
				meta_value longtext,
				PRIMARY KEY  (meta_id),
				KEY {$column} ({$column}),
				KEY meta_key (meta_key(191))
			)");
		}
	}

	public static function parse_defaults($defaults){
		$return	= [];

		foreach($defaults as $key => $default){
			if(is_numeric($key)){
				if(is_numeric($default)){
					continue;
				}

				$key		= $default;
				$default	= null;
			}

			$return[$key]	= $default;
		}

		return $return;
	}

	public static function get_config($key){
		if($key == 'defaults'){
			$defaults	= [
				'post'		=> [],
				'term'		=> [],
				'user'		=> [],
				'comment'	=> [],
			];

			if(is_multisite()){
				$defaults['blog']	= [];
				$defaults['site']	= [];
			}

			return $defaults;
		}
	}
}

class WPJAM_Meta_Option extends WPJAM_Register{
	#[ReturnTypeWillChange]
	public function offsetGet($key){
		$value	= parent::offsetGet($key);

		if($key == 'list_table' && is_null($value) && did_action('current_screen') && !empty($GLOBALS['plugin_page'])){
			return true;
		}

		return $value;
	}

	public function parse_args(){
		$args		= $this->args;
		$meta_type	= $args['meta_type'] ?? '';

		if(empty($args['callback']) && isset($args['update_callback'])){
			$args['callback']	= array_pull($args, 'update_callback');
		}

		if($meta_type == 'post'){
			$args	= wp_parse_args($args, ['fields'=>[], 'priority'=>'default']);

			if(!isset($args['post_type']) && isset($args['post_types'])){
				$args['post_type']	= array_pull($args, 'post_types') ?: null;
			}
		}elseif($meta_type == 'term'){
			if(!isset($args['taxonomy']) && isset($args['taxonomies'])){
				$args['taxonomy']	= array_pull($args, 'taxonomies') ?: null;
			}

			if(!isset($args['fields'])){
				$args['fields']		= [$this->name => array_except($args, 'taxonomy')];
				$args['from_field']	= true;
			}
		}

		return $args;
	}

	public function get_fields($id=null, $type=''){
		if(is_callable($this->fields)){
			$fields	= call_user_func($this->fields, $id, $this->name);

			return $type == 'object' ? WPJAM_Fields::create($fields) : $fields;
		}

		if($type == 'object'){
			if(is_null($this->_fields_object)){
				$this->_fields_object	= WPJAM_Fields::create($this->fields);
			}

			return $this->_fields_object;
		}else{
			return $this->fields;
		}
	}

	public function parse_list_table_args(){
		return wp_parse_args($this->get_args(), [
			'page_title'	=> '设置'.$this->title,
			'submit_text'	=> '设置',
			'meta_type'		=> $this->name,
			'fields'		=> [$this, 'get_fields']
		]);
	}

	public function prepare($id=null){
		if($this->callback){
			return [];
		}

		$args	= array_merge($this->get_args(), ['id'=>$id]);

		return $this->get_fields($id, 'object')->prepare($args);
	}

	public function validate($id=null){
		return $this->get_fields($id, 'object')->validate();
	}

	public function render($id, $args=[]){
		$args	= wp_parse_args($args, ['id'=>$id]);
		$args	= wp_parse_args($args, $this->get_args());

		$this->get_fields($id, 'object')->render($args);
	}

	public function callback($id, $data=null){
		if(is_null($data)){
			$data	= $this->validate($id);
		}

		if(is_wp_error($data)){
			return $data;
		}elseif(empty($data)){
			return true;
		}

		if($this->callback){
			if(!is_callable($this->callback)){
				return new WP_Error('invalid_callback');
			}

			$fields	= $this->get_fields($id);
			$result	= call_user_func($this->callback, $id, $data, $fields);

			if($result === false){
				return new WP_Error('invalid_callback');
			}

			return $result;
		}else{
			$defaults	= $this->get_fields($id, 'object')->get_defaults();

			return wpjam_update_metadata($this->meta_type, $id, $data, $defaults);
		}
	}

	public function list_table($value=null){
		if($this->title){
			if($value){
				return (bool)$this->list_table;
			}else{
				return $this->list_table !== 'only';
			}
		}

		return false;
	}

	public static function create($name, $args){
		$meta_type	= array_get($args, 'meta_type');

		if($meta_type){
			$object	= new self($name, $args);

			return self::register($meta_type.':'.$name, $object);
		}
	}

	public static function get_by(...$args){
		$args		= is_array($args[0]) ? $args[0] : [$args[0] => $args[1]];
		$list_table	= array_pull($args, 'list_table');
		$meta_type	= array_get($args, 'meta_type');

		if(!$meta_type){
			return [];
		}

		if(isset($list_table)){
			$list_table_key	= 'list_table:'.(int)$list_table;

			$args[$list_table_key]	= function($value){
				$parts	= explode(':', $value);
				$value	= $parts[1] ?? null;

				return $this->list_table($value);
			};
		}

		if($meta_type == 'post'){
			$post_type	= array_pull($args, 'post_type');

			if($post_type){
				$object	= wpjam_get_post_type_object($post_type);

				if($object){
					$object->register_option($list_table);
				}

				$args['post_type']	= 'match:'.$post_type;
			}
		}elseif($meta_type == 'term'){
			$taxonomy	= array_pull($args, 'taxonomy');
			$action		= array_pull($args, 'action');

			if($taxonomy){
				$object	= wpjam_get_taxonomy_object($taxonomy);

				if($object){
					$object->register_option($list_table);
				}

				$args['taxonomy']	= 'match:'.$taxonomy;
			}

			if($action){
				$args['action']		= 'match:'.$action;
			}
		}

		return static::get_registereds($args);
	}

	public static function get_config($key){
		if($key == 'orderby'){
			return 'order';
		}
	}
}

class WPJAM_Lazyloader extends WPJAM_Register{
	private $pending_objects	= [];

	public function callback($check){
		if($this->pending_objects){
			if($this->accepted_args && $this->accepted_args > 1){
				foreach($this->pending_objects as $object){
					call_user_func($this->callback, $object['ids'], ...$object['args']);
				}
			}else{
				call_user_func($this->callback, $this->pending_objects);
			}

			$this->pending_objects	= [];
		}

		remove_filter($this->filter, [$this, 'callback']);

		return $check;
	}

	public function queue_objects($object_ids, ...$args){
		if(!$object_ids){
			return;
		}

		if($this->accepted_args && $this->accepted_args > 1){
			if((count($args)+1) >= $this->accepted_args){
				$key	= wpjam_json_encode($args);

				if(!isset($this->pending_objects[$key])){
					$this->pending_objects[$key]	= ['ids'=>[], 'args'=>$args];
				}

				$this->pending_objects[$key]['ids']	= array_merge($this->pending_objects[$key]['ids'], $object_ids);
				$this->pending_objects[$key]['ids']	= array_unique($this->pending_objects[$key]['ids']);
			}
		}else{
			$this->pending_objects	= array_merge($this->pending_objects, $object_ids);
			$this->pending_objects	= array_unique($this->pending_objects);
		}

		add_filter($this->filter, [$this, 'callback']);
	}
}

class WPJAM_AJAX extends WPJAM_Register{
	public function __construct($name, $args=[]){
		parent::__construct($name, $args);

		add_action('wp_ajax_'.$name, [$this, 'callback']);

		if(!empty($args['nopriv'])){
			add_action('wp_ajax_nopriv_'.$name, [$this, 'callback']);
		}
	}

	public function callback(){
		if(!$this->callback || !is_callable($this->callback)){
			wp_die('0', 400);
		}

		if($this->verify !== false){
			$nonce	= wpjam_get_post_parameter('_ajax_nonce');

			if(!wp_verify_nonce($nonce, $this->get_nonce_action([], 'verify'))){
				wpjam_send_error_json('invalid_nonce');
			}
		}

		wpjam_send_json(call_user_func($this->callback));
	}

	public function get_attr($data=[], $return=null){
		$attr	= ['action'=>$this->name, 'data'=>$data];

		if($this->verify !== false){
			$attr['nonce']	= wp_create_nonce($this->get_nonce_action($data, 'create'));
		}

		return $return ? $attr : wpjam_attr($attr, 'data');
	}

	protected function get_nonce_action($args, $type='create'){
		$nonce_action	= $this->name;

		if($this->nonce_keys){
			foreach($this->nonce_keys as $key){
				if($type == 'verify'){
					$value	= wpjam_get_data_parameter($key);
				}else{
					$value	= $args[$key] ?? '';
				}

				if($value){
					$nonce_action	.= ':'.$value;
				}
			}
		}

		return $nonce_action;
	}

	public static function enqueue_scripts(){
		if(!wp_script_is('wpjam-ajax', 'enqueued')){
			wp_enqueue_script('wpjam-ajax',	WPJAM_BASIC_PLUGIN_URL.'static/ajax.js', ['jquery']);

			$scripts	= '
				if(typeof ajaxurl == "undefined"){
					var ajaxurl	= "'.admin_url('admin-ajax.php').'";
				}
			';

			wp_add_inline_script('wpjam-ajax', str_replace("\n\t\t\t\t", "\n", $scripts), 'before');
		}
	}
}

class WPJAM_Verification_Code extends WPJAM_Register{
	public function parse_args(){
		return wp_parse_args($this->args, [
			'failed_times'	=> 5,
			'cache_time'	=> MINUTE_IN_SECONDS*30,
			'interval'		=> MINUTE_IN_SECONDS,
			'cache'			=> wpjam_cache('verification_code', ['global'=>true, 'prefix'=>$this->name]),
		]);
	}

	public function is_over($key){
		if($this->failed_times && (int)$this->cache->get($key.':failed_times') > $this->failed_times){
			return new WP_Error('quota_exceeded', ['尝试的失败次数', '请15分钟后重试。']);
		}

		return false;
	}

	public function generate($key){
		if($over = $this->is_over($key)){
			return $over;
		}

		if($this->interval && $this->cache->get($key.':time') !== false){
			return new WP_Error('error', '验证码'.((int)($this->interval/60)).'分钟前已发送了。');
		}

		$code = rand(100000, 999999);

		$this->cache->set($key.':code', $code, $this->cache_time);

		if($this->interval){
			$this->cache->set($key.':time', time(), MINUTE_IN_SECONDS);
		}

		return $code;
	}

	public function verify($key, $code){
		if($over = $this->is_over($key)){
			return $over;
		}

		$current	= $this->cache->get($key.':code');

		if(!$code || $current === false){
			return new WP_Error('invalid_code');
		}

		if($code != $current){
			if($this->failed_times){
				$failed_times	= $this->cache->get($key.':failed_times') ?: 0;
				$failed_times	= $failed_times + 1;

				$this->cache->set($key.':failed_times', $failed_times, $this->cache_time/2);
			}

			return new WP_Error('invalid_code');
		}

		return true;
	}

	public static function get_instance($name, $args=[]){
		return self::get($name) ?: self::register($name, $args);
	}
}

class WPJAM_Verify_TXT extends WPJAM_Register{
	public function get_fields(){
		return [
			'name'	=>['title'=>'文件名称',	'type'=>'text',	'required', 'value'=>$this->get_data('name'),	'class'=>'all-options'],
			'value'	=>['title'=>'文件内容',	'type'=>'text',	'required', 'value'=>$this->get_data('value')]
		];
	}

	public function get_data($key=''){
		$data	= wpjam_get_setting('wpjam_verify_txts', $this->name) ?: [];

		return $key ? ($data[$key] ?? '') : $data;
	}

	public function set_data($data){
		return wpjam_update_setting('wpjam_verify_txts', $this->name, $data) || true;
	}

	public static function __callStatic($method, $args){	// 放弃
		$name	= $args[0];

		if($object = self::get($name)){
			if(in_array($method, ['get_name', 'get_value'])){
				return $object->get_data(str_replace('get_', '', $method));
			}elseif($method == 'set' || $method == 'set_value'){
				return $object->set_data(['name'=>$args[1], 'value'=>$args[2]]);
			}
		}
	}

	public static function filter_root_rewrite_rules($root_rewrite){
		if(empty($GLOBALS['wp_rewrite']->root)){
			$home_path	= parse_url(home_url());

			if(empty($home_path['path']) || '/' == $home_path['path']){
				$root_rewrite	= array_merge(['([^/]+)\.txt?$'=>'index.php?module=txt&action=$matches[1]'], $root_rewrite);
			}
		}

		return $root_rewrite;
	}

	public static function get_rewrite_rule(){
		add_filter('root_rewrite_rules',	[self::class, 'filter_root_rewrite_rules']);
	}

	public static function redirect($action){
		$txts = wpjam_get_option('wpjam_verify_txts');

		if($txts){
			$name	= str_replace('.txt', '', $action).'.txt';

			foreach($txts as $txt) {
				if($txt['name'] == $name){
					header('Content-Type: text/plain');
					echo $txt['value'];

					exit;
				}
			}
		}
	}
}