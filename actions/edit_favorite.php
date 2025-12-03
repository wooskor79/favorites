<?php
// File: app/actions/edit_favorite.php
require_once '../core/init.php';

header('Content-Type: application/json');

function send_json_error($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

if (!$is_loggedin || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_error('Unauthorized', 403);
}

$id = $_POST['id'] ?? null;
$alias = trim($_POST['alias'] ?? '');
$url = trim($_POST['url'] ?? '');

if (empty($id) || empty($alias) || empty($url)) {
    send_json_error('All fields are required.', 400);
}

if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
    $url = "https://".$url;
}

$stmt = $conn->prepare("UPDATE favorites SET alias = ?, url = ? WHERE id = ?");
$stmt->bind_param("ssi", $alias, $url, $id);

if ($stmt->execute()) {
    $stmt->close();
    
    // Fetch the updated data to return
    $fav_query = $conn->prepare("SELECT id, alias, url FROM favorites WHERE id = ?");
    $fav_query->bind_param("i", $id);
    $fav_query->execute();
    $result = $fav_query->get_result();
    $updated_favorite = $result->fetch_assoc();
    $fav_query->close();
    $conn->close();
    
    echo json_encode($updated_favorite);
} else {
    send_json_error('Failed to update favorite: ' . $stmt->error);
}
?>

