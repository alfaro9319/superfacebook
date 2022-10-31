<?php
$PRODUCTION=true;

if($PRODUCTION):
	$PAGES = [
		'169846986367382', //CENSIS
		'260688760608386'  //IGP OFICIAL

	];
	$PAGE_CENSIS='169846986367382';
	$PAGE_IGP='260688760608386';

else:
	$PAGES = [
		'2009646039336494'  //TEST
	];
endif;
$HOST='sismos.igp.gob.pe';
$ENDPOINT='http://10.10.1.9/facebook-status';
$APP_ID='151351399762336';
$APP_SECRET='809e05b4859fe43d2b000deda7cfc807';
$GRAPH_VERSION='v9.0';
$ACCESS_PAGE_TOKEN_CENSIS='EAACJpz5rSaABAD0RFNAjLssIMWRgQ4LFLglVibUs0YOH1sjITXhRP21ZBRv6HZA3XhWZAT1tNa8ByKGewQHZBUm1tkijpcaXmq6VpdmgYrESdFwk34u7rkgZCkG9RVPfzMewQErgZBPT0ShA1L8iyKcNSXE0ZAqLZA1jgSat6IRHSg4qs3iplN5G';
$ACCESS_PAGE_TOKEN_IGP='EAACJpz5rSaABAMSFsPR08wkoZBTHBgfEK9VQymgT3NkLXjfz61gOHKThEhdYO3GM3CZCPq3t5XwuTZAzkroRHyUYcQh2XdkrgXAfFpqBMWZC6ZCZC7TzVrxx9PZCbTfQULyCG8OpZAWiQBxrUEsbNiOeGmmDoB4j7YtUENuJ2yGZAswZDZD';
$PERMISSIONS = ['groups_show_list', 'pages_show_list', 'pages_messaging', 'publish_to_groups', 'pages_read_engagement', 'pages_manage_posts', 'public_profile'];
$URL_CALLBACK = "https://sismos.igp.gob.pe/superfacebook/fb-callback.php";

$fb = new Facebook\Facebook([
  'app_id' => $APP_ID,
  'app_secret' => $APP_SECRET,
  'default_graph_version' => $GRAPH_VERSION
]);
?>
