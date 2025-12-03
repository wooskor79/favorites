<?php
// File: app/templates/admin/url_shortener.php
$search = $_GET['q'] ?? '';
?>
<div class="bg-white p-6 rounded-xl shadow-md mb-8">
    <h2 class="text-2xl font-semibold mb-4 text-gray-700">새 URL줄이기</h2>
    <form action="actions/add_short_url.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div class="md:col-span-1">
            <label for="long_url" class="block text-sm font-medium text-gray-700 mb-1">원본 URL</label>
            <input type="text" id="long_url" name="long_url" placeholder="https://example.com/long-url" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="md:col-span-1">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">별칭 (검색용)</label>
            <input type="text" id="title" name="title" placeholder="예: 구글 메인" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="md:col-span-1 flex">
            <button type="submit" class="flex-grow bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg h-full">추가하기</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="p-4">
        <form method="get">
            <input type="hidden" name="tab" value="url_shortener">
            <div class="flex gap-2">
                <input type="search" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="별칭 또는 코드로 검색..." class="w-full p-2 border border-gray-300 rounded-lg">
                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">검색</button>
            </div>
        </form>
    </div>
    <table class="min-w-full leading-normal">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">단축 URL</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">별칭</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">원본 URL</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">클릭수</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">생성일</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">관리</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($shortener_links)): ?>
                <tr><td colspan="6" class="text-center py-10 text-gray-500">생성된 단축 URL이 없습니다.</td></tr>
            <?php else: ?>
                <?php foreach ($shortener_links as $link):
                    $short_url = 'https://s.wooskor.site/' . htmlspecialchars($link['code']);
                ?>
                <tr id="short-link-row-<?php echo $link['id']; ?>" class="hover:bg-gray-50">
                    <td class="px-5 py-4 border-b border-gray-200 text-sm">
                        <a href="<?php echo $short_url; ?>" target="_blank" class="text-blue-500 hover:underline font-semibold"><?php echo $short_url; ?></a>
                        <button onclick="copyToClipboard('<?php echo $short_url; ?>')" class="ml-2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 011-1h8a1 1 0 011 1v12a1 1 0 01-1 1h-8a1 1 0 01-1-1V3zM5 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H5z"></path></svg>
                        </button>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm">
                        <span class="view-mode"><?php echo htmlspecialchars($link['title']); ?></span>
                        <input type="text" value="<?php echo htmlspecialchars($link['title']); ?>" class="edit-mode hidden w-full p-2 border rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm max-w-xs truncate" title="<?php echo htmlspecialchars($link['long_url']); ?>">
                        <a href="<?php echo htmlspecialchars($link['long_url']); ?>" target="_blank" class="hover:underline"><?php echo htmlspecialchars($link['long_url']); ?></a>
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm"><?php echo $link['click_count']; ?></td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm"><?php echo $link['created_at']; ?></td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm">
                        <div class="view-mode">
                            <div class="admin-default-view flex items-center gap-3">
                                <button type="button" onclick="showEditShortUrl(<?php echo $link['id']; ?>)" class="text-indigo-600 hover:text-indigo-900">수정</button>
                                <button type="button" onclick="showAdminConfirm(this)" class="text-red-600 hover:text-red-900">삭제</button>
                            </div>
                            <div class="admin-confirm-view hidden items-center gap-2 text-sm">
                                <a href="actions/delete_short_url.php?id=<?php echo $link['id']; ?>" class="font-bold text-red-600 hover:underline">예</a>
                                <span class="text-gray-300">|</span>
                                <button type="button" onclick="hideAdminConfirm(this)" class="font-bold text-gray-600 hover:underline">아니오</button>
                            </div>
                        </div>
                        <div class="edit-mode hidden items-center gap-2">
                            <button onclick="saveShortUrl(<?php echo $link['id']; ?>)" class="font-bold text-green-600 hover:underline">저장</button>
                            <span class="text-gray-300">|</span>
                            <button onclick="hideEditShortUrl(<?php echo $link['id']; ?>)" class="font-bold text-gray-600 hover:underline">취소</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>