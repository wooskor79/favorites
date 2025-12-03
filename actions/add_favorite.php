<?php
// File: app/actions/add_favorite.php
require_once '../core/init.php';

if (!$is_loggedin) {
    header("location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $url = trim($_POST['url'] ?? '');
    $alias = trim($_POST['alias'] ?? '');

    if (empty($url) || empty($alias)) {
        $_SESSION['message'] = 'URL과 별칭을 모두 입력해주세요.';
        header("location: ../admin.php?tab=favorites");
        exit;
    }

    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "https://".$url;
    }

    // 그룹 ID를 '기본 그룹'으로 고정 (또는 NULL 처리 가능)
    $default_group_id = 1; 

    $stmt = $conn->prepare("INSERT INTO favorites (url, alias, group_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $url, $alias, $default_group_id);
    
    if($stmt->execute()){
        $_SESSION['message'] = '새로운 즐겨찾기가 추가되었습니다.';
    } else {
        $_SESSION['message'] = '즐겨찾기 추가에 실패했습니다: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("location: ../admin.php?tab=favorites");
    exit;
}
?>