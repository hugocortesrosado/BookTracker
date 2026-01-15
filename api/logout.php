<?php
// logout.php - API REST para cerrar sesión
session_start();
header('Content-Type: application/json');

// Destruir la sesión
session_destroy();

echo json_encode(["status" => "success", "message" => "Sesión cerrada correctamente"]);
?>
