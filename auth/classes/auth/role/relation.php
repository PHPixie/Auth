<?php

/**
 * Manages roles based on an ORM relationship.
 * Supports belongs_to and has_many relationships.
 *
 * @package    Auth
 */
class Relation_Role_Auth implements Role_Auth {

	/**
	 * Name of the role relation
	 * @var string
	 * @access protected
	 */
	protected $relation;
	
	/**
	 * Name of the field holding role name.
	 * @var string
	 * @access protected
	 */
	protected $name_field;
	
	/**
	 * Relationship type. 
	 * Either belongs_to or has_many
	 * @var string
	 * @access protected
	 */
	protected $type;
	
	/**
	 * Constructs this role strategy for the specified configuration.
	 * 
	 * @param string $config Name of the configuration
	 * @access public
	 * @return void
	 */
	public function __construct($config) {
		$this->relation = Config::get("auth.{$config}.roles.relation");
		$this->name_field = Config::get("auth.{$config}.roles.name_field");
		$this->type = Config::get("auth.{$config}.roles.type");
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
	public function has_role($user, $role) {
		$relation = $this->relation;
		$field = $this->name_field;
		
		if($this->type == 'has_many')
			return $user->$relation
					->where($this->name_field, $role)
					->count_all() > 0;
					
		if ($this->type = 'belongs_to')
			return $user->$relation->$field == $role;

		throw new Exception("The relationship must be either of has_many or has_one type");
	}
}