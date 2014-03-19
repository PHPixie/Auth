<?php

namespace PHPixie\Auth\Login;

/**
 * Password login provider using salted password hashes.
 */
class Password extends Provider {

	/**
	 * Field in the users table where the users
	 * login is stored.
	 * @var string
	 */
	protected $login_field;

	/**
	 * Field in the users table where the users
	 * password is stored.
	 * @var string
	 */
	protected $password_field;

	/**
	 * Field in the users table where the users
	 * remember_me token is stored.
	 * @var string
	 */
	protected $remember_me_field;

	/**
	 * Hash algorithm to use. If not set
	 * the passwords are saved without hashing.
	 * @var string
	 */
	protected $hash_method;

	/**
	 * Name of the login provider
	 * @var string
	 */
	protected $name = 'password';

	/**
	 * Lifetime of the remember_me cookie
	 * @var string
	 */
	protected $remember_me_lifetime = 604800;	//Default time = a week

	/**
	 * Secret key used for remember_me cookie
	 * @var string
	 */
	protected $secret_key = 'change_me_in_the_config_auth_file';

	/**
	 * Constructs password login provider for the specified configuration.
	 *
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param \PHPixie\Pixie\Service $service Service instance that this login provider belongs to.
	 * @param string $config Name of the configuration
	 */
	public function __construct($pixie, $service, $config) {
		parent::__construct($pixie, $service, $config);
		$this->login_field = $pixie->config->get($this->config_prefix."login_field");
		$this->password_field = $pixie->config->get($this->config_prefix."password_field");
		$this->remember_me_field = $pixie->config->get($this->config_prefix."remember_me_field");
		$this->remember_me_lifetime = $pixie->config->get($this->config_prefix."remember_me_lifetime");
		$this->secret_key = $pixie->config->get($this->config_prefix."secret_key");
		$this->hash_method = $pixie->config->get($this->config_prefix."hash_method",'md5');

		//Auto-login user if remembered
		if (!$this->check_login() && $pixie->cookie->get('remember_me')) {
			$this->login_remembered_me();
		}
	}

	/**
	 * Attempts to log the user in using his login and password
	 *
	 * @param string $login Users login
	 * @param string $password Users password
	 * @return bool If the user exists.
	 */
	public function login($login, $password, $remember_me = false) {
		$user = $this->service->user_model()
						->where($this->login_field, $login)
						->find();
		if($user->loaded()){
			$password_field = $this->password_field;
			$challenge = $user->$password_field;

			if($this->hash_method && 'crypt'==$this->hash_method) {
				if (function_exists('password_verify')) { // PHP 5.5.0+
					$password = password_verify($password, $challenge)?$challenge:false;
				} else {
					$password = crypt($password, $challenge);
				}
			} elseif($this->hash_method) {
				$salted = explode(':', $challenge);
				$password = hash($this->hash_method, $password.$salted[1]);
				$challenge = $salted[0];
			}
			if ($challenge === $password) {
				$this->set_user($user);

				if ($remember_me) {
					//Generate a token and save it for the user in db
					$token = md5(uniqid(rand(), true));
					$remember_me_field = $this->remember_me_field;
					$user->$remember_me_field = $token;
					$user->save();

					//Put the login info
					$cookie = $login . ':' . $token;
				    $mac = hash_hmac('sha256', $cookie, $this->secret_key);
				    $cookie .= ':' . $mac;
				    $this->pixie->cookie->set('remember_me', $cookie, $this->remember_me_lifetime, '/', null, null, false);
				}

				return true;
			}
		}
		return false;
	}

	/**
	 * Login a remembered user
	 *
	 * @return bool If the user exists.
	 */
	public function login_remembered_me()
	{
		$cookie = $this->pixie->cookie->get('remember_me') ? : '';
	    if ($cookie) {
	        list ($login, $token, $mac) = explode(':', $cookie);
	        if ($mac !== hash_hmac('sha256', $login . ':' . $token, $this->secret_key)) {
	            return false;
	        }
	    }
		$user = $this->service->user_model()
						->where($this->login_field, $login)
						->find();
		if ($user->loaded()) {
			$remember_me_field = $this->remember_me_field;
	        $token_from_db = $user->$remember_me_field;
	        if ($this->timing_safe_compare($token_from_db, $token)) {
	            $this->set_user($user);
	            return true;
	        }
		}
		return false;
	}

	/**
	 * A timing safe equals comparison
	 *
	 * Picked from http://stackoverflow.com/a/17266448/836501
	 * Also see http://blog.astrumfutura.com/2010/10/nanosecond-scale-remote-timing-attacks-on-php-applications-time-to-take-them-seriously/
	 *
	 * To prevent leaking length information, it is important
	 * that user input is always used as the second parameter.
	 *
	 * @param string $safe The internal (safe) value to be checked
	 * @param string $user The user submitted (unsafe) value
	 *
	 * @return bool True if the two strings are identical.
	 */
	private function timing_safe_compare($safe, $user) {
	    // Prevent issues if string length is 0
	    $safe .= chr(0);
	    $user .= chr(0);

	    $safeLen = strlen($safe);
	    $userLen = strlen($user);

	    // Set the result to the difference between the lengths
	    $result = $safeLen - $userLen;

	    // Note that we ALWAYS iterate over the user-supplied length
	    // This is to prevent leaking length information
	    for ($i = 0; $i < $userLen; $i++) {
	        // Using % here is a trick to prevent notices
	        // It's safe, since if the lengths are different
	        // $result is already non-0
	        $result |= (ord($safe[$i % $safeLen]) ^ ord($user[$i]));
	    }

	    // They are only identical strings if $result is exactly 0...
	    return $result === 0;
	}

	/**
	 * Hashes the password using the configured method
	 *
	 * @param string $password Password to hash
	 * @return string Hashed password
	 */
	public function hash_password($password){
		if(!$this->hash_method)
			return $password;
		if('crypt'==$this->hash_method) {
			if (function_exists('password_hash')) // PHP 5.5.0+
				return password_hash($password, PASSWORD_DEFAULT);
			$salt = str_replace(array('+', '='), array('.', ''),
			        base64_encode(pack('N9', mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand())));
			return crypt($password, '$2y$10$'.$salt);
		}
		$salt = uniqid(rand());
		return hash($this->hash_method, $password.$salt).':'.$salt;
	}
}
