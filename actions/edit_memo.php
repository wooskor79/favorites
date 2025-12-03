<?php
// File: src/edit_memo.php

require_once '../core/init.php';

// 항상 JSON 형식으로 응답하도록 헤더 설정
header('Content-Type: application/json');

// 오류 메시지를 JSON 형식으로 보내는 함수
function send_json_error($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// 1. 로그인 상태 확인
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    send_json_error('로그인이 필요합니다.', 403);
}

// 2. 요청 방식 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_error('잘못된 요청 방식입니다.', 405);
}

// 3. 데이터 가져오기 및 유효성 검사
$id = $_POST['id'] ?? '';
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$images_json = $_POST['images'] ?? '[]';


if (empty($id) || empty($title)) {
    send_json_error('ID와 제목은 필수입니다.', 400);
}

// 4. 데이터베이스 업데이트
$stmt = $conn->prepare("UPDATE memos SET title = ?, content = ?, images = ? WHERE id = ?");
if ($stmt === false) {
    send_json_error('SQL 준비에 실패했습니다: ' . $conn->error);
}

$stmt->bind_param("sssi", $title, $content, $images_json, $id);

if ($stmt->execute()) {
    $stmt->close();
    
    // 성공 시, 업데이트된 메모 정보를 다시 조회하여 반환
    $memo_query = $conn->prepare("SELECT id, title, content, images, created_at FROM memos WHERE id = ?");
    $memo_query->bind_param("i", $id);
    $memo_query->execute();
    $result = $memo_query->get_result();
    $updated_memo = $result->fetch_assoc();
    $memo_query->close();
    $conn->close();
    
    echo json_encode($updated_memo); // 성공 응답
} else {
    send_json_error('메모 수정에 실패했습니다: ' . $stmt->error);
}
?>