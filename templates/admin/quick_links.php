<?php
// File: app/templates/admin/quick_links.php
?>
<div class="bg-white p-6 rounded-xl shadow-md mb-8">
    <h2 class="text-2xl font-semibold mb-4 text-gray-700">새 링크 추가</h2>
    <form action="actions/add_link.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div class="md:col-span-1">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">링크 제목</label>
            <input type="text" name="title" placeholder="예: 구글" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="md:col-span-1">
            <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL 주소</label>
            <input type="text" name="url" placeholder="https://google.com" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="md:col-span-1 flex gap-2">
            <button type="submit" class="flex-grow bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg h-full">추가하기</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="min-w-full leading-normal">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">링크 제목</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">URL</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">생성일</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">관리</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($quick_links)): ?>
                <tr><td colspan="4" class="text-center py-10 text-gray-500">생성된 링크가 없습니다.</td></tr>
            <?php else: ?>
                <?php foreach ($quick_links as $link): ?>
                <tr id="link-row-<?php echo $link['id']; ?>" class="hover:bg-gray-50">
                    <td class="px-5 py-4 border-b border-gray-200 text-sm align-middle">
                        <span class="view-mode"><?php echo htmlspecialchars($link['title']); ?></span>
                        <input type="text" value="<?php echo htmlspecialchars($link['title']); ?>" class="edit-mode hidden w-full p-2 border rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm align-middle max-w-xs">
                        <span class="view-mode"><a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" class="text-blue-500 hover:underline truncate block" title="<?php echo htmlspecialchars($link['url']); ?>"><?php echo htmlspecialchars($link['url']); ?></a></span>
                        <input type="text" value="<?php echo htmlspecialchars($link['url']); ?>" class="edit-mode hidden w-full p-2 border rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm align-middle"><?php echo $link['created_at']; ?></td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm align-middle">
                         <div class="view-mode">
                            <div class="admin-default-view flex items-center gap-3">
                                <button type="button" onclick="showEditLink(<?php echo $link['id']; ?>)" class="text-indigo-600 hover:text-indigo-900">수정</button>
                                <button type="button" onclick="showAdminConfirm(this)" class="text-red-600 hover:text-red-900">삭제</button>
                            </div>
                            <div class="admin-confirm-view hidden items-center gap-2">
                                 <a href="actions/delete_link.php?id=<?php echo $link['id']; ?>" class="font-bold text-red-600 hover:underline">예</a>
                                 <span class="text-gray-300">|</span>
                                 <button type="button" onclick="hideAdminConfirm(this)" class="font-bold text-gray-600 hover:underline">아니오</button>
                            </div>
                        </div>
                        <div class="edit-mode hidden items-center gap-2">
                            <button onclick="saveLink(<?php echo $link['id']; ?>)" class="font-bold text-green-600 hover:underline">저장</button>
                            <span class="text-gray-300">|</span>
                            <button onclick="hideEditLink(<?php echo $link['id']; ?>)" class="font-bold text-gray-600 hover:underline">취소</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>