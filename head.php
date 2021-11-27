<?php
	include_once("config.php");

	if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
		if (!(($_COOKIE['username'] === $user_name) && ($_COOKIE['password'] === md5($pass_word)))) {
			header('Location: login.php');
		}
	} else {
		header('Location: login.php');
	}
?>

<!DOCTYPE html> 
<html>
<head>
<title>hMailServer Dynamic Black/White List</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" media="all" href="stylesheet.css">
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet"> 

<!-- Autocomplete jquery -->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

</head>
<body>
<?php include("header.php") ?>
<div class="wrapper">