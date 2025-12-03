<?php
// File: app/actions/upload_image.php
require_once '../core/init.php';

header('Content-Type: application/json');

// 오류 메시지를 JSON 형식으로 보내는 함수
function send_json_error($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// 0. GD 라이브러리 확인
if (!extension_loaded('gd')) {
    send_json_error('서버에 이미지 처리 라이브러리(GD)가 설치되지 않았습니다.', 500);
}

// 1. 로그인 상태 확인
if (!$is_loggedin) {
    send_json_error('로그인이 필요합니다.', 403);
}

// 2. 파일 업로드 확인
if (!isset($_FILES['image'])) {
    send_json_error('이미지 파일이 없습니다.', 400);
}

$file = $_FILES['image'];

// 3. 업로드 오류 확인
if ($file['error'] !== UPLOAD_ERR_OK) {
    send_json_error('파일 업로드 중 오류가 발생했습니다: ' . $file['error'], 500);
}

// 4. 파일 유효성 검사 (MIME 타입, 크기)
$allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_mime_types)) {
    send_json_error('허용되지 않는 이미지 형식입니다. (jpeg, png, gif, webp)', 400);
}

if ($file['size'] > 10 * 1024 * 1024) { // 10MB
    send_json_error('이미지 파일 크기는 10MB를 초과할 수 없습니다.', 400);
}

// 5. 저장 경로 설정 및 생성 (경로 변경)
$image_dir = $_SERVER['DOCUMENT_ROOT'] . '/images';
$cache_dir = $image_dir . '/cache'; // cache 폴더를 images 폴더 하위로 변경

if (!is_dir($image_dir)) mkdir($image_dir, 0775, true);
if (!is_dir($cache_dir)) mkdir($cache_dir, 0775, true);
if (!is_writable($image_dir) || !is_writable($cache_dir)) {
    send_json_error('서버 저장소에 쓰기 권한이 없습니다.', 500);
}

// 6. 파일 저장 및 썸네일 생성
$extension = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
$new_filename = uniqid('img_', true) . '.' . $extension;
$original_path = $image_dir . '/' . $new_filename;
$thumbnail_path = $cache_dir . '/' . $new_filename;

if (!move_uploaded_file($file['tmp_name'], $original_path)) {
    send_json_error('원본 이미지 저장에 실패했습니다.', 500);
}

// 썸네일 생성 함수
function create_thumbnail($source_path, $dest_path, $max_width = 300) {
    $image_info = getimagesize($source_path);
    if ($image_info === false) return false;
    list($width, $height, $type) = $image_info;

    if ($width <= $max_width) {
        return copy($source_path, $dest_path);
    }

    $new_width = $max_width;
    $new_height = floor($height * ($max_width / $width));
    $source_image = null;
    switch ($type) {
        case IMAGETYPE_JPEG: $source_image = imagecreatefromjpeg($source_path); break;
        case IMAGETYPE_PNG: $source_image = imagecreatefrompng($source_path); break;
        case IMAGETYPE_GIF: $source_image = imagecreatefromgif($source_path); break;
        case IMAGETYPE_WEBP: $source_image = imagecreatefromwebp($source_path); break;
        default: return false;
    }
    if ($source_image === false) return false;

    $thumb = imagecreatetruecolor($new_width, $new_height);
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
        imagefilledrectangle($thumb, 0, 0, $new_width, $new_height, $transparent);
    }
    imagecopyresampled($thumb, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG: $success = imagejpeg($thumb, $dest_path, 85); break;
        case IMAGETYPE_PNG: $success = imagepng($thumb, $dest_path, 9); break;
        case IMAGETYPE_GIF: $success = imagegif($thumb, $dest_path); break;
        case IMAGETYPE_WEBP: $success = imagewebp($thumb, $dest_path, 85); break;
    }
    imagedestroy($source_image);
    imagedestroy($thumb);
    return $success;
}

if (!create_thumbnail($original_path, $thumbnail_path)) {
    unlink($original_path);
    send_json_error('썸네일 생성에 실패했습니다. 지원되지 않는 이미지 형식이거나 파일이 손상되었을 수 있습니다.', 500);
}

// 7. 성공 응답 (웹 접근 가능 경로 반환 - 경로 변경)
$thumbnail_web_path = '/images/cache/' . $new_filename;
echo json_encode(['filePath' => $thumbnail_web_path]);

?>