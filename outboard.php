<?php
require('auth.php');
if(!$noauth_ok) {
	check_auth(true);
}
$body_class='';
if($_SERVER['SCRIPT_NAME'] == '/index.php') {
	$body_class=' class="splash"';
}

$path = preg_replace('/^.*\/outboard(?:\.php)?\//', '', $_SERVER['REQUEST_URI']);

if($path) {
	$selected_school = preg_replace('/^school\//', '', $path);
	if(!is_numeric($selected_school))
		$selected_school = null;
}
else {
	$selected_school = null;
}

function pledge_amount($user_id, $school_id, $amount)
{
	global $db;

	if(!is_numeric($user_id) || !is_numeric($school_id) || !is_numeric($amount)) {
		return false;
	}

	$sql = sprintf("
			Update members
			 INNER JOIN member_schools USING(userid)
			 INNER JOIN schools USING(schoolid)
			Set
				members.total_given = members.total_given + %s,
				schools.total_pledged = schools.total_pledged + %s,
				member_schools.total_given = member_schools.total_given + %s
			Where members.userid=%s AND schools.schoolid=%s",
				mysql_real_escape_string($amount, $db), mysql_real_escape_string($amount, $db), mysql_real_escape_string($amount, $db),
				mysql_real_escape_string($user_id, $db), mysql_real_escape_string($school_id, $db));

	$result = mysql_query($sql, $db);
	if(!$result) {
		error_log(sprintf("%s:%d:(%d) %s SQL error:\n%s", preg_replace('/.*\//', '', __FILE__), __LINE__, mysql_errno($db), mysql_error($db), $sql));
		return false;
	}

	if(mysql_affected_rows($db) >= 1) {
		return true;
	}

	return false;
}

function get_pledged($user_id, $school_id=-1)
{
	global $db;

	if(!is_numeric($user_id) || !is_numeric($school_id)) {
		return false;
	}

	$sql = sprintf("Select total_given as amt, 'school_me' as who From member_schools Where userid=%s AND schoolid=%s
			UNION
			Select total_given as amt, 'me_total' as who From members Where userid=%s
			UNION
			Select total_pledged as amt, 'school_total' as who From schools Where schoolid=%s",
				mysql_real_escape_string($user_id, $db),
				mysql_real_escape_string($school_id, $db),
				mysql_real_escape_string($user_id, $db),
				mysql_real_escape_string($school_id, $db));

	$result = mysql_query($sql, $db);
	if(!$result) {
		error_log(sprintf("%s:%d:(%d) %s SQL error:\n%s", preg_replace('/.*\//', '', __FILE__), __LINE__, mysql_errno($db), mysql_error($db), $sql));
		return false;
	}

	$data = array();

	while(($row = mysql_fetch_assoc($result))) {
		$data[$row['who']] = $row['amt'];
	}

	return $data;
}

function get_top_school()
{
	global $db;

	$sql = 'Select schools.*, COUNT(*) AS num_pledgers From schools INNER JOIN member_schools USING(schoolid)
		 Where total_pledged > 0 AND total_given > 0
		 Group By schoolid
		 Order By num_pledgers DESC, total_pledged DESC';

	$result = mysql_query($sql, $db);

	if(!$result) {
		error_log(sprintf("%s:%d:(%d) %s SQL error:\n%s", preg_replace('/.*\//', '', __FILE__), __LINE__, mysql_errno($db), mysql_error($db), $sql));
		return false;
	}

	return mysql_fetch_assoc($result);	// returns FALSE if no school matched
}

function cmp_year($a, $b)
{
	return $b['year'] - $a['year'];
}

$user = get_user();
$schools = array();
foreach($user['education'] as $school) {
	$schools[] = array('id' => $school['school']['id'], 'name' => $school['school']['name'], 'year' => $school['year']['name'], 'type' => $school['type']);
	if($school['school']['id'] === $selected_school) {
		$selected_school = $school['school'];
	}
}
usort($schools, 'cmp_year');
?>

<!doctype html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="initial-scale=1,width=device-width">

<title>Outgive</title>

