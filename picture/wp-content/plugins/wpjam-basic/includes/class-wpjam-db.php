<?php
class WPJAM_DB extends WPJAM_Args{
	protected $query_vars	= [];
	protected $where		= [];
	protected $meta_query	= false;

	public function __construct($table, array $args=[]){
		$this->args	= wp_parse_args($args, [
			'table'				=> $table,
			'primary_key'		=> 'id',
			'meta_type'			=> '',
			'cache'				=> true,
			'cache_key'			=> '',
			'cache_prefix'		=> '',
			'cache_group'		=> $table,
			'cache_time'		=> DAY_IN_SECONDS,
			'field_types'		=> [],
			'searchable_fields'	=> [],
			'filterable_fields'	=> []
		]);

		if($this->cache && !$this->cache_key){
			$this->cache_key	= $this->primary_key;
		}

		if(is_array($this->cache_group)){
			$group	= $this->cache_group[0];
			$global	= $this->cache_group[1] ?? false;

			if($global){
				wp_cache_add_global_groups($group);
			}

			$this->cache_group	= $group;
		}

		$this->clear();
	}

	public function __get($key){
		if(isset($this->query_vars[$key])){
			return $this->query_vars[$key];
		}

		return parent::__get($key);
	}

	public function __call($method, $args){
		if(str_starts_with($method, 'where_')){
			$method	= wpjam_remove_prefix($method, 'where_');
			$map	= [
				'not'		=> '!=',
				'lt'		=> '<',
				'lte'		=> '<=',
				'gt'		=> '>',
				'gte'		=> '>=',
				'in'		=> 'IN',
				'not_in'	=> 'NOT IN',
				'like'		=> 'LIKE',
				'not_like'	=> 'NOT LIKE',
			];

			$compare	= $map[$method] ?? '';

			if($compare){
				return $this->where($args[0], $args[1], $compare);
			}
		}elseif(in_array($method, [
			'found_rows',
			'limit',
			'offset',
			'orderby',
			'order',
			'groupby',
			'having',
			'search',
			'order_by',
			'group_by',
		])){
			$map	= [
				'search'	=> 'search_term',
				'order_by'	=> 'orderby',
				'group_by'	=> 'groupby',
			];

			$key	= $map[$method] ?? $method;

			if($key == 'order'){
				$value	= $args[0] ?? 'DESC';
			}elseif($key == 'found_rows'){
				$value	= $args[0] ?? true;
				$value	= (bool)$value;
			}else{
				$value	= $args[0] ?? null;
			}

			if(!is_null($value)){
				if(in_array($key, ['limit', 'offset'])){
					$value	= (int)$value;
				}elseif($key == 'order'){
					$value	= (strtoupper($value) == 'ASC') ? 'ASC' : 'DESC';
				}

				$this->query_vars[$key]	= $value;
			}

			return $this;
		}elseif(in_array($method, [
			'get_col',
			'get_var',
			'get_row',
		])){
			$field	= $args[0] ?? '';
			$args	= [$this->get_sql($field)];

			if($method == 'get_row'){
				$args[]	= ARRAY_A;
			}

			return call_user_func_array([$GLOBALS['wpdb'], $method], $args);
		}elseif(in_array($method, [
			'get_table',
			'get_primary_key',
			'get_meta_type',
			'get_cache_group',
			'get_cache_prefix',
			'get_searchable_fields',
			'get_filterable_fields'
		])){
			return $this->{substr($method, 4)};
		}elseif(in_array($method, [
			'set_meta_query',
			'set_searchable_fields',
			'set_filterable_fields'
		])){
			$this->{substr($method, 4)}	= $args[0];
		}elseif(in_array($method, [
			'cache_key',
			'cache_keys',
			'cache_get',
			'cache_get_force',
			'cache_get_by_primary_key',
			'cache_delete',
			'cache_delete_force',
			'cache_delete_by_primary_key',
			'cache_add',
			'cache_add_force',
			'cache_add_by_primary_key',
			'cache_set',
			'cache_set_force',
			'cache_set_by_primary_key'
		])){
			if(str_ends_with($method, '_force')){
				$method	= wpjam_remove_postfix($method, '_force');
			}else{
				if(!$this->cache){
					return false;
				}
			}

			$key	= $args[0];

			if(!is_scalar($key)){
				trigger_error(var_export($key, true));
				return false;
			}

			$primary	= str_ends_with($method, '_by_primary_key');
			$method		= wpjam_remove_postfix($method, '_by_primary_key');;
			$key		= $this->get_cache_key($key, $primary);
			$group		= $this->cache_group;

			if(in_array($method, ['cache_get', 'cache_delete'])){
				return call_user_func('wp_'.$method, $key, $group);
			}else{
				$data	= $args[1];
				$time	= !empty($args[2]) ? (int)$args[2] : $this->cache_time;

				return call_user_func('wp_'.$method, $key, $data, $group, $time);
			}
		}elseif(in_array($method, [
			'get_meta',
			'add_meta',
			'update_meta',
			'delete_meta',
			'delete_orphan_meta',
			'lazyload_meta',
			'delete_meta_by_key',
			'delete_meta_by_mid',
			'delete_meta_by_id',
			'update_meta_cache',
			'create_meta_table',
			'get_meta_table',
			'get_meta_column',
		])){
			$object	= wpjam_get_meta_type_object($this->meta_type);

			if($object){
				return call_user_func([$object, $method], ...$args);
			}
		}

		return new WP_Error('undefined_method', [$method]);
	}

