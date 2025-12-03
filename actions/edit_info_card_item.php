<?php
require_once '../core/init.php';
header('Content-Type: application/json');

function send_json_error($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

if (!$is_loggedin) { send_json_error('로그인 필요'); }

$id = $_POST['id'] ?? null;
$content = trim($_POST['content'] ?? '');
$url = trim($_POST['url'] ?? '');

if (empty($id) || empty($content) || empty($url)) {
    send_json_error('모든 필드는 필수입니다.');
}

if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
    $url = "https://".$url;
}

$stmt = $conn->prepare("UPDATE info_card_items SET content = ?, url = ? WHERE id = ?");
$stmt->bind_param("ssi", $content, $url, $id);

if ($stmt->execute()) {
    $stmt->close();
    $query = $conn->prepare("SELECT * FROM info_card_items WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $updated_item = $result->fetch_assoc();
    $query->close();
    $conn->close();
    echo json_encode($updated_item);
} else {
    send_json_error('업데이트 실패: ' . $stmt->error);
}