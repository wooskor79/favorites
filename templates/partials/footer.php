<?php
// File: app/templates/partials/footer.php
?>
    </div>
    
    <div id="image-lightbox" class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50 p-4">
        <span id="lightbox-close" class="absolute top-4 right-6 text-white text-5xl cursor-pointer hover:text-gray-300">&times;</span>
        <img id="lightbox-image" src="" alt="Enlarged image" class="max-w-[95vw] max-h-[95vh] object-contain">
    </div>

    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>