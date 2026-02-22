<!-- Logic to send the content of "actas" folder content -->
<?php
    $acta = $_POST['acta'] ?? null;

    if($acta){
        unlink("actas/$acta");
    }else{
        http_response_code(400);
        die("Error: La acta especificada no existe");
    }
?>