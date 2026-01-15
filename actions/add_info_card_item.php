<?php
require_once '../core/init.php';
if (!$is_loggedin) { exit('로그인 필요'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['group_id'] ?? null;
    $content = trim($_POST['content'] ?? '');
    $url = trim($_POST['url'] ?? '');

    if ($group_id && !empty($content) && !empty($url)) {
        // 아이템 개수 확인
        $count_res = $conn->query("SELECT COUNT(*) as count FROM info_card_items WHERE group_id = " . (int)$group_id);
        $count = $count_res->fetch_assoc()['count'];
        if ($count < 5) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = "https://".$url;
            }
            
            // [추가] 해당 그룹의 가장 큰 sort_order 값을 가져옴
            $order_res = $conn->query("SELECT MAX(sort_order) as max_order FROM info_card_items WHERE group_id = " . (int)$group_id);
            $max_order = $order_res->fetch_assoc()['max_order'];
            $next_order = ($max_order !== null) ? $max_order + 1 : 0;

            $stmt = $conn->prepare("INSERT INTO info_card_items (group_id, content, url, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $group_id, $content, $url, $next_order);
            $stmt->execute();
            $stmt->close();
        }
    }
}
$conn->close();
header("location: ../admin.php?tab=info_cards");
exit;