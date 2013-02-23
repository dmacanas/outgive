<?php
require('auth.php');
if(!$noauth_ok) {
	check_auth(true);
}
$body_class='';
if($_SERVER['SCRIPT_NAME'] == '/index.php') {
	$body_class=' class="splash"';
}
?>
<!doctype html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="initial-scale=1,width=device-width">

<title>Outgive</title>

<link href='http://fonts.googleapis.com/css?family=Leckerli+One' rel='stylesheet' type='text/css'>
<link href='/og.css' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="default.css" />
<script type="text/javascript" src="script.js"></script>
<script src="//connect.facebook.net/en_US/all.js" id="facebook-jssdk"></script>

<meta property="og:title" content="Outgive">
<meta property="og:type" content="cause">
<meta property="og:url" content="<?php echo BASE_URL ?>">
<meta property="og:image" content="">
<meta property="og:site_name" content="Outgive">
<meta property="fb:app_id" content="133717613395060">

</head>
<body<?php echo $body_class ?>>
<div id="fb-root"></div>
<script>
FB.init({ appId:'<?php echo APP_ID ?>', cookie:true, status:true, xfbml:true });
FB.Event.subscribe('auth.login', function(response) { window.location.reload(); });
FB.Event.subscribe('auth.logout', function(response) { window.location.reload(); });
</script>
<div class="header_bar" id="header">
<div class="header_topper" >
</div>
<h1><a href="<?php echo BASE_URL ?>"><img src="outgive.png" class="main_logo"></a></h1>
</div>
<div id="body">

