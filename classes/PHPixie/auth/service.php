<?php

namespace PHPixie\Auth;

/**
 * Authroization and access control module for PHPixie.
 *
 * This module is not included by default, download it here:
 *
 * https://github.com/dracony/PHPixie-Auth
 * 
 * To enable it add 'auth' to modules array in /application/config/core.php.
 * This modules let's you log in users using different login providers.
 * Currently two login providers are supported, for the usual login/password
 * login and Facebook authentication.
 * 
 * You can also control access based on user roles. The included role drivers
 * allow you to either use a field in the users table to specify the the role
 * of the user, or to use an ORM relationship between the user and the roles.
 *
 * Please refer to the auth.php config file for instractions how to configure login
 * and role providers.
 * 
 * @link https://github.com/dracony/PHPixie-Auth Download this module from Github
 * @package    Auth
 */
class Service {

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;
	
	/**
	 * ORM model that represents a user 
	 * @var \PHPixie\ORM\Model
	 * @access protected
	 */
	protected $model;
	
	/**
	 * Logged in user
	 * @var ORM
	 * @access public
	 */
	protected $user;
	
	/**
	 * Name of the login provider that
	 * the user logged in with.
	 * @var string
	 * @access public
	 */
	protected $logged_with;
	
	/**
	 * Login providers array.
	 * @var array
	 * @access public
	 */
	protected $login_providers = array();
	
	/**
	 * User role driver
	 * @var \PHPixie\Auth\Role
	 * @access public
	 */
	protected $role_driver;
	
	
	/**
	 * Constructs an Auth instance for the specified configuration.
	 * 
	 * @param string $config Name of the configuration.
	 * @return void
	 * @access public
	 * @throw
	 */
	public function __construct($pixie, $config = 'default') {
		$this->pixie = $pixie;
		$this->model = $pixie->config->get("auth.{$config}.model");
		
		$login_providers = $pixie->config->get("auth.{$config}.login", false);
		if (!$login_providers)
			throw new \Exception("No login providers have been configured.");
			
		foreach(array_keys($login_providers) as $provider) 
			$this->login_providers[$provider] = $pixie->auth->build_login($provider, $this, $config);
		
		$role_driver = $pixie->config->get("auth.{$config}.roles.driver", false);
		if ($role_driver)
			$this->role_driver = $pixie->auth->build_role($role_driver, $config);
		
		$this->check_login();
	}
	
	/**
	 * Sets the logged in user.
	 * 
	 * @param ORM $user logged in user
	 * @param strong $logged_with Name of the provider that
	 *                            performed the login.
	 * @return void
	 * @access public
	 */
	public function set_user($user, $logged_with) {
		$this->user = $user;
		$this->logged_with = $logged_with;
	}
	
	public function user() {
		return $this->user;
	}
	
	public function logged_with() {
		return $this->logged_with;
	}
	
	/**
	 * Logs the user out.
	 *
	 * @return void
	 * @access public
	 */
	public function logout() {
		$this->login_providers[$this->logged_with]->logout();
		$this->logged_with = null;
		$this->user = null;
	}
	
	/**
	 * Checks if the logged in user has the specified role.
	 *
	 * @param string $role Role to check for.
	 * @return bool If the user has the specified role
	 * @throws \Exception If the role driver is not specified
	 * @access public
	 */
	public function has_role($role) {
		if ($this->role_driver == null)
			throw new \Exception("No role configuration is present.");
		
		if ($this->user == null)
			return false;
			
		return $this->role_driver->has_role($this->user, $role);
		
	}
	
	/**
	 * Returns the login provider by name.
	 *
	 * @param string $provider Name of the login provider
	 * @return Login_Auth Login provider
	 * @access public
	 */
	public function provider($provider) {
		return $this->login_providers[$provider];
	}
	
	/**
	 * Checks if the user is logged in via any of the 
	 * login providers
	 *
	 * @return bool if the user is logged in
	 * @access public
	 */
	public function check_login() {
		foreach($this->login_providers as $provider)
			if ($provider->check_login())
				return true;
				
		return false;
	}

	/**
	 * Returns a new instance of the user model.
	 *
	 * @return ORM Model representing the user.
	 * @access public
	 */
	public function user_model() {
		return $this->pixie->orm->get($this->model);
	}

}