	public function clear(){
		$this->query_vars	= [
			'found_rows'	=> false,
			'limit'			=> 0,
			'offset'		=> 0,
			'orderby'		=> null,
			'order'			=> null,
			'groupby'		=> null,
			'having'		=> null,
			'search_term'	=> null
		];

		$this->where	= [];
	}

	public function get_last_changed(){
		return wp_cache_get_last_changed($this->cache_group);
	}

	public function set_last_changed(){
		wp_cache_set('last_changed', microtime(), $this->cache_group);
	}

	public function get_cache_key($key, $primary=false){
		if(is_array($key)){
			$map	= [];

			foreach($key as $k){
				$map[$k]	= $this->get_cache_key($k, $primary);
			}

			return $map;	
		}else{
			if(!$primary && $this->cache_key != $this->primary_key){
				$key	= $this->cache_key.':'.$key;
			}

			return $this->cache_prefix ? $this->cache_prefix.':'.$key : $key;
		}
	}

	public function cache_delete_by_conditions($conditions, $data=[]){
		if($this->cache){
			if(is_array($conditions)){
				if($data){
					if(!empty($data[$this->primary_key])){
						$this->cache_delete_by_primary_key($data[$this->primary_key]);

						if(isset($conditions[$this->primary_key])){
							$conditions[$this->primary_key]	= (array)$conditions[$this->primary_key];
						}else{
							$conditions[$this->primary_key]	= [];
						}

						$conditions[$this->primary_key][]	= $data[$this->primary_key];
					}

					if($this->primary_key != $this->cache_key && !empty($data[$this->cache_key])){
						$this->cache_delete($data[$this->cache_key]);

						$conditions[$this->cache_key]	= $data[$this->cache_key];
					}
				}

				$conditions	= $this->where_any($conditions, 'fragment');
			}

			if($conditions){
				$fields	= "{$this->primary_key}";

				if($this->cache_key != $this->primary_key){
					$fields	.= ", {$this->cache_key}";
				}

				$results	= $GLOBALS['wpdb']->get_results("SELECT {$fields} FROM `{$this->table}` WHERE {$conditions}", ARRAY_A) ?: [];

				foreach($results as $result){
					$this->cache_delete_by_primary_key($result[$this->primary_key]);

					if($this->cache_key != $this->primary_key){
						$this->cache_delete($result[$this->cache_key]);
					}
				}
			}
		}
	}

	public function find_by($field, $value, $order='ASC', $method='get_results'){
		$wpdb	= $GLOBALS['wpdb'];
		$format	= $this->process_field_formats($field);
		$sql	= "SELECT * FROM `{$this->table}` WHERE `{$field}` = {$format}";
		$sql	= $order ? $sql." ORDER BY `{$this->primary_key}` {$order}" : $sql;
		$sql	= $wpdb->prepare($sql, $value);

		return call_user_func([$wpdb, $method], $sql, ARRAY_A);
	}

