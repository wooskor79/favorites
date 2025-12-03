<?php
require_once '../core/init.php';
if (!$is_loggedin) { exit('로그인 필요'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    if (!empty($title)) {
        // 그룹 개수 확인
        $count_res = $conn->query("SELECT COUNT(*) as count FROM info_card_groups");
        $count = $count_res->fetch_assoc()['count'];
        if ($count < 4) {
            $stmt = $conn->prepare("INSERT INTO info_card_groups (title) VALUES (?)");
            $stmt->bind_param("s", $title);
            $stmt->execute();
            $stmt->close();
        }
    }
}
$conn->close();
header("location: ../admin.php?tab=info_cards");
exit;