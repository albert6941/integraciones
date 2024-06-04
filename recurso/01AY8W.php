<?php

$self = $_SERVER['PHP_SELF'];
header("refresh:500; url=$self"); //Refrescamos cada 180 segundos

include '../wialon/wialon.php';

$userId = "ws_avl_alltran";
$password= "PZZa*405Caaw_2";

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

//INICIA LA CONTENXION CON WIALON PARA OBTENER LA LATITUD, LONGITUD Y VELOCIDAD DE UNIDADES MEDIANTE EL TOKEN OBETNIDO DE WIALON 

$wialon_api = new Wialon();// SE LLAMA A LA API DE WAILON PARA COMENZAR LA CONEXION 

$token = 'c8c5897e3a64b236485f6ffce95184a8603F5892C277F78E16BB07F1FE63E8B714E5F6C0';

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
        'flags' => 4194305
    );

    // SE COMIENZA CON LA BUSQUEDA DE LOS ITEMS  SOLICITADOS  PARA QUE SEAN EN TIEMPO REAL Y NO PONER LOS PARAMETROS QUE NOSOTROS ESCRIBAMOS 
    $data = $wialon_api->core_search_items($param);
    $resultado = json_decode($data, true);
    //echo '<pre>'; print_r($resultado); echo '</pre>';
    if(!isset($resultado['error'])){

        // Función para buscar el vehículo por su identificador de activo
        function buscarActivoPorId($items, $asset) {
            foreach ($items as $item) {
                if ($item['nm'] === $asset) {
                    return $item;
                }
            }
            return null;
        }

        $asset = '01AY8W';
        $vehiculo = buscarActivoPorId($resultado['items'], $asset);

        if ($vehiculo) {
            
            $latitude = $vehiculo['pos']['y'];
            $longitude = $vehiculo['pos']['x'];
            $velocidad = $vehiculo['pos']['s'];
            $placa = $vehiculo['nm']; // Aquí se obtiene la placa
            $time = gmdate('Y-m-d\TH:i:s', time()); // SE OBTIENE LA FECHA Y HORA EN FORMATO UTC 0

            // // Crear un array con todos los datos de la unidad
            // $datosUnidad = array(
            //     // 'Placa' => $placa,
            //     // 'Latitud' => $latitude,
            //     // 'Longitud' => $longitude,
            //     // 'Velocidad' => $velocidad,
            //     // 'Hora' => $time,
            //     'OtrosDatos' => $vehiculo // Incluye todos los demás datos del vehículo
            // );
            
            // // Imprimir los datos de la unidad de manera estructurada
            // echo '<h3>Datos de la Unidad</h3>';
            // echo '<pre>'; print_r($datosUnidad); echo '</pre>';
        
        } else {
            // Manejar el caso donde el vehículo no se encuentra
            echo "Vehículo con identificador $asset no encontrado.";
        }

    }

    $wialon_api->logout(); // SE CIERRA SESION CON WAILON API 
} else {
    echo WialonError::error($json['error']); // SI HAY ERROR SE IMPRIME EL ERROR 
}

/* nm  = Dominio*/
/* latitude  = y*/
/* longitud = x*/
/* altitud  = z*/

$token = $var;
$altitude = '0';
// $asset = '01AY8W';
$battery = '0';
$code = '1';
$course = '0';
$id = '41013';
$name = 'FREDDY VERA GARCIA';
$date = $time;
$direction = '0';
$ignition = false;
$latitude = $latitude; // Corregido
$longitude = $longitude;
$odometer = '0';
$serialNumber = '0';
$shipment = '19';
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
