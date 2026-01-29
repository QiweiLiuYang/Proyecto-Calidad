<?php
    require "vendor/autoload.php";
    require_once "parseXLSX.php";
    use PhpOffice\PhpSpreadsheet\IOFactory;

    // Hacer copia de la plantilla original para trabajar sobre ella, por si a mitad de proceso
    // ocurre un error, que no se nos corrompa el original. Al acabar, se eliminará la plantilla copia.
    // Tendra como nombre, la fecha actual
    $plantillaOriginal = "plantilla.xlsx";
    $plantillaCopia = "copia_" . (new DateTime())->format("d-m-Y_H-i-s-v") . ".xlsx";
    copy($plantillaOriginal, $plantillaCopia);

    // Cargar plantilla en memoria 
    $doc = IOFactory::load($plantillaCopia);

    // Rellenado de las celdas de la hoja 0
    function rellenarHoja0($doc, $datos0, $datos1){
        // Abrir la primera pestaña del Excel
        $hoja0 = $doc->getSheet(0);

        $hoja0->setCellValue("G3", $datos1[0]['grup']);
        $hoja0->setCellValue("G4", $datos1[0]['tutor']);
        $inicioRellenado = 13;
        foreach($datos0 as $profesor){
            // print_r($profesor);
            if($profesor['grup'] == $datos1[0]['grup']){
                $hoja0->setCellValue("B" . $inicioRellenado, $profesor['mdas']);
                $hoja0->setCellValue("C" . $inicioRellenado, $profesor['prof']);
            }
            $inicioRellenado++;
        }
    }
    
    rellenarHoja0($doc, $datos0, $datos1);

    $guardar = IOFactory::createWriter($doc, "Xlsx");
    try {
        $guardar->save($plantillaCopia);
    } catch (Exception $e) {
        echo "Error al guardar: " . $e->getMessage();
        unlink($plantillaCopia);
    }
?>