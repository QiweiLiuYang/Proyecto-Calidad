<?php
    // Fichero xml
    $xml = "imexalum.xml";

    // Cargar el fichero en PHP
    $ficheroAlumnos = simplexml_load_file($xml);

    // foreach($ficheroAlumnos->alumnos->alumno as $alu){
    //     echo (string)$alu['nombre'] . " " . (string)$alu['apellido1'];
    //     echo  "\n";
    // }
?>