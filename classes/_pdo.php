<?php class _pdo{
	protected $pdo, $prepared;
	private $user, $password, $server, $database, $query;
	protected static $loaded = null;
	
	public static function load() { 						#To avoid creating several instances, use a static load()
		if(is_null(self::$loaded)) {
			self::$loaded = new self();
		}
		return self::$loaded;
	}
	
	public function __construct() {							#Called when using "new _pdo". Creates new instance
		global $site;
		#[TODO] Use some metthod other than global for importing $site. storage class would be good.
		/**
		* $site contains database credentials from connection.ini
		* We will use these for connecting to the database
		*/
		$this->user = $site['user'];
		$this->password = $site['password'];
		$this->server = $site['server'];
		$this->database = $site['database'];
		$this->pdo = new PDO("mysql:host={$this->server};dbname={$this->database}", $this->user, $this->password) or die("<h1>Error connecting</h1>");
	}
	
	protected function prepare($query) {					#Creates a new prepared statement
		/**
		* Argument $query is a SQL query in prepared statement format
		* "SELECT FROM `$table` WHERE `column` = ':$values'"
		* Note the use of the colon. These are what we are going to be 
		* binding values to a little later
		*
		* Returns $this for chaining. Most further functions will do the same where useful
		*/
		$this->prepared = $this->pdo->prepare($query) or die("<h1>Failed to prepare statement: <code>{$query}</code></h1>");
		return $this;
	}
	
	protected function bind($array) {						#Binds values to prepared statements
		foreach($array as $paramater => $value) {
			$pattern = "/\:{$paramater}/";
			$this->prepared->bindValue(':' . $paramater, $value) or die("<h1>Failed to bind: {$value} to {$paramater} </h1>");
		}
		return $this;
	}
	
	protected function execute() {							#Executes prepared statements. Does not return results
		$this->prepared->execute() or die("<h1>Error executing: <code>{$this->prepared}</code</h1>");
		return $this;
	}
	
	protected function get_results($n = null) {				#Gets results of prepared statement. $n can be passed to retreive a specific row
		$arr = $this->prepared->fetchAll(PDO::FETCH_CLASS);
		$results = array();
		foreach($arr as $data) {							#Convert from an associative array to a stdClass object
			$row = new stdClass();
			foreach($data as $key => $value) {
				$row->$key = trim($value);
			}
			array_push($results, $row);
		}
		#If $n is set, return $results[$n] (row $n of results) Else return all
		if(is_null($n)) return $results;
		else return $results[$n];
	}
	
	public function DB_close() {
		#$this->mysqli->close();
	}
	
	public function escape($query) {						#For lack of a pdo escape, use quote, trimming off the quotations
		$escaped = unquote(
			$this->pdo->quote(
				trim($query)
			)
		);
		return $escaped;
	}
	
	public function query($query) {						#Get the results of a SQL query
		$results = $this->pdo->query($query) or die("<h1>No Results: <code>{$query}</code></h1>");
		return $results;
	}
	
	public function fetch_array($query) {				#Return the results of a query as an associative array
		$results = $this->query($query);
		$data = $results->fetchAll(PDO::FETCH_CLASS);
		return $data;
	}
	
	public function DB_get_table($table, $these = '*') {
		$data = array();
		if($these !== '*') $these ="`{$these}`";
		$data = $this->fetch_array("SELECT {$these} FROM {$table}");
		
		return $data;
	}
	
	public function array_insert($table, $content) {		#Receives an array of names and values [$name => $value]
		foreach($content as &$value) $value = $this->pdo->quote($value);
		$query = "INSERT into `{$table}` (`". join('`,`', array_keys($content)) . "`) VALUES(" . join(',', $content) . ")";
		$this->pdo->query($query);
		return $this;
	}
	
	public function sql_table($table_name) {				#Prints out a SQL table in HTML formnatting. Used for updating via Ajax
		$table_data = $this->DB_get_table($table_name);
		$cols = $this->table_headers($table_name);
		$table = "<table border=\"1\" data-sql-table=\"{$table_name}\">";
		$thead = '<thead><tr>';
		foreach($cols as $col) {
			if($col !== 'id') {
				$thead .= "<th>{$col}</th>";
			}
		}
		$thead .= "</tr></thead>";
		$tbody = "<tbody>";
		foreach($table_data as $tr) {
			$tbody .= "<tr data-sql-id=\"{$tr->id}\">";
			foreach($tr as $key => $td) {
				if($key !== 'id') {
					#$tbody .= "<td data-sql-name=\"{$key}\">{$td}</td>";
					$tbody .= "<td><input name={$key} type=\"text\" value=\"{$td}\" class=\"sql\">";
				}
			}
		}
		$tbody .="</tbody>";
		$table .= $thead . $tbody .= "</table>";
		return $table;
	}
	
	public function DB_update($table, $name, $value, $where) {	#Updates a table according to these arguments
		return $this->query("UPDATE `{$table}` SET `{$name}` = '{$value}' WHERE {$where}");
	}
	
	public function show_tables() {								#Returns a 0 indexed array of tables in database
		$query = "SHOW TABLES";
		$results = $this->pdo->query($query);
		$tables = $results->fetchAll(PDO::FETCH_COLUMN, 0);
		return $tables;
	}
	
	public function table_headers($table) {						#Returns a 0 indexed array of column headers for $table
		$query = "DESCRIBE {$table}";
		$results = $this->pdo->query($query);
		$headers = $results->fetchAll(PDO::FETCH_COLUMN, 0);
		return $headers;
	}
	
	public function value_properties($query) {					#Returns the results of a SQL query as a stdClass object
		$array = array();
		$results = $this->fetch_array($query);
		foreach($results as $result) {
			$data = new stdClass();
			foreach($results as $key => $value) {
				$key = trim($key);
				$value = trim($value);
				$data->$key = $value;
			}
			array_push($array, $data);
		}
		return $array;
	}
	
	public function name_value($table = null) {					#For simple Name/Value tables. Gets all name/value pairs. Returns stdClass object
		$data = $this->fetch_array("SELECT `name`, `value` FROM `{$table}`");
		$values = new stdClass();
		foreach($data as $row) {
			$name = trim($row->name);
			$value = trim($row->value);
			$values->$name = $value;
		}
		return $values;
	}
	
	public function reset_table($table) {						#Removes all entries in a table and resets AUTO_INCREMENT to 1
		$this->query("DELETE FROM `{$table}`");
		$this->query("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
	}
}
?>
