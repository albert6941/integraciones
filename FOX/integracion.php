<?php
$self = $_SERVER['PHP_SELF'];
header("refresh:60; url=$self"); //Refrescamos cada 180 segundos

include '../wialon/wialon.php';

$wialon_api = new Wialon();
$token = '921293c514170405a1dc5aaf3916c25c714CC6A94AC5813BEB9FF0B251339BC6782CF0D3';

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
        'flags' => 4194304
    );

    $data = $wialon_api->core_search_items($param);
    $resultado = json_decode($data, true);

    if(!isset($resultado['error'])) {
        $placas = array(
            'Placa 1' => '56AH7C',
            'Placa 2' => '95AJ2Z',
            'Placa 3' => 'LF84570',
            'Placa 4' => '09AY9P'
        );

        $units_data = array();
        foreach ($resultado['items'] as $index => $unit) {
            $nombre = isset($placas["Placa " . ($index + 1)]) ? $placas["Placa " . ($index + 1)] : 'Nombre no disponible';
            $unit_data = array(
                'name' => $nombre,
                'latitude' => $unit['pos']['y'],
                'longitude' => $unit['pos']['x'],
                'speed' => $unit['pos']['s'],
                'timestamp' => gmdate('Y-m-d\TH:i:s', $unit['pos']['t'])
            );
            $units_data[] = $unit_data;
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
echo json_encode($json_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
