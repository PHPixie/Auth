<?php

/**
 * Facebook login provider
 *
 * @package    Auth
 */
class Facebook_Login_Auth extends Login_Auth {
	
	/**
	 * App ID of the facebook app
	 * @var string
	 * @access protected
	 */
	protected $app_id;
	
	/**
	 * App Secret of the facebook app
	 * @var string
	 * @access protected
	 */
	protected $app_secret;
	
	/**
	 * Permissions to request
	 * @var array
	 * @access protected
	 */
	protected $permissions;
	
	/**
	 * Field in the users table where the users
	 * facebook id is stored.
	 * @var string
	 * @access protected
	 */
	protected $fbid_field;
	
	/**
	 * Users access token
	 * @var string
	 * @access protected
	 */
	public $access_token;
	
	/**
	 * Token expiry time
	 * @var string
	 * @access protected
	 */
	public $token_expires;
	
	/**
	 * Session key to store the token in
	 * @var string
	 * @access protected
	 */
	protected $access_token_key;
	
	/**
	 * Session key to store token expiry time in
	 * @var string
	 * @access protected
	 */
	protected $token_expires_key;
	
	/**
	 * Constructs facebook login provider for the specified configuration.
	 * 
	 * @param Auth $auth Auth instance that this login provider belongs to.
	 * @param string $config Name of the configuration
	 * @access public
	 * @return void
	 */
	public function __construct($auth, $config) {
		parent::__construct($auth, $config);
		$this->app_id = Config::get($this->config_prefix."app_id");
		$this->app_secret = Config::get($this->config_prefix."app_secret");
		$this->permissions = Config::get($this->config_prefix."permissions",array());
		$this->fbid_field = Config::get($this->config_prefix."fbid_field");
		
		$this->access_token_key = "auth_{$config}_facebook_token";
		$this->token_expires_key = "auth_{$config}_facebook_token_expires";
	}
	
	/**
	 * Attempts to log the user in using his access token.
	 * 
	 * @param string $access_token Users access token
	 * @param int $token_lifetime Amount of seconds until the token expires
	 * @access public
	 * @return bool If the user exists.
	 */
	public function login($access_token, $token_lifetime = null) {
		$data = $this->request("https://graph.facebook.com/me?access_token=".$access_token);
		$user = json_decode($data);
		
		if(isset($user->id)){
			$user = $this->auth->user_model()->where($this->fbid_field, $user->id)->find();
			if ($user->loaded()) {
				$this->set_user($user, $access_token, $token_lifetime);
				return true;
			}
		}
		
		return false;		
	}
	
	/**
	 * Sets the user logged in via this provider.
	 * Stores users id, his token and token expiry time
	 * inside the session.
	 * 
	 * @param ORM $user Logged in user
	 * @param string $access_token Users access token
	 * @param int $token_lifetime Token lifetime
	 * @return void
	 * @access public
	 */
	public function set_user($user, $access_token = null, $token_lifetime = null) {
		parent::set_user($user);
		
		if ($access_token){
			$this->access_token = $access_token;
			$this->token_expires = $token_lifetime?(time() + $token_lifetime):null;
		}
		
		Session::set($this->access_token_key, $this->access_token);
		Session::set($this->token_expires_key, $this->token_expires);
	}
	
	/**
	 * Performs user logout.
	 * Also clears session variables associated with
	 * the users access token.
	 * 
	 * @access public
	 * @return void
	 */
	public function logout() {
		parent::logout();
		Session::remove($this->access_token_key);
		Session::remove($this->token_expires_key);
	}
	
	/**
	 * Checks if the user is logged in with facebook, if so
	 * notifies the associated Auth instance about it.
	 * 
	 * @return bool If the user is logged in
	 * @access public
	 */
	public function check_login() {
	
		if (parent::check_login()) {
			$this->access_token = Session::get($this->access_token_key);
			$this->token_expires = Session::get($this->token_expires_key);
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns login url for the server-side facebook login flow.
	 * 
	 * @param string $state A persistent code to prevent CSRF
	 * @param string $return_url URL to return the user after he authorizes the app.
	 * @param string $display_mode Determines the facebook page look.
	 *                             Can be either 'page' or 'popup'
	 * @access public
	 * @return string Login url.
	 */
	public function login_url($state, $return_url, $display_mode) {
		return "https://www.facebook.com/dialog/oauth/?"
				."client_id={$this->app_id}"
				."&redirect_uri={$return_url}"
				."&state={$state}"
				."&display={$display_mode}"
				."&scope=".implode(',', $this->permissions);
	}
	
	/**
	 * Exchanges OAuth code for the access token.
	 * 
	 * @param string $code OAuth code
	 * @param string $return_url URL to return the user after he authorizes the app.
	 * @access public
	 * @return array Parsed result of the facebook call.
	 */
	public function exchange_code($code, $return_url) {
		$url = "https://graph.facebook.com/oauth/access_token?"
				."client_id={$this->app_id}"
				."&redirect_uri={$return_url}"
				."&client_secret={$this->app_secret}"
				."&code={$code}";
		$response = $this->request($url);
		parse_str($response, $params);
		return $params;
	}
	
	/**
	 * Requests a url using CURL
	 * 
	 * @param string $url URL to fetch
	 * @param string $return_url URL to return the user after he authorizes the app.
	 * @access public
	 * @return array Parsed result of the facebook call.
	 * @throws Exception If the request failed
	 */
	public function request($url) {
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_HTTPHEADER     => array('Expect:'),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_URL            => $url
		));
		$response = curl_exec($ch);
		if($response === false)
			throw new Exception("URL request failed:".curl_error($ch));
			
		return $response;
	}
}