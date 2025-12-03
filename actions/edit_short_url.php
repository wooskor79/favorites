<?php
// File: app/actions/edit_short_url.php
require_once '../core/init.php';

header('Content-Type: application/json');

function send_json_error($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

if (!$is_loggedin || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_error('Unauthorized', 403);
}

$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');

if (empty($id)) {
    send_json_error('ID is required.', 400);
}

$conn_shortener = get_shortener_db_connection();
if (!$conn_shortener) {
    send_json_error('Shortener DB connection failed.');
}

$stmt = $conn_shortener->prepare("UPDATE links SET title = ? WHERE id = ?");
if ($stmt === false) {
    $conn_shortener->close();
    send_json_error('SQL prepare failed: ' . $conn_shortener->error);
}

$stmt->bind_param("si", $title, $id);

if ($stmt->execute()) {
    $stmt->close();
    
    $query = $conn_shortener->prepare("SELECT id, title FROM links WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $updated_link = $result->fetch_assoc();
    $query->close();
    
    $conn_shortener->close();
    echo json_encode($updated_link);
} else {
    $error_msg = $stmt->error;
    $stmt->close();
    $conn_shortener->close();
    send_json_error('Failed to update alias: ' . $error_msg);
}
?>