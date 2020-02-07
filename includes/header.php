<?php
// Database connection code with absolute path
require_once(dirname(__DIR__).'../../takahe.conn.php');
// Make sure session variables exist
if(session_status() == PHP_SESSION_NONE) session_start();
?><!doctype html>
<html class="no-js" lang="">

<head>
	<meta charset="utf-8">
	<?php
	global $title, $description, $tags, $subtitle, $image, $compactheader, $headerextra;
	echo "<title>$title - Pukeko Host</title>";
	echo "<meta name=\"og:title\" content=\"$title - Pukeko Host\">";

	if(isset($description)){
		echo "<meta name=\"description\" content=\"$description\">";
		echo "<meta name=\"og:description\" content=\"$description\">";
	}else{
		echo "<meta name=\"description\" content=\"Pay for hosting for your favourite games when you're playing them and never when you aren't.\">";
		echo "<meta name=\"og:description\" content=\"Pay for hosting for your favourite games when you're playing them and never when you aren't.\">";
	}
	if(isset($tags)){
		echo "<meta name=\"keywords\" content=\"game,gaming,hosting,server,game server,multiplayer,game host,pukeko,nz bird,new zealand,$tags\">";
	}else{
		echo "<meta name=\"keywords\" content=\"game,gaming,hosting,server,game server,multiplayer,game host,pukeko,nz bird,new zealand\">";
	}
	if(isset($image)){
		echo "<meta name=\"og:image\" content=\"$image\">";
	}
	?>

	<meta name="author" content="Yiays and Дункан">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="manifest" href="/site.webmanifest">
	<link rel="apple-touch-icon" href="/icon.png">
	<!-- Place favicon.ico in the root directory -->

	<link href="https://fonts.googleapis.com/css?family=Roboto:100,400,400i,700,700i&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/css/normalize.css">
	<link rel="stylesheet" href="/css/main.css?v=127">
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
				<a href="/">Home</a>
				<?php
				$result = $conn->query("SELECT Name,API FROM game");
				if($result){
				?>
				<div class="parent">
					<a href="#">Games</a>
					<div class="dropdown" href="#">
						<?php
						while($row = $result->fetch_assoc()){
							echo "<a href=\"/game/".$row['API']."\">".$row['Name']."</a>";
						} ?>
					</div>
				</div>
				<?php
				}
				?>
				<a href="/dashboard/">Dashboard</a>
				<a href="/account/"><?php echo (isset($_SESSION['access_token'])? $_SESSION['user']->username : 'Account'); ?></a>
			</nav>
		</div>
		<div class="content">
			<a class="brand" href="/">Pukeko<span class="dim">Host</span></a>
			<?php
				if(isset($subtitle)) echo "<h2>$subtitle</h2>";
			?>
		</div>
	</div>