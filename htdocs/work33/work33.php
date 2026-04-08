<?php 
    $dsn = 'mysql:host=localhost;dbname=********';
    $login_user = '********'; 
    $password = '********';
 ?>
<!DOCTYPE  html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>TRY47</title>
    </head>
    <body>
        <?php 
        try{
            // データベースへ接続
            $db=new PDO($dsn,$login_user,$password);
            //PDOのエラー時にPDOExceptionがはっせいするように設定
            $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction(); // トランザクション開始

            ///クエリを生成する
                $sql = "SELECT product_name, price FROM product WHERE price <= :id";

            //prepareメソッドによるクエリの実行準備をする
            $stmt = $db->prepare($sql);

            //値をバインドする
            $stmt->bindValue(':id', 160); 

            //クエリの実行
            $stmt->execute();
            $row = $stmt->rowCount();
            echo $row.'件情報を入手しました。<br>';
            // 連想配列を取得
            while ($row = $stmt->fetch()) {
                echo $row["product_name"] . $row["price"] . "<br>";
            }
            $db->commit(); // 正常に終了したらコミット
        } catch (PDOException $e) {
            echo $e->getMessage();
            $db->rollBack(); // エラーが起きたらロールバック
        }
        ?>
    </body>
</html>