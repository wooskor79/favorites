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

// --- 정보 카드 그룹 제목 수정 (누락된 기능 추가) ---
function showEditGroupTitle(id) {
    const header = document.getElementById(`group-header-${id}`);
    header.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
    header.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
}

function hideEditGroupTitle(id) {
    const header = document.getElementById(`group-header-${id}`);
    header.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
    header.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
}

async function saveGroupTitle(id) {
    const header = document.getElementById(`group-header-${id}`);
    const titleInput = header.querySelector('.edit-mode input');
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('title', titleInput.value.trim());

    try {
        const res = await fetch('actions/edit_info_card_group.php', { method: 'POST', body: new URLSearchParams(formData) });
        const data = await res.json();
        if (res.ok) {
            header.querySelector('.view-mode h3').textContent = data.title;
            hideEditGroupTitle(id);
        } else {
            alert('그룹 제목 저장 실패: ' + (data.error || '알 수 없는 오류'));
        }
    } catch (error) {
        alert('저장 중 오류가 발생했습니다.');
    }
}

// --- 정보 카드 아이템 수정 (누락된 기능 추가) ---
function showEditItem(id) {
    const row = document.getElementById(`item-row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
    row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
}

function hideEditItem(id) {
    const row = document.getElementById(`item-row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
    row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
}

async function saveItem(id) {
    const row = document.getElementById(`item-row-${id}`);
    // textarea와 input 요소 찾기
    const contentInput = row.querySelector('td:nth-child(1) .edit-mode');
    const urlInput = row.querySelector('td:nth-child(2) .edit-mode');
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('content', contentInput.value.trim());
    formData.append('url', urlInput.value.trim());

    try {
        const res = await fetch('actions/edit_info_card_item.php', { method: 'POST', body: new URLSearchParams(formData) });
        const data = await res.json();
        if (res.ok) {
            row.querySelector('td:nth-child(1) .view-mode').textContent = data.content;
            const link = row.querySelector('td:nth-child(2) .view-mode a');
            link.href = data.url;
            link.textContent = data.url;
            hideEditItem(id);
        } else {
            alert('아이템 저장 실패: ' + (data.error || '알 수 없는 오류'));
        }
    } catch (error) {
        alert('저장 중 오류가 발생했습니다.');
    }
}

// --- 정보 카드 순서 변경 (신규 기능 추가) ---
async function moveInfoCardItem(id, direction) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('direction', direction);

    try {
        const res = await fetch('actions/update_info_card_item_order.php', {
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

// ... 기존 메모/단축 URL 관련 함수들은 유지 ...
function showEditMemo(id, imagesJson) {
    const memoCard = document.getElementById(`memo-${id}`);
    if (memoCard) {
        memoCard.querySelector('.memo-display-view').classList.add('hidden');
        const editView = memoCard.querySelector('.memo-edit-view');
        editView.classList.remove('hidden');

        const previewContainer = editView.querySelector('.edit-memo-previews');
        previewContainer.innerHTML = ''; 
        
        try {
            const images = JSON.parse(imagesJson);
            if (images && Array.isArray(images)) {
                images.forEach(filePath => {
                    const previewWrapper = document.createElement('div');
                    previewWrapper.className = 'image-preview-wrapper';
                    previewWrapper.dataset.filePath = filePath;
                    previewWrapper.innerHTML = `
                        <img src="${escapeHTML(filePath)}" class="w-24 h-24 object-cover rounded-lg border">
                        <button type="button" class="remove-image-btn" onclick="this.parentElement.remove()">&times;</button>
                    `;
                    previewContainer.appendChild(previewWrapper);
                });
            }
        } catch(e) { /* ignore */ }

        const editContent = editView.querySelector('.memo-edit-content');
        editContent.onpaste = (e) => handlePaste(e, previewContainer);
    }
}

function hideEditMemo(id) {
    const memoCard = document.getElementById(`memo-${id}`);
    if (memoCard) {
        const editContent = memoCard.querySelector('.memo-edit-content');
        if(editContent) editContent.onpaste = null;
        memoCard.querySelector('.memo-display-view').classList.remove('hidden');
        memoCard.querySelector('.memo-edit-view').classList.add('hidden');
    }
}

async function saveMemo(id) {
    const memoCard = document.getElementById(`memo-${id}`);
    const title = memoCard.querySelector('.memo-edit-title').value.trim();
    if (!title) return alert('제목을 입력해주세요.');
    
    const previewContainer = memoCard.querySelector('.edit-memo-previews');
    const imagePaths = Array.from(previewContainer.querySelectorAll('.image-preview-wrapper'))
        .map(wrapper => wrapper.dataset.filePath);

    const formData = new FormData();
    formData.append('id', id);
    formData.append('title', title);
    formData.append('content', memoCard.querySelector('.memo-edit-content').value.trim());
    formData.append('images', JSON.stringify(imagePaths));

    try {
        const res = await fetch('actions/edit_memo.php', { method: 'POST', body: new URLSearchParams(formData) });
        const data = await res.json();
        if (res.ok) {
            const displayView = memoCard.querySelector('.memo-display-view');
            displayView.querySelector('.memo-title-text').textContent = data.title;
            displayView.querySelector('.memo-content-text').textContent = data.content;
            
            const imageContainer = displayView.querySelector('.memo-images-container');
            imageContainer.innerHTML = '';
            const images = data.images ? JSON.parse(data.images) : [];
            images.forEach(path => {
                const originalPath = path.replace('/cache/', '/');
                const img = document.createElement('img');
                img.src = path;
                img.dataset.original = originalPath;
                img.className = 'memo-thumbnail w-20 h-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity';
                imageContainer.appendChild(img);
            });
            
            const editButton = displayView.querySelector('button[onclick^="showEditMemo"]');
            editButton.setAttribute('onclick', `showEditMemo(${id}, '${escapeHTML(data.images || '[]')}')`);

            hideEditMemo(id);
        } else {
            alert('메모 저장 실패: ' + (data.error || '알 수 없는 오류'));
        }
    } catch (error) { alert('저장 중 오류가 발생했습니다.'); }
}

async function deleteMemo(id) {
    try {
        const res = await fetch(`actions/delete_memo.php?id=${id}`);
        if (res.ok) {
            document.getElementById(`memo-${id}`)?.remove();
        } else {
            alert('메모 삭제 실패: ' + await res.text());
        }
    } catch (error) { alert('오류가 발생했습니다.'); }
}

function showEditShortUrl(id) {
    const row = document.getElementById(`short-link-row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
    row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
}

function hideEditShortUrl(id) {
    const row = document.getElementById(`short-link-row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
    row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
}

async function saveShortUrl(id) {
    const row = document.getElementById(`short-link-row-${id}`);
    const titleInput = row.querySelector('td:nth-child(2) .edit-mode');

    const formData = new FormData();
    formData.append('id', id);
    formData.append('title', titleInput.value.trim());

    try {
        const res = await fetch('actions/edit_short_url.php', { method: 'POST', body: new URLSearchParams(formData) });
        const data = await res.json();
        if (res.ok) {
            row.querySelector('td:nth-child(2) .view-mode').textContent = data.title;
            hideEditShortUrl(id);
        } else {
            alert('별칭 저장 실패: ' + (data.error || '알 수 없는 오류'));
        }
    } catch (error) {
        console.error('Save short URL error:', error);
        alert('저장 중 오류가 발생했습니다.');
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('클립보드에 복사되었습니다: ' + text);
    }, () => {
        alert('클립보드 복사에 실패했습니다.');
    });
}