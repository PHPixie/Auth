<?php
return array(
	'default' => array(
	
		//Name of the ORM model that defines a user
		'model' => 'fairy',
		
		//Login providers
		'login' => array(
		
			'password' => array(
			
				//Field in the user table where the username is stored
				'login_field' => 'username',
				
				//Field in the user table where the password is stored
				'password_field' => 'password',
				
				//Hash algorith for passwords, defaults to 'md5'.
				//Set to 'false' to disable hashing
				'hash_method'   => 'md5'
			),
			
			'facebook' => array(
			
				//APP ID of your facebook app
				'app_id' => '138626646318836',
				
				//APP Secret of your facebook app
				'app_secret' => '4945da54b61464645321d9fbcb7e476e',
				
				//Permissions to request from the user
				'permissions'  => array('user_about_me'),
				
				//Field in the user table where the users facebook id is stored
				'fbid_field' => 'fb_id',
				
				//Default url to redirect the users after successful login.
				//By default users are returned to the page from which they came.
				//'return_url' => '/fairies'
			)
		),
		
		//Role driver configuration
		'roles' => array(
		
			//Driver name
			//Currently can be either 'field' or 'relation'
			'driver' => 'relation',
			
			//Field in the user table where the users role is stored
			//Used by the 'field' driver.
			'field'  => 'role',
			
			//Configurations for the 'relation' driver
			//Relation type, either 'belongs_to' or 'has_many'
			'type' => 'has_many',
			
			//Field in the roles table that stores the name of the role
			'name_field' => 'name',
			
			//Name of the relation that a user model has with the role model.
			'relation' => 'roles'
		)
	)
);