<?php
// login.php - API REST para inicio de sesión
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config.php';
$con = obtenerConexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $errors = [];

    if (empty($email)) {
        $errors['email'] = "Por favor, ingrese su email.";
    }
    
    if (empty($password)) {
        $errors['password'] = "Por favor, ingrese su contraseña.";
    }

    if (empty($errors)) {
        // Buscar usuario por email
        $sql = "SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1";

        if ($stmt = $con->prepare($sql)) {
            if ($stmt->execute([$email])) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        // Login correcto: establecer sesión
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        echo json_encode(["status" => "success", "message" => "Sesión iniciada correctamente"]);
                        exit;
                    } else {
                        echo json_encode(["status" => "error", "message" => "Contraseña incorrecta."]);
                        exit;
                    }
                } else {
                    echo json_encode(["status" => "error", "message" => "Usuario no encontrado."]);
                    exit;
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Error en la base de datos."]);
                exit;
            }
            $stmt = null;
        } else {
            echo json_encode(["status" => "error", "message" => "Error preparando la consulta."]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "errors" => $errors]);
        exit;
    }

    $con = null;
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    http_response_code(405);
}
?>