	public function find_one_by($field, $value, $order=''){
		return $this->find_by($field, $value, $order, 'get_row');
	}

	public function find_one($id){
		return $this->find_one_by($this->primary_key, $id);
	}

	public function get($id){
		if($this->cache){
			$result	= $this->cache_get_by_primary_key($id);

			if($result === false){
				$result	= $this->find_one($id);
				$time	= $result ? $this->cache_time : MINUTE_IN_SECONDS;

				$this->cache_set_by_primary_key($id, $result, $time);
			}

			return $result;
		}

		return $this->find_one($id);
	}

	public function get_by($field, $value, $order='ASC'){
		if($this->cache && $field == $this->primary_key){
			return $this->get($value);
		}

		$cache	= $this->cache && $field == $this->cache_key;
		$result	= $cache ? $this->cache_get($value) : false;

		if($result === false){
			$result	= $this->find_by($field, $value, $order);

			if($cache){
				$time	= $result ? $this->cache_time : MINUTE_IN_SECONDS;

				$this->cache_set($value, $result, $time);
			}
		}

		return $result;
	}

	public function update_caches($keys, $primary=false){
		$keys	= wp_parse_list($keys);
		$keys	= array_filter($keys);
		$keys	= array_unique($keys);
		$data	= [];

		if(!$keys){
			return $data;
		}

		if($this->cache){
			$cache_keys		= $this->get_cache_key($keys, $primary);
			$cache_map		= array_flip($cache_keys);
			$cache_values	= wp_cache_get_multiple($cache_keys, $this->cache_group);

			foreach($cache_values as $cache_key => $cache_value){
				if($cache_value !== false){
					$key	= $cache_map[$cache_key];

					$data[$key]	= $cache_value;
				}
			}
		}

		if(count($data) != count($keys)){
			$data	= [];
			$field	= $primary ? $this->primary_key : $this->cache_key;
			$result = $GLOBALS['wpdb']->get_results($this->where_in($field, $keys)->get_sql(), ARRAY_A);

			if($result){
				if($field == $this->primary_key){
					$data	= array_combine(array_column($result, $this->primary_key), $result);
				}else{
					foreach($keys as $key){
						$data[$key]	= array_values(wp_list_filter($result, [$field => $key]));
					}
				}
			}

			if($this->cache){
				foreach($cache_keys as $key => $cache_key){
					$value	= $data[$key] ?? [];
					$time	= $value ? $this->cache_time : MINUTE_IN_SECONDS;

					wp_cache_set($cache_key, $value, $this->cache_group, $time);
				}
			}
		}

		if($this->meta_type){
			$ids	= [];

			if($primary){
				foreach($data as $id => $item){
					if($item){
						$ids[]	= $id;
					}
				}
			}else{
				foreach($data as $items){
					if($items){
						$ids	= array_merge($ids, array_column($items, $this->primary_key));
					}
				}
			}

			$this->lazyload_meta($ids);
		}

		return $data;
	}

	public function get_ids($ids){
		return self::update_caches($ids, $primary=true);
	}

	public function get_by_ids($ids){
		return self::get_ids($ids);
	}

	protected function parse_orderby($orderby){
		if($orderby == 'rand'){
			return 'RAND()';
		}elseif(preg_match('/RAND\(([0-9]+)\)/i', $orderby, $matches)){
			return sprintf('RAND(%s)', (int)$matches[1]);
		}elseif(str_ends_with($orderby, '__in')){
			return '';	// 应该在 WPJAM_Query 里面处理
			// $field	= str_replace('__in', '', $orderby);
		}

		if($this->meta_type && $this->meta_query){
			$primary_meta_key	= '';
			$primary_meta_query	= false;
			$meta_clauses		= $this->meta_query->get_clauses();

			if(!empty($meta_clauses)){
				$primary_meta_query	= reset($meta_clauses);

				if(!empty($primary_meta_query['key'])){
					$primary_meta_key	= $primary_meta_query['key'];
				}

				if($orderby == $primary_meta_key || $orderby == 'meta_value'){
					if(!empty($primary_meta_query['type'])){
						return "CAST({$primary_meta_query['alias']}.meta_value AS {$primary_meta_query['cast']})";
					}else{
						return "{$primary_meta_query['alias']}.meta_value";
					}
				}elseif($orderby == 'meta_value_num'){
					return "{$primary_meta_query['alias']}.meta_value+0";
				}elseif(array_key_exists($orderby, $meta_clauses)){
					$meta_clause	= $meta_clauses[$orderby];
					return "CAST({$meta_clause['alias']}.meta_value AS {$meta_clause['cast']})";
				}
			}
		}
		
		if($orderby == 'meta_value_num' || $orderby == 'meta_value'){
			return '';
		}

		return '`'.sanitize_key($orderby).'`';
	}

