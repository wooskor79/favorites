<?php
// File: app/actions/delete_info_card.php
require_once '../core/init.php';

if (!$is_loggedin) {
    header("location: ../index.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM info_cards WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "정보 카드가 삭제되었습니다.";
    } else {
        $_SESSION['error'] = "삭제에 실패했습니다: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
header("location: ../admin.php?tab=info_cards");
exit;
?>