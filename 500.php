<?php
$title = "Internal Server Error";
require_once('includes/header.php');
?>
<div class="wrapper">
	<h1>Uh oh</h1>
	<p>
		Something went wrong when we were handling your last request.
		We'll be investigating this as soon as possible. In the meantime, could you describe what you were doing?
	</p>
	<form action="/500.php" method="POST">
		<textarea></textarea><br>
		<input type="submit" class="btn" value="submit"> 
	</form>
</div>
<?php
require_once('includes/footer.php');
?>