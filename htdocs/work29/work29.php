<?php
  $host = 'localhost';
  $login_user = '********';
  $password = '********';
  $database = '********';
  $error_msg = [];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>WORK29</title>
</head>
<body>
  <?php
    // データベースへ接続
    $db = new mysqli($host, $login_user, $password,   $database);
    if ($db->connect_error) {
      echo $db->connect_error;
      exit();
    } else {
      $db->set_charset("utf8");
    }
/*
1:「挿入」ボタンを押すと下記の商品が挿入される。product_id: 21
product_code: 1021
product_name: エシャロット
price: 200
category_id: 1
ただし失敗した場合はロールバックを行うソースコードにすること。ロールバックが実際になされているかを試すこと。
2:「削除」ボタンを押すと上記の商品が消去される。ただし失敗した場合はロールバックを行うソースコードにすること。ロールバックが実際になされているかを試すこと。*/
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      if(isset($_POST['insert'])) {
        $db->begin_transaction(); // トランザクション開始
        //INSERT文の実行
        $insert = "INSERT INTO product (product_id, product_code, product_name, price, category_id) VALUES (21, 1021, 'エシャロット', 200, 1);";
        if($result = $db->query($insert)) {
        $row = $db->affected_rows; // 変更された行数を取得
        } else {
        $error_msg[] = 'INSERT実行エラー[実行SQL] ' . $insert;
        }
        //$error_msg[] = '強制的にエラーメッセージを挿入';

        //エラーメッセージ格納の有無によりトランザクションの成否を判定
        if (count($error_msg) == 0) {
          echo $row. '件挿入しました。<br>';
          $db->commit(); // 正常に終了したらコミット
        } else {
          echo '挿入が失敗しました。<br>';
          $db->rollback(); // エラーが起きたらロールバック
        }
          // 下記はエラー確認用。エラー確認が必要な際にはコメントを外してください。
        //var_dump($error_msg); 
      }

      if(isset($_POST['delete'])) {
        $db->begin_transaction(); // トランザクション開始
          //DELETE文の実行     
        $delete = "DELETE FROM product WHERE product_id = 21;";
        if($db->query($delete)) {
        $row = $db->affected_rows; // 変更された行数を取得
        } else {
          $error_msg[] = 'DELETE実行エラー[実行SQL] ' . $delete;
        }
        //$error_msg[] = '強制的にエラーメッセージを挿入';

        //エラーメッセージ格納の有無によりトランザクションの成否を判定
        if (count($error_msg) == 0) {
          echo $row. '件削除しました。<br>';
          $db->commit(); // 正常に終了したらコミット
        } else {
          echo '削除が失敗しました。<br>';
          $db->rollback(); // エラーが起きたらロールバック
        }
          // 下記はエラー確認用。エラー確認が必要な際にはコメントを外してください。
          //var_dump($error_msg); 
      }
    }

      //SELECT文の実行
    $sql = "SELECT * FROM product ORDER BY product_id ASC;";
    if ($result = $db->query($sql)) {
        // 連想配列を取得
      foreach ($result as $row) {
        echo $row["product_name"] . $row["price"] . "<br>";
            }
        // 結果セットを閉じる
        $result->close();
    }
    
    $db->close();    //接続を閉じる

  ?>
  <form method="post">
    <input type="submit" value="挿入" name="insert">
    <input type="submit" value="削除" name="delete">
  </form>
</body>
</html>