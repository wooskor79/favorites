<?php
// File: app/actions/add_short_url.php
require_once '../core/init.php';

if (!$is_loggedin) {
    header("location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $long_url = trim($_POST['long_url'] ?? '');
    $title = trim($_POST['title'] ?? ''); // 별칭(Alias)

    if (empty($long_url)) {
        $_SESSION['error'] = "원본 URL을 입력해야 합니다.";
        header("location: ../admin.php?tab=url_shortener");
        exit;
    }

    // URL 유효성 검사 및 프로토콜 추가
    if (!preg_match("~^(?:f|ht)tps?://~i", $long_url)) {
        $long_url = "https://".$long_url;
    }

    $conn_shortener = get_shortener_db_connection();
    if (!$conn_shortener) {
        $_SESSION['error'] = "Shortener 데이터베이스 연결에 실패했습니다.";
        header("location: ../admin.php?tab=url_shortener");
        exit;
    }

    // 5자리 영문/숫자 고유 코드 생성
    $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    $max_attempts = 10;
    
    do {
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        // [수정] 테이블명 urls, 컬럼명 short_code로 변경
        $stmt_check = $conn_shortener->prepare('SELECT 1 FROM urls WHERE short_code = ?');
        $stmt_check->bind_param('s', $code);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $exists = $result->num_rows > 0;
        $stmt_check->close();
        
        $max_attempts--;
        if ($max_attempts < 0) {
            $_SESSION['error'] = "고유 코드를 생성하지 못했습니다. 다시 시도해주세요.";
            header("location: ../admin.php?tab=url_shortener");
            exit;
        }
    } while ($exists);

    // [수정] urls 테이블 구조에 맞춰 쿼리 변경
    // long_url, short_code, alias, owner_token, created_at
    // owner_token은 관리자이므로 현재 세션의 username을 사용합니다.
    $stmt_insert = $conn_shortener->prepare(
        'INSERT INTO urls (long_url, short_code, alias, owner_token, created_at) 
         VALUES (?, ?, ?, ?, NOW())'
    );
    
    // 관리자가 생성한 링크임을 표시하기 위해 owner_token에 admin 계정명 사용
    $owner_token = $_SESSION['username'] ?? 'admin';

    // alias가 비어있으면 NULL 처리
    $alias_val = empty($title) ? null : $title;

    $stmt_insert->bind_param("ssss", $long_url, $code, $alias_val, $owner_token);

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