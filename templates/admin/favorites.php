<?php
// File: app/templates/admin/favorites.php
?>
<div class="bg-white p-6 rounded-xl shadow-md mb-8">
    <h2 class="text-2xl font-semibold mb-4 text-gray-700">새 즐겨찾기 추가</h2>
    <form action="actions/add_favorite.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
        <input type="text" name="alias" placeholder="별칭" required class="md:col-span-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="text" name="url" placeholder="URL 주소" required class="md:col-span-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="md:col-span-1 flex gap-2">
             <button type="submit" class="flex-grow bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg">추가</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="min-w-full leading-normal">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">별칭</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">URL</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">관리</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($favorites)): ?>
                <tr><td colspan="3" class="text-center py-10 text-gray-500">생성된 즐겨찾기가 없습니다.</td></tr>
            <?php else: ?>
                <?php foreach($favorites as $fav): ?>
                <tr id="fav-row-<?php echo $fav['id']; ?>" class="hover:bg-gray-50">
                    <td class="px-5 py-4 border-b border-gray-200 text-sm align-middle">
                        <span class="view-mode"><?php echo htmlspecialchars($fav['alias']); ?></span>
                        <input type="text" value="<?php echo htmlspecialchars($fav['alias']); ?>" class="edit-mode hidden w-full p-2 border rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm align-middle max-w-xs">
                        <span class="view-mode"><a href="<?php echo htmlspecialchars($fav['url']); ?>" target="_blank" class="text-blue-500 hover:underline truncate block" title="<?php echo htmlspecialchars($fav['url']); ?>"><?php echo htmlspecialchars($fav['url']); ?></a></span>
                        <input type="text" value="<?php echo htmlspecialchars($fav['url']); ?>" class="edit-mode hidden w-full p-2 border rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm align-middle">
                        <div class="view-mode">
                            <div class="admin-default-view flex items-center gap-3">
                                <button type="button" onclick="showEditFavorite(<?php echo $fav['id']; ?>)" class="text-indigo-600 hover:text-indigo-900">수정</button>
                                <button type="button" onclick="showAdminConfirm(this)" class="text-red-600 hover:text-red-900">삭제</button>
                            </div>
                            <div class="admin-confirm-view hidden items-center gap-2">
                                 <a href="actions/delete_favorite.php?id=<?php echo $fav['id']; ?>" class="font-bold text-red-600 hover:underline">예</a>
                                 <span class="text-gray-300">|</span>
                                 <button type="button" onclick="hideAdminConfirm(this)" class="font-bold text-gray-600 hover:underline">아니오</button>
                            </div>
                        </div>
                        <div class="edit-mode hidden items-center gap-2">
                            <button onclick="saveFavorite(<?php echo $fav['id']; ?>)" class="font-bold text-green-600 hover:underline">저장</button>
                            <span class="text-gray-300">|</span>
                            <button onclick="hideEditFavorite(<?php echo $fav['id']; ?>)" class="font-bold text-gray-600 hover:underline">취소</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>