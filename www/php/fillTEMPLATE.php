<?php
    require "vendor/autoload.php";
    require_once "parseXLSX.php";
    require_once "parseXML.php";
    use PhpOffice\PhpSpreadsheet\IOFactory;

    // Grupo de la cual vamos a rellenar
    $grupoForm = $_POST['grupo'] ?? null;
    if($grupoForm === null || !isset($datos1[$grupoForm])) die("Error: No se ha recibido un grupo válido");

    $acta = "plantilla.xlsx";
    $index = "plantilla_Index.xlsx";

    // Cargar las plantillas en memoria 
    $docIndex = IOFactory::load($index);
    $docActa = IOFactory::load($acta);

    // Rellenado de la hoja de índice de las actas
    function rellenarActaIndex($docIndex, $docActa, $datos0, $datos1, $tutor){
        // Abrir la primera pestaña del Excel
        $hoja0 = $docIndex->getSheet(0);
        
        $fichaAlum = $docActa->getSheet(11);

        // Obtener el curso actual
        $curso = (date('n') >= 9) ? date('Y') . "-" . (date('Y') + 1) : (date('Y') - 1) . "-" . date('Y');

        $hoja0->setCellValue("G3", $datos1[$tutor]['grup']);
        $hoja0->setCellValue("G4", $datos1[$tutor]['tutor']);
        $hoja0->setCellValue("G5", $curso);

        $fichaAlum->setCellValue("K3", $datos1[$tutor]['grup']);
        $fichaAlum->setCellValue("K4", $datos1[$tutor]['tutor']);
        $fichaAlum->setCellValue("K5", $curso);

        $inicioRellenado = 13;
        foreach($datos0 as $profesor){
            if(str_contains($profesor['grup'], $datos1[$tutor]['grup'])){
                $hoja0->setCellValue("B" . $inicioRellenado, $profesor['mdas']);
                $hoja0->setCellValue("C" . $inicioRellenado, $profesor['prof']);
                $inicioRellenado++;
            }
        }
    }
    
    // Rellenado de las celdas de la hoja 3
    function rellenarFichaAlumActa($docActa, $ficheroAlumnos, $datos1, $tutor){
        // Abrir la tercera pestaña del Excel
        $fichaAlum = $docActa->getSheet(11);

        // Array que contendrá a los alumnos del grupo
        $grupo = [];

        // Curso al que va (primero o segundo)
        $curso = $datos1[$tutor]['grup'][0]%2 == 0 ? "1" : "2";

        // Formateado del nombre del curso para buscarlo en el fichero de alumnos
        $nombreGrupo = strtoupper(trim(substr($datos1[$tutor]['grup'], 1)));
        
        // Bucle que recorre la lista de alumnos
        foreach($ficheroAlumnos->alumnos->alumno as $alu){
            // Parseamos a string para asegurar la compatibilidad de tipos
            $grupoXml = (string)$alu['grupo'];

            // If para ver si está en primero o segundo y en que grupo
            if(str_contains($grupoXml, $curso) && str_contains($grupoXml, $nombreGrupo)){
                $grupo[] = [
                    "NIA" => (string)$alu['NIA'],
                    "apellido1" => (string)$alu['apellido1'],
                    "apellido2" => (string)$alu['apellido2'],
                    "nombre" => (string)$alu['nombre'],
                    "nuss" => (string)$alu['nuss'],
                    "fechaNac" => (string)$alu['fecha_nac'],
                    "sexo" => (string)$alu['sexo'],
                    "documento" => (string)$alu['documento'],
                    "domicilio" => (string)$alu['domicilio'],
                    "numero" => (string)$alu['numero'],
                    "puerta" => (string)$alu['puerta'],
                    "codPostal" => (string)$alu['cod_postal'],
                    "tel1" => (string)$alu['telefono1'],
                    "tel2" => (string)$alu['telefono2'],
                    "email1" => (string)$alu['email1'],
                    "email2" => (string)$alu['email2'],
                    "ensenanza" => (int)$alu['ensenanza'],
                    "curso" => (int)$alu['curso'],
                    "grupo" => (string)$alu['grupo'],
                    "turno" => (string)$alu['turno'],
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
        $inicioRellenado = 9;
        
        // Bucle para iniciar el rellenado de cada fila con sus datos
        foreach($grupo as $alu){
            //$nombre = "{$alu['apellido1']} {$alu['apellido2']}, {$alu['nombre']}";
            //$fichaAlum->setCellValue("C" . $inicioRellenado, $nombre);
            $fichaAlum->setCellValue("B" . $inicioRellenado, $alu['NIA']);
            $fichaAlum->setCellValue("C" . $inicioRellenado, $alu['nombre']);
            $fichaAlum->setCellValue("D" . $inicioRellenado, $alu['apellido1']);
            $fichaAlum->setCellValue("E" . $inicioRellenado, $alu['apellido2']);
            $fichaAlum->setCellValueExplicit("F" . $inicioRellenado, $alu['nuss'] ?? "No", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $fichaAlum->setCellValue("G" . $inicioRellenado, $alu['fechaNac']);
            $fichaAlum->setCellValue("H" . $inicioRellenado, $alu['sexo']);
            $fichaAlum->setCellValue("I" . $inicioRellenado, $alu['documento']);
            $fichaAlum->setCellValue("J" . $inicioRellenado, $alu['domicilio']);
            $fichaAlum->setCellValue("K" . $inicioRellenado, $alu['numero']);
            $fichaAlum->setCellValue("L" . $inicioRellenado, $alu['puerta']);
            $fichaAlum->setCellValue("M" . $inicioRellenado, $alu['codPostal']);
            $fichaAlum->setCellValue("N" . $inicioRellenado, $alu['tel1']);
            $fichaAlum->setCellValue("O" . $inicioRellenado, $alu['tel2']);
            $fichaAlum->setCellValue("P" . $inicioRellenado, $alu['email1']);
            $fichaAlum->setCellValue("Q" . $inicioRellenado, $alu['email2']);
            $fichaAlum->setCellValue("R" . $inicioRellenado, $alu['ensenanza']);
            $fichaAlum->setCellValue("S" . $inicioRellenado, $alu['curso']);
            $fichaAlum->setCellValue("T" . $inicioRellenado, $alu['grupo']);
            $fichaAlum->setCellValue("U" . $inicioRellenado, $alu['turno']);
            if((int)$alu['repite'] > 0) $fichaAlum->setCellValue("V" . $inicioRellenado, "Si");
            else $fichaAlum->setCellValue("V" . $inicioRellenado, "No");
            $inicioRellenado++;
        }
    }

    // Llamada a las funciones
    rellenarActaIndex($docIndex, $docActa, $datos0, $datos1, $grupoForm);
    rellenarFichaAlumActa($docActa, $ficheroAlumnos, $datos1, $grupoForm);

    try {
        $guardarIndex = IOFactory::createWriter($docIndex, "Xlsx");
        $guardarActa = IOFactory::createWriter($docActa, "Xlsx");

        $rutaIndex = "actas/index_acta_" . $datos1[$grupoForm]['grup'] . ".xlsx";
        $rutaActa = "actas/acta_" . $datos1[$grupoForm]['grup'] . ".xlsx";

        $guardarIndex->save($rutaIndex);
        $guardarActa->save($rutaActa);

        $zip = new ZipArchive();
        $rutaZip = "actas/" . (new DateTime())->format("Y-m-d_H-i-s-v") ."_acta.zip";

        if($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE){
            $zip->addFile($rutaIndex, "index.xlsx");
            $zip->addFile($rutaActa, "acta.xlsx");
            $zip->close();
            unlink($rutaIndex);
            unlink($rutaActa);
        }

    } catch (Exception $e) {
        echo "Error al guardar: " . $e->getMessage();
    }
?>