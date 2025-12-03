<?php
// File: app/actions/edit_info_card_group.php
require_once '../core/init.php';
header('Content-Type: application/json');

function send_json_error($message) {
    http_response_code(500);
    echo json_encode(['error' => $message]);
    exit;
}

if (!$is_loggedin) { send_json_error('로그인 필요'); }

$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');

if (empty($id) || empty($title)) {
    send_json_error('ID와 제목은 필수입니다.');
}

$stmt = $conn->prepare("UPDATE info_card_groups SET title = ? WHERE id = ?");
$stmt->bind_param("si", $title, $id);

if ($stmt->execute()) {
    $stmt->close();
    $query = $conn->prepare("SELECT * FROM info_card_groups WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $updated_group = $result->fetch_assoc();
    $query->close();
    $conn->close();
    echo json_encode($updated_group);
} else {
    send_json_error('그룹 제목 업데이트 실패: ' . $stmt->error);
}
?>