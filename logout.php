<?php
// File: src/logout.php
session_start();
$_SESSION = array(); // 모든 세션 변수 지우기
session_destroy(); // 세션 파괴
header("location: index.php"); // 메인 페이지로 리디렉션
exit;
?>

