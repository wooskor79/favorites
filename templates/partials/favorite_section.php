<?php
// File: app/templates/partials/favorite_section.php
?>
<main class="space-y-6">
    <div class="bg-white rounded-xl shadow-md group-container">
        <h2 class="text-2xl font-bold text-gray-700 p-4 border-b cursor-pointer flex justify-between items-center" onclick="toggleGroup(this)">
            <span>즐겨찾기</span>
            <svg class="w-6 h-6 transform transition-transform" style="transform: rotate(180deg);" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </h2>
        <div class="group-content p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                <?php foreach($favorites as $row): ?>
                <div class="bg-gray-50 p-4 rounded-lg border flex flex-col">
                    <div class="flex-grow">
                        <h3 class="font-bold text-lg break-all text-gray-800" title="<?php echo htmlspecialchars($row['alias']); ?>"><?php echo htmlspecialchars($row['alias']); ?></h3>
                        <p class="text-gray-500 text-sm truncate" title="<?php echo htmlspecialchars($row['url']); ?>"><?php echo htmlspecialchars($row['url']); ?></p>
                    </div>
                    <div class="mt-4">
                        <a href="<?php echo htmlspecialchars($row['url']); ?>" target="_blank" class="text-blue-500 hover:underline font-semibold">바로가기 &rarr;</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>
