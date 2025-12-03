<?php
// File: src/admin_includes/memos.php
// 메모 관리 탭의 내용
?>
<div class="bg-white p-6 rounded-xl shadow-md mb-8">
    <h2 class="text-2xl font-semibold mb-4 text-gray-700">새 메모 추가</h2>
    <form id="memoForm" class="flex flex-col gap-4">
        <input type="text" id="memo-title" placeholder="제목" required class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <textarea id="memo-content" placeholder="메모 내용을 입력하거나 이미지를 붙여넣으세요 (최대 5개)" class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-24"></textarea>
        <div id="new-memo-previews" class="flex flex-wrap gap-2 mt-2"></div>
        <button type="submit" class="self-end bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg">추가</button>
    </form>
</div>

<div id="memo-list" class="bg-white rounded-xl shadow-md p-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-700">메모 목록</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($memos as $memo): ?>
        <div id="memo-<?php echo $memo['id']; ?>" class="bg-gray-50 p-4 rounded-lg border flex flex-col">
            <div class="memo-display-view">
                <div class="flex justify-between items-start">
                    <h3 class="font-bold text-lg text-gray-800 break-all memo-title-text"><?php echo htmlspecialchars($memo['title']); ?></h3>
                    <div class="flex items-center flex-shrink-0 ml-2 gap-2">
                        <button onclick="showEditMemo(<?php echo $memo['id']; ?>, <?php echo htmlspecialchars(json_encode($memo['images'] ?? '[]')); ?>)" class="text-gray-400 hover:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L14.732 3.732z"></path></svg>
                        </button>
                        <div class="admin-default-view">
                            <button type="button" onclick="showAdminConfirm(this)" class="text-red-600 hover:text-red-900"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd"></path></svg></button>
                        </div>
                        <div class="admin-confirm-view hidden items-center gap-2 text-sm">
                            <button type="button" onclick="deleteMemo(<?php echo $memo['id']; ?>)" class="font-bold text-red-600 hover:underline">예</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="hideAdminConfirm(this)" class="font-bold text-gray-600 hover:underline">아니오</button>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 my-2 memo-date-text"><?php echo $memo['created_at']; ?></p>
                <p class="text-gray-700 whitespace-pre-wrap break-words memo-content-text"><?php echo htmlspecialchars($memo['content']); ?></p>
                <div class="memo-images-container mt-2 flex flex-wrap gap-2">
                    <?php
                    $images = json_decode($memo['images'] ?? '[]', true);
                    if ($images && is_array($images)) {
                        foreach ($images as $imgPath) {
                            $originalPath = str_replace('/cache/', '/', $imgPath);
                            echo '<img src="'.htmlspecialchars($imgPath).'" data-original="'.htmlspecialchars($originalPath).'" class="memo-thumbnail w-20 h-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity">';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="memo-edit-view hidden">
                <div class="flex flex-col gap-2">
                    <input type="text" value="<?php echo htmlspecialchars($memo['title']); ?>" class="memo-edit-title p-2 border rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <textarea class="memo-edit-content p-2 border rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 h-24"><?php echo htmlspecialchars($memo['content']); ?></textarea>
                    <div class="edit-memo-previews flex flex-wrap gap-2 mt-2"></div>
                    <div class="flex justify-end gap-2 mt-2">
                        <button onclick="hideEditMemo(<?php echo $memo['id']; ?>)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-1 px-3 rounded-lg text-sm">취소</button>
                        <button onclick="saveMemo(<?php echo $memo['id']; ?>)" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded-lg text-sm">저장</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="image-lightbox" class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50 p-4">
    <span id="lightbox-close" class="absolute top-4 right-6 text-white text-5xl cursor-pointer hover:text-gray-300">&times;</span>
    <img id="lightbox-image" src="" alt="Enlarged image" class="max-w-[95vw] max-h-[95vh] object-contain">
</div>