	protected function parse_order($order){
		if(!is_string($order) || empty($order)){
			return 'DESC';
		}

		return 'ASC' === strtoupper($order) ? 'ASC' : 'DESC';
	}

	public function get_clauses($fields=[]){
		$distinct	= '';
		$where		= '';
		$join		= '';
		$groupby	= $this->groupby ?: '';

		if($this->meta_type && $this->meta_query){
			$clauses	= $this->meta_query->get_sql($this->meta_type, $this->table, $this->primary_key, $this);

			$where	= $clauses['where'];
			$join	= $clauses['join'];

			if(!empty($this->meta_query->queries)){
				$groupby	= $groupby ?: $this->table.'.'.$this->primary_key;
				$fields		= $fields ?: $this->table.'.*';
			}
		}

		if($fields){
			if(is_array($fields)){
				$fields	= '`'.implode( '`, `', $fields ). '`';
				$fields	= esc_sql($fields); 
			}
		}else{
			$fields = '*';
		}

		if($groupby){
			if(strstr($groupby, ',') !== false || strstr($groupby, '(') !== false || strstr($groupby, '.') !== false){
				$groupby	= ' GROUP BY ' . $groupby;
			}else{
				$groupby	= ' GROUP BY `' . $groupby . '`';
			}
		}

		$having		= $this->having ? ' HAVING ' . $having : '';
		$orderby	= is_null($this->orderby) ? $this->primary_key : $this->orderby;

		if($orderby){
			if(is_array($orderby)){
				$orderby_array	= [];

				foreach($orderby as $_orderby => $order){
					$_orderby	= addslashes_gpc(urldecode($_orderby));

					if($parsed = $this->parse_orderby($_orderby)){
						$orderby_array[]	=  $parsed . ' ' . $this->parse_order($order);
					}
				}

				$orderby	= $orderby_array ? ' ORDER BY '.implode(', ', $orderby_array) : '';
			}elseif(strstr($orderby, '(') !== false && strstr($orderby, ')') !== false){
				$orderby	= ' ORDER BY ' . $orderby;
			}elseif(strstr($orderby, ',') !== false ){
				$orderby	= ' ORDER BY ' . $orderby;
			}else{
				$orderby	= addslashes_gpc(urldecode($orderby));

				if($parsed = $this->parse_orderby($orderby)){
					if($orderby == 'RAND()'){
						$order	= '';
					}else{
						$order	= $this->order ?: 'DESC';
					}
					
					$orderby	= ' ORDER BY ' . $parsed . ' ' . $order;
				}else{
					$orderby	= '';
				}
			}
		}

		$limits		= $this->limit ? ' LIMIT ' . $this->limit : '';
		$limits 	.= $this->offset ? ' OFFSET ' . $this->offset : '';
		$found_rows	= ($limits && $this->found_rows) ? 'SQL_CALC_FOUND_ROWS' : '';
		$conditions	= $this->get_conditions();

		if(!$conditions && $where){
			$where	= 'WHERE 1=1 '.$where;
		}else{
			$where	= $conditions.$where;
			$where	= $where ? ' WHERE '.$where : '';
		}

		return compact(...(self::get_clause_keys()));
	}

	public function get_clause_keys(){
		return ['found_rows', 'distinct', 'fields', 'join', 'where', 'groupby', 'having', 'orderby', 'limits'];
	}

	public function get_sql_by_clauses($clauses){
		$keys		= self::get_clause_keys();
		$clauses	= array_merge(array_fill_keys($keys, ''), $clauses);

		return sprintf("SELECT %s %s %s FROM `{$this->table}` %s %s %s %s %s %s", ...array_values($clauses));
	}

