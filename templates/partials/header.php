<?php
// File: app/templates/partials/header.php
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>즐겨찾기</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <script>
        (function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
</head>
<body class="font-sans">
    <div class="container mx-auto px-4 py-8">
        <header class="bg-white p-6 rounded-xl shadow-md mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-4xl font-bold text-gray-800">우성이 시작화면</h1>
                <div class="flex items-center gap-4">
                    <div class="theme-switch-wrapper">
                        <label class="theme-switch" for="theme-switch">
                            <input type="checkbox" id="theme-switch" />
                            <div class="slider round"></div>
                        </label>
                    </div>
                    <?php if ($is_loggedin): ?>
                        <div class="flex items-center gap-4">
                            <a href="admin.php" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">관리자 페이지</a>
                            <span class="text-gray-700">환영합니다, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>님</span>
                            <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">로그아웃</a>
                        </div>
                    <?php else: ?>
                        <form action="login.php" method="post" class="flex items-center gap-2">
                             <input type="password" name="password" placeholder="비밀번호" required
                                   class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                             <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">로그인</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </header>