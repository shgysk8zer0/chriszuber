<?php
	class ftp {
		/**
		 * @author Chris Zuber <shgysk8zer0@gmail.com>
		 * @copyright 2014, Chris Zuber
		 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
		 * @package core_shared
		 * @version 2014-04-19
		 */

		private $pass, $user, $server, $path;
		protected $ftp;
		public $tmp;

		public function __construct($u, $p, $s){
			$this->user = $u;
			$this->pass = $p;
			$this->server = $s;
			$this->tmp = '/tmp/ftpdata';
			$this->login();
		}

		private function login(){
			$this->ftp = ftp_connect($this->server);
			return ftp_login($this->ftp, $this->user, $this->pass) or die('<h1>Unable to Connect. Check Login Info</h1>');
		}

		public function cd($dir){
			($dir === '..') ? ftp_cdup($this->ftp) : ftp_chdir($this->ftp, $dir);
			$this->path = $this->pwd();
		}

		public function get($file, $mode = FTP_BINARY){
			ftp_get($this->ftp, $this->tmp, $file, $mode);
			return file_get_contents($this->tmp);
		}

		public function get_all($list){
			$all = array();
			foreach($list as $file){
				array_push($all, $this->get($file));
			}
			return $all;
		}

		public function count($exp = '.'){
			return count($this->ls($exp));
		}

		public function close(){
			ftp_close($this->ftp);
		}

		protected function exec($cmd){
			return ftp_exec($this->ftp, $cmd);
		}

		public function mkdir($name){
			return ftp_mkdir($this->ftp, $name);
		}

		public function rmdir($dir){
			return ftp_rmdir($this->ftp, $dir);
		}

		public function ls($dir = '.'){
			return ftp_nlist($this->ftp, $dir);
		}

		public function pwd(){
			return ftp_pwd($this->ftp);
		}

		public function rm($f){
			return $this->delete($f);
		}

		public function delete($f){
			return ftp_delete($this->ftp, $f);
		}

		public function mv($from, $to){
			return ftp_rename($this->ftp, $from, $to);
		}

		public function chmod($mode, $file){
			return ftp_chmod($this->ftp, $mode, $file);
		}

		public function exists($file){
			return (ftp_size($this->ftp, $file) !== -1);
		}
	}
?>
