<?php
require('../../takahe.conn.php');
require('../api/api.php');
//if(!isset($_SESSION['account'])) $_SESSION['account'] = serialize(new account($conn));
$account = unserialize($_SESSION['account']);
$account->conn = $conn;

if(isset($_GET['rememberme'])){
	$params = session_get_cookie_params();
	setcookie(session_name(), $_COOKIE[session_name()], time() + 60*60*24*30, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	die('1');
}
if(isset($_GET['login'])){
	if(!$account->logged_in){
		$result = $account->login(isset($_GET['return'])?urldecode($_GET['return']):'/account/');
		$_SESSION['account'] = serialize($account);
		header("Location: $result[redirect]");
		die();
	}
}
if(isset($_GET['logout'])){
	if($account->logged_in){
		$account->logout();
		$_SESSION['account'] = serialize($account);
	}
}

if($account->logged_in) {
	$title = $account->Username." | Account";
	$subtitle = "Account Settings";
	require_once('../includes/header.php');
	?>
	<div class="jumbotron dark">
		<div class="content">
			<h2>Logged In</h2>
			<p>Welcome, <?php echo $account->Username;?></p>
		</div>
		<div class="footer">
			<a href="?logout" class="btn">Logout</a>
		</div>
		<div class="background" style="background:black;">
			<img class="float-right" id="userphppfp" alt="<?php echo $account->Username;?>'s profile picture" src="<?php echo $account->Avatar;?>">
			<img class="stretch-fill blur dim" alt="<?php echo $account->Username;?>'s profile picture, but big and blury" src="<?php echo $account->Avatar;?>">
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
?>