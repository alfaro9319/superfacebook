<?php
//header("Access-Control-Allow-Origin: http://10.10.1.9, http://10.10.1.5, https://10.10.1.5");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: text/html; charset=utf-8");

if (!session_id()) { session_start(); }
require_once __DIR__ . '/php-graph-sdk-5.x/vendor/autoload.php';
require_once __DIR__ . '/super-configuracion.php';

$helper = $fb->getRedirectLoginHelper();
if(isset($_GET['state'])) {
    $helper->getPersistentDataHandler()->set('state', $_GET['state']);
}
try {
  $accessToken = $helper->getAccessToken($URL_CALLBACK);
if (! isset($accessToken)):
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
else:
$oAuth2Client = $fb->getOAuth2Client();
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
$tokenMetadata->validateAppId($APP_ID); // Replace {app-id} with your app id
$tokenMetadata->validateExpiration();
	if (! $accessToken->isLongLived()) {
    		$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
	}

$_SESSION['facebook_access_token'] = (string) $accessToken;

endif;
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  //exit;//
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  //exit;//
}
header('Location: ./mensaje.php');
exit;

?>
