<?php
// File: src/add_memo.php
require_once '../core/init.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    exit('로그인이 필요합니다.');
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$images_json = $_POST['images'] ?? '[]';

if (empty($title)) {
    http_response_code(400);
    exit('제목을 입력하세요.');
}

$stmt = $conn->prepare("INSERT INTO memos (title, content, images, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $title, $content, $images_json);

if ($stmt->execute()) {
    // 성공 시 마지막으로 삽입된 메모의 정보를 JSON으로 반환
    $last_id = $conn->insert_id;
    $memo_query = $conn->prepare("SELECT id, title, content, images, created_at FROM memos WHERE id = ?");
    $memo_query->bind_param("i", $last_id);
    $memo_query->execute();
    $result = $memo_query->get_result();
    $new_memo = $result->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($new_memo);
} else {
    http_response_code(500);
    echo '메모 추가에 실패했습니다.';
}

$stmt->close();
$conn->close();
?>