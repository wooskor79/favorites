<?php
// File: app/actions/delete_link.php
require_once '../core/init.php';

if (!$is_loggedin) { // 변수 사용
    header("location: ../index.php"); // 경로 수정!
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM quick_links WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "링크가 성공적으로 삭제되었습니다.";
    } else {
        $_SESSION['message'] = "링크 삭제에 실패했습니다: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
header("location: ../admin.php?tab=quick_links"); // 경로 수정!
exit;
?>