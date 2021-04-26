<?php
session_start();
if(!isset($_SESSION['is_login'])){
    header("Location: ./login.html");
    exit();
}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>HOME</title>
		<link rel="stylesheet" href="/static/home.css" />
		<noscript><link rel="stylesheet" href="/assets/css/noscript.css" /></noscript>
	</head>
	<body class="is-preload">
		<div id="wrapper">
			<div id="bg"></div>
			<div id="overlay"></div>
			<div id="main">

				<!-- Header -->
					<header id="header">
						<h1>HOME</h1>
						<p>&nbsp;&bull;&nbsp; 每天都是最好的自己 &nbsp;&bull;&nbsp;</p>
					</header>
			</div>
		</div>
		<script>
			window.onload = function() { document.body.classList.remove('is-preload'); }
			window.ontouchmove = function() { return false; }
			window.onorientationchange = function() { document.body.scrollTop = 0; }
		</script>

<script 
	disable-devtool-auto 
	src='https://cdn.jsdelivr.net/npm/disable-devtool@latest/disable-devtool.min.js' 
	disable-menu='false'
	></script>
</body>
</html>