	public function get_sql($fields=[], &$clauses=[]){
		$clauses	= $this->get_clauses($fields);

		return $this->get_sql_by_clauses($clauses);
	}

	public function get_results($fields=[]){
		$sql		= $this->get_sql($fields, $clauses);
		$results	= $GLOBALS['wpdb']->get_results($sql, ARRAY_A);

		return $this->filter_results($results, $clauses['fields']);
	}

	public function filter_results($results, $fields){
		if(!$results || ($fields != '*' && $fields != $this->table.'.*')){
			return $results;
		}
		
		$ids	= [];

		foreach($results as $result){
			if(!empty($result[$this->primary_key])){
				$id		= $result[$this->primary_key];
				$ids[]	= $id;

				$this->cache_set_by_primary_key($id, $result);
			}
		}

		if($ids){
			if($this->lazyload_callback){
				call_user_func($this->lazyload_callback, $ids, $results);
			}

			if($this->meta_type){
				$this->lazyload_meta($ids);
			}
		}

		return $results;
	}

	public function find($fields=[], $func='get_results'){
		return $this->$func($fields);
	}

	public function find_total(){
		return $GLOBALS['wpdb']->get_var("SELECT FOUND_ROWS();");
	}

	public function get_request(){
		return $GLOBALS['wpdb']->last_query;
	}

	public function insert_multi($datas){	// 使用该方法，自增的情况可能无法无法删除缓存，请注意
		if(empty($datas)){
			return 0;
		}

		$this->set_last_changed();

		$wpdb		= $GLOBALS['wpdb'];
		$data		= current($datas);
		$formats	= $this->process_field_formats($data);
		$values		= [];
		$fields		= '`'.implode('`, `', array_keys($data)).'`';
		$updates	= implode(', ', array_map(function($field){ return "`$field` = VALUES(`$field`)"; }, array_keys($data)));

		$cache_keys		= [];
		$primary_keys	= [];

		foreach($datas as $data){
			if($data){
				foreach($data as $k => $v){
					if(is_array($v)){
						trigger_error($k.'的值是数组：'.var_export($data,true));
						continue;
					}
				}

				$values[]	= $wpdb->prepare('('.implode(', ', $formats).')', array_values($data));

				if(!empty($data[$this->primary_key])){
					$this->cache_delete_by_primary_key($data[$this->primary_key]);

					$primary_keys[]	= $data[$this->primary_key];
				}

				if($this->cache_key != $this->primary_key && !empty($data[$this->cache_key])){
					$this->cache_delete($data[$this->cache_key]);

					$cache_keys[]	= $data[$this->cache_key];
				}
			}
		}

		if($this->cache_key != $this->primary_key){
			$conditions	= [];

			if($primary_keys){
				$conditions[$this->primary_key]	= $primary_keys;
			}

			if($cache_keys){
				$conditions[$this->cache_key]	= $cache_keys;
			}

			$this->cache_delete_by_conditions($conditions);
		}

		$values	= implode(',', $values);
		$sql	= "INSERT INTO `$this->table` ({$fields}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";

		if(wpjam_doing_debug()){
			echo $sql;
		}

		$result	= $wpdb->query($sql);

		if(false === $result){
			return new WP_Error('insert_error', $wpdb->last_error);
		}

		return $result;
	}

	public function insert($data){
		$this->set_last_changed();
		$this->cache_delete_by_conditions([], $data);

		$wpdb	= $GLOBALS['wpdb'];

		if(!empty($data[$this->primary_key])){
			$data 		= array_filter($data, 'is_exists');
			$formats	= $this->process_field_formats($data);
			$fields		= implode(', ', array_keys($data));
			$values		= $wpdb->prepare(implode(', ',$formats), array_values($data));
			$updates	= implode(', ', array_map(function($field){ return "`$field` = VALUES(`$field`)"; }, array_keys($data)));

			$wpdb->check_current_query = false;

			if(false === $wpdb->query("INSERT INTO `$this->table` ({$fields}) VALUES ({$values}) ON DUPLICATE KEY UPDATE {$updates}")){
				return new WP_Error('insert_error', $wpdb->last_error);
			}

			return $data[$this->primary_key];
		}else{
			$formats	= $this->process_field_formats($data);
			$result 	= $wpdb->insert($this->table, $data, $formats);

			if($result === false){
				return new WP_Error('insert_error', $wpdb->last_error);
			}

			$this->cache_delete_by_primary_key($wpdb->insert_id);

			return $wpdb->insert_id;
		}
	}

