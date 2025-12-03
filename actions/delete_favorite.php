<?php
// File: app/actions/delete_favorite.php
require_once '../core/init.php';

if (!$is_loggedin) {
    header("location: ../index.php");
    exit;
}

if (!isset($_GET["id"]) || empty(trim($_GET["id"]))) {
    header("location: ../admin.php?tab=favorites");
    exit();
}

$id = trim($_GET["id"]);

// ... (기존 삭제 로직은 그대로 유지)

// 즐겨찾기 삭제
$stmt_delete = $conn->prepare("DELETE FROM favorites WHERE id = ?");
$stmt_delete->bind_param("i", $id);
$stmt_delete->execute();
$stmt_delete->close();

// ... (빈 그룹 삭제 로직은 그대로 유지)

$conn->close();
$_SESSION['message'] = "즐겨찾기가 삭제되었습니다.";
header("location: ../admin.php?tab=favorites");
exit();
?>