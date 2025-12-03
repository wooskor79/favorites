<?php
// File: app/templates/partials/admin_header.php
?>
<header class="bg-white p-6 rounded-xl shadow-md mb-8 flex flex-wrap justify-between items-center gap-4">
    <h1 class="text-3xl font-bold text-gray-800">관리자 페이지</h1>
    <div class="flex items-center gap-4">
        <div class="theme-switch-wrapper">
            <label class="theme-switch" for="theme-switch">
                <input type="checkbox" id="theme-switch" />
                <div class="slider round"></div>
            </label>
        </div>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">메인으로 돌아가기</a>
    </div>
</header>