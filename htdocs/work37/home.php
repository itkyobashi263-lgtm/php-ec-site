<?php
// DB接続設定（ご自身の環境に合わせて書き換えてください）
    const DB_HOST = 'localhost';
    const DB_USER = '********';
    const DB_PASS = '********';
    const DB_NAME = '********';

// POSTデータの取得（自動サニタイズ処理を含む）
$user_name = filter_input(INPUT_POST, 'user_name');
$password = filter_input(INPUT_POST, 'password');
$cookie_confirmation = filter_input(INPUT_POST, 'cookie_confirmation');

// 初期値の設定（合致しない・存在しない場合は失敗とする）
$message = "ログインに失敗しました";
$welcome_message = "";

if ($user_name && $password) {
    try {
        // PDOによるデータベース接続
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        // DBからuser_nameを元にユーザー検索（SQLインジェクション対策済み）
        $stmt = $pdo->prepare("SELECT user_name, password FROM user_table WHERE user_name = :user_id");
        $stmt->bindValue(':user_id', $user_name, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        // パスワードの照合
        if ($user && $password === $user['password']) {
            $message = "ログインが完了しました";
            $welcome_message = htmlspecialchars($user['user_name'], ENT_QUOTES, 'UTF-8') . "さん、ようこそ！";

            // 自動化提案：チェックボックスの状態に応じたCookieの自動付与・自動破棄
            if ($cookie_confirmation === 'checked') {
                setcookie('user_name', $user_name, time() + 60 * 60 * 24 * 30); // 30日間保持
                setcookie('cookie_confirmation', 'checked', time() + 60 * 60 * 24 * 30);
            } else {
                setcookie('user_name', '', time() - 3600); // チェックがなければ破棄
                setcookie('cookie_confirmation', '', time() - 3600);
            }
        }
    } catch (PDOException $e) {
        $message = "ログインに失敗しました"; // ユーザー向けには詳細を隠蔽
        // 自動化提案：DBエラー発生時は裏側でエラーログに自動記録する
        error_log("DB Error: " . $e->getMessage(), 0);
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>
<body>
    <p><?php echo $message; ?></p>
    <?php if ($welcome_message !== ""): ?>
        <p><?php echo $welcome_message; ?></p>
    <?php endif; ?>
    <br>
    <a href="./work37.php">戻る</a>
</body>
</html>