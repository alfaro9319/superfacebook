<?php
/*el archivo index.php es usado por el antiguo sistema de publicación*/
if (!session_id()) { session_start(); }

require_once __DIR__ . '/php-graph-sdk-5.x/vendor/autoload.php'; // change path as needed
require_once __DIR__ . '/php-graph-sdk-5.x/src/Facebook/autoload.php';
require_once './super-configuracion.php';

header('Content-Type: text/html; charset=utf-8');
$super_url = "https://sismos.igp.gob.pe/superfacebook/old-facebook-connect-callback.php";
$fb = new Facebook\Facebook([
  'app_id' => $APP_ID,
  'app_secret' => $APP_SECRET,
  'default_graph_version' => $GRAPH_VERSION,
]);
$helper = $fb->getRedirectLoginHelper();
if (isset($_GET['state'])) {
    $helper->getPersistentDataHandler()->set('state', $_GET['state']);
}
$loginUrl = $helper->getLoginUrl($super_url, $PERMISSIONS);
if(isset($_SESSION['facebook_access_token'])){
	if(!isset($_POST['latitud'])){
		echo 'no se ha publicado en fb, envíe nuevamente los parámetros';
		exit;
	}
	if(strpos($_POST["referencia"], ",")){
		$referencia = substr_replace($_POST["referencia"],'',strpos($_POST["referencia"], ","),strlen($_POST["referencia"]));
	}else{
		$referencia = $_POST["referencia"];
	}
	//Calculamos las fechas y horas en UTC
	list($day,$month,$year)=explode("/", $_POST["fecha"]);
	$fecha_UTC=date("d/m/Y", strtotime("$year-$month-$day"." ".$_POST["hora"])+5*3600);
	$hora_UTC=date("H:i:s", strtotime("$year-$month-$day"." ".$_POST["hora"])+5*3600) ;
	$ccc="
";
//	$ccc=" %0A";
	$texto.=(isset($_POST["report_type"]) && $_POST["report_type"]=="S")?"SIMULACRO".$ccc:"";
	$texto.=(isset($_POST["report_type"]) && strtoupper(trim($_POST["report_type"]))!="S")?"IGP/CENSIS/RS ".$year."-".str_pad($_POST["correlativo"],4,"0",STR_PAD_LEFT).$ccc:"";
	$texto.="Fecha y Hora Local: ".$_POST["fecha"]." ".$_POST["hora"].$ccc;
	$texto.="Fecha y Hora UTC: ".$fecha_UTC." ".$hora_UTC.$ccc;
	$texto.="Magnitud: ".round($_POST['magnitud_valor'],1).$ccc;
	$texto.="Profundidad: ".round($_POST['profundidad_valor'])." km".$ccc;
	$texto.="Latitud: ".$_POST['latitud'].$ccc;
	$texto.="Longitud: ".$_POST['longitud'].$ccc;

	if(strlen(trim($_POST["intensidad"]))>0){
		$texto.="Intensidad: ".$_POST["intensidad"].$ccc;
	}
	$texto.="Referencia: ".$referencia.$ccc;
	$texto.="Mapa: http://ultimosismo.igp.gob.pe/mapa/".trim($_POST["latitud"])."/".trim($_POST["longitud"]);
	// Sets the default fallback access token so we don't have to pass it to each request


	$fb->setDefaultAccessToken( $_SESSION['facebook_access_token'] );
	try {
	  $response = $fb->get('/me');
	  $userNode = $response->getGraphUser();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	  // When Graph returns an error
	  echo 'Graph returned an error: ' . $e->getMessage();
	  exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	  // When validation fails or other local issues
	  echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  exit;
	}


	$response = $fb->get('/me/accounts');

	foreach ($response->getDecodedBody() as $allAccounts):
	    foreach ($allAccounts as $account ):
	      	if(isset($account['id'])):
	        	if ( in_array($account['id'],$PAGES)):
	            	    $appAccessToken = $account['access_token'];
			    try {
			        $response = $fb->post(
	        		  '/'.$account['id'].'/feed',
			              array(
			              	"message" => $texto,
	        		      ),
			            $appAccessToken
			        );
			        // Success
			        $postId = $response->getGraphNode();
			        echo '<p><b>Resultado:</b> Publicado en la página <b>'.$account['name'].'</b>!<br><b>Code:100</b></p>';
			    } catch(Facebook\Exceptions\FacebookResponseException $e) {
			        // When Graph returns an error
				if($e->getCode()==506){
				  echo '<p> Facebook no permite publicar mensajes duplicados.<br>La anterior publicación es identica a la de ahora.</p>';
				}else{
			        echo '<p>.Error de Graph: ' . $e->getMessage().'</p>';
				}
			    } catch(Facebook\Exceptions\FacebookSDKException $e) {
			      	// When validation fails or other local issues
			        if($e->getMessage()==""){
	         		    echo '<html><head><script src="jquery-1.10.2.js"></script></head><body>';
                		    echo '<div style="padding:15px;background-color:ff8099"><p>Advertencia: posible error</p></div>';
        	        	    echo '<script>function setStatus(){$.post("'.$ENDPOINT.'",{code:200});}setStatus();</script></body></html>';
	        	        }else{
	         		    echo '<html><head><script src="jquery-1.10.2.js"></script></head><body>';
        	        	    echo '<div style="padding:15px;background-color:ff8099"><p>SDK error: ' . $e->getMessage().'</p></div>';
	                	    echo '<script>function setStatus(){$.post("'.$ENDPOINT.'",{code:202});} setStatus();</script></body></html>';
		                }
                            }
	        	endif;
	      	endif;
	    endforeach;
	endforeach;

}else{
	header("Location: ".$loginUrl);
	exit;
	
}

?>
