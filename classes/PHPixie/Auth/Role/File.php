<?php

namespace PHPixie\Auth\Role;

/**
 * Manages roles based on a field in the users table.
 * The specified field must hold the name of the model
 *
 * @package    Auth
 */
class File extends Driver {

	/**
	 * Constructs this role strategy for the specified configuration
	 *
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param string $config Name of the configuration
	 */
	public function __construct($pixie, $config) {
		parent::__construct($pixie, $config);
	}

	/**
	 * Checks if the user belongs to the specified role
	 *
	 * @param \stdClass $user User to check the role for
	 * @param string $role Role name to check for
	 * @return bool If the user belongs to the specified role
	 */
	public function has_role($user, $role) {
		return $user->role == $role;
	}
}