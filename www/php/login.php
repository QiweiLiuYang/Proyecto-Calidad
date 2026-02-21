<?php
    header('Content-Type: application/json');
    include_once 'dropdownContent.php';
    
    $duracionCookie = 0;
    if(isset($_POST['recordar']) && $_POST['recordar'] == "si"){
        $duracionCookie = 36000;
    }

    session_set_cookie_params([
        'lifetime' => $duracionCookie,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();

    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    $usuario_valido = 'admin';
    $contrasena_valida = 'Adm1n2026@';
    
    if($usuario === $usuario_valido && $contrasena === $contrasena_valida){
        session_regenerate_id(true);
        $_SESSION['loggedin'] = true;
        $_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];

        echo json_encode(['success'=> $_SESSION['loggedin'], 'html' => $html]);
    } else {
        http_response_code(401);
    }
?>