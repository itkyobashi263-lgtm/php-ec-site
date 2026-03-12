<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset ="UTF-8">
    <title>WORK16</title>
  </head>
  <body>
    <!-- GETメソッドを用いて下記を実現してください。
下記を含んだフォームを実装する
名前（テキスト）
「選択肢01」「選択肢02」「選択肢03」（チェックボックス 、複数選択可）
遷移先をwork16_02.phpとし、フォームで取得した値が表示されるようになる。 -->
    <div>名前</div>
    <form method="get" action="work16_02.php">
      <input type="text" name="name">
      <div>選択肢</div>
        <input type="checkbox" name="option[]" value="選択肢01">選択肢01
        <input type="checkbox" name="option[]" value="選択肢02">選択肢02
        <input type="checkbox" name="option[]" value="選択肢03">選択肢03
      <input type="submit" value="送信">
    </form>
  </body>
</html>