<?php
	/**
	 * An FTP class designed to make FTP in PHP more similar to
	 * standard BASH commands.
	 *
	 * @author Chris Zuber <shgysk8zer0@gmail.com>
	 * @package core_shared
	 * @version 2014-04-19
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
	class ftp {
		private $pass, $user, $server, $path;
		protected $ftp;
		public $tmp;

		/**
		 * @param string $user
		 * @param string $pass
		 * @param string $server
		 */

		public function __construct($user, $pass, $server) {
			$this->user = (string)$user;
			$this->pass = (string)$pass;
			$this->server = (string)$server;
			$this->tmp = '/tmp/ftpdata';
			$this->login();
		}

		/**
		 * Login to FTP server using $user, $pass, & $server
		 * @param void
		 * @return boolean (status of ftp_login())
		 */

		private function login() {
			$this->ftp = ftp_connect($this->server);
			return ftp_login($this->ftp, $this->user, $this->pass);
		}

		/**
		 * Change Directory
		 * @param string $dir
		 * @return ftp
		 *
		 */

		public function cd($dir) {
			$dir = (string)$dir;
			($dir === '..') ? ftp_cdup($this->ftp) : ftp_chdir($this->ftp, $dir);
			$this->path = $this->pwd();
			return $this;
		}

		/**
		 * Downloads a file to $tmp and returns its contents
		 *
		 * @param string $file
		 * @param int $mode (binary or text)
		 * @return string (file contents)
		 */

		public function get($file, $mode = FTP_BINARY) {
			ftp_get($this->ftp, $this->tmp, (string)$file, $mode);
			return file_get_contents($this->tmp);
		}

		/**
		 * get(), but with an array of files.
		 *
		 * @param array $list (all files to get())
		 * @return array (return values of get() as an array)
		 */

		public function get_all(array $list) {
			$all = array();
			foreach($list as $file){
				array_push($all, $this->get((string)$file));
			}
			return $all;
		}

		/**
		 * Get count of files matching $exp
		 *
		 * @param string $exp
		 * @return int
		 */

		public function count($exp = '.') {
			return count($this->ls((string)$exp));
		}

		/**
		 * Closes FTP connection
		 *
		 * @param void
		 * @return void
		 */

		public function close() {
			ftp_close($this->ftp);
		}

		/**
		 * Execute an arbitrary command on the FTP server
		 *
		 * @param string command
		 * @return mixed
		 */

		protected function exec($cmd = null) {
			return ftp_exec($this->ftp, (string)$cmd);
		}

		/**
		 * Make Directory
		 *
		 * @param string $name
		 * @return boolean
		 */

		public function mkdir($name = null) {
			return ftp_mkdir($this->ftp, (string)$name);
		}

		/**
		 * Remove Directory
		 * @param string $dir
		 * @return boolean
		 */

		public function rmdir($dir = null) {
			return ftp_rmdir($this->ftp, (string)$dir);
		}

		/**
		 * List current or given directory
		 *
		 * @param string $dir
		 * @return string
		 */

		public function ls($dir = '.') {
			return ftp_nlist($this->ftp, (string)$dir);
		}

		/**
		 * Print Working Directory
		 *
		 * @param void
		 * @return string
		 */

		public function pwd() {
			return ftp_pwd($this->ftp);
		}

		/**
		 * Remove (alias for delete)
		 *
		 * @param string $f
		 * @return boolean
		 */

		public function rm($f = null) {
			return $this->delete($f);
		}

		/**
		 * Delete a file
		 *
		 * @param string $f
		 * @return boolean
		 */

		public function delete($f = null) {
			return ftp_delete($this->ftp, (string)$f);
		}

		/**
		 * Move
		 *
		 * @param string $from
		 * @param string $to
		 * @return boolean
		 */

		public function mv($from = null, $to = null) {
			return ftp_rename($this->ftp, (string)$from, (string)$to);
		}

		/**
		 * Change mode (permissions)
		 *
		 * @param string $mode (string of ints?)
		 * @param string $file
		 * @return boolean
		 */

		public function chmod($mode = null, $file = null) {
			return ftp_chmod($this->ftp, (string)$mode, (string)$file);
		}

		/**
		 * Check if a file exists
		 *
		 * @param string $file
		 * @return boolean
		 */

		public function exists($file = null) {
			return (ftp_size($this->ftp, (string)$file) !== -1);
		}
	}
?>
