<?php
// File: src/delete_memo.php
require_once '../core/init.php';

// 로그인 상태 확인
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403); // Forbidden
    exit('로그인이 필요합니다.');
}

// GET 요청에서 id 파라미터 확인
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400); // Bad Request
    exit('삭제할 메모의 ID가 필요합니다.');
}

$id = $_GET['id'];
$conn->begin_transaction();

try {
    // 1. 삭제하기 전에 먼저 이미지 파일 경로를 가져옵니다.
    $stmt_select = $conn->prepare("SELECT images FROM memos WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $memo = $result->fetch_assoc();
    $stmt_select->close();

    // 2. 데이터베이스에서 메모를 삭제합니다.
    $stmt_delete = $conn->prepare("DELETE FROM memos WHERE id = ?");
    $stmt_delete->bind_param("i", $id);
    $stmt_delete->execute();
    $stmt_delete->close();

    // 3. 데이터베이스 삭제가 성공하면 실제 이미지 파일을 삭제합니다.
    if ($memo && !empty($memo['images'])) {
        $images = json_decode($memo['images'], true);
        if (is_array($images)) {
            foreach ($images as $thumbnail_path_web) {
                // 웹 경로를 실제 서버 파일 시스템 경로로 변환합니다.
                $thumbnail_path_server = $_SERVER['DOCUMENT_ROOT'] . $thumbnail_path_web;
                $original_path_server = str_replace('/cache/', '/', $thumbnail_path_server);

                // 썸네일 파일 삭제
                if (file_exists($thumbnail_path_server)) {
                    unlink($thumbnail_path_server);
                }
                // 원본 이미지 파일 삭제
                if (file_exists($original_path_server)) {
                    unlink($original_path_server);
                }
            }
        }
    }

    // 모든 작업이 성공하면 변경사항을 확정합니다.
    $conn->commit();
    http_response_code(200); // OK
    echo '삭제되었습니다.';

} catch (Exception $e) {
    // 오류가 발생하면 모든 변경사항을 되돌립니다.
    $conn->rollback();
    http_response_code(500); // Internal Server Error
    echo '메모 삭제 중 오류가 발생했습니다: ' . $e->getMessage();
}

$conn->close();
?>