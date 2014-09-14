<?php
	/**
	 * Wrapper for standard PDO class.
	 *
	 * This class is meant only to be extended and
	 * not used directly. It offers only a protected
	 * __construct method and a public escape.
	 *
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package core_shared
	 * @version 2014-08-27
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

	namespace core\resources;
	class pdo_connect extends \PDO {
		protected $connect;
		protected static $instances = [];
		public $connected;

		/**
		 * Static class load method
		 *
		 * Creates a new instance and returns it if one does not exist,
		 * otherwise returns the existing instance
		 *
		 * @param mixed $con
		 * @return
		 */

		public static function load($con = 'connect') {
			if(!array_key_exists($con, self::$instances)) {
				self::$instances[$con] = new self($con);
			}
			return self::$instances[$con];
		}

		/**
		 * @method __construct
		 * @desc
		 * Gets database connection info from /connect.ini (using parse_ini_file)
		 * The default ini file to use is connect, but can be passed another
		 * in the $con argument.
		 *
		 * Uses that data to create a new PHP Data Object
		 *
		 * @param mixed $con (.ini file to use for database credentials)
		 * @return void
		 * @example parent::__construct($con)
		 */

		public function __construct($con = 'connect') {
			$this->connected = false;

			if(is_string($con)) {
				if($con === 'mysql:dbname=chriszuber') {
					echo '<pre><code>';
					print_r(debug_backtrace());
					echo '</code></pre>';
					exit();
				}
				$this->connect = (object)parse_ini_file("{$con}.ini");
				/*if(is_null($this->connect)) {
					$this->connect = (object)parse_ini_file("connect.ini");
				}*/
			}
			elseif(is_object($con)) {
				$this->connect = $con;
			}
			elseif(is_array($con)) {
				$this->connect = (object)$con;
			}

			try{
				if(!(isset($this->connect->user) and isset($this->connect->password))) throw new \Exception('Missing credentials to connect to database');
				$connect_string = (isset($this->connect->type)) ? "{$this->connect->type}:" : 'mysql:';
				$connect_string .= (isset($this->connect->database)) ?  "dbname={$this->connect->database}" : "dbname={$this->connect->user}";
				if(isset($this->connect->server)) $connect_string .= ";host={$this->connect->server}";
				if(isset($this->connect->port) and $this->connect->server !== 'localhost') $connect_string .= ";port={$this->connect->port}";
				parent::__construct($connect_string, $this->connect->user, $this->connect->password);
				$this->connected = true;
			}
			catch(\Exception $e) {
				if(!isset($connect_string)) {
					$connect_string = 'Connect String not set';
				}
				$this->log(__METHOD__, __LINE__, $connect_string . PHP_EOL . $e->getMessage());
			}
		}

		/**
		 * Writes errors to a log file
		 *
		 * @param string $method
		 * @param int $line
		 * @param string $message
		 * @return void
		 */

		private function log($method = null, $line = null, $message = '') {
			file_put_contents(BASE . '/' . __CLASS__ . '.log', "Error in $method in line $line: $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
		}

		/**
		 * Restores a MySQL database from file $fname
		 *
		 * @param string $fname
		 * @return self
		 */

		public function restore($fname = null) {
			if(is_null($fname)) {
				$fname = BASE . DIRECTORY_SEPERATOR . $this->connect->database;
			}

			$sql = file_get_contents("{$fname}.sql");
			if(is_string($sql)) {
				return $this->query($sql);
			}
			else {
				return false;
			}
		}

		/**
		 * Does a mysqldump if permissions allow
		 *
		 * Return value is based on whether or not permissions
		 * allow file to be written, not whether or not it was.
		 *
		 * Default filename is the name of the database
		 * from connection
		 *
		 * @param string $filename
		 * @return boolean
		 */

		public function dump($filename = null) {
			if(is_null($filename)) {
				$filename = BASE . '/' . $this->connect->database;
			}

			if((file_exists("{$filename}.sql") and is_writable("{$filename}.sql")) or (!file_exists("{$filename}.sql") and is_writable(BASE))) {
				$command = "mysqldump -u {$this->connect->user} -p" . escapeshellcmd($this->connect->password);

				if(isset($this->connect->server) and $this->connect->server !== 'localhost') {
					$command .= " -h {$this->connect->server}";
				}

				$command .= " {$this->connect->database} > {$filename}.sql";

				exec($command);
				return true;
			}
			else {
				return false;
			}
		}
	}
?>
