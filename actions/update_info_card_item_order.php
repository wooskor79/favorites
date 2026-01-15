<?php
// 파일명: app/actions/update_info_card_item_order.php
require_once '../core/init.php';

header('Content-Type: application/json');

if (!$is_loggedin) {
    http_response_code(403);
    echo json_encode(['error' => '로그인이 필요합니다.']);
    exit;
}

$id = $_POST['id'] ?? null;
$direction = $_POST['direction'] ?? ''; // 'up' or 'down'

if (!$id || !in_array($direction, ['up', 'down'])) {
    echo json_encode(['error' => '잘못된 요청입니다.']);
    exit;
}

// 1. 현재 선택된 항목의 정보(group_id, sort_order) 확인
$stmt = $conn->prepare("SELECT id, group_id, sort_order FROM info_card_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$current) {
    echo json_encode(['error' => '항목을 찾을 수 없습니다.']);
    exit;
}

$current_order = $current['sort_order'];
$group_id = $current['group_id'];

// 2. 바꿀 대상(같은 그룹 내의 이웃 항목) 찾기
if ($direction === 'up') {
    $sql = "SELECT id, sort_order FROM info_card_items WHERE group_id = ? AND sort_order < ? ORDER BY sort_order DESC LIMIT 1";
} else {
    $sql = "SELECT id, sort_order FROM info_card_items WHERE group_id = ? AND sort_order > ? ORDER BY sort_order ASC LIMIT 1";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $group_id, $current_order);
$stmt->execute();
$target = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($target) {
    $target_id = $target['id'];
    $target_order = $target['sort_order'];

    // 3. 트랜잭션으로 두 항목의 순서 교체
    $conn->begin_transaction();
    try {
        $upd1 = $conn->prepare("UPDATE info_card_items SET sort_order = ? WHERE id = ?");
        $upd1->bind_param("ii", $target_order, $id);
        $upd1->execute();

        $upd2 = $conn->prepare("UPDATE info_card_items SET sort_order = ? WHERE id = ?");
        $upd2->bind_param("ii", $current_order, $target_id);
        $upd2->execute();

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['error' => '순서 변경 중 오류가 발생했습니다.']);
    }
} else {
    // 더 이상 이동할 곳이 없을 때도 성공으로 처리
    echo json_encode(['success' => true, 'message' => '이미 끝에 도달했습니다.']);
}

$conn->close();
?>