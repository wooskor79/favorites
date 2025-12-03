<?php
// File: app/admin.php

// 현재 페이지가 관리자 페이지임을 정의합니다.
define('IS_ADMIN_PAGE', true);

require_once 'core/init.php';

if (!$is_loggedin) {
    header("location: index.php");
    exit;
}

$current_tab = $_GET['tab'] ?? 'memos';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 페이지</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <script>
        (function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
    <style type="text/tailwindcss">
        .tab-link {
            @apply flex-grow justify-center text-center px-4 py-2 font-semibold text-sm rounded-lg text-gray-600 hover:bg-gray-100 transition-colors;
        }
        .tab-link.active {
            @apply bg-blue-500 text-white shadow hover:bg-blue-600;
        }
        .dark-mode .tab-link {
            @apply text-gray-300 hover:bg-gray-700;
        }
        .dark-mode .tab-link.active {
            @apply bg-blue-600 text-white hover:bg-blue-700;
        }
    </style>
</head>
<body class="font-sans">
    <div class="container mx-auto px-4 py-8">
        <?php include 'templates/partials/admin_header.php'; ?>

        <div class="bg-white p-2 rounded-xl shadow-md mb-8">
            <nav class="flex items-center space-x-2" aria-label="Tabs">
                <a href="?tab=memos" class="tab-link <?php if($current_tab === 'memos') echo 'active'; ?>">메모</a>
                <a href="?tab=info_cards" class="tab-link <?php if($current_tab === 'info_cards') echo 'active'; ?>">정보 카드</a>
                <a href="?tab=favorites" class="tab-link <?php if($current_tab === 'favorites') echo 'active'; ?>">즐겨찾기</a>
                <a href="?tab=quick_links" class="tab-link <?php if($current_tab === 'quick_links') echo 'active'; ?>">빠른 링크</a>
                <a href="?tab=url_shortener" class="tab-link <?php if($current_tab === 'url_shortener') echo 'active'; ?>">URL줄이기</a>
            </nav>
        </div>

        <div>
            <?php
            if ($current_tab === 'memos') {
                include 'templates/admin/memos.php';
            } elseif ($current_tab === 'info_cards') {
                include 'templates/admin/info_cards.php';
            } elseif ($current_tab === 'favorites') {
                include 'templates/admin/favorites.php';
            } elseif ($current_tab === 'quick_links') {
                include 'templates/admin/quick_links.php';
            } elseif ($current_tab === 'url_shortener') {
                include 'templates/admin/url_shortener.php';
            }
            ?>
        </div>
    </div>
    <script src="assets/js/admin.js?v=<?php echo time(); ?>"></script>
</body>
</html>