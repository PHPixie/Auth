<?php

/**
 * Manages roles based on a field in the users table.
 * The specified field must hold the name of the model
 *
 * @package    Auth
 */
class Field_Role_Auth implements Role_Auth {

	/**
	 * Name of the role field
	 * @var string
	 * @access protected
	 */
	protected $field;
	
	/**
	 * Constructs this role strategy for the specified configuration.
	 * 
	 * @param string $config Name of the configuration
	 * @access public
	 * @return void
	 */
	public function __construct($config) {
		$this->field = Config::get("auth.{$config}.roles.field");
	}
	
	/**
	 * Checks if the user belongs to the specified role.
	 * 
	 * @param ORM $user User to check the role for
	 * @param string $role Role name to check for
	 * @access public
	 * @return bool If the user belongs to the specified role
	 */
	public function has_role($user, $role) {
		$field = $this->field;
		return $user->$field == $role;
	}
}