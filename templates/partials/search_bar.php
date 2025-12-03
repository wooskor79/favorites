<?php
// File: src/includes/search_bar.php
?>
<div class="bg-white p-4 rounded-xl shadow-md mb-8 flex flex-wrap md:flex-nowrap gap-4">
    
    <div class="w-full md:w-1/2">
        <form id="google-search-form" action="https://www.google.com/search" method="GET" target="_blank" class="mb-2">
            <input id="google-search-input" type="text" name="q" placeholder="Google 검색..."
                   class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required autofocus>
        </form>
        <form id="naver-search-form" action="https://search.naver.com/search.naver" method="GET" target="_blank">
            <input id="naver-search-input" type="text" name="query" placeholder="Naver 검색..."
                   class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
        </form>
    </div>

    <div class="w-full md:w-1/2 md:pl-4 md:border-l border-gray-200">
        <?php if (!empty($quick_links)): ?>
            <div class="flex flex-wrap gap-3 items-start mt-1">
                <?php foreach ($quick_links as $link):
                    $title = htmlspecialchars($link['title']);
                    $url = htmlspecialchars($link['url']);
                    $class = 'quick-link-btn';
                    if (mb_strlen($title, 'UTF-8') > 6) {
                        $class .= ' two-line';
                    }
                ?>
                    <a href="<?php echo $url; ?>" target="_blank" class="<?php echo $class; ?>">
                        <?php echo $title; ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
             <div class="flex items-center justify-center h-full">
                <p class="text-gray-400">관리자 페이지에서 빠른 링크를 추가하세요.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
        <strong class="font-bold">오류:</strong>
        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
    </div>
<?php endif; ?>