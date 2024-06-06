<?php
$self = htmlspecialchars($_SERVER['PHP_SELF']);
header("refresh:60; url=$self"); // Refresca cada 60 segundos

// Cargar la API de Wialon
include 'wialon/wialon.php';

$wialon_api = new Wialon();

// Leer el token desde una variable de entorno
$token = getenv('WIALON_TOKEN') ?: 'ruta_a_tu_archivo_seguro';
if (!$token) {
    die(json_encode(['error' => 'Token no disponible']));
}

// Validar y sanitizar las placas (aunque en este caso parece que son estáticas)
$placas = array_map('htmlspecialchars', ['', '']);

$result = $wialon_api->login($token); // Iniciar sesión en Wialon
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
                    'name' => htmlspecialchars($unit['nm']),
                    'id' => isset($unit['id']) ? htmlspecialchars($unit['id']) : 'ID no disponible',
                    'latitude' => isset($unit['pos']['y']) ? htmlspecialchars($unit['pos']['y']) : 'No disponible',
                    'longitude' => isset($unit['pos']['x']) ? htmlspecialchars($unit['pos']['x']) : 'No disponible',
                    'speed' => isset($unit['pos']['s']) ? htmlspecialchars($unit['pos']['s']) : 'No disponible',
                    'timestamp' => isset($unit['pos']['t']) ? gmdate('Y-m-d\TH:i:s', $unit['pos']['t']) : 'No disponible'
                );
                $units_data[] = $unit_data;
            }
        }

        if (!$found_units) {
            foreach ($resultado['items'] as $unit) {
                $unit_data = array(
                    'name' => htmlspecialchars($unit['nm']),
                    'id' => isset($unit['id']) ? htmlspecialchars($unit['id']) : 'ID no disponible',
                    'latitude' => isset($unit['pos']['y']) ? htmlspecialchars($unit['pos']['y']) : 'No disponible',
                    'longitude' => isset($unit['pos']['x']) ? htmlspecialchars($unit['pos']['x']) : 'No disponible',
                    'speed' => isset($unit['pos']['s']) ? htmlspecialchars($unit['pos']['s']) : 'No disponible',
                    'timestamp' => isset($unit['pos']['t']) ? gmdate('Y-m-d\TH:i:s', $unit['pos']['t']) : 'No disponible'
                );
                $units_data[] = $unit_data;
            }
        }

        $json_response['units'] = $units_data;
        $wialon_api->logout();
    } else {
        $json_response['error'] = $resultado['error'];
    }
} else {
    $json_response['error'] = $result['error'];
}

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Content-Security-Policy: default-src \'self\'');

echo json_encode($json_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>

