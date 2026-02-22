<?php
    // Set the response header to JSON format
    header('Content-Type: application/json');

    // Load the PHPSpreadSheet library
    require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;

    // Get the temporary path of the uploaded file
    $profesores = $_FILES['profesores']['tmp_name'] ?? null;

    // Load the Excel file into memory
    $doc = IOFactory::load($profesores);

    // Access individual worksheets by index
    $hoja0 = $doc->getSheet(0);
    $hoja1 = $doc->getSheet(1);


    // Index of where the data header on sheet 0
    $filaEncabezado0 = -1;
    // Index of where the data starts on sheet 0
    $filaInicioDatos0 = -1;
    // Index of where the data header on sheet 1
    $filaEncabezado1 = -1;
    // Index of where the data starts on sheet 1
    $filaInicioDatos1 = -1; 
    // Column where the identifier is located on sheet 0
    $columna0 = 'A'; 
    // Identifier to identificate the start of the data, by default, id. On sheet 0
    $identificador0 = 'id';
    // Column of the XLSX file
    $columna1 = 'A'; 
    // Identifier to identificate the start of the data, by default, group. On sheet 1
    $identificador1 = 'grup';
    // Associative array to link each header to which column it is in, from sheet 0
    $mapaEncabezado0 = [];
    // Associative array to link each header to which column it is in, from sheet 1
    $mapaEncabezado1 = [];
    // Array with the data obtained from sheet 0
    $datos0 = [];
    // Array with the data obtained from sheet 1
    $datos1 = [];

    // Loop to obtain where the data actually starts on sheet 0. By default, the cell begins with 'Id'
    foreach($hoja0->getRowIterator() as $fila){
        // Get the number of the actual row
        $indiceFila = $fila->getRowIndex();

        // Get the value from the cell in column A in the current row. Removes the whitespace and converts it to lowercase
        $celdaA = strtolower(trim($hoja0->getCell($columna0 . $indiceFila)->getValue()));
        // Compare if cell A in the current row matches the given identifier, if so, update $filaEncabezado0 and $filaInicioFatos0 and exit the loop
        if($celdaA == $identificador0){
            $filaEncabezado0 = $indiceFila;
            $filaInicioDatos0 = $indiceFila+1;
            break;
        }
    }

    // If $filaEncabezado0 has not changed, it means that it has not found 'ID' an print an error message
    if($filaEncabezado0 == -1){
        echo "Error: No se encontró la cabecera 'ID' en la columna A";
    }else{
        // If the content of $filaEncabezado0 has changed, it means it has found 'ID', so we get the last row of the document
        $ultimaFila = $hoja0->getHighestRow($columna0);
        // Gets the object from the header row
        $objetoFila = $hoja0->getRowIterator($filaEncabezado0, $filaEncabezado0)->current();
        // Gets the iterator of the cells (columns) in that row
        $iteradorCeldas = $objetoFila->getCellIterator();

        // This foreach loop iterates through each cell to get its header and which column it's in, to match it and add it to the array.
        foreach($iteradorCeldas as $celda){
            // Gets the column letter of the cell
            $letra = $celda->getColumn();
            // Gets the content (header) of the cell
            $encabezado = strtoupper(trim($celda->getValue()));

            // If $header has content, it puts it into the associative array
            if(!empty($encabezado)){
                $mapaEncabezado0[$encabezado] = $letra; 
            }
        }
    }
    // Loop that retrieves all the data from each row
    for($fila = $filaInicioDatos0; $fila <= $ultimaFila; $fila++){
        $id = trim($hoja0->getCell($mapaEncabezado0['ID'] . $fila)->getValue());
        $dpt = trim($hoja0->getCell($mapaEncabezado0['DPT'] . $fila)->getValue());
        $dir = trim($hoja0->getCell($mapaEncabezado0['DIR'] . $fila)->getValue());
        $etapa = trim($hoja0->getCell($mapaEncabezado0['ETAPA'] . $fila)->getValue());
        $grup = trim($hoja0->getCell($mapaEncabezado0['GRUP'] . $fila)->getValue());
        $codi = trim($hoja0->getCell($mapaEncabezado0['CODI'] . $fila)->getValue());
        $mdas = trim($hoja0->getCell($mapaEncabezado0['MD-AS'] . $fila)->getValue());
        $prof = trim($hoja0->getCell($mapaEncabezado0['PROF'] . $fila)->getValue());

        $datos0[] = [
            "id" => $id,
            "dpt" => $dpt,
            "dir" => $dir,
            "etapa" => $etapa,
            "grup" => $grup,
            "codi" => $codi,
            "mdas" => $mdas,
            "prof" => $prof
        ];
    }

    // Loop to find where the data actually starts in sheet1, we look for where the 'GRUP' cell begins
    foreach($hoja1->getRowIterator() as $fila){
        // Gets the current row number
        $indiceFila = $fila->getRowIndex();

        // Gets the value from the cell in column A in the current row, removes the whitespace, and converts it to lowercase.
        $celdaA = strtolower(trim($hoja1->getCell($columna1 . $indiceFila)->getValue()));
        
        // Compare if cell A of the current row matches the given identifier; if so, update $filaEncabezado1 and $filaInicioDatos1 and exit the loop
        if($celdaA == $identificador1){
            $filaEncabezado1 = $indiceFila;
            $filaInicioDatos1 = $indiceFila+1;
            break;
        }
    }

    // If $filaEncabezado1 has not changed, it means that 'GRUP' has not been found and we print an error message
    if($filaEncabezado1 == -1){
        echo "Error: No se encontró la cabecera 'ID' en la columna A";
    }else{
        // If the content of $filaEncabezado1 has changed, it means it has found 'GRUP', so we get the last row of the document.
        $ultimaFila = $hoja1->getHighestRow($columna1);
        // Gets the object from the header row
        $objetoFila = $hoja1->getRowIterator($filaEncabezado1, $filaEncabezado1)->current();
        // Gets the iterator of the cells (columns) in that row
        $iteradorCeldas = $objetoFila->getCellIterator();

        // This foreach loop iterates through each cell to get its header and the column it's in, to match it and add it to the array
        foreach($iteradorCeldas as $celda){
            // Gets the column letter of the cell
            $letra = $celda->getColumn();
            // Gets the content (header) of the cell
            $encabezado = strtoupper(trim($celda->getValue()));

            // If $header has content, it puts it into the associative array
            if(!empty($encabezado)){
                $mapaEncabezado1[$encabezado] = $letra; 
            }
        }
    }

    $indiceXMatriz = 0;
    // Loop that retrieves all the data from each row
    for($fila = $filaInicioDatos1; $fila <= $ultimaFila; $fila++){
        $grup = trim($hoja1->getCell($mapaEncabezado1['GRUP'] . $fila)->getValue());
        $tutor = trim($hoja1->getCell($mapaEncabezado1['TUTOR'] . $fila)->getValue());

        $datos1[$indiceXMatriz] = [
            "grup" => $grup,
            "tutor" => $tutor
        ];
        $indiceXMatriz++;
    }
    if (basename($_SERVER['SCRIPT_FILENAME']) === 'parseXLSX.php') {
        header('Content-Type: application/json');
        echo json_encode($datos1);
    }
?>