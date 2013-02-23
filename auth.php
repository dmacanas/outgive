<?php

require('facebook.php');

define('APP_ID', '133717613395060');
define('APP_SECRET', 'ca2424b53363e22053c3fa7050f1643e');
define('DOMAIN', 'www.outgive.me');
define('BASE_URL', 'http://' . DOMAIN . '/');

$auth_php = array('user' => '', 'cookie' => null);
$db = null;

$facebook = new Facebook(array(
	'appId' => APP_ID,
	'secret' => APP_SECRET
));

function check_user_account()
{
	global $auth_php, $db;

	require('db.php');

	$db = @mysql_connect($ogdb['host'], $ogdb['user'], $ogdb['pass']);
	if(!$db) {
		error_log(sprintf("%s:%d:(%d) %s Could not connect to db", preg_replace('/.*\//', '', __FILE__), __LINE__, mysql_errno(), mysql_error()));
		return false;
	}
	if(!mysql_select_db($ogdb['db'], $db)) {
		error_log(sprintf("%s:%d:(%d) %s Could not connect to db", preg_replace('/.*\//', '', __FILE__), __LINE__, mysql_errno($db), mysql_error($db)));
		return false;
	}

	$sql = sprintf("SELECT UNIX_TIMESTAMP(last_update) as last_update, total_given, overall, in_friends, in_hometown, in_location From members Where userid='%s'", mysql_real_escape_string($auth_php['user']['id'], $db));
	$result = mysql_query($sql, $db);
	if(!$result) {
		error_log(sprintf("%s:%d:(%d) %s SQL error:\n%s", preg_replace('/.*\//', '', __FILE__), __LINE__, mysql_errno($db), mysql_error($db), $sql));
		return false;
	}

	if(mysql_num_rows($result) === 1) {
		$row = mysql_fetch_assoc($result);
		$auth_php['user']['db_updated']  = $row['last_update'];
		$auth_php['user']['total_given'] = $row['total_given'];
		$auth_php['user']['in_friends']  = $row['in_friends'];
		$auth_php['user']['in_hometown'] = $row['in_hometown'];
		$auth_php['user']['in_location'] = $row['in_location'];
	}
	else {
		$schools = array();
		$member_schools = array();
		foreach($auth_php['user']['education'] as $school) {
			$slug = preg_replace('/-{2,}/', '-', preg_replace('/[^a-zA-Z0-9-]/', '-', strtolower($school['school']['name'])));

			$schools[] = sprintf("'%s', '%s', '%s'",
						mysql_real_escape_string($school['school']['id'], $db),
						mysql_real_escape_string($school['school']['name'], $db),
						mysql_real_escape_string($slug, $db));

			$member_schools[] = sprintf("'%s', '%s', '%s', '%s'",
						mysql_real_escape_string($auth_php['user']['id'], $db),
						mysql_real_escape_string($school['school']['id'], $db),
						mysql_real_escape_string($school['year']['name'], $db),
						mysql_real_escape_string($school['type'], $db));
		}
		$sql = sprintf("
			INSERT Ignore Into schools (
				schoolid, name, slug
			) VALUES (
				%s
			)
			",
			join("\n), (\n\t", $schools)
		);
		$result = mysql_query($sql, $db);
		if(!$result) {
			error_log(sprintf("%s:%d:(%d) %s SQL error:\n%s", preg_replace('/.*\//', '', __FILE__), __LINE__, mysql_errno($db), mysql_error($db), $sql));
			return false;
		}

		$sql = sprintf("
			INSERT Ignore Into members (
				userid, email, name, gender, fbpage, hometown, location, locale
			) VALUES (
				'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
			)",
			mysql_real_escape_string($auth_php['user']['id'], $db),
			mysql_real_escape_string($auth_php['user']['email'], $db),
			mysql_real_escape_string($auth_php['user']['name'], $db),
			mysql_real_escape_string($auth_php['user']['gender'], $db),
			mysql_real_escape_string(preg_replace('/^https?:\/\/www.facebook.com\//', '', $auth_php['user']['link']), $db),
			mysql_real_escape_string($auth_php['user']['hometown']['name'], $db),
			mysql_real_escape_string($auth_php['user']['location']['name'], $db),
			mysql_real_escape_string($auth_php['user']['locale'], $db)
		);

		$result = mysql_query($sql, $db);
		if(!$result) {
			error_log(sprintf("%s:%d:(%d) %s SQL error:\n%s", preg_replace('/.*\//', '', __FILE__), __LINE__, mysql_errno($db), mysql_error($db), $sql));
			return false;
		}
		if(mysql_affected_rows($db) == 1) {
			$auth_php['user']['db_updated'] = time();
		}
		$auth_php['user']['total_given'] = 0;
		$auth_php['user']['in_friends'] = $auth_php['user']['in_hometown'] = $auth_php['user']['in_location'] = null;

		$sql = sprintf("INSERT Ignore Into member_schools (
					userid, schoolid, year, type
				) VALUES (
					%s
				)", join("\n), (\n\t", $member_schools));
		$result = mysql_query($sql, $db);
		if(!$result) {
			error_log(sprintf("%s:%d:(%d) %s SQL error:\n%s", preg_replace('/.*\//', '', __FILE__), __LINE__, mysql_errno($db), mysql_error($db), $sql));
			return false;
		}
	}
	$auth_php['user']['has_account'] = true;
	return true;
}

function check_auth($redir=false)
{
	global $auth_php;

	if($auth_php['user'])
		return true;
	elseif($redir) {
		header("Location: " . BASE_URL);
		exit(0);
	}
	return false;
}

function get_user()
{
	global $auth_php, $facebook;

	if($auth_php['user']) {
		try {
			return $facebook->api('/me');
		}
		catch(FacebookApiException $e) {
			error_log("%s:%d:Exception %s", preg_replace('/.*\//', '', __FILE__), __LINE__, $e);
		}
	}
	return null;
}

$auth_php['user'] = $facebook->getUser();
if($auth_php['user']) {
	$auth_php['user'] = get_user();
}
if($auth_php['user']) {
	check_user_account();
}

?>
