<?php
    header('Content-Type: application/json');
    session_start();

    if(!isset($_SESSION['loggedin']) || $_SESSION['ua'] !== $_SERVER['HTTP_USER_AGENT']){
        session_unset();
        session_destroy();
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado', 'logged' => false]);
        exit;
    }else{
        include_once 'dropdownContent.php';
        echo json_encode(['html' => $html]);
    }