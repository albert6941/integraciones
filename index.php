<?php
$self = $_SERVER['PHP_SELF'];
header("refresh:60; url=$self"); // Refresca cada 60 segundos

include 'wialon/wialon.php';


// accedesmos a el archivo que esta en este directorio para para poder logearnos a wialon 
$wialon_api = new Wialon();
//ponemos el token que nos da alltranport.html 72 caracteres para hacer la conexion 
$token = '921293c514170405a1dc5aaf3916c25c714CC6A94AC5813BEB9FF0B251339BC6782CF0D3';

// si queremos buscar por unidades especificas se ponen las placas de las unidades a buscar
$placas = array('', '');

$result = $wialon_api->login($token); // si el token es correcto se iniciara la sesion con wialon 
$json_response = array();

if (!isset($result['error'])) {
    $param = array(
        'spec'=> array( //Este sub-array define los criterios de búsqueda para la API.
            'itemsType' => 'avl_unit',  //Este parámetro indica que se están buscando unidades AVL (Automatic Vehicle Location)
            'propName' => 'sys_id', // propiedad por la que se filtra 
            'propValueMask' => '*', //se seleccionarán todas las unidades sin importar el valor específico
            'sortType' => 'sys_id', //Especifica que los resultados deben ordenarse por sys_id.
            'propType' => 'customfield'
        ),
        'force' => 1, // fuerza la busqueda en vez de utilizar la cache 
        'from' => 0, // rango de busqueda al ser cero no se aplica un filtro 
        'to' => 0,// rango de busqueda al ser cero no se aplica un filtro 
        'flags' => 4194305 // tipo de acceso que tendremos para buscar la informacion en formato hexa
    );

    //se hace una llamada al metodo de la APi de wialon que nos permite buscar elementos 
    $data = $wialon_api->core_search_items($param); 
    $resultado = json_decode($data, true); // se decodifica el json en un array para php

    //comenzamos con los bucle para buscar las unidades que se encuentran en el array placas
    if (!isset($resultado['error'])) {
        $units_data = array();  //array bacio para almacenar los datos de las unidades encontradas
        $found_units = false; //  se inicializa en false para rastrear si se encuentran unidades específicas.
        
        // se comienza con el recorrido en el array de las placas para localizar las unidades especoficas
        foreach ($resultado['items'] as $unit) {
            // Filtra por los nombres de las unidades
            if (isset($unit['nm']) && in_array($unit['nm'], $placas)) {
                $found_units = true;
                $unit_data = array(
                    'name' => $unit['nm'], // nombre de la placa 
                    'id' => isset($unit['id']) ? $unit['id'] : 'ID no disponible', // id de la unidad 
                    'latitude' => isset($unit['pos']['y']) ? $unit['pos']['y'] : 'No disponible',
                    'longitude' => isset($unit['pos']['x']) ? $unit['pos']['x'] : 'No disponible',
                    'speed' => isset($unit['pos']['s']) ? $unit['pos']['s'] : 'No disponible',
                    'timestamp' => isset($unit['pos']['t']) ? gmdate('Y-m-d\TH:i:s', $unit['pos']['t']) : 'No disponible',
                    // 'additional_info' => $unit // si queremos toda la informacion de la unidad 
                );
                $units_data[] = $unit_data; // se añade al array vacio 
            }
        }

        //si en el apartado de placas el codigo no encuentra unidades, se salta ese paso y llega a este 
        // inicando el reccorrido de todas las unidades 
        if (!$found_units) {
            // Si no se encontraron unidades específicas, agregar todas las unidades
            foreach ($resultado['items'] as $unit) {
                $unit_data = array(
                    'name' => $unit['nm'],
                    'id' => isset($unit['id']) ? $unit['id'] : 'ID no disponible',
                    'latitude' => isset($unit['pos']['y']) ? $unit['pos']['y'] : 'No disponible',
                    'longitude' => isset($unit['pos']['x']) ? $unit['pos']['x'] : 'No disponible',
                    'speed' => isset($unit['pos']['s']) ? $unit['pos']['s'] : 'No disponible',
                    'timestamp' => isset($unit['pos']['t']) ? gmdate('Y-m-d\TH:i:s', $unit['pos']['t']) : 'No disponible',
                    // 'additional_info' => $unit //  si queremos toda la informacion de la unidad 
                );
                $units_data[] = $unit_data; // se añade al array vacio 
            }
        }

        $json_response['units'] = $units_data; // una vez que todo este correcto el array se le asigna a un json para que sea visual 
        $wialon_api->logout(); // cerramos sesion en la api wialon

        // este es el bloque de codigo si hay un error en la busqueda de las unidades muestra este mensaje 
    } else {
        $json_response['error'] = $resultado['error'];
    }
} else {
    $json_response['error'] = $result['error'];
}
// se imprime y se da formato al json y sea legible
header('Content-Type: application/json');
echo json_encode($json_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
