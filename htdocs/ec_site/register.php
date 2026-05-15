<?php
require_once '../../include/app.php';
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
render_header('ユーザー登録');
// メッセージの表示: $messageと$errorに応じて、成功メッセージやエラーメッセージを表示
if ($message !== '') { echo '<p class="msg ok">' . h($message) . '</p>'; }
if ($error !== '') { echo '<p class="msg err">' . h($error) . '</p>'; }
?>
<h1>ユーザー登録</h1>
<!-- 
  登録フォーム: 
  新規ユーザーのユーザー名とパスワードを入力して送信（POST）するためのフォーム。
  送信先（action）は自分自身（register.php）となっており、送信後に上部の app.php で処理が行われる。
-->
<form method="post" action="register.php">
  <input type="hidden" name="action" value="register">
  <!-- ユーザー名は英数字とアンダースコアのみ、5文字以上。
  パスワードは英数字とアンダースコアのみ、8文字以上。 -->
  <p>ユーザー名: <input name="user_name" required pattern="[A-Za-z0-9_]{5,}"></p>
  <p>パスワード: <input type="password" name="password" required pattern="[A-Za-z0-9_]{8,}"></p>
  <button type="submit">登録</button>
</form>
<!-- ログインページへのリンク: 既にアカウントを持っている人向けの案内 -->
<p><a href="login.php">ログインへ</a></p>

<!-- ページ全体のレイアウト（フッター部分）を出力してHTMLを閉じる -->
<?php render_footer();
