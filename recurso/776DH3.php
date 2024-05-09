<?php 

$self = $_SERVER['PHP_SELF']; //Obtenemos la página en la que nos encontramos
header("refresh:60; url=$self"); //Refrescamos cada 180 segundos
include '../wialon/wialon.php';

$userId = "user_SOS_TAMSA";
$password= "jOfi&/117neZm&&3";
try {
    $soapclient = new SoapClient('http://gps.rcontrol.com.mx/Tracking/wcf/RCService.svc?wsdl');
    $param = array('userId' => $userId, 'password' => $password);
    $response = $soapclient->GetUserToken($param);

    echo '<br><br><br>';
    $array = json_decode(json_encode($response), true);
    //echo '<pre>'; print_r($array); echo '</pre>';

    echo '<br><br><br>';
    echo '<br><br><br>';
    foreach ($array as $item) {
        //echo '<pre>'; var_dump($item);
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
$result = $array;
//echo '<pre>'; print_r($result); echo '</pre>';
$var = $array['GetUserTokenResult']['token'];
//echo '<pre>'; print_r($var); echo '</pre>';

$wialon_api = new Wialon();
  // old username and password login is deprecated, use token login
$token = 'c8c5897e3a64b236485f6ffce95184a857FDEA83EA59A8F54D9F05D5C16C6DADECC86D46';
$result = $wialon_api->login($token);
//echo '<pre>'; print_r($result); echo '</pre>';
$json = json_decode($result, true);
//echo '<pre>'; print_r($json); echo '</pre>';
if(!isset($json['error'])){

	$param = array(

		'spec'=> array(
			'itemsType' => 'avl_unit',
			'propName' => 'sys_id',
			'propValueMask' => '*',
			'sortType' => 'sys_id',
			'propType' => 'customfield'

		),

		'force' => 1,
		'from' => 0,
		'to' => 0,
		'flags' => 4194304

	);

	$data = $wialon_api->core_search_items($param);
	$resultado = json_decode($data, true);
	//echo '<pre>'; print_r($resultado); echo '</pre>';
	if(!isset($resultado['error'])){

		$latitude = $resultado['items'][31]['pos']['y'];
		//echo '<pre>'; print_r($latitude); echo '</pre>';

		$longitude = $resultado['items'][31]['pos']['x'];
		//echo '<pre>'; print_r($longitude); echo '</pre>';

		$velocidad = $resultado['items'][31]['pos']['s'];
		//echo '<pre>'; print_r($velocidad); echo '</pre>';

		$time = gmdate('Y-m-d\TH:i:s', time());
	}

	$wialon_api->logout();
} else {
	echo WialonError::error($json['error']);
}

/* nm  = Dominio*/
/* latitude  = y*/
/* longitud = x*/
/* altitud  = z*/

$token = $var;
$altitude = '0';
$asset = '776DH3';
$battery = '0';
$code = '1';
$course = '0';
$id = '';
$name = '';
$date = $time;
$direction = '0';
$ignition = false;
$latitude = $latitude; // Corregido
$longitude = $longitude;
$odometer = '0';
$serialNumber = '0';
$shipment = '0';
$speed = $velocidad;

try {
    $soapclient = new SoapClient('http://gps.rcontrol.com.mx/Tracking/wcf/RCService.svc?wsdl');
    $parametros = array(
        'token' => $token,
        'events' => array(
            'Event' => array(
                'altitude' => $altitude,
                'asset' => $asset,
                'battery' => $battery,
                'code' => $code,
                'course' => $course,
                'customer' => array('id' => $id, 'name' => $name),
                'date' => $date,
                'direction' => $direction,
                'ignition' => $ignition,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'odometer' => $odometer,
                'serialNumber' => $serialNumber,
                'shipment' => $shipment,
                'speed' => $speed
            )
        )
    );
    $resp = $soapclient->GPSAssetTracking($parametros);

    echo '<br><br><br>';
    $array2 = json_decode(json_encode($resp), true);
    echo '<pre>'; print_r($array2); echo '</pre>';


    echo '<br><br><br>';
    echo '<br><br><br>';
    foreach ($array2 as $item) {
        //echo '<pre>'; var_dump($item);
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>


