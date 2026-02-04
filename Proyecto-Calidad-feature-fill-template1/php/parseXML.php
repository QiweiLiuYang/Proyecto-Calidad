
<?php
/* Made by: Victoria */

/* Incluye el archivo autoload de Composer para cargar automáticamente todas las dependencias
del proyecto (librerías de terceros)*/

//require 'vendor/autoload.php';

function cargarAlumnosXML() {
    $xmlArchivo = "imexalum.xml";

    if (!file_exists($xmlArchivo)) {
        die("No se encuentra el archivo XML");
    }

    $xml = simplexml_load_file($xmlArchivo);

    if ($xml === false) {
        die("Error cargando el XML");
    }

    return $xml;

    
}


?> 