<?php
// File: app/core/init.php

// =============== 1. 기본 설정 ===============
session_start();


// =============== 2. 데이터베이스 연결 ===============
define('DB_HOST', 'localhost');
define('DB_NAME', 'favorites_db');
define('DB_USER', 'root');
define('DB_PASS', 'dldntjd@D79');
// [수정] 실제 생성한 단축 URL 데이터베이스 이름인 'short_url'로 변경
define('DB_SHORTENER_NAME', 'short_url');

$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

function get_shortener_db_connection() {
    try {
        // mysqli 에러 리포팅을 예외 모드로 설정 (연결 실패 시 try-catch로 잡기 위함)
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        $conn_shortener = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_SHORTENER_NAME);
        $conn_shortener->set_charset("utf8mb4");
        return $conn_shortener;
    } catch (Exception $e) {
        // 연결 실패 시 페이지가 멈추지 않도록 null 반환 (로그에는 남길 수 있음)
        // error_log("Shortener DB Connection Error: " . $e->getMessage());
        return null;
    }
}

// =============== 3. 데이터 조회 ===============

// 즐겨찾기 조회
$favorites = [];
$fav_result = $conn->query("SELECT * FROM favorites ORDER BY created_at DESC");
if ($fav_result) {
    while($row = $fav_result->fetch_assoc()) { $favorites[] = $row; }
}

// 메모 조회
$memos = [];
$memo_result = $conn->query("SELECT id, title, content, images, created_at FROM memos ORDER BY created_at DESC");
if ($memo_result) {
    while($row = $memo_result->fetch_assoc()) { $memos[] = $row; }
}

// 빠른 링크 조회
$quick_links = [];
$quick_links_result = $conn->query("SELECT * FROM quick_links ORDER BY created_at ASC");
if ($quick_links_result) {
    while($row = $quick_links_result->fetch_assoc()) { $quick_links[] = $row; }
}


// 정보 카드 그룹 및 아이템 조회 (새로운 로직)
$info_card_groups = [];
$group_result = $conn->query("SELECT * FROM info_card_groups ORDER BY sort_order ASC, id ASC");
if ($group_result) {
    while($row = $group_result->fetch_assoc()) {
        $row['items'] = []; // 각 그룹에 아이템을 담을 배열 초기화
        $info_card_groups[$row['id']] = $row;
    }
}

if (!empty($info_card_groups)) {
    $item_result = $conn->query("SELECT * FROM info_card_items ORDER BY id ASC");
    if ($item_result) {
        while($item = $item_result->fetch_assoc()) {
            if (isset($info_card_groups[$item['group_id']])) {
                $info_card_groups[$item['group_id']]['items'][] = $item;
            }
        }
    }
}


// =============== 4. 변수 설정 ===============
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// 단축 URL 목록 조회 (Shortener DB 연동)
$shortener_links = [];
if ($is_loggedin && basename($_SERVER['PHP_SELF']) == 'admin.php' && ($_GET['tab'] ?? '') === 'url_shortener') {
    $conn_shortener = get_shortener_db_connection();
    if ($conn_shortener) {
        $search_query = $_GET['q'] ?? '';
        
        // [수정] 실제 DB 스키마(urls 테이블)에 맞게 쿼리 수정
        // short_code -> code, alias -> title 로 별칭을 주어 기존 템플릿과 호환되게 함
        // click_count는 urls 테이블에 없다면 0으로 처리
        $sql = "SELECT id, long_url, short_code AS code, alias AS title, created_at, 0 AS click_count FROM urls WHERE short_code IS NOT NULL";
        
        if (!empty($search_query)) {
            // 검색 조건도 실제 컬럼명(short_code, alias)으로 변경
            $sql .= " AND (short_code LIKE '%" . $conn_shortener->real_escape_string($search_query) . "%' OR alias LIKE '%" . $conn_shortener->real_escape_string($search_query) . "%')";
        }
        $sql .= " ORDER BY id DESC";
        
        try {
            $shortener_result = $conn_shortener->query($sql);
            if ($shortener_result) {
                while($row = $shortener_result->fetch_assoc()) { $shortener_links[] = $row; }
            }
        } catch (Exception $e) {
            // 쿼리 에러 처리 (테이블이 없을 경우 등)
        }
        $conn_shortener->close();
    }
}
?>