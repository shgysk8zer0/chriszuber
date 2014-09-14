<?php
	/**
	 * Class for quickly and easily creating HTML <table>s
	 *
	 * The arguments in the constructor become the valid cells & headers, in order.
	 *
	 * After that, magic __get() method appends to a $data array
	 * if the $key is present in $cells.
	 *
	 * If you want to continue onto the next row (leaving any unset fileds
	 * blank), simply call next_row(). Can also be chained using the magic __call()
	 * method, which only sets $data, similarly to __set().
	 *

	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package core_shared
	 * @version 2014-08-18
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
	 *
	 * @var array $data
	 * @var array $headers
	 * @var int $row
	 * @var array $empty_row
	 * @var string $table
	 * @var string $thead
	 * @var string $tfoot
	 * @var string $tbody
	 * @var string $captioin
	 *
	 * @example
	 * $table = new table('first_name', 'last_name');
	 * $table->first_name = 'John';
	 * $table->last_name = 'Smith';
	 * $table->foo = 'bar';	//Does nothing
	 * $table->next_row();
	 *
	 * $table->first_name(
	 * 		$fist
	 * )->last_name(
	 * 		$last
	 * )->next_row();
	 */

	namespace core;
	class table implements magic_methods {
		private $data, $headers, $row, $empty_row, $table, $thead, $tfoot, $tbody;
		public $caption;

		/**
		 * Sets up default values for class
		 *
		 * $data needs to be a multi-dimenstional associative array
		 *
		 * $row is the current row (integer) to be working on. Incremented
		 * by next_row() method.
		 *
		 * $empty_row as an associative array with its keys defined by $headers,
		 * but all of its values null
		 *
		 * $thead, $tfoot, & $caption are strings for those elements in a table
		 *
		 * @param mixed arguments (will take arguments as an array or comma separated list, either results in an array)
		 * @example $table = new table($cells[] | 'field1'[, ...])
		 */

		public function __construct() {
			$this->data = [];
			$this->headers = flatten(func_get_args());
			$this->table = null;
			$this->thead = '<thead><tr>' . html_join('th', $this->headers) . '</tr></thead>';
			$this->tfoot = '<tfoot><tr>' . html_join('th', $this->headers) . '</tr></tfoot>';
			$this->tbody = '<tbody>';
			$this->caption = null;
			$this->row = 0;
			$this->empty_row = array_combine($this->headers, array_pad([], count($this->headers), null));
			$this->data[0] = $this->empty_row;
		}

		/**
		 * Magic setter for the class.
		 *
		 * Calls the private set() method too add a value to a cell
		 * @param string $cell
		 * @param string $value
		 * @return void
		 * @example $table->$cell = $value
		 */

		public function __set($cell, $value) {
			$this->set($cell, (string)$value);
		}

		/**
		 * Magic getter method for the class
		 * Allows for cells to be appended to rather than having to
		 * be built ahead of time.
		 *
		 * @param string $cell
		 * @return string
		 * @example $table->$cell .= ' and on and on...'
		 */

		public function __get($cell) {
			if(in_array($cell, $this->headers)) {
				return $this->data[$this->row][$cell];
			}
			else {
				return '';
			}
		}

		public function __isset($cell) {
			return array_key_exists($cell, $this->data[$this->row]);
		}

		public function __unset($cell) {
			unset($this->data[$this->row][$cell]);
		}

		/**
		 * Chaninable magic method, in this case only to set values
		 *
		 * Also calls the private set() method too add a value to a field
		 *
		 * @param string $cell
		 * @param array $arguments
		 * @return self
		 * @example $table->$cell[1]($value1)->$cell[2]($value2)...
		 */

		public function __call($cell, array $arguments) {
			$this->set($cell, join(null, $arguments));

			return $this;
		}

		/**
		 * Method to move to the next row of $data array.
		 * Increments $row, which is used in set() method
		 * when settings data ($data[$row]).
		 *
		 * Also sets the data for that row to an empty
		 * array pre-set with the keys defined by $cells
		 *
		 * @param void
		 * @return self
		 * @example $table->next_row();
		 */

		public function next_row() {
			$this->tbody .= '<tr>' . html_join('td', $this->data[$this->row]) . '</tr>';
			$this->data[$this->row] = $this->empty_row;
			$this->row++;

			return $this;
		}

		/**
		 * Returns all $data as a CSV formatted string
		 *
		 * Uses private build_table() method to convert $data
		 * array into a <table>
		 *
		 * @param bool $echo
		 * @return mixed (HTML formatted <table> string from $data if $echo is false)
		 */

		public function out($echo = false) {
			$this->build_table();
			if($echo) {
				echo $this->table;
			}
			else {
				return $this->table;
			}
		}

		/**
		 * Does all the work for creating a <table> from variables.
		 * This mostly adds <tfoot>, <thead>, & <tbody> to a <table>,
		 * as well as <cpation> if $caption is set.
		 *
		 * Will also append the current row to $tbody if it hasn't been already
		 *
		 * @param void
		 * @return void
		 */

		private function build_table() {
			if(is_null($this->table)) {
				if($this->data[$this->row] !== $this->empty_row) {
					$this->tbody .= '<tr>' . html_join('td', $this->data[$this->row]) . '</tr>';
				}
				unset($this->data[$this->row]);
				$this->table = '<table>';
				if(isset($this->caption)) $this->table .= "<caption>{$this->caption}</caption>";
				$this->table .= $this->thead;
				$this->table .= $this->tfoot;
				$this->table .= $this->tbody;
				$this->table .= '</tbody></table>';
			}
		}

		/**
		 * Private method for setting columns for the current $row
		 *
		 * Checks if $cell is in the array of available $headers
		 * and that both arguments are strings.
		 *
		 * If these conditions are true, it sets $data[$row][$cell] to $value
		 * and returns true.
		 *
		 * Otherwise returns false without setting any data
		 *
		 * @param string $cell (name of field to set for current row)
		 * @param string $value (value to set it to)
		 * @return boolean (whether or not $cell is available)
		 * @example $this->set($cell, $value)
		 */

		private function set($cell, $value) {
			if(is_string($cell) and in_array($cell, $this->headers)) {
				$this->data[$this->row][$cell] = (string)$value;
				return true;
			}

			return false;
		}
	}
?>
