<?php

/**
 * An interface for role strategies
 *
 * @package    Auth
 */
interface Role_Auth {

	/**
	 * Checks if the user belongs to the specified role.
	 * 
	 * @param ORM $user User to check the role for
	 * @param string $role Role name to check for
	 * @access public
	 * @return bool If the user belongs to the specified role
	 * @throws Exception If the relationship type is not belongs_to or has_many
	 */
	public function has_role($user, $role);
}