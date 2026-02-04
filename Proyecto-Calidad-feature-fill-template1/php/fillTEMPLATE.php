<?php
    require "vendor/autoload.php";
    require_once "parseXLSX.php";
    //Modificado por Vicky
    require 'parseXML.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;


    $ficheroAlumnos = cargarAlumnosXML();

    // Hacer copia de la plantilla original para trabajar sobre ella, por si a mitad de proceso
    // ocurre un error, que no se nos corrompa el original. Al acabar, se eliminará la plantilla copia.
    // Tendra como nombre, la fecha actual
    $plantillaOriginal = "plantilla.xlsx";
    $plantillaCopia = "temp/report_" . (new DateTime())->format("d-m-Y_H-i-s-v") . ".xlsx";
    copy($plantillaOriginal, $plantillaCopia);

    // Cargar plantilla en memoria 
    $doc = IOFactory::load($plantillaCopia);

    // Rellenado de las celdas de la hoja 0
    function rellenarHoja0($doc, $datos0, $datos1){
        // Abrir la primera pestaña del Excel
        $hoja0 = $doc->getSheet(0);

        $hoja0->setCellValue("G3", $datos1[39]['grup']);
        $hoja0->setCellValue("G4", $datos1[39]['tutor']);
        $inicioRellenado = 13;
        foreach($datos0 as $profesor){
            // print_r($profesor['grup']);
            // print_r($datos1[39]);
            //echo $profesor['grup'] . " " . $datos1[39]['grup'] . "   ";
            if(str_contains($profesor['grup'], $datos1[39]['grup'])){
                $hoja0->setCellValue("B" . $inicioRellenado, $profesor['mdas']);
                $hoja0->setCellValue("C" . $inicioRellenado, $profesor['prof']);
                $inicioRellenado++;
            }
        }
    }
    
    // Rellenado de las celdas de la hoja 3
    function rellenarHoja2($doc, $ficheroAlumnos, $datos1){
        // Abrir la tercera pestaña del Excel
        $hoja2 = $doc->getSheet(2);

        // Array que contendrá a los alumnos del grupo
        $grupo = [];

        // Curso al que va (primero o segundo)
        $curso = $datos1[39]['grup'][0]/2 == 0 ? "1" : "2";

        // Formateado del nombre del curso para buscarlo en el fichero de alumnos
        $nombreGrupo = strtoupper(trim(substr($datos1[39]['grup'], 1)));
        
        // Bucle que recorre la lista de alumnos
        foreach($ficheroAlumnos->alumnos->alumno as $alu){
            // If para ver si está en primero o segundo y en que grupo
            if(str_contains($alu['grupo'], $curso) && str_contains((string)$alu['grupo'], $nombreGrupo)){
                $grupo[] = [
                    "NIA" => (string)$alu['NIA'],
                    "apellido1" => (string)$alu['apellido1'],
                    "apellido2" => (string)$alu['apellido2'],
                    "nombre" => (string)$alu['nombre'],
                    "nuss" => (int)$alu['nuss'],
                    "repite" => (int)$alu['repite'],
                    "informe_medico" => (string)$alu['informe_medico']
                ];
            }
        }

        // Eliminamos duplicados porque el fichero xml contiene duplicados
        $grupo = array_values(array_unique($grupo, SORT_REGULAR));

        // Orden alfabético en español
        $collator = collator_create('es_ES');
        // Ordenar el array según apellido1, apellido2 y nombre
        usort($grupo, function($a, $b) use ($collator){
            $comparar = collator_compare($collator, $a['apellido1'], $b['apellido1']);
            if($comparar == 0){
                $comparar = collator_compare($collator, $a['apellido2'], $b['apellido2']);
            }
            if($comparar == 0){
                $comparar = collator_compare($collator, $a['nombre'], $b['nombre']);
            }

            return $comparar;
        });
        // print_r($grupo);

        $inicioRellenado = 36;
        foreach($grupo as $alu){
            $nombre = "{$alu['apellido1']} {$alu['apellido2']}, {$alu['nombre']}";
            $hoja2->setCellValue("B" . $inicioRellenado, $alu['NIA']);
            $hoja2->setCellValue("C" . $inicioRellenado, $nombre);
            $hoja2->setCellValue("E" . $inicioRellenado, $alu['nuss']);
            if($alu['informe_medico'] == "N") $hoja2->setCellValue("H" . $inicioRellenado, "NO");
            else $hoja2->setCellValue("H" . $inicioRellenado, "SI");
            if((int)$alu['repite'] > 0) $hoja2->setCellValue("J" . $inicioRellenado, "SI");
            else $hoja2->setCellValue("J" . $inicioRellenado, "NO");
            $inicioRellenado++;
        }
    }


    //By: Victoria
    // Rellenado de las celdas de la hoja ficha_alum
