// File: app/assets/js/main.js

document.addEventListener('DOMContentLoaded', () => {
    // 다크 모드 토글 스위치
    const themeSwitch = document.getElementById('theme-switch');
    if (themeSwitch) {
        if (localStorage.getItem('theme') === 'dark') {
            themeSwitch.checked = true;
        }
        themeSwitch.addEventListener('change', function(event) {
            if (event.currentTarget.checked) {
                document.documentElement.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark-mode');
                localStorage.setItem('theme', 'light');
            }
        });
    }

    // --- 이미지 라이트박스 처리 ---
    const lightbox = document.getElementById('image-lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const lightboxClose = document.getElementById('lightbox-close');

    if (lightbox && lightboxImage && lightboxClose) {
        document.body.addEventListener('click', (e) => {
            if (e.target.classList.contains('memo-thumbnail')) {
                lightboxImage.src = e.target.dataset.original;
                lightbox.classList.remove('hidden');
                lightbox.classList.add('flex');
            }
        });

        const closeLightbox = () => {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            lightboxImage.src = '';
        };

        lightboxClose.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) closeLightbox();
        });
    }

    // --- 새로운 검색 로직 추가 ---
    const googleForm = document.getElementById('google-search-form');
    const googleInput = document.getElementById('google-search-input');
    const naverForm = document.getElementById('naver-search-form');
    const naverInput = document.getElementById('naver-search-input');

    // Google 검색 폼 처리
    if (googleForm && googleInput) {
        googleForm.addEventListener('submit', function(event) {
            // 1. 기본 제출 동작을 막습니다.
            event.preventDefault(); 
            
            const searchValue = googleInput.value.trim();

            // 2. 입력값이 '??'인지 확인합니다.
            if (searchValue === '??') {
                window.open('https://google.com', '_blank');
            } else {
                // 3. 일반 검색을 수행합니다.
                const searchUrl = `https://www.google.com/search?q=${encodeURIComponent(searchValue)}`;
                window.open(searchUrl, '_blank');
            }
            
            // 4. 동작 후 검색창 내용을 지웁니다.
            googleForm.reset();
        });
    }

    // Naver 검색 폼 처리
    if (naverForm && naverInput) {
        naverForm.addEventListener('submit', function(event) {
            // 1. 기본 제출 동작을 막습니다.
            event.preventDefault();
            
            const searchValue = naverInput.value.trim();

            // 2. 입력값이 '??'인지 확인합니다.
            if (searchValue === '??') {
                window.open('https://naver.com', '_blank');
            } else {
                // 3. 일반 검색을 수행합니다.
                const searchUrl = `https://search.naver.com/search.naver?query=${encodeURIComponent(searchValue)}`;
                window.open(searchUrl, '_blank');
            }

            // 4. 동작 후 검색창 내용을 지웁니다.
            naverForm.reset();
        });
    }
});

// 그룹 펼치기/닫기
function openGroup(content, icon) {
    content.style.paddingTop = '1rem';
    content.style.paddingBottom = '1rem';
    content.style.maxHeight = content.scrollHeight + 20 + "px";
    icon.style.transform = 'rotate(180deg)';
    setTimeout(() => content.style.maxHeight = 'none', 300);
}

function closeGroup(content, icon) {
    content.style.maxHeight = content.scrollHeight + "px";
    content.offsetHeight;
    content.style.maxHeight = '0px';
    content.style.paddingTop = '0';
    content.style.paddingBottom = '0';
    icon.style.transform = 'rotate(0deg)';
}

function toggleGroup(headerElement) {
    const content = headerElement.nextElementSibling;
    const icon = headerElement.querySelector('svg');
    if (content.style.maxHeight && content.style.maxHeight !== '0px' && content.style.maxHeight !== 'none') {
        closeGroup(content, icon);
    } else {
        openGroup(content, icon);
    }
}