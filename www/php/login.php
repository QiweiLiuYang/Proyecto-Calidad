<?php
    // Set the response header to JSON format
    header('Content-Type: application/json');

    // Include the file containing the HTML structure for the dropdowns
    include_once 'dropdownContent.php';
    
    // Define the session cookie lifetime to 10 hours if remember me is selected, otherwise, until the browser is closed
    $duracionCookie = 0;
    if(isset($_POST['recordar']) && $_POST['recordar'] == "si"){
        $duracionCookie = 36000;
    }

    // Configure session cookie security settings before starting the session
    session_set_cookie_params([
        'lifetime' => $duracionCookie,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    // Initialize the session
    session_start();

    // Get the credentials from POST request
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    // Credentials for validation
    $usuario_valido = 'admin';
    $contrasena_valida = 'Adm1n2026@';
    
    // Validate credentials and send response
    if($usuario === $usuario_valido && $contrasena === $contrasena_valida){
        session_regenerate_id(true);
        $_SESSION['loggedin'] = true;
        $_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];

        echo json_encode(['success'=> $_SESSION['loggedin'], 'html' => $html]);
    } else {
        // Return 401 Unauthorized if credentials do not match
        http_response_code(401);
    }
?>