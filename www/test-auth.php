<?php

// ref: https://developers.google.com/identity/protocols/OAuth2WebServer

define('OAUTH2_CLIENT_ID', '323243031994-ls5pjlrk6o0pkh8eu4d8gr30mcu070gg.apps.googleusercontent.com');
define('OAUTH2_CLIENT_SECRET', 'TrFIIuOgpZO5mGJ4WnsRZKEB');

// $authorizeURL = 'https://github.com/login/oauth/authorize';
$authorizeURL = 'https://accounts.google.com/o/oauth2/v2/auth';
//$tokenURL = 'https://github.com/login/oauth/access_token';
$tokenURL = 'https://www.googleapis.com/oauth2/v4/token';
//$apiURLBase = 'https://api.github.com/';
//$apiURLBase = 'https://www.googleapis.com/plus/v1/people/me';

//$apiURLBase = 'https://www.googleapis.com/oauth2/v2/userinfo';
$apiURLBase = 'https://www.googleapis.com/userinfo/v2/me';

session_start();

if(get('action') == 'logout') {
  session_destroy();
  header('Location: ' . $_SERVER['PHP_SELF']);
  die();
}

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


function apiRequest($url, $post=FALSE, $headers=array()) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

  if($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

  $headers[] = 'Accept: application/json';

  if(session('access_token'))
    $headers[] = 'Authorization: Bearer ' . session('access_token');

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_exec($ch);
  return json_decode($response);
}

function get($key, $default=NULL) {
  return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default=NULL) {
  return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}
