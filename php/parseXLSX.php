<?php
    // Carga las librerías necesarias
    require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;

    // Nombre de la hoja XLSX
    $xlsx = '25-26 RELACIÓ - Plantilla IES ABASTOS.xlsx';
    // Cargar el fichero con la librería
    $doc = IOFactory::load($xlsx);
    // Obtener la hoja/ventana número 0
    $hoja0 = $doc->getSheet(0);
    // Obtener la hoja/ventana número 1
    $hoja1 = $doc->getSheet(1);


    // Índice de donde está la cabecera de los datos, en la hoja 0
    $filaEncabezado0 = -1;
    // Índice desde donde empieza los datos, en la hoja 0
    $filaInicioDatos0 = -1;
    // Índice de donde está la cabecera de los datos, en la hoja 1
    $filaEncabezado1 = -1;
    // Índice desde donde empieza los datos, en la hoja 1
    $filaInicioDatos1 = -1; 
    // La columna donde está el identificador, en la hoja 0
    $columna0 = 'A'; 
    // Identificador usado para localizar el inicio de los datos, por defecto, id. En la hoja 0
    $identificador0 = 'id';
    // La columna del archivo XLSX
    $columna1 = 'A'; 
    // Identificador usado para localizar el inicio de los datos, por defecto, id. En la hoja 0
    $identificador1 = 'grup';
    // Array asociativo para vincular cada encabezado en que columna está, de la hoja 0
    $mapaEncabezado0 = [];
    // Array asociativo para vincular cada encabezado en que columna está, de la hoja 1
    $mapaEncabezado1 = [];
    // Array con los datos obtenidos de la hoja 0
    $datos0 = [];
    // Array con los datos obtenidos de la hoja 1
    $datos1 = [];

    // Bucle para obtener donde empiezan los datos realmente en la hoja 0, buscamos donde empieza la celda 'Id'
    foreach($hoja0->getRowIterator() as $fila){
        $indiceFila = $fila->getRowIndex(); // Obtiene el número de la fila actual

        // Obtiene el valor de la celda de la columa A en la fila actual, quita los espacios blanco y la transforma en minúscula
        $celdaA = strtolower(trim($hoja0->getCell($columna0 . $indiceFila)->getValue()));
        // Comparar si la celda A de la fila actual coindice con el identificador dado, si es, actualiza $filaEncabezado0 
        // y $filaInicioDatos0 y sale del bucle 
        if($celdaA == $identificador0){
            $filaEncabezado0 = $indiceFila;
            $filaInicioDatos0 = $indiceFila+1;
            break;
        }
    }

    //Si $filaEncabezado0 no ha cambiado, significa que no ha encontrado 'ID' e imprimimos mensaje de error
    if($filaEncabezado0 == -1){
        echo "Error: No se encontró la cabecera 'ID' en la columna A";
    }else{
        // Si el contenido de $filaEncabezado0 ha cambiado, es que ha encontrado 'ID', entonces obtenemos la última fila del documento
        $ultimaFila = $hoja0->getHighestRow($columna0);
        // Obtiene el objeto de la fila del encabezado
        $objetoFila = $hoja0->getRowIterator($filaEncabezado0, $filaEncabezado0)->current();
        // Obtiene el iterador de las celdas (columas) de esa fila
        $iteradorCeldas = $objetoFila->getCellIterator();

        // Este bucle foreach itera cada celda para obtener su encabezado y en que columna está para enparejarlo y agregarlo al array
        foreach($iteradorCeldas as $celda){
            // Obtiene la letra de la columna de la celda
            $letra = $celda->getColumn();
            // Obtiene el contenido (encabezado) de la celda
            $encabezado = strtoupper(trim($celda->getValue()));

            // Si $encabezado tiene contenido, lo mete en el array asociativo
            if(!empty($encabezado)){
                $mapaEncabezado0[$encabezado] = $letra; 
            }
        }
    }
    //print_r($mapaEncabezado0);
    // Bucle que obtiene todos los datos de cada fila
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
    // print_r($datos0);

    // Bucle para obtener donde empiezan los datos realmente en la hoja1, buscamos donde empieza la celda 'GRUP'
    foreach($hoja1->getRowIterator() as $fila){
        $indiceFila = $fila->getRowIndex(); // Obtiene el número de la fila actual

        // Obtiene el valor de la celda de la columa A en la fila actual, quita los espacios blanco y la transforma en minúscula
        $celdaA = strtolower(trim($hoja1->getCell($columna1 . $indiceFila)->getValue()));
        
        // Comparar si la celda A de la fila actual coindice con el identificador dado, si es, actualiza $filaEncabezado1 
        // y $filaInicioDatos1 y sale del bucle 
        if($celdaA == $identificador1){
            $filaEncabezado1 = $indiceFila;
            $filaInicioDatos1 = $indiceFila+1;
            break;
        }
    }

    //Si $filaEncabezado1 no ha cambiado, significa que no ha encontrado 'GRUP' e imprimimos mensaje de error
    if($filaEncabezado1 == -1){
        echo "Error: No se encontró la cabecera 'ID' en la columna A";
    }else{
        // Si el contenido de $filaEncabezado1 ha cambiado, es que ha encontrado 'GRUP', entonces obtenemos la
        //  última fila del documento
        $ultimaFila = $hoja1->getHighestRow($columna1);
        // Obtiene el objeto de la fila del encabezado
        $objetoFila = $hoja1->getRowIterator($filaEncabezado1, $filaEncabezado1)->current();
        // Obtiene el iterador de las celdas (columas) de esa fila
        $iteradorCeldas = $objetoFila->getCellIterator();

        // Este bucle foreach itera cada celda para obtener su encabezado y en que columna está para enparejarlo
        //  y agregarlo al array
        foreach($iteradorCeldas as $celda){
            // Obtiene la letra de la columna de la celda
            $letra = $celda->getColumn();
            // Obtiene el contenido (encabezado) de la celda
            $encabezado = strtoupper(trim($celda->getValue()));

            // Si $encabezado tiene contenido, lo mete en el array asociativo
            if(!empty($encabezado)){
                $mapaEncabezado1[$encabezado] = $letra; 
            }
        }
    }

    // print_r($mapaEncabezado1);

    $indiceXMatriz = 0;
    // Bucle que obtiene todos los datos de cada fila
    for($fila = $filaInicioDatos1; $fila <= $ultimaFila; $fila++){
        $grup = trim($hoja1->getCell($mapaEncabezado1['GRUP'] . $fila)->getValue());
        $tutor = trim($hoja1->getCell($mapaEncabezado1['TUTOR'] . $fila)->getValue());

        $datos1[$indiceXMatriz] = [
            "grup" => $grup,
            "tutor" => $tutor
        ];
        $indiceXMatriz++;
    }

    // print_r($datos1);
?>