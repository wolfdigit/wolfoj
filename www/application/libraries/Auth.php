<?php

// ref: https://developers.google.com/identity/protocols/OAuth2WebServer

define('OAUTH2_CLIENT_ID', '323243031994-ls5pjlrk6o0pkh8eu4d8gr30mcu070gg.apps.googleusercontent.com');
define('OAUTH2_CLIENT_SECRET', 'TrFIIuOgpZO5mGJ4WnsRZKEB');


$CI = null;

class Auth {
private $authorizeURL = 'https://accounts.google.com/o/oauth2/v2/auth';
private $tokenURL = 'https://www.googleapis.com/oauth2/v4/token';
//$apiURLBase = 'https://www.googleapis.com/plus/v1/people/me';
//$apiURLBase = 'https://www.googleapis.com/oauth2/v2/userinfo';
private $apiURLBase = 'https://www.googleapis.com/userinfo/v2/me';
private $callbackURL = 'http://wolfoj.wolfdigit.csie.org/OJ2/user/getcode';
	public function __construct() {
		$CI = &get_instance();
		$CI->load->library('session');
	}

	public function user() {
		global $CI;
		//echo "(". var_dump($CI->session->userdata('user_id')) .")"; die();
		return $CI->session->userdata('user_id');
	}

	public function checkLogin() {
		if ($this->user()==null) return false;
		else                       return true;
	}

	public function needLogin() {
		global $CI;
		if (!$this->checkLogin()) {
			//echo "need login"; die();
			$CI->load->helper('URL');
			$CI->session->set_userdata('returnURL', current_url());
			redirect('/user/login');
		}
	}

	// Start the login process by sending the user to Github's authorization page
	public function login() {
		global $CI;
		$CI->load->helper('URL');

		// Generate a random hash and store in the session for security
		$state = hash('sha256', microtime(TRUE).rand().$_SERVER['REMOTE_ADDR']);
		$CI->session->set_userdata('auth_state', $state);
		$CI->session->unset_userdata('user_id');
		
		$params = array(
		  'client_id' => OAUTH2_CLIENT_ID,
		  'redirect_uri' => $this->callbackURL,
		  //'scope' => 'user',
		  //'scope' => 'https://www.googleapis.com/auth/userinfo.profile',
		  'scope' => 'https://www.googleapis.com/auth/userinfo.email',
		  'state' => $state,
		  'response_type' => 'code',
		  'prompt' => 'select_account'
		);
		

		var_dump($params);
		// Redirect the user to Github's authorization page
		redirect($this->authorizeURL . '?' . http_build_query($params));
		//die();
	}

	// When Github redirects the user back here, there will be a "code" and "state" parameter in the query string
	function gettoken($code) {
		global $CI;
		$CI->load->helper('URL');

		// Verify the state matches our stored state
		if ($CI->input->get('state')!=$CI->session->userdata('auth_state')) {
				// fail
				redirect('');
		}
		
		// Exchange the auth code for a token
		$token = apiRequest($this->tokenURL, array(
		  'client_id' => OAUTH2_CLIENT_ID,
		  'client_secret' => OAUTH2_CLIENT_SECRET,
		  'redirect_uri' => $this->callbackURL,
		  'state' => $CI->session->userdata('auth_state'),
		  'grant_type' => 'authorization_code',
		  'code' => $CI->input->get('code')
		));
		return $token->access_token;
	}

	public function token2email($token) {
		global $CI;
		$user = apiRequest($this->apiURLBase, false, $token);
		$CI->session->set_userdata('user_id', $user->email);
		return $user->email;
	}

	function redirectBack() {
		global $CI;
		redirect($CI->session->userdata('returnURL'));
		die();
	}

	function logout() {
		global $CI;
		$CI->session->unset_userdata('user_id');
		$CI->load->helper('URL');
		redirect('');
	}
}


/*
if(get('action') == 'logout') {
  session_destroy();
  header('Location: ' . $_SERVER['PHP_SELF']);
  die();
}
*/
/*
// Start the login process by sending the user to Github's authorization page
if(get('action') == 'login') {
  // Generate a random hash and store in the session for security
  $_SESSION['state'] = hash('sha256', microtime(TRUE).rand().$_SERVER['REMOTE_ADDR']);
  unset($_SESSION['access_token']);

  $params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
    //'scope' => 'user',
    //'scope' => 'https://www.googleapis.com/auth/userinfo.profile',
    'scope' => 'https://www.googleapis.com/auth/userinfo.email',
    'state' => $_SESSION['state'],
    'response_type' => 'code',
    'prompt' => 'select_account'
  );

  // Redirect the user to Github's authorization page
  header('Location: ' . $authorizeURL . '?' . http_build_query($params));
  die();
}
*/
/*
// When Github redirects the user back here, there will be a "code" and "state" parameter in the query string
if(get('code')) {
  // Verify the state matches our stored state
  if(!get('state') || $_SESSION['state'] != get('state')) {
    header('Location: ' . $_SERVER['PHP_SELF']);
    die();
  }

  // Exchange the auth code for a token
  $token = apiRequest($tokenURL, array(
    'client_id' => OAUTH2_CLIENT_ID,
    'client_secret' => OAUTH2_CLIENT_SECRET,
    'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
    'state' => $_SESSION['state'],
    'grant_type' => 'authorization_code',
    'code' => get('code')
  ));
  $_SESSION['access_token'] = $token->access_token;

  header('Location: ' . $_SERVER['PHP_SELF']);
}
*/
/*
if(session('access_token')) {
  $user = apiRequest($apiURLBase);

  echo '<h3>Logged In</h3>';
  echo '<h4>' . $user->name . '</h4>';
  echo '<pre>';
  print_r($user);
  echo '</pre>';
  echo '<p><a href="?action=logout">logout</a></p>';

} else {
  echo '<h3>Not logged in</h3>';
  echo '<p><a href="?action=login">Log In</a></p>';
}
*/

function apiRequest($url, $post=FALSE, $token=false, $headers=array()) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

  if($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

  $headers[] = 'Accept: application/json';

  if($token!=false)
    $headers[] = 'Authorization: Bearer ' . $token;

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_exec($ch);
  return json_decode($response);
}
/*
function get($key, $default=NULL) {
  return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default=NULL) {
  return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}
*/
