<?php
// books.php - API REST para gestión de libros
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(["status" => "error", "message" => "No autorizado"]));
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET': // Listar libros
        $stmt = $pdo->prepare("SELECT id, user_id, title, author, due_date, status, created_at FROM books WHERE user_id = ? ORDER BY status ASC, due_date ASC");
        $stmt->execute([$user_id]);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $books]);
        break;

    case 'POST': // Añadir libro
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['title']) || empty($data['author'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Título y autor son requeridos"]);
            break;
        }

        $due_date = $data['due_date'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO books (user_id, title, author, due_date, status) VALUES (?, ?, ?, ?, 'Pendiente')");
        
        if ($stmt->execute([$user_id, $data['title'], $data['author'], $due_date])) {
            echo json_encode(["status" => "success", "message" => "Libro añadido correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Error al añadir el libro"]);
        }
        break;

    case 'PUT': // Actualizar estado
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['id']) || empty($data['status'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID y estado son requeridos"]);
            break;
        }

        // Validar que el estado sea válido
        $valid_statuses = ['Pendiente', 'Leyendo', 'Finalizado'];
        if (!in_array($data['status'], $valid_statuses)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Estado no válido"]);
            break;
        }

        $stmt = $pdo->prepare("UPDATE books SET status = ? WHERE id = ? AND user_id = ?");
        
        if ($stmt->execute([$data['status'], $data['id'], $user_id])) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "success", "message" => "Estado actualizado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Libro no encontrado"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Error al actualizar el libro"]);
        }
        break;

    case 'DELETE': // Eliminar
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID del libro es requerido"]);
            break;
        }

        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ? AND user_id = ?");
        
        if ($stmt->execute([$data['id'], $user_id])) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "success", "message" => "Libro eliminado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Libro no encontrado"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Error al eliminar el libro"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Método no permitido"]);
        break;
}
?>