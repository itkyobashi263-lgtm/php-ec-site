<?php
$filename = 'data.txt';
$error_message = '';

// 送信ボタンが押された時の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    // バリデーション：両方の入力欄があるか確認
    if ($title !== '' && $content !== '') {
        // 保存する形式を作成（例：タイトル：書き込み内容）
        $new_entry = $title . "：" . $content . "\n";

        // 既存のデータを読み込み、新しいデータを先頭に追加して保存
        $current_data = file_exists($filename) ? file_get_contents($filename) : '';
        file_put_contents($filename, $new_entry . $current_data);

        // 再読み込みして二重送信を防止
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "入力情報が不足しています";
    }
}

// 保存されたデータの読み込み
$lines = file_exists($filename) ? file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>簡易掲示板</title>
    <style>
        .error { color: red; margin-bottom: 10px; }
        .entry-list { margin-top: 20px; border-top: 1px solid #ccc; padding-top: 10px; }
        .entry-item { margin-bottom: 5px; }
    </style>
</head>
<body>

    <form method="post" action="">
        <div>
            タイトル：<br>
            <input type="text" name="title">
        </div>
        <br>
        <div>
            書き込み内容：<br>
            <textarea name="content"></textarea>
        </div>
        <br>
        <button type="submit">送信</button>
    </form>

    <?php if ($error_message): ?>
        <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <div class="entry-list">
        <h3>投稿一覧</h3>
        <?php foreach ($lines as $line): ?>
            <div class="entry-item"><?php echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endforeach; ?>
    </div>

</body>
</html>