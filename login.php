<?php
// File: app/login.php

// 1. 코어 초기화 파일 불러오기 (db 연결 및 세션 시작 포함)
require_once 'core/init.php';

ob_start(); // 헤더 전송 전 출력을 버퍼에 저장

// 2. POST 요청으로만 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? 'admin';
    $password = $_POST['password'] ?? '';

    if (empty($password)) {
        $_SESSION['error'] = '비밀번호를 입력해주세요.';
        header("location: index.php");
        exit;
    }

    // 3. 사용자 정보 확인
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    // 4. 비밀번호 확인 및 로그인 처리
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(); // 보안을 위해 세션 ID 갱신
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    } else {
        $_SESSION['error'] = '비밀번호가 일치하지 않습니다.';
    }
}

// 5. 메인 페이지로 리디렉션
header("location: index.php");
exit;
?>