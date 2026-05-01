<?php
session_start(); // セッションを有効化（必ずファイルの先頭に記述）

// ログイン中にアクセスした場合は、home.phpにリダイレクト
if (!empty($_SESSION['user_name'])) {
    header('Location: home.php');
    exit;
}

// Cookie処理
$user_name = '';
if (isset($_COOKIE['user_name']) === TRUE) {
    $user_name = htmlspecialchars($_COOKIE['user_name'], ENT_QUOTES, 'UTF-8');
}
$cookie_confirmation = '';
if (isset($_COOKIE['cookie_confirmation']) === TRUE) {
    $cookie_confirmation = 'checked';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Work38</title>
</head>
<body>
    <form action="home.php" method="post">
        <label for="user_name">ユーザーID</label>
        <input type="text" id="user_name" name="user_name" value="<?php echo $user_name; ?>"><br>
        <label for="password">パスワード</label>
        <input type="password" id="password" name="password" value=""><br>
        <input type="checkbox" name="cookie_confirmation" value="checked" <?php print $cookie_confirmation;?>>次回からログインIDの入力を省略する<br>
        <input type="submit" value="ログイン">
   </form>
</body>
</html>