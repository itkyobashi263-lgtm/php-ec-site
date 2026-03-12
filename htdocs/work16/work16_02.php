<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>WORK16</title>
  </head>
  <body>
    <?php
      if(isset($_GET['name']) && $_GET['name'] != "") {
        print '名前： ' . htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8');
      } else {
        print '入力されていません';
      }

      if(isset($_GET['option']) && is_array($_GET['option'])) {
        print '<br>選択した内容： ';
        foreach($_GET['option'] as $option) {
          print htmlspecialchars($option, ENT_QUOTES, 'UTF-8') . ' ';
        }
      } else {
        print '<br>選択されていません';
      }
    ?>
  </body>
</html>