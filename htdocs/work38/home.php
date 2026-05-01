<?php
session_start();

// ログインしていない状態（かつフォーム送信ではない直接アクセス）はwork38.phpへリダイレクト
if (empty($_SESSION['user_name']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: work38.php');
    exit;
}
// DB接続設定（ご自身の環境に合わせて書き換えてください）
    const DB_HOST = 'localhost';
    const DB_USER = '********';
    const DB_PASS = '********';
    const DB_NAME = '********';
// 初期値の設定（合致しない・存在しない場合は失敗とする）
$message = "ログインに失敗しました";
$welcome_message = "";

// POSTデータの取得（自動サニタイズ処理を含む）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_user_name = filter_input(INPUT_POST, 'user_name');
    $password = filter_input(INPUT_POST, 'password');
    $cookie_confirmation = filter_input(INPUT_POST, 'cookie_confirmation');


  if ($post_user_name && $password) {
    try {
        // PDOによるデータベース接続
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        // DBからuser_nameを元にユーザー検索（SQLインジェクション対策済み）
        $stmt = $pdo->prepare("SELECT user_name, password FROM user_table WHERE user_name = :user_id");
        $stmt->bindValue(':user_id', $post_user_name, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        // パスワードの照合
        if ($user && $password === $user['password']) {
            $message = "ログインが完了しました";
            $welcome_message = htmlspecialchars($user['user_name'], ENT_QUOTES, 'UTF-8') . "さん、ようこそ！";
            // 照合成功：セッションにユーザー名を記録し、ログイン状態を確立
            $_SESSION['user_name'] = $user['user_name'];

            // 自動化提案：チェックボックスの状態に応じたCookieの自動付与・自動破棄
            if ($cookie_confirmation === 'checked') {
                setcookie('user_name', $post_user_name, time() + 60 * 60 * 24 * 30); // 30日間保持
                setcookie('cookie_confirmation', 'checked', time() + 60 * 60 * 24 * 30);
            } else {
                setcookie('user_name', '', time() - 3600); // チェックがなければ破棄
                setcookie('cookie_confirmation', '', time() - 3600);
            }
        } else {
            $message = "ログインに失敗しました";
        }
    } catch (PDOException $e) {
        $message = "ログインに失敗しました"; // ユーザー向けには詳細を隠蔽
        // 自動化提案：DBエラー発生時は裏側でエラーログに自動記録する
        error_log("DB Error: " . $e->getMessage(), 0);
    }
  } else {
    $message = "ログインに失敗しました";
  }
}

// セッションが存在する場合（ログイン成功直後、またはログイン中のアクセス）
if (!empty($_SESSION['user_name'])) {
    $welcome_message = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') . "さん：ログイン中です";
    $message = ""; // エラーメッセージをクリア
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
    <a href="./work38.php">戻る</a>
</body>
</html>