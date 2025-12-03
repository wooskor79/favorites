<?php
// File: app/actions/add_link.php
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

        $stmt = $conn->prepare("INSERT INTO quick_links (title, url) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $url);

        if ($stmt->execute()) {
            $_SESSION['message'] = "새로운 링크가 성공적으로 추가되었습니다.";
        } else {
            $_SESSION['message'] = "링크 추가에 실패했습니다: " . $conn->error;
        }
        $stmt->close();
    }
}
$conn->close();
// 올바른 탭으로 리디렉션
header("location: ../admin.php?tab=quick_links");
exit;
?>