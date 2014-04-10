<?php class login extends _pdo{					#Class to handle login or create new users from form submissions or $_SESSION

	public $user_data = array();
	private static $instance = null;

	public static function load() {				#Use static method to avoid multiple instances. Called as "login::load()"
		if(is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {				#Creates a new instance of the class. Called as "new login"
		parent::__construct();					#login extends _pdo, so create new instance of parent.
		#[TODO] Use static parent::load() instead, but this causes errors

		$this->user_data = array(
			'user' => null,
			'password' => null,
			'role' => null,
			'logged-in' => false
		);
	}

	public function create_from($source) {		#Creates new user using an array passed as source. Usually $_POST or $_SESSION
		if(array_keys_exist('user', 'password', $source)) {	#Check if needed entries exist in array
			if(array_key_exists('repeat', $source) and $source['password'] !== $source['repeat']) die("<code class=\"error\">Passwords do not match</code>");
			$salt = mcrypt_create_iv(50, MCRYPT_DEV_URANDOM);
			$options = [
					'cost' => 11,
					'salt' => $salt
			];

			$this->array_insert('users', [
				'user' => $source['user'],
				'password' => password_hash(trim($source['password']), PASSWORD_BCRYPT, $options)
			]);
		}
	}

	public function login_with($source) {		#Intended to find login info from $_COOKIE, $_SESSION, or $_POST
		#[TODO] Handle an invalid login
		if(array_keys_exist('user', 'password', $source)) {	#Make sure necessary information has been passed
			$query = "SELECT `user`, `password`, `role` FROM `users` WHERE `user` = '{$this->escape($source['user'])}' LIMIT 1";
			$results = $this->fetch_array($query)[0];
			if(password_verify(trim($source['password']), $results->password) and $results->role !== 'new') {	#Verifies by matching hashes
				$this->setUser($results->user)->setPassword($results->password)->setRole($results->role)->setLogged_In(true);
			}
		}
	}

	public function __set($key, $value) {		#Setter for class. Called as "$this->key = $value"
		$key = preg_replace('/_/', '-', strtolower($key));
		$this->user_data[$key] = $value;
		return $this;
	}

	public function __get($key) {				#Getter for the class. Called as "$this->key" and returns the value
		$key = preg_replace('/_/', '-', strtolower($key));
		if(array_key_exists($key, $this->user_data)) {
			return $this->user_data[$key];
		}
		return false;
	}

	public function __isset($key) {				#Returns boolean. Called as "isset($this->key)"
		$key = preg_replace('/_/', '-', strtolower($key));
		return array_key_exists($key, $this->user_data);
	}

	public function __unset($key) {				#Removes an entry. Called as "unset($this->key)"
		$key = preg_replace('/_/', '-', strtolower($key));
		unset($this->user_data[$key]);
	}

	public function __call($name, $arguments) {	#Magic and chained getter and setting. Called as "$this->[setName|getName]($value)"
		$name = strtolower($name);
		$act = substr($name, 0, 3);
		$key = preg_replace('/_/', '-', substr($name, 3));
		switch($act) {
			case 'get':
				if(array_key_exists($key, $this->user_data)) {
					return $this->user_data[$key];
				}
				else{
					die('Unknown variable.');
				}
				break;
			case 'set':
				$this->user_data[$key] = $arguments[0];
				return $this;
				break;
			default:
				die('Unknown method.');
		}
	}

	public function logout() {					#Undo the login. Destroy it. Removes session and cookie. Sets logged_in to false
		if(isset($_COOKIE['cert'])) setcookie('cert', '', time()-3600);
		if(isset($_SESSION)) session_destroy();
		$this->setUser(null)->setPassword(null)->setRole(null)->setLogged_In(false);
	}

	public function debug() {					#Method for debugging. Prints out all types of data and their values
		echo '<pre><code>';
		print_r($this);
		echo '</code></pre>';
	}
}?>
