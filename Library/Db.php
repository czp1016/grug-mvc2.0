<?php

class Db {
	protected static $db;
	private $obj_db;
	private $table;
	protected $fields_type;
	protected static $numeric_types = array (
			'bit',
			'tinyint',
			'smallint',
			'mediumint',
			'int',
			'bigint',
			'float',
			'double',
			'decimal'
	);
	public function __construct($connect_info, $table, $connect_type = 'IMysql') {
		$class = $connect_type;
		$this->obj_db = new $class();
		$info = Grug_Registry::get($connect_info);
		$this->obj_db->connect($info['host'], $info['user'], $info['passwd'], $info['dbname']);
		$this->table = $table;
	}
		
	public function where($where){

		return $this;
	}
	public function orderby($orderby){

		return $this;
	}
	public function groupby($groupby){

		return $this;
	}
	public function limit($limit){

		return $this;
	}

	public function query($sql){
		if (preg_match('#^INSERT#i', $sql)) {
			$this->obj_db->query($sql);
			return $this->getLastInsertId();
		} else {
			return $this->obj_db->query($sql);
		}
	}

	function queryAndFetchAssoc($sql) {
		return $this->obj_db->queryAndFetchAssoc($sql);	
	}

	public function getLastInsertId() {
		return $this->obj_db->getLastInsertId();	
	}

