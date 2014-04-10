<?php
	class regexp{											#Makes easy use of simple regular expressions
		/**
		* $reg = new regexp($str[, $location (begin, end, full)]);
		* echo $reg->replace($this)->with($that)->execute();
		* @boolean $reg->[ends_with]|[begins_with]($some_string)
		*
		* Replacement methods use preg_replace((array)$pattern, (array(replacement), (RegExp)$in))
		*/
		protected $pattern, $replacement, $limit = -1, $find;
		public $in;
		public $result;
		
		public function __construct($str = false) {			#Creates a new instance using "new regexp"
			$this->pattern = array();
			$this->replacement = array();
			if($str) $this->in = $str;
			return $this;
		}
		
		public function __isset($name) {					#Called by isset($this->name)
			return isset($this->$name);
		}
		
		public function set_pattern($type) {				#Probalby not useful. Function name says it all
			$this->pattern = pattern($type);
			return $this;
		}
		
		public function replace($str) {						#Adds a new pattern to $pattern
			array_push($this->pattern, $this->regexp($str));
			return $this;
		}
		
		public function with($str) {						#Adds a new replacement to $replacement
			array_push($this->replacement, $str);
			return $this;
		}
		
		public function ends_with($str) {					#RegExp at end of string
			$this->find = $this->regexp($str, 'end');
			return $this->test();
		}
		
		public function begins_with($str) {					#RegExp at beginning of string
			$this->find = $this->regexp($str, 'begin');
			return $this->test();
		}
		
		public function is($str) {							#RegExp of the full string. Begin and end
			$this->find = $this->regexp($str, 'full');
			return $this->test();
		}
		
		public function has($str) {							#Location agnostic RegExp
			$this->find = $this->regexp($str, null);
			return $this->test();
		}
		
		public function regexp($str, $loc = null) {			#Creates the RegExp format '/[^]pattern[$]/', replacing dangerous characters
			$pattern = preg_quote($str, '/');
			switch($loc) {
				case 'begin':
					$pattern = "/^$pattern/";
					break;
				case 'end':
					$pattern = "/$pattern$/";
					break;
				case 'full':
					$pattern = "/^$pattern$/";
					break;
				default:
					$pattern = "/$pattern/";
			}
			return $pattern;
		}
		
		public function test() {					#Returns boolean result of a RegExp search
			return preg_match($this->find, $this->in);
		}
		
		public function find($str, $loc = null) {	#Needle... Testing for this
			$this->find = $this->regexp($str, $full);
			return $this;
		}
		
		public function in($str) {					#Haystack. Looking in this
			$this->in = $str;
			return $this;
		}
		
		public function limit($n) {					#Optional limit to replacements. Defaults to unlimited
			$this->limit = $n;
			return $this;
		}
		
		public function execute() {					#Runs the RegExp replacement, modifies and returns the string
			$this->in = preg_replace($this->pattern, $this->replacement, $this->in, $this->limit);
			return $this->in;
		}
		
		public function value() {
			return $this->in;
		}
		
		public function matches_pattern($type = null) { #Returns input patterns for html inputs
			$pattern = pattern($type);
			$this->find = "/^$pattern$/";
			return $this->test();
		}
	}
?>