	/*
	用法：
	update($data, $where);
	update($id, $data);
	update($data); // $where各种 参数通过 where() 方法事先传递
	*/
	public function update(...$args){
		$this->set_last_changed();

		$wpdb		= $GLOBALS['wpdb'];
		$conditions	= [];
		$args_num	= count($args);

		if($args_num == 2){
			if(is_array($args[0])){
				$data	= $args[0];
				$where 	= $args[1];

				$conditions[] = $this->where_all($where, 'fragment');
			}else{
				$id		= $args[0];
				$data	= $args[1];
				$where	= [$this->primary_key=>$id];

				$this->cache_delete_by_primary_key($id);

				$conditions[$this->primary_key]	= $id;
			}

			$this->cache_delete_by_conditions($conditions, $data);

			$result	= $wpdb->update($this->table, $data, $where, $this->process_field_formats($data), $this->process_field_formats($where));

			return $result === false ? new WP_Error('update_error', $wpdb->last_error) : $result;
		}
		// 如果为空，则需要事先通过各种 where 方法传递进去
		elseif($args_num == 1){
			$data	= $args[0];
			$where	= $this->get_conditions();

			if($data && $where){
				$this->cache_delete_by_conditions($where);
				$this->cache_delete_by_conditions([], $data);

				$fields = $values = [];

				foreach($data as $field => $value){
					if(is_null($value)){
						$fields[] = "`$field` = NULL";
					}else{
						$fields[] = "`$field` = " . $this->process_field_formats($field);
						$values[]	= $value;
					}
				}

				$fields = implode(', ', $fields);
				$sql	= $wpdb->prepare("UPDATE `{$this->table}` SET {$fields} WHERE {$where}", $values);

				if(wpjam_doing_debug()){
					echo $sql;
				}

				return $wpdb->query($sql);
			}else{
				return 0;
			}
		}
	}

	/*
	用法：
	delete($where);
	delete($id);
	delete(); // $where 参数通过各种 where() 方法事先传递
	*/
	public function delete($where = ''){
		$this->set_last_changed();

		$wpdb	= $GLOBALS['wpdb'];
		$id		= null;

		if($where){
			// 如果传递进来字符串或者数字，认为根据主键删除
			if(!is_array($where)){
				$id		= $where; 
				$where	= [$this->primary_key => $id];

				$this->cache_delete_by_primary_key($id);

				if($this->cache_key != $this->primary_key){
					$this->cache_delete_by_conditions($where);
				}
			}
			// 传递数组，采用 wpdb 默认方式
			else{
				$this->cache_delete_by_conditions($where);
			}

			$result	= $wpdb->delete($this->table, $where, $this->process_field_formats($where));
		}
		// 如果为空，则 $where 参数通过各种 where() 方法事先传递
		else{
			$where	= $this->get_conditions();

			if(!$where){
				return 0;
			}
			
			$this->cache_delete_by_conditions($where);

			$sql = "DELETE FROM `{$this->table}` WHERE {$where}";

			if(wpjam_doing_debug()){
				echo $sql;
			}

			$result = $wpdb->query($sql);
		}

		if(false === $result){
			return new WP_Error('delete_error', $wpdb->last_error);
		}

		if($id){
			$this->delete_meta_by_id($id);
		}else{
			$this->delete_orphan_meta($this->table, $this->primary_key);
		}

		return $result;
	}

	public function delete_by($field, $value){
		return $this->delete([$field => $value]);
	}

	public function delete_multi($ids){
		if(empty($ids)){
			return 0;
		}

		$wpdb	= $GLOBALS['wpdb'];

		$this->set_last_changed();

		foreach($ids as $id){
			$this->cache_delete_by_primary_key($id);
		}

		if($this->primary_key != $this->cache_key){
			$this->cache_delete_by_conditions([$this->primary_key => $ids]);
		}

		$values = [];

		foreach($ids as $id){
			$values[]	= $wpdb->prepare($this->process_field_formats($this->primary_key), $id);
		}

		$where = 'WHERE `' . $this->primary_key . '` IN ('.implode(',', $values).') ';

		$sql = "DELETE FROM `{$this->table}` {$where}";

		if(wpjam_doing_debug()){
			echo $sql;
		}

		$result = $wpdb->query($sql);

		if(false === $result ){
			return new WP_Error('delete_error', $wpdb->last_error);
		}

		return $result ;
	}

