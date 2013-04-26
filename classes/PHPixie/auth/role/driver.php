<?php

namespace PHPixie\Auth\Role;

/**
 * An interface for role strategies
 *
 * @package    Auth
 */
abstract class Driver {
	
	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;
	
	public function __construct($pixie, $config) {
		$this->pixie = $pixie;
	}
	
	/**
	 * Checks if the user belongs to the specified role.
	 * 
	 * @param ORM $user User to check the role for
	 * @param string $role Role name to check for
	 * @access public
	 * @return bool If the user belongs to the specified role
	 * @throws Exception If the relationship type is not belongs_to or has_many
	 */
	public abstract function has_role($user, $role);
}