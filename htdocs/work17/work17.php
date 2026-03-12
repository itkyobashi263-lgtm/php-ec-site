<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset ="UTF-8">
    <title>WORK17</title>
  </head>
  <body>
    <!-- POSTメソッドを用いて下記を実現してください。
下記を含んだフォームを実装する
名前（テキスト）
「選択肢01」「選択肢02」「選択肢03」（チェックボックス 、複数選択可）
遷移先は同じページとし、フォームで取得した値が表示されるようになる。 
初期値としては何も表示しない-->
    <div>名前</div>
    <form method="post">
      <input type="text" name="name">
      <div>選択肢</div>
        <input type="checkbox" name="option[]" value="選択肢01">選択肢01
        <input type="checkbox" name="option[]" value="選択肢02">選択肢02
        <input type="checkbox" name="option[]" value="選択肢03">選択肢03
      <input type="submit" value="送信">
    </form>
    <?php if($_SERVER["REQUEST_METHOD"] == "POST"): ?>
      <?php if(isset($_POST['name']) && $_POST['name'] != "") {
          print '名前： ' . htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
        } else {
          print '入力されていません';
        }

        if(isset($_POST['option']) && is_array($_POST['option'])) {
          print '<br>選択した内容： ';
          foreach($_POST['option'] as $option) {
            print htmlspecialchars($option, ENT_QUOTES, 'UTF-8') . ' ';
          }
        } else {
          print '<br>選択されていません';
        }
        ?>
      <?php endif; ?>
  </body>
</html>