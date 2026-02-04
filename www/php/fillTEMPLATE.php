<?php
    require "vendor/autoload.php";
    require_once "parseXLSX.php";
    require_once "parseXML.php";
    use PhpOffice\PhpSpreadsheet\IOFactory;

    // Hacer copia de la plantilla original para trabajar sobre ella, por si a mitad de proceso
    // ocurre un error, que no se nos corrompa el original. Al acabar, se eliminará la plantilla copia.
    // Tendra como nombre, la fecha actual
    $plantillaOriginal = "plantilla.xlsx";
    $plantillaCopia = "temp/report_" . (new DateTime())->format("d-m-Y_H-i-s-v") . ".xlsx";
    copy($plantillaOriginal, $plantillaCopia);

    // Cargar plantilla en memoria 
    $doc = IOFactory::load($plantillaCopia);

    // Rellenado de las celdas de la hoja 0
    function rellenarHoja0($doc, $datos0, $datos1, $tutor){
        // Abrir la primera pestaña del Excel
        $hoja0 = $doc->getSheet(0);

        $hoja0->setCellValue("G3", $datos1[$tutor]['grup']);
        $hoja0->setCellValue("G4", $datos1[$tutor]['tutor']);
        $inicioRellenado = 13;
        foreach($datos0 as $profesor){
            // print_r($profesor['grup']);
            // print_r($datos1[39]);
            //echo $profesor['grup'] . " " . $datos1[39]['grup'] . "   ";
            if(str_contains($profesor['grup'], $datos1[$tutor]['grup'])){
                $hoja0->setCellValue("B" . $inicioRellenado, $profesor['mdas']);
                $hoja0->setCellValue("C" . $inicioRellenado, $profesor['prof']);
                $inicioRellenado++;
            }
        }
    }
    
    // Rellenado de las celdas de la hoja 3
    function rellenarHoja2($doc, $ficheroAlumnos, $datos1, $tutor){
        // Abrir la tercera pestaña del Excel
        $hoja2 = $doc->getSheet(2);

        // Array que contendrá a los alumnos del grupo
        $grupo = [];

        // Curso al que va (primero o segundo)
        $curso = $datos1[$tutor]['grup'][0]/2 == 0 ? "1" : "2";

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
                    "repite" => (int)$alu['repite']
                ];
            }
        }

        // Eliminamos duplicados porque el fichero xml contiene duplicados
        $grupoTemp = [];
        foreach($grupo as $alu){
            $grupoTemp[$alu['NIA']] = $alu;
        }
        $grupo = array_values($grupoTemp);

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

        // Desde donde empieza la celda de los alumnos
        $inicioRellenado = 36;
        
        // Bucle para iniciar el rellenado de cada fila con sus datos
        foreach($grupo as $alu){
            $nombre = "{$alu['apellido1']} {$alu['apellido2']}, {$alu['nombre']}";
            $hoja2->setCellValue("B" . $inicioRellenado, $alu['NIA']);
            $hoja2->setCellValue("C" . $inicioRellenado, $nombre);
            $hoja2->setCellValue("E" . $inicioRellenado, $alu['nuss'] ?? "no");
            if((int)$alu['repite'] > 0) $hoja2->setCellValue("J" . $inicioRellenado, "Si");
            else $hoja2->setCellValue("J" . $inicioRellenado, "No");
            $inicioRellenado++;
        }
    }

    // Llamada a las funciones
    rellenarHoja0($doc, $datos0, $datos1, 39);
    rellenarHoja2($doc, $ficheroAlumnos, $datos1, 39);

    $guardar = IOFactory::createWriter($doc, "Xlsx");
    try {
        $guardar->save($plantillaCopia);
        
    } catch (Exception $e) {
        echo "Error al guardar: " . $e->getMessage();
        unlink($plantillaCopia);
    }
?>