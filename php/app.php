<?php
    // Carga las librerías necesarias
    require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;

    // Nombre de la hoja XLSX
    $xlsx = '25-26 RELACIÓ - Plantilla IES ABASTOS.xlsx';
    // Cargar el fichero con la librería
    $doc = IOFactory::load($xlsx);
    // Obtener la ventana actual
    $hoja = $doc->getActiveSheet();
    
    // Índice de donde está la cabecera de los datos
    $filaEncabezado = -1;
    // Índice desde donde empieza los datos
    $filaInicioDatos = -1;
    // La columna del archivo XLSX
    $columna = 'A'; 
    // Identificador usado para localizar el inicio de los datos, por defecto, id
    $identificador = 'id';
    // Array asociativo para vincular cada encabezado en que columna está
    $mapaEncabezado = [];

    // Bucle para obtener donde empiezan los datos realmente, buscamos donde empieza la celda 'Id'
    foreach($hoja->getRowIterator() as $fila){
        $indiceFila = $fila->getRowIndex(); // Obtiene el número de la fila actual

        // Obtiene el valor de la celda de la columa A en la fila actual, quita los espacios blanco y la transforma en minúscula
        $celdaA = strtolower(trim($hoja->getCell($columna . $indiceFila)->getValue()));
        
        // Comparar si la celda A de la fila actual coindice con el identificador dado, si es, actualiza $filaEncabezado 
        // y $filaInicioDatos y sale del bucle 
        if($celdaA == $identificador){
            $filaEncabezado = $indiceFila;
            $filaInicioDatos = $indiceFila+1;
            break;
        }
    }

    //Si $filaEncabezado no ha cambiado, significa que no ha encontrado 'ID' e imprimimos mensaje de error
    if($filaEncabezado == -1){
        echo "Error: No se encontró la cabecera 'ID' en la columna A";
    }else{
        // Si el contenido de $filaEncabezado ha cambiado, es que ha encontrado 'ID', entonces obtenemos la última fila del documento
        $ultimaFila = $hoja->getHighestRow($columna);
        // Obtiene el objeto de la fila del encabezado
        $objetoFila = $hoja->getRowIterator($filaEncabezado, $filaEncabezado)->current();
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
                $mapaEncabezado[$encabezado] = $letra; 
                
            }
        }
    }
    //print_r($mapaEncabezado);
    // Bucle que obtiene todos los datos de cada fila
    for($fila = $filaInicioDatos; $fila <= $ultimaFila; $fila++){
        $id = $hoja->getCell($mapaEncabezado['ID'] . $fila)->getValue();
        $dpt = $hoja->getCell($mapaEncabezado['DPT'] . $fila)->getValue();
        $dir = $hoja->getCell($mapaEncabezado['DIR'] . $fila)->getValue();
        $etapa = $hoja->getCell($mapaEncabezado['ETAPA'] . $fila)->getValue();
        $grup = $hoja->getCell($mapaEncabezado['GRUP'] . $fila)->getValue();
        $codi = $hoja->getCell($mapaEncabezado['CODI'] . $fila)->getValue();
        $mdas = $hoja->getCell($mapaEncabezado['MD-AS'] . $fila)->getValue();
        $prof = $hoja->getCell($mapaEncabezado['PROF'] . $fila)->getValue();


        echo "$id | $dpt | $dir | $etapa | $grup | $codi | $mdas | $prof\n";
    }
?>