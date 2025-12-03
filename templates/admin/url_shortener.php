<?php
// File: app/templates/admin/url_shortener.php
// 다른 탭(빠른 링크 등)과 동일한 UI로 단축 URL을 직접 관리합니다.

$search = $_GET['q'] ?? '';
?>

<!-- 1. 상단: 새 단축 URL 생성 폼 -->
<div class="bg-white p-6 rounded-xl shadow-md mb-8">
    <h2 class="text-2xl font-semibold mb-4 text-gray-700">새 URL 줄이기</h2>
    <form action="actions/add_short_url.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div class="md:col-span-1">
            <label for="long_url" class="block text-sm font-medium text-gray-700 mb-1">원본 URL</label>
            <input type="text" id="long_url" name="long_url" placeholder="https://example.com/long-url" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="md:col-span-1">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">별칭 (관리용 제목)</label>
            <input type="text" id="title" name="title" placeholder="예: 알리익스프레스 특가" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="md:col-span-1 flex">
            <button type="submit" class="flex-grow bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg h-full transition duration-300">생성하기</button>
        </div>
    </form>
</div>

<!-- 2. 하단: 단축 URL 목록 및 관리 테이블 -->
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <!-- 검색 기능 -->
    <div class="p-4 border-b border-gray-200 bg-gray-50">
        <form method="get" class="flex gap-2">
            <input type="hidden" name="tab" value="url_shortener">
            <input type="search" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="별칭 또는 코드로 검색..." class="w-full md:w-1/3 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">검색</button>
            <?php if(!empty($search)): ?>
                <a href="?tab=url_shortener" class="bg-red-200 hover:bg-red-300 text-red-800 font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center">초기화</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- 테이블 -->
    <div class="overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/5">단축 URL</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/5">별칭</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/4">원본 URL</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">클릭수</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">생성일</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">관리</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($shortener_links)): ?>
                    <tr><td colspan="6" class="text-center py-10 text-gray-500">생성된 단축 URL이 없습니다.</td></tr>
                <?php else: ?>
                    <?php foreach ($shortener_links as $link):
                        $short_url = 'https://s.wooskor.com/' . htmlspecialchars($link['code']);
                    ?>
                    <tr id="short-link-row-<?php echo $link['id']; ?>" class="hover:bg-gray-50 transition duration-150">
                        <!-- 단축 URL (복사 기능 포함) -->
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo $short_url; ?>" target="_blank" class="text-blue-500 hover:underline font-bold truncate block max-w-[150px]" title="<?php echo $short_url; ?>">
                                    /<?php echo htmlspecialchars($link['code']); ?>
                                </a>
                                <button onclick="copyToClipboard('<?php echo $short_url; ?>')" class="text-gray-400 hover:text-indigo-600 transition-colors" title="복사">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            </div>
                        </td>

                        <!-- 별칭 (인라인 수정 가능) -->
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            <span class="view-mode font-medium text-gray-700"><?php echo htmlspecialchars($link['title']); ?></span>
                            <input type="text" value="<?php echo htmlspecialchars($link['title']); ?>" class="edit-mode hidden w-full p-2 border rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </td>

                        <!-- 원본 URL -->
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            <div class="max-w-xs truncate" title="<?php echo htmlspecialchars($link['long_url']); ?>">
                                <a href="<?php echo htmlspecialchars($link['long_url']); ?>" target="_blank" class="text-gray-500 hover:text-blue-500 hover:underline">
                                    <?php echo htmlspecialchars($link['long_url']); ?>
                                </a>
                            </div>
                        </td>

                        <!-- 클릭수 -->
                        <td class="px-5 py-4 border-b border-gray-200 text-sm text-center">
                            <span class="bg-gray-100 text-gray-600 py-1 px-2 rounded-full text-xs font-bold">
                                <?php echo number_format($link['click_count']); ?>
                            </span>
                        </td>

                        <!-- 생성일 -->
                        <td class="px-5 py-4 border-b border-gray-200 text-sm text-center text-gray-500">
                            <?php echo date('y.m.d', strtotime($link['created_at'])); ?>
                        </td>

                        <!-- 관리 버튼 (수정/삭제) -->
                        <td class="px-5 py-4 border-b border-gray-200 text-sm text-center">
                            <div class="view-mode flex justify-center">
                                <div class="admin-default-view flex items-center gap-3">
                                    <button type="button" onclick="showEditShortUrl(<?php echo $link['id']; ?>)" class="text-indigo-600 hover:text-indigo-900 font-medium transition duration-150">수정</button>
                                    <button type="button" onclick="showAdminConfirm(this)" class="text-red-600 hover:text-red-900 font-medium transition duration-150">삭제</button>
                                </div>
                                <div class="admin-confirm-view hidden items-center gap-2 text-sm bg-red-50 px-2 py-1 rounded">
                                    <span class="text-red-600 text-xs">삭제?</span>
                                    <a href="actions/delete_short_url.php?id=<?php echo $link['id']; ?>" class="font-bold text-red-600 hover:underline">예</a>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" onclick="hideAdminConfirm(this)" class="font-bold text-gray-600 hover:underline">아니오</button>
                                </div>
                            </div>
                            <div class="edit-mode hidden flex justify-center items-center gap-2">
                                <button onclick="saveShortUrl(<?php echo $link['id']; ?>)" class="text-green-600 hover:text-green-900 font-bold transition duration-150">저장</button>
                                <span class="text-gray-300">|</span>
                                <button onclick="hideEditShortUrl(<?php echo $link['id']; ?>)" class="text-gray-600 hover:text-gray-900 font-medium transition duration-150">취소</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>