	public function parse_list($list){
		if(!is_array($list)){
			$list	= preg_split('/[\s,]+/', $list);
		}

		return array_values(array_unique($list));
	}

	private function parse_where($qs=null){
		$wpdb	= $GLOBALS['wpdb'];
		$where	= [];
		$qs		= $qs ?? $this->where;

		foreach($qs as $q){
			if(isset($q['column'])){
				if(strstr($q['column'], '(') !== false){
					$q_column	= ' '.$q['column'].' ';
					$format		= '%s';
				}else{
					$q_column	= ' `' . $q['column']. '` ';
					$format		= $this->process_field_formats($q['column']);
				}
			}

			if(in_array($q['compare'], ['=', '!=', '<', '<=', '>', '>=', 'LIKE', 'NOT LIKE'])){
				$where[]	= $wpdb->prepare($q_column.' '.$q['compare'].' '.$format, $q['value']);
			}elseif($q['compare'] == 'IN'){
				$values		= [];

				foreach(array_unique($this->parse_list($q['value'])) as $value){
					$values[]	= $wpdb->prepare($format, $value);
				}

				if(count($values) == 1){
					$where[]	= $q_column.' = '.$values[0];
				}elseif($values){
					$where[]	= $q_column.' IN ('.implode(',', $values).') ';
				}else{
					$where[]	= $q_column.' = \'\'';
				}
			}elseif($q['compare'] == 'NOT IN'){
				$values = [];

				foreach($this->parse_list($q['value']) as $value){
					$values[]	= $wpdb->prepare($format, $value);
				}

				if(count($values) == 1){
					$where[]	= $q_column.' != '.$values[0];
				}elseif($values){
					$where[]	= $q_column.' NOT IN ('.implode(',', $values).') ';
				}else{
					$where[]	= $q_column.' != \'\'';
				}
			}elseif($q['compare'] == 'find_in_set'){
				$where[]	= ' FIND_IN_SET ('.$q_column.', '.$q['value'].')';
			}elseif($q['compare'] == 'fragment'){
				$where[]	= $q['fragment'];
			}
		}

		return $where;
	}

	private function parse_search(){
		if($this->searchable_fields && $this->search_term){
			$wpdb	= $GLOBALS['wpdb'];
			$where	= [];

			foreach($this->searchable_fields as $field){
				$like		= '%'.$wpdb->esc_like($this->search_term).'%';
				$where[]	= $wpdb->prepare('`' . $field . '` LIKE  %s', $like);
			}

			return implode(' OR ', $where);
		}

		return '';
	}

	public function get_conditions($return=''){
		$where	= $this->parse_where();

		if($search_where = $this->parse_search()){
			$where[]	= ' (' . $search_where . ')';
		}

		$this->clear();

		if($return === 'array'){
			return $where;
		}

		return $where ? implode(' AND ', $where) : '';
	}

	public function get_wheres(){	// 以后放弃，目前统计在用
		return $this->get_conditions();
	}

	private function process_field_formats($data){
		if(is_array($data)){
			$format	= [];

			foreach($data as $field => $value){
				$format[]	= $this->process_field_formats($field);
			}

			return $format;
		}else{
			return $this->field_types[$data] ?? '%s';
		}
	}

