<?php
// File: app/templates/admin/info_cards.php
?>

<div class="bg-white p-6 rounded-xl shadow-md mb-8">
    <h2 class="text-2xl font-semibold mb-4 text-gray-700">정보 카드 그룹 관리 (최대 4개)</h2>
    <?php if (count($info_card_groups) < 4): ?>
        <form action="actions/add_info_card_group.php" method="POST" class="flex gap-4 items-end border-t pt-4">
            <div class="flex-grow">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">새 그룹 제목</label>
                <input type="text" name="title" placeholder="예: 미디어, 유틸리티 등" required class="w-full p-3 border border-gray-300 rounded-lg">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg h-full">그룹 추가</button>
        </form>
    <?php else: ?>
        <p class="text-center text-gray-500 pt-4 border-t">그룹이 4개 모두 생성되어 더 이상 추가할 수 없습니다.</p>
    <?php endif; ?>
</div>

<div class="space-y-8">
    <?php foreach ($info_card_groups as $group): ?>
        <div class="bg-white p-6 rounded-xl shadow-md">
            <div id="group-header-<?php echo $group['id']; ?>" class="flex justify-between items-center mb-4 pb-4 border-b">
                <div class="flex-grow">
                    <span class="view-mode">
                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($group['title']); ?></h3>
                    </span>
                    <span class="edit-mode hidden">
                        <input type="text" value="<?php echo htmlspecialchars($group['title']); ?>" class="w-full p-2 border rounded-lg text-xl font-bold">
                    </span>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <div class="view-mode flex items-center gap-3">
                        <button onclick="showEditGroupTitle(<?php echo $group['id']; ?>)" class="text-sm text-indigo-600 hover:underline">제목 수정</button>
                        <a href="actions/delete_info_card_group.php?id=<?php echo $group['id']; ?>" onclick="return confirm('이 그룹과 모든 내용이 삭제됩니다. 계속하시겠습니까?')" class="text-sm text-red-500 hover:underline">그룹 삭제</a>
                    </div>
                    <div class="edit-mode hidden flex items-center gap-2">
                        <button onclick="saveGroupTitle(<?php echo $group['id']; ?>)" class="text-sm font-bold text-green-600 hover:underline">저장</button>
                        <button onclick="hideEditGroupTitle(<?php echo $group['id']; ?>)" class="text-sm font-bold text-gray-600 hover:underline">취소</button>
                    </div>
                </div>
            </div>

            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="pb-2 text-left text-xs font-semibold text-gray-600 uppercase">내용</th>
                        <th class="pb-2 text-left text-xs font-semibold text-gray-600 uppercase">URL</th>
                        <th class="pb-2 text-left text-xs font-semibold text-gray-600 uppercase">관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($group['items'] as $item): ?>
                        <tr id="item-row-<?php echo $item['id']; ?>">
                            <td class="py-2 border-b border-gray-200">
                                <span class="view-mode"><?php echo htmlspecialchars($item['content']); ?></span>
                                <textarea class="edit-mode hidden w-full p-2 border rounded-lg"><?php echo htmlspecialchars($item['content']); ?></textarea>
                            </td>
                            <td class="py-2 border-b border-gray-200">
                                <span class="view-mode"><a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($item['url']); ?></a></span>
                                <input type="text" value="<?php echo htmlspecialchars($item['url']); ?>" class="edit-mode hidden w-full p-2 border rounded-lg">
                            </td>
                            <td class="py-2 border-b border-gray-200 w-40">
                                <div class="view-mode flex items-center gap-3">
                                    <button onclick="showEditItem(<?php echo $item['id']; ?>)" class="text-indigo-600 hover:text-indigo-900">수정</button>
                                    <a href="actions/delete_info_card_item.php?id=<?php echo $item['id']; ?>" class="text-red-600 hover:text-red-900">삭제</a>
                                </div>
                                <div class="edit-mode hidden items-center gap-2">
                                    <button onclick="saveItem(<?php echo $item['id']; ?>)" class="font-bold text-green-600 hover:underline">저장</button>
                                    <button onclick="hideEditItem(<?php echo $item['id']; ?>)" class="font-bold text-gray-600 hover:underline">취소</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if (count($group['items']) < 5): ?>
                <form action="actions/add_info_card_item.php" method="POST" class="mt-4 pt-4 border-t flex gap-4">
                    <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
                    <input type="text" name="content" placeholder="내용" required class="w-1/2 p-2 border rounded-lg">
                    <input type="text" name="url" placeholder="URL" required class="w-1/2 p-2 border rounded-lg">
                    <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded-lg">추가</button>
                </form>
            <?php else: ?>
                <p class="text-center text-sm text-gray-500 mt-4 pt-4 border-t">내용을 5개까지 모두 추가하여 더 이상 추가할 수 없습니다.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>