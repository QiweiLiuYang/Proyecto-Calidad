<?php
    // Load dependencies via Composer
    require __DIR__ . "/vendor/autoload.php";

    // Include the parsing scripts for XLSX and XML files
    require_once __DIR__ . "/parseXLSX.php";
    require_once __DIR__ . "/parseXML.php";

    use PhpOffice\PhpSpreadsheet\IOFactory;

    // Get the selected group from the POST request and validate that is a valid group
    $grupoForm = $_POST['grupo'] ?? null;
    if($grupoForm === null || !isset($datos1[$grupoForm])){
        // 400 Bad Request
        http_response_code(400);
        die("Error: No se ha recibido un grupo válido");
    }

    // Path of the template files
    $acta = "plantilla.xlsx";
    $index = "plantilla_Index.xlsx";

    // Load templates into memory
    $docIndex = IOFactory::load($index);
    $docActa = IOFactory::load($acta);

    // Fills the index template with general course and teacher information
    function rellenarActaIndex($docIndex, $docActa, $datos0, $datos1, $tutor){
        // Open the first sheet of the index document
        $hoja0 = $docIndex->getSheet(0);
        
        // Access the specific sheet in the acta document
        $fichaAlum = $docActa->getSheet(11);

        // Calculate the current school year based on the month (usually the course starts in september)
        $curso = (date('n') >= 9) ? date('Y') . "-" . (date('Y') + 1) : (date('Y') - 1) . "-" . date('Y');

        // Fill the cell headers in both documents
        $hoja0->setCellValue("G3", $datos1[$tutor]['grup']);
        $hoja0->setCellValue("G4", $datos1[$tutor]['tutor']);
        $hoja0->setCellValue("G5", $curso);

        $fichaAlum->setCellValue("K3", $datos1[$tutor]['grup']);
        $fichaAlum->setCellValue("K4", $datos1[$tutor]['tutor']);
        $fichaAlum->setCellValue("K5", $curso);

        // Fill teacher and subject list starting from row 13
        $inicioRellenado = 13;
        foreach($datos0 as $profesor){
            // Filter data to only include subjects belonging to the selected group
            if(str_contains($profesor['grup'], $datos1[$tutor]['grup'])){
                $hoja0->setCellValue("B" . $inicioRellenado, $profesor['mdas']);
                $hoja0->setCellValue("C" . $inicioRellenado, $profesor['prof']);
                $inicioRellenado++;
            }
        }
    }
    
    // Processes and fills the student data sheet
    function rellenarFichaAlumActa($docActa, $ficheroAlumnos, $datos1, $tutor){
        // Access the specific sheet in the acta document
        $fichaAlum = $docActa->getSheet(11);

        // Array which contains the students
        $grupo = [];

        // Logic to determine the year based on the group name
        $curso = $datos1[$tutor]['grup'][0]%2 == 0 ? "1" : "2";

        // Format the group name for searching within the XML file
        $nombreGrupo = strtoupper(trim(substr($datos1[$tutor]['grup'], 1)));
        
        // Loop throught the XML student list
        foreach($ficheroAlumnos->alumnos->alumno as $alu){
            // Parse to string to ensure the compatibility of types
            $grupoXml = (string)$alu['grupo'];

            // Match studens based on year and group name
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

        // Eliminate duplicated students using NIA as a unique key
        $grupoTemp = [];
        foreach($grupo as $alu){
            $grupoTemp[$alu['NIA']] = $alu;
        }
        $grupo = array_values($grupoTemp);

        // Sort students alphabetically using Spanish language
        $collator = collator_create('es_ES');

        // Sort the array using apellido1, apellido2 y nombre
        if (!$collator) {
            usort($grupo, function($a, $b) {
                return strcmp($a['apellido1'], $b['apellido1']);
            });
        } else {
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
        }

        // Fill student data starting from row 9
        $inicioRellenado = 9;
        
        // Loop to fill each row with the student data
        foreach($grupo as $alu){
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

    // Call to functions
    rellenarActaIndex($docIndex, $docActa, $datos0, $datos1, $grupoForm);
    rellenarFichaAlumActa($docActa, $ficheroAlumnos, $datos1, $grupoForm);

    try {
        $dirActas = __DIR__ . '/actas/';

        // Prepare Excel writers
        $guardarIndex = IOFactory::createWriter($docIndex, "Xlsx");
        $guardarActa = IOFactory::createWriter($docActa, "Xlsx");

        $nombreGrupo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $datos1[$grupoForm]['grup']);

        // Temporary file paths fot the generated excels
        $rutaIndex = $dirActas . "/index_acta_" . $nombreGrupo . ".xlsx";
        $rutaActa = $dirActas . "/acta_" . $nombreGrupo . ".xlsx";

        // Save files to the server
        $guardarIndex->save($rutaIndex);
        $guardarActa->save($rutaActa);

        // Initialize ZipArchive to pachage both files
        $zip = new ZipArchive();
        $rutaZip = $dirActas . "/" . (new DateTime())->format("Y-m-d_H-i-s-v") ."_acta.zip";

        // Create the zip file, if it already exists, overwrite it
        if($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE){
            $zip->addFile($rutaIndex, "index.xlsx");
            $zip->addFile($rutaActa, "acta.xlsx");
            $zip->close();

            // Delete temporary Excel files
            unlink($rutaIndex);
            unlink($rutaActa);
        }
    } catch (\Throwable $e) {
        // 500 Internal Server Error
        http_response_code(500);
        echo "Error al guardar: " . $e->getMessage();
    }
?>