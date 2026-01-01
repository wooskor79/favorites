// 파일명: app/assets/js/admin.js

// HTML 특수문자 이스케이프 함수
function escapeHTML(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/[&<>'"]/g, 
        tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
    );
}

// --- 이미지 업로드 및 미리보기 관련 함수 ---
const MAX_IMAGES = 5;

async function uploadAndPreviewImage(file, previewContainer) {
    const currentImages = previewContainer.querySelectorAll('.image-preview-wrapper').length;
    if (currentImages >= MAX_IMAGES) {
        alert(`이미지는 최대 ${MAX_IMAGES}개까지만 추가할 수 있습니다.`);
        return;
    }

    const formData = new FormData();
    formData.append('image', file);

    try {
        const res = await fetch('actions/upload_image.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (res.ok && data.filePath) {
            const previewWrapper = document.createElement('div');
            previewWrapper.className = 'image-preview-wrapper';
            previewWrapper.dataset.filePath = data.filePath;

            const img = document.createElement('img');
            img.src = data.filePath;
            img.className = 'w-24 h-24 object-cover rounded-lg border';
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-image-btn';
            removeBtn.innerHTML = '&times;';
            removeBtn.onclick = () => previewWrapper.remove();

            previewWrapper.appendChild(img);
            previewWrapper.appendChild(removeBtn);
            previewContainer.appendChild(previewWrapper);
        } else {
            alert('이미지 업로드 실패: ' + (data.error || '알 수 없는 오류'));
        }
    } catch (error) {
        console.error('Upload error:', error);
        alert('이미지 업로드 중 오류가 발생했습니다.');
    }
}

function handlePaste(event, previewContainer) {
    const items = (event.clipboardData || window.clipboardData).items;
    for (const item of items) {
        if (item.type.indexOf('image') !== -1) {
            event.preventDefault();
            const file = item.getAsFile();
            uploadAndPreviewImage(file, previewContainer);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const themeSwitch = document.getElementById('theme-switch');
    if (themeSwitch) {
        if (localStorage.getItem('theme') === 'dark') themeSwitch.checked = true;
        themeSwitch.addEventListener('change', (event) => {
            document.documentElement.classList.toggle('dark-mode', event.currentTarget.checked);
            localStorage.setItem('theme', event.currentTarget.checked ? 'dark' : 'light');
        });
    }

    const memoForm = document.getElementById("memoForm");
    if (memoForm) {
        const titleInput = document.getElementById("memo-title");
        const contentInput = document.getElementById("memo-content");
        const newMemoPreviews = document.getElementById('new-memo-previews');
        contentInput.addEventListener('paste', (e) => handlePaste(e, newMemoPreviews));

        memoForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const title = titleInput.value.trim();
            const content = contentInput.value.trim();
            const imagePaths = Array.from(newMemoPreviews.querySelectorAll('.image-preview-wrapper'))
                .map(wrapper => wrapper.dataset.filePath);

            if (!title) return alert("제목을 입력하세요.");
            if (!content && imagePaths.length === 0) return alert("내용을 입력하거나 이미지를 추가해야 합니다.");

            const formData = new FormData();
            formData.append('title', title);
            formData.append('content', content);
            formData.append('images', JSON.stringify(imagePaths));

            try {
                const res = await fetch("actions/add_memo.php", {
                    method: "POST",
                    body: new URLSearchParams(formData)
                });
                if (res.ok) window.location.reload();
                else alert("메모 추가 실패: " + await res.text());
            } catch (error) {
                console.error('Error:', error);
                alert("오류가 발생했습니다.");
            }
        });
    }

    const lightbox = document.getElementById('image-lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const lightboxClose = document.getElementById('lightbox-close');

    if(lightbox) {
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
});

function showAdminConfirm(buttonElement) {
    const defaultView = buttonElement.closest('.admin-default-view');
    if (defaultView) {
        const confirmView = defaultView.nextElementSibling;
        defaultView.classList.add('hidden');
        if(confirmView) {
            confirmView.classList.remove('hidden');
            confirmView.classList.add('flex');
        }
    }
}

function hideAdminConfirm(buttonElement) {
    const confirmView = buttonElement.closest('.admin-confirm-view');
    if (confirmView) {
        const defaultView = confirmView.previousElementSibling;
        confirmView.classList.add('hidden');
        confirmView.classList.remove('flex');
        if(defaultView) defaultView.classList.remove('hidden');
    }
}

function showEditFavorite(id) {
    const row = document.getElementById(`fav-row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
    row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
}

function hideEditFavorite(id) {
    const row = document.getElementById(`fav-row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
    row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
}

async function saveFavorite(id) {
    const row = document.getElementById(`fav-row-${id}`);
    const aliasInput = row.querySelector('td:nth-child(1) .edit-mode');
    const urlInput = row.querySelector('td:nth-child(2) .edit-mode');
    const formData = new FormData();
    formData.append('id', id);
    formData.append('alias', aliasInput.value.trim());
    formData.append('url', urlInput.value.trim());
    try {
        const res = await fetch('actions/edit_favorite.php', { method: 'POST', body: new URLSearchParams(formData) });
        const data = await res.json();
        if (res.ok) {
            row.querySelector('td:nth-child(1) .view-mode').textContent = data.alias;
            const link = row.querySelector('td:nth-child(2) .view-mode a');
            link.href = data.url; link.textContent = data.url;
            hideEditFavorite(id);
        } else { alert('즐겨찾기 저장 실패: ' + (data.error || '알 수 없는 오류')); }
    } catch (error) { alert('저장 중 오류가 발생했습니다.'); }
}

function showEditLink(id) {
    const row = document.getElementById(`link-row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
    row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
}

function hideEditLink(id) {
    const row = document.getElementById(`link-row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
    row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
}

async function saveLink(id) {
    const row = document.getElementById(`link-row-${id}`);
    const titleInput = row.querySelector('td:nth-child(1) .edit-mode');
    const urlInput = row.querySelector('td:nth-child(2) .edit-mode');
    const formData = new FormData();
    formData.append('id', id);
    formData.append('title', titleInput.value.trim());
    formData.append('url', urlInput.value.trim());
    try {
        const res = await fetch('actions/edit_link.php', { method: 'POST', body: new URLSearchParams(formData) });
        const data = await res.json();
        if (res.ok) {
            row.querySelector('td:nth-child(1) .view-mode').textContent = data.title;
            const link = row.querySelector('td:nth-child(2) .view-mode a');
            link.href = data.url; link.textContent = data.url;
            hideEditLink(id);
        } else { alert('빠른 링크 저장 실패: ' + (data.error || '알 수 없는 오류')); }
    } catch (error) { alert('저장 중 오류가 발생했습니다.'); }
}

// 순서 변경 함수 추가
async function moveLink(id, direction) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('direction', direction);

    try {
        const res = await fetch('actions/update_link_order.php', {
            method: 'POST',
            body: new URLSearchParams(formData)
        });
        const data = await res.json();
        if (res.ok && data.success) {
            window.location.reload();
        } else {
            alert('순서 변경 실패: ' + (data.error || data.message || '알 수 없는 오류'));
        }
    } catch (error) {
        alert('서버 통신 중 오류가 발생했습니다.');
    }
}

// ... (나머지 기존 메모/정보카드 수정 함수들 생략)

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('클립보드에 복사되었습니다: ' + text);
    }, () => {
        alert('클립보드 복사에 실패했습니다.');
    });
}