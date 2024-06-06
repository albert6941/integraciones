<?php
$self = $_SERVER['PHP_SELF'];
header("refresh:60; url=$self"); // Refresca cada 60 segundos

// Carga segura del archivo de configuración
require_once 'config.php'; 

include 'wialon/wialon.php';

// Verifica que el token esté definido en el archivo de configuración
if (!defined('WIALON_TOKENABC') || empty(WIALON_TOKENABC)) {
    die(json_encode(['error' => 'Token no definido.']));
}

$wialon_api = new Wialon();
$token = WIALON_TOKENABC;

// Si las placas están vacías, se ignoran
$placas = array_filter(['', '']);

$result = $wialon_api->login($token);
$json_response = array();

if (!isset($result['error'])) {
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

    $data = $wialon_api->core_search_items($param);
    $resultado = json_decode($data, true);

    if (!isset($resultado['error'])) {
        $units_data = array();
        $found_units = false;

        foreach ($resultado['items'] as $unit) {
            if (isset($unit['nm']) && in_array($unit['nm'], $placas)) {
                $found_units = true;
                $unit_data = array(
                    'name' => $unit['nm'],
                    'id' => isset($unit['id']) ? $unit['id'] : 'ID no disponible',
                    'latitude' => isset($unit['pos']['y']) ? $unit['pos']['y'] : 'No disponible',
                    'longitude' => isset($unit['pos']['x']) ? $unit['pos']['x'] : 'No disponible',
                    'speed' => isset($unit['pos']['s']) ? $unit['pos']['s'] : 'No disponible',
                    'timestamp' => isset($unit['pos']['t']) ? gmdate('Y-m-d\TH:i:s', $unit['pos']['t']) : 'No disponible'
                );
                $units_data[] = $unit_data;
            }
        }

        if (!$found_units) {
            foreach ($resultado['items'] as $unit) {
                $unit_data = array(
                    'name' => $unit['nm'],
                    'id' => isset($unit['id']) ? $unit['id'] : 'ID no disponible',
                    'latitude' => isset($unit['pos']['y']) ? $unit['pos']['y'] : 'No disponible',
                    'longitude' => isset($unit['pos']['x']) ? $unit['pos']['x'] : 'No disponible',
                    'speed' => isset($unit['pos']['s']) ? $unit['pos']['s'] : 'No disponible',
                    'timestamp' => isset($unit['pos']['t']) ? gmdate('Y-m-d\TH:i:s', $unit['pos']['t']) : 'No disponible'
                );
                $units_data[] = $unit_data;
            }
        }

        $json_response['unidades'] = $units_data;
        $wialon_api->logout();
    } else {
        $json_response['error'] = $resultado['error'];
    }
} else {
    $json_response['error'] = $result['error'];
}

header('Content-Type: application/json');
echo json_encode($json_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>
