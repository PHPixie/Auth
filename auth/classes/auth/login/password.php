<?php

/**
 * Password login provider using salted password hashes.
 */
class Password_Login_Auth extends Login_Auth {

	/**
	 * Field in the users table where the users
	 * login is stored.
	 * @var string
	 * @access protected
	 */
	protected $login_field;
	
	/**
	 * Field in the users table where the users
	 * password is stored.
	 * @var string
	 * @access protected
	 */
	protected $password_field;
	
	/**
	 * Hash algorithm to use. If not set
	 * the passwords are saved without hashing.
	 * @var string
	 * @access protected
	 */
	protected $hash_method;
	
	/**
	 * Constructs password login provider for the specified configuration.
	 * 
	 * @param Auth $auth Auth instance that this login provider belongs to.
	 * @param string $config Name of the configuration
	 * @access public
	 * @return void
	 */
	public function __construct($auth, $config) {
		parent::__construct($auth, $config);
		$this->login_field = Config::get($this->config_prefix."login_field");
		$this->password_field = Config::get($this->config_prefix."password_field");
		$this->hash_method = Config::get($this->config_prefix."hash_method",'md5');
	}
	
	/**
	 * Attempts to log the user in using his login and password.
	 * 
	 * @param string $login Users login
	 * @param string $password Users password
	 * @access public
	 * @return bool If the user exists.
	 */
	public function login($login, $password) {
		$user = $this->auth->user_model()
						->where($this->login_field, $login)
						->find();
		if($user->loaded()){
			$password_field = $this->password_field;
			$challenge = $user->$password_field;
			
			if($this->hash_method){
				$salted = explode(':', $challenge);
				$password = hash($this->hash_method, $password.$salted[1]);
				$challenge = $salted[0];
			}
			if ($challenge == $password) {
				$this->set_user($user);
				return true;
			}
		}
		return false;
	}

	/**
	 * Hashes the password using the configured method.
	 * 
	 * @param string $password Password to hash
	 * @access public
	 * @return string Hashed password
	 */
	public function hash_password($password){
		if(!$this->hash_method)
			return $password;
		$salt = uniqid(rand());
		return hash($this->hash_method, $password.$salt).':'.$salt;
	}	
}