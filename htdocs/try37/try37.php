<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Try37</title>
</head>
<body>
    <?php
    // データベースへ接続
    $db = new mysqli('localhost', '********', '********', '********');
    if ($db->connect_error) {
        echo $db->connect_error;
        exit();
    } else {
      print("データベースへの接続に成功しました。");
    }
    $db->close();    //接続を閉じる
    ?>
</body>
</html>