	public function err($error) {
		die("error:".$error);
	}
	/**
	 * 按条件获取当前操作表的指定条数
	 * @param int $count       	
	 * @param int $page  从1开始     	
	 * @param mix $fields
	 * @param array $where
	 * @param array $order_by
	 * @param array $group_by   
	 * @param bool $with_rollup
	 * @param string $having 暂时请自己拼好having语句当参数传入
	 * @return array
	 */
	public function find($page = 1, $count = 10, $fields = '*', $where = null, $order_by = null, $group_by = null, $with_rollup = false, $having = null) {
		is_array($fields) and $fields = implode(',', $fields);
		if ($count > 0) {
			$limit = " LIMIT " . $count*($page-1) . " " . $count;
		} else {
			$limit = '';
		}
		$sql = "SELECT $fields FROM {$this->table}".$this->buildWhere($where).$this->buildGroupBy($group_by, $with_rollup, $having).$this->buildOrderBy($order_by).$limit;
		$list = $this->queryAndFetchAssoc($sql);
 		return $list;
	}
	public function findAll($fields = '*', $where = null, $order_by = null, $group_by = null, $with_rollup = false, $having = null) {
		return $this->find(0, -1, $fields, $where, $order_by, $group_by, $with_rollup, $having);
	}
	public function findOne($fields = '*', $where = null, $order_by = null, $group_by = null, $with_rollup = false, $having = null) {
		$list = $this->find(1, 1, $fields, $where, $order_by, $group_by, $with_rollup, $having);
		return $list[0];
	}
	/**
	 * 向当前表增加记录
	 * @param array $data 一维数组,array('id'=>1,'name'=>'jay')
	 * @param array/string $duplicate  请确保操作字段为非字符型,如果是字符型字段,请自己拼好duplicate子句当参数传入
	 * @return string
	 */
	public function insert($data, $duplicate = null) {
		foreach ($data as $field => $value) {
			$fields .= $this->addFieldQuotation($field) . ',';
			$values .= $this->autoValueQuotation($field, $value) . ',';
		}
		$fields = rtrim($fields, ',');
		$values = rtrim($values, ',');
		$sql = "INSERT INTO {$this->table} (".$fields.") VALUES (".$values.")";
		if ($duplicate) {
			if (is_array($duplicate)) {
				$sql .= ' ON DUPLICATE KEY UPDATE ' . $this->buildSet($duplicate);
			} else {
				$sql .= ' ' . $duplicate;
			}
		}
		return $this->query($sql);
	}
	/**
	 * 向当前表增加多条记录
	 * @param array $data_arr 二维数组,array(array('id'=>1,'name'=>'jay'),array('id'=>2,'name'=>'bay'))
	 * @param array/string $duplicate  请确保操作字段为非字符型,如果是字符型字段,请自己拼好duplicate子句当参数传入
	 * @return string
	 */
	public function multiInsert($data_arr, $duplicate = null) {
		if (empty($data_arr) || !is_array($data_arr) || !is_array($data_arr[0])) {
			return false;
		}
		foreach ($data_arr[0] as $field => $value) {
			$fields .= $this->addFieldQuotation($field) . ',';
		}
		$fields = rtrim($fields, ',');
		$sql = "INSERT INTO {$this->table} (".$fields.") VALUES ";
		foreach ($data_arr as $data) {
			$values = '';
			foreach ($data as $field => $value) {
				$values .= $this->autoValueQuotation($field, $value) . ',';
			}
			$values = rtrim($values, ',');
			$sql .= "(" . $values ."),";
		}
		$sql = rtrim($sql, ',');
		if ($duplicate) {
			if (is_array($duplicate)) {
				$sql .= ' ON DUPLICATE KEY UPDATE ' . $this->buildSet($duplicate);
			} else {
				$sql .= ' ' . $duplicate;
			}
		}
		return $this->query($sql);
	}
	/**
	 * 更新记录
	 * @param array $data 一维数组,array('id'=>1,'name'=>'jay')
	 * @param array $where  二维数组
	 * @param array $order_by 一维数组
	 * @param int $count
	 * @return int
	 */
	public function update($data, $where = null, $order_by = null, $count = -1) {
		if (empty($data) || !is_array($data)) {
			return false;
		}
		if (is_numeric($count) && $count > 0) {
			$limit = 'LIMIT ' . $count;
		} else {
			$limit = '';
		}
		$sql = "UPDATE {$this->table} SET ".$this->buildSet($data).$this->buildWhere($where).$this->buildOrderBy($order_by).$limit;
		return $this->query($sql);
	}
	/**
	 * 删除记录
	 * @param array $where  二维数组
	 * @param array $order_by 一维数组
	 * @param int $count
	 * @return int
	 */
	public function delete($where = null, $order_by = null, $count = -1) {
		if (is_numeric($count) && $count > 0) {
			$limit = 'LIMIT ' . $count;
		} else {
			$limit = '';
		}
		$sql = "DELETE FROM {$this->table}".$this->buildWhere($where).$this->buildOrderBy($order_by).$limit;
		return $this->query($sql);
	}
	/**
	 * 根据条件统计条目数
	 * @param array $where 二维数组
	 * @return int
	 */
	protected function count($where) {
		$sql = "SELECT COUNT(*) FROM {$this->table}" . $this->buildWhere($where);
		$res = $this->query($sql);
		return $res[0]['COUNT(*)'];
	}
	
