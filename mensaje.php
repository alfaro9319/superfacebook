<?php
//header("Access-Control-Allow-Origin: http://10.10.1.9,http:10.10.1.5,https://10.10.1.5");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: text/html; charset=utf-8");
if (!session_id()) { session_start(); }
require_once __DIR__ . '/php-graph-sdk-5.x/vendor/autoload.php'; // change path as needed
require_once __DIR__ . '/php-graph-sdk-5.x/src/Facebook/autoload.php';
require_once __DIR__ . '/super-configuracion.php';
$helper = $fb->getRedirectLoginHelper();

if (isset($_GET['state'])) {
    $helper->getPersistentDataHandler()->set('state', $_GET['state']);
}

//$loginUrl = $helper->getLoginUrl($URL_CALLBACK, $PERMISSIONS);
$_SESSION['facebook_access_token'] = (string) $ACCESS_PAGE_TOKEN_CENSIS;

if(isset($_SESSION['facebook_access_token'])):
	if(!isset($_GET['mensaje'])){
		header("Location: https://".$HOST."/status/15e475c91ef0f15f89d8f9c97f68c4c451598f4bd7d0a9ce34b2e7003a6f452d/203");
		exit;
	}
	$texto="";
	if($_GET["tiporeporte"]=="S"){
	$texto.="SIMULACRO
";
	$texto.=$_GET["mensaje"];

	}else{
	$texto.=$_GET["mensaje"];
	//$texto.="Mapa: http://ultimosismo.igp.gob.pe/mapa/".trim($_GET["latitud"])."/".trim($_GET["longitud"]);	
	$texto.= "https://www.igp.gob.pe/servicios/centro-sismologico-nacional/evento/".substr($texto,14,9);
	}

	$fb->setDefaultAccessToken( $_SESSION['facebook_access_token'] );
	try {
	  $response = $fb->get('/me');
	  $userNode = $response->getGraphUser();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	    // When Graph returns an error
		header("Location: https://".$HOST."/status/15e475c91ef0f15f89d8f9c97f68c4c451598f4bd7d0a9ce34b2e7003a6f452d/".$e->getCode());
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	    // When validation fails or other local issues
		header("Location: https://".$HOST."/status/15e475c91ef0f15f89d8f9c97f68c4c451598f4bd7d0a9ce34b2e7003a6f452d/".$e->getCode());
		exit;
	}


	$status_response = array();

	try {
		$response = $fb->post(
			'/'.$PAGE_CENSIS.'/feed',
			array("message" => $texto),
			$ACCESS_PAGE_TOKEN_CENSIS
		);
		$postId = $response->getGraphNode();
		array_push($status_response,200);


		$response = $fb->post(
			'/'.$PAGE_IGP.'/feed',
			array("message" => $texto),
			$ACCESS_PAGE_TOKEN_IGP
		);
		$postId = $response->getGraphNode();
		array_push($status_response,200);








	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		if($e->getCode()==506){
		      array_push($status_response,200);
		}else{
		      array_push($status_response,$e->getCode());
		}
  	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	     array_push($status_response,$e->getCode());
 	}

	$codes = implode(",",$status_response);
	$code = ("200,200"==$codes)?strstr($codes,",",true):$codes;
       	header("Location: https://".$HOST."/status/15e475c91ef0f15f89d8f9c97f68c4c451598f4bd7d0a9ce34b2e7003a6f452d/".$code);
	exit;
else:
	header("Location: ".$loginUrl);
	exit();
endif;

?>