<link href='http://fonts.googleapis.com/css?family=Leckerli+One' rel='stylesheet' type='text/css'>
<link href='/og.css' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="/default.css" />
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
<div class="header_bar" id="user">
<h1 class="user_name"><?php echo htmlspecialchars($user['name']); ?></h1><a href="/error.html" class="user_name">(Edit User Profile)</a> <a href="/error.html" class="user_name">(Sign out)</a>
    <a href=<?php echo BASE_URL ?>><img src="/logo.png" class="user_logo"></a>
</div>
<div id="body">

<div class="content" id="user_content">

<div class="schools" id="unselected">
<h2 id="school_list"></h2>
<ul>
<?php
$seen = array();
foreach($schools as $school) {
	if(!$seen[$school['id']] && in_array($school['type'], array('Graduate School', 'College')) ) {
		$seen[$school['id']] = true;
?>
<li class="unselected"><a href="/outboard.php/school/<?php echo rawurlencode($school['id']) ?>"><?php echo htmlspecialchars($school['name']) ?></a></li>
<?php
	}
}
?>
</ul>
</div><!-- /#schools -->

<?php
$top_school = get_top_school();
$pledges = get_pledged($user['id'], (is_array($selected_school)?$selected_school['id']:-1));
?>
<div class="display_info">
The top ranked school by number of donors is <strong><?php echo htmlspecialchars($top_school['name']) ?></strong>.
<em><?php echo $top_school['num_pledgers'] ?></em> of its alumni ha<?php echo ($top_school['num_pledgers'] == 1) ? 's':'ve'?> pledged a total of <em>$<?php printf('%.2f', $top_school['total_pledged']); ?></em>.
</div>
<div class="display_info">
You've pledged a total of <?php printf('<em>$%.2f</em>', $pledges['me_total']); ?><?php
	if(isset($pledges['school_me']))
		printf(', including <em>$%.2f</em> to <strong>%s</strong> which has received a total of <em>$%.2f</em> in pledges',
			$pledges['school_me'], $selected_school['name'], $pledges['school_total']);
?>.
</div>

<div class="selected_school">
<!-- h2>Pledge <?php echo ($pledges['school_me'] > 0) ? 'some more' : 'now' ?></h2 -->
<?php
if(is_array($selected_school)) {
	if(isset($_POST['nocsrf'])) {
		$rand = preg_replace('/^([0-9a-f]{4}).*$/', '$1', $_POST['nocsrf']);
		$token = $rand . hash_hmac('sha1', $user['id'] . $_SERVER['REMOTE_ADDR'] . $rand, 'floccinocciribonucleicacid');
	}
	$show_form=true;
	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pledge']) && $_POST['nocsrf'] == $token) {
		if(pledge_amount($user['id'], $selected_school['id'], $_POST['pledge'])) {
?>
Thank you.
<?php
			$show_form=false;
		}
		else {
?>
Our server made a booboo.  Try again or shout at us.
<?php
		}
	}
	if($show_form) {
		$rand = sprintf("%04x", rand(0, 0xffff));
		$token = $rand . hash_hmac('sha1', $user['id'] . $_SERVER['REMOTE_ADDR'] . $rand, 'floccinocciribonucleicacid');
?>
<form method="post">
<input type="hidden" name="nocsrf" value="<?php echo $token ?>">
<p class="pledge">
I, <?php echo htmlspecialchars($user['name']) ?>, hereby <label for="pledge">pledge, the sum of $<input type="text" size="6" class="inline-element" id="pledge" name="pledge" placeholder="0.00" tabindex="1"></label>
to <?php echo (!empty($selected_school['type']) ? 'my ' . htmlspecialchars($selected_school['type']) . ', ': '') ?><?php echo htmlspecialchars($selected_school['name']) ?>.
</p>
<p class="pledge">
<input type="submit" value="Pledge Now!">
</p>
</form>
<?php
	}
}
?>
</div>

<div id="feed">
<iframe src="//www.facebook.com/plugins/activity.php?site=<?php echo DOMAIN ?>&amp;width=200&amp;height=300&amp;header=true&amp;colorscheme=light&amp;linktarget=_blank&amp;border_color&amp;font&amp;recommendations=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:300px;" allowTransparency="true"></iframe>
</div>

<div>
</div>

</div><!-- /.content -->
<br style="clear:both;">

<?php
require('footer.php');
?>