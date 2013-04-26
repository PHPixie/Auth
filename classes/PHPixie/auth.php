<?php

namespace PHPixie;

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
class Auth {

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
	public $user;
	
	/**
	 * Name of the login provider that
	 * the user logged in with.
	 * @var string
	 * @access public
	 */
	public $logged_with;
	
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
	 * Array of initialized \PHPixie\Auth\Service instances.
	 * @var array
	 * @access public
	 */
	protected $_services;
	
	/**
	 * Constructs an Auth instance for the specified configuration.
	 * 
	 * @param string $config Name of the configuration.
	 * @return void
	 * @access public
	 */
	public function __construct($pixie) {
		$this->pixie = $pixie;
		$pixie->assets_dirs[] = dirname(dirname(dirname(__FILE__))).'/assets/';
	}

	/**
	 * Gets an instance of an auth service
	 *
	 * @param string  $config Configuration name of the connection.
	 *                        Defaults to  'default'.
	 * @return \PHPixie\Auth\Service  Driver implementation of the Connection class.
	 */
	public function service($config = "default") {
		if (!isset($this->_services[$config]))
			$this->_services[$config] = $this->build_service($config);
		
		return $this->_services[$config];
	}
	
	public function build_service($config) {
		return new \PHPixie\Auth\Service($this->pixie, $config);
	}
	
	public function build_login($provider, $service, $config) {
		$login_class = '\PHPixie\Auth\Login\\'.$provider;
		return new $login_class($this->pixie, $service, $config);
	}
	
	public function build_role($driver, $config) {
		$role_class = '\PHPixie\Auth\Role\\'.$driver;
		return new $role_class($this->pixie, $config);
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
	public function set_user($user, $logged_with, $config = 'default') {
		$this->service($config)->set_user($user, $logged_with);
	}
	
	/**
	 * Logs the user out.
	 *
	 * @return void
	 * @access public
	 */
	public function logout($config = 'default') {
		$this->service($config)->logout();
	}
	
	/**
	 * Checks if the logged in user has the specified role.
	 *
	 * @param string $role Role to check for.
	 * @return bool If the user has the specified role
	 * @throws Exception If the role driver is not specified
	 * @access public
	 */
	public function has_role($role, $config = 'default') {
		return $this->service($config)->has_role($role);
		
	}
	
	/**
	 * Returns the login provider by name.
	 *
	 * @param string $provider Name of the login provider
	 * @return Login_Auth Login provider
	 * @access public
	 */
	public function provider($provider, $config = 'default') {
		return $this->service($config)->provider($provider);
	}
	
	public function user($config = 'default') {
		return $this->service($config)->user();
	}
	
	public function logged_with($config = 'default') {
		return $this->service($config)->logged_with();
	}
	
}