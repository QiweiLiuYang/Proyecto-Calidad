<?php
    // Set the response header to JSON format
    header('Content-Type: application/json');

    // Location of the "actas" folder
    $ruta = "./actas";

    // Open the folder
    $dirActas = opendir($ruta);

    // Get the folder next content
    $entrada = readdir($dirActas);

    // Array to store the content
    $ficheros = [];

    // Loop to fill the array with all the folder valid content
    while($entrada !== false){
        if(str_ends_with($entrada, ".xlsx") || str_ends_with($entrada, ".zip")){
            $ficheros[] = $entrada;
        }
        $entrada = readdir($dirActas);
    }

    // Close the folder
    closedir($dirActas);

    // Sort the array
    rsort($ficheros);

    // Format the elements of the array to be useful for the front side
    $resultado = array_map(function($fichero){
        return "../php/actas/" . $fichero;
    }, $ficheros);

    // Send the response
    echo json_encode($resultado);