	public function convert_where($value, $column=null){
		if(is_array($value)){
			if(wp_is_numeric_array($value)){
				$value	= ['compare'=>'IN', 'value'=>$value];
			}

			if(!isset($value['value'])){
				return [];
			}

			if(is_numeric($column) || is_null($column)){
				if(!isset($value['column'])){
					if(!empty($value['fragment'])){
						return ['compare'=>'fragment', 'fragment'=>' ( '.$value['fragment'].' ) '];
					}else{
						return [];
					}
				}
			}else{
				$value['column']	= $column;
			}

			$non_numeric_operators	= [
				'=',
				'!=',
				'LIKE',
				'NOT LIKE',
				'IN',
				'NOT IN',
				// 'RLIKE',
				// 'REGEXP',
				// 'NOT REGEXP',
			];

			$numeric_operators		= [
				'>',
				'>=',
				'<',
				'<=',
				// 'BETWEEN',
				// 'NOT BETWEEN',
			];

			if(isset($value['compare'])){
				if(!in_array($value['compare'], $non_numeric_operators, true) && !in_array($value['compare'], $numeric_operators, true)){
					$value['compare']	= '=';
				}
			}else{
				$value['compare']	= '=';
			}

			return $value;
		}else{
			if(is_null($value)){
				return [];
			}

			if(is_numeric($column) || is_null($column)){
				return ['compare'=>'fragment', 'fragment'=>' ( '.$value.' ) '];
			}else{
				return ['compare'=>'=', 'column'=>$column, 'value'=>$value];
			}
		}

		return [];
	}

	public function where(...$args){
		if(count($args) >= 3){
			if($args[1] !== null){
				$this->where[]	= ['column'=>$args[0], 'value'=>$args[1], 'compare'=>$args[2]];
			}
		}elseif(count($args) == 2){
			if($where = $this->convert_where($args[1], $args[0])){
				$this->where[]	= $where;
			}
		}elseif(count($args) == 1){
			if(wp_is_numeric_array($args[0])){
				$this->where_all($args[0]);
			}else{
				if($where = $this->convert_where($args[0])){
					$this->where[]	= $where;
				}
			}
		}

		return $this;
	}

	public function where_any($any, $return='object'){
		$fragment	= '';

		if($any && is_array($any)){
			$where_any	= [];

			foreach($any as $column => $value){
				if($where = $this->convert_where($value, $column)){
					$where_any[]	= $where;
				}
			}

			if($where_any = $this->parse_where($where_any)){
				$fragment	= implode(' OR ', $where_any);
			}
		}

		if($return != 'object'){
			return $fragment ? ' ( '.$fragment.' ) ' : '';
		}

		return $this->where_fragment($fragment);
	}

	public function where_all($all, $return='object'){
		$fragment	= '';

		if($all && is_array($all)){
			$where_all	= [];

			foreach($all as $column => $value){
				if($where = $this->convert_where($value, $column)){
					$where_all[]	= $where;
				}
			}

			if($where_all = $this->parse_where($where_all)){
				$fragment	= implode(' AND ', $where_all);
			}
		}

		if($return != 'object'){
			return $fragment ? ' ( '.$fragment.' ) ' : '';
		}else{
			return $this->where_fragment($fragment);
		}
	}

	public function where_fragment($fragment){
		if($fragment){
			$this->where[] = ['compare'=>'fragment', 'fragment'=>' ( '.$fragment.' ) '];
		}

		return $this;
	}

	public function find_in_set($column, $value){
		if($value){
			$this->where[] = ['compare'=>'find_in_set', 'column'=>$column, 'value'=>$value];
		}

		return $this;
	}

	public function query_items($limit, $offset){ 
		$this->limit($limit)->offset($offset)->found_rows();

		if(is_null($this->orderby)){
			$this->orderby(wpjam_get_data_parameter('orderby'));
		}

		if(is_null($this->order)){
			$this->order(wpjam_get_data_parameter('order'));
		}

		if($this->searchable_fields && is_null($this->search_term)){
			$this->search(wpjam_get_data_parameter('s'));
		}

		foreach($this->filterable_fields as $filter_key){
			$this->where($filter_key, wpjam_get_data_parameter($filter_key));
		}

		return ['items'=>$this->get_results(), 'total'=>$this->find_total($this->groupby)];
	}
}

class WPJAM_DBTransaction{
	public static function beginTransaction(){
		return $GLOBALS['wpdb']->query("START TRANSACTION;");
	}

	public static function queryException(){
		$error = $GLOBALS['wpdb']->last_error;
		if(!empty($error)){
			throw new Exception($error);
		}
	}

	public static function commit(){
		self::queryException();
		return $GLOBALS['wpdb']->query("COMMIT;");
	}

	public static function rollBack(){
		return $GLOBALS['wpdb']->query("ROLLBACK;");
	}
}