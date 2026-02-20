<?php
    // Fichero xml
    // $xml = "imexalum.xml";
    $xml = $_FILES['alumnos']['tmp_name'] ?? null;

    // Comprobar y cargar el fichero en PHP
    if($xml && file_exists($xml)){
        $ficheroAlumnos = simplexml_load_file($xml);
    }else{
        die("Error: No se ha recibido ningún archivo XML");
    }
    

    // foreach($ficheroAlumnos->alumnos->alumno as $alu){
    //     echo (string)$alu['nombre'] . " " . (string)$alu['apellido1'];
    //     echo  "\n";
    // }
?>