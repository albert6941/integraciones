<?php
// Archivos PHP a ejecutar simultáneamente
$archivos = array(
    'recurso/01AY8W.php',
    'recurso/25BDN9N.php',
    'recurso/643AJ7.php',
    'recurso/776DH3.php'
);

// Ejecutar cada archivo PHP en segundo plano
foreach ($archivos as $archivo) {
    $comando = 'nohup php ' . $archivo . ' > /dev/null 2>&1 &';
    exec($comando);
}

// Este mensaje se mostrará después de iniciar los scripts en segundo plano
echo "Bienbenidos al api.";

?>
