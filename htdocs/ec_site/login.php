<?php
$page = 'login';
// app.phpを読み込み、共通関数や設定を利用できるようにする
require_once 'app.php';

/*
 * ログイン状態のチェック:
 * すでにログインしているユーザーがこのページにアクセスした場合、
 * 役割（管理者か一般ユーザーか）に応じて適切なページへリダイレクト（移動）させる。
 */
if (is_login()) {
  if (is_admin()) {
    to_page('admin.php'); 
  }
  to_page('products.php');
}

// ページ全体のレイアウト（ヘッダー部分）を出力する関数を呼び出す
render_header('ログイン');

// セッション等に保存されているメッセージやエラーがあれば、画面上部に表示する
if ($message !== '') { echo '<p class="msg ok">' . h($message) . '</p>'; }
if ($error !== '') { echo '<p class="msg err">' . h($error) . '</p>'; }
?>
<h1>ログイン</h1>
<!-- 
  ログインフォーム:
  ユーザー名とパスワードを入力して送信（POST）するためのフォーム。
  送信先（action）は自分自身（login.php）となっており、送信後に上部の app.php で処理が行われる。
-->
<form method="post" action="login.php">
  <input type="hidden" name="action" value="login">
  <!-- ユーザー名は英数字とアンダースコアのみ、5文字以上。
   パスワードは英数字とアンダースコアのみ、8文字以上。 -->
  <p>ユーザー名: <input name="user_name" required pattern="[A-Za-z0-9_]{5,}"></p>
  <p>パスワード: <input type="password" name="password" required pattern="[A-Za-z0-9_]{8,}"></p>
  <button type="submit">ログイン</button>
</form>
<!-- 
  ユーザー登録ページへのリンク: 
  まだアカウントを持っていないユーザー向けの案内 
-->
<p><a href="register.php">ユーザー登録へ</a></p>

<!-- ページ全体のレイアウト（フッター部分）を出力してHTMLを閉じる -->
<?php render_footer();
