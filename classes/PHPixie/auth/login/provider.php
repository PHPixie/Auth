<?php

namespace PHPixie\Auth\Login;

/**
 * Abstract class for handling user login.
 *
 * @package    Auth
 */
abstract class Provider {

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;
	
	/**
	 * Auth class that this login provider belongs to.
	 * @var Auth
	 * @access protected
	 */
	public $service;
	
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
	public function __construct($pixie, $service, $config) {
		$this->pixie = $pixie;
		$this->service = $service;
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
		$this->pixie->session->remove($this->user_id_key);
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
		$this->pixie->session->set($this->user_id_key, $user->id());
		$this->service->set_user($user, $this->name);
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
		$user_id = $this->pixie->session->get($this->user_id_key);
		if ($user_id) {
			$user = $this->service->user_model();
			$user = $user->where($user->id_field, $user_id)->find();
			if ($user->loaded()){
				$this->service->set_user($user, $this->name);
				return true;
			}
		}
		return false;
	}
}