<?php 
    $dsn = 'mysql:host=localhost;dbname=********';
    $login_user = '********'; 
    $password = '********';   
 ?>
<!DOCTYPE  html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>TRY46</title>
    </head>
    <body>
        <?php 
        try{
            // データベースへ接続
            $db=new PDO($dsn,$login_user,$password);
            //PDOのエラー時にPDOExceptionがはっせいするように設定
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $db->beginTransaction(); // トランザクション開始

            //UPDATE文の実行
            $sql = "UPDATE product SET price = 160 WHERE product_id = 1";
            $result = $db->query($sql);
            $row = $result->rowCount();
            echo $row.'件更新されました。';
            $db->commit(); // 正常に終了したらコミット
        } catch (PDOException $e) {
            echo $e->getMessage();
            $db->rollBack(); // エラーが起きたらロールバック
        }
        ?>
    </body>
</html>