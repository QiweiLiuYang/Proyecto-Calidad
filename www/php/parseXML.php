<?php
    // XML file
    $xml = $_FILES['alumnos']['tmp_name'] ?? null;

    // Check and load the XML file
    if($xml && file_exists($xml)){
        $ficheroAlumnos = simplexml_load_file($xml);
    }else{
        die("Error: No se ha recibido ningún archivo XML");
    }
?>