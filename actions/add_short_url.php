<?php
// File: app/actions/add_short_url.php
require_once '../core/init.php';

if (!$is_loggedin) {
    header("location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $long_url = trim($_POST['long_url'] ?? '');
    $title = trim($_POST['title'] ?? '');

    if (empty($long_url)) {
        $_SESSION['error'] = "원본 URL을 입력해야 합니다.";
        header("location: ../admin.php?tab=url_shortener");
        exit;
    }

    if (!preg_match("~^(?:f|ht)tps?://~i", $long_url)) {
        $long_url = "https://".$long_url;
    }

    $conn_shortener = get_shortener_db_connection();
    if (!$conn_shortener) {
        $_SESSION['error'] = "Shortener 데이터베이스 연결에 실패했습니다.";
        header("location: ../admin.php?tab=url_shortener");
        exit;
    }

    // 5자리 영문자로 된 고유 코드 생성
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    do {
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        $stmt_check = $conn_shortener->prepare('SELECT 1 FROM links WHERE code = ?');
        $stmt_check->bind_param('s', $code);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
    } while ($result->num_rows > 0);
    $stmt_check->close();

    $stmt_insert = $conn_shortener->prepare(
        'INSERT INTO links (long_url, code, title, created_at, creator_ip, user_agent) 
         VALUES (?, ?, ?, NOW(), ?, ?)'
    );
    
    $creator_ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $stmt_insert->bind_param("sssss", $long_url, $code, $title, $creator_ip, $user_agent);

    if ($stmt_insert->execute()) {
        $_SESSION['message'] = "새로운 단축 URL이 생성되었습니다.";
    } else {
        $_SESSION['error'] = "단축 URL 생성에 실패했습니다: " . $stmt_insert->error;
    }
    
    $stmt_insert->close();
    $conn_shortener->close();
}

header("location: ../admin.php?tab=url_shortener");
exit;
?>