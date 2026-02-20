<?php
    header('Content-Type: application/json');
    $ruta = "./actas";
    $dirActas = opendir($ruta);
    $entrada = readdir($dirActas);
    $ficheros = [];
    while($entrada !== false){
        if(str_ends_with($entrada, ".xlsx")){
            $ficheros[] = $entrada;
        }
        $entrada = readdir($dirActas);
    }
    closedir($dirActas);

    rsort($ficheros);

    $resultado = array_map(function($fichero){
        return "../php/actas/" . $fichero;
    }, $ficheros);

    echo json_encode($resultado);
?>