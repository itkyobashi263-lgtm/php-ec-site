<!DOCTYPE html>
<html lang="ja">
<head>
   <meta charset="UTF-8">
   <title>Work37</title>
</head>
<body>
    <?php
        //cookieに値がある場合、変数に格納する
        if (isset($_COOKIE['cookie_confirmation']) === TRUE) {
            $cookie_confirmation = "checked";
        } else {
            $cookie_confirmation = "";
        }
        if (isset($_COOKIE['user_name']) === TRUE) {
            $user_name = $_COOKIE['user_name'];
        } else {
            $user_name = '';
        }
    ?>
   <form action="home.php" method="post">
       <label for="user_name">ユーザーID</label><input type="text" id="user_name" name="user_name" value="<?php echo $user_name; ?>"><br>
       <label for="password">パスワード</label><input type="password" id="password" name="password" value=""><br>
       <input type="checkbox" name="cookie_confirmation" value="checked" <?php print $cookie_confirmation;?>>次回からログインIDの入力を省略する<br>
       <input type="submit" value="ログイン">
   </form>
</body>
</html>