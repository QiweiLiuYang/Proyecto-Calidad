<!-- Logic to send the content of "actas" folder content -->
<?php
    $acta = $_POST['acta'] ?? null;

    if($acta){
        // Prevent Directory Traversal attack
        $filename = basename($acta);
        $filePath = "actas/$filename";
        unlink($filePath);
    }else{
        // 400 Bad Request
        http_response_code(400);
        die("Error: La acta especificada no existe");
    }
?>