<?php
require_once('../../takahe.conn.php');
require_once('../api/account.php');
session_start();
if(!session('account')) $_SESSION['account'] = new account($conn);
else session('account')->conn = $conn;

if(get('rememberme')){
	$params = session_get_cookie_params();
	setcookie(session_name(), $_COOKIE[session_name()], time() + 60*60*24*30, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	die('1');
}
if(get('login')){
	if(!session('account')->logged_in){
		$result = session('account')->login(get('return')?urldecode(get('return')):'/account/');
		header("Location: $result[redirect]");
		die();
	}
}
if(get('logout')){
	if(session('account')->logged_in){
		session('account')->logout();
	}
}

if(session('account')->logged_in) {
	$title = session('account')->username." | Account";
	$subtitle = "Account Settings";
	require_once('../includes/header.php');
	?>
	<div class="jumbotron dark">
		<div class="content">
			<h2>Logged In</h2>
			<p>Welcome, <?php echo session('account')->username;?></p>
		</div>
		<div class="footer">
			<a href="?logout" class="btn">Logout</a>
		</div>
		<div class="background" style="background:black;">
			<img class="float-right" id="userphppfp" alt="<?php echo session('account')->username;?>'s profile picture" src="<?php echo session('account')->avatar;?>">
			<img class="stretch-fill blur dim" alt="<?php echo session('account')->username;?>'s profile picture, but big and blury" src="<?php echo session('account')->avatar;?>">
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
	<sub><b>By signing in to Pukeko Host, you are accepting the <a href="/terms/">Terms of Service and Privacy Policy</a>.</b></sub>
</div>
<?php
require_once('../includes/footer.php');

// some handy functions

function session($key, $default=NULL) {
	return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}
?>