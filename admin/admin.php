<?php
// Database connection code with absolute path
require_once(dirname(__DIR__).'/../../takahe.conn.php');
// Make sure session variables exist
if(session_status() == PHP_SESSION_NONE) session_start();

$title = "Admin";

?><!doctype html>
<html class="no-js" lang="">

<head>
	<meta charset="utf-8">
	<?php
	echo "<title>$title - Pukeko Host</title>";
	echo "<meta name=\"og:title\" content=\"$title - Pukeko Host\">";
	?>

	<meta name="author" content="Yiays and Дункан">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="manifest" href="/site.webmanifest">
	<link rel="apple-touch-icon" href="/icon.png">
	<!-- Place favicon.ico in the root directory -->

	<link href="https://fonts.googleapis.com/css?family=Roboto:100,400,400i,700,700i&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/css/normalize.css">
	<link rel="stylesheet" href="/css/main.css?v=158">
	<?php if(isset($headerextra)) echo $headerextra; ?>

	<meta name="theme-color" content="#134FB0">
</head>

<body>
	<!--[if IE]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
	<![endif]-->

	<div class="jumbotron dark main<?php if(isset($compactheader)) echo " compact"; ?>">
		<div class="background" style="background: #134FB0;background: linear-gradient(to left, #134FB0 0%,#30475D 100%);"></div>
		<div class="footer">
			<nav>
				<a href="/admin/">Home</a>
				<div class="parent">
					<a href="#">Tasks</a>
					<div class="dropdown" href="#">
						<a href="/admin/users/">Manage Users</a>
						<a href="/admin/users/">Manage Transactions</a>
						<a href="/admin/users/">Support Tickets</a>
						<a href="/admin/users/">Manage GSMSes</a>
						<a href="/admin/users/">Manage Games</a>
					</div>
				</div>
				<a href="/account/"><?php echo (isset($_SESSION['access_token'])? $_SESSION['user']->username : 'Account'); ?></a>
			</nav>
		</div>
		<div class="content">
			<a class="brand" href="/"><h1>Pukeko<span class="dim">Host</span></h1></a>
			<h2>Administration</h2>
		</div>
	</div>
	
	<script src="/js/vendor/modernizr-3.8.0.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.4.1.min.js"><\/script>')</script>
  <script src="/js/plugins.js"></script>
  <script src="/js/main.js?v=5"></script>
</body>

</html><?php
$conn->close();
?>