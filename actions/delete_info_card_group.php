<?php
require_once '../core/init.php';
if (!$is_loggedin) { exit('로그인 필요'); }

$id = $_GET['id'] ?? null;
if ($id) {
    // ON DELETE CASCADE 속성으로 그룹 삭제 시 아이템도 자동 삭제됨
    $stmt = $conn->prepare("DELETE FROM info_card_groups WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
header("location: ../admin.php?tab=info_cards");
exit;