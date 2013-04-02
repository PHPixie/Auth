<?php

/**
 * Abstract class for handling user login.
 *
 * @package    Auth
 */
abstract class Login_Auth {

	/**
	 * Auth class that this login provider belongs to.
	 * @var Auth
	 * @access protected
	 */
	public $auth;
	
	protected $name;
	protected $config_prefix;
	protected $user_id_key;
	
	/**
	 * Constructs this login provider for the specified configuration.
	 * 
	 * @param Auth $auth Auth instance that this login provider belongs to.
	 * @param string $config Name of the configuration
	 * @access public
	 * @return void
	 */
	public function __construct($auth, $config) {
		$this->auth = $auth;
		$this->name = substr(strtolower(get_class($this)), 0, -11);
		$this->config_prefix = "auth.{$config}.login.{$this->name}.";
		$this->user_id_key = "auth_{$config}_{$this->name}_uid";
	}
	
	
	/**
	 * Performs user logout.
	 * The default implementation deletes the 
	 * session variable holding the user id.
	 *
	 * @access public
	 * @return void
	 */
	public function logout() {
		Session::remove($this->user_id_key);
	}

	/**
	 * Sets the user logged in via this provider.
	 * The default implementation stores the users id
	 * in a session variable.
	 * 
	 * @param ORM $user Logged in user
	 * @return void
	 * @access public
	 */
	public function set_user($user) {
		Session::set($this->user_id_key, $user->id());
		$this->auth->set_user($user, $this->name);
	}

	/**
	 * Checks if the user is logged in with this login provider, if so
	 * notifies the associated Auth instance about it.
	 * This default implementation operates based on a session key
	 * holding user id. 
	 * 
	 * @return bool If the user is logged in
	 * @access public
	 */
	public function check_login() {
		$user_id = Session::get($this->user_id_key);
		if ($user_id) {
			$user = $this->auth->user_model();
			$user = $user->where($user->id_field, $user_id)->find();
			if ($user->loaded()){
				$this->auth->set_user($user, $this->name);
				return true;
			}
		}
		return false;
	}
}