	/**
	 * 构建where语句
	 * operator为IN时,condition可以是数组array(v1,v2,va3),也可以是字符串"v1,v2,v3"
	 * operator为between时,condition是数组array(min,max)
	 * @param array $where ,二维数组,比如array(array('field'=>'id','condition'=>1),array('field'=>'name','condition'=>'jay'))
	 * @return string
	 */
	private function buildWhere($where) {
		if ($where == null) {
			return '';
		}
		$res = ' WHERE 1';
		foreach ($where as $one) {
			isset($one['logic']) or $one['logic'] = 'AND';
			isset($one['operator']) or $one['operator'] = '=';
			$res .= ' '.$one['logic'].' '.$this->addFieldQuotation($one['field']).' ';
			$one['operator'] = strtoupper($one['operator']);
			if ($one['operator'] == 'IN' || $one['operator'] == 'NOT IN') {
				is_array($one['condition']) or $one['condition'] = explode(',', $one['condition']);
				foreach ($one['condition'] as $key => $value) {
					$one['condition'][$key] = $this->autoValueQuotation($one['field'], $value);
				}
				$one['condition'] = implode(',', $one['condition']);
				$res .= $one['operator'].' ('.$one['condition'].')';
			} elseif ($one['operator'] == 'LIKE' || $one['operator'] == 'NOT LIKE') {
				$res .= $one['operator'].' "'.mysql_escape_string($one['condition']).'"';
			} elseif ($one['operator'] == 'BETWEEN' || $one['operator'] == 'NOT BETWEEN') {
				foreach ($one['condition'] as $key => $value) {
					$one['condition'][$key] = $this->autoValueQuotation($one['field'], $value);
				}
				$res .= $one['operator'].' '.mysql_escape_string($one['condition'][0]).' and '.mysql_escape_string($one['condition'][1]);
			} else {
				$res .= $one['operator'].' '.$this->autoValueQuotation($one['field'], $one['condition']);
			}
		}
		return $res;
	}
	/**
	 * 构建group by语句
	 * @param array $group_by 一维数组,比如array('id'=>'','num'=>'DESC')
	 * @param bool $with_rollup
	 * @param string $having having语句拼接比较麻烦,暂时就先用string类型吧
	 * @return string
	 */
	private function buildGroupBy($group_by, $with_rollup, $having) {
		if ($group_by == null) {
			return '';
		}
		if (!is_array($group_by)) {
			return ' ' . $group_by;
		}
		foreach ($group_by as $field => $order) {
			$groupby_arr[] = $field . ' ' . $order;
		}
		false == $with_rollup or $with_rollup = ' WITH ROLLUP';
		return ' GROUP BY ' . implode(',', $groupby_arr) . $with_rollup . ' ' .$having;
	}
	/**
	 * 构建order by语句
	 * @param array $order_by ,一维数组,比如array('id'=>'DESC','num'=>"ASC")
	 * @return string
	 */
	private function buildOrderBy($order_by) {
		if ($order_by == null) {
			return '';
		}
		if (!is_array($order_by)) {
			return ' ' . $order_by;
		}
		foreach ($order_by as $field => $order) {
			$orderby_arr[] = $field . ' ' . $order;
		}
		return ' ORDER BY ' . implode(',', $orderby_arr);
	}
	/**
	 * 构建set语句
	 * @param array $data ,一维数组,比如array('id'=>1,'name'=>'jay')
	 * @return string
	 */
	private function buildSet($data) {
		foreach ($data as $field => $value) {
			$str .= $this->addFieldQuotation($field).'='.$this->autoValueQuotation($field, $value).',';
		}
		$str = rtrim($str, ',');
		return $str;
	}
	/**
	 * 给字段加上斜瞥号
	 * @param string $field        	
	 * @return string
	 */
	private function addFieldQuotation($field) {
		return '`' . mysql_escape_string ( $field ) . '`';
	}

	/**
	 * 判断字段值是否需要添加引号
	 * @param string $field
	 * @param unknown $value
	 * @return unknown
	 */
	private function autoValueQuotation($field, $value) {
		empty($this->fields_type) and $this->fields_type = $this->getFieldsType();
		list($field) = explode("(", $field);
		$value = mysql_escape_string ( $value );
		if (!in_array($this->fields_type[$field], self::$numeric_types)) {
			$value = '"' . $value . '"';
		}
		return $value;
	}
	/**
	 * 获取表的字段及类型
	 *        	
	 * @return array
	 */
	protected function getFieldsType() {
		$columns = $this->getTableSchema();
		foreach ($columns as $key => $value) {
			$fields_type[$value['Field']] = $value['Type'];
		}
		return $fields_type;
	}
	/**
	 * 获取表结构
	 *       	
	 * @return array
	 */
	protected function getTableSchema() {
		$sql = 'SHOW COLUMNS FROM ' . $this->table;
		$list = $this->queryAndFetchAssoc($sql);
		return $list;
	}

}