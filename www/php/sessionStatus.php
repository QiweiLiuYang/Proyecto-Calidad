<?php
    session_start();
    header('Content-Type: application/json');

    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true){
        echo json_encode(['logged' => true]);
    }else{
        echo json_encode(['logged' => false]);
    }
?>