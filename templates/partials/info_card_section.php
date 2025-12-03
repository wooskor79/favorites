<?php
// File: app/templates/partials/info_card_section.php
?>
<div class="mb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($info_card_groups as $group): ?>
            <div class="info-card bg-white rounded-xl shadow-md p-6 h-full flex flex-col">
                <h3 class="font-bold text-xl text-gray-800 mb-4 pb-2 border-b"><?php echo htmlspecialchars($group['title']); ?></h3>
                <div class="flex-grow space-y-3">
                    <?php foreach ($group['items'] as $item): ?>
                        <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank" class="block text-blue-500 hover:underline" title="<?php echo htmlspecialchars($item['content']); ?>">
                            <?php echo htmlspecialchars($item['content']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>