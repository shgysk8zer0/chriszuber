<?php
	/**
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package core_shared
	 * @version 2014-06-13
	 * @copyright 2014, Chris Zuber
	 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License
	 * as published by the Free Software Foundation, either version 3
	 * of the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	namespace core;
	class prepared extends pdo_resources {
		private static $instances = [];

		public $stm = null;

		/**
		 * Static load function avoids creating multiple instances/connections
		 * It stores an array of instances in the static instances array.
		 * It uses $ini as the key to the array, and the _pdo instance as
		 * the value.
		 *
		 * @params string $ini (.ini file to use for database credentials)
		 * @return prepared Object
		 * @example $prepared prepared::load($connect) or $prepared = prepared::load($connect)
		 */

		public static function load($ini = 'connect') {
			if(!array_key_exists($ini, self::$instances)) {
				self::$instances[$ini] = new self($ini);
			}
			return self::$instances[$ini];
		}

		public function __construct($ini = 'connect') {
			/**
			 * Do I need this? Will __construct not just be
			 * inherited?
			 */

			parent::__construct($ini);
		}

		/**
		 * Create a prepared statement and save as $stm
		 *
		 * Need to use parent::prepare() in order to
		 * keep code withing the class because otherwise
		 * we would have to work with a PDOStatement Object
		 * and lose the chaining benefits as well as the ability
		 * to define its methods.
		 *
		 * @param string $query
		 * @return prepared Object
		 * @example
		 * $prepared->prepare("
		 * 	SELECT *
		 * 	FROM `$table`
		 * 	WHERE `name` = :name
		 * ");
		 */

		public function prepare($query) {
			$this->stm = $this->pdo->prepare((string)$query);
			return $this;
		}

		/**
		 * Bind values to $stm using [$name => $value]
		 * without needing to use the ':' prefix.
		 *
		 * Can bind multiple times in a single call by
		 * using an array and looping through it.
		 *
		 * @param array $binders
		 * @return prepared Object
		 * @example
		 * $prepared->bind([
		 * 	$name => $value
		 * 	...
		 * ])
		 */

		public function bind(array $binders) {
			foreach($binders as $name => $value) {
				$this->stm->bindValue(':' . $name, (string)$value);
			}
			return $this;
		}

		public function execute() {
			$this->stm->execute();
			return $this;
		}

		/**
		 * Gets results of prepared statement. $n can be passed to retreive a specific row
		 *
		 * @param [int $n]
		 * @return mixed
		 */

		public function get_results($n = null) {
			$results = [];
			foreach($this->stm->fetchAll(\PDO::FETCH_CLASS) as $data) {			//Convert from an associative array to a \stdClass object
				/*$row = new \stdClass();
				foreach($data as $key => $value) {
					$row->$key = trim($value);
				}*/
				$results[] = $row;
			}
			//If $n is set, return $results[$n] (row $n of results) Else return all
			if(!count($results)) return false;
			if(is_int($n)) return $results[$n];
			else return $results;
		}
	}
?>
