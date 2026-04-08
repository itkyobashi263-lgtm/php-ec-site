<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Try39</title>
</head>
<body>
    <?php
    // データベースへ接続
      $db = new mysqli('localhost', '********', '********', '********');
      if ($db->connect_error) {
        echo $db->connect_error;
        exit();
      } else {
        $db->set_charset("utf8"); // 文字コードをUTF-8に設定
      }
      //SELECT文の実行
      $sql = "SELECT product_name, price FROM product WHERE price <= 100";
      if ($result = $db->query($sql)) {
        // 連想配列を取得
        foreach ($result as $row) {
            echo $row['product_name']. $row['price'] . "<br>";
        }
        // 結果セットを閉じる
        $result->close();
      } 

      $db->close();    //接続を閉じる
    ?>
</body>
</html>