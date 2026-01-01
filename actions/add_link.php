<?php
// 파일명: app/actions/add_link.php
require_once '../core/init.php';

if (!$is_loggedin) {
    header("location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['url'] ?? '');

    if (empty($title) || empty($url)) {
        $_SESSION['message'] = "링크 제목과 URL을 모두 입력해야 합니다.";
    } else {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://".$url;
        }

        // 현재 최대 sort_order 값을 가져와서 다음 순번 결정
        $res = $conn->query("SELECT MAX(sort_order) as max_order FROM quick_links");
        $row = $res->fetch_assoc();
        $next_order = ($row['max_order'] !== null) ? (int)$row['max_order'] + 1 : 0;

        $stmt = $conn->prepare("INSERT INTO quick_links (title, url, sort_order) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $url, $next_order);

        if ($stmt->execute()) {
            $_SESSION['message'] = "새로운 링크가 성공적으로 추가되었습니다.";
        } else {
            $_SESSION['message'] = "링크 추가에 실패했습니다: " . $conn->error;
        }
        $stmt->close();
    }
}
$conn->close();
header("location: ../admin.php?tab=quick_links");
exit;
?>