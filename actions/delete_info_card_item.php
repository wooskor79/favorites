<?php
require_once '../core/init.php';
if (!$is_loggedin) { exit('로그인 필요'); }

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM info_card_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
header("location: ../admin.php?tab=info_cards");
exit; 