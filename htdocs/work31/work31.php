<?php
  $dsn = 'mysql:host=localhost;dbname=********';
  $login_user= '********';
  $password= '********';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Try45</title>
</head>
<body>
    <?php
    try{
      // データベースへ接続
        $db = new PDO($dsn,$login_user, $password);
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit();
    }
    //SELECT文の実行:PDOとSELECT文を用いて、productテーブルからcategory_idが「1」であるレコードを、categoryテーブルから取得したカテゴリー名とともに表示するプログラムを実装してください。
    $sql = "SELECT product.product_name, product.price, category.category_name FROM product JOIN category ON product.category_id = category.category_id WHERE product.category_id = 1";
    if ($result = $db->query($sql)) {
        // 連想配列を取得
        while ($row = $result->fetch()) {
            echo $row["product_name"] . " - " . $row["price"] . " - " . $row["category_name"] . "<br>";
        }
    }
    ?>
</body>
</html>