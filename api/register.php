<?php
// register.php - API REST para registro de usuarios
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config.php';
$con = obtenerConexion();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $contrasena = trim($_POST["password"] ?? '');

    $errors = [];

    // Validar nombre
    if (empty($nombre)) {
        $errors['name'] = "Por favor, ingrese un nombre completo.";
    }

    // Validar email
    if (empty($email)) {
        $errors['email'] = "Por favor, ingrese un email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "El email no es válido.";
    } else {
        // Verificar que el email no existe
        $sql = "SELECT id FROM users WHERE email = ?"; 
        if ($stmt = $con->prepare($sql)) {
            if ($stmt->execute([$email])) {
                if ($stmt->fetchColumn()) {
                    $errors['email'] = "Este email ya está registrado.";
                }
            }
            $stmt = null;
        }
    }

    // Validar contraseña
    if (empty($contrasena)) {
        $errors['password'] = "Por favor, ingrese una contraseña.";     
    } elseif (strlen($contrasena) < 6) {
        $errors['password'] = "La contraseña debe tener al menos 6 caracteres.";
    }

    // Si no hay errores, insertar usuario
    if (empty($errors)) {
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
         
        if ($stmt = $con->prepare($sql)) {
            $param_contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
            
            if ($stmt->execute([$nombre, $email, $param_contrasena_hash])) {
                echo json_encode(["status" => "success", "message" => "Usuario registrado correctamente"]);
                exit();
            } else {
                echo json_encode(["status" => "error", "message" => "Error al registrar el usuario"]);
                exit();
            }
            $stmt = null;
        }
    } else {
        echo json_encode(["status" => "error", "errors" => $errors]);
        exit();
    }

    $con = null;
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    http_response_code(405);
}
?>
