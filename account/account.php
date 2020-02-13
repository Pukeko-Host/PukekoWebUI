<?php
// Authorize with discord (define('OAUTH2_CLIENT_ID') and define('OAUTH2_CLIENT_SECRET'))
require_once('../../takahe.discord.php');
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes. In case if your CURL is slow and is loading too much (Can be IPv6 problem)
error_reporting(E_ALL);
$apiURLBase = 'https://discordapp.com/api';
$home = 'https://pukeko.yiays.com/account/';

if(get('rememberme')){
	$params = session_get_cookie_params();
	setcookie(session_name(), $_COOKIE[session_name()], time() + 60*60*24*30, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	die('1');
}
if(get('code')) { // When Discord redirects the user back here, there will be a "code" and "state" parameter in the query string
	if(isset($_SESSION['access_token'])){
		// This is an old request that has already been handled
		header('Location: '.$home);
	}
	// Exchange the auth code for a token
	$token = apiRequest($apiURLBase.'/oauth2/token', array(
		"grant_type" => "authorization_code",
		'client_id' => OAUTH2_CLIENT_ID,
		'client_secret' => OAUTH2_CLIENT_SECRET,
		'redirect_uri' => $home,
		'code' => get('code')
	));
	if($token){
		// Connect to DB
		require_once('../../takahe.conn.php');
		$_SESSION['access_token'] = $token->access_token;
		$user = apiRequest($apiURLBase.'/users/@me');
		$guilds = apiRequest($apiURLBase.'/users/@me/guilds');
		$_SESSION['user'] = $user;
		$_SESSION['guilds'] = $guilds;
		if(!$conn->query("CALL AddUser(".strval($user->id).",'".$conn->escape_string($user->username)."',".strval($user->discriminator).",'".$conn->escape_string($user->email)."',@UserId)")){
			$params = array(
				'access_token' => session('access_token')
			);
			apiRequest($apiURLBase.'/oauth2/token/revoke',$params);
			session_destroy();
			printf("Error recording your login! Please try logging in later. ".$conn->error);
			$conn->close();
			die();
		}else{
			foreach($guilds as &$guild){
				$conn->query("CALL AddGuild(".$guild->id.",'".$guild->icon."',\"".$conn->escape_string($guild->name)."\",".$user->id.")");
			}
			$result = $conn->query("SELECT @UserId;");
			$_SESSION['userid'] = $result->fetch_array()[0][0];
			if(isset($_SESSION['return'])){
				header('Location: '.$_SESSION['return']);
				$conn->close();
				die('You are being redirected.');
			}
		}
		$conn->close();
	}else{
		unset($_SESSION['return']);
		die('Failed to log in, please <a href="/account/?login">try again</a>.');
	}
}
if(get('error')) {
	echo '<h1>'.get('error').'</h1>';
	echo '<p>'.get('error_description').'</p>';
	unset($_SESSION['return']);
	die('Failed to log in, please <a href="/account/?login">try again</a>.');
}
if(get('logout')) {
	$params = array(
		'access_token' => session('access_token')
	);
	apiRequest($apiURLBase.'/oauth2/token/revoke',$params);
	session_destroy();
	header('Location: '.$home);
	die();
}
if(get('login')) { // Start the login process by sending the user to Discord's authorization page
	if(get('return')) $_SESSION['return']=urldecode(get('return'));
	else $_SESSION['return']='/account/';
	if(isset($_SESSION['access_token'])){
		header('Location: '.$_SESSION['return']);
		unset($_SESSION['return']);
		die();
	}
	$params = array(
		'client_id' => OAUTH2_CLIENT_ID,
		'redirect_uri' => $home,
		'response_type' => 'code',
		'scope' => 'identify email guilds'
	);
	// Redirect the user to Discord's authorization page
	header("Location: $apiURLBase/oauth2/authorize?" . http_build_query($params));
	die();
}

// If the page hasn't died yet, show a default screen.
if(session('access_token')) {
	$user = apiRequest($apiURLBase.'/users/@me');
	$title = $user->username." | Account";
	$subtitle = "Account Settings";
	require_once('../includes/header.php');
	?>
	<div class="jumbotron dark">
		<div class="content">
			<h2>Logged In</h2>
			<p>Welcome, <?php echo $user->username;?></p>
		</div>
		<div class="footer">
			<a href="?logout" class="btn">Logout</a>
		</div>
		<div class="background" style="background:black;">
			<img class="float-right" id="userphppfp" alt="<?php echo $user->username;?>'s profile picture" src="https://cdn.discordapp.com/avatars/<?php echo $user->id.'/'.$user->avatar;?>.jpg?size=256">
			<img class="stretch-fill blur dim" alt="<?php echo $user->username;?>'s profile picture, but big and blury" src="https://cdn.discordapp.com/avatars/<?php echo $user->id.'/'.$user->avatar;?>.jpg?size=256">
		</div>
	</div>
<?php
} else {
	$title = "Account";
	$subtitle = "Account Settings";
	require_once('../includes/header.php');
	?>
	<div class="jumbotron">
		<div class="content">
			<h2>Not logged in</h2>
		</div><div class="footer">
			<p><a href="?login" class="btn">Login</a></p>
		</div>
	</div>
	<div class="jumbotron dark">
		<div class="content">
			<h3>Manage your account</h3>
			<p>This is where you can manage your account, once logged in with Discord.</p>
		</div>
		<div class="background" style="background:linear-gradient(to bottom, #737c88 0%,#333c48 100%);">
		</div>
	</div>
	<?php
}
?>
<div class="wrapper main">
	<sub><b>By signing in to Pukeko Host, you are accepting the <a href="/terms">Terms of Service and Privacy Policy</a>.</b></sub>
</div>
<?php
require_once('../includes/footer.php');

// some handy functions

function apiRequest($url, $post=FALSE, $headers=array()) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	if($post){
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
	}
	$headers[] = 'Accept: application/json';
	if(session('access_token'))
		$headers[] = 'Authorization: Bearer ' . session('access_token');
	array_push($headers,"Content-Type: application/x-www-form-urlencoded");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$response = curl_exec($ch);
	return json_decode($response);
}
function get($key, $default=NULL) {
	return array_key_exists($key, $_GET) ? (empty($_GET[$key]) ? True : $_GET[$key]) : $default;
}
function session($key, $default=NULL) {
	return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}
?>