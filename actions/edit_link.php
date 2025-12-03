<?php
// File: app/actions/edit_info_card.php
require_once '../core/init.php';

// 응답 형식을 JSON으로 설정
header('Content-Type: application/json');

// JSON 오류 메시지 전송 함수
function send_json_error($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// 로그인 및 요청 방식 확인
if (!$is_loggedin || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_error('Unauthorized', 403);
}

// 폼 데이터 가져오기
$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$url = trim($_POST['url'] ?? '');

// 데이터 유효성 검사
if (empty($id) || empty($title) || empty($url)) {
    send_json_error('ID, 제목, URL은 필수입니다.', 400);
}

if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
    $url = "https://".$url;
}

// DB 업데이트 (sort_order는 수정하지 않음)
$stmt = $conn->prepare("UPDATE info_cards SET title = ?, content = ?, url = ? WHERE id = ?");
if ($stmt === false) {
    send_json_error('SQL 준비 실패: ' . $conn->error);
}

$stmt->bind_param("sssi", $title, $content, $url, $id);

if ($stmt->execute()) {
    $stmt->close();
    
    // 성공 시, 업데이트된 최신 카드 정보를 다시 조회
    $query = $conn->prepare("SELECT id, title, content, url, sort_order FROM info_cards WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $updated_card = $result->fetch_assoc();
    $query->close();
    $conn->close();

    // 최신 카드 정보를 JSON으로 응답
    echo json_encode($updated_card);
} else {
    send_json_error('카드 업데이트 실패: ' . $stmt->error);
}
?>