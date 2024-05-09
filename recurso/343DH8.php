<?php 

$self = $_SERVER['PHP_SELF']; //Obtenemos la página en la que nos encontramos
header("refresh:60; url=$self"); //Refrescamos cada 180 segundos
include '../wialon/wialon.php';

//USUARIO Y CONTRASEÑA PARA OBTENER EL TOKEN DE RECURSO CONFIABLE 
$userId = "user_SOS_TAMSA";
$password= "jOfi&/117neZm&&3";

// INICIA LA CONEXION CON RECURSO CONFIABLE PARA OBTENER EL TOEKN 
try {
    $soapclient = new SoapClient('http://gps.rcontrol.com.mx/Tracking/wcf/RCService.svc?wsdl');
    $param = array('userId' => $userId, 'password' => $password);
    $response = $soapclient->GetUserToken($param);//UTILIZAMOS EEVENTO GET USERTOKEN PARA OBTENER EL TOKEN

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
$var = $array['GetUserTokenResult']['token']; //SE OBTINE EL TOKEN Y SE GUARDA EN LA VARIABLE VAR PARA SER UTILIZADA DESPUES 
//echo '<pre>'; print_r($var); echo '</pre>';

//INICIA LA CONTENXION CON WIALON PARA OBTENER LA LATITUD, LONGITUD Y VELOCIDAD DE UNIDADES MEDIANTE EL TOKEN OBETNIDO DE WAILON 

$wialon_api = new Wialon();// SE LLAMA A LA API DE WAILON PARA COMENZAR LA CONEXION 

$token = 'c8c5897e3a64b236485f6ffce95184a8603F5892C277F78E16BB07F1FE63E8B714E5F6C0'; //token obtenido en la pagina de alltranspor/login.html 
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
 // SE COMIENZA CON LA BUSQUEDA DE LOS ITEMS  SOLICITADOS  PARA QUE SEAN EN TIEMPO REAL Y NO PONER LOS PARAMETROS QUE NOSOSTROS ESCRIBAMOS 
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

		$time = gmdate('Y-m-d\TH:i:s', time()); // SE OBETIENE LA FECHA Y HORA EN FORMATO UTC 0 
	}

	$wialon_api->logout(); // SE CIERRA SESION CON WAILON API 
} else {
	echo WialonError::error($json['error']); // SI HAY ERROR SE IMPRIME EL ERROR 
}

/* nm  = Dominio*/
/* latitude  = y*/
/* longitud = x*/
/* altitud  = z*/

// SE COMIENZA CON EL LLENADO DE INFORMACION SOLICITADA Y SIGUENDO LA DOCUMENTACION TECNICA EN PDF 

$token = $var; //SE PONE EL TOKEN DE RECURSO CONFIABLE 
$altitude = '0';
$asset = '343DH8'; // PLACA DE EL VEHICULO 
$battery = '0';
$code = '1';
$course = '0';
$id = ''; // SI SE REUIERE COSTUMER ID 
$name = ''; // SE SE REQUIERE EL CUSTUMER NAME ES DECIR TODO LO DE EL CLIENTE 
$date = $time; // SE PONE LA VARIABE TIME DECLARADA ARRIBA PARA QUE EXTRAIGA INFORMACION EN TIEMPO REAL Y EN EL FORMATO SOLICITADO 
$direction = '0';
$ignition = false;
$latitude = $latitude; // Corregido
$longitude = $longitude;
$odometer = '0';
$serialNumber = '0';
$shipment = '0';
$speed = $velocidad;

	//SE COMIENZA CON LA IMPRESION DE LOS DATOS Y MANDANDOLOS A RC DIRECTO A PRODUCCION AQUI VAN LOS NOMBRES DE LAS VARIBLES DECLARADAS ARRIBA
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
    $resp = $soapclient->GPSAssetTracking($parametros); // ESPERAMOS LA RESPUESTA USANDO EL EVENTO GPSASSET 

    echo '<br><br><br>';
    $array2 = json_decode(json_encode($resp), true); // IMPROMIME LA RESPUESTA SI HAY ERROR SE VERIFICA DONDE ESTA EL ERROR
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


