<?php
// File: app/core/init.php

// =============== 1. 기본 설정 ===============
session_start();


// =============== 2. 데이터베이스 연결 ===============
define('DB_HOST', 'localhost');
define('DB_NAME', 'favorites_db');
define('DB_USER', 'root');
define('DB_PASS', 'dldntjd@D79');
define('DB_SHORTENER_NAME', 'shortener');

$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

function get_shortener_db_connection() {
    $conn_shortener = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_SHORTENER_NAME);
    if ($conn_shortener->connect_error) return null;
    $conn_shortener->set_charset("utf8mb4");
    return $conn_shortener;
}

// =============== 3. 데이터 조회 ===============

// ... (favorites, memos, quick_links 조회 코드는 기존과 동일) ...
$favorites = [];
$fav_result = $conn->query("SELECT * FROM favorites ORDER BY created_at DESC");
if ($fav_result) {
    while($row = $fav_result->fetch_assoc()) { $favorites[] = $row; }
}

$memos = [];
$memo_result = $conn->query("SELECT id, title, content, images, created_at FROM memos ORDER BY created_at DESC");
if ($memo_result) {
    while($row = $memo_result->fetch_assoc()) { $memos[] = $row; }
}

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

// ... (Shortener 연동 코드는 기존과 동일) ...
$shortener_links = [];
if ($is_loggedin && basename($_SERVER['PHP_SELF']) == 'admin.php' && ($_GET['tab'] ?? '') === 'url_shortener') {
    $conn_shortener = get_shortener_db_connection();
    if ($conn_shortener) {
        $search_query = $_GET['q'] ?? '';
        $sql = "SELECT * FROM links WHERE code IS NOT NULL";
        if (!empty($search_query)) {
            $sql .= " AND (code LIKE '%" . $conn_shortener->real_escape_string($search_query) . "%' OR title LIKE '%" . $conn_shortener->real_escape_string($search_query) . "%')";
        }
        $sql .= " ORDER BY id DESC";
        $shortener_result = $conn_shortener->query($sql);
        if ($shortener_result) {
            while($row = $shortener_result->fetch_assoc()) { $shortener_links[] = $row; }
        }
        $conn_shortener->close();
    }
}
?>