<?php
	class search {
		private $select = '*',
				$from = '',
				$where = null,
				$limit;
		public $query,
				$pdo;

		public function __construct() {
			//parent::_construct();
			$this->pdo = _pdo::load();
		}

		public function select() {
			$cols = flatten(func_get_args());
			$count = count($cols);
			if(($count) and !($count === 1 and $cols[0] === '*')) {
				foreach($cols as &$col) $col = $this->pdo->escape($col);
				$this->select = $cols;
			}
			return $this;
		}

		public function from($table) {
			$this->from = $this->pdo->escape($table);
			return $this;
		}

		public function where($arr) {
			$this->where = $arr;
			return $this;
		}

		public function limit($int) {
			$this->limit = (int) $int;
			return $this;
		}

		private function build() {
			(is_array($this->select)) ? $this->query = 'SELECT `' . join('` ,`', $this->select) . '`' : $this->query = "SELECT *";
			$this->query .= " FROM `{$this->from}`";
			if(count($this->where) !== 0) {
				$this->query .= ' WHERE ';
				$wheres = [];
				foreach(array_keys($this->where) as $where) {
					$wheres[] = "`$where` LIKE :$where";
				}
				$this->query .= join (' AND ', $wheres);
			}
			if(preg_match('/\d+/', $this->limit)) {
				$this->query .= " LIMIT {$this->limit}";
			}
			return $this->query;
		}

		public function execute($row = null) {
			$this->pdo->prepare($this->build());
			if(count($this->where) !== 0) {
				foreach($this->where as $key => $value) {
					$this->pdo->bind([$key => "%{$value}%"]);
				}
			}
			$this->pdo->execute();
			return (is_null($row)) ? $this->pdo->get_results() : $this->pdo->get_results($row);
		}

		public function debug() {
			$this->pdo->prepare($this->build());
			ob_start();
			debug($this);
			return ob_get_clean();
		}
	}
?>