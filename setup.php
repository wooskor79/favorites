<?php
// File: app/setup.php
// 초기 관리자 비밀번호를 웹 양식을 통해 설정하는 파일입니다.
// 설정 후에는 보안을 위해 반드시 이 파일을 삭제해주세요.

// 데이터베이스 연결 정보 (상수로 정의)
require_once 'core/init.php'; // init.php를 불러와 DB 상수를 사용

$message = '';
$is_success = false;
$admin_username = 'admin';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($password) || empty($password_confirm)) {
        $message = "<p style='color:red;'>비밀번호와 비밀번호 확인을 모두 입력해주세요.</p>";
    } elseif ($password !== $password_confirm) {
        $message = "<p style='color:red;'>입력한 두 비밀번호가 일치하지 않습니다.</p>";
    } else {
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            $message = "<p style='color:red;'>데이터베이스 연결 실패: " . htmlspecialchars($conn->connect_error) . "</p>";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $admin_username);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $stmt_update = $conn->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
                $stmt_update->bind_param("ss", $password_hash, $admin_username);
                if ($stmt_update->execute()) {
                    $message = "<p style='color:blue; font-weight:bold;'>'" . htmlspecialchars($admin_username) . "' 계정의 비밀번호가 성공적으로 업데이트되었습니다.</p>";
                    $is_success = true;
                } else {
                    $message = "<p style='color:red;'>비밀번호 업데이트 실패: " . htmlspecialchars($stmt_update->error) . "</p>";
                }
                $stmt_update->close();
            } else {
                $stmt_insert = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
                $stmt_insert->bind_param("ss", $admin_username, $password_hash);
                if ($stmt_insert->execute()) {
                    $message = "<p style='color:blue; font-weight:bold;'>'" . htmlspecialchars($admin_username) . "' 계정이 성공적으로 생성되었습니다.</p>";
                    $is_success = true;
                } else {
                    $message = "<p style='color:red;'>계정 생성 실패: " . htmlspecialchars($stmt_insert->error) . "</p>";
                }
                $stmt_insert->close();
            }
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>초기 관리자 설정</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        h1 { text-align: center; }
        form { display: flex; flex-direction: column; gap: 15px; }
        label { font-weight: bold; }
        input[type="password"] { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 1em; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        button:hover { background-color: #0056b3; }
        .message { margin-top: 20px; padding: 15px; border-radius: 4px; text-align: center; }
        .warning { color: #856404; background-color: #fff3cd; border: 1px solid #ffeeba; }
    </style>
</head>
<body>

    <h1>초기 관리자 비밀번호 설정</h1>
    <p>사용자 이름 '<strong><?php echo htmlspecialchars($admin_username); ?></strong>'의 비밀번호를 설정합니다.</p>

    <?php if (!$is_success): ?>
    <form action="setup.php" method="POST">
        <div>
            <label for="password">새 비밀번호:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="password_confirm">비밀번호 확인:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit">비밀번호 설정하기</button>
    </form>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($is_success): ?>
        <div class="message warning">
            <h2 style='color:red;'>중요: 설정이 완료되었습니다.</h2>
            <p>이제 메인 페이지로 이동하여 로그인할 수 있습니다.<br><strong>보안을 위해 지금 바로 'setup.php' 파일을 서버에서 삭제하세요!</strong></p>
            <a href="index.php">메인 페이지로 가기</a>
        </div>
    <?php endif; ?>

</body>
</html>