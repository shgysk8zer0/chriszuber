<?php
	class ftp {
		/**
		 * An FTP class designed to make FTP in PHP more similar to
		 * standard BASH commands.
		 *
		 * @author Chris Zuber <shgysk8zer0@gmail.com>
		 * @copyright 2014, Chris Zuber
		 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
		 * @package core_shared
		 * @version 2014-04-19
		 */

		private $pass, $user, $server, $path;
		protected $ftp;
		public $tmp;

		public function __construct($user, $pass, $server) {
			/**
			 * @param string $user
			 * @param string $pass
			 * @param string $server
			 */

			$this->user = (string)$user;
			$this->pass = (string)$pass;
			$this->server = (string)$server;
			$this->tmp = '/tmp/ftpdata';
			$this->login();
		}

		private function login() {
			/**
			 * Login to FTP server using $user, $pass, & $server
			 * @param void
			 * @return boolean (status of ftp_login())
			 */

			$this->ftp = ftp_connect($this->server);
			return ftp_login($this->ftp, $this->user, $this->pass);
		}

		public function cd($dir) {
			/**
			 * Change Directory
			 * @param string $dir
			 * @return ftp
			 *
			 */

			$dir = (string)$dir;
			($dir === '..') ? ftp_cdup($this->ftp) : ftp_chdir($this->ftp, $dir);
			$this->path = $this->pwd();
			return $this;
		}

		public function get($file, $mode = FTP_BINARY) {
			/**
			 * Downloads a file to $tmp and returns its contents
			 *
			 * @param string $file
			 * @param int $mode (binary or text)
			 * @return string (file contents)
			 */

			ftp_get($this->ftp, $this->tmp, (string)$file, $mode);
			return file_get_contents($this->tmp);
		}

		public function get_all(array $list) {
			/**
			 * get(), but with an array of files.
			 *
			 * @param array $list (all files to get())
			 * @return array (return values of get() as an array)
			 */

			$all = array();
			foreach($list as $file){
				array_push($all, $this->get((string)$file));
			}
			return $all;
		}

		public function count($exp = '.') {
			/**
			 * Get count of files matching $exp
			 *
			 * @param string $exp
			 * @return int
			 */

			return count($this->ls((string)$exp));
		}

		public function close() {
			/**
			 * Closes FTP connection
			 *
			 * @param void
			 * @return void
			 */
			ftp_close($this->ftp);
		}

		protected function exec($cmd = null) {
			/**
			 * Execute an arbitrary command on the FTP server
			 *
			 * @param string command
			 * @return mixed
			 */

			return ftp_exec($this->ftp, (string)$cmd);
		}

		public function mkdir($name = null) {
			/**
			 * Make Directory
			 *
			 * @param string $name
			 * @return boolean
			 */

			return ftp_mkdir($this->ftp, (string)$name);
		}

		public function rmdir($dir = null) {
			/**
			 * Remove Directory
			 * @param string $dir
			 * @return boolean
			 */

			return ftp_rmdir($this->ftp, (string)$dir);
		}

		public function ls($dir = '.') {
			/**
			 * List current or given directory
			 *
			 * @param string $dir
			 * @return string
			 */

			return ftp_nlist($this->ftp, (string)$dir);
		}

		public function pwd() {
			/**
			 * Print Working Directory
			 *
			 * @param void
			 * @return string
			 */

			return ftp_pwd($this->ftp);
		}

		public function rm($f = null) {
			/**
			 * Remove (alias for delete)
			 *
			 * @param string $f
			 * @return boolean
			 */

			return $this->delete($f);
		}

		public function delete($f = null) {
			/**
			 * Delete a file
			 *
			 * @param string $f
			 * @return boolean
			 */

			return ftp_delete($this->ftp, (string)$f);
		}

		public function mv($from = null, $to = null) {
			/**
			 * Move
			 *
			 * @param string $from
			 * @param string $to
			 * @return boolean
			 */

			return ftp_rename($this->ftp, (string)$from, (string)$to);
		}

		public function chmod($mode = null, $file = null) {
			/**
			 * Change mode (permissions)
			 *
			 * @param string $mode (string of ints?)
			 * @param string $file
			 * @return boolean
			 */

			return ftp_chmod($this->ftp, (string)$mode, (string)$file);
		}

		public function exists($file = null) {
			/**
			 * Check if a file exists
			 *
			 * @param string $file
			 * @return boolean
			 */

			return (ftp_size($this->ftp, (string)$file) !== -1);
		}
	}
?>