function rellenarFichaAlum($doc, $ficheroAlumnos, $datos1){
    // Abrir la hoja ficha_alum (para referencia futuro posicion 12)
    $fichaAlum = $doc->getSheet(12);

    // Array que contendrá a los alumnos del grupo
    $grupo = [];

    // Curso al que va 
    $curso = $datos1[39]['grup'][0]/2 == 0 ? "1" : "2";

    // Formateado del nombre del curso para buscarlo en el fichero de alumnos
    $nombreGrupo = strtoupper(trim(substr($datos1[39]['grup'], 1)));
    
    // Bucle que recorre la lista de alumnos
    foreach($ficheroAlumnos->alumnos->alumno as $alu){
        // If para ver si está en primero o segundo y en que grupo
        if(str_contains($alu['grupo'], $curso) && str_contains((string)$alu['grupo'], $nombreGrupo)){
            $grupo[] = [
                "NIA" => (string)$alu['NIA'],
                "nombre" => (string)$alu['nombre'],
                "apellido1" => (string)$alu['apellido1'],
                "apellido2" => (string)$alu['apellido2'],
                "fecha_nac" => (string)$alu['fecha_nac'],
                "sexo" => (string)$alu['sexo'],
                "documento" => (string)$alu['documento'],
                "domicilio" => (string)$alu['domicilio'],
                "numero" => (string)$alu['numero'],
                "puerta" => (string)$alu['puerta'],
                "cod_postal" => (string)$alu['cod_postal'],
                "telefono1" => (string)$alu['telefono1'],
                "telefono2" => (string)$alu['telefono2'],
                "email1" => (string)$alu['email1'],
                "email2" => (string)$alu['email2'],
                "ensenanza" => (string)$alu['ensenanza'],
                "curso" => (string)$alu['curso'],
                "grupo" => (string)$alu['grupo'],
                "turno" => (string)$alu['turno'],
                "repite" => (string)$alu['repite']
            ];
        }
    }

    // Eliminamos duplicados porque el fichero xml contiene duplicados
    $grupo = array_values(array_unique($grupo, SORT_REGULAR));

    // Orden alfabético en español
    $collator = collator_create('es_ES');


    // Ordenar el array según apellido1, apellido2 y nombre
    usort($grupo, function($a, $b) use ($collator){
        $comparar = collator_compare($collator, $a['apellido1'], $b['apellido1']);
        if($comparar == 0){
            $comparar = collator_compare($collator, $a['apellido2'], $b['apellido2']);
        }
        if($comparar == 0){
            $comparar = collator_compare($collator, $a['nombre'], $b['nombre']);
        }

        return $comparar;
    });

    $inicioRellenado = 9;
    foreach($grupo as $alu){
        $fichaAlum->setCellValue("B" . $inicioRellenado, $alu['NIA']);
        $fichaAlum->setCellValue("C" . $inicioRellenado, $alu['nombre']);
        $fichaAlum->setCellValue("D" . $inicioRellenado, $alu['apellido1']);
        $fichaAlum->setCellValue("E" . $inicioRellenado, $alu['apellido2']);
        $fichaAlum->setCellValue("F" . $inicioRellenado, $alu['fecha_nac']);
        $fichaAlum->setCellValue("G" . $inicioRellenado, $alu['sexo']);
        $fichaAlum->setCellValue("H" . $inicioRellenado, $alu['documento']);
        $fichaAlum->setCellValue("I" . $inicioRellenado, $alu['domicilio']);
        $fichaAlum->setCellValue("J" . $inicioRellenado, $alu['numero']);
        $fichaAlum->setCellValue("K" . $inicioRellenado, $alu['puerta']);
        $fichaAlum->setCellValue("L" . $inicioRellenado, $alu['cod_postal']);
        $fichaAlum->setCellValue("M" . $inicioRellenado, $alu['telefono1']);
        $fichaAlum->setCellValue("N" . $inicioRellenado, $alu['telefono2']);
        $fichaAlum->setCellValue("O" . $inicioRellenado, $alu['email1']);
        $fichaAlum->setCellValue("P" . $inicioRellenado, $alu['email2']);
        $fichaAlum->setCellValue("Q" . $inicioRellenado, $alu['ensenanza']);
        $fichaAlum->setCellValue("R" . $inicioRellenado, $alu['curso']);
        $fichaAlum->setCellValue("S" . $inicioRellenado, $alu['grupo']);
        $fichaAlum->setCellValue("T" . $inicioRellenado, $alu['turno']);
        $fichaAlum->setCellValue("U" . $inicioRellenado, $alu['repite']);
        $inicioRellenado++;
    }
    /* By: Victoria */
}



    rellenarHoja0($doc, $datos0, $datos1);
    rellenarHoja2($doc, $ficheroAlumnos, $datos1);
    //By: Victoria | Rellenar la hoja de Alumno
    rellenarFichaAlum($doc, $ficheroAlumnos, $datos1);


    $guardar = IOFactory::createWriter($doc, "Xlsx");
    try {
        $guardar->save($plantillaCopia);
        
    } catch (Exception $e) {
        echo "Error al guardar: " . $e->getMessage();
        unlink($plantillaCopia);
    }
?>