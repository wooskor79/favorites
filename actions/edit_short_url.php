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
$title = trim($_POST['title'] ?? ''); // 프론트엔드에서 title로 보냄 -> DB엔 alias로 저장

if (empty($id)) {
    send_json_error('ID is required.', 400);
}

$conn_shortener = get_shortener_db_connection();
if (!$conn_shortener) {
    send_json_error('Shortener DB connection failed.');
}

// [수정] 테이블명 urls, 컬럼명 alias로 변경
$stmt = $conn_shortener->prepare("UPDATE urls SET alias = ? WHERE id = ?");
if ($stmt === false) {
    $conn_shortener->close();
    send_json_error('SQL prepare failed: ' . $conn_shortener->error);
}

$stmt->bind_param("si", $title, $id);

if ($stmt->execute()) {
    $stmt->close();
    
    // [수정] 업데이트된 데이터 반환 시 프론트엔드 호환성을 위해 alias AS title로 반환
    $query = $conn_shortener->prepare("SELECT id, alias AS title FROM urls WHERE id = ?");
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