<?php
	class template extends regexp{
		private static $instance = [];
		private $template, $path;

		public static function load($tpl) {
			/**
			 * Static load function avoids creating multiple instances/connections
			 * It checks if an instance has been created and returns that or a new instance
			 *
			 * @params void
			 * @return storage object/class
			 * @example $storage = storage::load
			 */

			if(!array_key_exists($tpl, self::$instance)) {
				self::$instance[$tpl] = new self($tpl);
			}
			return self::$instance[$tpl];
		}

		public function __construct($tpl) {
			$this->path = BASE . "/components/templates/{$tpl}.tpl";
			if(file_exists($this->path)) {
				parent::__construct(file_get_contents($this->path));
			}
		}

		public function __set($replace, $with) {
			$this->replace('%' . trim(strtoupper($replace)) . '%')->with($with);
		}

		public function set($arr) {
			foreach($arr as $replace => $with) {
				$this->replace('%' . trim(strtoupper($replace)) . '%')->with($with);
			}
			return $this;
		}

		public function out() {
			echo $this->execute(false);
			$this->pattern = $this->replacement = [];
		}
	}
?>
